ALTER TABLE A_ACCOUNT_LIST MODIFY COLUMN USERNAME VARCHAR(270);
ALTER TABLE A_ACCOUNT_LIST MODIFY COLUMN USERNAME_JP VARCHAR(270);
ALTER TABLE A_ACCOUNT_LIST ADD COLUMN AUTH_TYPE VARCHAR(10) AFTER PW_LAST_UPDATE_TIME;
ALTER TABLE A_ACCOUNT_LIST ADD COLUMN PROVIDER_ID INT AFTER AUTH_TYPE;
ALTER TABLE A_ACCOUNT_LIST ADD COLUMN PROVIDER_USER_ID VARCHAR(256) AFTER PROVIDER_ID;

ALTER TABLE A_ACCOUNT_LIST_JNL MODIFY COLUMN USERNAME VARCHAR(270);
ALTER TABLE A_ACCOUNT_LIST_JNL MODIFY COLUMN USERNAME_JP VARCHAR(270);
ALTER TABLE A_ACCOUNT_LIST_JNL ADD COLUMN AUTH_TYPE VARCHAR(10) AFTER PW_LAST_UPDATE_TIME;
ALTER TABLE A_ACCOUNT_LIST_JNL ADD COLUMN PROVIDER_ID INT AFTER AUTH_TYPE;
ALTER TABLE A_ACCOUNT_LIST_JNL ADD COLUMN PROVIDER_USER_ID VARCHAR(256) AFTER PROVIDER_ID;


ALTER TABLE A_MENU_GROUP_LIST MODIFY COLUMN MENU_GROUP_NAME VARCHAR(256);

ALTER TABLE A_MENU_GROUP_LIST_JNL MODIFY COLUMN MENU_GROUP_NAME VARCHAR(256);


ALTER TABLE A_MENU_LIST MODIFY COLUMN MENU_NAME VARCHAR(256);

ALTER TABLE A_MENU_LIST_JNL MODIFY COLUMN MENU_NAME VARCHAR(256);


CREATE TABLE A_PROVIDER_LIST
(
PROVIDER_ID                    INT                          , -- プロバイダーID
PROVIDER_NAME                  VARCHAR (100)                , -- プロバイダー名
LOGO                           VARCHAR (256)                , -- ロゴ
AUTH_TYPE                      VARCHAR (10)                 , -- 認証方式
VISIBLE_FLAG                   INT                          , -- 表示フラグ
NOTE                           VARCHAR (4000)               , -- 備考
DISUSE_FLAG                    VARCHAR (1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)                  , -- 最終更新日時
LAST_UPDATE_USER               INT                          , -- 最終更新ユーザ
PRIMARY KEY(PROVIDER_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_PROVIDER_ATTRIBUTE_LIST (
PROVIDER_ATTRIBUTE_ID          INT                          , -- 属性ID
PROVIDER_ID                    INT                          , -- プロバイダーID
NAME                           VARCHAR (100)                , -- 属性名
VALUE                          VARCHAR (256)                , -- 属性値
NOTE                           VARCHAR (4000)               , -- 備考
DISUSE_FLAG                    VARCHAR (1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)                  , -- 最終更新日時
LAST_UPDATE_USER               INT                          , -- 最終更新ユーザ
PRIMARY KEY (PROVIDER_ATTRIBUTE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_PROVIDER_AUTH_TYPE_LIST (
ID                             INT                          , -- ID
NAME                           VARCHAR (10)                 , -- 認証方式名称
NOTE                           VARCHAR (4000)               , -- 備考
DISUSE_FLAG                    VARCHAR (1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)                  , -- 最終更新日時
LAST_UPDATE_USER               INT                          , -- 最終更新ユーザ
PRIMARY KEY (ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_VISIBLE_FLAG_LIST (
ID                             INT                          , -- ID
FLAG                           VARCHAR (10)                 , -- 表示フラグ名称
NOTE                           VARCHAR (4000)               , -- 備考
DISUSE_FLAG                    VARCHAR (1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)                  , -- 最終更新日時
LAST_UPDATE_USER               INT                          , -- 最終更新ユーザ
PRIMARY KEY (ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_PROVIDER_ATTRIBUTE_NAME_LIST (
ID                             INT                          , -- SSO認証属性名称ID
NAME                           VARCHAR (50)                 , -- SSO認証属性名称
NOTE                           VARCHAR (4000)               , -- 備考
DISUSE_FLAG                    VARCHAR (1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)                  , -- 最終更新日時
LAST_UPDATE_USER               INT                          , -- 最終更新ユーザ
PRIMARY KEY (ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE A_PROVIDER_LIST_JNL
(
JOURNAL_SEQ_NO               INT                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME         DATETIME(6)                    , -- 履歴用変更日時
JOURNAL_ACTION_CLASS         VARCHAR (8)                    , -- 履歴用変更種別

PROVIDER_ID                  INT                            , -- プロバイダーID
PROVIDER_NAME                VARCHAR (100)                  , -- プロバイダー名
LOGO                         VARCHAR (256)                  , -- ロゴ
AUTH_TYPE                    VARCHAR (10)                   , -- 認証方式
VISIBLE_FLAG                 INT                            , -- 表示フラグ
NOTE                         VARCHAR (4000)                 , -- 備考
DISUSE_FLAG                  VARCHAR (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP        DATETIME(6)                    , -- 最終更新日時
LAST_UPDATE_USER             INT                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_PROVIDER_ATTRIBUTE_LIST_JNL (
JOURNAL_SEQ_NO                 INT                          , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           DATETIME(6)                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR (8)                  , -- 履歴用変更種別

PROVIDER_ATTRIBUTE_ID          INT                          , -- 属性ID
PROVIDER_ID                    INT                          , -- プロバイダーID
NAME                           VARCHAR (100)                , -- 属性名
VALUE                          VARCHAR (256)                , -- 属性値
NOTE                           VARCHAR (4000)               , -- 備考
DISUSE_FLAG                    VARCHAR (1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)                  , -- 最終更新日時
LAST_UPDATE_USER               INT                          , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_PROVIDER_AUTH_TYPE_LIST_JNL (
JOURNAL_SEQ_NO                 INT                          , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           DATETIME(6)                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR (8)                  , -- 履歴用変更種別

ID                             INT                          , -- ID
NAME                           VARCHAR (10)                 , -- 認証方式名称
NOTE                           VARCHAR (4000)               , -- 備考
DISUSE_FLAG                    VARCHAR (1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)                  , -- 最終更新日時
LAST_UPDATE_USER               INT                          , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_VISIBLE_FLAG_LIST_JNL (
JOURNAL_SEQ_NO                 INT                          , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           DATETIME(6)                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR (8)                  , -- 履歴用変更種別

ID                             INT                          , -- ID
FLAG                           VARCHAR (10)                 , -- 表示フラグ名称
NOTE                           VARCHAR (4000)               , -- 備考
DISUSE_FLAG                    VARCHAR (1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)                  , -- 最終更新日時
LAST_UPDATE_USER               INT                          , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (
JOURNAL_SEQ_NO                 INT                          , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           DATETIME(6)                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR (8)                  , -- 履歴用変更種別

ID                             INT                          , -- SSO認証属性名称ID
NAME                           VARCHAR (50)                 , -- SSO認証属性名称
NOTE                           VARCHAR (4000)               , -- 備考
DISUSE_FLAG                    VARCHAR (1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)                  , -- 最終更新日時
LAST_UPDATE_USER               INT                          , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


ALTER TABLE B_ITA_EXT_STM_MASTER ADD COLUMN MENU_ID INT AFTER ITA_EXT_LINK_LIB_PATH;
ALTER TABLE B_ITA_EXT_STM_MASTER ADD COLUMN EXEC_INS_MNG_TABLE_NAME VARCHAR(64) AFTER MENU_ID;
ALTER TABLE B_ITA_EXT_STM_MASTER ADD COLUMN LOG_TARGET INT AFTER EXEC_INS_MNG_TABLE_NAME;

ALTER TABLE B_ITA_EXT_STM_MASTER_JNL ADD COLUMN MENU_ID INT AFTER ITA_EXT_LINK_LIB_PATH;
ALTER TABLE B_ITA_EXT_STM_MASTER_JNL ADD COLUMN EXEC_INS_MNG_TABLE_NAME VARCHAR(64) AFTER MENU_ID;
ALTER TABLE B_ITA_EXT_STM_MASTER_JNL ADD COLUMN LOG_TARGET INT AFTER EXEC_INS_MNG_TABLE_NAME;


ALTER TABLE C_STM_LIST ADD COLUMN CREDENTIAL_TYPE_ID INT AFTER HOSTS_EXTRA_ARGS;
ALTER TABLE C_STM_LIST DROP COLUMN DSC_CERTIFICATE_FILE;
ALTER TABLE C_STM_LIST DROP COLUMN DSC_CERTIFICATE_THUMBPRINT;
UPDATE C_STM_LIST SET CREDENTIAL_TYPE_ID=1;

ALTER TABLE C_STM_LIST_JNL ADD COLUMN CREDENTIAL_TYPE_ID INT AFTER HOSTS_EXTRA_ARGS;
ALTER TABLE C_STM_LIST_JNL DROP COLUMN DSC_CERTIFICATE_FILE;
ALTER TABLE C_STM_LIST_JNL DROP COLUMN DSC_CERTIFICATE_THUMBPRINT;
UPDATE C_STM_LIST_JNL SET CREDENTIAL_TYPE_ID=1;


ALTER TABLE C_PATTERN_PER_ORCH ADD COLUMN TERRAFORM_WORKSPACE_ID INT AFTER OPENST_ENVIRONMENT;
ALTER TABLE C_PATTERN_PER_ORCH DROP COLUMN DSC_RETRY_TIMEOUT;

ALTER TABLE C_PATTERN_PER_ORCH_JNL ADD COLUMN TERRAFORM_WORKSPACE_ID INT AFTER OPENST_ENVIRONMENT;
ALTER TABLE C_PATTERN_PER_ORCH_JNL DROP COLUMN DSC_RETRY_TIMEOUT;


ALTER TABLE C_MOVEMENT_INSTANCE_MNG DROP COLUMN I_DSC_RETRY_TIMEOUT;

ALTER TABLE C_MOVEMENT_INSTANCE_MNG_JNL DROP COLUMN I_DSC_RETRY_TIMEOUT;


-- ----Conductorインターフェース
CREATE TABLE C_CONDUCTOR_IF_INFO
(
CONDUCTOR_IF_INFO_ID               INT                        , -- 識別シーケンス

CONDUCTOR_STORAGE_PATH_ITA         VARCHAR (256)              , -- ITA側のCONDUCTORインスタンス毎の共有ディレクトリ
CONDUCTOR_REFRESH_INTERVAL         INT                        , -- 状態監視周期(単位ミリ秒)

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ

PRIMARY KEY (CONDUCTOR_IF_INFO_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE C_CONDUCTOR_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    INT                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                , -- 履歴用変更種別

CONDUCTOR_IF_INFO_ID               INT                        , -- 識別シーケンス

CONDUCTOR_STORAGE_PATH_ITA         VARCHAR (256)              , -- ITA側のCONDUCTORインスタンス毎の共有ディレクトリ
CONDUCTOR_REFRESH_INTERVAL         INT                        , -- 状態監視周期(単位ミリ秒)

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- Conductorインターフェース----

-- ----Conductorクラス
CREATE TABLE C_CONDUCTOR_CLASS_MNG
(
CONDUCTOR_CLASS_NO                INT                        ,

CONDUCTOR_NAME                    VARCHAR (256)              ,
DESCRIPTION                       VARCHAR (4000)             ,

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ

PRIMARY KEY (CONDUCTOR_CLASS_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE C_CONDUCTOR_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    INT                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                , -- 履歴用変更種別

CONDUCTOR_CLASS_NO                INT                        ,

CONDUCTOR_NAME                    VARCHAR (256)              ,
DESCRIPTION                       VARCHAR (4000)             ,

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- Conductorクラス----

-- ----Nodeクラス
CREATE TABLE C_NODE_CLASS_MNG
(
NODE_CLASS_NO                     INT                        ,

NODE_NAME                         VARCHAR (256)              ,
NODE_TYPE_ID                      INT                        ,
ORCHESTRATOR_ID                   INT                        ,
PATTERN_ID                        INT                        ,
CONDUCTOR_CALL_CLASS_NO           INT                        ,
DESCRIPTION                       VARCHAR (4000)             ,
CONDUCTOR_CLASS_NO                INT                        ,
OPERATION_NO_IDBH                 INT                        ,
SKIP_FLAG                         INT                        ,
NEXT_PENDING_FLAG                 INT                        ,
POINT_X                           INT                        ,
POINT_Y                           INT                        ,
POINT_W                           INT                        ,
POINT_H                           INT                        ,

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ

PRIMARY KEY (NODE_CLASS_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE C_NODE_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    INT                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                , -- 履歴用変更種別

NODE_CLASS_NO                     INT                        ,

NODE_NAME                         VARCHAR (256)              ,
NODE_TYPE_ID                      INT                        ,
ORCHESTRATOR_ID                   INT                        ,
PATTERN_ID                        INT                        ,
CONDUCTOR_CALL_CLASS_NO           INT                        ,
DESCRIPTION                       VARCHAR (4000)             ,
CONDUCTOR_CLASS_NO                INT                        ,
OPERATION_NO_IDBH                 INT                        ,
SKIP_FLAG                         INT                        ,
NEXT_PENDING_FLAG                 INT                        ,
POINT_X                           INT                        ,
POINT_Y                           INT                        ,
POINT_W                           INT                        ,
POINT_H                           INT                        ,

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- Nodeクラス----

-- ----Terminalクラス
CREATE TABLE C_NODE_TERMINALS_CLASS_MNG
(
TERMINAL_CLASS_NO                 INT                        ,

TERMINAL_CLASS_NAME               VARCHAR (256)              ,
TERMINAL_TYPE_ID                  INT                        ,
NODE_CLASS_NO                     INT                        ,
CONDUCTOR_CLASS_NO                INT                        ,
CONNECTED_NODE_NAME               VARCHAR (256)              ,
LINE_NAME                         VARCHAR (256)              ,
TERMINAL_NAME                     VARCHAR (256)              ,
CONDITIONAL_ID                    VARCHAR (256)              ,
CASE_NO                           INT                        ,
DESCRIPTION                       VARCHAR (4000)             ,
POINT_X                           INT                        ,
POINT_Y                           INT                        ,

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ

PRIMARY KEY (TERMINAL_CLASS_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE C_NODE_TERMINALS_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    INT                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                , -- 履歴用変更種別

TERMINAL_CLASS_NO                 INT                        ,

TERMINAL_CLASS_NAME               VARCHAR (256)              ,
TERMINAL_TYPE_ID                  INT                        ,
NODE_CLASS_NO                     INT                        ,
CONDUCTOR_CLASS_NO                INT                        ,
CONNECTED_NODE_NAME               VARCHAR (256)              ,
LINE_NAME                         VARCHAR (256)              ,
TERMINAL_NAME                     VARCHAR (256)              ,
CONDITIONAL_ID                    VARCHAR (256)              ,
CASE_NO                           INT                        ,
DESCRIPTION                       VARCHAR (4000)             ,
POINT_X                           INT                        ,
POINT_Y                           INT                        ,

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- Terminalクラス----


-- ----Conductorインスタンス
CREATE TABLE C_CONDUCTOR_INSTANCE_MNG
(
CONDUCTOR_INSTANCE_NO             INT                        ,

I_CONDUCTOR_CLASS_NO              INT                        ,
I_CONDUCTOR_NAME                  VARCHAR (256)              ,
I_DESCRIPTION                     VARCHAR (4000)             ,
OPERATION_NO_UAPK                 INT                        ,
I_OPERATION_NAME                  VARCHAR (256)              , 
STATUS_ID                         INT                        ,
EXECUTION_USER                    VARCHAR (80)               ,
ABORT_EXECUTE_FLAG                INT                        ,
CONDUCTOR_CALL_FLAG               INT                        ,
CONDUCTOR_CALLER_NO               INT                        ,
TIME_BOOK                         DATETIME(6)                ,
TIME_START                        DATETIME(6)                ,
TIME_END                          DATETIME(6)                ,

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ

PRIMARY KEY (CONDUCTOR_INSTANCE_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE C_CONDUCTOR_INSTANCE_MNG_JNL
(
JOURNAL_SEQ_NO                    INT                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                , -- 履歴用変更種別
--
CONDUCTOR_INSTANCE_NO             INT                        ,
--
I_CONDUCTOR_CLASS_NO              INT                        ,
I_CONDUCTOR_NAME                   VARCHAR (256)              ,
I_DESCRIPTION                     VARCHAR (4000)             ,
OPERATION_NO_UAPK                 INT                        ,
I_OPERATION_NAME                  VARCHAR (256)              ,
STATUS_ID                         INT                        ,
EXECUTION_USER                    VARCHAR (80)               ,
ABORT_EXECUTE_FLAG                INT                        ,
CONDUCTOR_CALL_FLAG               INT                        ,
CONDUCTOR_CALLER_NO               INT                        ,
TIME_BOOK                         DATETIME(6)                ,
TIME_START                        DATETIME(6)                ,
TIME_END                          DATETIME(6)                ,

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- Conductorインスタンス----

-- ----Nodeインスタンス
CREATE TABLE C_NODE_INSTANCE_MNG
(
NODE_INSTANCE_NO                  INT                        ,

I_NODE_CLASS_NO                   INT                        ,
I_NODE_TYPE_ID                    INT                        ,
I_ORCHESTRATOR_ID                 INT                        ,
I_PATTERN_ID                      INT                        ,
I_PATTERN_NAME                    VARCHAR (256)              ,
I_TIME_LIMIT                      INT                        ,
I_ANS_HOST_DESIGNATE_TYPE_ID      INT                        ,
I_ANS_WINRM_ID                    INT                        ,
I_DSC_RETRY_TIMEOUT               INT                        ,
I_MOVEMENT_SEQ                    INT                        ,
I_NEXT_PENDING_FLAG               INT                        ,
I_DESCRIPTION                     VARCHAR (4000)             ,
CONDUCTOR_INSTANCE_NO             INT                        ,
CONDUCTOR_INSTANCE_CALL_NO        INT                        ,
EXECUTION_NO                      INT                        ,
STATUS_ID                         INT                        ,
ABORT_RECEPTED_FLAG               INT                        ,
TIME_START                        DATETIME(6)                ,
TIME_END                          DATETIME(6)                ,
RELEASED_FLAG                     INT                        ,

EXE_SKIP_FLAG                     INT                        ,
OVRD_OPERATION_NO_UAPK            INT                        ,
OVRD_I_OPERATION_NAME             VARCHAR (256)              ,
OVRD_I_OPERATION_NO_IDBH          INT                        ,

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ

PRIMARY KEY (NODE_INSTANCE_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE C_NODE_INSTANCE_MNG_JNL
(
JOURNAL_SEQ_NO                    INT                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                , -- 履歴用変更種別
NODE_INSTANCE_NO                  INT                        ,

I_NODE_CLASS_NO                   INT                        ,
I_NODE_TYPE_ID                    INT                        ,
I_ORCHESTRATOR_ID                 INT                        ,
I_PATTERN_ID                      INT                        ,
I_PATTERN_NAME                    VARCHAR (256)              ,
I_TIME_LIMIT                      INT                        ,
I_ANS_HOST_DESIGNATE_TYPE_ID      INT                        ,
I_ANS_WINRM_ID                    INT                        ,
I_DSC_RETRY_TIMEOUT               INT                        ,
I_MOVEMENT_SEQ                    INT                        ,
I_NEXT_PENDING_FLAG               INT                        ,
I_DESCRIPTION                     VARCHAR (4000)             ,
CONDUCTOR_INSTANCE_NO             INT                        ,
CONDUCTOR_INSTANCE_CALL_NO        INT                        ,
EXECUTION_NO                      INT                        ,
STATUS_ID                         INT                        ,
ABORT_RECEPTED_FLAG               INT                        ,
TIME_START                        DATETIME(6)                ,
TIME_END                          DATETIME(6)                ,
RELEASED_FLAG                     INT                        ,

EXE_SKIP_FLAG                     INT                        ,
OVRD_OPERATION_NO_UAPK            INT                        ,
OVRD_I_OPERATION_NAME             VARCHAR (256)              ,
OVRD_I_OPERATION_NO_IDBH          INT                        ,

DISP_SEQ                          INT                        , -- 表示順序
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- Nodeインスタンス----


-- ----NODEタイプマスタ
CREATE TABLE B_NODE_TYPE_MASTER
(
NODE_TYPE_ID                      INT                               ,

NODE_TYPE_NAME                    VARCHAR (64)                      ,

DISP_SEQ                          INT                               , -- 表示順序, 
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ

PRIMARY KEY (NODE_TYPE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_NODE_TYPE_MASTER_JNL
(
JOURNAL_SEQ_NO                    INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                       , -- 履歴用変更種別

NODE_TYPE_ID                      INT                               ,

NODE_TYPE_NAME                    VARCHAR (64)                      ,

DISP_SEQ                          INT                               , -- 表示順序, 
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- NODEタイプマスタ----

-- ----TERMINALタイプマスタ
CREATE TABLE B_TERMINAL_TYPE_MASTER
(
TERMINAL_TYPE_ID                  INT                               , 

TERMINAL_TYPE_NAME                VARCHAR (64)                      ,

DISP_SEQ                          INT                               , -- 表示順序, 
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ

PRIMARY KEY (TERMINAL_TYPE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_TERMINAL_TYPE_MASTER_JNL
(
JOURNAL_SEQ_NO                    INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                       , -- 履歴用変更種別

TERMINAL_TYPE_ID                  INT                               ,

TERMINAL_TYPE_NAME                VARCHAR (64)                      ,

DISP_SEQ                          INT                               , -- 表示順序, 
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
--TERMINALタイプマスタ ----


-- -------------------------------------------------------
-- --定期作業実行用(Conductor)
-- -------------------------------------------------------
-- ----定期作業実行用(Conductor)
CREATE TABLE C_REGULARLY2_LIST
(
REGULARLY_ID                      INT                          ,
CONDUCTOR_CLASS_NO                INT                          ,
OPERATION_NO_IDBH                 INT                          ,
CONDUCTOR_INSTANCE_NO             INT                          ,
STATUS_ID                         INT                          ,
NEXT_EXECUTION_DATE               DATETIME(6)                  ,
START_DATE                        DATETIME(6)                  ,
END_DATE                          DATETIME(6)                  ,
EXECUTION_STOP_START_DATE         DATETIME(6)                  ,
EXECUTION_STOP_END_DATE           DATETIME(6)                  ,
EXECUTION_INTERVAL                INT                          ,
REGULARLY_PERIOD_ID               INT                          ,
PATTERN_TIME                      VARCHAR (5)                  ,
PATTERN_DAY                       INT                          ,
PATTERN_DAY_OF_WEEK               INT                          ,
PATTERN_WEEK_NUMBER               INT                          ,
DISP_SEQ                          INT                          ,
NOTE                              VARCHAR (4000)               ,
DISUSE_FLAG                       VARCHAR (1)                  ,
LAST_UPDATE_TIMESTAMP             DATETIME(6)                  ,
LAST_UPDATE_USER                  INT                          ,

PRIMARY KEY (REGULARLY_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE C_REGULARLY2_LIST_JNL
(
JOURNAL_SEQ_NO                    INT                          ,
JOURNAL_REG_DATETIME              DATETIME(6)                  ,
JOURNAL_ACTION_CLASS              VARCHAR (8)                  ,

REGULARLY_ID                      INT                          ,
CONDUCTOR_CLASS_NO                INT                          ,
OPERATION_NO_IDBH                 INT                          ,
CONDUCTOR_INSTANCE_NO             INT                          ,
STATUS_ID                         INT                          ,
NEXT_EXECUTION_DATE               DATETIME(6)                  ,
START_DATE                        DATETIME(6)                  ,
END_DATE                          DATETIME(6)                  ,
EXECUTION_STOP_START_DATE         DATETIME(6)                  ,
EXECUTION_STOP_END_DATE           DATETIME(6)                  ,
EXECUTION_INTERVAL                INT                          ,
REGULARLY_PERIOD_ID               INT                          ,
PATTERN_TIME                      VARCHAR (5)                  ,
PATTERN_DAY                       INT                          ,
PATTERN_DAY_OF_WEEK               INT                          ,
PATTERN_WEEK_NUMBER               INT                          ,
DISP_SEQ                          INT                          ,
NOTE                              VARCHAR (4000)               ,
DISUSE_FLAG                       VARCHAR (1)                  ,
LAST_UPDATE_TIMESTAMP             DATETIME(6)                  ,
LAST_UPDATE_USER                  INT                          ,

PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 定期作業実行用(Conductor)----


CREATE OR REPLACE VIEW D_ACCOUNT_LIST AS 
SELECT TAB_A.USER_ID              ,
       TAB_A.USERNAME             ,
       TAB_A.PASSWORD             ,
       TAB_A.USERNAME_JP          ,
       TAB_A.MAIL_ADDRESS         ,
       TAB_A.PW_LAST_UPDATE_TIME  ,
       TAB_B.LOCK_ID              ,
       TAB_B.MISS_INPUT_COUNTER   ,
       TAB_B.LOCKED_TIMESTAMP     ,
       CONCAT(TAB_A.USER_ID,':',TAB_A.USERNAME) USER_PULLDOWN,
       TAB_C.USER_JUDGE_ID        ,
       TAB_C.AD_USER_SID          ,
       TAB_A.AUTH_TYPE            ,
       TAB_A.PROVIDER_ID          ,
       TAB_A.PROVIDER_USER_ID     ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM   A_ACCOUNT_LIST TAB_A
LEFT JOIN A_ACCOUNT_LOCK TAB_B ON (TAB_A.USER_ID = TAB_B.USER_ID)
LEFT JOIN A_AD_USER_JUDGEMENT TAB_C ON (TAB_A.USER_ID = TAB_C.ITA_USER_ID)
WHERE  TAB_A.USER_ID > 0;

CREATE OR REPLACE VIEW D_ACCOUNT_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.USER_ID              ,
       TAB_A.USERNAME             ,
       TAB_A.PASSWORD             ,
       TAB_A.USERNAME_JP          ,
       TAB_A.MAIL_ADDRESS         ,
       TAB_A.PW_LAST_UPDATE_TIME  ,
       TAB_B.LOCK_ID              ,
       TAB_B.MISS_INPUT_COUNTER   ,
       TAB_B.LOCKED_TIMESTAMP     ,
       CONCAT(TAB_A.USER_ID,':',TAB_A.USERNAME) USER_PULLDOWN,
       TAB_C.USER_JUDGE_ID        ,
       TAB_C.AD_USER_SID          ,
       TAB_A.AUTH_TYPE            ,
       TAB_A.PROVIDER_ID          ,
       TAB_A.PROVIDER_USER_ID     ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM   A_ACCOUNT_LIST_JNL TAB_A
LEFT JOIN A_ACCOUNT_LOCK TAB_B ON (TAB_A.USER_ID = TAB_B.USER_ID)
LEFT JOIN A_AD_USER_JUDGEMENT TAB_C ON (TAB_A.USER_ID = TAB_C.ITA_USER_ID)
WHERE  TAB_A.USER_ID > 0;


CREATE VIEW D_PROVIDER_LIST AS
SELECT TAB_A.PROVIDER_ID,
       TAB_A.PROVIDER_NAME,
       TAB_A.LOGO,
       TAB_A.AUTH_TYPE,
       TAB_A.VISIBLE_FLAG,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_LIST TAB_A;

CREATE VIEW D_PROVIDER_LIST_JNL AS
SELECT TAB_A.JOURNAL_SEQ_NO,
       TAB_A.JOURNAL_REG_DATETIME,
       TAB_A.JOURNAL_ACTION_CLASS,
       TAB_A.PROVIDER_ID,
       TAB_A.PROVIDER_NAME,
       TAB_A.LOGO,
       TAB_A.AUTH_TYPE,
       TAB_A.VISIBLE_FLAG,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_LIST_JNL TAB_A;

CREATE VIEW D_PROVIDER_ATTRIBUTE_LIST AS
SELECT TAB_A.PROVIDER_ATTRIBUTE_ID,
       TAB_A.PROVIDER_ID,
       TAB_A.NAME,
       TAB_A.VALUE,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_ATTRIBUTE_LIST TAB_A;

CREATE VIEW D_PROVIDER_ATTRIBUTE_LIST_JNL AS
SELECT TAB_A.JOURNAL_SEQ_NO,
       TAB_A.JOURNAL_REG_DATETIME,
       TAB_A.JOURNAL_ACTION_CLASS,
       TAB_A.PROVIDER_ATTRIBUTE_ID,
       TAB_A.PROVIDER_ID,
       TAB_A.NAME,
       TAB_A.VALUE,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_ATTRIBUTE_LIST_JNL TAB_A;


CREATE OR REPLACE VIEW E_STM_LIST 
AS 

SELECT TAB_A.SYSTEM_ID                        SYSTEM_ID                     ,
       TAB_A.HARDAWRE_TYPE_ID                 HARDAWRE_TYPE_ID              ,
       TAB_A.HOSTNAME                         HOSTNAME                      ,
       CONCAT(TAB_A.SYSTEM_ID,':',TAB_A.HOSTNAME) HOST_PULLDOWN,
       TAB_A.IP_ADDRESS                       IP_ADDRESS                    ,
       TAB_A.PROTOCOL_ID                      PROTOCOL_ID                   ,
       TAB_A.LOGIN_USER                       LOGIN_USER                    ,
       TAB_A.LOGIN_PW_HOLD_FLAG               LOGIN_PW_HOLD_FLAG            ,
       TAB_A.LOGIN_PW                         LOGIN_PW                      ,
       TAB_A.ETH_WOL_MAC_ADDRESS              ETH_WOL_MAC_ADDRESS           ,
       TAB_A.ETH_WOL_NET_DEVICE               ETH_WOL_NET_DEVICE            ,
       TAB_A.LOGIN_AUTH_TYPE                  LOGIN_AUTH_TYPE               ,
       TAB_A.WINRM_PORT                       WINRM_PORT                    ,
       TAB_A.OS_TYPE_ID                       OS_TYPE_ID                    ,
       TAB_A.HOSTNAME                         SYSTEM_NAME                   ,
       TAB_A.COBBLER_PROFILE_ID               COBBLER_PROFILE_ID            ,
       TAB_A.INTERFACE_TYPE                   INTERFACE_TYPE                ,
       TAB_A.MAC_ADDRESS                      MAC_ADDRESS                   ,
       TAB_A.NETMASK                          NETMASK                       ,
       TAB_A.GATEWAY                          GATEWAY                       ,
       TAB_A.STATIC                           STATIC                        ,

       TAB_A.CONN_SSH_KEY_FILE                CONN_SSH_KEY_FILE             ,

       TAB_A.DISP_SEQ                         DISP_SEQ                      ,
       TAB_A.NOTE                             NOTE                          ,
       TAB_A.DISUSE_FLAG                      DISUSE_FLAG                   ,
       TAB_A.LAST_UPDATE_TIMESTAMP            LAST_UPDATE_TIMESTAMP         ,
       TAB_A.LAST_UPDATE_USER                 LAST_UPDATE_USER

FROM C_STM_LIST TAB_A;

CREATE OR REPLACE VIEW E_STM_LIST_JNL 
AS 

SELECT TAB_A.JOURNAL_SEQ_NO                   JOURNAL_SEQ_NO                ,
       TAB_A.JOURNAL_REG_DATETIME             JOURNAL_REG_DATETIME          ,
       TAB_A.JOURNAL_ACTION_CLASS             JOURNAL_ACTION_CLASS          ,

       TAB_A.SYSTEM_ID                        SYSTEM_ID                     ,
       TAB_A.HARDAWRE_TYPE_ID                 HARDAWRE_TYPE_ID              ,
       TAB_A.HOSTNAME                         HOSTNAME                      ,
       CONCAT(TAB_A.SYSTEM_ID,':',TAB_A.HOSTNAME) HOST_PULLDOWN,
       TAB_A.IP_ADDRESS                       IP_ADDRESS                    ,
       TAB_A.PROTOCOL_ID                      PROTOCOL_ID                   ,
       TAB_A.LOGIN_USER                       LOGIN_USER                    ,
       TAB_A.LOGIN_PW_HOLD_FLAG               LOGIN_PW_HOLD_FLAG            ,
       TAB_A.LOGIN_PW                         LOGIN_PW                      ,
       TAB_A.ETH_WOL_MAC_ADDRESS              ETH_WOL_MAC_ADDRESS           ,
       TAB_A.ETH_WOL_NET_DEVICE               ETH_WOL_NET_DEVICE            ,
       TAB_A.LOGIN_AUTH_TYPE                  LOGIN_AUTH_TYPE               ,
       TAB_A.WINRM_PORT                       WINRM_PORT                    ,
       TAB_A.OS_TYPE_ID                       OS_TYPE_ID                    ,
       TAB_A.HOSTNAME                         SYSTEM_NAME                   ,
       TAB_A.COBBLER_PROFILE_ID               COBBLER_PROFILE_ID            ,
       TAB_A.INTERFACE_TYPE                   INTERFACE_TYPE                ,
       TAB_A.MAC_ADDRESS                      MAC_ADDRESS                   ,
       TAB_A.NETMASK                          NETMASK                       ,
       TAB_A.GATEWAY                          GATEWAY                       ,
       TAB_A.STATIC                           STATIC                        ,

       TAB_A.CONN_SSH_KEY_FILE                CONN_SSH_KEY_FILE             ,

       TAB_A.DISP_SEQ                         DISP_SEQ                      ,
       TAB_A.NOTE                             NOTE                          ,
       TAB_A.DISUSE_FLAG                      DISUSE_FLAG                   ,
       TAB_A.LAST_UPDATE_TIMESTAMP            LAST_UPDATE_TIMESTAMP         ,
       TAB_A.LAST_UPDATE_USER                 LAST_UPDATE_USER

FROM C_STM_LIST_JNL TAB_A;


ALTER TABLE B_CMDB_MENU_COLUMN ADD COLUMN COL_CLASS VARCHAR(64) AFTER COL_NAME;
ALTER TABLE B_CMDB_MENU_COLUMN MODIFY COLUMN COL_TITLE VARCHAR(4096) AFTER COL_NAME;

ALTER TABLE B_CMDB_MENU_COLUMN_JNL ADD COLUMN COL_CLASS VARCHAR(64) AFTER COL_NAME;
ALTER TABLE B_CMDB_MENU_COLUMN_JNL MODIFY COLUMN COL_TITLE VARCHAR(4096) AFTER COL_NAME;


UPDATE A_SEQUENCE SET VALUE=25 WHERE NAME IN ('B_CMDB_HIDE_MENU_GRP_RIC', 'B_CMDB_HIDE_MENU_GRP_JSQ');

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_PROVIDER_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_PROVIDER_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_PROVIDER_ATTRIBUTE_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_PROVIDER_ATTRIBUTE_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_PROVIDER_AUTH_TYPE_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_PROVIDER_AUTH_TYPE_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_VISIBLE_FLAG_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_VISIBLE_FLAG_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_PROVIDER_ATTRIBUTE_NAME_LIST',11);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_PROVIDER_ATTRIBUTE_NAME_LIST',11);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_CONDUCTOR_CLASS_MNG_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_CONDUCTOR_CLASS_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_NODE_CLASS_MNG_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_NODE_CLASS_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_NODE_TERMINALS_CLASS_MNG_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_NODE_TERMINALS_CLASS_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_CONDUCTOR_INSTANCE_MNG_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_CONDUCTOR_INSTANCE_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_NODE_INSTANCE_MNG_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_NODE_INSTANCE_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_REGULARLY2_LIST_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_REGULARLY2_LIST_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_CONDUCTOR_IF_INFO_JSQ',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_CONDUCTOR_IF_INFO_RIC',2);


INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090001,'Conductor','conductor.png',27,'Conductor','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100090001,'Conductor','conductor.png',27,'Conductor','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100100001,'Symphony','symphony.png',26,'Symphony','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100100001,'Symphony','symphony.png',26,'Symphony','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


UPDATE A_MENU_LIST SET MENU_GROUP_ID=2100100001 WHERE MENU_ID=2100000306;
UPDATE A_MENU_LIST SET MENU_GROUP_ID=2100100001 WHERE MENU_ID=2100000307;
UPDATE A_MENU_LIST SET MENU_GROUP_ID=2100100001 WHERE MENU_ID=2100000308;
UPDATE A_MENU_LIST SET MENU_GROUP_ID=2100100001 WHERE MENU_ID=2100000309;
UPDATE A_MENU_LIST SET MENU_GROUP_ID=2100100001 WHERE MENU_ID=2100000310;
UPDATE A_MENU_LIST SET MENU_GROUP_ID=2100100001 WHERE MENU_ID=2100000311;
UPDATE A_MENU_LIST SET MENU_GROUP_ID=2100100001 WHERE MENU_ID=2100000312;
UPDATE A_MENU_LIST SET MENU_GROUP_ID=2100100001 WHERE MENU_ID=2100000313;
UPDATE A_MENU_LIST SET MENU_GROUP_ID=2100100001,MENU_NAME='Symphony定期作業実行' WHERE MENU_ID=2100000314;

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000231,2100000002,'SSO基本情報管理',NULL,NULL,NULL,1,0,1,1,31,'single sign on basic preference','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-231,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000231,2100000002,'SSO基本情報管理',NULL,NULL,NULL,1,0,1,1,31,'single sign on basic preference','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000232,2100000002,'SSO属性情報管理',NULL,NULL,NULL,1,0,1,1,32,'single sign on attribute preference','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-232,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000232,2100000002,'SSO属性情報管理',NULL,NULL,NULL,1,0,1,1,32,'single sign on attribute preference','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180001,2100090001,'Conductorインターフェース情報',NULL,NULL,NULL,1,0,1,1,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-315,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180001,2100090001,'Conductorインターフェース情報',NULL,NULL,NULL,1,0,1,1,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180002,2100090001,'Conductorクラス一覧',NULL,NULL,NULL,1,0,1,1,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-316,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180002,2100090001,'Conductorクラス一覧',NULL,NULL,NULL,1,0,1,1,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180003,2100090001,'Conductorクラス編集',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-317,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180003,2100090001,'Conductorクラス編集',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180004,2100090001,'Conductor作業実行',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-318,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180004,2100090001,'Conductor作業実行',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180005,2100090001,'Conductor作業確認',NULL,NULL,NULL,1,0,2,2,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-319,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180005,2100090001,'Conductor作業確認',NULL,NULL,NULL,1,0,2,2,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180006,2100090001,'Conductor作業一覧',NULL,NULL,NULL,1,0,1,2,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-320,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180006,2100090001,'Conductor作業一覧',NULL,NULL,NULL,1,0,1,2,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180007,2100090001,'Conductor紐付Node一覧',NULL,NULL,NULL,1,0,1,1,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-321,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180007,2100090001,'Conductor紐付Node一覧',NULL,NULL,NULL,1,0,1,1,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180008,2100090001,'Node紐付Terminal一覧',NULL,NULL,NULL,1,0,1,1,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-322,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180008,2100090001,'Node紐付Terminal一覧',NULL,NULL,NULL,1,0,1,1,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180009,2100090001,'Conductorインスタンス一覧',NULL,NULL,NULL,1,0,1,1,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-323,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180009,2100090001,'Conductorインスタンス一覧',NULL,NULL,NULL,1,0,1,1,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180010,2100090001,'Nodeインスタンス一覧',NULL,NULL,NULL,1,0,1,1,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-324,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180010,2100090001,'Nodeインスタンス一覧',NULL,NULL,NULL,1,0,1,1,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180011,2100090001,'Conductor定期作業実行',NULL,NULL,NULL,1,0,1,2,110,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-325,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180011,2100090001,'Conductor定期作業実行',NULL,NULL,NULL,1,0,1,2,110,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


INSERT INTO A_ROLE_LIST (ROLE_ID,ROLE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000001,'SSOデフォルトロール','SSOデフォルトロール','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROLE_ID,ROLE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000001,'SSOデフォルトロール','SSOデフォルトロール','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


UPDATE A_ACCOUNT_LIST SET AUTH_TYPE='local' WHERE USER_ID > 0;

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100030,'a07','5ebbc37e034d6874a2af59eb04beaa52','SingleSignOnユーザ/ロール管理プロシージャ','sample@xxx.bbb.ccc',NULL,'SingleSignOnユーザ/ロール管理プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100030,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100030,'a07','5ebbc37e034d6874a2af59eb04beaa52','SingleSignOnユーザ/ロール管理プロシージャ','sample@xxx.bbb.ccc',NULL,'SingleSignOnユーザ/ロール管理プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-6,'c06','5ebbc37e034d6874a2af59eb04beaa52','コンダクター管理プロシージャ','sample@xxx.bbb.ccc',NULL,'コンダクター管理プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-6,'c06','5ebbc37e034d6874a2af59eb04beaa52','コンダクター管理プロシージャ','sample@xxx.bbb.ccc',NULL,'コンダクター管理プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180001,1,2100180001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-315,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180001,1,2100180001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180002,1,2100180002,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-316,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180002,1,2100180002,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180003,1,2100180003,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-317,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180003,1,2100180003,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180004,1,2100180004,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-318,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180004,1,2100180004,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180005,1,2100180005,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-319,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180005,1,2100180005,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180006,1,2100180006,2,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-320,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180006,1,2100180006,2,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180007,1,2100180007,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-321,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180007,1,2100180007,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180008,1,2100180008,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-322,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180008,1,2100180008,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180009,1,2100180009,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-323,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180009,1,2100180009,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180010,1,2100180010,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-324,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180010,1,2100180010,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100180011,1,2100180011,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-325,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100180011,1,2100180011,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000231,1,2100000231,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-231,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000231,1,2100000231,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000232,1,2100000232,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-232,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000232,1,2100000232,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


DELETE FROM A_RELATE_STATUS WHERE RELATE_STATUS_ID IN (7);
DELETE FROM A_RELATE_STATUS_JNL WHERE RELATE_STATUS_ID IN (7);

INSERT INTO A_RELATE_STATUS (RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'2100080011','D_TERRAFORM_INS_STATUS',5,6,7,8,10,9,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_RELATE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',9,'2100080011','D_TERRAFORM_INS_STATUS',5,6,7,8,10,9,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(23,'2100080001',23,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(23,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',23,'2100080001',23,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(24,'2100090001',24,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(24,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',24,'2100090001',24,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(25,'2100100001',25,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(25,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',25,'2100100001',25,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


INSERT INTO A_PROVIDER_AUTH_TYPE_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('1','oauth2',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_AUTH_TYPE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','1','oauth2',NULL,'0',1);

INSERT INTO A_VISIBLE_FLAG_LIST (ID,FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('0','非表示',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_VISIBLE_FLAG_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(0,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','0','非表示',NULL,'0',1);
INSERT INTO A_VISIBLE_FLAG_LIST (ID,FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('1','表示',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_VISIBLE_FLAG_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','1','表示',NULL,'0',1);

INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('-1','debug',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(-1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','-1','debug',NULL,'0',1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('0','proxy',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(0,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','0','proxy',NULL,'0',1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('1','clientId',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','1','clientId',NULL,'0',1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('2','clientSecret',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','2','clientSecret',NULL,'0',1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('3','authorizationUri',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','3','authorizationUri',NULL,'0',1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('4','accessTokenUri',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','4','accessTokenUri',NULL,'0',1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('5','resourceOwnerUri',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','5','resourceOwnerUri',NULL,'0',1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('6','scope',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','6','scope',NULL,'0',1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('7','id',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(7,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','7','id',NULL,'0',1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('8','name',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(8,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','8','name',NULL,'0',1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('9','email',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(9,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','9','email',NULL,'0',1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST (ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES('10','imageUrl',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_USER) VALUES(10,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT','10','imageUrl',NULL,'0',1);


UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100070004 WHERE HIDE_ID=31;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100070005 WHERE HIDE_ID=32;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100070006 WHERE HIDE_ID=33;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100070007 WHERE HIDE_ID=34;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100150004 WHERE HIDE_ID=35;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100150005 WHERE HIDE_ID=36;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100150102 WHERE HIDE_ID=37;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100150103 WHERE HIDE_ID=38;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100150104 WHERE HIDE_ID=39;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100150105 WHERE HIDE_ID=40;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100150106 WHERE HIDE_ID=41;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100160003 WHERE HIDE_ID=42;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100160004 WHERE HIDE_ID=43;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100170005 WHERE HIDE_ID=44;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100000299 WHERE HIDE_ID=45;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100000401 WHERE HIDE_ID=46;
INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('47','2100000402');
INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('48','2100000403');
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100080009 WHERE HIDE_ID=49;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100080010 WHERE HIDE_ID=50;
UPDATE B_DP_HIDE_MENU_LIST SET MENU_ID=2100080011 WHERE HIDE_ID=51;

DELETE FROM B_DP_HIDE_MENU_LIST WHERE HIDE_ID IN (52,53);

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('55','2100180004');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('56','2100180005');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('57','2100180006');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('58','2100180009');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('59','2100180010');


DELETE FROM B_SVC_TO_STOP_IMP_SYM_OPE WHERE ROW_ID IN ('2100000007', '2100000008');


INSERT INTO B_SYM_EXE_STATUS (SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'想定外エラー(ループ)',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_SYM_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',10,'想定外エラー(ループ)',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);



INSERT INTO B_NODE_TYPE_MASTER (NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'start','1',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'start','1',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER (NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'end','2',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'end','2',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER (NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'movement','3',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'movement','3',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER (NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'call','4',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'call','4',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER (NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'parallel','5',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'parallel','5',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER (NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'conditional','6',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',6,'conditional','6',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER (NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'merge','7',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',7,'merge','7',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER (NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'stop','8',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',8,'stop','8',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER (NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'blank','9',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_NODE_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,NODE_TYPE_ID,NODE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',9,'blank','9',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_TERMINAL_TYPE_MASTER (TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'start','1',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'start','1',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER (TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'end','2',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'end','2',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER (TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'movement','3',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'movement','3',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER (TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'call','4',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'call','4',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER (TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'parallel','5',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'parallel','5',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER (TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'conditional','6',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',6,'conditional','6',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER (TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'merge','7',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',7,'merge','7',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER (TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'stop','8',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',8,'stop','8',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER (TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'blank','9',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERMINAL_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TERMINAL_TYPE_ID,TERMINAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',9,'blank','9',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO C_CONDUCTOR_IF_INFO (CONDUCTOR_IF_INFO_ID,CONDUCTOR_STORAGE_PATH_ITA,CONDUCTOR_REFRESH_INTERVAL,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/conductor',3000,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO C_CONDUCTOR_IF_INFO_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CONDUCTOR_IF_INFO_ID,CONDUCTOR_STORAGE_PATH_ITA,CONDUCTOR_REFRESH_INTERVAL,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/conductor',3000,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

