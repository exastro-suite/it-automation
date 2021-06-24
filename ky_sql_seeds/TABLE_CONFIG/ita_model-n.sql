
-- *****************************************************************************
-- *** ***** CreateParameterMenu Tables                                      ***
-- *****************************************************************************
CREATE TABLE F_HOSTGROUP_LIST
(
ROW_ID                             %INT%                           , -- 識別シーケンスホストグループID

HOSTGROUP_NAME                     %VARCHR%(256)                    ,
STRENGTH                           %INT%                           ,

DISP_SEQ                           %INT%                           , -- 表示順序
ACCESS_AUTH                        TEXT                            ,
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_HOSTGROUP_LIST_JNL
(
JOURNAL_SEQ_NO                     %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR%(8)                     , -- 履歴用変更種別

ROW_ID                             %INT%                           , -- 識別シーケンスホストグループID

HOSTGROUP_NAME                     %VARCHR%(256)                    ,
STRENGTH                           %INT%                           ,

DISP_SEQ                           %INT%                           , -- 表示順序
ACCESS_AUTH                        TEXT                            ,
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_HOST_LINK_LIST
(
ROW_ID                             %INT%                           , -- 識別シーケンス項番

LOOPALARM                          %INT%                           ,
PA_HOSTGROUP                       %INT%                           ,
CH_HOSTGROUP                       %INT%                           ,

DISP_SEQ                           %INT%                           , -- 表示順序
ACCESS_AUTH                        TEXT                            ,
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_HOST_LINK_LIST_JNL
(
JOURNAL_SEQ_NO                     %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR%(8)                     , -- 履歴用変更種別

ROW_ID                             %INT%                           , -- 識別シーケンス項番

LOOPALARM                          %INT%                           ,
PA_HOSTGROUP                       %INT%                           ,
CH_HOSTGROUP                       %INT%                           ,

DISP_SEQ                           %INT%                           , -- 表示順序
ACCESS_AUTH                        TEXT                            ,
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_HOST_LINK
(
ROW_ID                             %INT%                           , -- 識別シーケンス項番

HOSTGROUP_NAME                     %INT%                           ,
OPERATION_ID                       %INT%                           ,
HOSTNAME                           %INT%                           ,

DISP_SEQ                           %INT%                           , -- 表示順序
ACCESS_AUTH                        TEXT                            ,
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_HOST_LINK_JNL
(
JOURNAL_SEQ_NO                     %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR%(8)                     , -- 履歴用変更種別

ROW_ID                             %INT%                           , -- 識別シーケンス項番

HOSTGROUP_NAME                     %INT%                           ,
OPERATION_ID                       %INT%                           ,
HOSTNAME                           %INT%                           ,

DISP_SEQ                           %INT%                           , -- 表示順序
ACCESS_AUTH                        TEXT                            ,
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_SPLIT_TARGET
(
ROW_ID                             %INT%                           , -- 識別シーケンス項番

INPUT_MENU_ID                      %INT%                           ,
OUTPUT_MENU_ID                     %INT%                           ,
DIVIDED_FLG                        %VARCHR% (1)                    ,

ACCESS_AUTH                        TEXT                            ,
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE F_SPLIT_TARGET_JNL
(
JOURNAL_SEQ_NO                     %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR%(8)                     , -- 履歴用変更種別

ROW_ID                             %INT%                           , -- 識別シーケンス項番

INPUT_MENU_ID                      %INT%                           ,
OUTPUT_MENU_ID                     %INT%                           ,
DIVIDED_FLG                        %VARCHR% (1)                    ,

ACCESS_AUTH                        TEXT                            ,
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;



-- *****************************************************************************
-- *** ***** Views                                                           ***
-- *****************************************************************************

CREATE OR REPLACE VIEW G_SPLIT_TARGET AS 
SELECT TAB_A.ROW_ID                 ,
       TAB_A.INPUT_MENU_ID          ,
       TAB_A.INPUT_MENU_ID          INPUT_MENU_ID_CLONE,
       TAB_A.INPUT_MENU_ID          INPUT_MENU_ID_CLONE_02,
       TAB_A.INPUT_MENU_ID          INPUT_MENU_ID_CLONE_03,
       TAB_A.INPUT_MENU_ID          INPUT_MENU_ID_CLONE_04,
       TAB_A.OUTPUT_MENU_ID         ,
       TAB_A.OUTPUT_MENU_ID         OUTPUT_MENU_ID_CLONE,
       TAB_A.OUTPUT_MENU_ID         OUTPUT_MENU_ID_CLONE_02,
       TAB_A.OUTPUT_MENU_ID         OUTPUT_MENU_ID_CLONE_03,
       TAB_A.OUTPUT_MENU_ID         OUTPUT_MENU_ID_CLONE_04,
       TAB_A.DIVIDED_FLG,
       TAB_A.ACCESS_AUTH            ,
       TAB_A.NOTE                   ,
       TAB_A.DISUSE_FLAG            ,
       TAB_A.LAST_UPDATE_TIMESTAMP  ,
       TAB_A.LAST_UPDATE_USER 
FROM F_SPLIT_TARGET TAB_A
;

CREATE VIEW G_SPLIT_TARGET_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO         ,
       TAB_A.JOURNAL_REG_DATETIME   ,
       TAB_A.JOURNAL_ACTION_CLASS   ,
       TAB_A.ROW_ID                 ,
       TAB_A.INPUT_MENU_ID          ,
       TAB_A.INPUT_MENU_ID          INPUT_MENU_ID_CLONE,
       TAB_A.INPUT_MENU_ID          INPUT_MENU_ID_CLONE_02,
       TAB_A.INPUT_MENU_ID          INPUT_MENU_ID_CLONE_03,
       TAB_A.INPUT_MENU_ID          INPUT_MENU_ID_CLONE_04,
       TAB_A.OUTPUT_MENU_ID         ,
       TAB_A.OUTPUT_MENU_ID         OUTPUT_MENU_ID_CLONE,
       TAB_A.OUTPUT_MENU_ID         OUTPUT_MENU_ID_CLONE_02,
       TAB_A.OUTPUT_MENU_ID         OUTPUT_MENU_ID_CLONE_03,
       TAB_A.OUTPUT_MENU_ID         OUTPUT_MENU_ID_CLONE_04,
       TAB_A.DIVIDED_FLG,
       TAB_A.ACCESS_AUTH            ,
       TAB_A.NOTE                   ,
       TAB_A.DISUSE_FLAG            ,
       TAB_A.LAST_UPDATE_TIMESTAMP  ,
       TAB_A.LAST_UPDATE_USER 
FROM F_SPLIT_TARGET_JNL TAB_A
;

CREATE VIEW G_UQ_HOST_LIST AS
SELECT SYSTEM_ID                                                    AS KY_KEY   ,
       [%CONCAT_HEAD/%]'[H]'[%CONCAT_MID/%]HOSTNAME[%CONCAT_TAIL/%] AS KY_VALUE ,
       0                                                            AS KY_SOURCE,
       9223372036854775807                                          AS STRENGTH ,
       ACCESS_AUTH                                                  AS ACCESS_AUTH,
       DISUSE_FLAG                                                              ,
       LAST_UPDATE_TIMESTAMP                                                    ,
       LAST_UPDATE_USER
FROM   C_STM_LIST
WHERE  DISUSE_FLAG = '0'
UNION
SELECT ROW_ID + 10000000                                                    AS KY_KEY   ,
       [%CONCAT_HEAD/%]'[HG]'[%CONCAT_MID/%]HOSTGROUP_NAME[%CONCAT_TAIL/%]  AS KY_VALUE ,
       1                                                                    AS KY_SOURCE,
       STRENGTH                                                             AS STRENGTH ,
       ACCESS_AUTH                                                          AS ACCESS_AUTH,
       DISUSE_FLAG                                                                      ,
       LAST_UPDATE_TIMESTAMP                                                            ,
       LAST_UPDATE_USER
FROM   F_HOSTGROUP_LIST
WHERE  DISUSE_FLAG = '0'
;

CREATE VIEW G_FLAG_MASTER AS
SELECT 1      AS FLAG_ID                ,
       '●'   AS FLAG_NAME              ,
       ''     AS ACCESS_AUTH            ,
       NULL   AS NOTE                   ,
       '0'    AS DISUSE_FLAG            ,
       NOW(6) AS LAST_UPDATE_TIMESTAMP  ,
       1      AS LAST_UPDATE_USER
;

