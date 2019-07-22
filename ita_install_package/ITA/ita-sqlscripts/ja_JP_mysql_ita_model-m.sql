-- *****************************************************************************
-- *** ***** CreateParameterMenu Tables                                      ***
-- *****************************************************************************
-- -------------------------
-- パラメータシート作成情報
-- -------------------------
CREATE TABLE F_CREATE_MENU_INFO
(
CREATE_MENU_ID                      INT                             , -- 識別シーケンス項番
MENU_NAME                           VARCHAR (64)                    ,
PURPOSE                             INT                             ,
MENUGROUP_FOR_HG                    INT                             ,
MENUGROUP_FOR_H                     INT                             ,
MENUGROUP_FOR_VIEW                  INT                             ,
MENUGROUP_FOR_CONV                  INT                             ,
DISP_SEQ                            INT                             ,
DESCRIPTION                         VARCHAR (1024)                  ,
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
MENU_NAME                           VARCHAR (64)                    ,
PURPOSE                             INT                             ,
MENUGROUP_FOR_HG                    INT                             ,
MENUGROUP_FOR_H                     INT                             ,
MENUGROUP_FOR_VIEW                  INT                             ,
MENUGROUP_FOR_CONV                  INT                             ,
DISP_SEQ                            INT                             ,
DESCRIPTION                         VARCHAR (1024)                  ,
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
PREG_MATCH                          VARCHAR (1024)                  ,
OTHER_MENU_LINK_ID                  INT                             ,
DESCRIPTION                         VARCHAR (1024)                  ,
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
PREG_MATCH                          VARCHAR (1024)                  ,
OTHER_MENU_LINK_ID                  INT                             ,
DESCRIPTION                         VARCHAR (1024)                  ,
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
FILE_NAME                           VARCHAR (64)                    ,
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
FILE_NAME                           VARCHAR (64)                    ,
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
COLUMN_DISP_NAME                    VARCHAR (256)                   ,
TABLE_NAME                          VARCHAR (64)                    ,
PRI_NAME                            VARCHAR (64)                    ,
COLUMN_NAME                         VARCHAR (64)                    ,
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
COLUMN_DISP_NAME                    VARCHAR (256)                   ,
TABLE_NAME                          VARCHAR (64)                    ,
PRI_NAME                            VARCHAR (64)                    ,
COLUMN_NAME                         VARCHAR (64)                    ,
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
NOTE                          VARCHAR  (4000)   ,
DISUSE_FLAG                   VARCHAR  (1)      ,
LAST_UPDATE_TIMESTAMP         DATETIME(6)       ,
LAST_UPDATE_USER              INT               ,
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- マスタ作成情報
-- -------------------------
CREATE TABLE F_CREATE_MST_MENU_INFO
(
CREATE_MENU_ID                      INT                             , -- 識別シーケンス項番
MENU_NAME                           VARCHAR (64)                    ,
MENUGROUP_FOR_MST                   INT                             ,
DISP_SEQ                            INT                             ,
DESCRIPTION                         VARCHAR (1024)                  ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (CREATE_MENU_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_CREATE_MST_MENU_INFO_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR (8)                     , -- 履歴用変更種別

CREATE_MENU_ID                      INT                             , -- 識別シーケンス項番
MENU_NAME                           VARCHAR (64)                    ,
MENUGROUP_FOR_MST                   INT                             ,
DISP_SEQ                            INT                             ,
DESCRIPTION                         VARCHAR (1024)                  ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- マスタ項目作成情報
-- -------------------------
CREATE TABLE F_CREATE_MST_ITEM_INFO
(
CREATE_ITEM_ID                      INT                             , -- 識別シーケンス項番
CREATE_MENU_ID                      INT                             ,
ITEM_NAME                           VARCHAR (256)                   ,
DISP_SEQ                            INT                             ,
MAX_LENGTH                          INT                             ,
PREG_MATCH                          VARCHAR (1024)                  ,
DESCRIPTION                         VARCHAR (1024)                  ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (CREATE_ITEM_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_CREATE_MST_ITEM_INFO_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

CREATE_ITEM_ID                      INT                             , -- 識別シーケンス項番
CREATE_MENU_ID                      INT                             ,
ITEM_NAME                           VARCHAR (256)                   ,
DISP_SEQ                            INT                             ,
MAX_LENGTH                          INT                             ,
PREG_MATCH                          VARCHAR (1024)                  ,
DESCRIPTION                         VARCHAR (1024)                  ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- マスタ・テーブル紐付
-- -------------------------
CREATE TABLE F_MST_MENU_TABLE_LINK
(
MENU_TABLE_LINK_ID                  INT                             , -- 識別シーケンス項番
CREATE_MENU_ID                      INT                             ,
TABLE_NAME_MST                      VARCHAR (64)                    ,
TABLE_NAME_MST_JNL                  VARCHAR (64)                    ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ

PRIMARY KEY (MENU_TABLE_LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_MST_MENU_TABLE_LINK_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

MENU_TABLE_LINK_ID                  INT                             , -- 識別シーケンス項番
CREATE_MENU_ID                      INT                             ,
TABLE_NAME_MST                      VARCHAR (64)                    ,
TABLE_NAME_MST_JNL                  VARCHAR (64)                    ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- テーブル項目名一覧（マスタ作成）
-- -------------------------
CREATE TABLE F_MST_TABLE_ITEM_LIST
(
TABLE_ITEM_ID                       INT                             , -- 識別シーケンス項番
CREATE_MENU_ID                      INT                             ,
CREATE_ITEM_ID                      INT                             ,
COLUMN_NAME                         VARCHAR (64)                    ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (TABLE_ITEM_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_MST_TABLE_ITEM_LIST_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

TABLE_ITEM_ID                       INT                             , -- 識別シーケンス項番
CREATE_MENU_ID                      INT                             ,
CREATE_ITEM_ID                      INT                             ,
COLUMN_NAME                         VARCHAR (64)                    ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- マスタ作成管理
-- -------------------------
CREATE TABLE F_CREATE_MST_MENU_STATUS
(
MM_STATUS_ID                        INT                             , -- 識別シーケンス項番

CREATE_MENU_ID                      INT                             ,
STATUS_ID                           INT                             ,
FILE_NAME                           VARCHAR (64)                    ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY (MM_STATUS_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_CREATE_MST_MENU_STATUS_JNL
(
JOURNAL_SEQ_NO                      INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                VARCHAR  (8)                    , -- 履歴用変更種別

MM_STATUS_ID                        INT                             , -- 識別シーケンス項番
CREATE_MENU_ID                      INT                             ,
STATUS_ID                           INT                             ,
FILE_NAME                           VARCHAR (64)                    ,
NOTE                                VARCHAR  (4000)                 , -- 備考
DISUSE_FLAG                         VARCHAR  (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                    INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
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
       '0'    AS DISUSE_FLAG            ,
       NOW(6) AS LAST_UPDATE_TIMESTAMP  ,
       1      AS LAST_UPDATE_USER
;

-- -------------------------
-- 他メニュー連携
-- -------------------------
CREATE VIEW G_OTHER_MENU_LINK AS 
SELECT TAB_A.LINK_ID,
       TAB_C.MENU_GROUP_ID,
       TAB_C.MENU_GROUP_NAME,
       TAB_A.MENU_ID,
       TAB_A.MENU_ID MENU_ID_CLONE,
       TAB_B.MENU_NAME,
       TAB_A.COLUMN_DISP_NAME,
       CONCAT(TAB_C.MENU_GROUP_NAME,':',TAB_B.MENU_NAME,':',TAB_A.COLUMN_DISP_NAME) LINK_PULLDOWN,
       TAB_A.TABLE_NAME,
       TAB_A.PRI_NAME,
       TAB_A.COLUMN_NAME,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM F_OTHER_MENU_LINK TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
WHERE TAB_B.DISUSE_FLAG='0' AND TAB_C.DISUSE_FLAG='0'
;

CREATE VIEW G_OTHER_MENU_LINK_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO,
       TAB_A.JOURNAL_REG_DATETIME,
       TAB_A.JOURNAL_ACTION_CLASS,
       TAB_A.LINK_ID,
       TAB_C.MENU_GROUP_ID,
       TAB_C.MENU_GROUP_NAME,
       TAB_A.MENU_ID,
       TAB_A.MENU_ID MENU_ID_CLONE,
       TAB_B.MENU_NAME,
       TAB_A.COLUMN_DISP_NAME,
       CONCAT(TAB_C.MENU_GROUP_NAME,':',TAB_B.MENU_NAME,':',TAB_A.COLUMN_DISP_NAME) LINK_PULLDOWN,
       TAB_A.TABLE_NAME,
       TAB_A.PRI_NAME,
       TAB_A.COLUMN_NAME,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
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
       TAB_A.PREG_MATCH,
       TAB_A.OTHER_MENU_LINK_ID,
       TAB_A.DESCRIPTION,
       TAB_C.FULL_COL_GROUP_NAME,
       CASE
           WHEN TAB_C.FULL_COL_GROUP_NAME IS NULL THEN CONCAT(TAB_B.MENU_NAME,':',TAB_A.ITEM_NAME)
           ELSE CONCAT(TAB_B.MENU_NAME,':',TAB_C.FULL_COL_GROUP_NAME,'/',TAB_A.ITEM_NAME)
       END LINK_PULLDOWN,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM F_CREATE_ITEM_INFO TAB_A
LEFT JOIN F_CREATE_MENU_INFO TAB_B ON (TAB_A.CREATE_MENU_ID = TAB_B.CREATE_MENU_ID)
LEFT JOIN F_COLUMN_GROUP TAB_C ON (TAB_A.COL_GROUP_ID = TAB_C.COL_GROUP_ID)
WHERE TAB_B.MENUGROUP_FOR_CONV != ""
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
       TAB_A.PREG_MATCH,
       TAB_A.OTHER_MENU_LINK_ID,
       TAB_A.DESCRIPTION,
       CASE
           WHEN TAB_C.FULL_COL_GROUP_NAME IS NULL THEN CONCAT(TAB_B.MENU_NAME,':',TAB_A.ITEM_NAME)
           ELSE CONCAT(TAB_B.MENU_NAME,':',TAB_C.FULL_COL_GROUP_NAME,'/',TAB_A.ITEM_NAME)
       END LINK_PULLDOWN,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM F_CREATE_ITEM_INFO_JNL TAB_A
LEFT JOIN F_CREATE_MENU_INFO TAB_B ON (TAB_A.CREATE_MENU_ID = TAB_B.CREATE_MENU_ID)
LEFT JOIN F_COLUMN_GROUP TAB_C ON (TAB_A.COL_GROUP_ID = TAB_C.COL_GROUP_ID)
WHERE TAB_B.MENUGROUP_FOR_CONV != ""
;
INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_MENU_INFO_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_MENU_INFO_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_ITEM_INFO_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_ITEM_INFO_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_MENU_TABLE_LINK_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_MENU_TABLE_LINK_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_MENU_STATUS_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_MENU_STATUS_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CM_STATUS_MASTER_RIC',5);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CM_STATUS_MASTER_JSQ',5);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_PARAM_PURPOSE_RIC',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_PARAM_PURPOSE_JSQ',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_OTHER_MENU_LINK_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_OTHER_MENU_LINK_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_INPUT_METHOD_RIC',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_INPUT_METHOD_JSQ',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_COLUMN_GROUP_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_COLUMN_GROUP_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CONVERT_PARAM_INFO_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CONVERT_PARAM_INFO_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_COL_TO_ROW_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_COL_TO_ROW_MNG_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_MST_MENU_INFO_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_MST_MENU_INFO_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_MST_ITEM_INFO_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_MST_ITEM_INFO_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_MST_MENU_TABLE_LINK_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_MST_MENU_TABLE_LINK_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_MST_TABLE_ITEM_LIST_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_MST_TABLE_ITEM_LIST_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_MST_MENU_STATUS_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_CREATE_MST_MENU_STATUS_JSQ',1);


INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011602,'マスタ作成','master.png',50,'マスタ作成','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011602,'マスタ作成','master.png',50,'マスタ作成','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011601,'パラメータシート作成','sheet.png',51,'パラメータシート作成','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011601,'パラメータシート作成','sheet.png',51,'パラメータシート作成','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011609,'パラメータシート(縦)ホスト分解',NULL,59,'パラメータシート(縦)ホスト分解','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011609,'パラメータシート(縦)ホスト分解',NULL,59,'パラメータシート(縦)ホスト分解','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160001,2100011601,'パラメータシート作成情報',NULL,NULL,NULL,1,0,1,2,1,'create_menu_info','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160001,2100011601,'パラメータシート作成情報',NULL,NULL,NULL,1,0,1,2,1,'create_menu_info','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160002,2100011601,'パラメータシート項目作成情報',NULL,NULL,NULL,1,0,1,2,3,'create_item_info','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160002,2100011601,'パラメータシート項目作成情報',NULL,NULL,NULL,1,0,1,2,3,'create_item_info','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160003,2100011601,'パラメータシート作成実行',NULL,NULL,NULL,1,0,2,2,5,'create_menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160003,2100011601,'パラメータシート作成実行',NULL,NULL,NULL,1,0,2,2,5,'create_menu','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160004,2100011601,'パラメータシート作成管理',NULL,NULL,NULL,1,0,1,2,6,'create_menu_status','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160004,2100011601,'パラメータシート作成管理',NULL,NULL,NULL,1,0,1,2,6,'create_menu_status','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160005,2100011601,'パラメータシート・テーブル紐付',NULL,NULL,NULL,1,0,1,2,101,'menu_table_link','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160005,2100011601,'パラメータシート・テーブル紐付',NULL,NULL,NULL,1,0,1,2,101,'menu_table_link','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160007,2100011601,'他メニュー連携',NULL,NULL,NULL,1,0,1,2,103,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160007,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160007,2100011601,'他メニュー連携',NULL,NULL,NULL,1,0,1,2,103,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160008,2100011601,'カラムグループ管理',NULL,NULL,NULL,1,0,1,2,2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160008,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160008,2100011601,'カラムグループ管理',NULL,NULL,NULL,1,0,1,2,2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160009,2100011601,'パラメータシート(縦)作成情報',NULL,NULL,NULL,1,0,1,2,4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160009,2100011601,'パラメータシート(縦)作成情報',NULL,NULL,NULL,1,0,1,2,4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160010,2100011601,'パラメータシート縦横変換管理',NULL,NULL,NULL,1,0,1,2,104,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160010,2100011601,'パラメータシート縦横変換管理',NULL,NULL,NULL,1,0,1,2,104,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160101,2100011602,'マスタ作成情報',NULL,NULL,NULL,1,0,1,2,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160101,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160101,2100011602,'マスタ作成情報',NULL,NULL,NULL,1,0,1,2,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160102,2100011602,'マスタ項目作成情報',NULL,NULL,NULL,1,0,1,2,2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160102,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160102,2100011602,'マスタ項目作成情報',NULL,NULL,NULL,1,0,1,2,2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160103,2100011602,'マスタ作成実行',NULL,NULL,NULL,1,0,2,2,3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160103,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160103,2100011602,'マスタ作成実行',NULL,NULL,NULL,1,0,2,2,3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160104,2100011602,'マスタ作成管理',NULL,NULL,NULL,1,0,1,2,4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160104,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160104,2100011602,'マスタ作成管理',NULL,NULL,NULL,1,0,1,2,4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160105,2100011602,'マスタ・テーブル紐付',NULL,NULL,NULL,1,0,1,2,5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160105,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160105,2100011602,'マスタ・テーブル紐付',NULL,NULL,NULL,1,0,1,2,5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160106,2100011602,'テーブル項目名一覧',NULL,NULL,NULL,1,0,1,2,6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160106,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160106,2100011602,'テーブル項目名一覧',NULL,NULL,NULL,1,0,1,2,6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101601,'m01','5ebbc37e034d6874a2af59eb04beaa52','パラメータシート作成機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101601,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101601,'m01','5ebbc37e034d6874a2af59eb04beaa52','パラメータシート作成機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101602,'m02','5ebbc37e034d6874a2af59eb04beaa52','マスタ作成機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101602,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101602,'m02','5ebbc37e034d6874a2af59eb04beaa52','マスタ作成機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101603,'m03','5ebbc37e034d6874a2af59eb04beaa52','他メニュー連携メニュー更新機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101603,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101603,'m03','5ebbc37e034d6874a2af59eb04beaa52','他メニュー連携メニュー更新機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101604,'m04','5ebbc37e034d6874a2af59eb04beaa52','パラメータシート変換機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101604,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101604,'m04','5ebbc37e034d6874a2af59eb04beaa52','パラメータシート変換機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160001,1,2100160001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160001,1,2100160001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160002,1,2100160002,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160002,1,2100160002,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160003,1,2100160003,2,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160003,1,2100160003,2,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160004,1,2100160004,2,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160004,1,2100160004,2,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160005,1,2100160005,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160005,1,2100160005,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160007,1,2100160007,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160007,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160007,1,2100160007,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160008,1,2100160008,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160008,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160008,1,2100160008,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160009,1,2100160009,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160009,1,2100160009,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160010,1,2100160010,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160010,1,2100160010,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160101,1,2100160101,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160101,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160101,1,2100160101,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160102,1,2100160102,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160102,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160102,1,2100160102,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160103,1,2100160103,2,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160103,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160103,1,2100160103,2,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160104,1,2100160104,2,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160104,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160104,1,2100160104,2,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160105,1,2100160105,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160105,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160105,1,2100160105,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100160106,1,2100160106,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160106,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100160106,1,2100160106,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_CM_STATUS_MASTER (STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'未実行',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'未実行',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER (STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'実行中',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'実行中',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER (STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'完了',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'完了',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER (STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'完了(異常)',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_CM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'完了(異常)',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_PARAM_PURPOSE (PURPOSE_ID,PURPOSE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'ホスト用',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_PARAM_PURPOSE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,PURPOSE_ID,PURPOSE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'ホスト用',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_PARAM_PURPOSE (PURPOSE_ID,PURPOSE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'ホストグループ用',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_PARAM_PURPOSE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,PURPOSE_ID,PURPOSE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'ホストグループ用',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'文字列',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'文字列',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD (INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'他メニュー参照',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_INPUT_METHOD_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,INPUT_METHOD_ID,INPUT_METHOD_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'他メニュー参照',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000001,2100000303,'ホスト名','C_STM_LIST','SYSTEM_ID','HOSTNAME',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000001,2100000303,'ホスト名','C_STM_LIST','SYSTEM_ID','HOSTNAME',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000002,2100000303,'IPアドレス','C_STM_LIST','SYSTEM_ID','IP_ADDRESS',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000002,2100000303,'IPアドレス','C_STM_LIST','SYSTEM_ID','IP_ADDRESS',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


COMMIT;
