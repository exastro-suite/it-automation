-- *****************************************************************************
-- *** ***** OpenStack Tables                                                ***
-- *****************************************************************************
CREATE TABLE B_OPENST_DETAIL_STATUS
(

STATUS_ID                         NUMBER                           ,
STATUS_NAME                       VARCHAR2(32)                     ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY (STATUS_ID)
);

CREATE TABLE B_OPENST_DETAIL_STATUS_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

STATUS_ID                         NUMBER                           ,
STATUS_NAME                       VARCHAR2(32)                     ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE B_OPENST_IF_INFO
(

OPENST_IF_INFO_ID                 NUMBER                           ,
OPENST_PROTOCOL                   VARCHAR2(8)                      ,
OPENST_HOSTNAME                   VARCHAR2(128)                    ,
OPENST_PORT                       NUMBER                           ,
OPENST_USER                       VARCHAR2(30)                     ,
OPENST_PASSWORD                   VARCHAR2(30)                     ,
OPENST_REFRESH_INTERVAL           NUMBER                           ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY (OPENST_IF_INFO_ID)
);

CREATE TABLE B_OPENST_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

OPENST_IF_INFO_ID                 NUMBER                           ,
OPENST_PROTOCOL                   VARCHAR2(8)                      ,
OPENST_HOSTNAME                   VARCHAR2(128)                    ,
OPENST_PORT                       NUMBER                           ,
OPENST_USER                       VARCHAR2(30)                     ,
OPENST_PASSWORD                   VARCHAR2(30)                     ,
OPENST_REFRESH_INTERVAL           NUMBER                           ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE B_OPENST_MASTER_SYNC
(

TENANT_ID                         VARCHAR2(45)                     ,
NAME                              VARCHAR2(128)                    ,
VALUE                             CLOB                             ,

DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            -- 最終更新ユーザ
);

CREATE TABLE B_OPENST_PROJECT_INFO
(

OPENST_PROJECT_ID                 VARCHAR2(128)                    ,
OPENST_PROJECT_NAME               VARCHAR2(128)                    ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                             -- 最終更新ユーザ
);

CREATE TABLE B_OPENST_RUN_MODE
(

RUN_MODE_ID                       NUMBER                           ,
RUN_MODE_NAME                     VARCHAR2(32)                     ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY (RUN_MODE_ID)
);

CREATE TABLE B_OPENST_RUN_MODE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

RUN_MODE_ID                       NUMBER                           ,
RUN_MODE_NAME                     VARCHAR2(32)                     ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE B_OPENST_STATUS
(

STATUS_ID                         NUMBER                           ,
STATUS_NAME                       VARCHAR2(32)                     ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY (STATUS_ID)
);

CREATE TABLE B_OPENST_STATUS_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

STATUS_ID                         NUMBER                           ,
STATUS_NAME                       VARCHAR2(32)                     ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE B_OPENST_VARS_ASSIGN
(

ASSIGN_ID                         NUMBER                           ,
OPERATION_NO_UAPK                 NUMBER                           ,
PATTERN_ID                        NUMBER                           ,
SYSTEM_ID                         VARCHAR2(64)                     ,
VARS_ENTRY                        VARCHAR2(4000)                   ,
ASSIGN_SEQ                        NUMBER                           ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY (ASSIGN_ID)
);

CREATE TABLE B_OPENST_VARS_ASSIGN_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

ASSIGN_ID                         NUMBER                           ,
OPERATION_NO_UAPK                 NUMBER                           ,
PATTERN_ID                        NUMBER                           ,
SYSTEM_ID                         VARCHAR2(64)                     ,
VARS_ENTRY                        VARCHAR2(4000)                   ,
ASSIGN_SEQ                        NUMBER                           ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE C_OPENST_RESULT_DETAIL
(

RESULT_DETAIL_ID                  NUMBER                           ,
EXECUTION_NO                      NUMBER                           ,
STATUS_ID                         NUMBER                           ,
STACK_ID                          VARCHAR2(64)                     ,
STACK_URL                         VARCHAR2(512)                    ,
SYSTEM_ID                         VARCHAR2(64)                     ,
SYSTEM_NAME                       VARCHAR2(45)                     ,
REQUEST_TEMPLATE                  VARCHAR2(2014)                   ,
RESPONSE_JSON                     VARCHAR2(4000)                   ,
RESPONSE_MESSAGE                  VARCHAR2(256)                    ,
TIME_START                        TIMESTAMP                        ,
TIME_END                          TIMESTAMP                        ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY (RESULT_DETAIL_ID)
);

CREATE TABLE C_OPENST_RESULT_DETAIL_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

RESULT_DETAIL_ID                  NUMBER                           ,
EXECUTION_NO                      NUMBER                           ,
STATUS_ID                         NUMBER                           ,
STACK_ID                          VARCHAR2(64)                     ,
STACK_URL                         VARCHAR2(512)                    ,
SYSTEM_ID                         VARCHAR2(64)                     ,
SYSTEM_NAME                       VARCHAR2(45)                     ,
REQUEST_TEMPLATE                  VARCHAR2(2014)                   ,
RESPONSE_JSON                     VARCHAR2(4000)                   ,
RESPONSE_MESSAGE                  VARCHAR2(256)                    ,
TIME_START                        TIMESTAMP                        ,
TIME_END                          TIMESTAMP                        ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE C_OPENST_RESULT_MNG
(

EXECUTION_NO                      NUMBER                           ,
STATUS_ID                         NUMBER                           ,
EXECUTION_USER                    VARCHAR2(80)                     , -- 実行ユーザ
SYMPHONY_NAME                     VARCHAR2(128)                    , -- シンフォニークラス名
PATTERN_ID                        NUMBER                           ,
I_PATTERN_NAME                    VARCHAR2(256)                    ,
I_TIME_LIMIT                      NUMBER                           ,
OPERATION_NO_UAPK                 NUMBER                           ,
I_OPERATION_NAME                  VARCHAR2(128)                    ,
I_OPERATION_NO_IDBH               NUMBER                           ,
TIME_BOOK                         TIMESTAMP                        ,
TIME_START                        TIMESTAMP                        ,
TIME_END                          TIMESTAMP                        ,
HEAT_INPUT                        VARCHAR2(1024)                   ,
HEAT_RESULT                       VARCHAR2(1024)                   ,
RUN_MODE                          NUMBER                           ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY (EXECUTION_NO)
);

CREATE TABLE C_OPENST_RESULT_MNG_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

EXECUTION_NO                      NUMBER                           ,
STATUS_ID                         NUMBER                           ,
EXECUTION_USER                    VARCHAR2(80)                     , -- 実行ユーザ
SYMPHONY_NAME                     VARCHAR2(128)                    , -- シンフォニークラス名
PATTERN_ID                        NUMBER                           ,
I_PATTERN_NAME                    VARCHAR2(256)                    ,
I_TIME_LIMIT                      NUMBER                           ,
OPERATION_NO_UAPK                 NUMBER                           ,
I_OPERATION_NAME                  VARCHAR2(128)                    ,
I_OPERATION_NO_IDBH               NUMBER                           ,
TIME_BOOK                         TIMESTAMP                        ,
TIME_START                        TIMESTAMP                        ,
TIME_END                          TIMESTAMP                        ,
HEAT_INPUT                        VARCHAR2(1024)                   ,
HEAT_RESULT                       VARCHAR2(1024)                   ,
RUN_MODE                          NUMBER                           ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);


CREATE VIEW D_OPENST_IF_INFO AS
SELECT
        OPENST_IF_INFO_ID        ,
        OPENST_PROTOCOL          ,
        OPENST_HOSTNAME          ,
        OPENST_PORT              ,
        OPENST_USER              ,
        OPENST_PASSWORD          ,
        OPENST_REFRESH_INTERVAL  ,
        DISP_SEQ                 ,
        NOTE                     ,
        DISUSE_FLAG              ,
        LAST_UPDATE_TIMESTAMP    ,
        LAST_UPDATE_USER
FROM B_OPENST_IF_INFO
;

CREATE VIEW D_OPENST_IF_INFO_JNL AS
SELECT
        JOURNAL_SEQ_NO           ,
        JOURNAL_REG_DATETIME     ,
        JOURNAL_ACTION_CLASS     ,
        OPENST_IF_INFO_ID        ,
        OPENST_PROTOCOL          ,
        OPENST_HOSTNAME          ,
        OPENST_PORT              ,
        OPENST_USER              ,
        OPENST_PASSWORD          ,
        OPENST_REFRESH_INTERVAL  ,
        DISP_SEQ                 ,
        NOTE                     ,
        DISUSE_FLAG              ,
        LAST_UPDATE_TIMESTAMP    ,
        LAST_UPDATE_USER
FROM B_OPENST_IF_INFO_JNL
;

CREATE VIEW D_OPENST_LNS_INS_RUN_MODE AS
SELECT
        RUN_MODE_ID              ,
        RUN_MODE_NAME            ,
        DISP_SEQ                 ,
        NOTE                     ,
        DISUSE_FLAG              ,
        LAST_UPDATE_TIMESTAMP    ,
        LAST_UPDATE_USER
FROM B_OPENST_RUN_MODE
;

CREATE VIEW D_OPENST_LNS_INS_RUN_MODE_JNL AS
SELECT
        JOURNAL_SEQ_NO           ,
        JOURNAL_REG_DATETIME     ,
        JOURNAL_ACTION_CLASS     ,
        RUN_MODE_ID              ,
        RUN_MODE_NAME            ,
        DISP_SEQ                 ,
        NOTE                     ,
        DISUSE_FLAG              ,
        LAST_UPDATE_TIMESTAMP    ,
        LAST_UPDATE_USER
FROM B_OPENST_RUN_MODE_JNL
;

CREATE VIEW D_OPENST_STATUS AS
SELECT
        STATUS_ID                ,
        STATUS_NAME              ,
        DISP_SEQ                 ,
        NOTE                     ,
        DISUSE_FLAG              ,
        LAST_UPDATE_TIMESTAMP    ,
        LAST_UPDATE_USER
FROM B_OPENST_STATUS
;

CREATE VIEW D_OPENST_STATUS_JNL AS
SELECT
        JOURNAL_SEQ_NO           ,
        JOURNAL_REG_DATETIME     ,
        JOURNAL_ACTION_CLASS     ,
        STATUS_ID                ,
        STATUS_NAME              ,
        DISP_SEQ                 ,
        NOTE                     ,
        DISUSE_FLAG              ,
        LAST_UPDATE_TIMESTAMP    ,
        LAST_UPDATE_USER
FROM B_OPENST_STATUS_JNL
;

CREATE VIEW E_OPENST_PATTERN AS 
SELECT 
        PATTERN_ID                   ,
        PATTERN_NAME                 ,
        PATTERN_ID || ':' || PATTERN_NAME PATTERN,
        ITA_EXT_STM_ID               ,
        TIME_LIMIT                   ,
        ANS_HOST_DESIGNATE_TYPE_ID   ,
        ANS_PARALLEL_EXE             ,
        ANS_WINRM_ID                 ,
        OPENST_TEMPLATE              ,
        OPENST_ENVIRONMENT           ,
        DISP_SEQ                     ,
        NOTE                         ,
        DISUSE_FLAG                  ,
        LAST_UPDATE_TIMESTAMP        ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 9
;

CREATE VIEW E_OPENST_PATTERN_JNL AS 
SELECT 
        JOURNAL_SEQ_NO               ,
        JOURNAL_REG_DATETIME         ,
        JOURNAL_ACTION_CLASS         ,
        PATTERN_ID                   ,
        PATTERN_NAME                 ,
        PATTERN_ID || ':' || PATTERN_NAME PATTERN,
        ITA_EXT_STM_ID               ,
        TIME_LIMIT                   ,
        ANS_HOST_DESIGNATE_TYPE_ID   ,
        ANS_PARALLEL_EXE             ,
        ANS_WINRM_ID                 ,
        OPENST_TEMPLATE              ,
        OPENST_ENVIRONMENT           ,
        DISP_SEQ                     ,
        NOTE                         ,
        DISUSE_FLAG                  ,
        LAST_UPDATE_TIMESTAMP        ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH_JNL TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 9
;

CREATE VIEW E_OPENST_RESULT_MNG AS
SELECT
        TAB_A.EXECUTION_NO           ,
        TAB_A.SYMPHONY_NAME          ,
        TAB_A.EXECUTION_USER         ,
        TAB_A.STATUS_ID              ,
        TAB_C.STATUS_NAME            ,
        TAB_A.PATTERN_ID             ,
        TAB_A.I_PATTERN_NAME         ,
        TAB_A.I_TIME_LIMIT           ,
        TAB_A.OPERATION_NO_UAPK      ,
        TAB_A.I_OPERATION_NAME       ,
        TAB_A.I_OPERATION_NO_IDBH    ,
        TAB_A.TIME_BOOK              ,
        TAB_A.TIME_START             ,
        TAB_A.TIME_END               ,
        TAB_A.HEAT_INPUT             ,
        TAB_A.HEAT_RESULT            ,
        TAB_A.RUN_MODE               ,
        TAB_D.RUN_MODE_NAME          ,
        TAB_A.DISP_SEQ               ,
        TAB_A.NOTE                   ,
        TAB_A.DISUSE_FLAG            ,
        TAB_A.LAST_UPDATE_TIMESTAMP  ,
        TAB_A.LAST_UPDATE_USER
FROM C_OPENST_RESULT_MNG TAB_A
LEFT JOIN E_OPENST_PATTERN TAB_B ON (TAB_B.PATTERN_ID = TAB_A.PATTERN_ID)
LEFT JOIN D_OPENST_STATUS  TAB_C ON (TAB_A.STATUS_ID = TAB_C.STATUS_ID)
LEFT JOIN D_OPENST_LNS_INS_RUN_MODE TAB_D ON (TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID)
;

CREATE VIEW E_OPENST_RESULT_MNG_JNL AS
SELECT
        TAB_A.JOURNAL_SEQ_NO         ,
        TAB_A.SYMPHONY_NAME          ,
        TAB_A.EXECUTION_USER         ,
        TAB_A.JOURNAL_REG_DATETIME   ,
        TAB_A.JOURNAL_ACTION_CLASS   ,
        TAB_A.EXECUTION_NO           ,
        TAB_A.STATUS_ID              ,
        TAB_C.STATUS_NAME            ,
        TAB_A.PATTERN_ID             ,
        TAB_A.I_PATTERN_NAME         ,
        TAB_A.I_TIME_LIMIT           ,
        TAB_A.OPERATION_NO_UAPK      ,
        TAB_A.I_OPERATION_NAME       ,
        TAB_A.I_OPERATION_NO_IDBH    ,
        TAB_A.TIME_BOOK              ,
        TAB_A.TIME_START             ,
        TAB_A.TIME_END               ,
        TAB_A.HEAT_INPUT             ,
        TAB_A.HEAT_RESULT            ,
        TAB_A.RUN_MODE               ,
        TAB_D.RUN_MODE_NAME          ,
        TAB_A.DISP_SEQ               ,
        TAB_A.NOTE                   ,
        TAB_A.DISUSE_FLAG            ,
        TAB_A.LAST_UPDATE_TIMESTAMP  ,
        TAB_A.LAST_UPDATE_USER
FROM C_OPENST_RESULT_MNG_JNL TAB_A
LEFT JOIN E_OPENST_PATTERN TAB_B ON (TAB_B.PATTERN_ID = TAB_A.PATTERN_ID)
LEFT JOIN D_OPENST_STATUS TAB_C ON (TAB_A.STATUS_ID = TAB_C.STATUS_ID)
LEFT JOIN D_OPENST_LNS_INS_RUN_MODE TAB_D ON (TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID)
;

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OPENST_DETAIL_STATUS_RIC',6);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OPENST_DETAIL_STATUS_JSQ',6);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OPENST_IF_INFO_RIC',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OPENST_IF_INFO_JSQ',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OPENST_RUN_MODE_RIC',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OPENST_RUN_MODE_JSQ',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OPENST_STATUS_RIC',11);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OPENST_STATUS_JSQ',11);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OPENST_VARS_ASSIGN_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OPENST_VARS_ASSIGN_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_OPENST_RESULT_DETAIL_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_OPENST_RESULT_DETAIL_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_OPENST_RESULT_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_OPENST_RESULT_MNG_JSQ',1);


INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070001,'OpenStack','openstack.png',150,'OpenStack','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-70001,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070001,'OpenStack','openstack.png',150,'OpenStack','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070001,2100070001,'Interface information',NULL,NULL,NULL,1,0,1,1,20,'if_info_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-70002,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070001,2100070001,'Interface information',NULL,NULL,NULL,1,0,1,1,20,'if_info_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070002,2100070001,'Movement list',NULL,NULL,NULL,1,0,1,1,30,'pattern_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-70003,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070002,2100070001,'Movement list',NULL,NULL,NULL,1,0,1,1,30,'pattern_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070003,2100070001,'Substitution value list',NULL,NULL,NULL,1,0,1,1,40,'vars_assign_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-70004,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070003,2100070001,'Substitution value list',NULL,NULL,NULL,1,0,1,1,40,'vars_assign_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070004,2100070001,'Execution',NULL,NULL,NULL,1,0,1,1,50,'register_execution','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-70005,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070004,2100070001,'Execution',NULL,NULL,NULL,1,0,1,1,50,'register_execution','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070005,2100070001,'Check operation status',NULL,NULL,NULL,1,0,2,2,60,'monitor_execution','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-70006,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070005,2100070001,'Check operation status',NULL,NULL,NULL,1,0,2,2,60,'monitor_execution','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070006,2100070001,'Result list',NULL,NULL,NULL,1,0,1,2,70,'result_mng','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-70007,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070006,2100070001,'Result list',NULL,NULL,NULL,1,0,1,2,70,'result_mng','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070007,2100070001,'Result details',NULL,NULL,NULL,1,0,1,2,80,'result_detail','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-70008,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070007,2100070001,'Result details',NULL,NULL,NULL,1,0,1,2,80,'result_detail','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100903,'o1a','5ebbc37e034d6874a2af59eb04beaa52','OpenStack synchronization management procedure','sample@xxx.bbb.ccc','OpenStack synchronization management procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100903,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100903,'o1a','5ebbc37e034d6874a2af59eb04beaa52','OpenStack synchronization management procedure','sample@xxx.bbb.ccc','OpenStack synchronization management procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100902,'o1b','5ebbc37e034d6874a2af59eb04beaa52','OpenStack execution procedure','sample@xxx.bbb.ccc','OpenStack execution procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100902,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100902,'o1b','5ebbc37e034d6874a2af59eb04beaa52','OpenStack execution procedure','sample@xxx.bbb.ccc','OpenStack execution procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100901,'o1c','5ebbc37e034d6874a2af59eb04beaa52','OpenStack execution checking procedure','sample@xxx.bbb.ccc','OpenStack execution checking procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100901,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100901,'o1c','5ebbc37e034d6874a2af59eb04beaa52','OpenStack execution checking procedure','sample@xxx.bbb.ccc','OpenStack execution checking procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070001,1,2100070001,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100902,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070001,1,2100070001,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070002,1,2100070002,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100903,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070002,1,2100070002,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070003,1,2100070003,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100904,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070003,1,2100070003,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070004,1,2100070004,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100905,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070004,1,2100070004,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070005,1,2100070005,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100906,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070005,1,2100070005,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070006,1,2100070006,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100907,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070006,1,2100070006,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100070007,1,2100070007,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100908,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100070007,1,2100070007,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000020,3600,7200,'B_OPENST_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Substitution value list(OpenStack)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000020,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000020,3600,7200,'B_OPENST_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Substitution value list(OpenStack)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000021,3600,7200,'C_OPENST_RESULT_MNG','EXECUTION_NO','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Result list(OpenStack)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000021,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000021,3600,7200,'C_OPENST_RESULT_MNG','EXECUTION_NO','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Result list(OpenStack)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_ITA_EXT_STM_MASTER (ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'OpenStack','openstack_driver',9,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_ITA_EXT_STM_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',9,'OpenStack','openstack_driver',9,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_OPENST_DETAIL_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(0,'Cancel',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_DETAIL_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',0,'Cancel',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_DETAIL_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Build in progress',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_DETAIL_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Build in progress',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_DETAIL_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Failure (HEAT error)',3,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_DETAIL_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Failure (HEAT error)',3,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_DETAIL_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Failure (other errors)',4,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_DETAIL_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',3,'Failure (other errors)',4,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_DETAIL_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Completed',5,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_DETAIL_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',4,'Completed',5,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_OPENST_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Unexecuted',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Unexecuted',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Unexecuted (schedule)',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Unexecuted (schedule)',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Preparing',3,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',3,'Preparing',3,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Executing',4,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',4,'Executing',4,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'Emergency stop - processing',5,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',5,'Emergency stop - processing',5,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'Emergency stop - completed',6,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',6,'Emergency stop - completed',6,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'Failure',7,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',7,'Failure',7,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'Completed (partial failure)',8,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',8,'Completed (partial failure)',8,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'Completed',9,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',9,'Completed',9,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'Schedule cancellation',10,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',10,'Schedule cancellation',10,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_OPENST_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Normal',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Normal',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Dry run',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Dry run',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_OPENST_IF_INFO (OPENST_IF_INFO_ID,OPENST_PROTOCOL,OPENST_HOSTNAME,OPENST_USER,OPENST_PASSWORD,OPENST_REFRESH_INTERVAL,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'http','Write the endpoint of Openstack "Identity" service  as「xxx.xxx.xxx.xxx:xxx/vx.x」','dummy','dummy','3000',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_OPENST_IF_INFO_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,OPENST_IF_INFO_ID,OPENST_PROTOCOL,OPENST_HOSTNAME,OPENST_USER,OPENST_PASSWORD,OPENST_REFRESH_INTERVAL,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'http','Write the endpoint of Openstack "Identity" service  as「xxx.xxx.xxx.xxx:xxx/vx.x」','dummy','dummy','3000',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);


COMMIT;

EXIT;
