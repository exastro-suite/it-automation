-- *****************************************************************************
-- *** ***** CreateParameterMenu Tables                                      ***
-- *****************************************************************************
-- -------------------------
-- パラメータシート作成情報
-- -------------------------
CREATE TABLE F_CREATE_MENU_INFO
(
CREATE_MENU_ID                      %INT%                           , -- 識別シーケンス項番
MENU_NAME                           %VARCHR%(256)                    ,
PURPOSE                             %INT%                           ,
TARGET                              %INT%                           ,
VERTICAL                            %INT%                           ,
MENUGROUP_FOR_INPUT                 %INT%                           ,
MENUGROUP_FOR_SUBST                 %INT%                           ,
MENUGROUP_FOR_VIEW                  %INT%                           ,
DISP_SEQ                            %INT%                           ,
DESCRIPTION                         %VARCHR%(1024)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (CREATE_MENU_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_CREATE_MENU_INFO_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR%(8)                     , -- 履歴用変更種別

CREATE_MENU_ID                      %INT%                           , -- 識別シーケンス項番
MENU_NAME                           %VARCHR%(256)                    ,
PURPOSE                             %INT%                           ,
TARGET                              %INT%                           ,
VERTICAL                            %INT%                           ,
MENUGROUP_FOR_INPUT                 %INT%                           ,
MENUGROUP_FOR_SUBST                 %INT%                           ,
MENUGROUP_FOR_VIEW                  %INT%                           ,
DISP_SEQ                            %INT%                           ,
DESCRIPTION                         %VARCHR%(1024)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- パラメータシート項目作成情報
-- -------------------------
CREATE TABLE F_CREATE_ITEM_INFO
(
CREATE_ITEM_ID                      %INT%                           , -- 識別シーケンス項番
CREATE_MENU_ID                      %INT%                           ,
ITEM_NAME                           %VARCHR%(256)                   ,
DISP_SEQ                            %INT%                           ,
REQUIRED                            %INT%                           ,
UNIQUED                             %INT%                           ,
COL_GROUP_ID                        %INT%                           ,
INPUT_METHOD_ID                     %INT%                           ,
MAX_LENGTH                          %INT%                           ,
MULTI_MAX_LENGTH                    %INT%                           ,
PREG_MATCH                          %TEXT%                          ,
MULTI_PREG_MATCH                    %TEXT%                          ,
OTHER_MENU_LINK_ID                  %INT%                           ,
INT_MAX                             %INT%                           ,
INT_MIN                             %INT%                           ,
FLOAT_MAX                           %DOUBLE%                        ,
FLOAT_MIN                           %DOUBLE%                        ,
FLOAT_DIGIT                         %INT%                           ,
PW_MAX_LENGTH                       %INT%                           ,
UPLOAD_MAX_SIZE                     LONG                            ,
LINK_LENGTH                         %INT%                           ,
DESCRIPTION                         %VARCHR%(1024)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (CREATE_ITEM_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_CREATE_ITEM_INFO_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                    , -- 履歴用変更種別

CREATE_ITEM_ID                      %INT%                           , -- 識別シーケンス項番
CREATE_MENU_ID                      %INT%                           ,
ITEM_NAME                           %VARCHR%(256)                   ,
DISP_SEQ                            %INT%                           ,
REQUIRED                            %INT%                           ,
UNIQUED                             %INT%                           ,
COL_GROUP_ID                        %INT%                           ,
INPUT_METHOD_ID                     %INT%                           ,
MAX_LENGTH                          %INT%                           ,
MULTI_MAX_LENGTH                    %INT%                           ,
PREG_MATCH                          %TEXT%                          ,
MULTI_PREG_MATCH                    %TEXT%                          ,
OTHER_MENU_LINK_ID                  %INT%                           ,
INT_MAX                             %INT%                           ,
INT_MIN                             %INT%                           ,
FLOAT_MAX                           %DOUBLE%                        ,
FLOAT_MIN                           %DOUBLE%                        ,
FLOAT_DIGIT                         %INT%                           ,
PW_MAX_LENGTH                       %INT%                           ,
UPLOAD_MAX_SIZE                     LONG                            ,
LINK_LENGTH                         %INT%                           ,
DESCRIPTION                         %VARCHR%(1024)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- パラメータシート・テーブル紐付
-- -------------------------
CREATE TABLE F_MENU_TABLE_LINK
(
MENU_TABLE_LINK_ID                  %INT%                           , -- 識別シーケンス項番
MENU_ID                             %INT%                           ,
TABLE_NAME                          %VARCHR%(64)                    ,
KEY_COL_NAME                        %VARCHR%(64)                    ,
TABLE_NAME_JNL                      %VARCHR%(64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ

PRIMARY KEY (MENU_TABLE_LINK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_MENU_TABLE_LINK_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                    , -- 履歴用変更種別

MENU_TABLE_LINK_ID                  %INT%                           , -- 識別シーケンス項番
MENU_ID                             %INT%                           ,
TABLE_NAME                          %VARCHR%(64)                    ,
KEY_COL_NAME                        %VARCHR%(64)                    ,
TABLE_NAME_JNL                      %VARCHR%(64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- パラメータシート作成管理
-- -------------------------
CREATE TABLE F_CREATE_MENU_STATUS
(
MM_STATUS_ID                        %INT%                           , -- 識別シーケンス項番

CREATE_MENU_ID                      %INT%                           ,
STATUS_ID                           %INT%                           ,
FILE_NAME                           %VARCHR%(64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (MM_STATUS_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_CREATE_MENU_STATUS_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                    , -- 履歴用変更種別

MM_STATUS_ID                        %INT%                           , -- 識別シーケンス項番
CREATE_MENU_ID                      %INT%                           ,
STATUS_ID                           %INT%                           ,
FILE_NAME                           %VARCHR%(64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- メニュー作成ステータスマスタ
-- -------------------------
CREATE TABLE F_CM_STATUS_MASTER
(
STATUS_ID                           %INT%                           , -- 識別シーケンス項番
STATUS_NAME                         %VARCHR%(64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (STATUS_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_CM_STATUS_MASTER_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                    , -- 履歴用変更種別

STATUS_ID                           %INT%                           , -- 識別シーケンス項番
STATUS_NAME                         %VARCHR%(64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- 用途マスタ
-- -------------------------
CREATE TABLE F_PARAM_PURPOSE
(
PURPOSE_ID                          %INT%                           , -- 識別シーケンス項番
PURPOSE_NAME                        %VARCHR%(64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (PURPOSE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_PARAM_PURPOSE_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                    , -- 履歴用変更種別

PURPOSE_ID                          %INT%                           , -- 識別シーケンス項番
PURPOSE_NAME                        %VARCHR%(64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- 他メニュー連携
-- -------------------------
CREATE TABLE F_OTHER_MENU_LINK
(
LINK_ID                             %INT%                           , -- 識別シーケンス項番
MENU_ID                             %INT%                           ,
COLUMN_DISP_NAME                    %VARCHR%(4096)                   ,
TABLE_NAME                          %VARCHR%(64)                    ,
PRI_NAME                            %VARCHR%(64)                    ,
COLUMN_NAME                         %VARCHR%(64)                    ,
COLUMN_TYPE                         %INT%                           ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (LINK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_OTHER_MENU_LINK_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                    , -- 履歴用変更種別

LINK_ID                             %INT%                           , -- 識別シーケンス項番
MENU_ID                             %INT%                           ,
COLUMN_DISP_NAME                    %VARCHR%(4096)                   ,
TABLE_NAME                          %VARCHR%(64)                    ,
PRI_NAME                            %VARCHR%(64)                    ,
COLUMN_NAME                         %VARCHR%(64)                    ,
COLUMN_TYPE                         %INT%                           ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- 入力方式マスタ
-- -------------------------
CREATE TABLE F_INPUT_METHOD
(
INPUT_METHOD_ID                     %INT%                           , -- 識別シーケンス項番
INPUT_METHOD_NAME                   %VARCHR% (256)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (INPUT_METHOD_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_INPUT_METHOD_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                    , -- 履歴用変更種別

INPUT_METHOD_ID                     %INT%                           , -- 識別シーケンス項番
INPUT_METHOD_NAME                   %VARCHR% (256)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- カラムグループ管理
-- -------------------------
CREATE TABLE F_COLUMN_GROUP
(
COL_GROUP_ID                        %INT%                           , -- 識別シーケンス項番
PA_COL_GROUP_ID                     %INT%                           ,
FULL_COL_GROUP_NAME                 %VARCHR% (4096)                 ,
COL_GROUP_NAME                      %VARCHR% (256)                  ,
DISP_SEQ                            %INT%                           ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (COL_GROUP_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_COLUMN_GROUP_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                    , -- 履歴用変更種別

COL_GROUP_ID                        %INT%                           , -- 識別シーケンス項番
PA_COL_GROUP_ID                     %INT%                           ,
FULL_COL_GROUP_NAME                 %VARCHR% (4096)                 ,
COL_GROUP_NAME                      %VARCHR% (256)                  ,
DISP_SEQ                            %INT%                           ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- パラメータシート(縦)作成情報
-- -------------------------
CREATE TABLE F_CONVERT_PARAM_INFO
(
CONVERT_PARAM_ID                    %INT%                           , -- 識別シーケンス項番
CREATE_ITEM_ID                      %INT%                           ,
COL_CNT                             %INT%                           ,
REPEAT_CNT                          %INT%                           ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (CONVERT_PARAM_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_CONVERT_PARAM_INFO_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                    , -- 履歴用変更種別

CONVERT_PARAM_ID                    %INT%                           , -- 識別シーケンス項番
CREATE_ITEM_ID                      %INT%                           ,
COL_CNT                             %INT%                           ,
REPEAT_CNT                          %INT%                           ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- パラメータシート縦横変換管理
-- -------------------------
CREATE TABLE F_COL_TO_ROW_MNG (
ROW_ID                        %INT%             ,
FROM_MENU_ID                  %INT%             ,
TO_MENU_ID                    %INT%             ,
PURPOSE                       %INT%             ,
START_COL_NAME                %VARCHR% (64)     ,
COL_CNT                       %INT%             ,
REPEAT_CNT                    %INT%             ,
CHANGED_FLG                   %VARCHR% (1)      ,
ACCESS_AUTH                  TEXT               ,
NOTE                          %VARCHR% (4000)   ,
DISUSE_FLAG                   %VARCHR% (1)      ,
LAST_UPDATE_TIMESTAMP         %DATETIME6%       ,
LAST_UPDATE_USER              %INT%             ,
PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_COL_TO_ROW_MNG_JNL (
JOURNAL_SEQ_NO                %INT%             ,
JOURNAL_REG_DATETIME          %DATETIME6%       ,
JOURNAL_ACTION_CLASS          %VARCHR% (8)      ,
ROW_ID                        %INT%             ,
FROM_MENU_ID                  %INT%             ,
TO_MENU_ID                    %INT%             ,
PURPOSE                       %INT%             ,
START_COL_NAME                %VARCHR% (64)     ,
COL_CNT                       %INT%             ,
REPEAT_CNT                    %INT%             ,
CHANGED_FLG                   %VARCHR% (1)      ,
ACCESS_AUTH                  TEXT               ,
NOTE                          %VARCHR% (4000)   ,
DISUSE_FLAG                   %VARCHR% (1)      ,
LAST_UPDATE_TIMESTAMP         %DATETIME6%       ,
LAST_UPDATE_USER              %INT%             ,
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;




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
       [%CONCAT_HEAD/%]TAB_C.MENU_GROUP_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_B.MENU_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.COLUMN_DISP_NAME[%CONCAT_TAIL/%] LINK_PULLDOWN,
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
       [%CONCAT_HEAD/%]TAB_C.MENU_GROUP_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_B.MENU_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.COLUMN_DISP_NAME[%CONCAT_TAIL/%] LINK_PULLDOWN,
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
       TAB_A.DESCRIPTION,
       TAB_C.FULL_COL_GROUP_NAME,
       CASE
           WHEN TAB_C.FULL_COL_GROUP_NAME IS NULL THEN [%CONCAT_HEAD/%]TAB_B.MENU_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.ITEM_NAME[%CONCAT_TAIL/%]
           ELSE [%CONCAT_HEAD/%]TAB_B.MENU_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_C.FULL_COL_GROUP_NAME[%CONCAT_MID/%]'/'[%CONCAT_MID/%]TAB_A.ITEM_NAME[%CONCAT_TAIL/%]
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
       TAB_A.DESCRIPTION,
       CASE
           WHEN TAB_C.FULL_COL_GROUP_NAME IS NULL THEN [%CONCAT_HEAD/%]TAB_B.MENU_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.ITEM_NAME[%CONCAT_TAIL/%]
           ELSE [%CONCAT_HEAD/%]TAB_B.MENU_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_C.FULL_COL_GROUP_NAME[%CONCAT_MID/%]'/'[%CONCAT_MID/%]TAB_A.ITEM_NAME[%CONCAT_TAIL/%]
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
