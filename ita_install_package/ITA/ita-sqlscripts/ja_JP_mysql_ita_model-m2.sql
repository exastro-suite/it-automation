
-- -------------------------------------------------------
--　収集機能パースタイプマスタ
-- -------------------------------------------------------
CREATE TABLE B_PARSE_TYPE_MASTER
(
PARSE_TYPE_ID                     INT                               ,
PARSE_TYPE_NAME                   VARCHAR (64)                      ,
DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (PARSE_TYPE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_PARSE_TYPE_MASTER_JNL
(
JOURNAL_SEQ_NO                    INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                       , -- 履歴用変更種別

PARSE_TYPE_ID                     INT                               ,
PARSE_TYPE_NAME                   VARCHAR (64)                      ,
DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


-- -------------------------------------------------------
-- 収集機能ステータスマスタ
-- -------------------------------------------------------
CREATE TABLE B_COLLECT_STATUS
(
COLLECT_STATUS_ID                 INT                               ,
COLLECT_STATUS_NAME               VARCHAR (64)                      ,
DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (COLLECT_STATUS_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_COLLECT_STATUS_JNL
(
JOURNAL_SEQ_NO                    INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                       , -- 履歴用変更種別

COLLECT_STATUS_ID                 INT                               ,
COLLECT_STATUS_NAME               VARCHAR (64)                      ,
DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


-- -------------------------------------------------------
-- Ansible共通　収集インターフェース
-- -------------------------------------------------------

CREATE TABLE C_COLLECT_IF_INFO
(
COLLECT_IF_INFO_ID                INT                               ,
HOSTNAME                          VARCHAR (128)                     ,
IP_ADDRESS                        VARCHAR (15)                      ,
HOST_DESIGNATE_TYPE_ID            INT                               ,
PROTOCOL                          VARCHAR (8)                       ,
PORT                              INT                               ,
LOGIN_USER                        VARCHAR (30)                      ,
LOGIN_PW                          VARCHAR (60)                      ,
LOGIN_PW_ANSIBLE_VAULT            VARCHAR (512)                     ,

DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (COLLECT_IF_INFO_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE C_COLLECT_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                       , -- 履歴用変更種別

COLLECT_IF_INFO_ID                INT                               ,
HOSTNAME                          VARCHAR (128)                     ,
IP_ADDRESS                        VARCHAR (15)                      ,
HOST_DESIGNATE_TYPE_ID            INT                               ,
PROTOCOL                          VARCHAR (8)                       ,
PORT                              INT                               ,
LOGIN_USER                        VARCHAR (30)                      ,
LOGIN_PW                          VARCHAR (60)                      ,
LOGIN_PW_ANSIBLE_VAULT            VARCHAR (512)                     ,

DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------------------------------------
-- Ansible共通　収集項目値管理
-- -------------------------------------------------------
CREATE TABLE B_ANS_CMDB_LINK
(
COLUMN_ID                         INT                               ,
MENU_ID                           INT                               ,
COLUMN_LIST_ID                    INT                               ,
FILE_PREFIX                       VARCHAR (256)                     ,
VARS_NAME                         VARCHAR (256)                     ,
VRAS_MEMBER_NAME                  VARCHAR (256)                     ,
PARSE_TYPE_ID                     INT                               ,

DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (COLUMN_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_ANS_CMDB_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                       , -- 履歴用変更種別

COLUMN_ID                         INT                               ,
MENU_ID                           INT                               ,
COLUMN_LIST_ID                    INT                               ,
FILE_PREFIX                       VARCHAR (256)                     ,
VARS_NAME                         VARCHAR (256)                     ,
VRAS_MEMBER_NAME                  VARCHAR (256)                     ,
PARSE_TYPE_ID                     INT                               ,

DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- *****************************************************************************
-- *** Ansible共通　収集項目値管理VIEW                                         ***
-- *****************************************************************************

CREATE VIEW D_ANS_CMDB_LINK AS 
SELECT DISTINCT
  TAB_A.* ,
  TAB_B.MENU_GROUP_ID,
  TAB_B.MENU_GROUP_ID AS MENU_GROUP_ID_CLONE, 
  TAB_C.MENU_GROUP_NAME ,
  TAB_A.MENU_ID AS MENU_ID_CLONE,
  TAB_A.MENU_ID AS MENU_ID_CLONE_02,
  TAB_B.MENU_NAME,
  TAB_A.COLUMN_LIST_ID AS REST_COLUMN_LIST_ID,
  TAB_D.COL_NAME,
  TAB_D.COL_TITLE,
  TAB_D.COL_CLASS,
  TAB_E.TABLE_NAME
FROM (
        (
          B_ANS_CMDB_LINK TAB_A 
          LEFT JOIN A_MENU_LIST TAB_B ON(TAB_A.MENU_ID = TAB_B.MENU_ID)
        ) 
        LEFT JOIN A_MENU_GROUP_LIST TAB_C ON(TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
        LEFT JOIN B_CMDB_MENU_COLUMN TAB_D ON(TAB_A.COLUMN_LIST_ID = TAB_D.COLUMN_LIST_ID)
        LEFT JOIN F_MENU_TABLE_LINK TAB_E ON(TAB_A.MENU_ID = TAB_E.MENU_ID)
    )
;

CREATE VIEW D_ANS_CMDB_LINK_JNL AS 
SELECT DISTINCT
  TAB_A.* ,
  TAB_B.MENU_GROUP_ID,
  TAB_B.MENU_GROUP_ID AS MENU_GROUP_ID_CLONE, 
  TAB_C.MENU_GROUP_NAME ,
  TAB_A.MENU_ID AS MENU_ID_CLONE,
  TAB_A.MENU_ID AS MENU_ID_CLONE_02,
  TAB_B.MENU_NAME,
  TAB_A.COLUMN_LIST_ID AS REST_COLUMN_LIST_ID,
  TAB_D.COL_NAME,
  TAB_D.COL_TITLE,
  TAB_D.COL_CLASS,
  TAB_E.TABLE_NAME
FROM (
        (
          B_ANS_CMDB_LINK_JNL TAB_A 
          LEFT JOIN A_MENU_LIST TAB_B ON(TAB_A.MENU_ID = TAB_B.MENU_ID)
        ) 
        LEFT JOIN A_MENU_GROUP_LIST TAB_C ON(TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
        LEFT JOIN B_CMDB_MENU_COLUMN TAB_D ON(TAB_A.COLUMN_LIST_ID = TAB_D.COLUMN_LIST_ID)
        LEFT JOIN F_MENU_TABLE_LINK TAB_E ON(TAB_A.MENU_ID = TAB_E.MENU_ID)
    )
;

-- -------------------------------------------------------
-- ***  Ansible共通　収集項目値管理の「メニューグループ:メニュー:項目」SHEET_TYPE=4用
-- -------------------------------------------------------
CREATE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_4 AS
SELECT
  TAB_B.*,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01   ,
  TAB_A.ACCESS_AUTH_01 AS ACCESS_AUTH_02,
  TAB_A.ACCESS_AUTH_02 AS ACCESS_AUTH_03
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_1         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.DISUSE_FLAG = '0'
;

CREATE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_4_JNL AS
SELECT
  TAB_B.*,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01   ,
  TAB_A.ACCESS_AUTH_01 AS ACCESS_AUTH_02,
  TAB_A.ACCESS_AUTH_02 AS ACCESS_AUTH_03
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_1_JNL         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN_JNL TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.DISUSE_FLAG = '0'
;

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_COLLECT_IF_INFO_RIC',2,'2100040709',2100620001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_COLLECT_IF_INFO_JSQ',2,'2100040709',2100620002,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_CMDB_LINK_RIC',1,'2100040710',2100620003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_CMDB_LINK_JSQ',1,'2100040710',2100620004,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_COLLECT_STATUS_RIC',4,NULL,2100690007,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_COLLECT_STATUS_JSQ',4,NULL,2100690008,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_PARSE_TYPE_MASTER_RIC',2,NULL,2100690009,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_PARSE_TYPE_MASTER_JSQ',2,NULL,2100690010,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));


INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040709,2100020000,'収集インターフェース情報',NULL,NULL,NULL,1,0,1,2,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-190001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040709,2100020000,'収集インターフェース情報',NULL,NULL,NULL,1,0,1,2,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040710,2100020000,'収集項目値管理',NULL,NULL,NULL,1,0,1,2,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-190002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040710,2100020000,'収集項目値管理',NULL,NULL,NULL,1,0,1,2,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100021,'a8a','5ebbc37e034d6874a2af59eb04beaa52','収集作業プロシージャ',NULL,NULL,NULL,NULL,'収集作業プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100021,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100021,'a8a','5ebbc37e034d6874a2af59eb04beaa52','収集作業プロシージャ',NULL,NULL,NULL,NULL,'収集作業プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040709,1,2100040709,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-190001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040709,1,2100040709,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040710,1,2100040710,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-190002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040710,1,2100040710,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000003,2100040703,'ファイル埋込変数名','B_ANS_CONTENTS_FILE','CONTENTS_FILE_ID','CONTENTS_FILE_VARS_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000003,2100040703,'ファイル埋込変数名','B_ANS_CONTENTS_FILE','CONTENTS_FILE_ID','CONTENTS_FILE_VARS_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK (LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000004,2100040704,'テンプレート埋込変数名','B_ANS_TEMPLATE_FILE','ANS_TEMPLATE_ID','ANS_TEMPLATE_VARS_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_OTHER_MENU_LINK_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,MENU_ID,COLUMN_DISP_NAME,TABLE_NAME,PRI_NAME,COLUMN_NAME,COLUMN_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2000000004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2000000004,2100040704,'テンプレート埋込変数名','B_ANS_TEMPLATE_FILE','ANS_TEMPLATE_ID','ANS_TEMPLATE_VARS_NAME',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_PARSE_TYPE_MASTER (PARSE_TYPE_ID,PARSE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'YAML','1',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_PARSE_TYPE_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,PARSE_TYPE_ID,PARSE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'YAML','1',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_COLLECT_STATUS (COLLECT_STATUS_ID,COLLECT_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'収集済み',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_COLLECT_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,COLLECT_STATUS_ID,COLLECT_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'収集済み',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_COLLECT_STATUS (COLLECT_STATUS_ID,COLLECT_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'収集済み（通知あり）',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_COLLECT_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,COLLECT_STATUS_ID,COLLECT_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'収集済み（通知あり）',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_COLLECT_STATUS (COLLECT_STATUS_ID,COLLECT_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'対象外',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_COLLECT_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,COLLECT_STATUS_ID,COLLECT_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'対象外',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_COLLECT_STATUS (COLLECT_STATUS_ID,COLLECT_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'収集エラー',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_COLLECT_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,COLLECT_STATUS_ID,COLLECT_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'収集エラー',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO C_COLLECT_IF_INFO (COLLECT_IF_INFO_ID,HOSTNAME,IP_ADDRESS,HOST_DESIGNATE_TYPE_ID,PROTOCOL,PORT,LOGIN_USER,LOGIN_PW,LOGIN_PW_ANSIBLE_VAULT,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'localhost','127.0.0.1',1,'http',80,NULL,NULL,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO C_COLLECT_IF_INFO_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,COLLECT_IF_INFO_ID,HOSTNAME,IP_ADDRESS,HOST_DESIGNATE_TYPE_ID,PROTOCOL,PORT,LOGIN_USER,LOGIN_PW,LOGIN_PW_ANSIBLE_VAULT,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'localhost','127.0.0.1',1,'http',80,NULL,NULL,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


COMMIT;
