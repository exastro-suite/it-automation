-- *****************************************************************************
-- *** ***** Terraform Common Tables                                         ***
-- *****************************************************************************
-- ----更新系テーブル作成
-- インタフェース情報
CREATE TABLE B_TERRAFORM_CLI_IF_INFO
(
TERRAFORM_IF_INFO_ID              INT                              ,
TERRAFORM_NUM_PARALLEL_EXEC       INT                              , -- 並列実行数
TERRAFORM_REFRESH_INTERVAL        INT                              ,
TERRAFORM_TAILLOG_LINES           INT                              ,
NULL_DATA_HANDLING_FLG            INT                              , -- Null値の連携 1:有効　2:無効
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (TERRAFORM_IF_INFO_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- インタフェース情報(履歴)
CREATE TABLE B_TERRAFORM_CLI_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
TERRAFORM_IF_INFO_ID              INT                              ,
TERRAFORM_NUM_PARALLEL_EXEC       INT                              , -- 並列実行数
TERRAFORM_REFRESH_INTERVAL        INT                              ,
TERRAFORM_TAILLOG_LINES           INT                              ,
NULL_DATA_HANDLING_FLG            INT                              , -- Null値の連携 1:有効　2:無効
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成----
--ステータス
CREATE TABLE B_TERRAFORM_CLI_STATUS
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

-- ----履歴系テーブル作成----
--ステータス(履歴)
CREATE TABLE B_TERRAFORM_CLI_STATUS_JNL
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

-- ----更新系テーブル作成----
-- 実行モード情報
CREATE TABLE B_TERRAFORM_CLI_RUN_MODE
(
RUN_MODE_ID                       INT                              ,
RUN_MODE_NAME                     VARCHAR (32)                     ,
DISP_SEQ                          INT                              , -- 表示順序
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (RUN_MODE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成----
-- 実行モード情報(履歴)
CREATE TABLE B_TERRAFORM_CLI_RUN_MODE_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
RUN_MODE_ID                       INT                              ,
RUN_MODE_NAME                     VARCHAR (32)                     ,
DISP_SEQ                          INT                              , -- 表示順序
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成----
-- HCLフラグ
CREATE TABLE B_TERRAFORM_CLI_HCL_FLAG
(
HCL_FLAG                          INT                              ,
HCL_FLAG_SELECT                   VARCHAR (32)                     ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (HCL_FLAG)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成----
-- HCLフラグ(履歴)
CREATE TABLE B_TERRAFORM_CLI_HCL_FLAG_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
HCL_FLAG                          INT                              ,
HCL_FLAG_SELECT                   VARCHAR (32)                     ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成----
--変数タイプ一覧
CREATE TABLE B_TERRAFORM_CLI_TYPES_MASTER
(
TYPE_ID INT                                                         , -- タイプID
TYPE_NAME TEXT                                                      , -- タイプ名
MEMBER_VARS_FLAG INT                                                , -- メンバー変数の入力有(1)/無(0))
ASSIGN_SEQ_FLAG INT                                                 , -- 代入順序の入力有(1)/無(0)                                            
ENCODE_FLAG INT                                                     , -- (1)/無(0)
DISP_SEQ INT                                                        , -- 表示順序
ACCESS_AUTH TEXT                                                    ,
NOTE VARCHAR (4000)                                                 , -- 備考
DISUSE_FLAG VARCHAR (1)                                             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP DATETIME(6)                                   , -- 最終更新日時
LAST_UPDATE_USER INT                                                , -- 最終更新ユーザ
PRIMARY KEY (TYPE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- ----更新系テーブル作成----

-- *****************************************************************************
-- *** ***** Terraform Common Tables                                         ***
-- *****************************************************************************

-- *****************************************************************************
-- *** ***** Terraform Tables                                                ***
-- *****************************************************************************

-- ----更新系テーブル作成
--Workspaces管理
CREATE TABLE B_TERRAFORM_CLI_WORKSPACES
(
WORKSPACE_ID                      INT                              ,
WORKSPACE_NAME                    VARCHAR (90)                     ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (WORKSPACE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--Workspaces管理(履歴)
CREATE TABLE B_TERRAFORM_CLI_WORKSPACES_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
WORKSPACE_ID                      INT                              ,
WORKSPACE_NAME                    VARCHAR (90)                     ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
--Module素材
CREATE TABLE B_TERRAFORM_CLI_MODULE
(
MODULE_MATTER_ID                  INT                              ,
MODULE_MATTER_NAME                VARCHAR (256)                    ,
MODULE_MATTER_FILE                VARCHAR (256)                    ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (MODULE_MATTER_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--Module素材(履歴)
CREATE TABLE B_TERRAFORM_CLI_MODULE_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
MODULE_MATTER_ID                  INT                              ,
MODULE_MATTER_NAME                VARCHAR (256)                    ,
MODULE_MATTER_FILE                VARCHAR (256)                    ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----


-- ----更新系テーブル作成
--作業パターン詳細
CREATE TABLE B_TERRAFORM_CLI_PATTERN_LINK
(
LINK_ID                           INT                              ,
PATTERN_ID                        INT                              ,
MODULE_MATTER_ID                  INT                              ,
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
--作業パターン詳細(履歴)
CREATE TABLE B_TERRAFORM_CLI_PATTERN_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
LINK_ID                           INT                              ,
PATTERN_ID                        INT                              ,
MODULE_MATTER_ID                  INT                              ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- ----更新系テーブル作成
--実行管理
CREATE TABLE C_TERRAFORM_CLI_EXE_INS_MNG
(
EXECUTION_NO                      INT                              ,
EXECUTION_USER                    VARCHAR (80)                     ,
SYMPHONY_NAME                     VARCHAR (256)                    ,
STATUS_ID                         INT                              ,
SYMPHONY_INSTANCE_NO              INT                              ,
PATTERN_ID                        INT                              ,
I_PATTERN_NAME                    VARCHAR (256)                    ,
I_TIME_LIMIT                      INT                              ,
I_TERRAFORM_WORKSPACE_ID          INT                              ,
I_TERRAFORM_WORKSPACE             VARCHAR (256)                    ,
OPERATION_NO_UAPK                 INT                              ,
I_OPERATION_NAME                  VARCHAR (256)                    ,
I_OPERATION_NO_IDBH               INT                              ,
CONDUCTOR_NAME                    VARCHAR (256)                    , -- コンダクタ名
CONDUCTOR_INSTANCE_NO             INT                              , -- コンダクタ インスタンスID
TIME_BOOK                         DATETIME(6)                      ,
TIME_START                        DATETIME(6)                      ,
TIME_END                          DATETIME(6)                      ,
FILE_INPUT                        VARCHAR (1024)                   ,
FILE_RESULT                       VARCHAR (1024)                   ,
RUN_MODE                          INT                              ,
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
--実行管理(履歴)
CREATE TABLE C_TERRAFORM_CLI_EXE_INS_MNG_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
EXECUTION_NO                      INT                              ,
EXECUTION_USER                    VARCHAR (80)                     ,
SYMPHONY_NAME                     VARCHAR (256)                    ,
STATUS_ID                         INT                              ,
SYMPHONY_INSTANCE_NO              INT                              ,
PATTERN_ID                        INT                              ,
I_PATTERN_NAME                    VARCHAR (256)                    ,
I_TIME_LIMIT                      INT                              ,
I_TERRAFORM_WORKSPACE_ID          INT                              ,
I_TERRAFORM_WORKSPACE             VARCHAR (256)                    ,
OPERATION_NO_UAPK                 INT                              ,
I_OPERATION_NAME                  VARCHAR (256)                    ,
I_OPERATION_NO_IDBH               INT                              ,
CONDUCTOR_NAME                    VARCHAR (256)                    , -- コンダクタ名
CONDUCTOR_INSTANCE_NO             INT                              , -- コンダクタ インスタンスID
TIME_BOOK                         DATETIME(6)                      ,
TIME_START                        DATETIME(6)                      ,
TIME_END                          DATETIME(6)                      ,
FILE_INPUT                        VARCHAR (1024)                   ,
FILE_RESULT                       VARCHAR (1024)                   ,
RUN_MODE                          INT                              ,
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
--代入値管理
CREATE TABLE B_TERRAFORM_CLI_VARS_ASSIGN
(
ASSIGN_ID                         INT                              ,
OPERATION_NO_UAPK                 INT                              , -- オペレーションID
PATTERN_ID                        INT                              , -- パターンID
MODULE_VARS_LINK_ID               INT                              , -- 代入値リンクID
VARS_ENTRY                        text                             ,
ASSIGN_SEQ                        INT                              , -- 代入順序
MEMBER_VARS                       INT                              , -- メンバー変数
HCL_FLAG                          VARCHAR (1)                      , -- HCL設定
SENSITIVE_FLAG                    VARCHAR (1)                      , -- Sensitive設定
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
----代入値管理(履歴)
CREATE TABLE B_TERRAFORM_CLI_VARS_ASSIGN_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
ASSIGN_ID                         INT                              ,
OPERATION_NO_UAPK                 INT                              , -- オペレーションID
PATTERN_ID                        INT                              , -- パターンID
MODULE_VARS_LINK_ID               INT                              , -- 代入値リンクID
VARS_ENTRY                        text                             ,
ASSIGN_SEQ                        INT                              , -- 代入順序
MEMBER_VARS                       INT                              , -- メンバー変数
HCL_FLAG                          VARCHAR (1)                      , -- HCL設定
SENSITIVE_FLAG                    VARCHAR (1)                      , -- Sensitive設定
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
--代入値自動登録設定
CREATE TABLE B_TERRAFORM_CLI_VAL_ASSIGN (
COLUMN_ID                         INT                     , -- 識別シーケンス
MENU_ID                           INT                     , -- メニューID
COLUMN_LIST_ID                    INT                     , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                          INT                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                        INT                     , -- 作業パターンID
VAL_VARS_LINK_ID                  INT                     , -- Value値　作業パターン変数紐付
KEY_VARS_LINK_ID                  INT                     , -- Key値　作業パターン変数紐付
KEY_ASSIGN_SEQ                    INT                     , -- Keyの代入順序
KEY_MEMBER_VARS                   INT                     , -- Keyのメンバ変数
VAL_ASSIGN_SEQ                    INT                     , -- Valueの代入順序
VAL_MEMBER_VARS                   INT                     , -- Valueのメンバ変数
HCL_FLAG                          VARCHAR (1)             , -- HCL設定
VAL_VARS_HCL_FLAG                 VARCHAR (1)             , -- Value値 HCL設定
KEY_VARS_HCL_FLAG                 VARCHAR (1)             , -- Key値 HCL設定
NULL_DATA_HANDLING_FLG            INT                     , -- Null値の連携
DISP_SEQ                          INT                     , -- 表示順序
ACCESS_AUTH                       TEXT                    ,
NOTE                              VARCHAR (4000)          , -- 備考
DISUSE_FLAG                       VARCHAR (1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)             , -- 最終更新日時
LAST_UPDATE_USER                  INT                     , -- 最終更新ユーザ
PRIMARY KEY(COLUMN_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--代入値自動登録設定(履歴)
CREATE TABLE B_TERRAFORM_CLI_VAL_ASSIGN_JNL
(
JOURNAL_SEQ_NO                    INT                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)             , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)             , -- 履歴用変更種別
COLUMN_ID                         INT                     , -- 識別シーケンス
MENU_ID                           INT                     , -- メニューID
COLUMN_LIST_ID                    INT                     , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                          INT                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                        INT                     , -- 作業パターンID
VAL_VARS_LINK_ID                  INT                     , -- Value値　作業パターン変数紐付
KEY_VARS_LINK_ID                  INT                     , -- Key値　作業パターン変数紐付
KEY_ASSIGN_SEQ                    INT                     , -- Keyの代入順序
KEY_MEMBER_VARS                   INT                     , -- Keyのメンバ変数
VAL_ASSIGN_SEQ                    INT                     , -- Valueの代入順序
VAL_MEMBER_VARS                   INT                     , -- Valueのメンバ変数
HCL_FLAG                          VARCHAR (1)             , -- HCL設定
VAL_VARS_HCL_FLAG                 VARCHAR (1)             , -- Value値 HCL設定
KEY_VARS_HCL_FLAG                 VARCHAR (1)             , -- Key値 HCL設定
NULL_DATA_HANDLING_FLG            INT                     , -- Null値の連携
DISP_SEQ                          INT                     , -- 表示順序
ACCESS_AUTH                       TEXT                    ,
NOTE                              VARCHAR (4000)          , -- 備考
DISUSE_FLAG                       VARCHAR (1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)             , -- 最終更新日時
LAST_UPDATE_USER                  INT                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
--Module変数紐付管理
CREATE TABLE B_TERRAFORM_CLI_MODULE_VARS_LINK
(
MODULE_VARS_LINK_ID               INT                              ,
MODULE_MATTER_ID                  INT                              ,
VARS_NAME                         VARCHAR (256)                    ,
VARS_DESCRIPTION                  VARCHAR (256)                    ,
TYPE_ID                           INT                              , -- タイプID
VARS_VALUE                        TEXT                             , -- デフォルト値
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (MODULE_VARS_LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--Module変数紐付管理(履歴)
CREATE TABLE B_TERRAFORM_CLI_MODULE_VARS_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
MODULE_VARS_LINK_ID               INT                              ,
MODULE_MATTER_ID                  INT                              ,
VARS_NAME                         VARCHAR (256)                    ,
VARS_DESCRIPTION                  VARCHAR (256)                    ,
TYPE_ID                           INT                              , -- タイプID
VARS_VALUE                        TEXT                             , -- デフォルト値
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
--メンバー変数管理
CREATE TABLE B_TERRAFORM_CLI_VAR_MEMBER
(
CHILD_MEMBER_VARS_ID              INT                              , -- メンバー変数のID
PARENT_VARS_ID                    INT                              , -- 親変数(Module変数紐付管理)ID
PARENT_MEMBER_VARS_ID             INT                              , -- 親メンバー変数のID
CHILD_MEMBER_VARS_NEST            TEXT                             , -- メンバー変数のキー(フル)
CHILD_MEMBER_VARS_KEY             TEXT                             , -- メンバー変数のキー
CHILD_VARS_TYPE_ID                INT                              , -- 子メンバ変数のタイプID
ARRAY_NEST_LEVEL                  INT                              , -- 子メンバ変数の階層
ASSIGN_SEQ                        INT                              , -- 代入順序
CHILD_MEMBER_VARS_VALUE           TEXT                             , -- デフォルト値
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(CHILD_MEMBER_VARS_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--メンバー変数管理(履歴)
CREATE TABLE B_TERRAFORM_CLI_VAR_MEMBER_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
CHILD_MEMBER_VARS_ID              INT                              , -- メンバー変数のID
PARENT_VARS_ID                    INT                              , -- 親変数(Module変数紐付管理)ID
PARENT_MEMBER_VARS_ID             INT                              , -- 親メンバー変数のID
CHILD_MEMBER_VARS_NEST            TEXT                             , -- メンバー変数のキー(フル)
CHILD_MEMBER_VARS_KEY             TEXT                             , -- メンバー変数のキー
CHILD_VARS_TYPE_ID                INT                              , -- 子メンバ変数のタイプID
ARRAY_NEST_LEVEL                  INT                              , -- 子メンバ変数の階層
ASSIGN_SEQ                        INT                              , -- 代入順序
CHILD_MEMBER_VARS_VALUE           TEXT                             , -- デフォルト値
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
--変数ネスト管理
CREATE TABLE B_TERRAFORM_CLI_LRL_MAX_MEMBER_COL
(
MAX_COL_SEQ_ID                    INT                              , -- 項番
VARS_ID                           INT                              , -- 変数ID
MEMBER_VARS_ID                    INT                              , -- メンバー変数ID
MAX_COL_SEQ                       INT                              , -- 最大繰り返し数
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(MAX_COL_SEQ_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--変数ネスト管理(履歴)
CREATE TABLE B_TERRAFORM_CLI_LRL_MAX_MEMBER_COL_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
MAX_COL_SEQ_ID                    INT                              , -- 項番
VARS_ID                           INT                              , -- 変数ID
MEMBER_VARS_ID                    INT                              , -- メンバー変数ID
MAX_COL_SEQ                       INT                              , -- 最大繰り返し数
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
-- *** Terraform Tables *****                                                ***
-- *****************************************************************************



-- *****************************************************************************
-- *** ***** Terraform Views                                                 ***
-- *****************************************************************************
-- インターフェース情報 VIEW
CREATE VIEW D_TERRAFORM_CLI_IF_INFO AS 
SELECT * 
FROM B_TERRAFORM_CLI_IF_INFO;

CREATE VIEW D_TERRAFORM_CLI_IF_INFO_JNL AS 
SELECT * 
FROM B_TERRAFORM_CLI_IF_INFO_JNL;

-- ステータス VIEW
CREATE VIEW D_TERRAFORM_CLI_INS_STATUS AS 
SELECT * 
FROM B_TERRAFORM_CLI_STATUS;

CREATE VIEW D_TERRAFORM_CLI_INS_STATUS_JNL AS 
SELECT * 
FROM B_TERRAFORM_CLI_STATUS_JNL;

--実行モード情報 VIEW
CREATE VIEW D_TERRAFORM_CLI_INS_RUN_MODE AS 
SELECT * 
FROM B_TERRAFORM_CLI_RUN_MODE;

CREATE VIEW D_TERRAFORM_CLI_INS_RUN_MODE_JNL AS 
SELECT * 
FROM B_TERRAFORM_CLI_RUN_MODE_JNL;

--作業パターン詳細 VIEW
CREATE VIEW E_TERRAFORM_CLI_PATTERN AS 
SELECT 
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TERRAFORM_CLI_WORKSPACE_ID    ,
        TIME_LIMIT                    ,
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 11;

CREATE VIEW E_TERRAFORM_CLI_PATTERN_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                ,
        JOURNAL_REG_DATETIME          ,
        JOURNAL_ACTION_CLASS          ,
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TERRAFORM_CLI_WORKSPACE_ID    ,
        TIME_LIMIT                    ,
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH_JNL TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 11;


--作業管理 VIEW
CREATE VIEW E_TERRAFORM_CLI_EXE_INS_MNG AS
SELECT 
         TAB_A.EXECUTION_NO              ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.SYMPHONY_INSTANCE_NO      , -- Symphonyインスタンス番号
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_TERRAFORM_WORKSPACE_ID  ,
         TAB_A.I_TERRAFORM_WORKSPACE     ,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM C_TERRAFORM_CLI_EXE_INS_MNG       TAB_A
LEFT JOIN E_TERRAFORM_CLI_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_TERRAFORM_CLI_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_TERRAFORM_CLI_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
;

CREATE VIEW E_TERRAFORM_CLI_EXE_INS_MNG_JNL AS 
SELECT 
         TAB_A.JOURNAL_SEQ_NO            ,
         TAB_A.JOURNAL_REG_DATETIME      ,
         TAB_A.JOURNAL_ACTION_CLASS      ,
         TAB_A.EXECUTION_NO              ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.SYMPHONY_INSTANCE_NO      , -- Symphonyインスタンス番号
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_TERRAFORM_WORKSPACE_ID  ,
         TAB_A.I_TERRAFORM_WORKSPACE     ,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER           
FROM C_TERRAFORM_CLI_EXE_INS_MNG_JNL   TAB_A
LEFT JOIN E_TERRAFORM_CLI_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_TERRAFORM_CLI_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_TERRAFORM_CLI_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
;

--Movement*変数の連番振り分け用VIEW
CREATE VIEW D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_1 AS 
SELECT DISTINCT
        TAB_A.PATTERN_ID                    ,
        TAB_B.MODULE_VARS_LINK_ID           ,
        TAB_B.MODULE_MATTER_ID              ,
        TAB_B.VARS_NAME                     
from E_TERRAFORM_CLI_PATTERN TAB_A 
CROSS JOIN B_TERRAFORM_CLI_MODULE_VARS_LINK TAB_B
;

CREATE VIEW D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 AS 
SELECT
        ROW_NUMBER() OVER(ORDER BY TAB_A.PATTERN_ID, TAB_A.MODULE_VARS_LINK_ID) MODULE_PTN_LINK_ID,
        TAB_A.PATTERN_ID                    ,
        TAB_A.MODULE_VARS_LINK_ID           ,
        TAB_A.MODULE_MATTER_ID              ,
        TAB_A.VARS_NAME                     
FROM D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_1 TAB_A
;


--代入値管理 VIEW
CREATE VIEW D_TERRAFORM_CLI_VARS_ASSIGN AS
SELECT
  TAB_A.*,
  TAB_B.MODULE_PTN_LINK_ID            VARS_PTN_LINK_ID,
  TAB_B.MODULE_PTN_LINK_ID            REST_MODULE_VARS_LINK_ID,
  TAB_A.MEMBER_VARS                   REST_MEMBER_VARS
FROM
  B_TERRAFORM_CLI_VARS_ASSIGN TAB_A
LEFT JOIN
  D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
;

CREATE VIEW D_TERRAFORM_CLI_VARS_ASSIGN_JNL AS
SELECT
  TAB_A.*,
  TAB_B.MODULE_PTN_LINK_ID            VARS_PTN_LINK_ID,
  TAB_B.MODULE_PTN_LINK_ID            REST_MODULE_VARS_LINK_ID,
  TAB_A.MEMBER_VARS                   REST_MEMBER_VARS
FROM
  B_TERRAFORM_CLI_VARS_ASSIGN_JNL TAB_A
LEFT JOIN
  D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
;

--代入値自動登録設定メニュー用　VIEW
CREATE VIEW D_TERRAFORM_CLI_VAL_ASSIGN_SUB AS 
SELECT
  c1.*,
  CASE WHEN c1.COL_TYPE = 3 THEN m1.MODULE_PTN_LINK_ID
    WHEN c1.COL_TYPE = 1 THEN m1.MODULE_PTN_LINK_ID
    ELSE NULL
  END AS VAL_VARS_PTN_LINK_ID,
  CASE WHEN c1.COL_TYPE = 3 THEN m2.MODULE_PTN_LINK_ID
    WHEN c1.COL_TYPE = 2 THEN m2.MODULE_PTN_LINK_ID
    ELSE NULL
  END AS KEY_VARS_PTN_LINK_ID
FROM B_TERRAFORM_CLI_VAL_ASSIGN AS c1
LEFT JOIN D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 AS m1
  ON c1.PATTERN_ID = m1.PATTERN_ID AND c1.VAL_VARS_LINK_ID = m1.MODULE_VARS_LINK_ID
LEFT JOIN D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 AS m2
  ON c1.PATTERN_ID = m2.PATTERN_ID AND c1.KEY_VARS_LINK_ID = m2.MODULE_VARS_LINK_ID
ORDER BY c1.COLUMN_ID
;

CREATE VIEW D_TERRAFORM_CLI_VAL_ASSIGN AS 
SELECT
        TAB_A.COLUMN_ID                      , -- 識別シーケンス
        TAB_A.MENU_ID                        , -- メニューID
        TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
        TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
        TAB_A.PATTERN_ID                     , -- 作業パターンID
        TAB_A.VAL_VARS_LINK_ID               , -- Value値　Module変数紐付
        TAB_A.KEY_VARS_LINK_ID               , -- Key値　Module変数紐付
        TAB_A.KEY_ASSIGN_SEQ                 , -- Keyの代入順序
        TAB_A.KEY_MEMBER_VARS                , -- Keyのメンバ変数
        TAB_A.VAL_ASSIGN_SEQ                 , -- Valueの代入順序
        TAB_A.VAL_MEMBER_VARS                , -- Valueのメンバ変数
        TAB_A.VAL_VARS_PTN_LINK_ID           , -- Value値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_VARS_PTN_LINK_ID           , -- Key値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.HCL_FLAG                       , -- HCL設定
        TAB_A.VAL_VARS_HCL_FLAG              , -- Value値 HCL設定
        TAB_A.KEY_VARS_HCL_FLAG              , -- Key値 HCL設定
        TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
        TAB_B.MENU_GROUP_ID                  ,
        TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
        TAB_C.MENU_GROUP_NAME                ,
        TAB_A.MENU_ID           MENU_ID_CLONE,
        TAB_A.MENU_ID           MENU_ID_CLONE_02,
        TAB_B.MENU_NAME                      ,
        TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
        TAB_A.VAL_VARS_PTN_LINK_ID REST_VAL_VARS_LINK_ID, -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.VAL_MEMBER_VARS       REST_VAL_MEMBER_VARS, -- REST/EXCEL/CSV用　Value値　変数名+メンバー変数
        TAB_A.KEY_VARS_PTN_LINK_ID REST_KEY_VARS_LINK_ID, -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_MEMBER_VARS       REST_KEY_MEMBER_VARS, -- REST/EXCEL/CSV用　Key値　変数名+メンバー変数
        TAB_A.DISP_SEQ                       ,
        TAB_A.ACCESS_AUTH                    ,
        TAB_A.NOTE                           ,
        TAB_A.DISUSE_FLAG                    ,
        TAB_A.LAST_UPDATE_TIMESTAMP          ,
        TAB_A.LAST_UPDATE_USER 
FROM D_TERRAFORM_CLI_VAL_ASSIGN_SUB AS TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
;


CREATE VIEW D_TERRAFORM_CLI_VAL_ASSIGN_SUB_JNL AS 
SELECT
  c1.*,
  CASE WHEN c1.COL_TYPE = 3 THEN m1.MODULE_PTN_LINK_ID
    WHEN c1.COL_TYPE = 1 THEN m1.MODULE_PTN_LINK_ID
    ELSE NULL
  END AS VAL_VARS_PTN_LINK_ID,
  CASE WHEN c1.COL_TYPE = 3 THEN m2.MODULE_PTN_LINK_ID
    WHEN c1.COL_TYPE = 2 THEN m2.MODULE_PTN_LINK_ID
    ELSE NULL
  END AS KEY_VARS_PTN_LINK_ID
FROM B_TERRAFORM_CLI_VAL_ASSIGN_JNL AS c1
LEFT JOIN D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 AS m1
  ON c1.PATTERN_ID = m1.PATTERN_ID AND c1.VAL_VARS_LINK_ID = m1.MODULE_VARS_LINK_ID
LEFT JOIN D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 AS m2
  ON c1.PATTERN_ID = m2.PATTERN_ID AND c1.KEY_VARS_LINK_ID = m2.MODULE_VARS_LINK_ID
ORDER BY c1.COLUMN_ID
;


CREATE VIEW D_TERRAFORM_CLI_VAL_ASSIGN_JNL AS 
SELECT
        TAB_A.JOURNAL_SEQ_NO                 ,
        TAB_A.JOURNAL_REG_DATETIME           ,
        TAB_A.JOURNAL_ACTION_CLASS           ,
        TAB_A.COLUMN_ID                      , -- 識別シーケンス
        TAB_A.MENU_ID                        , -- メニューID
        TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
        TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
        TAB_A.PATTERN_ID                     , -- 作業パターンID
        TAB_A.VAL_VARS_LINK_ID               , -- Value値　Module変数紐付
        TAB_A.KEY_VARS_LINK_ID               , -- Key値　Module変数紐付
        TAB_A.KEY_ASSIGN_SEQ                 , -- Keyの代入順序
        TAB_A.KEY_MEMBER_VARS                , -- Keyのメンバ変数
        TAB_A.VAL_ASSIGN_SEQ                 , -- Valueの代入順序
        TAB_A.VAL_MEMBER_VARS                , -- Valueのメンバ変数
        TAB_A.VAL_VARS_PTN_LINK_ID           , -- Value値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_VARS_PTN_LINK_ID           , -- Key値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.HCL_FLAG                       , -- HCL設定
        TAB_A.VAL_VARS_HCL_FLAG              , -- Value値 HCL設定
        TAB_A.KEY_VARS_HCL_FLAG              , -- Key値 HCL設定
        TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
        TAB_B.MENU_GROUP_ID                  ,
        TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
        TAB_C.MENU_GROUP_NAME                ,
        TAB_A.MENU_ID           MENU_ID_CLONE,
        TAB_A.MENU_ID           MENU_ID_CLONE_02,
        TAB_B.MENU_NAME                      ,
        TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
        TAB_A.VAL_VARS_PTN_LINK_ID REST_VAL_VARS_LINK_ID, -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.VAL_MEMBER_VARS       REST_VAL_MEMBER_VARS, -- REST/EXCEL/CSV用　Value値　変数名+メンバー変数
        TAB_A.KEY_VARS_PTN_LINK_ID REST_KEY_VARS_LINK_ID, -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_MEMBER_VARS       REST_KEY_MEMBER_VARS, -- REST/EXCEL/CSV用　Key値　変数名+メンバー変数
        TAB_A.DISP_SEQ                       ,
        TAB_A.ACCESS_AUTH                    ,
        TAB_A.NOTE                           ,
        TAB_A.DISUSE_FLAG                    ,
        TAB_A.LAST_UPDATE_TIMESTAMP          ,
        TAB_A.LAST_UPDATE_USER 
FROM D_TERRAFORM_CLI_VAL_ASSIGN_SUB_JNL AS TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
;


--Module変数紐付管理 VIEW
CREATE VIEW D_TERRAFORM_CLI_PTN_VARS_LINK AS 
SELECT 
        TAB_D.MODULE_PTN_LINK_ID            ,
        TAB_A.MODULE_VARS_LINK_ID           ,
        TAB_B.PATTERN_ID                    ,
        TAB_C.PATTERN_NAME                  ,
        TAB_A.VARS_NAME                     ,
        CONCAT(TAB_A.MODULE_VARS_LINK_ID,':',TAB_A.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.TYPE_ID                       ,
        TAB_A.VARS_VALUE                    ,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_TERRAFORM_CLI_MODULE_VARS_LINK     TAB_A
LEFT JOIN B_TERRAFORM_CLI_PATTERN_LINK  TAB_B ON ( TAB_A.MODULE_MATTER_ID = TAB_B.MODULE_MATTER_ID )
LEFT JOIN E_TERRAFORM_CLI_PATTERN       TAB_C ON ( TAB_B.PATTERN_ID = TAB_C.PATTERN_ID )
LEFT JOIN D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 TAB_D ON ( TAB_B.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_D.MODULE_VARS_LINK_ID )
;

CREATE VIEW D_TERRAFORM_CLI_PTN_VARS_LINK_JNL AS 
SELECT
        JOURNAL_SEQ_NO                      ,
        JOURNAL_REG_DATETIME                ,
        JOURNAL_ACTION_CLASS                ,
        TAB_D.MODULE_PTN_LINK_ID            ,
        TAB_A.MODULE_VARS_LINK_ID           ,
        TAB_B.PATTERN_ID                    ,
        TAB_C.PATTERN_NAME                  ,
        TAB_A.VARS_NAME                     ,
        CONCAT(TAB_A.MODULE_VARS_LINK_ID,':',TAB_A.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.TYPE_ID                       ,
        TAB_A.VARS_VALUE                    ,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_TERRAFORM_CLI_MODULE_VARS_LINK_JNL     TAB_A
LEFT JOIN B_TERRAFORM_CLI_PATTERN_LINK  TAB_B ON ( TAB_A.MODULE_MATTER_ID = TAB_B.MODULE_MATTER_ID )
LEFT JOIN E_TERRAFORM_CLI_PATTERN       TAB_C ON ( TAB_B.PATTERN_ID = TAB_C.PATTERN_ID )
LEFT JOIN D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 TAB_D ON ( TAB_B.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_D.MODULE_VARS_LINK_ID )
;

--Module変数紐付プルダウン用 VIEW
CREATE VIEW D_TERRAFORM_CLI_PTN_VARS_LINK_VFP AS 
SELECT 
        TAB_D.MODULE_PTN_LINK_ID      ,
        TAB_A.MODULE_VARS_LINK_ID      ,
        TAB_B.PATTERN_ID              ,
        TAB_C.PATTERN_NAME            ,
        TAB_A.VARS_NAME               ,
        CONCAT(TAB_A.MODULE_VARS_LINK_ID,':',TAB_A.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.ACCESS_AUTH             ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER        ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_TERRAFORM_CLI_MODULE_VARS_LINK     TAB_A
LEFT JOIN B_TERRAFORM_CLI_PATTERN_LINK  TAB_B ON ( TAB_A.MODULE_MATTER_ID = TAB_B.MODULE_MATTER_ID )
LEFT JOIN E_TERRAFORM_CLI_PATTERN       TAB_C ON ( TAB_B.PATTERN_ID = TAB_C.PATTERN_ID )
LEFT JOIN D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 TAB_D ON ( TAB_B.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_D.MODULE_VARS_LINK_ID  )
WHERE TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;

--変数データ紐付(backyard処理用) VIEW
CREATE VIEW D_TERRAFORM_CLI_VARS_DATA AS
SELECT 
         TAB_A.ASSIGN_ID                 ,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.PATTERN_ID                ,
         TAB_A.MODULE_VARS_LINK_ID       ,
         TAB_B.VARS_NAME                 ,
         TAB_A.VARS_ENTRY                ,
         TAB_A.MEMBER_VARS               ,
         TAB_A.ASSIGN_SEQ                ,
         TAB_A.HCL_FLAG                  ,
         TAB_A.SENSITIVE_FLAG            ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM B_TERRAFORM_CLI_VARS_ASSIGN         TAB_A
LEFT JOIN D_TERRAFORM_CLI_PTN_VARS_LINK  TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID AND TAB_B.MODULE_VARS_LINK_ID = TAB_A.MODULE_VARS_LINK_ID )
;


-- Operationプルダウン VIEW
CREATE VIEW E_OPE_FOR_PULLDOWN_TERRAFORM_CLI
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
       TAB_A.LAST_UPDATE_USER
FROM 
    E_OPERATION_LIST TAB_A
WHERE
    TAB_A.DISUSE_FLAG IN ('0') 
;

-- -------------------------------------------------------
-- Terraform 代入値管理/代入値自動登録用 REST API対応
--        Movement+変数名  リスト用 View
-- -------------------------------------------------------
CREATE VIEW E_TERRAFORM_CLI_PTN_VAR_LIST AS
SELECT DISTINCT
  TAB_A.MODULE_PTN_LINK_ID               ,
  TAB_A.MODULE_VARS_LINK_ID              ,
  TAB_A.PATTERN_ID                       ,
  TAB_A.PATTERN_NAME                     ,
  TAB_A.VARS_NAME                        ,
  TAB_A.VARS_LINK_PULLDOWN               ,
  TAB_A.DISP_SEQ                         ,
  TAB_C.ACCESS_AUTH                      ,
  TAB_A.NOTE                             ,
  TAB_A.DISUSE_FLAG                      ,
  TAB_A.LAST_UPDATE_TIMESTAMP            ,
  TAB_A.LAST_UPDATE_USER                 ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01    ,
  TAB_A.ACCESS_AUTH_01 AS ACCESS_AUTH_02 ,
  TAB_A.ACCESS_AUTH_02 AS ACCESS_AUTH_03 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_04    ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.MODULE_VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  D_TERRAFORM_CLI_PTN_VARS_LINK_VFP          TAB_A
  LEFT JOIN B_TERRAFORM_CLI_MODULE_VARS_LINK TAB_B ON ( TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
  LEFT JOIN C_PATTERN_PER_ORCH           TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
  LEFT JOIN B_TERRAFORM_CLI_PATTERN_LINK     TAB_D ON ( TAB_A.PATTERN_ID   = TAB_D.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0' AND
  TAB_D.DISUSE_FLAG = '0';

CREATE VIEW E_TERRAFORM_CLI_PTN_VAR_LIST_JNL AS
SELECT DISTINCT
  TAB_A.MODULE_PTN_LINK_ID               ,
  TAB_A.MODULE_VARS_LINK_ID              ,
  TAB_A.PATTERN_ID                       ,
  TAB_A.PATTERN_NAME                     ,
  TAB_A.VARS_NAME                        ,
  TAB_A.VARS_LINK_PULLDOWN               ,
  TAB_A.DISP_SEQ                         ,
  TAB_C.ACCESS_AUTH                      ,
  TAB_A.NOTE                             ,
  TAB_A.DISUSE_FLAG                      ,
  TAB_A.LAST_UPDATE_TIMESTAMP            ,
  TAB_A.LAST_UPDATE_USER                 ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01    ,
  TAB_A.ACCESS_AUTH_01 AS ACCESS_AUTH_02 ,
  TAB_A.ACCESS_AUTH_02 AS ACCESS_AUTH_03 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_04    ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.MODULE_VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  D_TERRAFORM_CLI_PTN_VARS_LINK_JNL     TAB_A
  LEFT JOIN B_TERRAFORM_CLI_MODULE_VARS_LINK_JNL TAB_B ON ( TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
  LEFT JOIN C_PATTERN_PER_ORCH_JNL      TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
  LEFT JOIN B_TERRAFORM_CLI_PATTERN_LINK     TAB_D ON ( TAB_A.PATTERN_ID   = TAB_D.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0' AND
  TAB_D.DISUSE_FLAG = '0';

-- -------------------------------------------------------
-- Terraform Movement変数紐付ページ用 View
-- -------------------------------------------------------
CREATE VIEW D_TERRAFORM_CLI_PTN_VAR_LIST AS
SELECT DISTINCT
  TAB_A.MODULE_PTN_LINK_ID               ,
  TAB_A.PATTERN_ID                       ,
  TAB_A.MODULE_VARS_LINK_ID              ,
  TAB_A.MODULE_MATTER_ID                 ,
  TAB_A.VARS_NAME                        ,
  CONCAT(TAB_A.MODULE_VARS_LINK_ID,':',TAB_A.VARS_NAME) VARS_LINK_PULLDOWN,
  TAB_B.DISP_SEQ                         ,
  TAB_B.ACCESS_AUTH                      ,
  TAB_B.NOTE                             ,
  CASE WHEN TAB_B.DISUSE_FLAG = 1 THEN 1
       WHEN TAB_C.DISUSE_FLAG = 1 THEN 1
       WHEN TAB_D.LINK_ID IS NULL THEN 1
       ELSE 0
       END AS DISUSE_FLAG                ,
  TAB_B.LAST_UPDATE_TIMESTAMP            ,
  TAB_B.LAST_UPDATE_USER                 ,    
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_01    ,
  TAB_D.ACCESS_AUTH AS ACCESS_AUTH_02
FROM
  D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 TAB_A
  LEFT JOIN E_TERRAFORM_CLI_PATTERN TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID)
  LEFT JOIN B_TERRAFORM_CLI_MODULE_VARS_LINK TAB_C ON (TAB_A.MODULE_VARS_LINK_ID = TAB_C.MODULE_VARS_LINK_ID)
  LEFT JOIN B_TERRAFORM_CLI_PATTERN_LINK TAB_D ON (TAB_A.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_MATTER_ID = TAB_D.MODULE_MATTER_ID AND TAB_D.DISUSE_FLAG = 0)
ORDER BY TAB_A.MODULE_PTN_LINK_ID
;

CREATE VIEW D_TERRAFORM_CLI_PTN_VAR_LIST_JNL AS
SELECT DISTINCT
  TAB_B.JOURNAL_SEQ_NO                   ,
  TAB_B.JOURNAL_REG_DATETIME             ,
  TAB_B.JOURNAL_ACTION_CLASS             ,
  TAB_A.MODULE_PTN_LINK_ID               ,
  TAB_A.PATTERN_ID                       ,
  TAB_A.MODULE_VARS_LINK_ID              ,
  TAB_A.MODULE_MATTER_ID                 ,
  TAB_A.VARS_NAME                        ,
  CONCAT(TAB_A.MODULE_VARS_LINK_ID,':',TAB_A.VARS_NAME) VARS_LINK_PULLDOWN,
  TAB_B.DISP_SEQ                         ,
  TAB_B.ACCESS_AUTH                      ,
  TAB_B.NOTE                             ,
  CASE WHEN TAB_B.DISUSE_FLAG = 1 THEN 1
       WHEN TAB_C.DISUSE_FLAG = 1 THEN 1
       WHEN TAB_D.LINK_ID IS NULL THEN 1
       ELSE 0
       END AS DISUSE_FLAG                ,
  TAB_B.LAST_UPDATE_TIMESTAMP            ,
  TAB_B.LAST_UPDATE_USER                 ,    
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_01    ,
  TAB_D.ACCESS_AUTH AS ACCESS_AUTH_02          
FROM
  D_TERRAFORM_CLI_MODULE_PTN_VARS_LINK_2 TAB_A
  LEFT JOIN E_TERRAFORM_CLI_PATTERN_JNL TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID)
  LEFT JOIN B_TERRAFORM_CLI_MODULE_VARS_LINK TAB_C ON (TAB_A.MODULE_VARS_LINK_ID = TAB_C.MODULE_VARS_LINK_ID)
  LEFT JOIN B_TERRAFORM_CLI_PATTERN_LINK TAB_D ON (TAB_A.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_MATTER_ID = TAB_D.MODULE_MATTER_ID AND TAB_D.DISUSE_FLAG = 0)
;

--Module素材 VIEW
CREATE VIEW D_TERRAFORM_CLI_MODULE AS
SELECT  MODULE_MATTER_ID      ,
        MODULE_MATTER_NAME    ,
        CONCAT(MODULE_MATTER_ID,':',MODULE_MATTER_NAME) MODULE,
        MODULE_MATTER_FILE    ,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_CLI_MODULE;

CREATE VIEW D_TERRAFORM_CLI_MODULE_JNL AS
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        MODULE_MATTER_ID      ,
        MODULE_MATTER_NAME    ,
        CONCAT(MODULE_MATTER_ID,':',MODULE_MATTER_NAME) MODULE,
        MODULE_MATTER_FILE    ,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_CLI_MODULE_JNL;

--メンバー変数管理VIEW
CREATE VIEW D_TERRAFORM_CLI_VAR_MEMBER AS
SELECT
        CHILD_MEMBER_VARS_ID,
        PARENT_VARS_ID,
        PARENT_MEMBER_VARS_ID,
        CHILD_MEMBER_VARS_NEST,
        CHILD_MEMBER_VARS_KEY,
        CHILD_VARS_TYPE_ID,
        ARRAY_NEST_LEVEL,
        ASSIGN_SEQ,
        CHILD_MEMBER_VARS_VALUE,
          CASE
            WHEN
            NOT EXISTS(
              SELECT PARENT_MEMBER_VARS_ID FROM B_TERRAFORM_CLI_VAR_MEMBER AS TAB_B WHERE TAB_B.PARENT_MEMBER_VARS_ID = TAB_A.CHILD_MEMBER_VARS_ID AND TAB_B.DISUSE_FLAG = 0 OR TAB_A.CHILD_VARS_TYPE_ID = 7 AND TAB_B.DISUSE_FLAG = 0
            )
            AND
            NOT EXISTS(
              SELECT CHILD_MEMBER_VARS_ID FROM B_TERRAFORM_CLI_VAR_MEMBER AS TAB_B WHERE PARENT_VARS_ID = TAB_A.PARENT_VARS_ID AND TAB_B.CHILD_VARS_TYPE_ID = 7 AND TAB_B.DISUSE_FLAG = 0
            )
            THEN 1
            ELSE 0
          END AS VARS_ASSIGN_FLAG,
        DISP_SEQ,
        ACCESS_AUTH,
        NOTE,
        DISUSE_FLAG,
        LAST_UPDATE_TIMESTAMP,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_CLI_VAR_MEMBER AS TAB_A;

CREATE VIEW D_TERRAFORM_CLI_VAR_MEMBER_JNL AS
SELECT
TAB_A.JOURNAL_SEQ_NO,
TAB_A.JOURNAL_REG_DATETIME,
TAB_A.JOURNAL_ACTION_CLASS,
TAB_A.CHILD_MEMBER_VARS_ID,
TAB_A.PARENT_VARS_ID,
TAB_A.PARENT_MEMBER_VARS_ID,
TAB_A.CHILD_MEMBER_VARS_NEST,
TAB_A.CHILD_MEMBER_VARS_KEY,
TAB_A.CHILD_VARS_TYPE_ID,
TAB_A.ARRAY_NEST_LEVEL,
TAB_A.ASSIGN_SEQ,
TAB_A.CHILD_MEMBER_VARS_VALUE,
CASE
WHEN
NOT EXISTS(
  SELECT PARENT_MEMBER_VARS_ID FROM B_TERRAFORM_CLI_VAR_MEMBER AS TAB_B WHERE TAB_B.PARENT_MEMBER_VARS_ID = TAB_A.CHILD_MEMBER_VARS_ID AND TAB_B.DISUSE_FLAG = 0 OR TAB_A.CHILD_VARS_TYPE_ID = 7 AND TAB_B.DISUSE_FLAG = 0
)
AND
NOT EXISTS(
  SELECT CHILD_MEMBER_VARS_ID FROM B_TERRAFORM_CLI_VAR_MEMBER AS TAB_B WHERE PARENT_VARS_ID = TAB_A.PARENT_VARS_ID AND TAB_B.CHILD_VARS_TYPE_ID = 7 AND TAB_B.DISUSE_FLAG = 0
)
THEN 1
ELSE 0
END AS VARS_ASSIGN_FLAG,
TAB_A.DISP_SEQ,
TAB_A.ACCESS_AUTH,
TAB_A.NOTE,
TAB_A.DISUSE_FLAG,
TAB_A.LAST_UPDATE_TIMESTAMP,
TAB_A.LAST_UPDATE_USER
FROM B_TERRAFORM_CLI_VAR_MEMBER_JNL AS TAB_A;

--代入値管理/代入値自動登録　変数名+メンバー変数  リスト用 View
CREATE VIEW E_TERRAFORM_CLI_VAR_MEMBER_LIST AS
SELECT DISTINCT
  TAB_A.CHILD_MEMBER_VARS_ID,
  TAB_A.PARENT_VARS_ID,
  TAB_A.PARENT_MEMBER_VARS_ID,
  TAB_A.CHILD_MEMBER_VARS_NEST,
  TAB_A.CHILD_MEMBER_VARS_KEY,
  TAB_A.CHILD_VARS_TYPE_ID,
  TAB_A.ARRAY_NEST_LEVEL,
  TAB_A.ASSIGN_SEQ,
  TAB_A.DISP_SEQ,
  TAB_A.CHILD_MEMBER_VARS_VALUE,
  TAB_A.ACCESS_AUTH,
  TAB_A.NOTE,
  TAB_A.DISUSE_FLAG,
  TAB_A.LAST_UPDATE_TIMESTAMP,
  TAB_A.LAST_UPDATE_USER,
  CONCAT(TAB_B.VARS_NAME,'.',TAB_A.CHILD_MEMBER_VARS_ID,':',TAB_A.CHILD_MEMBER_VARS_NEST) VAR_MEMBER_PULLDOWN 
FROM
  D_TERRAFORM_CLI_VAR_MEMBER          TAB_A
  LEFT JOIN B_TERRAFORM_CLI_MODULE_VARS_LINK    TAB_B ON ( TAB_A.PARENT_VARS_ID = TAB_B.MODULE_VARS_LINK_ID)
WHERE
  TAB_A.VARS_ASSIGN_FLAG = '1' AND
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE VIEW E_TERRAFORM_CLI_VAR_MEMBER_LIST_JNL AS
SELECT DISTINCT
  TAB_A.CHILD_MEMBER_VARS_ID,
  TAB_A.PARENT_VARS_ID,
  TAB_A.PARENT_MEMBER_VARS_ID,
  TAB_A.CHILD_MEMBER_VARS_NEST,
  TAB_A.CHILD_MEMBER_VARS_KEY,
  TAB_A.CHILD_VARS_TYPE_ID,
  TAB_A.ARRAY_NEST_LEVEL,
  TAB_A.ASSIGN_SEQ,
  TAB_A.DISP_SEQ,
  TAB_A.CHILD_MEMBER_VARS_VALUE,
  TAB_A.ACCESS_AUTH,
  TAB_A.NOTE,
  TAB_A.DISUSE_FLAG,
  TAB_A.LAST_UPDATE_TIMESTAMP,
  TAB_A.LAST_UPDATE_USER,
  CONCAT(TAB_B.VARS_NAME,'.',TAB_A.CHILD_MEMBER_VARS_ID,':',TAB_A.CHILD_MEMBER_VARS_NEST) VAR_MEMBER_PULLDOWN 
FROM
  D_TERRAFORM_CLI_VAR_MEMBER          TAB_A
  LEFT JOIN B_TERRAFORM_CLI_MODULE_VARS_LINK    TAB_B ON ( TAB_A.PARENT_VARS_ID = TAB_B.MODULE_VARS_LINK_ID)
WHERE
  TAB_A.VARS_ASSIGN_FLAG = '1' AND
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';
-- *****************************************************************************
-- *** Terraform Views *****                                                 ***
-- *****************************************************************************

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_IF_INFO_RIC',2,'2100200001',2100910001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_IF_INFO_JSQ',2,'2100200001',2100910002,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_WORKSPACES_RIC',1,'2100200002',2100910003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_WORKSPACES_JSQ',1,'2100200002',2100910004,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_MODULE_RIC',1,'2100200004',2100910005,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_MODULE_JSQ',1,'2100200004',2100910006,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_PATTERN_LINK_RIC',1,'2100200005',2100910007,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_PATTERN_LINK_JSQ',1,'2100200005',2100910008,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_LRL_MAX_MEMBER_COL_RIC',1,'2100200006',2100910019,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_LRL_MAX_MEMBER_COL_JSQ',1,'2100200006',2100910020,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_VAL_ASSIGN_RIC',1,'2100200007',2100910011,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_VAL_ASSIGN_JSQ',1,'2100200007',2100910012,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_VARS_ASSIGN_RIC',1,'2100200008',2100910013,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_VARS_ASSIGN_JSQ',1,'2100200008',2100910014,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_TERRAFORM_CLI_EXE_INS_MNG_RIC',1,'2100200011',2100910009,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_TERRAFORM_CLI_EXE_INS_MNG_JSQ',1,'2100200011',2100910010,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_MODULE_VARS_LINK_RIC',1,'2100200012',2100910015,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_MODULE_VARS_LINK_JSQ',1,'2100200012',2100910016,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_VAR_MEMBER_RIC',1,'2100200013',2100910017,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_VAR_MEMBER_JSQ',1,'2100200013',2100910018,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_STATUS_RIC',11,NULL,2100910019,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_STATUS_JSQ',11,NULL,2100910020,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_RUN_MODE_RIC',3,NULL,2100910021,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_CLI_RUN_MODE_JSQ',3,NULL,2100910022,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));


INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100140001,'Terraform-CLI','terraform_cli.png',170,'Terraform-CLI','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-140001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100140001,'Terraform-CLI','terraform_cli.png',170,'Terraform-CLI','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200001,2100140001,'インターフェース情報',NULL,NULL,NULL,1,0,1,1,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200001,2100140001,'インターフェース情報',NULL,NULL,NULL,1,0,1,1,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200002,2100140001,'Workspaces管理',NULL,NULL,NULL,1,0,1,1,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200002,2100140001,'Workspaces管理',NULL,NULL,NULL,1,0,1,1,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200003,2100140001,'Movement一覧',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200003,2100140001,'Movement一覧',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200004,2100140001,'Module素材集',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200004,2100140001,'Module素材集',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200005,2100140001,'Movement-Module紐付',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200005,2100140001,'Movement-Module紐付',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200006,2100140001,'変数ネスト管理',NULL,NULL,NULL,1,0,1,2,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200006,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200006,2100140001,'変数ネスト管理',NULL,NULL,NULL,1,0,1,2,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200007,2100140001,'代入値自動登録設定',NULL,NULL,NULL,1,0,1,2,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200007,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200007,2100140001,'代入値自動登録設定',NULL,NULL,NULL,1,0,1,2,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200008,2100140001,'代入値管理',NULL,NULL,NULL,1,0,1,2,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200008,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200008,2100140001,'代入値管理',NULL,NULL,NULL,1,0,1,2,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200009,2100140001,'作業実行',NULL,NULL,NULL,1,0,1,1,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200009,2100140001,'作業実行',NULL,NULL,NULL,1,0,1,1,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200010,2100140001,'作業状態確認',NULL,NULL,NULL,1,0,1,2,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200010,2100140001,'作業状態確認',NULL,NULL,NULL,1,0,1,2,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200011,2100140001,'作業管理',NULL,NULL,NULL,1,0,1,2,110,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200011,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200011,2100140001,'作業管理',NULL,NULL,NULL,1,0,1,2,110,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200012,2100140001,'Module変数紐付管理',NULL,NULL,NULL,1,0,1,2,120,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200012,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200012,2100140001,'Module変数紐付管理',NULL,NULL,NULL,1,0,1,2,120,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200013,2100140001,'メンバー変数管理',NULL,NULL,NULL,1,0,1,2,130,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200013,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200013,2100140001,'メンバー変数管理',NULL,NULL,NULL,1,0,1,2,130,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200014,2100140001,'Movement変数紐付管理',NULL,NULL,NULL,1,0,1,2,140,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200014,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200014,2100140001,'Movement変数紐付管理',NULL,NULL,NULL,1,0,1,2,140,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101901,'t2c','5ebbc37e034d6874a2af59eb04beaa52','Terraform-CLI状態確認プロシージャ',NULL,NULL,NULL,NULL,'Terraform-CLI状態確認プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),NULL);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101901,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101901,'t2c','5ebbc37e034d6874a2af59eb04beaa52','Terraform-CLI状態確認プロシージャ',NULL,NULL,NULL,NULL,'Terraform-CLI状態確認プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),NULL);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101902,'t2e','5ebbc37e034d6874a2af59eb04beaa52','Terraform-CLI作業実行プロシージャ',NULL,NULL,NULL,NULL,'Terraform-CLI作業実行プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),NULL);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101902,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101902,'t2e','5ebbc37e034d6874a2af59eb04beaa52','Terraform-CLI作業実行プロシージャ',NULL,NULL,NULL,NULL,'Terraform-CLI作業実行プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),NULL);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101903,'t2a','5ebbc37e034d6874a2af59eb04beaa52','Terraform-CLI変数更新プロシージャ',NULL,NULL,NULL,NULL,'Terraform-CLI変数更新プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),NULL);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101903,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101903,'t2a','5ebbc37e034d6874a2af59eb04beaa52','Terraform-CLI変数更新プロシージャ',NULL,NULL,NULL,NULL,'Terraform-CLI変数更新プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),NULL);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101904,'t2b','5ebbc37e034d6874a2af59eb04beaa52','Terraform-CLI代入値自動登録設定プロシージャ',NULL,NULL,NULL,NULL,'Terraform-CLI代入値自動登録設定プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),NULL);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101904,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101904,'t2b','5ebbc37e034d6874a2af59eb04beaa52','Terraform-CLI代入値自動登録設定プロシージャ',NULL,NULL,NULL,NULL,'Terraform-CLI代入値自動登録設定プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),NULL);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200001,1,2100200001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200001,1,2100200001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200002,1,2100200002,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200002,1,2100200002,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200003,1,2100200003,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200003,1,2100200003,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200004,1,2100200004,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200004,1,2100200004,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200005,1,2100200005,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200005,1,2100200005,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200006,1,2100200006,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200006,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200006,1,2100200006,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200007,1,2100200007,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200007,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200007,1,2100200007,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200008,1,2100200008,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200008,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200008,1,2100200008,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200009,1,2100200009,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200009,1,2100200009,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200010,1,2100200010,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200010,1,2100200010,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200011,1,2100200011,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200011,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200011,1,2100200011,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200012,1,2100200012,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200012,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200012,1,2100200012,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200013,1,2100200013,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200013,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200013,1,2100200013,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100200014,1,2100200014,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-200014,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100200014,1,2100200014,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000025,3600,7200,'B_TERRAFORM_CLI_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'代入値管理(Terraform-CLI)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000025,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000025,3600,7200,'B_TERRAFORM_CLI_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'代入値管理(Terraform-CLI)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000026,3600,7200,'C_TERRAFORM_CLI_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK',NULL,'uploadfiles/2100200011/FILE_INPUT/','uploadfiles/2100200011/FILE_RESULT/',NULL,NULL,'作業管理(Terraform-CLI)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000026,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000026,3600,7200,'C_TERRAFORM_CLI_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK',NULL,'uploadfiles/2100200011/FILE_INPUT/','uploadfiles/2100200011/FILE_RESULT/',NULL,NULL,'作業管理(Terraform-CLI)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_PROC_LOADED_LIST (ROW_ID,PROC_NAME,LOADED_FLG,LAST_UPDATE_TIMESTAMP) VALUES(2100080003,'ky_terraform_cli_varsautolistup-workflow','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_PROC_LOADED_LIST (ROW_ID,PROC_NAME,LOADED_FLG,LAST_UPDATE_TIMESTAMP) VALUES(2100080004,'ky_terraform_cli_valautosetup-workflow','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));


INSERT INTO B_ITA_EXT_STM_MASTER (ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,MENU_ID,EXEC_INS_MNG_TABLE_NAME,LOG_TARGET,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,'Terraform CLI','terraform_cli_driver',2100200011,'C_TERRAFORM_CLI_EXE_INS_MNG','1',11,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ITA_EXT_STM_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,MENU_ID,EXEC_INS_MNG_TABLE_NAME,LOG_TARGET,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',11,'Terraform CLI','terraform_cli_driver',2100200011,'C_TERRAFORM_CLI_EXE_INS_MNG','1',11,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'未実行',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'未実行',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'準備中',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'準備中',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'実行中',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'実行中',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'実行中(遅延)',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'実行中(遅延)',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'完了',5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'完了',5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'完了(異常)',6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',6,'完了(異常)',6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'想定外エラー',7,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',7,'想定外エラー',7,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'緊急停止',8,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',8,'緊急停止',8,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'未実行(予約)',9,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',9,'未実行(予約)',9,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'予約取消',10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',10,'予約取消',10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'通常',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'通常',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Plan確認',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Plan確認',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'リソース削除',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'リソース削除',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_IF_INFO (TERRAFORM_IF_INFO_ID,TERRAFORM_REFRESH_INTERVAL,TERRAFORM_TAILLOG_LINES,TERRAFORM_NUM_PARALLEL_EXEC,NULL_DATA_HANDLING_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'3000','1000','100','2',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_IF_INFO_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TERRAFORM_IF_INFO_ID,TERRAFORM_REFRESH_INTERVAL,TERRAFORM_TAILLOG_LINES,TERRAFORM_NUM_PARALLEL_EXEC,NULL_DATA_HANDLING_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'3000','1000','100','2',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_HCL_FLAG (HCL_FLAG,HCL_FLAG_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'OFF',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_HCL_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HCL_FLAG,HCL_FLAG_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'OFF',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_HCL_FLAG (HCL_FLAG,HCL_FLAG_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'ON',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_CLI_HCL_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HCL_FLAG,HCL_FLAG_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'ON',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('1','string',0,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('2','number',0,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('3','bool',0,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('4','null',0,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('5','list',0,1,1,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('6','tuple',1,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('7','map',0,0,1,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('8','object',1,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('9','set',0,1,1,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('10','list(list) ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('11','list(set)',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('12','set(list) ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('13','set(set) ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('14','list(tuple)  ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('15','list(object)  ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('16','set(tuple)  ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('17','set(object)  ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_CLI_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('18','any',0,0,1,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);



COMMIT;
