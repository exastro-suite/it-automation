-- *****************************************************************************
-- *** ***** Terraform Common Tables                                         ***
-- *****************************************************************************
-- ----更新系テーブル作成
-- インタフェース情報
CREATE TABLE B_TERRAFORM_IF_INFO
(
TERRAFORM_IF_INFO_ID              INT                              ,
TERRAFORM_HOSTNAME                VARCHAR (256)                    ,
TERRAFORM_TOKEN                   VARCHAR (512)                    ,
TERRAFORM_PROXY_ADDRESS           VARCHAR (128)                    ,
TERRAFORM_PROXY_PORT              INT                              ,
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
CREATE TABLE B_TERRAFORM_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
TERRAFORM_IF_INFO_ID              INT                              ,
TERRAFORM_HOSTNAME                VARCHAR (256)                    ,
TERRAFORM_TOKEN                   VARCHAR (512)                    ,
TERRAFORM_PROXY_ADDRESS           VARCHAR (128)                    ,
TERRAFORM_PROXY_PORT              INT                              ,
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
CREATE TABLE B_TERRAFORM_STATUS
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
CREATE TABLE B_TERRAFORM_STATUS_JNL
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
CREATE TABLE B_TERRAFORM_RUN_MODE
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
CREATE TABLE B_TERRAFORM_RUN_MODE_JNL
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
CREATE TABLE B_TERRAFORM_HCL_FLAG
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
CREATE TABLE B_TERRAFORM_HCL_FLAG_JNL
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

-- *****************************************************************************
-- *** ***** Terraform Common Tables                                         ***
-- *****************************************************************************

-- *****************************************************************************
-- *** ***** Terraform Tables                                                ***
-- *****************************************************************************
-- ----更新系テーブル作成
--Organizations管理
CREATE TABLE B_TERRAFORM_ORGANIZATIONS
(
ORGANIZATION_ID                   INT                              ,
ORGANIZATION_NAME                 VARCHAR (40)                     ,
EMAIL_ADDRESS                     VARCHAR (128)                    ,
CHECK_RESULT                      VARCHAR (8)                      ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (ORGANIZATION_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--Organizations管理(履歴)
CREATE TABLE B_TERRAFORM_ORGANIZATIONS_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
ORGANIZATION_ID                   INT                              ,
ORGANIZATION_NAME                 VARCHAR (40)                     ,
EMAIL_ADDRESS                     VARCHAR (128)                    ,
CHECK_RESULT                      VARCHAR (8)                      ,
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
--Workspaces管理
CREATE TABLE B_TERRAFORM_WORKSPACES
(
WORKSPACE_ID                      INT                              ,
ORGANIZATION_ID                   INT                              ,
WORKSPACE_NAME                    VARCHAR (90)                     ,
TERRAFORM_VERSION                 VARCHAR (32)                     ,
CHECK_RESULT                      VARCHAR (8)                      ,
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
CREATE TABLE B_TERRAFORM_WORKSPACES_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
WORKSPACE_ID                      INT                              ,
ORGANIZATION_ID                   INT                              ,
WORKSPACE_NAME                    VARCHAR (90)                     ,
TERRAFORM_VERSION                 VARCHAR (32)                     ,
CHECK_RESULT                      VARCHAR (8)                      ,
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
CREATE TABLE B_TERRAFORM_MODULE
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
CREATE TABLE B_TERRAFORM_MODULE_JNL
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
--Policy管理
CREATE TABLE B_TERRAFORM_POLICY
(
POLICY_ID                         INT                              ,   
POLICY_NAME                       VARCHAR (256)                    , 
POLICY_MATTER_FILE                VARCHAR (256)                    ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (POLICY_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--Policy管理(履歴)
CREATE TABLE B_TERRAFORM_POLICY_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
POLICY_ID                         INT                              ,
POLICY_NAME                       VARCHAR (256)                    , 
POLICY_MATTER_FILE                VARCHAR (256)                    ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


-- ----更新系テーブル作成
--PolicySet管理
CREATE TABLE B_TERRAFORM_POLICY_SETS
(
POLICY_SET_ID                     INT                              ,
POLICY_SET_NAME                   VARCHAR (256)                    ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (POLICY_SET_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--PolicySet管理(履歴)
CREATE TABLE B_TERRAFORM_POLICY_SETS_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
POLICY_SET_ID                     INT                              ,
POLICY_SET_NAME                   VARCHAR (256)                    ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- ----更新系テーブル作成
--PolicySet-Policy紐付管理
CREATE TABLE B_TERRAFORM_POLICYSET_POLICY_LINK
(
POLICYSET_POLICY_LINK_ID          INT                              ,
POLICY_SET_ID                     INT                              ,
POLICY_ID                         INT                              ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (POLICYSET_POLICY_LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--PolicySet-Policy紐付管理(履歴)
CREATE TABLE B_TERRAFORM_POLICYSET_POLICY_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
POLICYSET_POLICY_LINK_ID          INT                              ,
POLICY_SET_ID                     INT                              ,
POLICY_ID                         INT                              ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- ----更新系テーブル作成
--PolicySet-Workspace紐付管理
CREATE TABLE B_TERRAFORM_POLICYSET_WORKSPACE_LINK
(
POLICYSET_WORKSPACE_LINK_ID       INT                              ,
POLICY_SET_ID                     INT                              ,
WORKSPACE_ID                      INT                              ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (POLICYSET_WORKSPACE_LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--PolicySet-Workspace紐付管理(履歴)
CREATE TABLE B_TERRAFORM_POLICYSET_WORKSPACE_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
POLICYSET_WORKSPACE_LINK_ID       INT                              ,
POLICY_SET_ID                     INT                              ,
WORKSPACE_ID                      INT                              ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


-- ----更新系テーブル作成
--作業パターン詳細
CREATE TABLE B_TERRAFORM_PATTERN_LINK
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
CREATE TABLE B_TERRAFORM_PATTERN_LINK_JNL
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
CREATE TABLE C_TERRAFORM_EXE_INS_MNG
(
EXECUTION_NO                      INT                              ,
EXECUTION_USER                    VARCHAR (80)                     ,
SYMPHONY_NAME                     VARCHAR (256)                    ,
STATUS_ID                         INT                              ,
SYMPHONY_INSTANCE_NO              INT                              ,
PATTERN_ID                        INT                              ,
I_PATTERN_NAME                    VARCHAR (256)                    ,
I_TIME_LIMIT                      INT                              ,
I_TERRAFORM_RUN_ID                VARCHAR (32)                     ,
I_TERRAFORM_WORKSPACE_ID          INT                              ,
I_TERRAFORM_ORGANIZATION_WORKSPACE VARCHAR (256)                   ,
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
CREATE TABLE C_TERRAFORM_EXE_INS_MNG_JNL
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
I_TERRAFORM_RUN_ID                VARCHAR (32)                     ,
I_TERRAFORM_WORKSPACE_ID          INT                              ,
I_TERRAFORM_ORGANIZATION_WORKSPACE VARCHAR (256)                   ,
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
CREATE TABLE B_TERRAFORM_VARS_ASSIGN
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
CREATE TABLE B_TERRAFORM_VARS_ASSIGN_JNL
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
CREATE TABLE B_TERRAFORM_VAL_ASSIGN (
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
CREATE TABLE B_TERRAFORM_VAL_ASSIGN_JNL
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
CREATE TABLE B_TERRAFORM_MODULE_VARS_LINK
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
CREATE TABLE B_TERRAFORM_MODULE_VARS_LINK_JNL
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
--変数タイプ一覧
CREATE TABLE B_TERRAFORM_TYPES_MASTER
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
-- 更新系テーブル作成----

-- ----更新系テーブル作成
--メンバー変数管理
CREATE TABLE B_TERRAFORM_VAR_MEMBER
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
CREATE TABLE B_TERRAFORM_VAR_MEMBER_JNL
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
CREATE TABLE B_TERRAFORM_LRL_MAX_MEMBER_COL
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
CREATE TABLE B_TERRAFORM_LRL_MAX_MEMBER_COL_JNL
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
CREATE VIEW D_TERRAFORM_IF_INFO AS 
SELECT * 
FROM B_TERRAFORM_IF_INFO;

CREATE VIEW D_TERRAFORM_IF_INFO_JNL AS 
SELECT * 
FROM B_TERRAFORM_IF_INFO_JNL;

-- ステータス VIEW
CREATE VIEW D_TERRAFORM_INS_STATUS AS 
SELECT * 
FROM B_TERRAFORM_STATUS;

CREATE VIEW D_TERRAFORM_INS_STATUS_JNL AS 
SELECT * 
FROM B_TERRAFORM_STATUS_JNL;

--実行モード情報 VIEW
CREATE VIEW D_TERRAFORM_INS_RUN_MODE AS 
SELECT * 
FROM B_TERRAFORM_RUN_MODE;

CREATE VIEW D_TERRAFORM_INS_RUN_MODE_JNL AS 
SELECT * 
FROM B_TERRAFORM_RUN_MODE_JNL;

--Organizations-Workspaces紐付 VIEW
CREATE VIEW D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK AS
SELECT
        TAB_B.ORGANIZATION_ID         ,
        TAB_B.ORGANIZATION_NAME ORGANIZATION_NAME       ,
        TAB_A.WORKSPACE_ID            ,
        TAB_A.WORKSPACE_NAME WORKSPACE_NAME          ,
        CONCAT(ORGANIZATION_NAME,':',WORKSPACE_NAME) ORGANIZATION_WORKSPACE,
        TAB_A.DISP_SEQ             ,
        TAB_A.ACCESS_AUTH          ,
        TAB_A.NOTE                 ,
        TAB_A.DISUSE_FLAG          ,
        TAB_A.LAST_UPDATE_TIMESTAMP,
        TAB_A.LAST_UPDATE_USER     ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM B_TERRAFORM_WORKSPACES TAB_A
LEFT JOIN B_TERRAFORM_ORGANIZATIONS TAB_B ON ( TAB_A.ORGANIZATION_ID = TAB_B.ORGANIZATION_ID )
;
CREATE VIEW D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK_JNL AS
SELECT 
        TAB_A.JOURNAL_SEQ_NO                ,
        TAB_A.JOURNAL_REG_DATETIME          ,
        TAB_A.JOURNAL_ACTION_CLASS          ,
        TAB_B.ORGANIZATION_ID         ,
        TAB_B.ORGANIZATION_NAME ORGANIZATION_NAME       ,
        TAB_A.WORKSPACE_ID            ,
        TAB_A.WORKSPACE_NAME WORKSPACE_NAME          ,
        CONCAT(ORGANIZATION_NAME,':',WORKSPACE_NAME) ORGANIZATION_WORKSPACE,
        TAB_A.DISP_SEQ             ,
        TAB_A.ACCESS_AUTH          ,
        TAB_A.NOTE                 ,
        TAB_A.DISUSE_FLAG          ,
        TAB_A.LAST_UPDATE_TIMESTAMP,
        TAB_A.LAST_UPDATE_USER     ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM B_TERRAFORM_WORKSPACES_JNL TAB_A
LEFT JOIN B_TERRAFORM_ORGANIZATIONS_JNL TAB_B ON ( TAB_A.ORGANIZATION_ID = TAB_B.ORGANIZATION_ID )
;

--作業パターン詳細 VIEW
CREATE VIEW E_TERRAFORM_PATTERN AS 
SELECT 
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TERRAFORM_WORKSPACE_ID        ,
        TIME_LIMIT                    ,
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 10;

CREATE VIEW E_TERRAFORM_PATTERN_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                ,
        JOURNAL_REG_DATETIME          ,
        JOURNAL_ACTION_CLASS          ,
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TERRAFORM_WORKSPACE_ID        ,
        TIME_LIMIT                    ,
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH_JNL TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 10;


--作業管理 VIEW
CREATE VIEW E_TERRAFORM_EXE_INS_MNG AS
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
         TAB_A.I_TERRAFORM_RUN_ID        ,
         TAB_A.I_TERRAFORM_WORKSPACE_ID  ,
         TAB_A.I_TERRAFORM_ORGANIZATION_WORKSPACE,
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
FROM C_TERRAFORM_EXE_INS_MNG       TAB_A
LEFT JOIN E_TERRAFORM_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_TERRAFORM_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_TERRAFORM_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
;

CREATE VIEW E_TERRAFORM_EXE_INS_MNG_JNL AS 
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
         TAB_A.I_TERRAFORM_RUN_ID        ,
         TAB_A.I_TERRAFORM_WORKSPACE_ID  ,
         TAB_A.I_TERRAFORM_ORGANIZATION_WORKSPACE,
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
FROM C_TERRAFORM_EXE_INS_MNG_JNL   TAB_A
LEFT JOIN E_TERRAFORM_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_TERRAFORM_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_TERRAFORM_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
;

--Movement*変数の連番振り分け用VIEW
CREATE VIEW D_TERRAFORM_MODULE_PTN_VARS_LINK_1 AS 
SELECT DISTINCT
        TAB_A.PATTERN_ID                    ,
        TAB_B.MODULE_VARS_LINK_ID           ,
        TAB_B.MODULE_MATTER_ID              ,
        TAB_B.VARS_NAME                     
from E_TERRAFORM_PATTERN TAB_A 
CROSS JOIN B_TERRAFORM_MODULE_VARS_LINK TAB_B
;

CREATE VIEW D_TERRAFORM_MODULE_PTN_VARS_LINK_2 AS 
SELECT
        ROW_NUMBER() OVER(ORDER BY TAB_A.PATTERN_ID, TAB_A.MODULE_VARS_LINK_ID) MODULE_PTN_LINK_ID,
        TAB_A.PATTERN_ID                    ,
        TAB_A.MODULE_VARS_LINK_ID           ,
        TAB_A.MODULE_MATTER_ID              ,
        TAB_A.VARS_NAME                     
FROM D_TERRAFORM_MODULE_PTN_VARS_LINK_1 TAB_A
;


--代入値管理 VIEW
CREATE VIEW D_TERRAFORM_VARS_ASSIGN AS
SELECT
  TAB_A.*,
  TAB_B.MODULE_PTN_LINK_ID            VARS_PTN_LINK_ID,
  TAB_B.MODULE_PTN_LINK_ID            REST_MODULE_VARS_LINK_ID,
  TAB_A.MEMBER_VARS                   REST_MEMBER_VARS
FROM
  B_TERRAFORM_VARS_ASSIGN TAB_A
LEFT JOIN
  D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
;

CREATE VIEW D_TERRAFORM_VARS_ASSIGN_JNL AS
SELECT
  TAB_A.*,
  TAB_B.MODULE_PTN_LINK_ID            VARS_PTN_LINK_ID,
  TAB_B.MODULE_PTN_LINK_ID            REST_MODULE_VARS_LINK_ID,
  TAB_A.MEMBER_VARS                   REST_MEMBER_VARS
FROM
  B_TERRAFORM_VARS_ASSIGN_JNL TAB_A
LEFT JOIN
  D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
;

--代入値自動登録設定メニュー用　VIEW
CREATE VIEW D_TERRAFORM_VAL_ASSIGN AS 
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
FROM (
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
        FROM B_TERRAFORM_VAL_ASSIGN AS c1
        LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 AS m1
          ON c1.PATTERN_ID = m1.PATTERN_ID AND c1.VAL_VARS_LINK_ID = m1.MODULE_VARS_LINK_ID
        LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 AS m2
          ON c1.PATTERN_ID = m2.PATTERN_ID AND c1.KEY_VARS_LINK_ID = m2.MODULE_VARS_LINK_ID
        ORDER BY c1.COLUMN_ID
) AS TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
;


CREATE VIEW D_TERRAFORM_VAL_ASSIGN_JNL AS 
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
FROM (
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
        FROM B_TERRAFORM_VAL_ASSIGN_JNL AS c1
        LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 AS m1
          ON c1.PATTERN_ID = m1.PATTERN_ID AND c1.VAL_VARS_LINK_ID = m1.MODULE_VARS_LINK_ID
        LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 AS m2
          ON c1.PATTERN_ID = m2.PATTERN_ID AND c1.KEY_VARS_LINK_ID = m2.MODULE_VARS_LINK_ID
        ORDER BY c1.COLUMN_ID
) AS TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
;


--Module変数紐付管理 VIEW
CREATE VIEW D_TERRAFORM_PTN_VARS_LINK AS 
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
FROM B_TERRAFORM_MODULE_VARS_LINK     TAB_A
LEFT JOIN B_TERRAFORM_PATTERN_LINK  TAB_B ON ( TAB_A.MODULE_MATTER_ID = TAB_B.MODULE_MATTER_ID )
LEFT JOIN E_TERRAFORM_PATTERN       TAB_C ON ( TAB_B.PATTERN_ID = TAB_C.PATTERN_ID )
LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_D ON ( TAB_B.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_D.MODULE_VARS_LINK_ID )
;

CREATE VIEW D_TERRAFORM_PTN_VARS_LINK_JNL AS 
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
FROM B_TERRAFORM_MODULE_VARS_LINK_JNL     TAB_A
LEFT JOIN B_TERRAFORM_PATTERN_LINK  TAB_B ON ( TAB_A.MODULE_MATTER_ID = TAB_B.MODULE_MATTER_ID )
LEFT JOIN E_TERRAFORM_PATTERN       TAB_C ON ( TAB_B.PATTERN_ID = TAB_C.PATTERN_ID )
LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_D ON ( TAB_B.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_D.MODULE_VARS_LINK_ID )
;

--Module変数紐付プルダウン用 VIEW
CREATE VIEW D_TERRAFORM_PTN_VARS_LINK_VFP AS 
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
FROM B_TERRAFORM_MODULE_VARS_LINK     TAB_A
LEFT JOIN B_TERRAFORM_PATTERN_LINK  TAB_B ON ( TAB_A.MODULE_MATTER_ID = TAB_B.MODULE_MATTER_ID )
LEFT JOIN E_TERRAFORM_PATTERN       TAB_C ON ( TAB_B.PATTERN_ID = TAB_C.PATTERN_ID )
LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_D ON ( TAB_B.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_D.MODULE_VARS_LINK_ID  )
WHERE TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;

--変数データ紐付(backyard処理用) VIEW
CREATE VIEW D_TERRAFORM_VARS_DATA AS
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
FROM B_TERRAFORM_VARS_ASSIGN         TAB_A
LEFT JOIN D_TERRAFORM_PTN_VARS_LINK  TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID AND TAB_B.MODULE_VARS_LINK_ID = TAB_A.MODULE_VARS_LINK_ID )
;


-- Operationプルダウン VIEW
CREATE VIEW E_OPE_FOR_PULLDOWN_TERRAFORM
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
CREATE VIEW E_TERRAFORM_PTN_VAR_LIST AS
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
  D_TERRAFORM_PTN_VARS_LINK_VFP          TAB_A
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK TAB_B ON ( TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
  LEFT JOIN C_PATTERN_PER_ORCH           TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
  LEFT JOIN B_TERRAFORM_PATTERN_LINK     TAB_D ON ( TAB_A.PATTERN_ID   = TAB_D.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0' AND
  TAB_D.DISUSE_FLAG = '0';

CREATE VIEW E_TERRAFORM_PTN_VAR_LIST_JNL AS
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
  D_TERRAFORM_PTN_VARS_LINK_JNL     TAB_A
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK_JNL TAB_B ON ( TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
  LEFT JOIN C_PATTERN_PER_ORCH_JNL      TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
  LEFT JOIN B_TERRAFORM_PATTERN_LINK     TAB_D ON ( TAB_A.PATTERN_ID   = TAB_D.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0' AND
  TAB_D.DISUSE_FLAG = '0';

-- -------------------------------------------------------
-- Terraform Movement変数紐付ページ用 View
-- -------------------------------------------------------
CREATE VIEW D_TERRAFORM_PTN_VAR_LIST AS
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
  D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_A
  LEFT JOIN E_TERRAFORM_PATTERN TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID)
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK TAB_C ON (TAB_A.MODULE_VARS_LINK_ID = TAB_C.MODULE_VARS_LINK_ID)
  LEFT JOIN B_TERRAFORM_PATTERN_LINK TAB_D ON (TAB_A.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_MATTER_ID = TAB_D.MODULE_MATTER_ID AND TAB_D.DISUSE_FLAG = 0)
ORDER BY TAB_A.MODULE_PTN_LINK_ID
;

CREATE VIEW D_TERRAFORM_PTN_VAR_LIST_JNL AS
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
  D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_A
  LEFT JOIN E_TERRAFORM_PATTERN_JNL TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID)
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK TAB_C ON (TAB_A.MODULE_VARS_LINK_ID = TAB_C.MODULE_VARS_LINK_ID)
  LEFT JOIN B_TERRAFORM_PATTERN_LINK TAB_D ON (TAB_A.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_MATTER_ID = TAB_D.MODULE_MATTER_ID AND TAB_D.DISUSE_FLAG = 0)
;

--Module素材 VIEW
CREATE VIEW D_TERRAFORM_MODULE AS
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
FROM    B_TERRAFORM_MODULE;

CREATE VIEW D_TERRAFORM_MODULE_JNL AS
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
FROM    B_TERRAFORM_MODULE_JNL;

--Policy管理 VIEW
CREATE VIEW D_TERRAFORM_POLICY AS
SELECT  POLICY_ID      ,
        POLICY_NAME    ,
        CONCAT(POLICY_ID,':',POLICY_NAME) POLICY,
        POLICY_MATTER_FILE    ,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_POLICY;

CREATE VIEW D_TERRAFORM_POLICY_JNL AS
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        POLICY_ID             ,
        POLICY_NAME           ,
        CONCAT(POLICY_ID,':',POLICY_NAME) POLICY,
        POLICY_MATTER_FILE    ,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_POLICY_JNL;

--PolicySet管理 VIEW
CREATE VIEW D_TERRAFORM_POLICY_SETS AS
SELECT  POLICY_SET_ID      ,
        POLICY_SET_NAME    ,
        CONCAT(POLICY_SET_ID,':',POLICY_SET_NAME) POLICY_SET,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_POLICY_SETS;

CREATE VIEW D_TERRAFORM_POLICY_SETS_JNL AS
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        POLICY_SET_ID             ,
        POLICY_SET_NAME           ,
        CONCAT(POLICY_SET_ID,':',POLICY_SET_NAME) POLICY_SET,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_POLICY_SETS_JNL;

CREATE VIEW D_TERRAFORM_VAR_MEMBER AS
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
              SELECT PARENT_MEMBER_VARS_ID FROM B_TERRAFORM_VAR_MEMBER AS TAB_B WHERE TAB_B.PARENT_MEMBER_VARS_ID = TAB_A.CHILD_MEMBER_VARS_ID AND TAB_B.DISUSE_FLAG = 0 OR TAB_A.CHILD_VARS_TYPE_ID = 7 AND TAB_B.DISUSE_FLAG = 0
            )
            AND
            NOT EXISTS(
              SELECT CHILD_MEMBER_VARS_ID FROM B_TERRAFORM_VAR_MEMBER AS TAB_B WHERE PARENT_VARS_ID = TAB_A.PARENT_VARS_ID AND TAB_B.CHILD_VARS_TYPE_ID = 7 AND TAB_B.DISUSE_FLAG = 0
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
FROM    B_TERRAFORM_VAR_MEMBER AS TAB_A;

CREATE VIEW D_TERRAFORM_VAR_MEMBER_JNL AS
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
  SELECT PARENT_MEMBER_VARS_ID FROM B_TERRAFORM_VAR_MEMBER AS TAB_B WHERE TAB_B.PARENT_MEMBER_VARS_ID = TAB_A.CHILD_MEMBER_VARS_ID AND TAB_B.DISUSE_FLAG = 0 OR TAB_A.CHILD_VARS_TYPE_ID = 7 AND TAB_B.DISUSE_FLAG = 0
)
AND
NOT EXISTS(
  SELECT CHILD_MEMBER_VARS_ID FROM B_TERRAFORM_VAR_MEMBER AS TAB_B WHERE PARENT_VARS_ID = TAB_A.PARENT_VARS_ID AND TAB_B.CHILD_VARS_TYPE_ID = 7 AND TAB_B.DISUSE_FLAG = 0
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
FROM B_TERRAFORM_VAR_MEMBER_JNL AS TAB_A;

--代入値管理/代入値自動登録　変数名+メンバー変数  リスト用 View
CREATE VIEW E_TERRAFORM_VAR_MEMBER_LIST AS
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
  D_TERRAFORM_VAR_MEMBER          TAB_A
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK    TAB_B ON ( TAB_A.PARENT_VARS_ID = TAB_B.MODULE_VARS_LINK_ID)
WHERE
  TAB_A.VARS_ASSIGN_FLAG = '1' AND
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE VIEW E_TERRAFORM_VAR_MEMBER_LIST_JNL AS
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
  D_TERRAFORM_VAR_MEMBER          TAB_A
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK    TAB_B ON ( TAB_A.PARENT_VARS_ID = TAB_B.MODULE_VARS_LINK_ID)
WHERE
  TAB_A.VARS_ASSIGN_FLAG = '1' AND
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';
-- *****************************************************************************
-- *** Terraform Views *****                                                 ***
-- *****************************************************************************

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_IF_INFO_RIC',2,'2100080001',2100810001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_IF_INFO_JSQ',2,'2100080001',2100810002,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_ORGANIZATIONS_RIC',1,'2100080002',2100810003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_ORGANIZATIONS_JSQ',1,'2100080002',2100810004,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_WORKSPACES_RIC',1,'2100080003',2100810005,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_WORKSPACES_JSQ',1,'2100080003',2100810006,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_MODULE_RIC',1,'2100080005',2100810007,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_MODULE_JSQ',1,'2100080005',2100810008,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_PATTERN_LINK_RIC',1,'2100080007',2100810009,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_PATTERN_LINK_JSQ',1,'2100080007',2100810010,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_TERRAFORM_EXE_INS_MNG_RIC',1,'2100080011',2100810011,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_TERRAFORM_EXE_INS_MNG_JSQ',1,'2100080011',2100810012,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_POLICY_RIC',1,'2100080006',2100810013,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_POLICY_JSQ',1,'2100080006',2100810014,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_POLICY_SETS_RIC',1,'2100080012',2100810015,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_POLICY_SETS_JSQ',1,'2100080012',2100810016,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_POLICYSET_POLICY_LINK_RIC',1,'2100080013',2100810017,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_POLICYSET_POLICY_LINK_JSQ',1,'2100080013',2100810018,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_POLICYSET_WORKSPACE_LINK_RIC',1,'2100080014',2100810019,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_POLICYSET_WORKSPACE_LINK_JSQ',1,'2100080014',2100810020,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_VAL_ASSIGN_RIC',1,'2100080015',2100810021,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_VAL_ASSIGN_JSQ',1,'2100080015',2100810022,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_VARS_ASSIGN_RIC',1,'2100080008',2100810023,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_VARS_ASSIGN_JSQ',1,'2100080008',2100810024,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_MODULE_VARS_LINK_RIC',1,'2100080016',2100810025,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_MODULE_VARS_LINK_JSQ',1,'2100080016',2100810026,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_STATUS_RIC',11,NULL,2100890001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_STATUS_JSQ',11,NULL,2100890002,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_RUN_MODE_RIC',3,NULL,2100890003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_RUN_MODE_JSQ',3,NULL,2100890004,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_VAR_MEMBER_RIC',1,'2100080019',2100081019,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_VAR_MEMBER_JSQ',1,'2100080019',2100082019,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_LRL_MAX_MEMBER_COL_RIC',1,'2100080020',2100081020,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_TERRAFORM_LRL_MAX_MEMBER_COL_JSQ',1,'2100080020',2100082020,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));


INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080001,'Terraform','terraform.png',160,'Terraform','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080001,'Terraform','terraform.png',160,'Terraform','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080001,2100080001,'インターフェース情報',NULL,NULL,NULL,1,0,1,1,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080001,2100080001,'インターフェース情報',NULL,NULL,NULL,1,0,1,1,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080002,2100080001,'Organizations管理',NULL,NULL,NULL,1,0,1,1,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080002,2100080001,'Organizations管理',NULL,NULL,NULL,1,0,1,1,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080003,2100080001,'Workspaces管理',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080003,2100080001,'Workspaces管理',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080004,2100080001,'Movement一覧',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080004,2100080001,'Movement一覧',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080005,2100080001,'Module素材集',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080005,2100080001,'Module素材集',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080006,2100080001,'Policies管理',NULL,NULL,NULL,1,0,1,1,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80006,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080006,2100080001,'Policies管理',NULL,NULL,NULL,1,0,1,1,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080007,2100080001,'Movement-Module紐付',NULL,NULL,NULL,1,0,1,1,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80007,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080007,2100080001,'Movement-Module紐付',NULL,NULL,NULL,1,0,1,1,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080008,2100080001,'代入値管理',NULL,NULL,NULL,1,0,1,2,110,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80008,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080008,2100080001,'代入値管理',NULL,NULL,NULL,1,0,1,2,110,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080009,2100080001,'作業実行',NULL,NULL,NULL,1,0,1,1,120,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080009,2100080001,'作業実行',NULL,NULL,NULL,1,0,1,1,120,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080010,2100080001,'作業状態確認',NULL,NULL,NULL,1,0,1,2,130,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080010,2100080001,'作業状態確認',NULL,NULL,NULL,1,0,1,2,130,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080011,2100080001,'作業管理',NULL,NULL,NULL,1,0,1,2,140,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80011,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080011,2100080001,'作業管理',NULL,NULL,NULL,1,0,1,2,140,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080012,2100080001,'Policy Sets管理',NULL,NULL,NULL,1,0,1,1,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80012,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080012,2100080001,'Policy Sets管理',NULL,NULL,NULL,1,0,1,1,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080013,2100080001,'PolicySet-Policy紐付管理',NULL,NULL,NULL,1,0,1,1,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80013,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080013,2100080001,'PolicySet-Policy紐付管理',NULL,NULL,NULL,1,0,1,1,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080014,2100080001,'PolicySet-Workspace紐付管理',NULL,NULL,NULL,1,0,1,1,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80014,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080014,2100080001,'PolicySet-Workspace紐付管理',NULL,NULL,NULL,1,0,1,1,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080015,2100080001,'代入値自動登録設定',NULL,NULL,NULL,1,0,1,2,105,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80015,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080015,2100080001,'代入値自動登録設定',NULL,NULL,NULL,1,0,1,2,105,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080016,2100080001,'Module変数紐付管理',NULL,NULL,NULL,1,0,1,2,200,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80016,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080016,2100080001,'Module変数紐付管理',NULL,NULL,NULL,1,0,1,2,200,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080017,2100080001,'連携先Terraform管理',NULL,NULL,NULL,1,0,2,2,250,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80017,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080017,2100080001,'連携先Terraform管理',NULL,NULL,NULL,1,0,2,2,250,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080018,2100080001,'Movement変数紐付管理',NULL,NULL,NULL,1,0,1,2,210,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80018,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080018,2100080001,'Movement変数紐付管理',NULL,NULL,NULL,1,0,1,2,210,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080019,2100080001,'メンバー変数管理',NULL,NULL,NULL,1,0,1,2,205,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80019,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080019,2100080001,'メンバー変数管理',NULL,NULL,NULL,1,0,1,2,205,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080020,2100080001,'変数ネスト管理',NULL,NULL,NULL,1,0,1,2,103,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80020,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080020,2100080001,'変数ネスト管理',NULL,NULL,NULL,1,0,1,2,103,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101801,'t1c','5ebbc37e034d6874a2af59eb04beaa52','Terraform状態確認プロシージャ',NULL,NULL,NULL,NULL,'Terraform状態確認プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101801,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101801,'t1c','5ebbc37e034d6874a2af59eb04beaa52','Terraform状態確認プロシージャ',NULL,NULL,NULL,NULL,'Terraform状態確認プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101802,'t1e','5ebbc37e034d6874a2af59eb04beaa52','Terraform作業実行プロシージャ',NULL,NULL,NULL,NULL,'Terraform作業実行プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101802,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101802,'t1e','5ebbc37e034d6874a2af59eb04beaa52','Terraform作業実行プロシージャ',NULL,NULL,NULL,NULL,'Terraform作業実行プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101803,'t1a','5ebbc37e034d6874a2af59eb04beaa52','Terraform変数更新プロシージャ',NULL,NULL,NULL,NULL,'Terraform変数更新プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101803,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101803,'t1a','5ebbc37e034d6874a2af59eb04beaa52','Terraform変数更新プロシージャ',NULL,NULL,NULL,NULL,'Terraform変数更新プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101804,'t1b','5ebbc37e034d6874a2af59eb04beaa52','Terraform代入値自動登録設定プロシージャ',NULL,NULL,NULL,NULL,'Terraform代入値自動登録設定プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101804,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101804,'t1b','5ebbc37e034d6874a2af59eb04beaa52','Terraform代入値自動登録設定プロシージャ',NULL,NULL,NULL,NULL,'Terraform代入値自動登録設定プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080001,1,2100080001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080001,1,2100080001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080002,1,2100080002,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080002,1,2100080002,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080003,1,2100080003,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080003,1,2100080003,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080004,1,2100080004,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080004,1,2100080004,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080005,1,2100080005,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080005,1,2100080005,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080006,1,2100080006,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180006,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080006,1,2100080006,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080007,1,2100080007,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180007,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080007,1,2100080007,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080008,1,2100080008,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180008,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080008,1,2100080008,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080009,1,2100080009,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080009,1,2100080009,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080010,1,2100080010,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080010,1,2100080010,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080011,1,2100080011,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180011,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080011,1,2100080011,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080012,1,2100080012,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180012,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080012,1,2100080012,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080013,1,2100080013,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180013,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080013,1,2100080013,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080014,1,2100080014,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180014,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080014,1,2100080014,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080015,1,2100080015,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180015,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080015,1,2100080015,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080016,1,2100080016,1,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180016,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080016,1,2100080016,1,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080017,1,2100080017,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180017,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080017,1,2100080017,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101080004,2100000002,2100080004,2,'oaseアクション','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000023,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101080004,2100000002,2100080004,2,'oaseアクション','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101080008,2100000002,2100080008,2,'oaseアクション','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000024,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101080008,2100000002,2100080008,2,'oaseアクション','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080018,1,2100080018,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180018,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080018,1,2100080018,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080019,1,2100080019,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180019,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080019,1,2100080019,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080020,1,2100080020,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180020,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080020,1,2100080020,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000022,3600,7200,'B_TERRAFORM_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'代入値管理(Terraform)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000022,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000022,3600,7200,'B_TERRAFORM_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'代入値管理(Terraform)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000023,3600,7200,'C_TERRAFORM_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK',NULL,'uploadfiles/2100080011/FILE_INPUT/','uploadfiles/2100080011/FILE_RESULT/',NULL,NULL,'作業管理(Terraform)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000023,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000023,3600,7200,'C_TERRAFORM_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK',NULL,'uploadfiles/2100080011/FILE_INPUT/','uploadfiles/2100080011/FILE_RESULT/',NULL,NULL,'作業管理(Terraform)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_PROC_LOADED_LIST (ROW_ID,PROC_NAME,LOADED_FLG,LAST_UPDATE_TIMESTAMP) VALUES(2100080001,'ky_terraform_varsautolistup-workflow','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_PROC_LOADED_LIST (ROW_ID,PROC_NAME,LOADED_FLG,LAST_UPDATE_TIMESTAMP) VALUES(2100080002,'ky_terraform_valautosetup-workflow','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));


INSERT INTO B_ITA_EXT_STM_MASTER (ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,MENU_ID,EXEC_INS_MNG_TABLE_NAME,LOG_TARGET,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'Terraform','terraform_driver',2100080011,'C_TERRAFORM_EXE_INS_MNG','1',10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ITA_EXT_STM_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,MENU_ID,EXEC_INS_MNG_TABLE_NAME,LOG_TARGET,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',10,'Terraform','terraform_driver',2100080011,'C_TERRAFORM_EXE_INS_MNG','1',10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'未実行',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'未実行',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'準備中',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'準備中',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'実行中',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'実行中',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'実行中(遅延)',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'実行中(遅延)',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'完了',5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'完了',5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'完了(異常)',6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',6,'完了(異常)',6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'想定外エラー',7,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',7,'想定外エラー',7,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'緊急停止',8,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',8,'緊急停止',8,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'未実行(予約)',9,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',9,'未実行(予約)',9,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'予約取消',10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',10,'予約取消',10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'通常',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'通常',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Plan確認',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Plan確認',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'リソース削除',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'リソース削除',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_IF_INFO (TERRAFORM_IF_INFO_ID,TERRAFORM_HOSTNAME,TERRAFORM_TOKEN,TERRAFORM_PROXY_ADDRESS,TERRAFORM_PROXY_PORT,TERRAFORM_REFRESH_INTERVAL,TERRAFORM_TAILLOG_LINES,NULL_DATA_HANDLING_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Terraformのホスト名を記載','Terraformの[User Settings]より発行したTokenを記載',NULL,NULL,'3000','1000','2',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_IF_INFO_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TERRAFORM_IF_INFO_ID,TERRAFORM_HOSTNAME,TERRAFORM_TOKEN,TERRAFORM_PROXY_ADDRESS,TERRAFORM_PROXY_PORT,TERRAFORM_REFRESH_INTERVAL,TERRAFORM_TAILLOG_LINES,NULL_DATA_HANDLING_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Terraformのホスト名を記載','Terraformの[User Settings]より発行したTokenを記載',NULL,NULL,'3000','1000','2',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_HCL_FLAG (HCL_FLAG,HCL_FLAG_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'OFF',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_HCL_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HCL_FLAG,HCL_FLAG_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'OFF',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_HCL_FLAG (HCL_FLAG,HCL_FLAG_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'ON',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_HCL_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HCL_FLAG,HCL_FLAG_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'ON',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('1','string',0,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('2','number',0,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('3','bool',0,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('4','null',0,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('5','list',0,1,1,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('6','tuple',1,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('7','map',0,0,1,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('8','object',1,0,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('9','set',0,1,1,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('10','list(list) ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('11','list(set)',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('12','set(list) ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('13','set(set) ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('14','list(tuple)  ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('15','list(object)  ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('16','set(tuple)  ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('17','set(object)  ',1,1,0,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERRAFORM_TYPES_MASTER (TYPE_ID,TYPE_NAME,MEMBER_VARS_FLAG,ASSIGN_SEQ_FLAG,ENCODE_FLAG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('18','any',0,0,1,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);



COMMIT;
