-- *****************************************************************************
-- *** ***** CreateParameterMenu Tables                                      ***
-- *****************************************************************************
CREATE TABLE F_MM_STATUS_MASTER
(
FILE_STATUS_ID                     INT                              , -- 識別シーケンス項番

FILE_STATUS_NAME                   VARCHAR (32)                     ,
DISP_FLAG_1                        INT                              ,
DISP_FLAG_2                        INT                              ,
DISP_FLAG_3                        INT                              ,
DISP_FLAG_4                        INT                              ,
DISP_FLAG_5                        INT                              ,
DISP_FLAG_6                        INT                              ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ

PRIMARY KEY (FILE_STATUS_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_MM_STATUS_MASTER_JNL
(
JOURNAL_SEQ_NO                     INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR  (8)                     , -- 履歴用変更種別

FILE_STATUS_ID                     INT                              , -- 識別シーケンス項番

FILE_STATUS_NAME                   VARCHAR (32)                     ,
DISP_FLAG_1                        INT                              ,
DISP_FLAG_2                        INT                              ,
DISP_FLAG_3                        INT                              ,
DISP_FLAG_4                        INT                              ,
DISP_FLAG_5                        INT                              ,
DISP_FLAG_6                        INT                              ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_DIR_MASTER
(
DIR_ID                             INT                              , -- 識別シーケンスディレクトリID

DIR_NAME                           VARCHAR (128)                    ,
PARENT_DIR_ID                      INT                              ,
DIR_NAME_FULLPATH                  VARCHAR (1024)                   ,
CHMOD                              VARCHAR (3)                      ,
GROUP_AUTH                         VARCHAR (128)                    ,
USER_AUTH                          VARCHAR (128)                    ,
DIR_USAGE                          VARCHAR (4000)                   ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ

PRIMARY KEY (DIR_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_DIR_MASTER_JNL
(
JOURNAL_SEQ_NO                     INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR  (8)                     , -- 履歴用変更種別

DIR_ID                             INT                              , -- 識別シーケンスディレクトリID

DIR_NAME                           VARCHAR (128)                    ,
PARENT_DIR_ID                      INT                              ,
DIR_NAME_FULLPATH                  VARCHAR (1024)                   ,
CHMOD                              VARCHAR (3)                      ,
GROUP_AUTH                         VARCHAR (128)                    ,
USER_AUTH                          VARCHAR (128)                    ,
DIR_USAGE                          VARCHAR (4000)                   ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_AUTO_RETURN
(
ROW_ID                             INT                              , -- 識別シーケンス項番

AUTO_FLAG                          INT                              ,
AUTO_CONFIG                        VARCHAR (128)                    ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_AUTO_RETURN_JNL
(
JOURNAL_SEQ_NO                     INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR  (8)                     , -- 履歴用変更種別

ROW_ID                             INT                              , -- 識別シーケンス項番

AUTO_FLAG                          INT                              ,
AUTO_CONFIG                        VARCHAR (128)                    ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_FILE_MASTER
(
FILE_ID                             INT                             , -- 識別シーケンスファイルID

FILE_NAME                          VARCHAR (128)                    ,
DIR_ID                             INT                              ,
AUTO_RETURN_FLAG                   INT                              ,
CHMOD                              VARCHAR (3)                      ,
GROUP_AUTH                         text(128)                        ,
USER_AUTH                          text(128)                        ,
DIR_USAGE                          VARCHAR (4000)                   ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ

PRIMARY KEY (FILE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_FILE_MASTER_JNL
(
JOURNAL_SEQ_NO                     INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR  (8)                     , -- 履歴用変更種別

FILE_ID                             INT                             , -- 識別シーケンスファイルID

FILE_NAME                          VARCHAR (128)                    ,
DIR_ID                             INT                              ,
AUTO_RETURN_FLAG                   INT                              ,
CHMOD                              VARCHAR (3)                      ,
GROUP_AUTH                         text(128)                        ,
USER_AUTH                          text(128)                        ,
DIR_USAGE                          VARCHAR (4000)                   ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_FILE_MANAGEMENT
(
FILE_M_ID                          INT                              , -- 識別シーケンス申請No

FILE_STATUS_ID                     INT                              ,
FILE_ID                            INT                              ,
REQUIRE_DATE                       DATETIME(6)                      ,
REQUIRE_USER_ID                    INT                              ,
REQUIRE_TICKET                     VARCHAR (8)                      ,
REQUIRE_ABSTRUCT                   VARCHAR (4000)                   ,
REQUIRE_SCHEDULEDATE               DATETIME(6)                      ,
ASSIGN_DATE                        DATETIME(6)                      ,
ASSIGN_USER_ID                     INT                              ,
ASSIGN_FILE                        VARCHAR (256)                    ,
ASSIGN_REVISION                    VARCHAR (64)                     ,
RETURN_DATE                        DATETIME(6)                      ,
RETURN_USER_ID                     INT                              ,
RETURN_FILE                        VARCHAR (256)                    ,
RETURN_DIFF                        VARCHAR (256)                    ,
RETURN_TESTCASES                   VARCHAR (256)                    ,
RETURN_EVIDENCES                   VARCHAR (256)                    ,
CLOSE_DATE                         DATETIME(6)                      ,
CLOSE_USER_ID                      INT                              ,
CLOSE_REVISION                     VARCHAR (64)                     ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ

PRIMARY KEY (FILE_M_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_FILE_MANAGEMENT_INITIAL
(
FILE_M_ID                          INT                              , -- 識別シーケンス申請No

FILE_STATUS_ID                     INT                              ,
FILE_ID                            INT                              ,
REQUIRE_DATE                       DATETIME(6)                      ,
REQUIRE_USER_ID                    INT                              ,
REQUIRE_TICKET                     VARCHAR (8)                      ,
REQUIRE_ABSTRUCT                   VARCHAR (4000)                   ,
REQUIRE_SCHEDULEDATE               DATETIME(6)                      ,
ASSIGN_DATE                        DATETIME(6)                      ,
ASSIGN_USER_ID                     INT                              ,
ASSIGN_FILE                        VARCHAR (256)                    ,
ASSIGN_REVISION                    VARCHAR (64)                     ,
RETURN_DATE                        DATETIME(6)                      ,
RETURN_USER_ID                     INT                              ,
RETURN_FILE                        VARCHAR (256)                    ,
RETURN_DIFF                        VARCHAR (256)                    ,
RETURN_TESTCASES                   VARCHAR (256)                    ,
RETURN_EVIDENCES                   VARCHAR (256)                    ,
CLOSE_DATE                         DATETIME(6)                      ,
CLOSE_USER_ID                      INT                              ,
CLOSE_REVISION                     VARCHAR (64)                     ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ

PRIMARY KEY (FILE_M_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_FILE_MANAGEMENT_JNL
(
JOURNAL_SEQ_NO                     INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR  (8)                     , -- 履歴用変更種別

FILE_M_ID                          INT                              , -- 識別シーケンス申請No

FILE_STATUS_ID                     INT                              ,
FILE_ID                            INT                              ,
REQUIRE_DATE                       DATETIME(6)                      ,
REQUIRE_USER_ID                    INT                              ,
REQUIRE_TICKET                     VARCHAR (8)                      ,
REQUIRE_ABSTRUCT                   VARCHAR (4000)                   ,
REQUIRE_SCHEDULEDATE               DATETIME(6)                      ,
ASSIGN_DATE                        DATETIME(6)                      ,
ASSIGN_USER_ID                     INT                              ,
ASSIGN_FILE                        VARCHAR (256)                    ,
ASSIGN_REVISION                    VARCHAR (64)                     ,
RETURN_DATE                        DATETIME(6)                      ,
RETURN_USER_ID                     INT                              ,
RETURN_FILE                        VARCHAR (256)                    ,
RETURN_DIFF                        VARCHAR (256)                    ,
RETURN_TESTCASES                   VARCHAR (256)                    ,
RETURN_EVIDENCES                   VARCHAR (256)                    ,
CLOSE_DATE                         DATETIME(6)                      ,
CLOSE_USER_ID                      INT                              ,
CLOSE_REVISION                     VARCHAR (64)                     ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_FILE_MANAGEMENT_INITIAL_JNL
(
JOURNAL_SEQ_NO                     INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR  (8)                     , -- 履歴用変更種別

FILE_M_ID                          INT                              , -- 識別シーケンス申請No

FILE_STATUS_ID                     INT                              ,
FILE_ID                            INT                              ,
REQUIRE_DATE                       DATETIME(6)                      ,
REQUIRE_USER_ID                    INT                              ,
REQUIRE_TICKET                     VARCHAR (8)                      ,
REQUIRE_ABSTRUCT                   VARCHAR (4000)                   ,
REQUIRE_SCHEDULEDATE               DATETIME(6)                      ,
ASSIGN_DATE                        DATETIME(6)                      ,
ASSIGN_USER_ID                     INT                              ,
ASSIGN_FILE                        VARCHAR (256)                    ,
ASSIGN_REVISION                    VARCHAR (64)                     ,
RETURN_DATE                        DATETIME(6)                      ,
RETURN_USER_ID                     INT                              ,
RETURN_FILE                        VARCHAR (256)                    ,
RETURN_DIFF                        VARCHAR (256)                    ,
RETURN_TESTCASES                   VARCHAR (256)                    ,
RETURN_EVIDENCES                   VARCHAR (256)                    ,
CLOSE_DATE                         DATETIME(6)                      ,
CLOSE_USER_ID                      INT                              ,
CLOSE_REVISION                     VARCHAR (64)                     ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_MATERIAL_IF_INFO
(
ROW_ID                             INT                              , -- 識別シーケンス項番

REMORT_REPO_URL                    VARCHAR (256)                    ,
BRANCH                             VARCHAR (256)                    ,
CLONE_REPO_DIR                     VARCHAR (256)                    ,
PASSWORD                           VARCHAR (128)                    ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE F_MATERIAL_IF_INFO_JNL
(
JOURNAL_SEQ_NO                     INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR  (8)                     , -- 履歴用変更種別

ROW_ID                             INT                              , -- 識別シーケンス項番

REMORT_REPO_URL                    VARCHAR (256)                    ,
BRANCH                             VARCHAR (256)                    ,
CLONE_REPO_DIR                     VARCHAR (256)                    ,
PASSWORD                           VARCHAR (128)                    ,

NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


-- *****************************************************************************
-- *** ***** Views                                                           ***
-- *****************************************************************************
CREATE OR REPLACE VIEW G_FILE_STATUS_MASTER_1 AS 
SELECT * 
FROM   F_MM_STATUS_MASTER 
WHERE  DISUSE_FLAG = '0';
	
CREATE OR REPLACE VIEW G_FILE_STATUS_MASTER_2 AS
SELECT *
FROM   F_MM_STATUS_MASTER
WHERE  DISUSE_FLAG = '0'
AND    FILE_STATUS_ID IN ( 1 );

CREATE OR REPLACE VIEW G_FILE_STATUS_MASTER_3 AS
SELECT *
FROM   F_MM_STATUS_MASTER
WHERE  DISUSE_FLAG = '0'
AND    FILE_STATUS_ID IN ( 3, 7 );

CREATE OR REPLACE VIEW G_FILE_STATUS_MASTER_4 AS
SELECT *
FROM   F_MM_STATUS_MASTER
WHERE  DISUSE_FLAG = '0'
AND    FILE_STATUS_ID IN ( 4 );

CREATE OR REPLACE VIEW G_FILE_STATUS_MASTER_5 AS
SELECT *
FROM   F_MM_STATUS_MASTER
WHERE  DISUSE_FLAG = '0'
AND    FILE_STATUS_ID IN ( 5, 8 );

CREATE OR REPLACE VIEW G_FILE_STATUS_MASTER_6 AS
SELECT *
FROM   F_MM_STATUS_MASTER
WHERE  DISUSE_FLAG = '0'
AND    FILE_STATUS_ID IN ( 9 );

CREATE OR REPLACE VIEW G_FILE_MASTER AS 
SELECT TAB_A.FILE_ID                                                             ,
       TAB_A.FILE_NAME                                                           ,
       TAB_A.DIR_ID                                                              ,
       CONCAT(TAB_B.DIR_NAME_FULLPATH,TAB_A.FILE_NAME) AS FILE_NAME_FULLPATH                                                 ,
       TAB_A.AUTO_RETURN_FLAG                                                    ,
       TAB_A.CHMOD                                                               ,
       TAB_A.GROUP_AUTH                                                          ,
       TAB_A.USER_AUTH                                                           ,
       TAB_A.DIR_USAGE                                                           ,
       TAB_A.NOTE                                                                ,
       TAB_A.DISUSE_FLAG                                                         ,
       TAB_A.LAST_UPDATE_TIMESTAMP                                               ,
       TAB_A.LAST_UPDATE_USER 
FROM   F_FILE_MASTER TAB_A 
       LEFT JOIN F_DIR_MASTER TAB_B
       ON        (TAB_A.DIR_ID = TAB_B.DIR_ID)                                   ;

CREATE OR REPLACE VIEW G_FILE_MASTER_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                                                      ,
       TAB_A.JOURNAL_REG_DATETIME                                                ,
       TAB_A.JOURNAL_ACTION_CLASS                                                ,
       TAB_A.FILE_ID                                                             ,
       TAB_A.FILE_NAME                                                           ,
       TAB_A.DIR_ID                                                              ,
       CONCAT(TAB_B.DIR_NAME_FULLPATH,TAB_A.FILE_NAME) AS FILE_NAME_FULLPATH                                                 ,
       TAB_A.AUTO_RETURN_FLAG                                                    ,
       TAB_A.CHMOD                                                               ,
       TAB_A.GROUP_AUTH                                                          ,
       TAB_A.USER_AUTH                                                           ,
       TAB_A.DIR_USAGE                                                           ,
       TAB_A.NOTE                                                                ,
       TAB_A.DISUSE_FLAG                                                         ,
       TAB_A.LAST_UPDATE_TIMESTAMP                                               ,
       TAB_A.LAST_UPDATE_USER 
FROM   F_FILE_MASTER_JNL TAB_A 
       LEFT JOIN F_DIR_MASTER TAB_B
       ON        (TAB_A.DIR_ID = TAB_B.DIR_ID)                                   ;

CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_1 AS 
SELECT TAB_A.FILE_M_ID             ,
       TAB_A.FILE_STATUS_ID        ,
       TAB_A.FILE_ID               ,
       TAB_B.FILE_NAME_FULLPATH    ,
       TAB_A.REQUIRE_DATE          ,
       TAB_A.REQUIRE_USER_ID       ,
       TAB_A.REQUIRE_TICKET        ,
       TAB_A.REQUIRE_ABSTRUCT      ,
       TAB_A.REQUIRE_SCHEDULEDATE  ,
       TAB_A.ASSIGN_DATE           ,
       TAB_A.ASSIGN_USER_ID        ,
       TAB_A.ASSIGN_FILE           ,
       TAB_A.ASSIGN_REVISION       ,
       TAB_A.RETURN_DATE           ,
       TAB_A.RETURN_USER_ID        ,
       TAB_A.RETURN_FILE           ,
       TAB_A.RETURN_DIFF           ,
       TAB_A.RETURN_TESTCASES      ,
       TAB_A.RETURN_EVIDENCES      ,
       TAB_A.CLOSE_DATE            ,
       TAB_A.CLOSE_USER_ID         ,
       TAB_A.CLOSE_REVISION        ,
       TAB_A.NOTE                  ,
       TAB_A.DISUSE_FLAG           ,
       TAB_A.LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER 
FROM   F_FILE_MANAGEMENT TAB_A
LEFT JOIN G_FILE_MASTER TAB_B ON (TAB_A.FILE_ID = TAB_B.FILE_ID) 
WHERE  FILE_STATUS_ID 
IN     (SELECT FILE_STATUS_ID 
        FROM   F_MM_STATUS_MASTER 
        WHERE  DISUSE_FLAG = '0' 
        AND    DISP_FLAG_1 = 1 )   ;

--  払出申請画面
CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_2 AS 
SELECT TAB_A.FILE_M_ID             ,
       TAB_A.FILE_STATUS_ID        ,
       TAB_A.FILE_ID               ,
       TAB_B.FILE_NAME_FULLPATH    ,
       TAB_A.REQUIRE_DATE          ,
       TAB_A.REQUIRE_USER_ID       ,
       TAB_A.REQUIRE_TICKET        ,
       TAB_A.REQUIRE_ABSTRUCT      ,
       TAB_A.REQUIRE_SCHEDULEDATE  ,
       TAB_A.ASSIGN_DATE           ,
       TAB_A.ASSIGN_USER_ID        ,
       TAB_A.ASSIGN_FILE           ,
       TAB_A.ASSIGN_REVISION       ,
       TAB_A.RETURN_DATE           ,
       TAB_A.RETURN_USER_ID        ,
       TAB_A.RETURN_FILE           ,
       TAB_A.RETURN_DIFF           ,
       TAB_A.RETURN_TESTCASES      ,
       TAB_A.RETURN_EVIDENCES      ,
       TAB_A.CLOSE_DATE            ,
       TAB_A.CLOSE_USER_ID         ,
       TAB_A.CLOSE_REVISION        ,
       TAB_A.NOTE                  ,
       TAB_A.DISUSE_FLAG           ,
       TAB_A.LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER 
FROM   F_FILE_MANAGEMENT TAB_A
LEFT JOIN G_FILE_MASTER TAB_B ON (TAB_A.FILE_ID = TAB_B.FILE_ID) 
WHERE  FILE_STATUS_ID 
IN     (SELECT FILE_STATUS_ID 
        FROM   F_MM_STATUS_MASTER 
        WHERE  DISUSE_FLAG = '0' 
        AND    DISP_FLAG_2 = 1 )   ;

--  払出画面
CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_3 AS 
SELECT TAB_A.FILE_M_ID             ,
       TAB_A.FILE_STATUS_ID        ,
       TAB_A.FILE_ID               ,
       TAB_B.FILE_NAME_FULLPATH    ,
       TAB_A.REQUIRE_DATE          ,
       TAB_A.REQUIRE_USER_ID       ,
       TAB_A.REQUIRE_TICKET        ,
       TAB_A.REQUIRE_ABSTRUCT      ,
       TAB_A.REQUIRE_SCHEDULEDATE  ,
       TAB_A.ASSIGN_DATE           ,
       TAB_A.ASSIGN_USER_ID        ,
       TAB_A.ASSIGN_FILE           ,
       TAB_A.ASSIGN_REVISION       ,
       TAB_A.RETURN_DATE           ,
       TAB_A.RETURN_USER_ID        ,
       TAB_A.RETURN_FILE           ,
       TAB_A.RETURN_DIFF           ,
       TAB_A.RETURN_TESTCASES      ,
       TAB_A.RETURN_EVIDENCES      ,
       TAB_A.CLOSE_DATE            ,
       TAB_A.CLOSE_USER_ID         ,
       TAB_A.CLOSE_REVISION        ,
       TAB_A.NOTE                  ,
       TAB_A.DISUSE_FLAG           ,
       TAB_A.LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER 
FROM   F_FILE_MANAGEMENT TAB_A
LEFT JOIN G_FILE_MASTER TAB_B ON (TAB_A.FILE_ID = TAB_B.FILE_ID) 
WHERE  FILE_STATUS_ID 
IN     (SELECT FILE_STATUS_ID 
        FROM   F_MM_STATUS_MASTER 
        WHERE  DISUSE_FLAG = '0' 
        AND    DISP_FLAG_3 = 1 )   ;

--  払戻申請画面
CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_4 AS 
SELECT TAB_A.FILE_M_ID             ,
       TAB_A.FILE_STATUS_ID        ,
       TAB_A.FILE_ID               ,
       TAB_B.FILE_NAME_FULLPATH    ,
       TAB_A.REQUIRE_DATE          ,
       TAB_A.REQUIRE_USER_ID       ,
       TAB_A.REQUIRE_TICKET        ,
       TAB_A.REQUIRE_ABSTRUCT      ,
       TAB_A.REQUIRE_SCHEDULEDATE  ,
       TAB_A.ASSIGN_DATE           ,
       TAB_A.ASSIGN_USER_ID        ,
       TAB_A.ASSIGN_FILE           ,
       TAB_A.ASSIGN_REVISION       ,
       TAB_A.RETURN_DATE           ,
       TAB_A.RETURN_USER_ID        ,
       TAB_A.RETURN_FILE           ,
       TAB_A.RETURN_DIFF           ,
       TAB_A.RETURN_TESTCASES      ,
       TAB_A.RETURN_EVIDENCES      ,
       TAB_A.CLOSE_DATE            ,
       TAB_A.CLOSE_USER_ID         ,
       TAB_A.CLOSE_REVISION        ,
       TAB_A.NOTE                  ,
       TAB_A.DISUSE_FLAG           ,
       TAB_A.LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER 
FROM   F_FILE_MANAGEMENT TAB_A
LEFT JOIN G_FILE_MASTER TAB_B ON (TAB_A.FILE_ID = TAB_B.FILE_ID) 
WHERE  FILE_STATUS_ID 
IN     (SELECT FILE_STATUS_ID 
        FROM   F_MM_STATUS_MASTER 
        WHERE  DISUSE_FLAG = '0' 
        AND    DISP_FLAG_4 = 1 )   ;

--  払戻画面
CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_5 AS 
SELECT TAB_A.FILE_M_ID             ,
       TAB_A.FILE_STATUS_ID        ,
       TAB_A.FILE_ID               ,
       TAB_B.FILE_NAME_FULLPATH    ,
       TAB_A.REQUIRE_DATE          ,
       TAB_A.REQUIRE_USER_ID       ,
       TAB_A.REQUIRE_TICKET        ,
       TAB_A.REQUIRE_ABSTRUCT      ,
       TAB_A.REQUIRE_SCHEDULEDATE  ,
       TAB_A.ASSIGN_DATE           ,
       TAB_A.ASSIGN_USER_ID        ,
       TAB_A.ASSIGN_FILE           ,
       TAB_A.ASSIGN_REVISION       ,
       TAB_A.RETURN_DATE           ,
       TAB_A.RETURN_USER_ID        ,
       TAB_A.RETURN_FILE           ,
       TAB_A.RETURN_DIFF           ,
       TAB_A.RETURN_TESTCASES      ,
       TAB_A.RETURN_EVIDENCES      ,
       TAB_A.CLOSE_DATE            ,
       TAB_A.CLOSE_USER_ID         ,
       TAB_A.CLOSE_REVISION        ,
       TAB_A.NOTE                  ,
       TAB_A.DISUSE_FLAG           ,
       TAB_A.LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER 
FROM   F_FILE_MANAGEMENT TAB_A
LEFT JOIN G_FILE_MASTER TAB_B ON (TAB_A.FILE_ID = TAB_B.FILE_ID) 
WHERE  FILE_STATUS_ID 
IN     (SELECT FILE_STATUS_ID 
        FROM   F_MM_STATUS_MASTER 
        WHERE  DISUSE_FLAG = '0' 
        AND    DISP_FLAG_5 = 1 )   ;

--  取下げ画面
CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_6 AS 
SELECT TAB_A.FILE_M_ID             ,
       TAB_A.FILE_STATUS_ID        ,
       TAB_A.FILE_ID               ,
       TAB_B.FILE_NAME_FULLPATH    ,
       TAB_A.REQUIRE_DATE          ,
       TAB_A.REQUIRE_USER_ID       ,
       TAB_A.REQUIRE_TICKET        ,
       TAB_A.REQUIRE_ABSTRUCT      ,
       TAB_A.REQUIRE_SCHEDULEDATE  ,
       TAB_A.ASSIGN_DATE           ,
       TAB_A.ASSIGN_USER_ID        ,
       TAB_A.ASSIGN_FILE           ,
       TAB_A.ASSIGN_REVISION       ,
       TAB_A.RETURN_DATE           ,
       TAB_A.RETURN_USER_ID        ,
       TAB_A.RETURN_FILE           ,
       TAB_A.RETURN_DIFF           ,
       TAB_A.RETURN_TESTCASES      ,
       TAB_A.RETURN_EVIDENCES      ,
       TAB_A.CLOSE_DATE            ,
       TAB_A.CLOSE_USER_ID         ,
       TAB_A.CLOSE_REVISION        ,
       TAB_A.NOTE                  ,
       TAB_A.DISUSE_FLAG           ,
       TAB_A.LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER 
FROM   F_FILE_MANAGEMENT TAB_A
LEFT JOIN G_FILE_MASTER TAB_B ON (TAB_A.FILE_ID = TAB_B.FILE_ID) 
WHERE  FILE_STATUS_ID 
IN     (SELECT FILE_STATUS_ID 
        FROM   F_MM_STATUS_MASTER 
        WHERE  DISUSE_FLAG = '0' 
        AND    DISP_FLAG_6 = 1 )   ;

-- 全画面共通履歴用VIEW

CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO        ,
       TAB_A.JOURNAL_REG_DATETIME  ,
       TAB_A.JOURNAL_ACTION_CLASS  ,
       TAB_A.FILE_M_ID             ,
       TAB_A.FILE_STATUS_ID        ,
       TAB_A.FILE_ID               ,
       TAB_B.FILE_NAME_FULLPATH    ,
       TAB_A.REQUIRE_DATE          ,
       TAB_A.REQUIRE_USER_ID       ,
       TAB_A.REQUIRE_TICKET        ,
       TAB_A.REQUIRE_ABSTRUCT      ,
       TAB_A.REQUIRE_SCHEDULEDATE  ,
       TAB_A.ASSIGN_DATE           ,
       TAB_A.ASSIGN_USER_ID        ,
       TAB_A.ASSIGN_FILE           ,
       TAB_A.ASSIGN_REVISION       ,
       TAB_A.RETURN_DATE           ,
       TAB_A.RETURN_USER_ID        ,
       TAB_A.RETURN_FILE           ,
       TAB_A.RETURN_DIFF           ,
       TAB_A.RETURN_TESTCASES      ,
       TAB_A.RETURN_EVIDENCES      ,
       TAB_A.CLOSE_DATE            ,
       TAB_A.CLOSE_USER_ID         ,
       TAB_A.CLOSE_REVISION        ,
       TAB_A.NOTE                  ,
       TAB_A.DISUSE_FLAG           ,
       TAB_A.LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER 
FROM F_FILE_MANAGEMENT_JNL TAB_A
LEFT JOIN G_FILE_MASTER TAB_B ON (TAB_A.FILE_ID = TAB_B.FILE_ID) 
WHERE FILE_STATUS_ID 
IN    (SELECT FILE_STATUS_ID 
       FROM   F_MM_STATUS_MASTER 
       WHERE  DISUSE_FLAG = '0' 
       AND    DISP_FLAG_1 = 1 )    ;

-- 資材一覧メニュー用VIEW
CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_UNION AS 
SELECT  TAB_A.FILE_M_ID             AS FILE_M_ID            ,
        TAB_A.FILE_STATUS_ID        AS FILE_STATUS_ID       ,
        TAB_A.FILE_ID               AS FILE_ID              ,
        TAB_A.RETURN_FILE           AS RETURN_FILE          ,
        TAB_A.CLOSE_DATE            AS CLOSE_DATE           ,
        TAB_A.RETURN_USER_ID        AS RETURN_USER_ID       ,
        TAB_A.CLOSE_REVISION        AS CLOSE_REVISION       ,
        TAB_A.NOTE                  AS NOTE                 ,
        TAB_A.DISUSE_FLAG           AS DISUSE_FLAG          ,
        TAB_A.LAST_UPDATE_TIMESTAMP AS LAST_UPDATE_TIMESTAMP,
        TAB_A.LAST_UPDATE_USER      AS LAST_UPDATE_USER
FROM F_FILE_MANAGEMENT TAB_A
WHERE  TAB_A.FILE_STATUS_ID IN (6)
UNION
SELECT -TAB_B.FILE_M_ID             AS FILE_M_ID            ,
        TAB_B.FILE_STATUS_ID        AS FILE_STATUS_ID       ,
        TAB_B.FILE_ID               AS FILE_ID              ,
        TAB_B.RETURN_FILE           AS RETURN_FILE          ,
        TAB_B.CLOSE_DATE            AS CLOSE_DATE           ,
        TAB_B.RETURN_USER_ID        AS RETURN_USER_ID       ,
        TAB_B.CLOSE_REVISION        AS CLOSE_REVISION       ,
        TAB_B.NOTE                  AS NOTE                 ,
        TAB_B.DISUSE_FLAG           AS DISUSE_FLAG          ,
        TAB_B.LAST_UPDATE_TIMESTAMP AS LAST_UPDATE_TIMESTAMP,
        TAB_B.LAST_UPDATE_USER      AS LAST_UPDATE_USER
FROM F_FILE_MANAGEMENT_INITIAL TAB_B
;

CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_NEWEST AS 
SELECT TAB_A.FILE_M_ID             ,
       TAB_A.FILE_ID               ,
       TAB_A.RETURN_FILE           ,
       TAB_C.FILE_NAME_FULLPATH    ,
       TAB_A.CLOSE_DATE            ,
       TAB_A.RETURN_USER_ID        ,
       TAB_A.CLOSE_REVISION        ,
       TAB_A.NOTE                  ,
       TAB_A.DISUSE_FLAG           ,
       TAB_A.LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER      ,
       IF(TAB_A.CLOSE_DATE = (SELECT MAX(TAB_B.CLOSE_DATE) FROM G_FILE_MANAGEMENT_UNION TAB_B WHERE TAB_A.FILE_ID = TAB_B.FILE_ID AND TAB_B.DISUSE_FLAG='0'), "●", "") NEWEST_FLAG
FROM   G_FILE_MANAGEMENT_UNION TAB_A
LEFT JOIN G_FILE_MASTER TAB_C ON (TAB_A.FILE_ID = TAB_C.FILE_ID) 
WHERE TAB_C.DISUSE_FLAG='0'
;

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_MM_STATUS_MASTER_RIC',11);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_MM_STATUS_MASTER_JSQ',11);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_DIR_MASTER_RIC',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_DIR_MASTER_JSQ',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_AUTO_RETURN_RIC',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_AUTO_RETURN_JSQ',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_FILE_MASTER_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_FILE_MASTER_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_FILE_MANAGEMENT_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_FILE_MANAGEMENT_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_FILE_MANAGEMENT_INITIAL_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_FILE_MANAGEMENT_INITIAL_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_MATERIAL_IF_INFO_RIC',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('F_MATERIAL_IF_INFO_JSQ',2);


INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011501,'File control management','sizai_kanri.png',30,'File control management','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011501,'File control management','sizai_kanri.png',30,'File control management','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100011502,'File control check-in/check-out','sizai_harai.png',40,'File control check-in/check-out','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100011502,'File control check-in/check-out','sizai_harai.png',40,'File control check-in/check-out','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150001,2100011501,'Interface information',NULL,NULL,NULL,1,0,1,1,1,'material_if_info','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150001,2100011501,'Interface information',NULL,NULL,NULL,1,0,1,1,1,'material_if_info','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150002,2100011501,'Directory master',NULL,NULL,NULL,1,0,1,2,2,'dir_master','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150002,2100011501,'Directory master',NULL,NULL,NULL,1,0,1,2,2,'dir_master','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150003,2100011501,'File master',NULL,NULL,NULL,1,0,1,2,3,'file_master','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150003,2100011501,'File master',NULL,NULL,NULL,1,0,1,2,3,'file_master','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150004,2100011501,'File list',NULL,NULL,NULL,1,0,1,2,4,'latest_file_master','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150004,2100011501,'File list',NULL,NULL,NULL,1,0,1,2,4,'latest_file_master','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150005,2100011501,'Status master',NULL,NULL,NULL,1,0,1,2,5,'status_master','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150005,2100011501,'Status master',NULL,NULL,NULL,1,0,1,2,5,'status_master','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150101,2100011502,'Browse',NULL,NULL,NULL,1,0,1,2,1,'file_management_1','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150102,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150101,2100011502,'Browse',NULL,NULL,NULL,1,0,1,2,1,'file_management_1','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150102,2100011502,'Check-out request',NULL,NULL,NULL,1,0,1,2,2,'file_management_2','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150103,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150102,2100011502,'Check-out request',NULL,NULL,NULL,1,0,1,2,2,'file_management_2','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150103,2100011502,'Check out',NULL,NULL,NULL,1,0,1,2,3,'file_management_3','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150104,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150103,2100011502,'Check out',NULL,NULL,NULL,1,0,1,2,3,'file_management_3','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150104,2100011502,'Check-in request',NULL,NULL,NULL,1,0,1,2,4,'file_management_4','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150105,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150104,2100011502,'Check-in request',NULL,NULL,NULL,1,0,1,2,4,'file_management_4','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150105,2100011502,'Check in',NULL,NULL,NULL,1,0,1,2,5,'file_management_5','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150106,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150105,2100011502,'Check in',NULL,NULL,NULL,1,0,1,2,5,'file_management_5','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150106,2100011502,'Cancel',NULL,NULL,NULL,1,0,1,2,6,'file_management_6','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150107,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150106,2100011502,'Cancel',NULL,NULL,NULL,1,0,1,2,6,'file_management_6','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101501,'l01','5ebbc37e034d6874a2af59eb04beaa52','Initial synchronization procedure','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101501,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101501,'l01','5ebbc37e034d6874a2af59eb04beaa52','Initial synchronization procedure','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101502,'l02','5ebbc37e034d6874a2af59eb04beaa52','Auto check-in/check-out procedure','sample@xxx.bbb.ccc',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101502,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101502,'l02','5ebbc37e034d6874a2af59eb04beaa52','Auto check-in/check-out procedure','sample@xxx.bbb.ccc',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101503,'l03','5ebbc37e034d6874a2af59eb04beaa52','File link procedure','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101503,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101503,'l03','5ebbc37e034d6874a2af59eb04beaa52','File link procedure','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101504,'l04','5ebbc37e034d6874a2af59eb04beaa52','Reflect master update procedure','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101504,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-101504,'l04','5ebbc37e034d6874a2af59eb04beaa52','Reflect master update procedure','sample@xxx.bbb.ccc',NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150001,1,2100150001,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150001,1,2100150001,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150002,1,2100150002,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150002,1,2100150002,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150003,1,2100150003,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150003,1,2100150003,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150004,1,2100150004,2,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150004,1,2100150004,2,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150005,1,2100150005,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150005,1,2100150005,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150101,1,2100150101,2,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150102,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150101,1,2100150101,2,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150102,1,2100150102,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150103,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150102,1,2100150102,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150103,1,2100150103,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150104,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150103,1,2100150103,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150104,1,2100150104,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150105,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150104,1,2100150104,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150105,1,2100150105,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150106,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150105,1,2100150105,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100150106,1,2100150106,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-150107,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100150106,1,2100150106,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_MM_STATUS_MASTER (FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Check out requested',1,1,1,0,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Check out requested',1,1,1,0,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER (FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Check out requested (duplicate)',1,1,1,0,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Check out requested (duplicate)',1,1,1,0,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER (FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Checking out',1,0,0,1,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'Checking out',1,0,0,1,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER (FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Check in requested',1,0,0,0,1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'Check in requested',1,0,0,0,1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER (FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'Checking in',1,0,0,0,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'Checking in',1,0,0,0,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER (FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'Check in complete',1,0,0,0,0,0,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',6,'Check in complete',1,0,0,0,0,0,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER (FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'Return (check-out request)',1,1,0,0,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',7,'Return (check-out request)',1,1,0,0,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER (FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'Return (check-in request)',1,0,0,1,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',8,'Return (check-in request)',1,0,0,1,0,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER (FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'Cancel',1,0,0,0,0,0,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',9,'Cancel',1,0,0,0,0,0,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER (FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'Initial registration',0,0,0,0,0,0,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MM_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FILE_STATUS_ID,FILE_STATUS_NAME,DISP_FLAG_1,DISP_FLAG_2,DISP_FLAG_3,DISP_FLAG_4,DISP_FLAG_5,DISP_FLAG_6,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',10,'Initial registration',0,0,0,0,0,0,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_DIR_MASTER (DIR_ID,DIR_NAME,PARENT_DIR_ID,DIR_NAME_FULLPATH,CHMOD,GROUP_AUTH,USER_AUTH,DIR_USAGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,NULL,NULL,'/','755','root','root',NULL,'初期データのため更新不可','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_DIR_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,DIR_ID,DIR_NAME,PARENT_DIR_ID,DIR_NAME_FULLPATH,CHMOD,GROUP_AUTH,USER_AUTH,DIR_USAGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,NULL,NULL,'/','755','root','root',NULL,'初期データのため更新不可','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_AUTO_RETURN (ROW_ID,AUTO_FLAG,AUTO_CONFIG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,0,'Check in after administrator approval',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_AUTO_RETURN_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,AUTO_FLAG,AUTO_CONFIG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,0,'Check in after administrator approval',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_AUTO_RETURN (ROW_ID,AUTO_FLAG,AUTO_CONFIG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,1,'Auto-check in',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_AUTO_RETURN_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,AUTO_FLAG,AUTO_CONFIG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,1,'Auto-check in',NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO F_MATERIAL_IF_INFO (ROW_ID,REMORT_REPO_URL,CLONE_REPO_DIR,PASSWORD,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,NULL,NULL,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO F_MATERIAL_IF_INFO_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,REMORT_REPO_URL,CLONE_REPO_DIR,PASSWORD,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,NULL,NULL,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


COMMIT;
