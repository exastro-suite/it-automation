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
MENU_CREATE_STATUS                  %VARCHR% (1)                    ,
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
MENU_CREATE_STATUS                  %VARCHR% (1)                    ,
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
REFERENCE_ITEM                      TEXT                            ,
TYPE3_REFERENCE                     %INT%                           ,
SINGLE_DEFAULT_VALUE                TEXT                            ,
MULTI_DEFAULT_VALUE                 TEXT                            ,
INT_DEFAULT_VALUE                   %INT%                           ,
FLOAT_DEFAULT_VALUE                 %DOUBLE%                        ,
DATETIME_DEFAULT_VALUE              %DATETIME6%                     ,
DATE_DEFAULT_VALUE                  %DATETIME6%                     ,
PULLDOWN_DEFAULT_VALUE              %INT%                           ,
LINK_DEFAULT_VALUE                  TEXT                            ,
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
REFERENCE_ITEM                      TEXT                            ,
TYPE3_REFERENCE                     %INT%                           ,
SINGLE_DEFAULT_VALUE                TEXT                            ,
MULTI_DEFAULT_VALUE                 TEXT                            ,
INT_DEFAULT_VALUE                   %INT%                           ,
FLOAT_DEFAULT_VALUE                 %DOUBLE%                        ,
DATETIME_DEFAULT_VALUE              %DATETIME6%                     ,
DATE_DEFAULT_VALUE                  %DATETIME6%                     ,
PULLDOWN_DEFAULT_VALUE              %INT%                           ,
LINK_DEFAULT_VALUE                  TEXT                            ,
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
MENU_CREATE_TYPE_ID                 %INT%                           ,
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
MENU_CREATE_TYPE_ID                 %INT%                           ,
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

-- -------------------------
-- 一意制約管理
-- -------------------------
CREATE TABLE F_UNIQUE_CONSTRAINT (
UNIQUE_CONSTRAINT_ID          %INT%             ,
CREATE_MENU_ID                %INT%             ,
UNIQUE_CONSTRAINT_ITEM        TEXT              ,
ACCESS_AUTH                   TEXT              ,
NOTE                          %VARCHR% (4000)   ,
DISUSE_FLAG                   %VARCHR% (1)      ,
LAST_UPDATE_TIMESTAMP         %DATETIME6%       ,
LAST_UPDATE_USER              %INT%             ,
PRIMARY KEY (UNIQUE_CONSTRAINT_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_UNIQUE_CONSTRAINT_JNL (
JOURNAL_SEQ_NO                %INT%             ,
JOURNAL_REG_DATETIME          %DATETIME6%       ,
JOURNAL_ACTION_CLASS          %VARCHR% (8)      ,
UNIQUE_CONSTRAINT_ID          %INT%             ,
CREATE_MENU_ID                %INT%             ,
UNIQUE_CONSTRAINT_ITEM        TEXT              ,
ACCESS_AUTH                   TEXT              ,
NOTE                          %VARCHR% (4000)   ,
DISUSE_FLAG                   %VARCHR% (1)      ,
LAST_UPDATE_TIMESTAMP         %DATETIME6%       ,
LAST_UPDATE_USER              %INT%             ,
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- 参照項目情報
-- -------------------------
CREATE TABLE F_MENU_REFERENCE_ITEM
(
ITEM_ID                             %INT%                             , -- 識別シーケンス項番
LINK_ID                             %INT%                             ,
MENU_ID                             %INT%                             ,
DISP_SEQ                            %INT%                             ,
TABLE_NAME                          %VARCHR% (64)                     ,
PRI_NAME                            %VARCHR% (64)                     ,
COLUMN_NAME                         %VARCHR% (64)                     ,
ITEM_NAME                           %VARCHR% (64)                     ,
COL_GROUP_NAME                      TEXT                              ,
DESCRIPTION                         TEXT                              ,
INPUT_METHOD_ID                     %INT%                             ,
SENSITIVE_FLAG                      %VARCHR% (1)                      ,
ORIGINAL_MENU_FLAG                  %VARCHR% (1)                      ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                %VARCHR% (4000)                   , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (ITEM_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE F_MENU_REFERENCE_ITEM_JNL
(
JOURNAL_SEQ_NO                      %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                      , -- 履歴用変更種別

ITEM_ID                             %INT%                             , -- 識別シーケンス項番
LINK_ID                             %INT%                             ,
MENU_ID                             %INT%                             ,
DISP_SEQ                            %INT%                             ,
TABLE_NAME                          %VARCHR% (64)                     ,
PRI_NAME                            %VARCHR% (64)                     ,
COLUMN_NAME                         %VARCHR% (64)                     ,
ITEM_NAME                           %VARCHR% (64)                     ,
COL_GROUP_NAME                      TEXT                              ,
DESCRIPTION                         TEXT                              ,
INPUT_METHOD_ID                     %INT%                             ,
SENSITIVE_FLAG                      %VARCHR% (1)                      ,
ORIGINAL_MENU_FLAG                  %VARCHR% (1)                      ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


-- -------------------------
-- メニュー作成状態マスタ
-- -------------------------
CREATE TABLE F_MENU_CREATE_STATUS
(
MENU_CREATE_STATUS                  %INT%                           ,
MENU_CREATE_STATUS_SELECT           %VARCHR% (256)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (MENU_CREATE_STATUS)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_MENU_CREATE_STATUS_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                    , -- 履歴用変更種別

MENU_CREATE_STATUS                  %INT%                           ,
MENU_CREATE_STATUS_SELECT           %VARCHR% (256)                  ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- フラグ管理マスタ
-- -------------------------
CREATE TABLE F_FLAG_ALT_MASTER
(
FLAG_ID                           %INT%                             , -- 識別シーケンス
YESNO_STATUS                      %VARCHR%(64)                      , -- ステータス
TRUEFALSE_STATUS                  %VARCHR%(64)                      ,
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (FLAG_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_FLAG_ALT_MASTER_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別

FLAG_ID                           %INT%                             , -- 識別シーケンス
YESNO_STATUS                      %VARCHR%(64)                      , -- ステータス
TRUEFALSE_STATUS                  %VARCHR%(64)                      ,
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_FLAG_MASTER
(
FLAG_ID                           %INT%                             , -- 識別シーケンス
ASTBLANK_STATUS                   %VARCHR%(64)                      ,
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (FLAG_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_FLAG_MASTER_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別

FLAG_ID                           %INT%                             , -- 識別シーケンス
ASTBLANK_STATUS                   %VARCHR%(64)                      ,
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
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
CONTRAST_LIST_ID                    %INT%                             , -- 識別シーケンス項番
CONTRAST_NAME                       TEXT                              ,
CONTRAST_MENU_ID_1                  %INT%                             ,
CONTRAST_MENU_ID_2                  %INT%                             ,
ALL_MATCH_FLG                       %INT%                             ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                %VARCHR% (4000)                   , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (CONTRAST_LIST_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_CONTRAST_LIST_JNL
(
JOURNAL_SEQ_NO                      %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                      , -- 履歴用変更種別

CONTRAST_LIST_ID                    %INT%                             , -- 識別シーケンス項番
CONTRAST_NAME                       TEXT                              ,
CONTRAST_MENU_ID_1                  %INT%                             ,
CONTRAST_MENU_ID_2                  %INT%                             ,
ALL_MATCH_FLG                       %INT%                             ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                %VARCHR% (4000)                   , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------
-- 比較定義詳細
-- -------------------------
CREATE TABLE A_CONTRAST_DETAIL
(
CONTRAST_DETAIL_ID                  %INT%                             , -- 識別シーケンス項番
CONTRAST_LIST_ID                    %INT%                             ,
CONTRAST_COL_TITLE                  TEXT                              ,
CONTRAST_COL_ID_1                   %INT%                             ,
CONTRAST_COL_ID_2                   %INT%                             ,
DISP_SEQ                            %INT%                             ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                %VARCHR% (4000)                   , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (CONTRAST_DETAIL_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE A_CONTRAST_DETAIL_JNL
(
JOURNAL_SEQ_NO                      %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                      , -- 履歴用変更種別

CONTRAST_DETAIL_ID                  %INT%                             , -- 識別シーケンス項番
CONTRAST_LIST_ID                    %INT%                             ,
CONTRAST_COL_TITLE                  TEXT                              ,
CONTRAST_COL_ID_1                   %INT%                             ,
CONTRAST_COL_ID_2                   %INT%                             ,
DISP_SEQ                            %INT%                             ,
ACCESS_AUTH                         TEXT                              ,
NOTE                                %VARCHR% (4000)                   , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                             , -- 最終更新ユーザ
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



