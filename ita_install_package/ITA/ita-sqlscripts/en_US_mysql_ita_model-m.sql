-- *****************************************************************************
-- *** ***** CreateParameterMenu Tables                                      ***
-- *****************************************************************************
-- -------------------------
-- パラメータシート作成情報
-- -------------------------
CREATE TABLE F_CREATE_MENU_INFO
(
CREATE_MENU_ID                      INT                             , -- 識別シーケンス項番
MENU_NAME                           VARCHAR (256)                    ,
PURPOSE                             INT                             ,
TARGET                              INT                             ,
VERTICAL                            INT                             ,
MENUGROUP_FOR_INPUT                 INT                             ,
MENUGROUP_FOR_SUBST                 INT                             ,
MENUGROUP_FOR_VIEW                  INT                             ,
MENU_CREATE_STATUS                  VARCHAR  (1)                    ,
DISP_SEQ                            INT                             ,
DESCRIPTION                         VARCHAR (1024)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (CREATE_MENU_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_CREATE_MENU_INFO_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR (8)                     , -- 履歴用変更種別

CREATE_MENU_ID                      INT                             , -- 識別シーケンス項番
MENU_NAME                           VARCHAR (256)                    ,
PURPOSE                             INT                             ,
TARGET                              INT                             ,
VERTICAL                            INT                             ,
MENUGROUP_FOR_INPUT                 INT                             ,
MENUGROUP_FOR_SUBST                 INT                             ,
MENUGROUP_FOR_VIEW                  INT                             ,
MENU_CREATE_STATUS                  VARCHAR  (1)                    ,
DISP_SEQ                            INT                             ,
DESCRIPTION                         VARCHAR (1024)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- パラメータシート項目作成情報
-- -------------------------
CREATE TABLE F_CREATE_ITEM_INFO
(
CREATE_ITEM_ID                      INT                             , -- 識別シーケンス項番
CREATE_MENU_ID                      INT                             ,
ITEM_NAME                           VARCHAR (256)                   ,
DISP_SEQ                            INT                             ,
REQUIRED                            INT                             ,
UNIQUED                             INT                             ,
COL_GROUP_ID                        INT                             ,
INPUT_METHOD_ID                     INT                             ,
MAX_LENGTH                          INT                             ,
MULTI_MAX_LENGTH                    INT                             ,
PREG_MATCH                          TEXT                            ,
MULTI_PREG_MATCH                    TEXT                            ,
OTHER_MENU_LINK_ID                  INT                             ,
INT_MAX                             INT                             ,
INT_MIN                             INT                             ,
FLOAT_MAX                           DOUBLE                          ,
FLOAT_MIN                           DOUBLE                          ,
FLOAT_DIGIT                         INT                             ,
PW_MAX_LENGTH                       INT                             ,
UPLOAD_MAX_SIZE                     LONG                            ,
LINK_LENGTH                         INT                             ,
REFERENCE_ITEM                      TEXT                            ,
TYPE3_REFERENCE                     INT                             ,
SINGLE_DEFAULT_VALUE                TEXT                            ,
MULTI_DEFAULT_VALUE                 TEXT                            ,
INT_DEFAULT_VALUE                   INT                             ,
FLOAT_DEFAULT_VALUE                 DOUBLE                          ,
DATETIME_DEFAULT_VALUE              DATETIME(6)                     ,
DATE_DEFAULT_VALUE                  DATETIME(6)                     ,
PULLDOWN_DEFAULT_VALUE              INT                             ,
LINK_DEFAULT_VALUE                  TEXT                            ,
DESCRIPTION                         VARCHAR (1024)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (CREATE_ITEM_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_CREATE_ITEM_INFO_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

CREATE_ITEM_ID                      INT                             , -- 識別シーケンス項番
CREATE_MENU_ID                      INT                             ,
ITEM_NAME                           VARCHAR (256)                   ,
DISP_SEQ                            INT                             ,
REQUIRED                            INT                             ,
UNIQUED                             INT                             ,
COL_GROUP_ID                        INT                             ,
INPUT_METHOD_ID                     INT                             ,
MAX_LENGTH                          INT                             ,
MULTI_MAX_LENGTH                    INT                             ,
PREG_MATCH                          TEXT                            ,
MULTI_PREG_MATCH                    TEXT                            ,
OTHER_MENU_LINK_ID                  INT                             ,
INT_MAX                             INT                             ,
INT_MIN                             INT                             ,
FLOAT_MAX                           DOUBLE                          ,
FLOAT_MIN                           DOUBLE                          ,
FLOAT_DIGIT                         INT                             ,
PW_MAX_LENGTH                       INT                             ,
UPLOAD_MAX_SIZE                     LONG                            ,
LINK_LENGTH                         INT                             ,
REFERENCE_ITEM                      TEXT                            ,
TYPE3_REFERENCE                     INT                             ,
SINGLE_DEFAULT_VALUE                TEXT                            ,
MULTI_DEFAULT_VALUE                 TEXT                            ,
INT_DEFAULT_VALUE                   INT                             ,
FLOAT_DEFAULT_VALUE                 DOUBLE                          ,
DATETIME_DEFAULT_VALUE              DATETIME(6)                     ,
DATE_DEFAULT_VALUE                  DATETIME(6)                     ,
PULLDOWN_DEFAULT_VALUE              INT                             ,
LINK_DEFAULT_VALUE                  TEXT                            ,
DESCRIPTION                         VARCHAR (1024)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- パラメータシート・テーブル紐付
-- -------------------------
CREATE TABLE F_MENU_TABLE_LINK
(
MENU_TABLE_LINK_ID                  INT                             , -- 識別シーケンス項番
MENU_ID                             INT                             ,
TABLE_NAME                          VARCHAR (64)                    ,
KEY_COL_NAME                        VARCHAR (64)                    ,
TABLE_NAME_JNL                      VARCHAR (64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ

PRIMARY KEY (MENU_TABLE_LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_MENU_TABLE_LINK_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

MENU_TABLE_LINK_ID                  INT                             , -- 識別シーケンス項番
MENU_ID                             INT                             ,
TABLE_NAME                          VARCHAR (64)                    ,
KEY_COL_NAME                        VARCHAR (64)                    ,
TABLE_NAME_JNL                      VARCHAR (64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- パラメータシート作成管理
-- -------------------------
CREATE TABLE F_CREATE_MENU_STATUS
(
MM_STATUS_ID                        INT                             , -- 識別シーケンス項番

CREATE_MENU_ID                      INT                             ,
STATUS_ID                           INT                             ,
MENU_CREATE_TYPE_ID                 INT                             ,
FILE_NAME                           VARCHAR (64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (MM_STATUS_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_CREATE_MENU_STATUS_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

MM_STATUS_ID                        INT                             , -- 識別シーケンス項番
CREATE_MENU_ID                      INT                             ,
STATUS_ID                           INT                             ,
MENU_CREATE_TYPE_ID                 INT                             ,
FILE_NAME                           VARCHAR (64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- メニュー作成ステータスマスタ
-- -------------------------
CREATE TABLE F_CM_STATUS_MASTER
(
STATUS_ID                           INT                             , -- 識別シーケンス項番
STATUS_NAME                         VARCHAR (64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (STATUS_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_CM_STATUS_MASTER_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

STATUS_ID                           INT                             , -- 識別シーケンス項番
STATUS_NAME                         VARCHAR (64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- 用途マスタ
-- -------------------------
CREATE TABLE F_PARAM_PURPOSE
(
PURPOSE_ID                          INT                             , -- 識別シーケンス項番
PURPOSE_NAME                        VARCHAR (64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (PURPOSE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_PARAM_PURPOSE_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

PURPOSE_ID                          INT                             , -- 識別シーケンス項番
PURPOSE_NAME                        VARCHAR (64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- 他メニュー連携
-- -------------------------
CREATE TABLE F_OTHER_MENU_LINK
(
LINK_ID                             INT                             , -- 識別シーケンス項番
MENU_ID                             INT                             ,
COLUMN_DISP_NAME                    VARCHAR (4096)                   ,
TABLE_NAME                          VARCHAR (64)                    ,
PRI_NAME                            VARCHAR (64)                    ,
COLUMN_NAME                         VARCHAR (64)                    ,
COLUMN_TYPE                         INT                             ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_OTHER_MENU_LINK_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

LINK_ID                             INT                             , -- 識別シーケンス項番
MENU_ID                             INT                             ,
COLUMN_DISP_NAME                    VARCHAR (4096)                   ,
TABLE_NAME                          VARCHAR (64)                    ,
PRI_NAME                            VARCHAR (64)                    ,
COLUMN_NAME                         VARCHAR (64)                    ,
COLUMN_TYPE                         INT                             ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- 入力方式マスタ
-- -------------------------
CREATE TABLE F_INPUT_METHOD
(
INPUT_METHOD_ID                     INT                             , -- 識別シーケンス項番
INPUT_METHOD_NAME                   VARCHAR  (256)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (INPUT_METHOD_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_INPUT_METHOD_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

INPUT_METHOD_ID                     INT                             , -- 識別シーケンス項番
INPUT_METHOD_NAME                   VARCHAR  (256)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- カラムグループ管理
-- -------------------------
CREATE TABLE F_COLUMN_GROUP
(
COL_GROUP_ID                        INT                             , -- 識別シーケンス項番
PA_COL_GROUP_ID                     INT                             ,
FULL_COL_GROUP_NAME                 VARCHAR  (4096)                 ,
COL_GROUP_NAME                      VARCHAR  (256)                  ,
DISP_SEQ                            INT                             ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (COL_GROUP_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_COLUMN_GROUP_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

COL_GROUP_ID                        INT                             , -- 識別シーケンス項番
PA_COL_GROUP_ID                     INT                             ,
FULL_COL_GROUP_NAME                 VARCHAR  (4096)                 ,
COL_GROUP_NAME                      VARCHAR  (256)                  ,
DISP_SEQ                            INT                             ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- パラメータシート(縦)作成情報
-- -------------------------
CREATE TABLE F_CONVERT_PARAM_INFO
(
CONVERT_PARAM_ID                    INT                             , -- 識別シーケンス項番
CREATE_ITEM_ID                      INT                             ,
COL_CNT                             INT                             ,
REPEAT_CNT                          INT                             ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (CONVERT_PARAM_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_CONVERT_PARAM_INFO_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

CONVERT_PARAM_ID                    INT                             , -- 識別シーケンス項番
CREATE_ITEM_ID                      INT                             ,
COL_CNT                             INT                             ,
REPEAT_CNT                          INT                             ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- パラメータシート縦横変換管理
-- -------------------------
CREATE TABLE F_COL_TO_ROW_MNG (
ROW_ID                        INT               ,
FROM_MENU_ID                  INT               ,
TO_MENU_ID                    INT               ,
PURPOSE                       INT               ,
START_COL_NAME                VARCHAR  (64)     ,
COL_CNT                       INT               ,
REPEAT_CNT                    INT               ,
CHANGED_FLG                   VARCHAR  (1)      ,
ACCESS_AUTH                  TEXT               ,
NOTE                          VARCHAR  (4000)   ,
DISUSE_FLAG                   VARCHAR  (1)      ,
LAST_UPDATE_TIMESTAMP         DATETIME(6)       ,
LAST_UPDATE_USER              INT               ,
PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_COL_TO_ROW_MNG_JNL (
JOURNAL_SEQ_NO                INT               ,
JOURNAL_REG_DATETIME          DATETIME(6)       ,
JOURNAL_ACTION_CLASS          VARCHAR  (8)      ,
ROW_ID                        INT               ,
FROM_MENU_ID                  INT               ,
TO_MENU_ID                    INT               ,
PURPOSE                       INT               ,
START_COL_NAME                VARCHAR  (64)     ,
COL_CNT                       INT               ,
REPEAT_CNT                    INT               ,
CHANGED_FLG                   VARCHAR  (1)      ,
ACCESS_AUTH                  TEXT               ,
NOTE                          VARCHAR  (4000)   ,
DISUSE_FLAG                   VARCHAR  (1)      ,
LAST_UPDATE_TIMESTAMP         DATETIME(6)       ,
LAST_UPDATE_USER              INT               ,
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- 一意制約管理
-- -------------------------
CREATE TABLE F_UNIQUE_CONSTRAINT (
UNIQUE_CONSTRAINT_ID          INT               ,
CREATE_MENU_ID                INT               ,
UNIQUE_CONSTRAINT_ITEM        TEXT              ,
ACCESS_AUTH                   TEXT              ,
NOTE                          VARCHAR  (4000)   ,
DISUSE_FLAG                   VARCHAR  (1)      ,
LAST_UPDATE_TIMESTAMP         DATETIME(6)       ,
LAST_UPDATE_USER              INT               ,
PRIMARY KEY (UNIQUE_CONSTRAINT_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_UNIQUE_CONSTRAINT_JNL (
JOURNAL_SEQ_NO                INT               ,
JOURNAL_REG_DATETIME          DATETIME(6)       ,
JOURNAL_ACTION_CLASS          VARCHAR  (8)      ,
UNIQUE_CONSTRAINT_ID          INT               ,
CREATE_MENU_ID                INT               ,
UNIQUE_CONSTRAINT_ITEM        TEXT              ,
ACCESS_AUTH                   TEXT              ,
NOTE                          VARCHAR  (4000)   ,
DISUSE_FLAG                   VARCHAR  (1)      ,
LAST_UPDATE_TIMESTAMP         DATETIME(6)       ,
LAST_UPDATE_USER              INT               ,
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- 参照項目情報
-- -------------------------
CREATE TABLE F_MENU_REFERENCE_ITEM
(
ITEM_ID                             INT                               , -- 識別シーケンス項番
LINK_ID                             INT                               ,
MENU_ID                             INT                               ,
DISP_SEQ                            INT                               ,
TABLE_NAME                          VARCHAR  (64)                     ,
PRI_NAME                            VARCHAR  (64)                     ,
COLUMN_NAME                         VARCHAR  (64)                     ,
ITEM_NAME                           VARCHAR  (64)                     ,
COL_GROUP_NAME                      TEXT                              ,
DESCRIPTION                         TEXT                              ,
INPUT_METHOD_ID                     INT                               ,
SENSITIVE_FLAG                      VARCHAR  (1)                      ,
ORIGINAL_MENU_FLAG                  VARCHAR  (1)                      ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                VARCHAR  (4000)                   , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                    INT                               , -- 最終更新ユーザ
PRIMARY KEY (ITEM_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_MENU_REFERENCE_ITEM_JNL
(
JOURNAL_SEQ_NO                      INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                      , -- 履歴用変更種別

ITEM_ID                             INT                               , -- 識別シーケンス項番
LINK_ID                             INT                               ,
MENU_ID                             INT                               ,
DISP_SEQ                            INT                               ,
TABLE_NAME                          VARCHAR  (64)                     ,
PRI_NAME                            VARCHAR  (64)                     ,
COLUMN_NAME                         VARCHAR  (64)                     ,
ITEM_NAME                           VARCHAR  (64)                     ,
COL_GROUP_NAME                      TEXT                              ,
DESCRIPTION                         TEXT                              ,
INPUT_METHOD_ID                     INT                               ,
SENSITIVE_FLAG                      VARCHAR  (1)                      ,
ORIGINAL_MENU_FLAG                  VARCHAR  (1)                      ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                    INT                               , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


-- -------------------------
-- メニュー作成状態マスタ
-- -------------------------
CREATE TABLE F_MENU_CREATE_STATUS
(
MENU_CREATE_STATUS                  INT                             ,
MENU_CREATE_STATUS_SELECT           VARCHAR  (256)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (MENU_CREATE_STATUS)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_MENU_CREATE_STATUS_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

MENU_CREATE_STATUS                  INT                             ,
MENU_CREATE_STATUS_SELECT           VARCHAR  (256)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- フラグ管理マスタ
-- -------------------------
CREATE TABLE F_FLAG_ALT_MASTER
(
FLAG_ID                           INT                               , -- 識別シーケンス
YESNO_STATUS                      VARCHAR (64)                      , -- ステータス
TRUEFALSE_STATUS                  VARCHAR (64)                      ,
DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (FLAG_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_FLAG_ALT_MASTER_JNL
(
JOURNAL_SEQ_NO                    INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                       , -- 履歴用変更種別

FLAG_ID                           INT                               , -- 識別シーケンス
YESNO_STATUS                      VARCHAR (64)                      , -- ステータス
TRUEFALSE_STATUS                  VARCHAR (64)                      ,
DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_FLAG_MASTER
(
FLAG_ID                           INT                               , -- 識別シーケンス
ASTBLANK_STATUS                   VARCHAR (64)                      ,
DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (FLAG_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_FLAG_MASTER_JNL
(
JOURNAL_SEQ_NO                    INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                       , -- 履歴用変更種別

FLAG_ID                           INT                               , -- 識別シーケンス
ASTBLANK_STATUS                   VARCHAR (64)                      ,
DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- *****************************************************************************
-- *** ***** Views                                                           ***
-- *****************************************************************************

-- -------------------------
-- 必須マスタ
-- -------------------------
CREATE OR REPLACE VIEW G_REQUIRED_MASTER AS
SELECT 1      AS REQUIRED_ID            ,
       '●'   AS REQUIRED_NAME          ,
       ''     AS ACCESS_AUTH            ,
       '0'    AS DISUSE_FLAG            ,
       NOW(6) AS LAST_UPDATE_TIMESTAMP  ,
       1      AS LAST_UPDATE_USER
;

-- -------------------------
-- 他メニュー連携
-- -------------------------
CREATE VIEW G_OTHER_MENU_LINK AS 
SELECT TAB_A.LINK_ID                       ,
       TAB_C.MENU_GROUP_ID                 ,
       TAB_C.MENU_GROUP_NAME               ,
       TAB_A.MENU_ID                       ,
       TAB_A.MENU_ID MENU_ID_CLONE         ,
       TAB_B.MENU_NAME                     ,
       TAB_A.COLUMN_DISP_NAME              ,
       CONCAT(TAB_C.MENU_GROUP_NAME,':',TAB_B.MENU_NAME,':',TAB_A.COLUMN_DISP_NAME) LINK_PULLDOWN,
       TAB_A.TABLE_NAME                    ,
       TAB_A.PRI_NAME                      ,
       TAB_A.COLUMN_NAME                   ,
       TAB_A.COLUMN_TYPE                   ,
       TAB_A.ACCESS_AUTH                   ,
       TAB_A.NOTE                          ,
       TAB_A.DISUSE_FLAG                   ,
       TAB_A.LAST_UPDATE_TIMESTAMP         ,
       TAB_A.LAST_UPDATE_USER              ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM F_OTHER_MENU_LINK TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
WHERE TAB_B.DISUSE_FLAG='0' AND TAB_C.DISUSE_FLAG='0'
;

CREATE VIEW G_OTHER_MENU_LINK_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                ,
       TAB_A.JOURNAL_REG_DATETIME          ,
       TAB_A.JOURNAL_ACTION_CLASS          ,
       TAB_A.LINK_ID                       ,
       TAB_C.MENU_GROUP_ID                 ,
       TAB_C.MENU_GROUP_NAME               ,
       TAB_A.MENU_ID                       ,
       TAB_A.MENU_ID MENU_ID_CLONE         ,
       TAB_B.MENU_NAME                     ,
       TAB_A.COLUMN_DISP_NAME              ,
       CONCAT(TAB_C.MENU_GROUP_NAME,':',TAB_B.MENU_NAME,':',TAB_A.COLUMN_DISP_NAME) LINK_PULLDOWN,
       TAB_A.TABLE_NAME                    ,
       TAB_A.PRI_NAME                      ,
       TAB_A.COLUMN_NAME                   ,
       TAB_A.COLUMN_TYPE                   ,
       TAB_A.ACCESS_AUTH                   ,
       TAB_A.NOTE                          ,
       TAB_A.DISUSE_FLAG                   ,
       TAB_A.LAST_UPDATE_TIMESTAMP         ,
       TAB_A.LAST_UPDATE_USER              ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM F_OTHER_MENU_LINK_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
WHERE TAB_B.DISUSE_FLAG='0' AND TAB_C.DISUSE_FLAG='0'
;

-- -------------------------
-- パラメータシート項目作成情報
-- -------------------------
CREATE VIEW G_CREATE_ITEM_INFO AS 
SELECT TAB_A.CREATE_ITEM_ID,
       TAB_A.CREATE_MENU_ID,
       TAB_A.ITEM_NAME,
       TAB_A.DISP_SEQ,
       TAB_A.REQUIRED,
       TAB_A.UNIQUED,
       TAB_A.COL_GROUP_ID,
       TAB_A.INPUT_METHOD_ID,
       TAB_A.MAX_LENGTH,
       TAB_A.MULTI_MAX_LENGTH,
       TAB_A.PREG_MATCH,
       TAB_A.MULTI_PREG_MATCH,
       TAB_A.OTHER_MENU_LINK_ID,
       TAB_A.INT_MAX,
       TAB_A.INT_MIN,
       TAB_A.FLOAT_MAX,
       TAB_A.FLOAT_MIN,
       TAB_A.FLOAT_DIGIT,
       TAB_A.PW_MAX_LENGTH,
       TAB_A.UPLOAD_MAX_SIZE,
       TAB_A.LINK_LENGTH,
       TAB_A.REFERENCE_ITEM,
       TAB_A.TYPE3_REFERENCE,
       TAB_A.SINGLE_DEFAULT_VALUE,
       TAB_A.MULTI_DEFAULT_VALUE,
       TAB_A.INT_DEFAULT_VALUE,
       TAB_A.FLOAT_DEFAULT_VALUE,
       TAB_A.DATETIME_DEFAULT_VALUE,
       TAB_A.DATE_DEFAULT_VALUE,
       TAB_A.PULLDOWN_DEFAULT_VALUE,
       TAB_A.LINK_DEFAULT_VALUE,
       TAB_A.DESCRIPTION,
       TAB_C.FULL_COL_GROUP_NAME,
       CASE
           WHEN TAB_C.FULL_COL_GROUP_NAME IS NULL THEN CONCAT(TAB_B.MENU_NAME,':',TAB_A.ITEM_NAME)
           ELSE CONCAT(TAB_B.MENU_NAME,':',TAB_C.FULL_COL_GROUP_NAME,'/',TAB_A.ITEM_NAME)
       END LINK_PULLDOWN,
       TAB_A.ACCESS_AUTH,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM F_CREATE_ITEM_INFO TAB_A
LEFT JOIN F_CREATE_MENU_INFO TAB_B ON (TAB_A.CREATE_MENU_ID = TAB_B.CREATE_MENU_ID)
LEFT JOIN F_COLUMN_GROUP TAB_C ON (TAB_A.COL_GROUP_ID = TAB_C.COL_GROUP_ID)
WHERE TAB_B.VERTICAL != ""
;

CREATE VIEW G_CREATE_ITEM_INFO_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO,
       TAB_A.JOURNAL_REG_DATETIME,
       TAB_A.JOURNAL_ACTION_CLASS,
       TAB_A.CREATE_ITEM_ID,
       TAB_A.CREATE_MENU_ID,
       TAB_A.ITEM_NAME,
       TAB_A.DISP_SEQ,
       TAB_A.REQUIRED,
       TAB_A.UNIQUED,
       TAB_A.COL_GROUP_ID,
       TAB_A.INPUT_METHOD_ID,
       TAB_A.MAX_LENGTH,
       TAB_A.MULTI_MAX_LENGTH,
       TAB_A.PREG_MATCH,
       TAB_A.MULTI_PREG_MATCH,
       TAB_A.OTHER_MENU_LINK_ID,
       TAB_A.INT_MAX,
       TAB_A.INT_MIN,
       TAB_A.FLOAT_MAX,
       TAB_A.FLOAT_MIN,
       TAB_A.FLOAT_DIGIT,
       TAB_A.PW_MAX_LENGTH,
       TAB_A.UPLOAD_MAX_SIZE,
       TAB_A.LINK_LENGTH,
       TAB_A.REFERENCE_ITEM,
       TAB_A.TYPE3_REFERENCE,
       TAB_A.SINGLE_DEFAULT_VALUE,
       TAB_A.MULTI_DEFAULT_VALUE,
       TAB_A.INT_DEFAULT_VALUE,
       TAB_A.FLOAT_DEFAULT_VALUE,
       TAB_A.DATETIME_DEFAULT_VALUE,
       TAB_A.DATE_DEFAULT_VALUE,
       TAB_A.PULLDOWN_DEFAULT_VALUE,
       TAB_A.LINK_DEFAULT_VALUE,
       TAB_A.DESCRIPTION,
       CASE
           WHEN TAB_C.FULL_COL_GROUP_NAME IS NULL THEN CONCAT(TAB_B.MENU_NAME,':',TAB_A.ITEM_NAME)
           ELSE CONCAT(TAB_B.MENU_NAME,':',TAB_C.FULL_COL_GROUP_NAME,'/',TAB_A.ITEM_NAME)
       END LINK_PULLDOWN,
       TAB_A.ACCESS_AUTH,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM F_CREATE_ITEM_INFO_JNL TAB_A
LEFT JOIN F_CREATE_MENU_INFO TAB_B ON (TAB_A.CREATE_MENU_ID = TAB_B.CREATE_MENU_ID)
LEFT JOIN F_COLUMN_GROUP TAB_C ON (TAB_A.COL_GROUP_ID = TAB_C.COL_GROUP_ID)
WHERE TAB_B.VERTICAL != ""
;


-- -------------------------
-- 参照項目情報（メニュー作成用）
-- -------------------------
CREATE VIEW G_CREATE_REFERENCE_ITEM AS 
SELECT DISTINCT TAB_A.CREATE_ITEM_ID ITEM_ID      ,
       NULL AS LINK_ID                            ,
       TAB_C.MENU_ID MENU_ID                      ,
       TAB_A.DISP_SEQ DISP_SEQ                    ,
       TAB_C.TABLE_NAME TABLE_NAME                ,
       TAB_C.PRI_NAME PRI_NAME                    ,
       CASE WHEN CHAR_LENGTH(TAB_A.CREATE_ITEM_ID) <= 4 THEN CONCAT('KY_AUTO_COL_', lpad(TAB_A.CREATE_ITEM_ID, 4, '0')) ELSE CONCAT('KY_AUTO_COL_', TAB_A.CREATE_ITEM_ID) END AS COLUMN_NAME,
       TAB_A.ITEM_NAME ITEM_NAME                  ,
       TAB_D.FULL_COL_GROUP_NAME COL_GROUP_NAME   ,
       TAB_A.DESCRIPTION DESCRIPTION              ,
       TAB_A.INPUT_METHOD_ID INPUT_METHOD_ID      ,
       CASE WHEN TAB_A.INPUT_METHOD_ID = 8 THEN 2 ELSE 1 END AS SENSITIVE_FLAG,
       NULL AS ORIGINAL_MENU_FLAG                 ,
       TAB_A.ACCESS_AUTH                          ,
       TAB_A.NOTE                                 ,
       TAB_A.DISUSE_FLAG                          ,
       TAB_A.LAST_UPDATE_TIMESTAMP                ,
       TAB_A.LAST_UPDATE_USER                     ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01        ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02        ,
       TAB_D.ACCESS_AUTH AS ACCESS_AUTH_03 
FROM F_CREATE_ITEM_INFO TAB_A
LEFT JOIN F_CREATE_MENU_INFO TAB_B ON (TAB_A.CREATE_MENU_ID = TAB_B.CREATE_MENU_ID)
LEFT JOIN G_OTHER_MENU_LINK TAB_C ON (TAB_B.MENU_NAME = TAB_C.MENU_NAME)
LEFT JOIN F_COLUMN_GROUP TAB_D ON (TAB_A.COL_GROUP_ID = TAB_D.COL_GROUP_ID)
WHERE NOT TAB_A.INPUT_METHOD_ID = 7 AND NOT TAB_A.INPUT_METHOD_ID = 11 AND TAB_B.DISUSE_FLAG='0' AND TAB_C.DISUSE_FLAG='0'
;


CREATE VIEW G_CREATE_REFERENCE_ITEM_JNL AS 
SELECT DISTINCT TAB_A.JOURNAL_SEQ_NO              ,
       TAB_A.JOURNAL_REG_DATETIME                 ,
       TAB_A.JOURNAL_ACTION_CLASS                 ,
       TAB_A.CREATE_ITEM_ID ITEM_ID               ,
       NULL AS LINK_ID                            ,
       TAB_C.MENU_ID MENU_ID                      ,
       TAB_A.DISP_SEQ DISP_SEQ                    ,
       TAB_C.TABLE_NAME TABLE_NAME                ,
       TAB_C.PRI_NAME PRI_NAME                    ,
       CASE WHEN CHAR_LENGTH(TAB_A.CREATE_ITEM_ID) <= 4 THEN CONCAT('KY_AUTO_COL_', lpad(TAB_A.CREATE_ITEM_ID, 4, '0')) ELSE CONCAT('KY_AUTO_COL_', TAB_A.CREATE_ITEM_ID) END AS COLUMN_NAME,
       TAB_A.ITEM_NAME ITEM_NAME                  ,
       TAB_D.FULL_COL_GROUP_NAME COL_GROUP_NAME   ,
       TAB_A.DESCRIPTION DESCRIPTION              ,
       TAB_A.INPUT_METHOD_ID INPUT_METHOD_ID      ,
       CASE WHEN TAB_A.INPUT_METHOD_ID = 8 THEN 2 ELSE 1 END AS SENSITIVE_FLAG,
       NULL AS ORIGINAL_MENU_FLAG                 ,
       TAB_A.ACCESS_AUTH                          ,
       TAB_A.NOTE                                 ,
       TAB_A.DISUSE_FLAG                          ,
       TAB_A.LAST_UPDATE_TIMESTAMP                ,
       TAB_A.LAST_UPDATE_USER                     ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01        ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02        ,
       TAB_D.ACCESS_AUTH AS ACCESS_AUTH_03 
FROM F_CREATE_ITEM_INFO_JNL TAB_A
LEFT JOIN F_CREATE_MENU_INFO TAB_B ON (TAB_A.CREATE_MENU_ID = TAB_B.CREATE_MENU_ID)
LEFT JOIN G_OTHER_MENU_LINK TAB_C ON (TAB_B.MENU_NAME = TAB_C.MENU_NAME)
LEFT JOIN F_COLUMN_GROUP TAB_D ON (TAB_A.COL_GROUP_ID = TAB_D.COL_GROUP_ID)
WHERE NOT TAB_A.INPUT_METHOD_ID = 7 AND NOT TAB_A.INPUT_METHOD_ID = 11 AND TAB_B.DISUSE_FLAG='0' AND TAB_C.DISUSE_FLAG='0'
;

-- -------------------------
-- 参照項目情報（既存メニュー/作成メニュー結合）
-- -------------------------
CREATE VIEW G_MENU_REFERENCE_ITEM AS 
SELECT TAB_A.ITEM_ID  ITEM_ID                            ,
       TAB_A.LINK_ID  LINK_ID                            ,
       TAB_A.MENU_ID  MENU_ID                            ,
       TAB_A.DISP_SEQ DISP_SEQ                           ,
       TAB_A.TABLE_NAME TABLE_NAME                       ,
       TAB_A.PRI_NAME PRI_NAME                           ,
       TAB_A.COLUMN_NAME COLUMN_NAME                     ,
       TAB_A.ITEM_NAME ITEM_NAME                         ,
       TAB_A.COL_GROUP_NAME COL_GROUP_NAME               ,
       TAB_A.DESCRIPTION DESCRIPTION                     ,
       TAB_A.INPUT_METHOD_ID INPUT_METHOD_ID             ,
       TAB_A.SENSITIVE_FLAG SENSITIVE_FLAG               ,
       TAB_A.ORIGINAL_MENU_FLAG ORIGINAL_MENU_FLAG       ,
       TAB_A.ACCESS_AUTH ACCESS_AUTH                     ,
       TAB_A.NOTE NOTE                                   ,
       TAB_A.DISUSE_FLAG DISUSE_FLAG                     ,
       TAB_A.LAST_UPDATE_TIMESTAMP LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER LAST_UPDATE_USER
FROM  F_MENU_REFERENCE_ITEM TAB_A
WHERE TAB_A.DISUSE_FLAG = '0'
UNION ALL
SELECT TAB_B.ITEM_ID  ITEM_ID                            ,
       TAB_B.LINK_ID  LINK_ID                            ,
       TAB_B.MENU_ID  MENU_ID                            ,
       TAB_B.DISP_SEQ DISP_SEQ                           ,
       TAB_B.TABLE_NAME TABLE_NAME                       ,
       TAB_B.PRI_NAME PRI_NAME                           ,
       TAB_B.COLUMN_NAME COLUMN_NAME                     ,
       TAB_B.ITEM_NAME ITEM_NAME                         ,
       TAB_B.COL_GROUP_NAME COL_GROUP_NAME               ,
       TAB_B.DESCRIPTION DESCRIPTION                     ,
       TAB_B.INPUT_METHOD_ID INPUT_METHOD_ID             ,
       TAB_B.SENSITIVE_FLAG SENSITIVE_FLAG               ,
       TAB_B.ORIGINAL_MENU_FLAG ORIGINAL_MENU_FLAG       ,
       TAB_B.ACCESS_AUTH ACCESS_AUTH                     ,
       TAB_B.NOTE NOTE                                   ,
       TAB_B.DISUSE_FLAG DISUSE_FLAG                     ,
       TAB_B.LAST_UPDATE_TIMESTAMP LAST_UPDATE_TIMESTAMP ,
       TAB_B.LAST_UPDATE_USER LAST_UPDATE_USER
FROM G_CREATE_REFERENCE_ITEM TAB_B
WHERE TAB_B.DISUSE_FLAG = '0'
;


CREATE VIEW G_MENU_REFERENCE_ITEM_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                              ,
       TAB_A.JOURNAL_REG_DATETIME                        ,
       TAB_A.JOURNAL_ACTION_CLASS                        ,
       TAB_A.ITEM_ID  ITEM_ID                            ,
       TAB_A.LINK_ID  LINK_ID                            ,
       TAB_A.MENU_ID  MENU_ID                            ,
       TAB_A.DISP_SEQ DISP_SEQ                           ,
       TAB_A.TABLE_NAME TABLE_NAME                       ,
       TAB_A.PRI_NAME PRI_NAME                           ,
       TAB_A.COLUMN_NAME COLUMN_NAME                     ,
       TAB_A.ITEM_NAME ITEM_NAME                         ,
       TAB_A.COL_GROUP_NAME COL_GROUP_NAME               ,
       TAB_A.DESCRIPTION DESCRIPTION                     ,
       TAB_A.INPUT_METHOD_ID INPUT_METHOD_ID             ,
       TAB_A.SENSITIVE_FLAG SENSITIVE_FLAG               ,
       TAB_A.ORIGINAL_MENU_FLAG ORIGINAL_MENU_FLAG       ,
       TAB_A.ACCESS_AUTH ACCESS_AUTH                     ,
       TAB_A.NOTE NOTE                                   ,
       TAB_A.DISUSE_FLAG DISUSE_FLAG                     ,
       TAB_A.LAST_UPDATE_TIMESTAMP LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER LAST_UPDATE_USER
FROM  F_MENU_REFERENCE_ITEM_JNL TAB_A
WHERE TAB_A.DISUSE_FLAG = '0'
UNION ALL
SELECT TAB_B.JOURNAL_SEQ_NO                              ,
       TAB_B.JOURNAL_REG_DATETIME                        ,
       TAB_B.JOURNAL_ACTION_CLASS                        ,
       TAB_B.ITEM_ID  ITEM_ID                            ,
       TAB_B.LINK_ID  LINK_ID                            ,
       TAB_B.MENU_ID  MENU_ID                            ,
       TAB_B.DISP_SEQ DISP_SEQ                           ,
       TAB_B.TABLE_NAME TABLE_NAME                       ,
       TAB_B.PRI_NAME PRI_NAME                           ,
       TAB_B.COLUMN_NAME COLUMN_NAME                     ,
       TAB_B.ITEM_NAME ITEM_NAME                         ,
       TAB_B.COL_GROUP_NAME COL_GROUP_NAME               ,
       TAB_B.DESCRIPTION DESCRIPTION                     ,
       TAB_B.INPUT_METHOD_ID INPUT_METHOD_ID             ,
       TAB_B.SENSITIVE_FLAG SENSITIVE_FLAG               ,
       TAB_B.ORIGINAL_MENU_FLAG ORIGINAL_MENU_FLAG       ,
       TAB_B.ACCESS_AUTH ACCESS_AUTH                     ,
       TAB_B.NOTE NOTE                                   ,
       TAB_B.DISUSE_FLAG DISUSE_FLAG                     ,
       TAB_B.LAST_UPDATE_TIMESTAMP LAST_UPDATE_TIMESTAMP ,
       TAB_B.LAST_UPDATE_USER LAST_UPDATE_USER
FROM G_CREATE_REFERENCE_ITEM_JNL TAB_B
WHERE TAB_B.DISUSE_FLAG = '0'
;

-- -------------------------
-- パラメータシート(オペレーションあり)参照情報
-- -------------------------
CREATE VIEW G_CREATE_REFERENCE_SHEET_TYPE_3 AS 
SELECT DISTINCT TAB_A.CREATE_ITEM_ID ITEM_ID      ,
       TAB_B.MENU_NAME MENU_NAME                  ,
       TAB_B.MENUGROUP_FOR_SUBST MENUGROUP_FOR_SUBST ,
       TAB_C.MENU_ID MENU_ID                      ,
       TAB_C.MENU_GROUP_ID MENU_GROUP_ID          ,
       TAB_C.MENU_GROUP_NAME MENU_GROUP_NAME      ,
       TAB_D.MENU_TABLE_LINK_ID MENU_TABLE_LINK_ID,
       TAB_D.TABLE_NAME TABLE_NAME                ,
       TAB_A.CREATE_ITEM_ID CREATE_ITEM_ID        ,
       TAB_A.ITEM_NAME ITEM_NAME                  ,
       TAB_A.INPUT_METHOD_ID INPUT_METHOD_ID      ,
       TAB_E.COL_GROUP_ID COL_GROUP_ID            ,
       TAB_E.FULL_COL_GROUP_NAME FULL_COL_GROUP_NAME ,
       CASE WHEN TAB_E.FULL_COL_GROUP_NAME IS NULL THEN TAB_A.ITEM_NAME ELSE CONCAT(TAB_E.FULL_COL_GROUP_NAME,'/',TAB_A.ITEM_NAME) END AS COL_TITLE,
       CASE WHEN TAB_E.FULL_COL_GROUP_NAME IS NULL THEN CONCAT(TAB_C.MENU_GROUP_NAME,':',TAB_B.MENU_NAME,':',TAB_A.ITEM_NAME) ELSE CONCAT(TAB_C.MENU_GROUP_NAME,':',TAB_B.MENU_NAME,':',TAB_E.FULL_COL_GROUP_NAME,'/',TAB_A.ITEM_NAME) END AS MENU_PULLDOWN,
       CASE WHEN CHAR_LENGTH(TAB_A.CREATE_ITEM_ID) <= 4 THEN CONCAT('KY_AUTO_COL_', lpad(TAB_A.CREATE_ITEM_ID, 4, '0')) ELSE CONCAT('KY_AUTO_COL_', TAB_A.CREATE_ITEM_ID) END AS COLUMN_NAME,
       TAB_A.DISP_SEQ DISP_SEQ                    ,
       TAB_A.ACCESS_AUTH                          ,
       TAB_A.NOTE                                 ,
       TAB_A.DISUSE_FLAG                          ,
       TAB_A.LAST_UPDATE_TIMESTAMP                ,
       TAB_A.LAST_UPDATE_USER                     ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01        ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02        ,
       TAB_D.ACCESS_AUTH AS ACCESS_AUTH_03        ,
       TAB_E.ACCESS_AUTH AS ACCESS_AUTH_04
FROM F_CREATE_ITEM_INFO TAB_A
LEFT JOIN F_CREATE_MENU_INFO TAB_B ON (TAB_A.CREATE_MENU_ID = TAB_B.CREATE_MENU_ID AND TAB_B.TARGET='3')
LEFT JOIN D_MENU_LIST TAB_C ON (TAB_B.MENU_NAME = TAB_C.MENU_NAME AND TAB_C.MENU_GROUP_ID = TAB_B.MENUGROUP_FOR_SUBST)
LEFT JOIN F_MENU_TABLE_LINK TAB_D ON (TAB_C.MENU_ID = TAB_D.MENU_ID)
LEFT JOIN F_COLUMN_GROUP TAB_E ON (TAB_A.COL_GROUP_ID = TAB_E.COL_GROUP_ID)
WHERE (TAB_A.DISUSE_FLAG='0' AND TAB_B.DISUSE_FLAG='0' AND TAB_C.DISUSE_FLAG='0' AND TAB_D.DISUSE_FLAG='0')
AND (TAB_A.INPUT_METHOD_ID != 7 AND TAB_A.INPUT_METHOD_ID != 11)
;

CREATE VIEW G_CREATE_REFERENCE_SHEET_TYPE_3_JNL AS 
SELECT DISTINCT TAB_A.JOURNAL_SEQ_NO              ,
       TAB_A.JOURNAL_REG_DATETIME                 ,
       TAB_A.JOURNAL_ACTION_CLASS                 ,
       TAB_A.CREATE_ITEM_ID ITEM_ID               ,
       TAB_B.MENU_NAME MENU_NAME                  ,
       TAB_B.MENUGROUP_FOR_SUBST MENUGROUP_FOR_SUBST ,
       TAB_C.MENU_ID MENU_ID                      ,
       TAB_C.MENU_GROUP_ID MENU_GROUP_ID          ,
       TAB_C.MENU_GROUP_NAME MENU_GROUP_NAME      ,
       TAB_D.MENU_TABLE_LINK_ID MENU_TABLE_LINK_ID,
       TAB_D.TABLE_NAME TABLE_NAME                ,
       TAB_A.CREATE_ITEM_ID CREATE_ITEM_ID        ,
       TAB_A.ITEM_NAME ITEM_NAME                  ,
       TAB_A.INPUT_METHOD_ID INPUT_METHOD_ID      ,
       TAB_E.COL_GROUP_ID COL_GROUP_ID            ,
       TAB_E.FULL_COL_GROUP_NAME FULL_COL_GROUP_NAME ,
       CASE WHEN TAB_E.FULL_COL_GROUP_NAME IS NULL THEN TAB_A.ITEM_NAME ELSE CONCAT(TAB_E.FULL_COL_GROUP_NAME,'/',TAB_A.ITEM_NAME) END AS COL_TITLE,
       CASE WHEN TAB_E.FULL_COL_GROUP_NAME IS NULL THEN CONCAT(TAB_C.MENU_GROUP_NAME,':',TAB_B.MENU_NAME,':',TAB_A.ITEM_NAME) ELSE CONCAT(TAB_C.MENU_GROUP_NAME,':',TAB_B.MENU_NAME,':',TAB_E.FULL_COL_GROUP_NAME,'/',TAB_A.ITEM_NAME) END AS MENU_PULLDOWN,
       CASE WHEN CHAR_LENGTH(TAB_A.CREATE_ITEM_ID) <= 4 THEN CONCAT('KY_AUTO_COL_', lpad(TAB_A.CREATE_ITEM_ID, 4, '0')) ELSE CONCAT('KY_AUTO_COL_', TAB_A.CREATE_ITEM_ID) END AS COLUMN_NAME,
       TAB_A.DISP_SEQ DISP_SEQ                    ,
       TAB_A.ACCESS_AUTH                          ,
       TAB_A.NOTE                                 ,
       TAB_A.DISUSE_FLAG                          ,
       TAB_A.LAST_UPDATE_TIMESTAMP                ,
       TAB_A.LAST_UPDATE_USER                     ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01        ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02        ,
       TAB_D.ACCESS_AUTH AS ACCESS_AUTH_03        ,
       TAB_E.ACCESS_AUTH AS ACCESS_AUTH_04
FROM F_CREATE_ITEM_INFO_JNL TAB_A
LEFT JOIN F_CREATE_MENU_INFO TAB_B ON (TAB_A.CREATE_MENU_ID = TAB_B.CREATE_MENU_ID AND TAB_B.TARGET='3')
LEFT JOIN D_MENU_LIST TAB_C ON (TAB_B.MENU_NAME = TAB_C.MENU_NAME AND TAB_C.MENU_GROUP_ID = TAB_B.MENUGROUP_FOR_SUBST)
LEFT JOIN F_MENU_TABLE_LINK TAB_D ON (TAB_C.MENU_ID = TAB_D.MENU_ID)
LEFT JOIN F_COLUMN_GROUP TAB_E ON (TAB_A.COL_GROUP_ID = TAB_E.COL_GROUP_ID)
WHERE (TAB_A.DISUSE_FLAG='0' AND TAB_B.DISUSE_FLAG='0' AND TAB_C.DISUSE_FLAG='0' AND TAB_D.DISUSE_FLAG='0')
AND (TAB_A.INPUT_METHOD_ID != 7 AND TAB_A.INPUT_METHOD_ID != 11)
;


-- *****************************************************************************
-- *** ***** Contrast Tables                                      ***
-- *****************************************************************************
-- -------------------------
-- 比較定義情報
-- -------------------------
CREATE TABLE A_CONTRAST_LIST
(
CONTRAST_LIST_ID                    INT                               , -- 識別シーケンス項番
CONTRAST_NAME                       TEXT                              ,
CONTRAST_MENU_ID_1                  INT                               ,
CONTRAST_MENU_ID_2                  INT                               ,
ALL_MATCH_FLG                       INT                               ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                VARCHAR  (4000)                   , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                    INT                               , -- 最終更新ユーザ
PRIMARY KEY (CONTRAST_LIST_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_CONTRAST_LIST_JNL
(
JOURNAL_SEQ_NO                      INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                      , -- 履歴用変更種別

CONTRAST_LIST_ID                    INT                               , -- 識別シーケンス項番
CONTRAST_NAME                       TEXT                              ,
CONTRAST_MENU_ID_1                  INT                               ,
CONTRAST_MENU_ID_2                  INT                               ,
ALL_MATCH_FLG                       INT                               ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                VARCHAR  (4000)                   , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                    INT                               , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- 比較定義詳細
-- -------------------------
CREATE TABLE A_CONTRAST_DETAIL
(
CONTRAST_DETAIL_ID                  INT                               , -- 識別シーケンス項番
CONTRAST_LIST_ID                    INT                               ,
CONTRAST_COL_TITLE                  TEXT                              ,
CONTRAST_COL_ID_1                   INT                               ,
CONTRAST_COL_ID_2                   INT                               ,
DISP_SEQ                            INT                               ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                VARCHAR  (4000)                   , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                    INT                               , -- 最終更新ユーザ
PRIMARY KEY (CONTRAST_DETAIL_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_CONTRAST_DETAIL_JNL
(
JOURNAL_SEQ_NO                      INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                      , -- 履歴用変更種別

CONTRAST_DETAIL_ID                  INT                               , -- 識別シーケンス項番
CONTRAST_LIST_ID                    INT                               ,
CONTRAST_COL_TITLE                  TEXT                              ,
CONTRAST_COL_ID_1                   INT                               ,
CONTRAST_COL_ID_2                   INT                               ,
DISP_SEQ                            INT                               ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                VARCHAR  (4000)                   , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                    INT                               , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- *****************************************************************************
-- *** ***** Contrast View                                      ***
-- *****************************************************************************
-- -------------------------
-- 比較定義情報「比較定義名:対象メニュー1/2」(プルダウン用)
-- -------------------------
CREATE VIEW D_CONTRAST_LIST AS 
SELECT 
    TAB_A.* ,
    concat( TAB_A.CONTRAST_NAME ,' [ ' ,TAB_A.CONTRAST_MENU_ID_1 ,':', TAB_B.MENU_NAME ,'-', TAB_A.CONTRAST_MENU_ID_2 ,':', TAB_C.MENU_NAME ,' ] ') AS PULLDOWN 
FROM A_CONTRAST_LIST TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON ( TAB_B.MENU_ID = TAB_A.CONTRAST_MENU_ID_1 )
LEFT JOIN A_MENU_LIST TAB_C ON ( TAB_C.MENU_ID = TAB_A.CONTRAST_MENU_ID_2 )
WHERE
    TAB_A.ALL_MATCH_FLG IS NULL AND
    TAB_A.DISUSE_FLAG = '0' AND
    TAB_B.DISUSE_FLAG = '0'
;
CREATE VIEW D_CONTRAST_LIST_JNL AS 
SELECT 
    TAB_A.*,
    concat( TAB_A.CONTRAST_NAME ,' [ ' ,TAB_A.CONTRAST_MENU_ID_1 ,':', TAB_B.MENU_NAME ,'-', TAB_A.CONTRAST_MENU_ID_2 ,':', TAB_C.MENU_NAME ,' ] ') AS PULLDOWN 
FROM A_CONTRAST_LIST_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON ( TAB_B.MENU_ID = TAB_A.CONTRAST_MENU_ID_1 )
LEFT JOIN A_MENU_LIST TAB_C ON ( TAB_C.MENU_ID = TAB_A.CONTRAST_MENU_ID_2 )
WHERE
    TAB_A.ALL_MATCH_FLG IS NULL AND
    TAB_A.DISUSE_FLAG = '0' AND
    TAB_B.DISUSE_FLAG = '0'
;
-- -------------------------
-- 比較定義詳細
-- -------------------------
CREATE VIEW D_CONTRAST_DETAIL AS 
SELECT 
    TAB_A.* ,
    TAB_A.CONTRAST_COL_ID_1 AS REST_CONTRAST_COL_ID_1,
    TAB_A.CONTRAST_COL_ID_2 AS REST_CONTRAST_COL_ID_2
FROM
    A_CONTRAST_DETAIL TAB_A 
;

CREATE VIEW D_CONTRAST_DETAIL_JNL AS 
SELECT 
    TAB_A.* ,
    TAB_A.CONTRAST_COL_ID_1 AS REST_CONTRAST_COL_ID_1,
    TAB_A.CONTRAST_COL_ID_2 AS REST_CONTRAST_COL_ID_2
FROM
    A_CONTRAST_DETAIL_JNL TAB_A
;

-- -------------------------
-- 比較定義詳細項目参照情報「メニューグループ:メニュー:項目」(プルダウン用)
-- -------------------------
CREATE VIEW D_CMDB_MG_MU_COL_LIST_CONTRAST AS 
SELECT
    TAB_A.*                 , 
    CONCAT(TAB_D.MENU_GROUP_ID,':',TAB_D.MENU_GROUP_NAME,':',TAB_A.MENU_ID,':',TAB_C.MENU_NAME,':',TAB_A.COLUMN_LIST_ID,':',TAB_A.COL_TITLE) MENU_COL_TITLE_PULLDOWN,
    TAB_B.SHEET_TYPE                     ,
    TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01  ,
    TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02  ,
    TAB_D.ACCESS_AUTH AS ACCESS_AUTH_03
FROM B_CMDB_MENU_COLUMN TAB_A
    LEFT JOIN B_CMDB_MENU_LIST       TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
    LEFT JOIN A_MENU_LIST            TAB_C ON (TAB_A.MENU_ID       = TAB_C.MENU_ID)
    LEFT JOIN A_MENU_GROUP_LIST      TAB_D ON (TAB_C.MENU_GROUP_ID = TAB_D.MENU_GROUP_ID)
WHERE
    TAB_A.COL_CLASS   <>  'PasswordColumn' AND 
    TAB_A.DISUSE_FLAG = '0' AND
    TAB_B.DISUSE_FLAG = '0' AND
    TAB_C.DISUSE_FLAG = '0' AND
    TAB_D.DISUSE_FLAG = '0';

CREATE VIEW D_CMDB_MG_MU_COL_LIST_CONTRAST_JNL AS 
SELECT 
    TAB_A.*                 , 
    CONCAT(TAB_D.MENU_GROUP_ID,':',TAB_D.MENU_GROUP_NAME,':',TAB_A.MENU_ID,':',TAB_C.MENU_NAME,':',TAB_A.COLUMN_LIST_ID,':',TAB_A.COL_TITLE) MENU_COL_TITLE_PULLDOWN,
    TAB_B.SHEET_TYPE                     ,
    TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01  ,
    TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02  ,
    TAB_D.ACCESS_AUTH AS ACCESS_AUTH_03
FROM B_CMDB_MENU_COLUMN_JNL TAB_A
    LEFT JOIN B_CMDB_MENU_LIST           TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
    LEFT JOIN A_MENU_LIST                TAB_C ON (TAB_A.MENU_ID       = TAB_C.MENU_ID)
    LEFT JOIN A_MENU_GROUP_LIST          TAB_D ON (TAB_C.MENU_GROUP_ID = TAB_D.MENU_GROUP_ID)
WHERE
   TAB_A.COL_CLASS   <>  'PasswordColumn' AND 
   TAB_A.DISUSE_FLAG = '0' AND
   TAB_B.DISUSE_FLAG = '0' AND
   TAB_C.DISUSE_FLAG = '0' AND
   TAB_D.DISUSE_FLAG = '0';

-- -------------------------------------------------------
-- 比較定義メニュー参照情報「メニューグループ:メニュー」(プルダウン用)
-- -------------------------------------------------------
CREATE VIEW D_CMDB_MENU_LIST_CONTRAST AS
SELECT
 *
FROM D_CMDB_MENU_LIST TAB_A
WHERE (SHEET_TYPE IS NULL OR SHEET_TYPE = 1 OR SHEET_TYPE = 4)
;

CREATE VIEW D_CMDB_MENU_LIST_CONTRAST_JNL AS
SELECT
 *
FROM D_CMDB_MENU_LIST_JNL TAB_A
WHERE (SHEET_TYPE IS NULL OR SHEET_TYPE = 1 OR SHEET_TYPE = 4)
;



INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_CREATE_MENU_INFO_RIC',1,'2100160001',2100610001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_CREATE_MENU_INFO_JSQ',1,'2100160001',2100610002,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_CREATE_ITEM_INFO_RIC',1,'2100160002',2100610003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_CREATE_ITEM_INFO_JSQ',1,'2100160002',2100610004,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_MENU_TABLE_LINK_RIC',1,'2100160005',2100610005,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_MENU_TABLE_LINK_JSQ',1,'2100160005',2100610006,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_CREATE_MENU_STATUS_RIC',1,'2100160004',2100610007,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_CREATE_MENU_STATUS_JSQ',1,'2100160004',2100610008,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_OTHER_MENU_LINK_RIC',1,'2100160007',2100610009,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_OTHER_MENU_LINK_JSQ',1,'2100160007',2100610010,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_COLUMN_GROUP_RIC',1,'2100160008',2100610011,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_COLUMN_GROUP_JSQ',1,'2100160008',2100610012,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_CONVERT_PARAM_INFO_RIC',1,'2100160009',2100610013,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_CONVERT_PARAM_INFO_JSQ',1,'2100160009',2100610014,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_COL_TO_ROW_MNG_RIC',1,'2100160010',2100610015,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_COL_TO_ROW_MNG_JSQ',1,'2100160010',2100610016,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_CM_STATUS_MASTER_RIC',5,NULL,2100690001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_CM_STATUS_MASTER_JSQ',5,NULL,2100690002,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_PARAM_PURPOSE_RIC',3,NULL,2100690003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_PARAM_PURPOSE_JSQ',3,NULL,2100690004,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_INPUT_METHOD_RIC',3,NULL,2100690005,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_INPUT_METHOD_JSQ',3,NULL,2100690006,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_MENU_REFERENCE_ITEM_RIC',1,'2100160012',2100610017,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_MENU_REFERENCE_ITEM_JSQ',1,'2100160012',2100610018,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('A_CONTRAST_LIST_RIC',1,'2100190001',2100900001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('A_CONTRAST_LIST_JSQ',1,'2100190001',2100900002,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('A_CONTRAST_DETAIL_RIC',1,'2100190002',2100900003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('A_CONTRAST_DETAIL_JSQ',1,'2100190002',2100900004,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_FLAG_MASTER_RIC',2,'2100160016',2100610019,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_FLAG_MASTER_JSQ',2,'2100160016',2100610020,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_FLAG_ALT_MASTER_RIC',3,'2100160017',2100610021,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_FLAG_ALT_MASTER_JSQ',3,'2100160017',2100610022,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_UNIQUE_CONSTRAINT_RIC',1,'2100160018',2100610023,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('F_UNIQUE_CONSTRAINT_JSQ',1,'2100160018',2100610024,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));


INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011601,'Create Menu','sheet.png',51,'Create Menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011601,'Create Menu','sheet.png',51,'Create Menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011609,'Vertical Menu broken into host',NULL,59,'Vertical Menu broken into host','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011609,'Vertical Menu broken into host',NULL,59,'Vertical Menu broken into host','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011610,'Input','for-input.png',52,'Create Menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011610,'Input','for-input.png',52,'Create Menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011611,'Substitution value','for-subst.png',53,'Create Menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160011,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011611,'Substitution value','for-subst.png',53,'Create Menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011612,'Reference','for-view.png',54,'Create Menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160012,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011612,'Reference','for-view.png',54,'Create Menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011613,'Vertical conveｒsion',NULL,58,'Create Menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160013,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011613,'Vertical conveｒsion',NULL,58,'Create Menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100110001,'Compare','comparison.png',55,'Compare','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160014,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100110001,'Compare','comparison.png',55,'Compare','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160001,2100011601,'Menu definition information',NULL,NULL,NULL,1,0,1,2,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160001,2100011601,'Menu definition information',NULL,NULL,NULL,1,0,1,2,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160002,2100011601,'Menu item creation information',NULL,NULL,NULL,1,0,1,2,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160002,2100011601,'Menu item creation information',NULL,NULL,NULL,1,0,1,2,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160003,2100011601,'Create Menu',NULL,NULL,NULL,1,0,2,2,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160003,2100011601,'Create Menu',NULL,NULL,NULL,1,0,2,2,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160004,2100011601,'Menu creation history',NULL,NULL,NULL,1,0,1,2,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160004,2100011601,'Menu creation history',NULL,NULL,NULL,1,0,1,2,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160005,2100011601,'Menu・Table link list',NULL,NULL,NULL,1,0,1,2,210,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160005,2100011601,'Menu・Table link list',NULL,NULL,NULL,1,0,1,2,210,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160007,2100011601,'Other menu link',NULL,NULL,NULL,1,0,1,2,220,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160007,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160007,2100011601,'Other menu link',NULL,NULL,NULL,1,0,1,2,220,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160008,2100011601,'Column group list',NULL,NULL,NULL,1,0,1,2,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160008,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160008,2100011601,'Column group list',NULL,NULL,NULL,1,0,1,2,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160009,2100011601,'Vertical Menu creation information',NULL,NULL,NULL,1,0,1,2,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160009,2100011601,'Vertical Menu creation information',NULL,NULL,NULL,1,0,1,2,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160010,2100011601,'Menu conversion information',NULL,NULL,NULL,1,0,1,2,230,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160010,2100011601,'Menu conversion information',NULL,NULL,NULL,1,0,1,2,230,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160011,2100011601,'Create・Define menu',NULL,NULL,NULL,1,0,1,2,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160011,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160011,2100011601,'Create・Define menu',NULL,NULL,NULL,1,0,1,2,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160012,2100011601,'Reference Item Info',NULL,NULL,NULL,1,0,1,2,240,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160012,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160012,2100011601,'Reference Item Info',NULL,NULL,NULL,1,0,1,2,240,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100190001,2100110001,'Compare list',NULL,NULL,NULL,1,0,1,2,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160013,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100190001,2100110001,'Compare list',NULL,NULL,NULL,1,0,1,2,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100190002,2100110001,'Compare details',NULL,NULL,NULL,1,0,1,2,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160014,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100190002,2100110001,'Compare details',NULL,NULL,NULL,1,0,1,2,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100190003,2100110001,'Compare execution',NULL,NULL,NULL,1,0,1,2,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160015,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100190003,2100110001,'Compare execution',NULL,NULL,NULL,1,0,1,2,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160016,2100011601,'Selection 1',NULL,NULL,NULL,1,0,1,2,250,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160016,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160016,2100011601,'Selection 1',NULL,NULL,NULL,1,0,1,2,250,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160017,2100011601,'Selection 2',NULL,NULL,NULL,1,0,1,2,260,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160017,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160017,2100011601,'Selection 2',NULL,NULL,NULL,1,0,1,2,260,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160018,2100011601,'Unique constraint(Multiple items) creation information',NULL,NULL,NULL,1,0,1,2,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160018,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160018,2100011601,'Unique constraint(Multiple items) creation information',NULL,NULL,NULL,1,0,1,2,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101601,'m01','5ebbc37e034d6874a2af59eb04beaa52','Create Menu procedure',NULL,NULL,NULL,NULL,NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101601,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101601,'m01','5ebbc37e034d6874a2af59eb04beaa52','Create Menu procedure',NULL,NULL,NULL,NULL,NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101603,'m03','5ebbc37e034d6874a2af59eb04beaa52','Update other menu link procedure',NULL,NULL,NULL,NULL,NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101603,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101603,'m03','5ebbc37e034d6874a2af59eb04beaa52','Update other menu link procedure',NULL,NULL,NULL,NULL,NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101604,'m04','5ebbc37e034d6874a2af59eb04beaa52','Convert Menu procedure',NULL,NULL,NULL,NULL,NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101604,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101604,'m04','5ebbc37e034d6874a2af59eb04beaa52','Convert Menu procedure',NULL,NULL,NULL,NULL,NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160001,1,2100160001,2,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160001,1,2100160001,2,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160002,1,2100160002,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160002,1,2100160002,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160003,1,2100160003,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160003,1,2100160003,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160004,1,2100160004,2,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160004,1,2100160004,2,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160005,1,2100160005,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160005,1,2100160005,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160007,1,2100160007,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160007,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160007,1,2100160007,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160008,1,2100160008,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160008,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160008,1,2100160008,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160009,1,2100160009,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160009,1,2100160009,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160010,1,2100160010,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160010,1,2100160010,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160011,1,2100160011,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160011,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160011,1,2100160011,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160012,1,2100160012,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160012,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160012,1,2100160012,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100190001,1,2100190001,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160013,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100190001,1,2100190001,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100190002,1,2100190002,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160014,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100190002,1,2100190002,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100190003,1,2100190003,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160015,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100190003,1,2100190003,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160016,1,2100160016,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160016,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160016,1,2100160016,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160017,1,2100160017,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160017,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160017,1,2100160017,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101160001,2100000002,2100160001,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000018,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101160001,2100000002,2100160001,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101160002,2100000002,2100160002,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000019,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101160002,2100000002,2100160002,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160018,1,2100160018,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160018,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160018,1,2100160018,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('62','2100160016');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('63','2100160017');


INSERT INTO F_CM_STATUS_MASTER (STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Unexecuted',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Unexecuted',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER (STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Executing',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Executing',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER (STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Completed',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'Completed',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER (STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Completed(error)',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'Completed(error)',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_PARAM_PURPOSE (PURPOSE_ID,PURPOSE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'For Host',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_PARAM_PURPOSE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,PURPOSE_ID,PURPOSE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'For Host',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_PARAM_PURPOSE (PURPOSE_ID,PURPOSE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'For HostGroup',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_PARAM_PURPOSE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,PURPOSE_ID,PURPOSE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'For HostGroup',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'String',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'String',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Multi string',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Multi string',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Integer',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'Integer',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Decimal number',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'Decimal number',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'Date',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'Date',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'Date/time',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',6,'Date/time',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'Pulldown selection',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',7,'Pulldown selection',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'Password',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',8,'Password',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'File upload',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',9,'File upload',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'Link',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',10,'Link',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,'Parameter Sheet Reference',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',11,'Parameter Sheet Reference',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000001,2100000303,'Host name','C_STM_LIST','SYSTEM_ID','HOSTNAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000001,2100000303,'Host name','C_STM_LIST','SYSTEM_ID','HOSTNAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000002,2100000303,'IP address','C_STM_LIST','SYSTEM_ID','IP_ADDRESS',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000002,2100000303,'IP address','C_STM_LIST','SYSTEM_ID','IP_ADDRESS',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000005,2100000205,'Menu name','D_MENU_LIST','MENU_ID','MENU_PULLDOWN',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000005,2100000205,'Menu name','D_MENU_LIST','MENU_ID','MENU_PULLDOWN',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000006,2100000208,'Login ID','A_ACCOUNT_LIST','USER_ID','USERNAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000006,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000006,2100000208,'Login ID','A_ACCOUNT_LIST','USER_ID','USERNAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000007,2100000304,'Operation name','C_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000007,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000007,2100000304,'Operation name','C_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000008,2100000305,'Movement name','C_PATTERN_PER_ORCH','PATTERN_ID','PATTERN_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000008,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000008,2100000305,'Movement name','C_PATTERN_PER_ORCH','PATTERN_ID','PATTERN_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000009,2100000307,'Symphony name','C_SYMPHONY_CLASS_MNG','SYMPHONY_CLASS_NO','SYMPHONY_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000009,2100000307,'Symphony name','C_SYMPHONY_CLASS_MNG','SYMPHONY_CLASS_NO','SYMPHONY_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000010,2100180002,'Conductor name','C_CONDUCTOR_EDIT_CLASS_MNG','CONDUCTOR_CLASS_NO','CONDUCTOR_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000010,2100180002,'Conductor name','C_CONDUCTOR_EDIT_CLASS_MNG','CONDUCTOR_CLASS_NO','CONDUCTOR_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000012,2100160016,'*-(blank)','F_FLAG_MASTER','FLAG_ID','ASTBLANK_STATUS',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000012,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000012,2100160016,'*-(blank)','F_FLAG_MASTER','FLAG_ID','ASTBLANK_STATUS',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000013,2100160017,'True-False','F_FLAG_ALT_MASTER','FLAG_ID','TRUEFALSE_STATUS',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000013,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000013,2100160017,'True-False','F_FLAG_ALT_MASTER','FLAG_ID','TRUEFALSE_STATUS',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000014,2100160017,'Yes-No','F_FLAG_ALT_MASTER','FLAG_ID','YESNO_STATUS',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000014,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000014,2100160017,'Yes-No','F_FLAG_ALT_MASTER','FLAG_ID','YESNO_STATUS',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_MENU_REFERENCE_ITEM (ITEM_ID,LINK_ID,MENU_ID,DISP_SEQ,TABLE_NAME,PRI_NAME,COLUMN_NAME,ITEM_NAME,COL_GROUP_NAME,DESCRIPTION,INPUT_METHOD_ID,SENSITIVE_FLAG,ORIGINAL_MENU_FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000001,2000000005,2100000205,0,'D_MENU_LIST','MENU_ID','MENU_ID','Menu ID',NULL,NULL,1,1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_REFERENCE_ITEM_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,LINK_ID,MENU_ID,DISP_SEQ,TABLE_NAME,PRI_NAME,COLUMN_NAME,ITEM_NAME,COL_GROUP_NAME,DESCRIPTION,INPUT_METHOD_ID,SENSITIVE_FLAG,ORIGINAL_MENU_FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000001,2000000005,2100000205,0,'D_MENU_LIST','MENU_ID','MENU_ID','Menu ID',NULL,NULL,1,1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_REFERENCE_ITEM (ITEM_ID,LINK_ID,MENU_ID,DISP_SEQ,TABLE_NAME,PRI_NAME,COLUMN_NAME,ITEM_NAME,COL_GROUP_NAME,DESCRIPTION,INPUT_METHOD_ID,SENSITIVE_FLAG,ORIGINAL_MENU_FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000003,2000000006,2100000208,0,'A_ACCOUNT_LIST','USER_ID','MAIL_ADDRESS','Mail address',NULL,NULL,1,1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_REFERENCE_ITEM_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,LINK_ID,MENU_ID,DISP_SEQ,TABLE_NAME,PRI_NAME,COLUMN_NAME,ITEM_NAME,COL_GROUP_NAME,DESCRIPTION,INPUT_METHOD_ID,SENSITIVE_FLAG,ORIGINAL_MENU_FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000003,2000000006,2100000208,0,'A_ACCOUNT_LIST','USER_ID','MAIL_ADDRESS','Mail address',NULL,NULL,1,1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_REFERENCE_ITEM (ITEM_ID,LINK_ID,MENU_ID,DISP_SEQ,TABLE_NAME,PRI_NAME,COLUMN_NAME,ITEM_NAME,COL_GROUP_NAME,DESCRIPTION,INPUT_METHOD_ID,SENSITIVE_FLAG,ORIGINAL_MENU_FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000004,2000000001,2100000303,0,'C_STM_LIST','SYSTEM_ID','IP_ADDRESS','IP address',NULL,NULL,1,1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_REFERENCE_ITEM_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,LINK_ID,MENU_ID,DISP_SEQ,TABLE_NAME,PRI_NAME,COLUMN_NAME,ITEM_NAME,COL_GROUP_NAME,DESCRIPTION,INPUT_METHOD_ID,SENSITIVE_FLAG,ORIGINAL_MENU_FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000004,2000000001,2100000303,0,'C_STM_LIST','SYSTEM_ID','IP_ADDRESS','IP address',NULL,NULL,1,1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_REFERENCE_ITEM (ITEM_ID,LINK_ID,MENU_ID,DISP_SEQ,TABLE_NAME,PRI_NAME,COLUMN_NAME,ITEM_NAME,COL_GROUP_NAME,DESCRIPTION,INPUT_METHOD_ID,SENSITIVE_FLAG,ORIGINAL_MENU_FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000005,2000000001,2100000303,1,'C_STM_LIST','SYSTEM_ID','LOGIN_USER','Login user ID',NULL,NULL,1,1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_REFERENCE_ITEM_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,LINK_ID,MENU_ID,DISP_SEQ,TABLE_NAME,PRI_NAME,COLUMN_NAME,ITEM_NAME,COL_GROUP_NAME,DESCRIPTION,INPUT_METHOD_ID,SENSITIVE_FLAG,ORIGINAL_MENU_FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000005,2000000001,2100000303,1,'C_STM_LIST','SYSTEM_ID','LOGIN_USER','Login user ID',NULL,NULL,1,1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_REFERENCE_ITEM (ITEM_ID,LINK_ID,MENU_ID,DISP_SEQ,TABLE_NAME,PRI_NAME,COLUMN_NAME,ITEM_NAME,COL_GROUP_NAME,DESCRIPTION,INPUT_METHOD_ID,SENSITIVE_FLAG,ORIGINAL_MENU_FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000006,2000000001,2100000303,2,'C_STM_LIST','SYSTEM_ID','LOGIN_PW','Login password',NULL,NULL,8,2,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_REFERENCE_ITEM_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,LINK_ID,MENU_ID,DISP_SEQ,TABLE_NAME,PRI_NAME,COLUMN_NAME,ITEM_NAME,COL_GROUP_NAME,DESCRIPTION,INPUT_METHOD_ID,SENSITIVE_FLAG,ORIGINAL_MENU_FLAG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000006,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000006,2000000001,2100000303,2,'C_STM_LIST','SYSTEM_ID','LOGIN_PW','Login password',NULL,NULL,8,2,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_MENU_CREATE_STATUS (MENU_CREATE_STATUS,MENU_CREATE_STATUS_SELECT,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Not created',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_CREATE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_CREATE_STATUS,MENU_CREATE_STATUS_SELECT,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Not created',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_CREATE_STATUS (MENU_CREATE_STATUS,MENU_CREATE_STATUS_SELECT,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Created',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MENU_CREATE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_CREATE_STATUS,MENU_CREATE_STATUS_SELECT,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Created',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_FLAG_MASTER (FLAG_ID,ASTBLANK_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'*',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_FLAG_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FLAG_ID,ASTBLANK_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'*',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_FLAG_ALT_MASTER (FLAG_ID,YESNO_STATUS,TRUEFALSE_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Yes','True',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_FLAG_ALT_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FLAG_ID,YESNO_STATUS,TRUEFALSE_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Yes','True',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_FLAG_ALT_MASTER (FLAG_ID,YESNO_STATUS,TRUEFALSE_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'No','False',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_FLAG_ALT_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FLAG_ID,YESNO_STATUS,TRUEFALSE_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'No','False',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


COMMIT;
