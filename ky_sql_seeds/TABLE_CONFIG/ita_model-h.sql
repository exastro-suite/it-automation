-- *****************************************************************************
-- *** ***** OpenStack Tables                                                ***
-- *****************************************************************************
CREATE TABLE B_OPENST_DETAIL_STATUS
(

STATUS_ID                         %INT%                            ,
STATUS_NAME                       %VARCHR%(32)                     ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (STATUS_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_OPENST_DETAIL_STATUS_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

STATUS_ID                         %INT%                            ,
STATUS_NAME                       %VARCHR%(32)                     ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_OPENST_IF_INFO
(

OPENST_IF_INFO_ID                 %INT%                            ,
OPENST_PROTOCOL                   %VARCHR%(8)                      ,
OPENST_HOSTNAME                   %VARCHR%(128)                    ,
OPENST_PORT                       %INT%                            ,
OPENST_USER                       %VARCHR%(30)                     ,
OPENST_PASSWORD                   %VARCHR%(30)                     ,
OPENST_REFRESH_INTERVAL           %INT%                            ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (OPENST_IF_INFO_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_OPENST_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

OPENST_IF_INFO_ID                 %INT%                            ,
OPENST_PROTOCOL                   %VARCHR%(8)                      ,
OPENST_HOSTNAME                   %VARCHR%(128)                    ,
OPENST_PORT                       %INT%                            ,
OPENST_USER                       %VARCHR%(30)                     ,
OPENST_PASSWORD                   %VARCHR%(30)                     ,
OPENST_REFRESH_INTERVAL           %INT%                            ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_OPENST_MASTER_SYNC
(

TENANT_ID                         %VARCHR%(45)                     ,
NAME                              %VARCHR%(128)                    ,
VALUE                             %LONGTEXT%                       ,

DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             -- 最終更新ユーザ
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_OPENST_PROJECT_INFO
(

OPENST_PROJECT_ID                 %VARCHR%(128)                    ,
OPENST_PROJECT_NAME               %VARCHR%(128)                    ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                              -- 最終更新ユーザ
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_OPENST_RUN_MODE
(

RUN_MODE_ID                       %INT%                            ,
RUN_MODE_NAME                     %VARCHR%(32)                     ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (RUN_MODE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_OPENST_RUN_MODE_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

RUN_MODE_ID                       %INT%                            ,
RUN_MODE_NAME                     %VARCHR%(32)                     ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_OPENST_STATUS
(

STATUS_ID                         %INT%                            ,
STATUS_NAME                       %VARCHR%(32)                     ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (STATUS_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_OPENST_STATUS_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

STATUS_ID                         %INT%                            ,
STATUS_NAME                       %VARCHR%(32)                     ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_OPENST_VARS_ASSIGN
(

ASSIGN_ID                         %INT%                            ,
OPERATION_NO_UAPK                 %INT%                            ,
PATTERN_ID                        %INT%                            ,
SYSTEM_ID                         %VARCHR%(64)                     ,
VARS_ENTRY                        %VARCHR%(4000)                   ,
ASSIGN_SEQ                        %INT%                            ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (ASSIGN_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_OPENST_VARS_ASSIGN_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

ASSIGN_ID                         %INT%                            ,
OPERATION_NO_UAPK                 %INT%                            ,
PATTERN_ID                        %INT%                            ,
SYSTEM_ID                         %VARCHR%(64)                     ,
VARS_ENTRY                        %VARCHR%(4000)                   ,
ASSIGN_SEQ                        %INT%                            ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_OPENST_RESULT_DETAIL
(

RESULT_DETAIL_ID                  %INT%                            ,
EXECUTION_NO                      %INT%                            ,
STATUS_ID                         %INT%                            ,
STACK_ID                          %VARCHR%(64)                     ,
STACK_URL                         %VARCHR%(512)                    ,
SYSTEM_ID                         %VARCHR%(64)                     ,
SYSTEM_NAME                       %VARCHR%(45)                     ,
REQUEST_TEMPLATE                  %VARCHR%(2014)                   ,
RESPONSE_JSON                     %VARCHR%(4000)                   ,
RESPONSE_MESSAGE                  %VARCHR%(256)                    ,
TIME_START                        %DATETIME6%                      ,
TIME_END                          %DATETIME6%                      ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (RESULT_DETAIL_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_OPENST_RESULT_DETAIL_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

RESULT_DETAIL_ID                  %INT%                            ,
EXECUTION_NO                      %INT%                            ,
STATUS_ID                         %INT%                            ,
STACK_ID                          %VARCHR%(64)                     ,
STACK_URL                         %VARCHR%(512)                    ,
SYSTEM_ID                         %VARCHR%(64)                     ,
SYSTEM_NAME                       %VARCHR%(45)                     ,
REQUEST_TEMPLATE                  %VARCHR%(2014)                   ,
RESPONSE_JSON                     %VARCHR%(4000)                   ,
RESPONSE_MESSAGE                  %VARCHR%(256)                    ,
TIME_START                        %DATETIME6%                      ,
TIME_END                          %DATETIME6%                      ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_OPENST_RESULT_MNG
(

EXECUTION_NO                      %INT%                            ,
STATUS_ID                         %INT%                            ,
EXECUTION_USER                    %VARCHR%(80)                     , -- 実行ユーザ
SYMPHONY_NAME                     %VARCHR%(128)                    , -- シンフォニークラス名
PATTERN_ID                        %INT%                            ,
I_PATTERN_NAME                    %VARCHR%(256)                    ,
I_TIME_LIMIT                      %INT%                            ,
OPERATION_NO_UAPK                 %INT%                            ,
I_OPERATION_NAME                  %VARCHR%(128)                    ,
I_OPERATION_NO_IDBH               %INT%                            ,
TIME_BOOK                         %DATETIME6%                      ,
TIME_START                        %DATETIME6%                      ,
TIME_END                          %DATETIME6%                      ,
HEAT_INPUT                        %VARCHR%(1024)                   ,
HEAT_RESULT                       %VARCHR%(1024)                   ,
RUN_MODE                          %INT%                            ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (EXECUTION_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_OPENST_RESULT_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

EXECUTION_NO                      %INT%                            ,
STATUS_ID                         %INT%                            ,
EXECUTION_USER                    %VARCHR%(80)                     , -- 実行ユーザ
SYMPHONY_NAME                     %VARCHR%(128)                    , -- シンフォニークラス名
PATTERN_ID                        %INT%                            ,
I_PATTERN_NAME                    %VARCHR%(256)                    ,
I_TIME_LIMIT                      %INT%                            ,
OPERATION_NO_UAPK                 %INT%                            ,
I_OPERATION_NAME                  %VARCHR%(128)                    ,
I_OPERATION_NO_IDBH               %INT%                            ,
TIME_BOOK                         %DATETIME6%                      ,
TIME_START                        %DATETIME6%                      ,
TIME_END                          %DATETIME6%                      ,
HEAT_INPUT                        %VARCHR%(1024)                   ,
HEAT_RESULT                       %VARCHR%(1024)                   ,
RUN_MODE                          %INT%                            ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


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
        [%CONCAT_HEAD/%]PATTERN_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]PATTERN_NAME[%CONCAT_TAIL/%] PATTERN,
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
        [%CONCAT_HEAD/%]PATTERN_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]PATTERN_NAME[%CONCAT_TAIL/%] PATTERN,
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

