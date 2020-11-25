ALTER TABLE A_SEQUENCE ADD COLUMN MENU_ID INT AFTER VALUE;
ALTER TABLE A_SEQUENCE ADD COLUMN DISP_SEQ INT AFTER MENU_ID;
ALTER TABLE A_SEQUENCE ADD COLUMN NOTE VARCHAR (4000) AFTER DISP_SEQ;
ALTER TABLE A_SEQUENCE ADD COLUMN LAST_UPDATE_TIMESTAMP DATETIME(6) AFTER NOTE;

ALTER TABLE A_ACCOUNT_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER PROVIDER_USER_ID;

ALTER TABLE A_SYSTEM_CONFIG_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER VALUE;

ALTER TABLE A_PERMISSIONS_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER IP_INFO;

ALTER TABLE A_ROLE_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER ROLE_NAME;

ALTER TABLE A_MENU_GROUP_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE A_MENU_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE A_ROLE_ACCOUNT_LINK_LIST ADD COLUMN DEF_ACCESS_AUTH_FLAG VARCHAR(1) AFTER USER_ID;
ALTER TABLE A_ROLE_ACCOUNT_LINK_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER DEF_ACCESS_AUTH_FLAG;

ALTER TABLE A_ROLE_MENU_LINK_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER PRIVILEGE;

ALTER TABLE A_LOGIN_NECESSITY_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER NAME;

ALTER TABLE A_SERVICE_STATUS_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER NAME;

ALTER TABLE A_PRIVILEGE_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER NAME;

ALTER TABLE A_PROVIDER_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER VISIBLE_FLAG;

ALTER TABLE A_PROVIDER_ATTRIBUTE_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER VALUE;

ALTER TABLE A_PROVIDER_AUTH_TYPE_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER NAME;

ALTER TABLE A_VISIBLE_FLAG_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER FLAG;

ALTER TABLE A_PROVIDER_ATTRIBUTE_NAME_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER NAME;

CREATE TABLE A_WIDGET_LIST (
WIDGET_ID                      INT                          , -- ウィジェットID
WIDGET_DATA                    TEXT                         , -- ウィジェット本体(JSON)
USER_ID                        INT                          , -- ユーザID
LAST_UPDATE_TIMESTAMP          DATETIME(6)                  , -- 最終更新日時
PRIMARY KEY (WIDGET_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE IF NOT EXISTS F_PARAM_TARGET
(
TARGET_ID                           INT                             , -- 識別シーケンス項番
DISP_SEQ                            INT                             , 
TARGET_NAME                         VARCHAR (64)                    ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (TARGET_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

ALTER TABLE F_PARAM_TARGET ADD COLUMN DISP_SEQ INT AFTER TARGET_ID;
ALTER TABLE F_PARAM_TARGET ADD COLUMN ACCESS_AUTH TEXT AFTER TARGET_NAME;

CREATE TABLE IF NOT EXISTS F_PARAM_TARGET_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

TARGET_ID                           INT                             , -- 識別シーケンス項番
DISP_SEQ                            INT                             , 
TARGET_NAME                         VARCHAR (64)                    ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

ALTER TABLE F_PARAM_TARGET_JNL ADD COLUMN DISP_SEQ INT AFTER TARGET_ID;
ALTER TABLE F_PARAM_TARGET_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER TARGET_NAME;

ALTER TABLE A_ACCOUNT_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER PROVIDER_USER_ID;

ALTER TABLE A_SYSTEM_CONFIG_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER VALUE;

ALTER TABLE A_PERMISSIONS_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER IP_INFO;

ALTER TABLE A_ROLE_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER ROLE_NAME;

ALTER TABLE A_MENU_GROUP_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE A_MENU_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE A_ROLE_ACCOUNT_LINK_LIST_JNL ADD COLUMN DEF_ACCESS_AUTH_FLAG VARCHAR(1) AFTER USER_ID;
ALTER TABLE A_ROLE_ACCOUNT_LINK_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DEF_ACCESS_AUTH_FLAG;

ALTER TABLE A_ROLE_MENU_LINK_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER PRIVILEGE;

ALTER TABLE A_LOGIN_NECESSITY_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER NAME;

ALTER TABLE A_SERVICE_STATUS_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER NAME;

ALTER TABLE A_PRIVILEGE_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER NAME;

ALTER TABLE A_TODO_MASTER ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE A_TODO_MASTER_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE A_PROVIDER_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER VISIBLE_FLAG;

ALTER TABLE A_PROVIDER_ATTRIBUTE_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER VALUE;

ALTER TABLE A_PROVIDER_AUTH_TYPE_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER NAME;

ALTER TABLE A_VISIBLE_FLAG_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER ID;

ALTER TABLE A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER NAME;

ALTER TABLE B_ITA_EXT_STM_MASTER ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_ITA_EXT_STM_MASTER_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_HARDAWRE_TYPE ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_HARDAWRE_TYPE_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_PROTOCOL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_PROTOCOL_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_HOST_DESIGNATE_TYPE_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_HOST_DESIGNATE_TYPE_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_STM_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_STM_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_PATTERN_PER_ORCH ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_PATTERN_PER_ORCH_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_OPERATION_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_OPERATION_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_SYMPHONY_IF_INFO ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_SYMPHONY_IF_INFO_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_SYMPHONY_CLASS_MNG ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_SYMPHONY_CLASS_MNG_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_SYMPHONY_INSTANCE_MNG ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_SYMPHONY_INSTANCE_MNG_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_MOVEMENT_CLASS_MNG ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_MOVEMENT_CLASS_MNG_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_MOVEMENT_INSTANCE_MNG ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_MOVEMENT_INSTANCE_MNG_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_SYM_EXE_STATUS ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_SYM_EXE_STATUS_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_SYM_ABORT_FLAG ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_SYM_ABORT_FLAG_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_LOGIN_AUTH_TYPE ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_LOGIN_AUTH_TYPE_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE D_FLAG_LIST_01 ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE D_FLAG_LIST_01_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_DP_STATUS ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_DP_STATUS_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_DP_STATUS_MASTER ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_DP_STATUS_MASTER_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_DP_TYPE ADD COLUMN ACCESS_AUTH TEXT AFTER DP_TYPE;

ALTER TABLE B_DP_TYPE_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DP_TYPE;

ALTER TABLE B_DP_IMPORT_TYPE ADD COLUMN ACCESS_AUTH TEXT AFTER IMPORT_TYPE;

ALTER TABLE B_DP_IMPORT_TYPE_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER IMPORT_TYPE;

ALTER TABLE A_AD_GROUP_JUDGEMENT ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE A_AD_GROUP_JUDGEMENT_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE A_AD_USER_JUDGEMENT ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE A_AD_USER_JUDGEMENT_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_DP_SYM_OPE_STATUS ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_DP_SYM_OPE_STATUS_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_REGULARLY_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_REGULARLY_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_REGULARLY_STATUS ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_REGULARLY_STATUS_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_REGULARLY_PERIOD ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_REGULARLY_PERIOD_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_DAY_OF_WEEK ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_DAY_OF_WEEK_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_WEEK_NUMBER ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_WEEK_NUMBER_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_CONDUCTOR_IF_INFO ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_CONDUCTOR_IF_INFO_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

-- ----Conductorクラス(編集用)
CREATE TABLE C_CONDUCTOR_EDIT_CLASS_MNG
(
CONDUCTOR_CLASS_NO                INT                        ,

CONDUCTOR_NAME                    VARCHAR (256)              ,
DESCRIPTION                       VARCHAR (4000)             ,

DISP_SEQ                          INT                        , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ

PRIMARY KEY (CONDUCTOR_CLASS_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE C_CONDUCTOR_EDIT_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    INT                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                , -- 履歴用変更種別

CONDUCTOR_CLASS_NO                INT                        ,

CONDUCTOR_NAME                    VARCHAR (256)              ,
DESCRIPTION                       VARCHAR (4000)             ,

DISP_SEQ                          INT                        , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- Conductorクラス(編集用)----

-- ----Nodeクラス(編集用)
CREATE TABLE C_NODE_EDIT_CLASS_MNG
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
ACCESS_AUTH                       TEXT                       ,
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ

PRIMARY KEY (NODE_CLASS_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE C_NODE_EDIT_CLASS_MNG_JNL
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
ACCESS_AUTH                       TEXT                       ,
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- Nodeクラス(編集用)----

-- ----Terminalクラス(編集用)
CREATE TABLE C_NODE_TERMINALS_EDIT_CLASS_MNG
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
ACCESS_AUTH                       TEXT                       ,
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ

PRIMARY KEY (TERMINAL_CLASS_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE C_NODE_TERMINALS_EDIT_CLASS_MNG_JNL
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
ACCESS_AUTH                       TEXT                       ,
NOTE                              VARCHAR (4000)             , -- 備考
DISUSE_FLAG                       VARCHAR (1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                , -- 最終更新日時
LAST_UPDATE_USER                  INT                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- Terminalクラス(編集用)----

ALTER TABLE C_CONDUCTOR_CLASS_MNG ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_CONDUCTOR_CLASS_MNG_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_CONDUCTOR_INSTANCE_MNG ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_CONDUCTOR_INSTANCE_MNG_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_NODE_INSTANCE_MNG ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_NODE_INSTANCE_MNG_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

-- ----SensitiveFマスタ
CREATE TABLE B_SENSITIVE_FLAG
(
VARS_SENSITIVE                    INT                              ,
VARS_SENSITIVE_SELECT             VARCHAR (16)                     ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (VARS_SENSITIVE)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_SENSITIVE_FLAG_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
VARS_SENSITIVE                    INT                              ,
VARS_SENSITIVE_SELECT             VARCHAR (16)                     ,
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- SensitiveFマスタ----

ALTER TABLE C_REGULARLY2_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_REGULARLY2_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_COBBLER_PROFILE ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE C_COBBLER_PROFILE_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

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
       TAB_A.ACCESS_AUTH          ,
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
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM   A_ACCOUNT_LIST_JNL TAB_A
LEFT JOIN A_ACCOUNT_LOCK TAB_B ON (TAB_A.USER_ID = TAB_B.USER_ID)
LEFT JOIN A_AD_USER_JUDGEMENT TAB_C ON (TAB_A.USER_ID = TAB_C.ITA_USER_ID)
WHERE  TAB_A.USER_ID > 0;

CREATE OR REPLACE VIEW D_MENU_GROUP_LIST AS 
SELECT TAB_A.MENU_GROUP_ID        ,
       TAB_A.MENU_GROUP_NAME      ,
       TAB_A.MENU_GROUP_ID          MENU_GROUP_ID_CLONE,
       CONCAT(TAB_A.MENU_GROUP_ID,':',TAB_A.MENU_GROUP_NAME) MENU_GROUP_PULLDOWN,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
FROM   A_MENU_GROUP_LIST TAB_A;

CREATE OR REPLACE VIEW D_MENU_GROUP_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.MENU_GROUP_ID        ,
       TAB_A.MENU_GROUP_NAME      ,
       TAB_A.MENU_GROUP_ID          MENU_GROUP_ID_CLONE,
       CONCAT(TAB_A.MENU_GROUP_ID,':',TAB_A.MENU_GROUP_NAME) MENU_GROUP_PULLDOWN,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
FROM   A_MENU_GROUP_LIST_JNL TAB_A;

CREATE OR REPLACE VIEW D_ROLE_LIST AS 
SELECT TAB_A.ROLE_ID              ,
       TAB_A.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       CONCAT(TAB_A.ROLE_ID,':',TAB_A.ROLE_NAME) ROLE_PULLDOWN,
       TAB_B.GROUP_JUDGE_ID       ,
       TAB_B.AD_GROUP_SID         ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
FROM   A_ROLE_LIST TAB_A
LEFT JOIN A_AD_GROUP_JUDGEMENT TAB_B ON (TAB_A.ROLE_ID = TAB_B.ITA_ROLE_ID);

CREATE OR REPLACE VIEW D_ROLE_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.ROLE_ID              ,
       TAB_A.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       CONCAT(TAB_A.ROLE_ID,':',TAB_A.ROLE_NAME) ROLE_PULLDOWN,
       TAB_B.GROUP_JUDGE_ID       ,
       TAB_B.AD_GROUP_SID         ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
FROM   A_ROLE_LIST_JNL TAB_A
LEFT JOIN A_AD_GROUP_JUDGEMENT TAB_B ON (TAB_A.ROLE_ID = TAB_B.ITA_ROLE_ID);

CREATE OR REPLACE VIEW D_MENU_LIST AS 
SELECT TAB_A.MENU_ID              ,
       TAB_A.MENU_GROUP_ID        ,
       TAB_B.MENU_GROUP_NAME      ,
       TAB_A.MENU_GROUP_ID          MENU_GROUP_ID_CLONE,
       TAB_A.MENU_NAME            ,
       CONCAT(TAB_A.MENU_GROUP_ID,':',TAB_B.MENU_GROUP_NAME,':',TAB_A.MENU_ID,':',TAB_A.MENU_NAME) MENU_PULLDOWN,
       TAB_A.LOGIN_NECESSITY      ,
       TAB_A.SERVICE_STATUS       ,
       TAB_A.AUTOFILTER_FLG       ,
       TAB_A.INITIAL_FILTER_FLG   ,
       TAB_A.WEB_PRINT_LIMIT      ,
       TAB_A.WEB_PRINT_CONFIRM    ,
       TAB_A.XLS_PRINT_LIMIT      ,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM   A_MENU_LIST TAB_A
LEFT JOIN A_MENU_GROUP_LIST TAB_B ON (TAB_A.MENU_GROUP_ID = TAB_B.MENU_GROUP_ID);

CREATE OR REPLACE VIEW D_MENU_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.MENU_ID              ,
       TAB_A.MENU_GROUP_ID        ,
       TAB_B.MENU_GROUP_NAME      ,
       TAB_A.MENU_GROUP_ID          MENU_GROUP_ID_CLONE,
       TAB_A.MENU_NAME            ,
       CONCAT(TAB_A.MENU_GROUP_ID,':',TAB_B.MENU_GROUP_NAME,':',TAB_A.MENU_ID,':',TAB_A.MENU_NAME) MENU_PULLDOWN,
       TAB_A.LOGIN_NECESSITY      ,
       TAB_A.SERVICE_STATUS       ,
       TAB_A.AUTOFILTER_FLG       ,
       TAB_A.INITIAL_FILTER_FLG   ,
       TAB_A.WEB_PRINT_LIMIT      ,
       TAB_A.WEB_PRINT_CONFIRM    ,
       TAB_A.XLS_PRINT_LIMIT      ,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM   A_MENU_LIST_JNL TAB_A
LEFT JOIN A_MENU_GROUP_LIST TAB_B ON (TAB_A.MENU_GROUP_ID = TAB_B.MENU_GROUP_ID);

CREATE OR REPLACE VIEW D_ROLE_MENU_LINK_LIST AS 
SELECT TAB_A.LINK_ID              ,
       TAB_A.ROLE_ID              ,
       TAB_D.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       TAB_B.MENU_GROUP_ID        ,
       TAB_C.MENU_GROUP_NAME      ,
       TAB_A.MENU_ID              ,
       TAB_B.MENU_NAME            ,
       TAB_A.MENU_ID                MENU_ID_CLONE,
       TAB_A.PRIVILEGE            ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER 
FROM A_ROLE_MENU_LINK_LIST TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
LEFT JOIN A_ROLE_LIST TAB_D ON (TAB_A.ROLE_ID = TAB_D.ROLE_ID);

CREATE OR REPLACE VIEW D_ROLE_MENU_LINK_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.LINK_ID              ,
       TAB_A.ROLE_ID              ,
       TAB_D.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       TAB_B.MENU_GROUP_ID        ,
       TAB_C.MENU_GROUP_NAME      ,
       TAB_A.MENU_ID              ,
       TAB_B.MENU_NAME            ,
       TAB_A.MENU_ID                MENU_ID_CLONE,
       TAB_A.PRIVILEGE            ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER 
FROM A_ROLE_MENU_LINK_LIST_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
LEFT JOIN A_ROLE_LIST TAB_D ON (TAB_A.ROLE_ID = TAB_D.ROLE_ID);

CREATE OR REPLACE VIEW D_ROLE_ACCOUNT_LINK_LIST AS 
SELECT TAB_A.LINK_ID              ,
       TAB_A.ROLE_ID              ,
       TAB_C.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       TAB_A.USER_ID              ,
       TAB_B.USERNAME             ,
       TAB_A.USER_ID                USER_ID_CLONE,
       TAB_A.DEF_ACCESS_AUTH_FLAG ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER 
FROM A_ROLE_ACCOUNT_LINK_LIST TAB_A
LEFT JOIN A_ACCOUNT_LIST TAB_B ON (TAB_A.USER_ID = TAB_B.USER_ID)
LEFT JOIN A_ROLE_LIST TAB_C ON (TAB_A.ROLE_ID = TAB_C.ROLE_ID)
WHERE TAB_A.USER_ID > 0;

CREATE OR REPLACE VIEW D_ROLE_ACCOUNT_LINK_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.LINK_ID              ,
       TAB_A.ROLE_ID              ,
       TAB_C.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       TAB_A.USER_ID              ,
       TAB_B.USERNAME             ,
       TAB_A.USER_ID                USER_ID_CLONE,
       TAB_A.DEF_ACCESS_AUTH_FLAG ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER 
FROM A_ROLE_ACCOUNT_LINK_LIST_JNL TAB_A
LEFT JOIN A_ACCOUNT_LIST TAB_B ON (TAB_A.USER_ID = TAB_B.USER_ID)
LEFT JOIN A_ROLE_LIST TAB_C ON (TAB_A.ROLE_ID = TAB_C.ROLE_ID)
WHERE TAB_A.USER_ID > 0;

CREATE OR REPLACE VIEW D_PROVIDER_LIST AS
SELECT TAB_A.PROVIDER_ID,
       TAB_A.PROVIDER_NAME,
       TAB_A.LOGO,
       TAB_A.AUTH_TYPE,
       TAB_A.VISIBLE_FLAG,
       TAB_A.ACCESS_AUTH,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_LIST TAB_A;

CREATE OR REPLACE VIEW D_PROVIDER_LIST_JNL AS
SELECT TAB_A.JOURNAL_SEQ_NO,
       TAB_A.JOURNAL_REG_DATETIME,
       TAB_A.JOURNAL_ACTION_CLASS,
       TAB_A.PROVIDER_ID,
       TAB_A.PROVIDER_NAME,
       TAB_A.LOGO,
       TAB_A.AUTH_TYPE,
       TAB_A.VISIBLE_FLAG,
       TAB_A.ACCESS_AUTH,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_LIST_JNL TAB_A;

CREATE OR REPLACE VIEW D_PROVIDER_ATTRIBUTE_LIST AS
SELECT TAB_A.PROVIDER_ATTRIBUTE_ID,
       TAB_A.PROVIDER_ID,
       TAB_A.NAME,
       TAB_A.VALUE,
       TAB_A.ACCESS_AUTH,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_ATTRIBUTE_LIST TAB_A;

CREATE OR REPLACE VIEW D_PROVIDER_ATTRIBUTE_LIST_JNL AS
SELECT TAB_A.JOURNAL_SEQ_NO,
       TAB_A.JOURNAL_REG_DATETIME,
       TAB_A.JOURNAL_ACTION_CLASS,
       TAB_A.PROVIDER_ATTRIBUTE_ID,
       TAB_A.PROVIDER_ID,
       TAB_A.NAME,
       TAB_A.VALUE,
       TAB_A.ACCESS_AUTH,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_ATTRIBUTE_LIST_JNL TAB_A;

CREATE OR REPLACE VIEW D_SEQUENCE AS 
SELECT TAB_A.NAME                 ,
       TAB_A.VALUE                ,
       TAB_B.MENU_NAME            ,
       TAB_B.MENU_GROUP_NAME      ,
       TAB_A.DISP_SEQ             ,
       TAB_A.NOTE                 ,
       '0' as DISUSE_FLAG         ,
       TAB_A.LAST_UPDATE_TIMESTAMP
FROM A_SEQUENCE  as TAB_A
     LEFT JOIN D_MENU_LIST as TAB_B on TAB_A.MENU_ID = TAB_B.MENU_ID
WHERE TAB_A.MENU_ID IS NOT NULL AND
      TAB_B.DISUSE_FLAG = '0';

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
       TAB_A.ACCESS_AUTH                      ACCESS_AUTH                   ,
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
       TAB_A.ACCESS_AUTH                      ACCESS_AUTH                   ,
       TAB_A.NOTE                             NOTE                          ,
       TAB_A.DISUSE_FLAG                      DISUSE_FLAG                   ,
       TAB_A.LAST_UPDATE_TIMESTAMP            LAST_UPDATE_TIMESTAMP         ,
       TAB_A.LAST_UPDATE_USER                 LAST_UPDATE_USER

FROM C_STM_LIST_JNL TAB_A;

CREATE OR REPLACE VIEW E_OPERATION_LIST 
AS 
SELECT TAB_A.OPERATION_NO_UAPK    ,
       TAB_A.OPERATION_NAME       ,
       TAB_A.OPERATION_DATE       ,
       TAB_A.OPERATION_NO_IDBH    ,
       CONCAT(TAB_A.OPERATION_NO_IDBH,':',TAB_A.OPERATION_NAME) OPERATION,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
FROM C_OPERATION_LIST TAB_A;

CREATE OR REPLACE VIEW E_OPERATION_LIST_JNL 
AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.OPERATION_NO_UAPK    ,
       TAB_A.OPERATION_NAME       ,
       TAB_A.OPERATION_DATE       ,
       TAB_A.OPERATION_NO_IDBH    ,
       CONCAT(TAB_A.OPERATION_NO_IDBH,':',TAB_A.OPERATION_NAME) OPERATION,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
FROM C_OPERATION_LIST_JNL TAB_A;

CREATE OR REPLACE VIEW G_OPERATION_LIST AS
SELECT OPERATION_NO_IDBH                             OPERATION_ID           ,
       OPERATION_NAME                                                       ,
       CONCAT(DATE_FORMAT( OPERATION_DATE, '%Y/%m/%d %H:%i' ),'_',OPERATION_NO_IDBH,':',OPERATION_NAME) OPERATION_ID_N_NAME,
       CASE
           WHEN LAST_EXECUTE_TIMESTAMP IS NULL THEN OPERATION_DATE
           ELSE LAST_EXECUTE_TIMESTAMP
       END BASE_TIMESTAMP,
       OPERATION_DATE                                                       ,
       DATE_FORMAT( OPERATION_DATE, '%Y/%m/%d %H:%i' ) OPERATION_DATE_DISP  ,
       LAST_EXECUTE_TIMESTAMP                                               ,
       ACCESS_AUTH                                                          ,
       NOTE                                                                 ,
       DISUSE_FLAG                                                          ,
       LAST_UPDATE_TIMESTAMP                                                ,
       LAST_UPDATE_USER
FROM   C_OPERATION_LIST;

ALTER TABLE B_CMDB_MENU_LIST ADD COLUMN SHEET_TYPE INT AFTER MENU_ID;
ALTER TABLE B_CMDB_MENU_LIST ADD COLUMN ACCESS_AUTH_FLG INT AFTER SHEET_TYPE;
ALTER TABLE B_CMDB_MENU_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;
UPDATE B_CMDB_MENU_LIST SET SHEET_TYPE=1;

ALTER TABLE B_CMDB_MENU_LIST_JNL ADD COLUMN SHEET_TYPE INT AFTER MENU_ID;
ALTER TABLE B_CMDB_MENU_LIST_JNL ADD COLUMN ACCESS_AUTH_FLG INT AFTER SHEET_TYPE;
ALTER TABLE B_CMDB_MENU_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;
UPDATE B_CMDB_MENU_LIST_JNL SET SHEET_TYPE=1;

CREATE OR REPLACE VIEW D_CMDB_MENU_LIST AS 
SELECT 
       TAB_A.MENU_LIST_ID                   , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_B.MENU_GROUP_ID                  ,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_B.MENU_NAME                      ,
       CONCAT(TAB_B.MENU_GROUP_ID,':',TAB_C.MENU_GROUP_NAME,':',TAB_A.MENU_ID,':',TAB_B.MENU_NAME) MENU_PULLDOWN,
       TAB_A.SHEET_TYPE                     ,
       TAB_A.ACCESS_AUTH_FLG                ,
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_CMDB_MENU_LIST TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
WHERE TAB_B.DISUSE_FLAG = '0'
;

CREATE OR REPLACE VIEW D_CMDB_MENU_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                 ,
       TAB_A.JOURNAL_REG_DATETIME           ,
       TAB_A.JOURNAL_ACTION_CLASS           ,
       TAB_A.MENU_LIST_ID                   , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_B.MENU_GROUP_ID                  ,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_B.MENU_NAME                      ,
       CONCAT(TAB_B.MENU_GROUP_ID,':',TAB_C.MENU_GROUP_NAME,':',TAB_A.MENU_ID,':',TAB_B.MENU_NAME) MENU_PULLDOWN,
       TAB_A.SHEET_TYPE                     ,
       TAB_A.ACCESS_AUTH_FLG                ,
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_CMDB_MENU_LIST_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
WHERE TAB_B.DISUSE_FLAG = '0'
;

ALTER TABLE B_CMDB_MENU_TABLE ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_CMDB_MENU_TABLE_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_CMDB_MENU_COLUMN ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_CMDB_MENU_COLUMN_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_CMDB_MENU_COL_TYPE ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_CMDB_MENU_COL_TYPE_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

CREATE OR REPLACE VIEW D_CMDB_MENU_GRP_LIST AS 
SELECT *
FROM   A_MENU_GROUP_LIST TAB_A
WHERE  MENU_GROUP_ID NOT IN 
(SELECT MENU_GROUP_ID 
 FROM  B_CMDB_HIDE_MENU_GRP);

CREATE OR REPLACE VIEW D_CMDB_MENU_GRP_LIST_JNL AS 
SELECT *
FROM   A_MENU_GROUP_LIST_JNL TAB_A
WHERE  MENU_GROUP_ID NOT IN 
(SELECT MENU_GROUP_ID 
 FROM  B_CMDB_HIDE_MENU_GRP);

CREATE OR REPLACE VIEW D_CMDB_TARGET_MENU_LIST AS 
SELECT 
  TAB_A.MENU_ID,
  TAB_A.MENU_NAME,
  TAB_B.MENU_GROUP_ID,
  TAB_B.MENU_GROUP_NAME,
  CONCAT(TAB_B.MENU_GROUP_ID,':',TAB_B.MENU_GROUP_NAME,':',TAB_A.MENU_ID,':',TAB_A.MENU_NAME) MENU_PULLDOWN,
  TAB_A.DISUSE_FLAG,
  TAB_A.ACCESS_AUTH
FROM 
  ( A_MENU_LIST TAB_A
    INNER JOIN A_MENU_GROUP_LIST TAB_B ON TAB_B.MENU_GROUP_ID = TAB_A.MENU_GROUP_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_B.MENU_GROUP_ID IN (SELECT MENU_GROUP_ID FROM D_CMDB_MENU_GRP_LIST)
;

CREATE OR REPLACE VIEW D_CMDB_TARGET_MENU_LIST_JNL AS 
SELECT 
  TAB_A.MENU_ID,
  TAB_A.MENU_NAME,
  TAB_B.MENU_GROUP_ID,
  TAB_B.MENU_GROUP_NAME,
  CONCAT(TAB_B.MENU_GROUP_ID,':',TAB_B.MENU_GROUP_NAME,':',TAB_A.MENU_ID,':',TAB_A.MENU_NAME) MENU_PULLDOWN,
  TAB_A.DISUSE_FLAG,
  TAB_A.ACCESS_AUTH
FROM 
  ( A_MENU_LIST_JNL TAB_A
    INNER JOIN A_MENU_GROUP_LIST TAB_B ON TAB_B.MENU_GROUP_ID = TAB_A.MENU_GROUP_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_B.MENU_GROUP_ID IN (SELECT MENU_GROUP_ID FROM D_CMDB_MENU_GRP_LIST)
;

CREATE OR REPLACE VIEW D_CMDB_MG_MU_COL_LIST AS 
SELECT
  TAB_A.COLUMN_LIST_ID                 , 
  CONCAT(TAB_D.MENU_GROUP_ID,':',TAB_D.MENU_GROUP_NAME,':',TAB_C.MENU_ID,':',TAB_C.MENU_NAME,':',TAB_A.COLUMN_LIST_ID,':',TAB_A.COL_TITLE) MENU_COL_TITLE_PULLDOWN,
  TAB_C.MENU_ID                        ,
  TAB_B.SHEET_TYPE                     ,
  TAB_A.COL_TITLE_DISP_SEQ             ,
  TAB_A.DISP_SEQ                       ,
  TAB_A.ACCESS_AUTH                    ,
  TAB_A.NOTE                           ,
  TAB_A.DISUSE_FLAG                    ,
  TAB_A.LAST_UPDATE_TIMESTAMP          ,
  TAB_A.LAST_UPDATE_USER 
FROM        B_CMDB_MENU_COLUMN TAB_A
  LEFT JOIN B_CMDB_MENU_LIST       TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
  LEFT JOIN A_MENU_LIST            TAB_C ON (TAB_A.MENU_ID       = TAB_C.MENU_ID)
  LEFT JOIN A_MENU_GROUP_LIST      TAB_D ON (TAB_C.MENU_GROUP_ID = TAB_D.MENU_GROUP_ID)
WHERE
   TAB_A.DISUSE_FLAG = '0' AND
   TAB_B.DISUSE_FLAG = '0' AND
   TAB_C.DISUSE_FLAG = '0' AND
   TAB_D.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW D_CMDB_MG_MU_COL_LIST_JNL AS 
SELECT 
  TAB_A.COLUMN_LIST_ID                 , 
  CONCAT(TAB_D.MENU_GROUP_ID,':',TAB_D.MENU_GROUP_NAME,':',TAB_C.MENU_ID,':',TAB_C.MENU_NAME,':',TAB_A.COLUMN_LIST_ID,':',TAB_A.COL_TITLE) MENU_COL_PULLDOWN,
  TAB_C.MENU_ID                        ,
  TAB_B.SHEET_TYPE                     ,
  TAB_A.COL_TITLE_DISP_SEQ             ,
  TAB_A.DISP_SEQ                       ,
  TAB_A.ACCESS_AUTH                    ,
  TAB_A.NOTE                           ,
  TAB_A.DISUSE_FLAG                    ,
  TAB_A.LAST_UPDATE_TIMESTAMP          ,
  TAB_A.LAST_UPDATE_USER 
FROM        B_CMDB_MENU_COLUMN_JNL TAB_A
  LEFT JOIN B_CMDB_MENU_LIST           TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
  LEFT JOIN A_MENU_LIST                TAB_C ON (TAB_A.MENU_ID       = TAB_C.MENU_ID)
  LEFT JOIN A_MENU_GROUP_LIST          TAB_D ON (TAB_C.MENU_GROUP_ID = TAB_D.MENU_GROUP_ID)
WHERE
   TAB_A.DISUSE_FLAG = '0' AND
   TAB_B.DISUSE_FLAG = '0' AND
   TAB_C.DISUSE_FLAG = '0' AND
   TAB_D.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_1 AS
SELECT
 *
FROM D_CMDB_MENU_LIST TAB_A
WHERE (SHEET_TYPE IS NULL OR SHEET_TYPE = 1)
;

CREATE OR REPLACE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_1_JNL AS
SELECT
 *
FROM D_CMDB_MENU_LIST_JNL TAB_A
WHERE (SHEET_TYPE IS NULL OR SHEET_TYPE = 1)
;

CREATE OR REPLACE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_1 AS
SELECT
 *
FROM D_CMDB_MG_MU_COL_LIST TAB_A
WHERE (SHEET_TYPE IS NULL OR SHEET_TYPE = 1)
;

CREATE OR REPLACE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_1_JNL AS
SELECT
 *
FROM D_CMDB_MG_MU_COL_LIST_JNL TAB_A
WHERE (SHEET_TYPE IS NULL OR SHEET_TYPE = 1)
;

CREATE OR REPLACE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_1 AS
SELECT
  TAB_B.*
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_1         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.DISUSE_FLAG = '0'
;

CREATE OR REPLACE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_1_JNL AS
SELECT
  TAB_B.*
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_1_JNL         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN_JNL TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.DISUSE_FLAG = '0'
;

CREATE OR REPLACE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_1_PIONEER AS
SELECT
  TAB_B.*
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_1         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.COL_CLASS   <>  'MultiTextColumn' AND
  TAB_B.DISUSE_FLAG = '0'
;

CREATE OR REPLACE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_1_PIONEER_JNL AS
SELECT
  TAB_B.*
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_1_JNL         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN_JNL TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.COL_CLASS   <>  'MultiTextColumn' AND
  TAB_B.DISUSE_FLAG = '0'
;

CREATE OR REPLACE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_1_PIONEER AS
SELECT 
  TBL_A.*
FROM 
  D_CMDB_MENU_LIST_SHEET_TYPE_1 TBL_A
WHERE
  (SELECT 
     COUNT(*) 
   FROM 
     B_CMDB_MENU_COLUMN TBL_B
   WHERE
     TBL_A.MENU_ID     =   TBL_B.MENU_ID     AND
     TBL_B.COL_CLASS   <>  'MultiTextColumn' AND
     TBL_B.DISUSE_FLAG =   '0'
  ) <> 0 
;

CREATE OR REPLACE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_1_PIONEER_JNL AS
SELECT 
  TBL_A.*
FROM 
  D_CMDB_MENU_LIST_SHEET_TYPE_1_JNL TBL_A
WHERE
  (SELECT 
     COUNT(*) 
   FROM 
     B_CMDB_MENU_COLUMN_JNL TBL_B
   WHERE
     TBL_A.MENU_ID     =   TBL_B.MENU_ID     AND
     TBL_B.COL_CLASS   <>  'MultiTextColumn' AND
     TBL_B.DISUSE_FLAG =   '0'
  ) <> 0
;

CREATE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_1_PIONEER AS
SELECT
  TAB_A.COLUMN_LIST_ID                 ,
  CONCAT(TAB_D.MENU_GROUP_ID,':',TAB_D.MENU_GROUP_NAME,':',TAB_C.MENU_ID,':',TAB_C.MENU_NAME,':',TAB_A.COLUMN_LIST_ID,':',TAB_A.COL_TITLE) MENU_COL_TITLE_PULLDOWN,
  TAB_C.MENU_ID                        ,
  TAB_A.COL_TITLE_DISP_SEQ             ,
  TAB_B.ACCESS_AUTH                    ,
  TAB_A.DISP_SEQ                       ,
  TAB_A.NOTE                           ,
  TAB_A.DISUSE_FLAG                    ,
  TAB_A.LAST_UPDATE_TIMESTAMP          ,
  TAB_A.LAST_UPDATE_USER
FROM        D_CMDB_MENU_COLUMN_SHEET_TYPE_1_PIONEER    TAB_A
  LEFT JOIN D_CMDB_MENU_LIST_SHEET_TYPE_1_PIONEER      TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
  LEFT JOIN A_MENU_LIST                                TAB_C ON (TAB_A.MENU_ID       = TAB_C.MENU_ID)
  LEFT JOIN A_MENU_GROUP_LIST                          TAB_D ON (TAB_C.MENU_GROUP_ID = TAB_D.MENU_GROUP_ID)
WHERE
   TAB_A.DISUSE_FLAG = '0' AND
   TAB_B.DISUSE_FLAG = '0' AND
   TAB_C.DISUSE_FLAG = '0' AND
   TAB_D.DISUSE_FLAG = '0'
;

CREATE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_1_PIONEER_JNL AS
SELECT
  TAB_A.COLUMN_LIST_ID                 ,
  CONCAT(TAB_D.MENU_GROUP_ID,':',TAB_D.MENU_GROUP_NAME,':',TAB_C.MENU_ID,':',TAB_C.MENU_NAME,':',TAB_A.COLUMN_LIST_ID,':',TAB_A.COL_TITLE) MENU_COL_PULLDOWN,
  TAB_C.MENU_ID                        ,
  TAB_A.COL_TITLE_DISP_SEQ             ,
  TAB_B.ACCESS_AUTH                    ,
  TAB_A.DISP_SEQ                       ,
  TAB_A.NOTE                           ,
  TAB_A.DISUSE_FLAG                    ,
  TAB_A.LAST_UPDATE_TIMESTAMP          ,
  TAB_A.LAST_UPDATE_USER
FROM        D_CMDB_MENU_COLUMN_SHEET_TYPE_1_PIONEER_JNL TAB_A
  LEFT JOIN D_CMDB_MENU_LIST_SHEET_TYPE_1_PIONEER       TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
  LEFT JOIN A_MENU_LIST                                 TAB_C ON (TAB_A.MENU_ID       = TAB_C.MENU_ID)
  LEFT JOIN A_MENU_GROUP_LIST                           TAB_D ON (TAB_C.MENU_GROUP_ID = TAB_D.MENU_GROUP_ID)
WHERE
   TAB_A.DISUSE_FLAG = '0' AND
   TAB_B.DISUSE_FLAG = '0' AND
   TAB_C.DISUSE_FLAG = '0' AND
   TAB_D.DISUSE_FLAG = '0'
;

-- -------------------------------------------------------
-- --代入値自動登録設定の「メニューグループ:メニュー:項目」SHEET_TYPE=3用
-- -------------------------------------------------------
CREATE OR REPLACE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_3 AS
SELECT
 *
FROM D_CMDB_MENU_LIST TAB_A
WHERE SHEET_TYPE = 3
;

CREATE OR REPLACE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_3_JNL AS
SELECT
 *
FROM D_CMDB_MENU_LIST_JNL TAB_A
WHERE SHEET_TYPE = 3
;

CREATE OR REPLACE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_3 AS
SELECT
 *
FROM D_CMDB_MG_MU_COL_LIST TAB_A
WHERE SHEET_TYPE = 3
;

CREATE OR REPLACE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_3_JNL AS
SELECT
 *
FROM D_CMDB_MG_MU_COL_LIST_JNL TAB_A
WHERE SHEET_TYPE = 3
;

CREATE OR REPLACE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_3 AS
SELECT
  TAB_B.*
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_3         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.DISUSE_FLAG = '0'
;

CREATE OR REPLACE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_3_JNL AS
SELECT
  TAB_B.*
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_3_JNL         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN_JNL TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.DISUSE_FLAG = '0'
;

ALTER TABLE A_DEL_OPERATION_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER DATA_PATH_4;

ALTER TABLE A_DEL_OPERATION_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DATA_PATH_4;

ALTER TABLE A_DEL_FILE_LIST ADD COLUMN ACCESS_AUTH TEXT AFTER DEL_SUB_DIR_FLG;

ALTER TABLE A_DEL_FILE_LIST_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DEL_SUB_DIR_FLG;

ALTER TABLE B_VALID_INVALID_MASTER ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;

ALTER TABLE B_VALID_INVALID_MASTER_JNL ADD COLUMN ACCESS_AUTH TEXT AFTER DISP_SEQ;



INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_CONDUCTOR_EDIT_CLASS_MNG_RIC',1,'2100180002',2100150009,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));
INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_CONDUCTOR_EDIT_CLASS_MNG_JSQ',1,'2100180002',2100150010,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));
INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_NODE_EDIT_CLASS_MNG_RIC',1,'2100180007',2100150011,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));
INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_NODE_EDIT_CLASS_MNG_JSQ',1,'2100180007',2100150012,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));
INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_NODE_TERMINALS_EDIT_CLASS_MNG_RIC',1,'2100180008',2100150013,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));
INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_NODE_TERMINALS_EDIT_CLASS_MNG_JSQ',1,'2100180008',2100150014,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));
INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('A_WIDGET_LIST_RIC',1,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

UPDATE A_SEQUENCE SET MENU_ID=2100000202, DISP_SEQ=2100110001, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_SYSTEM_CONFIG_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000202, DISP_SEQ=2100110002, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_SYSTEM_CONFIG_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000203, DISP_SEQ=2100110003, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_PERMISSIONS_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000203, DISP_SEQ=2100110004, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_PERMISSIONS_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000204, DISP_SEQ=2100110005, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_MENU_GROUP_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000204, DISP_SEQ=2100110006, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_MENU_GROUP_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000205, DISP_SEQ=2100110007, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_MENU_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000205, DISP_SEQ=2100110008, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_MENU_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000207, DISP_SEQ=2100110009, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_ROLE_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000207, DISP_SEQ=2100110010, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_ROLE_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000208, DISP_SEQ=2100110011, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_ACCOUNT_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000208, DISP_SEQ=2100110012, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_ACCOUNT_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000209, DISP_SEQ=2100110013, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_ROLE_MENU_LINK_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000209, DISP_SEQ=2100110014, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_ROLE_MENU_LINK_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000210, DISP_SEQ=2100110015, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_ROLE_ACCOUNT_LINK_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000210, DISP_SEQ=2100110016, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_ROLE_ACCOUNT_LINK_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000222, DISP_SEQ=2100110017, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_AD_USER_JUDGEMENT';
UPDATE A_SEQUENCE SET MENU_ID=2100000222, DISP_SEQ=2100110018, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_AD_USER_JUDGEMENT';
UPDATE A_SEQUENCE SET MENU_ID=2100000221, DISP_SEQ=2100110019, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_AD_GROUP_JUDGEMENT';
UPDATE A_SEQUENCE SET MENU_ID=2100000221, DISP_SEQ=2100110020, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_AD_GROUP_JUDGEMENT';
UPDATE A_SEQUENCE SET MENU_ID=2100000214, DISP_SEQ=2100110021, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='A_DEL_OPERATION_LIST_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000214, DISP_SEQ=2100110022, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='A_DEL_OPERATION_LIST_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000215, DISP_SEQ=2100110023, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='A_DEL_FILE_LIST_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000215, DISP_SEQ=2100110024, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='A_DEL_FILE_LIST_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000231, DISP_SEQ=2100110025, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_PROVIDER_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000231, DISP_SEQ=2100110026, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_PROVIDER_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000232, DISP_SEQ=2100110027, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_PROVIDER_ATTRIBUTE_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000232, DISP_SEQ=2100110028, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_PROVIDER_ATTRIBUTE_LIST';
UPDATE A_SEQUENCE SET MENU_ID=2100000303, DISP_SEQ=2100120001, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_STM_LIST_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000303, DISP_SEQ=2100120002, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_STM_LIST_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000304, DISP_SEQ=2100120003, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_OPERATION_LIST_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000304, DISP_SEQ=2100120004, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_OPERATION_LIST_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000305, DISP_SEQ=2100120005, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_PATTERN_PER_ORCH_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000305, DISP_SEQ=2100120006, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_PATTERN_PER_ORCH_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000304, DISP_SEQ=2100120007, NOTE='for the oparation ID.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_OPERATION_LIST_ANR1';
UPDATE A_SEQUENCE SET MENU_ID=2100000501, DISP_SEQ=2100120008, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_CMDB_MENU_LIST_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000501, DISP_SEQ=2100120009, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_CMDB_MENU_LIST_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000502, DISP_SEQ=2100120010, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_CMDB_MENU_TABLE_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000502, DISP_SEQ=2100120011, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_CMDB_MENU_TABLE_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000503, DISP_SEQ=2100120012, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_CMDB_MENU_COLUMN_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000503, DISP_SEQ=2100120013, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_CMDB_MENU_COLUMN_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000213, DISP_SEQ=2100130001, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_DP_STATUS_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000213, DISP_SEQ=2100130002, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_DP_STATUS_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000403, DISP_SEQ=2100130003, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_DP_SYM_OPE_STATUS_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000403, DISP_SEQ=2100130004, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_DP_SYM_OPE_STATUS_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000307, DISP_SEQ=2100140001, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_SYMPHONY_CLASS_MNG_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000307, DISP_SEQ=2100140002, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_SYMPHONY_CLASS_MNG_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000310, DISP_SEQ=2100140003, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_SYMPHONY_INSTANCE_MNG_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000310, DISP_SEQ=2100140004, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_SYMPHONY_INSTANCE_MNG_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000311, DISP_SEQ=2100140005, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_MOVEMENT_CLASS_MNG_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000311, DISP_SEQ=2100140006, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_MOVEMENT_CLASS_MNG_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000312, DISP_SEQ=2100140007, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_MOVEMENT_INSTANCE_MNG_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000312, DISP_SEQ=2100140008, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_MOVEMENT_INSTANCE_MNG_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000313, DISP_SEQ=2100140009, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_SYMPHONY_IF_INFO_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000313, DISP_SEQ=2100140010, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_SYMPHONY_IF_INFO_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100000314, DISP_SEQ=2100140011, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_REGULARLY_LIST_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100000314, DISP_SEQ=2100140012, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_REGULARLY_LIST_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100180006, DISP_SEQ=2100150001, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_CONDUCTOR_INSTANCE_MNG_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100180006, DISP_SEQ=2100150002, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_CONDUCTOR_INSTANCE_MNG_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100180010, DISP_SEQ=2100150003, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_NODE_INSTANCE_MNG_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100180010, DISP_SEQ=2100150004, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_NODE_INSTANCE_MNG_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100180011, DISP_SEQ=2100150005, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_REGULARLY2_LIST_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100180011, DISP_SEQ=2100150006, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_REGULARLY2_LIST_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=2100180001, DISP_SEQ=2100150007, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_CONDUCTOR_IF_INFO_RIC';
UPDATE A_SEQUENCE SET MENU_ID=2100180001, DISP_SEQ=2100150008, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_CONDUCTOR_IF_INFO_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190001, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_ACCOUNT_LOCK';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190002, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_ACCOUNT_LOCK';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190003, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_LOGIN_NECESSITY_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190004, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_LOGIN_NECESSITY_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190005, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_SERVICE_STATUS_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190006, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_SERVICE_STATUS_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190007, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_REPRESENTATIVE_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190008, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_REPRESENTATIVE_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190009, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_PRIVILEGE_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190010, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_PRIVILEGE_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190011, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_TODO_MASTER';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190012, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_TODO_MASTER';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190013, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_ITA_EXT_STM_ID';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190014, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_ITA_EXT_STM_ID';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190015, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_HARDAWRE_TYPE_RIC';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190016, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_HARDAWRE_TYPE_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190017, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_PROTOCOL_RIC';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190018, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_PROTOCOL_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190019, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_HOST_DESIGNATE_TYPE_LIST_RIC';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190020, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_HOST_DESIGNATE_TYPE_LIST_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190021, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_LOGIN_AUTH_TYPE_RIC';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190022, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_LOGIN_AUTH_TYPE_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190023, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='D_FLAG_LIST_01_RIC';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190024, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='D_FLAG_LIST_01_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190025, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_DP_STATUS_MASTER_RIC';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190026, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_DP_STATUS_MASTER_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190027, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_CMDB_MENU_COL_TYPE_RIC';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190028, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_CMDB_MENU_COL_TYPE_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190029, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_CMDB_HIDE_MENU_GRP_RIC';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190030, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='B_CMDB_HIDE_MENU_GRP_JSQ';
UPDATE A_SEQUENCE SET VALUE=4, MENU_ID=NULL, DISP_SEQ=2100190031, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='F_PARAM_TARGET_RIC';
UPDATE A_SEQUENCE SET VALUE=4, MENU_ID=NULL, DISP_SEQ=2100190032, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='F_PARAM_TARGET_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190033, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_PROVIDER_AUTH_TYPE_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190034, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_PROVIDER_AUTH_TYPE_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190035, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_VISIBLE_FLAG_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190036, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_VISIBLE_FLAG_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190037, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='SEQ_A_PROVIDER_ATTRIBUTE_NAME_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190038, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='JSEQ_A_PROVIDER_ATTRIBUTE_NAME_LIST';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190039, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_CONDUCTOR_CLASS_MNG_RIC';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190040, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_CONDUCTOR_CLASS_MNG_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190041, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_NODE_CLASS_MNG_RIC';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190042, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_NODE_CLASS_MNG_JSQ';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190043, NOTE=NULL, LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_NODE_TERMINALS_CLASS_MNG_RIC';
UPDATE A_SEQUENCE SET MENU_ID=NULL, DISP_SEQ=2100190044, NOTE='for the history table.', LAST_UPDATE_TIMESTAMP=STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f') WHERE NAME='C_NODE_TERMINALS_CLASS_MNG_JSQ';


INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000216,2100000002,'Sequence list',NULL,NULL,NULL,1,0,1,1,16,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-216,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000216,2100000002,'Sequence list',NULL,NULL,NULL,1,0,1,1,16,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000216,1,2100000216,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-216,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000216,1,2100000216,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(26,'2100011613',26,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(26,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',26,'2100011613',26,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000019,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_download','*',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000019,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000019,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_download','*',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000020,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/terraform_download_temp','*',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000020,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000020,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/terraform_download_temp','*',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000021,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/terraform_module_temp','*',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000021,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000021,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/terraform_module_temp','*',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('60','2100000216');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('61','2100080017');

INSERT INTO B_SENSITIVE_FLAG (VARS_SENSITIVE,VARS_SENSITIVE_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'OFF',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_SENSITIVE_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,VARS_SENSITIVE,VARS_SENSITIVE_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'OFF',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_SENSITIVE_FLAG (VARS_SENSITIVE,VARS_SENSITIVE_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'ON',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_SENSITIVE_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,VARS_SENSITIVE,VARS_SENSITIVE_SELECT,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'ON',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT IGNORE INTO F_PARAM_TARGET (TARGET_ID,DISP_SEQ,TARGET_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,1,'Parameter Sheet(Host/Operation)',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
UPDATE F_PARAM_TARGET SET DISP_SEQ=1 WHERE TARGET_ID=1;
INSERT IGNORE INTO F_PARAM_TARGET_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TARGET_ID,DISP_SEQ,TARGET_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,1,'Parameter Sheet(Host/Operation)',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
UPDATE F_PARAM_TARGET_JNL SET DISP_SEQ=1 WHERE TARGET_ID=1;
INSERT IGNORE INTO F_PARAM_TARGET (TARGET_ID,DISP_SEQ,TARGET_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,3,'Data Sheet',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
UPDATE F_PARAM_TARGET SET DISP_SEQ=3 WHERE TARGET_ID=2;
INSERT IGNORE INTO F_PARAM_TARGET_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TARGET_ID,DISP_SEQ,TARGET_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,3,'Data Sheet',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
UPDATE F_PARAM_TARGET_JNL SET DISP_SEQ=3 WHERE TARGET_ID=2;
INSERT IGNORE INTO F_PARAM_TARGET (TARGET_ID,DISP_SEQ,TARGET_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,2,'Parameter Sheet(Operation)',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
UPDATE F_PARAM_TARGET SET DISP_SEQ=2 WHERE TARGET_ID=3;
INSERT IGNORE INTO F_PARAM_TARGET_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TARGET_ID,DISP_SEQ,TARGET_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,2,'Parameter Sheet(Operation)',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
UPDATE F_PARAM_TARGET_JNL SET DISP_SEQ=2 WHERE TARGET_ID=3;

