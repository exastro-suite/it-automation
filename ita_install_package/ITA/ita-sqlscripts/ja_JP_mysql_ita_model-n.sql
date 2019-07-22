
-- *****************************************************************************
-- *** ***** CreateParameterMenu Tables                                      ***
-- *****************************************************************************
CREATE TABLE F_HOSTGROUP_LIST
(
ROW_ID                             INT                             , -- 識別シーケンスホストグループID

HOSTGROUP_NAME                     VARCHAR (128)                    ,
STRENGTH                           INT                             ,

DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_HOSTGROUP_LIST_JNL
(
JOURNAL_SEQ_NO                     INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR (8)                     , -- 履歴用変更種別

ROW_ID                             INT                             , -- 識別シーケンスホストグループID

HOSTGROUP_NAME                     VARCHAR (128)                    ,
STRENGTH                           INT                             ,

DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_HOST_LINK_LIST
(
ROW_ID                             INT                             , -- 識別シーケンス項番

LOOPALARM                          INT                             ,
PA_HOSTGROUP                       INT                             ,
CH_HOSTGROUP                       INT                             ,

DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_HOST_LINK_LIST_JNL
(
JOURNAL_SEQ_NO                     INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR (8)                     , -- 履歴用変更種別

ROW_ID                             INT                             , -- 識別シーケンス項番

LOOPALARM                          INT                             ,
PA_HOSTGROUP                       INT                             ,
CH_HOSTGROUP                       INT                             ,

DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_HOST_LINK
(
ROW_ID                             INT                             , -- 識別シーケンス項番

HOSTGROUP_NAME                     INT                             ,
OPERATION_ID                       INT                             ,
HOSTNAME                           INT                             ,

DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_HOST_LINK_JNL
(
JOURNAL_SEQ_NO                     INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR (8)                     , -- 履歴用変更種別

ROW_ID                             INT                             , -- 識別シーケンス項番

HOSTGROUP_NAME                     INT                             ,
OPERATION_ID                       INT                             ,
HOSTNAME                           INT                             ,

DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_SPLIT_TARGET
(
ROW_ID                             INT                             , -- 識別シーケンス項番

INPUT_MENU_ID                      INT                             ,
OUTPUT_MENU_ID                     INT                             ,
DIVIDED_FLG                        VARCHAR  (1)                    ,

NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_SPLIT_TARGET_JNL
(
JOURNAL_SEQ_NO                     INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR (8)                     , -- 履歴用変更種別

ROW_ID                             INT                             , -- 識別シーケンス項番

INPUT_MENU_ID                      INT                             ,
OUTPUT_MENU_ID                     INT                             ,
DIVIDED_FLG                        VARCHAR  (1)                    ,

NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;



-- *****************************************************************************
-- *** ***** Views                                                           ***
-- *****************************************************************************

CREATE VIEW G_SPLIT_TARGET AS 
SELECT TAB_A.ROW_ID                 ,
       TAB_B.MENU_GROUP_ID          INPUT_MENU_GROUP_ID,
       TAB_C.MENU_GROUP_NAME        INPUT_MENU_GROUP_NAME,
       TAB_A.INPUT_MENU_ID          ,
       TAB_B.MENU_NAME              INPUT_MENU_NAME,
       TAB_A.INPUT_MENU_ID          INPUT_MENU_ID_CLONE,
       TAB_D.MENU_GROUP_ID          OUTPUT_MENU_GROUP_ID,
       TAB_E.MENU_GROUP_NAME        OUTPUT_MENU_GROUP_NAME,
       TAB_A.OUTPUT_MENU_ID         ,
       TAB_D.MENU_NAME              OUTPUT_MENU_NAME,
       TAB_A.OUTPUT_MENU_ID         OUTPUT_MENU_ID_CLONE,
       TAB_A.DIVIDED_FLG,
       TAB_A.NOTE                   ,
       TAB_A.DISUSE_FLAG            ,
       TAB_A.LAST_UPDATE_TIMESTAMP  ,
       TAB_A.LAST_UPDATE_USER 
FROM F_SPLIT_TARGET TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.INPUT_MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
LEFT JOIN A_MENU_LIST TAB_D ON (TAB_A.OUTPUT_MENU_ID = TAB_D.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_E ON (TAB_D.MENU_GROUP_ID = TAB_E.MENU_GROUP_ID)
;

CREATE VIEW G_SPLIT_TARGET_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO         ,
       TAB_A.JOURNAL_REG_DATETIME   ,
       TAB_A.JOURNAL_ACTION_CLASS   ,
       TAB_A.ROW_ID                 ,
       TAB_B.MENU_GROUP_ID          INPUT_MENU_GROUP_ID,
       TAB_C.MENU_GROUP_NAME        INPUT_MENU_GROUP_NAME,
       TAB_A.INPUT_MENU_ID          ,
       TAB_B.MENU_NAME              INPUT_MENU_NAME,
       TAB_A.INPUT_MENU_ID          INPUT_MENU_ID_CLONE,
       TAB_D.MENU_GROUP_ID          OUTPUT_MENU_GROUP_ID,
       TAB_E.MENU_GROUP_NAME        OUTPUT_MENU_GROUP_NAME,
       TAB_A.OUTPUT_MENU_ID         ,
       TAB_D.MENU_NAME              OUTPUT_MENU_NAME,
       TAB_A.OUTPUT_MENU_ID         OUTPUT_MENU_ID_CLONE,
       TAB_A.DIVIDED_FLG,
       TAB_A.NOTE                   ,
       TAB_A.DISUSE_FLAG            ,
       TAB_A.LAST_UPDATE_TIMESTAMP  ,
       TAB_A.LAST_UPDATE_USER 
FROM F_SPLIT_TARGET_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.INPUT_MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
LEFT JOIN A_MENU_LIST TAB_D ON (TAB_A.OUTPUT_MENU_ID = TAB_D.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_E ON (TAB_D.MENU_GROUP_ID = TAB_E.MENU_GROUP_ID)
;

CREATE VIEW G_UQ_HOST_LIST AS
SELECT SYSTEM_ID                                                    AS KY_KEY   ,
       CONCAT('[H]',HOSTNAME) AS KY_VALUE ,
       0                                                            AS KY_SOURCE,
       9223372036854775807                                          AS STRENGTH ,
       DISUSE_FLAG                                                              ,
       LAST_UPDATE_TIMESTAMP                                                    ,
       LAST_UPDATE_USER
FROM   C_STM_LIST
WHERE  DISUSE_FLAG = '0'
UNION
SELECT ROW_ID + 10000                                                       AS KY_KEY   ,
       CONCAT('[HG]',HOSTGROUP_NAME)  AS KY_VALUE ,
       1                                                                    AS KY_SOURCE,
       STRENGTH                                                             AS STRENGTH ,
       DISUSE_FLAG                                                                      ,
       LAST_UPDATE_TIMESTAMP                                                            ,
       LAST_UPDATE_USER
FROM   F_HOSTGROUP_LIST
WHERE  DISUSE_FLAG = '0'
;

CREATE VIEW G_FLAG_MASTER AS
SELECT 1      AS FLAG_ID                ,
       '●'   AS FLAG_NAME              ,
       NULL   AS NOTE                   ,
       '0'    AS DISUSE_FLAG            ,
       NOW(6) AS LAST_UPDATE_TIMESTAMP  ,
       1      AS LAST_UPDATE_USER
;

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_HOSTGROUP_LIST_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_HOSTGROUP_LIST_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_HOST_LINK_LIST_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_HOST_LINK_LIST_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_HOST_LINK_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_HOST_LINK_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_SPLIT_TARGET_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_SPLIT_TARGET_JSQ',1);


INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011701,'ホストグループ管理','host_group.png',60,'ホストグループ管理','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-170001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011701,'ホストグループ管理','host_group.png',60,'ホストグループ管理','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100170001,2100011701,'ホストグループ一覧',NULL,NULL,NULL,1,0,1,2,1,'hostgroup_list','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-170001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100170001,2100011701,'ホストグループ一覧',NULL,NULL,NULL,1,0,1,2,1,'hostgroup_list','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100170002,2100011701,'ホストグループ親子紐付',NULL,NULL,NULL,1,0,1,2,2,'hostgroup_link_list','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-170002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100170002,2100011701,'ホストグループ親子紐付',NULL,NULL,NULL,1,0,1,2,2,'hostgroup_link_list','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100170003,2100011701,'ホスト紐付管理',NULL,NULL,NULL,1,0,1,2,3,'host_link','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-170003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100170003,2100011701,'ホスト紐付管理',NULL,NULL,NULL,1,0,1,2,3,'host_link','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100170004,2100011701,'ホストグループ分割対象',NULL,NULL,NULL,1,0,1,2,4,'split_target','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-170004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100170004,2100011701,'ホストグループ分割対象',NULL,NULL,NULL,1,0,1,2,4,'split_target','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101701,'n01','5ebbc37e034d6874a2af59eb04beaa52','ホストグループ分解機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101701,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101701,'n01','5ebbc37e034d6874a2af59eb04beaa52','ホストグループ分解機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101702,'n02','5ebbc37e034d6874a2af59eb04beaa52','ホストグループ変数化機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101702,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101702,'n02','5ebbc37e034d6874a2af59eb04beaa52','ホストグループ変数化機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101703,'n03','5ebbc37e034d6874a2af59eb04beaa52','ホストグループ変数登録機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101703,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101703,'n03','5ebbc37e034d6874a2af59eb04beaa52','ホストグループ変数登録機能','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100170001,1,2100170001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-170001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100170001,1,2100170001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100170002,1,2100170002,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-170002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100170002,1,2100170002,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100170003,1,2100170003,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-170003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100170003,1,2100170003,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100170004,1,2100170004,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-170004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100170004,1,2100170004,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


COMMIT;
