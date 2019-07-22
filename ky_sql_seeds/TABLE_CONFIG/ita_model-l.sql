-- *****************************************************************************
-- *** ***** CreateParameterMenu Tables                                      ***
-- *****************************************************************************
CREATE TABLE F_MM_STATUS_MASTER
(
FILE_STATUS_ID                     %INT%                            , -- 識別シーケンス項番

FILE_STATUS_NAME                   %VARCHR%(32)                     ,
DISP_FLAG_1                        %INT%                            ,
DISP_FLAG_2                        %INT%                            ,
DISP_FLAG_3                        %INT%                            ,
DISP_FLAG_4                        %INT%                            ,
DISP_FLAG_5                        %INT%                            ,
DISP_FLAG_6                        %INT%                            ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (FILE_STATUS_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_MM_STATUS_MASTER_JNL
(
JOURNAL_SEQ_NO                     %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR% (8)                     , -- 履歴用変更種別

FILE_STATUS_ID                     %INT%                            , -- 識別シーケンス項番

FILE_STATUS_NAME                   %VARCHR%(32)                     ,
DISP_FLAG_1                        %INT%                            ,
DISP_FLAG_2                        %INT%                            ,
DISP_FLAG_3                        %INT%                            ,
DISP_FLAG_4                        %INT%                            ,
DISP_FLAG_5                        %INT%                            ,
DISP_FLAG_6                        %INT%                            ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_DIR_MASTER
(
DIR_ID                             %INT%                            , -- 識別シーケンスディレクトリID

DIR_NAME                           %VARCHR%(128)                    ,
PARENT_DIR_ID                      %INT%                            ,
DIR_NAME_FULLPATH                  %VARCHR%(1024)                   ,
CHMOD                              %VARCHR%(3)                      ,
GROUP_AUTH                         %VARCHR%(128)                    ,
USER_AUTH                          %VARCHR%(128)                    ,
DIR_USAGE                          %VARCHR%(4000)                   ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (DIR_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_DIR_MASTER_JNL
(
JOURNAL_SEQ_NO                     %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR% (8)                     , -- 履歴用変更種別

DIR_ID                             %INT%                            , -- 識別シーケンスディレクトリID

DIR_NAME                           %VARCHR%(128)                    ,
PARENT_DIR_ID                      %INT%                            ,
DIR_NAME_FULLPATH                  %VARCHR%(1024)                   ,
CHMOD                              %VARCHR%(3)                      ,
GROUP_AUTH                         %VARCHR%(128)                    ,
USER_AUTH                          %VARCHR%(128)                    ,
DIR_USAGE                          %VARCHR%(4000)                   ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_AUTO_RETURN
(
ROW_ID                             %INT%                            , -- 識別シーケンス項番

AUTO_FLAG                          %INT%                            ,
AUTO_CONFIG                        %VARCHR%(128)                    ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_AUTO_RETURN_JNL
(
JOURNAL_SEQ_NO                     %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR% (8)                     , -- 履歴用変更種別

ROW_ID                             %INT%                            , -- 識別シーケンス項番

AUTO_FLAG                          %INT%                            ,
AUTO_CONFIG                        %VARCHR%(128)                    ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_FILE_MASTER
(
FILE_ID                             %INT%                           , -- 識別シーケンスファイルID

FILE_NAME                          %VARCHR%(128)                    ,
DIR_ID                             %INT%                            ,
AUTO_RETURN_FLAG                   %INT%                            ,
CHMOD                              %VARCHR%(3)                      ,
GROUP_AUTH                         text(128)                        ,
USER_AUTH                          text(128)                        ,
DIR_USAGE                          %VARCHR%(4000)                   ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (FILE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_FILE_MASTER_JNL
(
JOURNAL_SEQ_NO                     %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR% (8)                     , -- 履歴用変更種別

FILE_ID                             %INT%                           , -- 識別シーケンスファイルID

FILE_NAME                          %VARCHR%(128)                    ,
DIR_ID                             %INT%                            ,
AUTO_RETURN_FLAG                   %INT%                            ,
CHMOD                              %VARCHR%(3)                      ,
GROUP_AUTH                         text(128)                        ,
USER_AUTH                          text(128)                        ,
DIR_USAGE                          %VARCHR%(4000)                   ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_FILE_MANAGEMENT
(
FILE_M_ID                          %INT%                            , -- 識別シーケンス申請No

FILE_STATUS_ID                     %INT%                            ,
FILE_ID                            %INT%                            ,
REQUIRE_DATE                       %DATETIME6%                      ,
REQUIRE_USER_ID                    %INT%                            ,
REQUIRE_TICKET                     %VARCHR%(8)                      ,
REQUIRE_ABSTRUCT                   %VARCHR%(4000)                   ,
REQUIRE_SCHEDULEDATE               %DATETIME6%                      ,
ASSIGN_DATE                        %DATETIME6%                      ,
ASSIGN_USER_ID                     %INT%                            ,
ASSIGN_FILE                        %VARCHR%(256)                    ,
ASSIGN_REVISION                    %VARCHR%(64)                     ,
RETURN_DATE                        %DATETIME6%                      ,
RETURN_USER_ID                     %INT%                            ,
RETURN_FILE                        %VARCHR%(256)                    ,
RETURN_DIFF                        %VARCHR%(256)                    ,
RETURN_TESTCASES                   %VARCHR%(256)                    ,
RETURN_EVIDENCES                   %VARCHR%(256)                    ,
CLOSE_DATE                         %DATETIME6%                      ,
CLOSE_USER_ID                      %INT%                            ,
CLOSE_REVISION                     %VARCHR%(64)                     ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (FILE_M_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_FILE_MANAGEMENT_INITIAL
(
FILE_M_ID                          %INT%                            , -- 識別シーケンス申請No

FILE_STATUS_ID                     %INT%                            ,
FILE_ID                            %INT%                            ,
REQUIRE_DATE                       %DATETIME6%                      ,
REQUIRE_USER_ID                    %INT%                            ,
REQUIRE_TICKET                     %VARCHR%(8)                      ,
REQUIRE_ABSTRUCT                   %VARCHR%(4000)                   ,
REQUIRE_SCHEDULEDATE               %DATETIME6%                      ,
ASSIGN_DATE                        %DATETIME6%                      ,
ASSIGN_USER_ID                     %INT%                            ,
ASSIGN_FILE                        %VARCHR%(256)                    ,
ASSIGN_REVISION                    %VARCHR%(64)                     ,
RETURN_DATE                        %DATETIME6%                      ,
RETURN_USER_ID                     %INT%                            ,
RETURN_FILE                        %VARCHR%(256)                    ,
RETURN_DIFF                        %VARCHR%(256)                    ,
RETURN_TESTCASES                   %VARCHR%(256)                    ,
RETURN_EVIDENCES                   %VARCHR%(256)                    ,
CLOSE_DATE                         %DATETIME6%                      ,
CLOSE_USER_ID                      %INT%                            ,
CLOSE_REVISION                     %VARCHR%(64)                     ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (FILE_M_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_FILE_MANAGEMENT_JNL
(
JOURNAL_SEQ_NO                     %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR% (8)                     , -- 履歴用変更種別

FILE_M_ID                          %INT%                            , -- 識別シーケンス申請No

FILE_STATUS_ID                     %INT%                            ,
FILE_ID                            %INT%                            ,
REQUIRE_DATE                       %DATETIME6%                      ,
REQUIRE_USER_ID                    %INT%                            ,
REQUIRE_TICKET                     %VARCHR%(8)                      ,
REQUIRE_ABSTRUCT                   %VARCHR%(4000)                   ,
REQUIRE_SCHEDULEDATE               %DATETIME6%                      ,
ASSIGN_DATE                        %DATETIME6%                      ,
ASSIGN_USER_ID                     %INT%                            ,
ASSIGN_FILE                        %VARCHR%(256)                    ,
ASSIGN_REVISION                    %VARCHR%(64)                     ,
RETURN_DATE                        %DATETIME6%                      ,
RETURN_USER_ID                     %INT%                            ,
RETURN_FILE                        %VARCHR%(256)                    ,
RETURN_DIFF                        %VARCHR%(256)                    ,
RETURN_TESTCASES                   %VARCHR%(256)                    ,
RETURN_EVIDENCES                   %VARCHR%(256)                    ,
CLOSE_DATE                         %DATETIME6%                      ,
CLOSE_USER_ID                      %INT%                            ,
CLOSE_REVISION                     %VARCHR%(64)                     ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_FILE_MANAGEMENT_INITIAL_JNL
(
JOURNAL_SEQ_NO                     %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR% (8)                     , -- 履歴用変更種別

FILE_M_ID                          %INT%                            , -- 識別シーケンス申請No

FILE_STATUS_ID                     %INT%                            ,
FILE_ID                            %INT%                            ,
REQUIRE_DATE                       %DATETIME6%                      ,
REQUIRE_USER_ID                    %INT%                            ,
REQUIRE_TICKET                     %VARCHR%(8)                      ,
REQUIRE_ABSTRUCT                   %VARCHR%(4000)                   ,
REQUIRE_SCHEDULEDATE               %DATETIME6%                      ,
ASSIGN_DATE                        %DATETIME6%                      ,
ASSIGN_USER_ID                     %INT%                            ,
ASSIGN_FILE                        %VARCHR%(256)                    ,
ASSIGN_REVISION                    %VARCHR%(64)                     ,
RETURN_DATE                        %DATETIME6%                      ,
RETURN_USER_ID                     %INT%                            ,
RETURN_FILE                        %VARCHR%(256)                    ,
RETURN_DIFF                        %VARCHR%(256)                    ,
RETURN_TESTCASES                   %VARCHR%(256)                    ,
RETURN_EVIDENCES                   %VARCHR%(256)                    ,
CLOSE_DATE                         %DATETIME6%                      ,
CLOSE_USER_ID                      %INT%                            ,
CLOSE_REVISION                     %VARCHR%(64)                     ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_MATERIAL_IF_INFO
(
ROW_ID                             %INT%                            , -- 識別シーケンス項番

REMORT_REPO_URL                    %VARCHR%(256)                    ,
BRANCH                             %VARCHR%(256)                    ,
CLONE_REPO_DIR                     %VARCHR%(256)                    ,
PASSWORD                           %VARCHR%(128)                    ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_MATERIAL_IF_INFO_JNL
(
JOURNAL_SEQ_NO                     %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR% (8)                     , -- 履歴用変更種別

ROW_ID                             %INT%                            , -- 識別シーケンス項番

REMORT_REPO_URL                    %VARCHR%(256)                    ,
BRANCH                             %VARCHR%(256)                    ,
CLONE_REPO_DIR                     %VARCHR%(256)                    ,
PASSWORD                           %VARCHR%(128)                    ,

NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


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
       [%CONCAT_HEAD/%]TAB_B.DIR_NAME_FULLPATH[%CONCAT_MID/%]TAB_A.FILE_NAME[%CONCAT_TAIL/%] AS FILE_NAME_FULLPATH                                                 ,
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
       [%CONCAT_HEAD/%]TAB_B.DIR_NAME_FULLPATH[%CONCAT_MID/%]TAB_A.FILE_NAME[%CONCAT_TAIL/%] AS FILE_NAME_FULLPATH                                                 ,
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

