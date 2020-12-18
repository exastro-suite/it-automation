-- -- //////////////////////////////////////////////////////////////////////
-- -- //
-- -- //  【処理概要】
-- -- //    ・インストーラー用のSQL
-- -- //
-- -- //////////////////////////////////////////////////////////////////////

-- *****************************************************************************
-- *** *****  WEB-DBCORE Tables                                              ***
-- *****************************************************************************
-- シーケンスオブジェクト作成
CREATE TABLE A_SEQUENCE
(
NAME                    %VARCHR%(64)            ,
VALUE                   %INT%                   ,
MENU_ID                 %INT%                   ,
DISP_SEQ                %INT%                   ,
NOTE                    %VARCHR%(4000)          ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
PRIMARY KEY(NAME)
)%%TABLE_CREATE_OUT_TAIL%%;

-- 更新系テーブル作成
CREATE TABLE A_ACCOUNT_LIST
(
USER_ID                 %INT%                   ,
USERNAME                %VARCHR%(270)           ,
PASSWORD                %VARCHR%(32)            ,
USERNAME_JP             %VARCHR%(270)           ,
MAIL_ADDRESS            %VARCHR%(256)           ,
PW_LAST_UPDATE_TIME     %DATETIME6%             ,
AUTH_TYPE               %VARCHR%(10)            ,
PROVIDER_ID             %INT%                   ,
PROVIDER_USER_ID        %VARCHR%(256)           ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(USER_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_ACCOUNT_LOCK
(
LOCK_ID                 %INT%                   ,
USER_ID                 %INT%                   ,
MISS_INPUT_COUNTER      %INT%                   ,
LOCKED_TIMESTAMP        %DATETIME6%             ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(LOCK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_SYSTEM_CONFIG_LIST
(
ITEM_ID                 %INT%                   ,
CONFIG_ID               %VARCHR%(32)            ,
CONFIG_NAME             %VARCHR%(64)            ,
VALUE                   %VARCHR%(1024)          ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(ITEM_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PERMISSIONS_LIST
(
PERMISSIONS_ID          %INT%                   ,
IP_ADDRESS              %VARCHR%(15)            ,
IP_INFO                 %VARCHR%(256)            ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(PERMISSIONS_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_ROLE_LIST
(
ROLE_ID                 %INT%                   ,
ROLE_NAME               %VARCHR%(256)            ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(ROLE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_MENU_GROUP_LIST
(
MENU_GROUP_ID           %INT%                   ,
MENU_GROUP_NAME         %VARCHR%(256)            ,
MENU_GROUP_ICON         %VARCHR%(256)           ,
DISP_SEQ                %INT%                   ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(MENU_GROUP_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_MENU_LIST
(
MENU_ID                 %INT%                   ,
MENU_GROUP_ID           %INT%                   ,
MENU_NAME               %VARCHR%(256)            ,
LOGIN_NECESSITY         %INT%                   ,
SERVICE_STATUS          %INT%                   ,
AUTOFILTER_FLG          %INT%                   ,
INITIAL_FILTER_FLG      %INT%                   ,
WEB_PRINT_LIMIT         %INT%                   ,
WEB_PRINT_CONFIRM       %INT%                   ,
XLS_PRINT_LIMIT         %INT%                   ,
DISP_SEQ                %INT%                   ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(MENU_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_ROLE_ACCOUNT_LINK_LIST
(
LINK_ID                 %INT%                   ,
ROLE_ID                 %INT%                   ,
USER_ID                 %INT%                   ,
DEF_ACCESS_AUTH_FLAG    %VARCHR%(1)             ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(LINK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_ROLE_MENU_LINK_LIST
(
LINK_ID                 %INT%                   ,
ROLE_ID                 %INT%                   ,
MENU_ID                 %INT%                   ,
PRIVILEGE               %INT%                   ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(LINK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_LOGIN_NECESSITY_LIST
(
FLAG                    %INT%                   ,
NAME                    %VARCHR%(64)            ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(FLAG)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_SERVICE_STATUS_LIST
(
FLAG                    %INT%                   ,
NAME                    %VARCHR%(64)            ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(FLAG)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_REPRESENTATIVE_LIST
(
FLAG                    %INT%                   ,
NAME                    %VARCHR%(64)            ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(FLAG)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PRIVILEGE_LIST
(
FLAG                    %INT%                   ,
NAME                    %VARCHR%(64)            ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(FLAG)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PROVIDER_LIST
(
PROVIDER_ID                    %INT%                        , -- プロバイダーID
PROVIDER_NAME                  %VARCHR%(100)                , -- プロバイダー名
LOGO                           %VARCHR%(256)                , -- ロゴ
AUTH_TYPE                      %VARCHR%(10)                 , -- 認証方式
VISIBLE_FLAG                   %INT%                        , -- 表示フラグ
ACCESS_AUTH                    TEXT                         ,
NOTE                           %VARCHR%(4000)               , -- 備考
DISUSE_FLAG                    %VARCHR%(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
LAST_UPDATE_USER               %INT%                        , -- 最終更新ユーザ
PRIMARY KEY(PROVIDER_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PROVIDER_ATTRIBUTE_LIST (
PROVIDER_ATTRIBUTE_ID          %INT%                        , -- 属性ID
PROVIDER_ID                    %INT%                        , -- プロバイダーID
NAME                           %VARCHR%(100)                , -- 属性名
VALUE                          %VARCHR%(256)                , -- 属性値
ACCESS_AUTH                    TEXT                         ,
NOTE                           %VARCHR%(4000)               , -- 備考
DISUSE_FLAG                    %VARCHR%(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
LAST_UPDATE_USER               %INT%                        , -- 最終更新ユーザ
PRIMARY KEY (PROVIDER_ATTRIBUTE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PROVIDER_AUTH_TYPE_LIST (
ID                             %INT%                        , -- ID
NAME                           %VARCHR%(10)                 , -- 認証方式名称
ACCESS_AUTH                    TEXT                         ,
NOTE                           %VARCHR%(4000)               , -- 備考
DISUSE_FLAG                    %VARCHR%(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
LAST_UPDATE_USER               %INT%                        , -- 最終更新ユーザ
PRIMARY KEY (ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_VISIBLE_FLAG_LIST (
ID                             %INT%                        , -- ID
FLAG                           %VARCHR%(10)                 , -- 表示フラグ名称
ACCESS_AUTH                    TEXT                         ,
NOTE                           %VARCHR%(4000)               , -- 備考
DISUSE_FLAG                    %VARCHR%(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
LAST_UPDATE_USER               %INT%                        , -- 最終更新ユーザ
PRIMARY KEY (ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PROVIDER_ATTRIBUTE_NAME_LIST (
ID                             %INT%                        , -- SSO認証属性名称ID
NAME                           %VARCHR%(50)                 , -- SSO認証属性名称
ACCESS_AUTH                    TEXT                         ,
NOTE                           %VARCHR%(4000)               , -- 備考
DISUSE_FLAG                    %VARCHR%(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
LAST_UPDATE_USER               %INT%                        , -- 最終更新ユーザ
PRIMARY KEY (ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_WIDGET_LIST (
WIDGET_ID                      %INT%                        , -- ウィジェットID
WIDGET_DATA                    TEXT                         , -- ウィジェット本体(JSON)
USER_ID                        %INT%                        , -- ユーザID
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
PRIMARY KEY (WIDGET_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------
-- 作成対象マスタ
-- -------------------------
CREATE TABLE F_PARAM_TARGET
(
TARGET_ID                           %INT%                           , -- 識別シーケンス項番
DISP_SEQ                            %INT%                           , 
TARGET_NAME                         %VARCHR%(64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY (TARGET_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE F_PARAM_TARGET_JNL
(
JOURNAL_SEQ_NO                      %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME                %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS                %VARCHR% (8)                    , -- 履歴用変更種別

TARGET_ID                           %INT%                           , -- 識別シーケンス項番
DISP_SEQ                            %INT%                           , 
TARGET_NAME                         %VARCHR%(64)                    ,
ACCESS_AUTH                         TEXT                            ,
NOTE                                %VARCHR% (4000)                 , -- 備考
DISUSE_FLAG                         %VARCHR% (1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP               %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                    %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- 履歴系テーブル作成
CREATE TABLE A_ACCOUNT_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
USER_ID                 %INT%                   ,
USERNAME                %VARCHR%(270)           ,
PASSWORD                %VARCHR%(32)            ,
USERNAME_JP             %VARCHR%(270)           ,
MAIL_ADDRESS            %VARCHR%(256)           ,
PW_LAST_UPDATE_TIME     %DATETIME6%             ,
AUTH_TYPE               %VARCHR%(10)            ,
PROVIDER_ID             %INT%                   ,
PROVIDER_USER_ID        %VARCHR%(256)           ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_ACCOUNT_LOCK_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
LOCK_ID                 %INT%                   ,
USER_ID                 %INT%                   ,
MISS_INPUT_COUNTER      %INT%                   ,
LOCKED_TIMESTAMP        %DATETIME6%             ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_SYSTEM_CONFIG_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
ITEM_ID                 %INT%                   ,
CONFIG_ID               %VARCHR%(32)            ,
CONFIG_NAME             %VARCHR%(64)            ,
VALUE                   %VARCHR%(1024)          ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PERMISSIONS_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
PERMISSIONS_ID          %INT%                   ,
IP_ADDRESS              %VARCHR%(15)            ,
IP_INFO                 %VARCHR%(256)            ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_ROLE_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
ROLE_ID                 %INT%                   ,
ROLE_NAME               %VARCHR%(256)            ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_MENU_GROUP_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
MENU_GROUP_ID           %INT%                   ,
MENU_GROUP_NAME         %VARCHR%(256)            ,
MENU_GROUP_ICON         %VARCHR%(256)           ,
DISP_SEQ                %INT%                   ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_MENU_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
MENU_ID                 %INT%                   ,
MENU_GROUP_ID           %INT%                   ,
MENU_NAME               %VARCHR%(256)            ,
LOGIN_NECESSITY         %INT%                   ,
SERVICE_STATUS          %INT%                   ,
AUTOFILTER_FLG          %INT%                   ,
INITIAL_FILTER_FLG      %INT%                   ,
WEB_PRINT_LIMIT         %INT%                   ,
WEB_PRINT_CONFIRM       %INT%                   ,
XLS_PRINT_LIMIT         %INT%                   ,
DISP_SEQ                %INT%                   ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_ROLE_ACCOUNT_LINK_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
LINK_ID                 %INT%                   ,
ROLE_ID                 %INT%                   ,
USER_ID                 %INT%                   ,
DEF_ACCESS_AUTH_FLAG    %VARCHR%(1)             ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_ROLE_MENU_LINK_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
LINK_ID                 %INT%                   ,
ROLE_ID                 %INT%                   ,
MENU_ID                 %INT%                   ,
PRIVILEGE               %INT%                   ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_LOGIN_NECESSITY_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
FLAG                    %INT%                   ,
NAME                    %VARCHR%(64)            ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_SERVICE_STATUS_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
FLAG                    %INT%                   ,
NAME                    %VARCHR%(64)            ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_REPRESENTATIVE_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
FLAG                    %INT%                   ,
NAME                    %VARCHR%(64)            ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PRIVILEGE_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
FLAG                    %INT%                   ,
NAME                    %VARCHR%(64)            ,
ACCESS_AUTH             TEXT                    ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_TODO_MASTER
(
TODO_ID                           %INT%                             , -- 識別シーケンス
TODO_STATUS                       %VARCHR%(64)                      , -- ステータス
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (TODO_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_TODO_MASTER_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別

TODO_ID                           %INT%                             , -- 識別シーケンス
TODO_STATUS                       %VARCHR%(64)                      , -- ステータス
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE A_PROVIDER_LIST_JNL
(
JOURNAL_SEQ_NO               %INT%                          , -- 履歴用シーケンス
JOURNAL_REG_DATETIME         %DATETIME6%                    , -- 履歴用変更日時
JOURNAL_ACTION_CLASS         %VARCHR%(8)                    , -- 履歴用変更種別

PROVIDER_ID                  %INT%                          , -- プロバイダーID
PROVIDER_NAME                %VARCHR%(100)                  , -- プロバイダー名
LOGO                         %VARCHR%(256)                  , -- ロゴ
AUTH_TYPE                    %VARCHR%(10)                   , -- 認証方式
VISIBLE_FLAG                 %INT%                          , -- 表示フラグ
ACCESS_AUTH                  TEXT                           ,
NOTE                         %VARCHR%(4000)                 , -- 備考
DISUSE_FLAG                  %VARCHR%(1)                    , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP        %DATETIME6%                    , -- 最終更新日時
LAST_UPDATE_USER             %INT%                          , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PROVIDER_ATTRIBUTE_LIST_JNL (
JOURNAL_SEQ_NO                 %INT%                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           %DATETIME6%                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           %VARCHR%(8)                  , -- 履歴用変更種別

PROVIDER_ATTRIBUTE_ID          %INT%                        , -- 属性ID
PROVIDER_ID                    %INT%                        , -- プロバイダーID
NAME                           %VARCHR%(100)                , -- 属性名
VALUE                          %VARCHR%(256)                , -- 属性値
ACCESS_AUTH                    TEXT                         ,
NOTE                           %VARCHR%(4000)               , -- 備考
DISUSE_FLAG                    %VARCHR%(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
LAST_UPDATE_USER               %INT%                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PROVIDER_AUTH_TYPE_LIST_JNL (
JOURNAL_SEQ_NO                 %INT%                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           %DATETIME6%                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           %VARCHR%(8)                  , -- 履歴用変更種別

ID                             %INT%                        , -- ID
NAME                           %VARCHR%(10)                 , -- 認証方式名称
ACCESS_AUTH                    TEXT                         ,
NOTE                           %VARCHR%(4000)               , -- 備考
DISUSE_FLAG                    %VARCHR%(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
LAST_UPDATE_USER               %INT%                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_VISIBLE_FLAG_LIST_JNL (
JOURNAL_SEQ_NO                 %INT%                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           %DATETIME6%                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           %VARCHR%(8)                  , -- 履歴用変更種別

ID                             %INT%                        , -- ID
ACCESS_AUTH                    TEXT                         ,
FLAG                           %VARCHR%(10)                 , -- 表示フラグ名称
NOTE                           %VARCHR%(4000)               , -- 備考
DISUSE_FLAG                    %VARCHR%(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
LAST_UPDATE_USER               %INT%                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PROVIDER_ATTRIBUTE_NAME_LIST_JNL (
JOURNAL_SEQ_NO                 %INT%                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           %DATETIME6%                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           %VARCHR%(8)                  , -- 履歴用変更種別

ID                             %INT%                        , -- SSO認証属性名称ID
NAME                           %VARCHR%(50)                 , -- SSO認証属性名称
ACCESS_AUTH                    TEXT                         ,
NOTE                           %VARCHR%(4000)               , -- 備考
DISUSE_FLAG                    %VARCHR%(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
LAST_UPDATE_USER               %INT%                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- *****************************************************************************
-- *** WEB-DBCORE Tables *****                                               ***
-- *****************************************************************************



-- *****************************************************************************
-- *** ***** ITA-BASE Tables                                                 ***
-- *****************************************************************************
-- ----更新系テーブル作成
CREATE TABLE B_ITA_EXT_STM_MASTER
(
ITA_EXT_STM_ID                  %INT%                        ,
ITA_EXT_STM_NAME                %VARCHR%(64)                 ,
ITA_EXT_LINK_LIB_PATH           %VARCHR%(64)                 ,
MENU_ID                         %INT%                        , -- 作業管理メニューID
EXEC_INS_MNG_TABLE_NAME         %VARCHR%(64)                 , -- 作業インスタンステーブル名
LOG_TARGET                      %INT%                        , -- ログ収集対象有無 1:対象 他:対象外
DISP_SEQ                        %INT%                        ,
ACCESS_AUTH                     TEXT                         ,
NOTE                            %VARCHR%(4000)               ,
DISUSE_FLAG                     %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP           %DATETIME6%                  ,
LAST_UPDATE_USER                %INT%                        ,
PRIMARY KEY ( ITA_EXT_STM_ID )
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ITA_EXT_STM_MASTER_JNL
(
JOURNAL_SEQ_NO                  %INT%                        ,
JOURNAL_REG_DATETIME            %DATETIME6%                  ,
JOURNAL_ACTION_CLASS            %VARCHR%(8)                  ,
ITA_EXT_STM_ID                  %INT%                        ,
ITA_EXT_STM_NAME                %VARCHR%(64)                 ,
ITA_EXT_LINK_LIB_PATH           %VARCHR%(64)                 ,
MENU_ID                         %INT%                        , -- 作業管理メニューID
EXEC_INS_MNG_TABLE_NAME         %VARCHR%(64)                 , -- 作業インスタンステーブル名
LOG_TARGET                      %INT%                        , -- ログ収集対象有無 1:対象 他:対象外
DISP_SEQ                        %INT%                        ,
ACCESS_AUTH                     TEXT                         ,
NOTE                            %VARCHR%(4000)               ,
DISUSE_FLAG                     %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP           %DATETIME6%                  ,
LAST_UPDATE_USER                %INT%                        ,
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- 更新系テーブル作成----
CREATE TABLE B_HARDAWRE_TYPE
(
HARDAWRE_TYPE_ID                  %INT%                     ,

HARDAWRE_TYPE_NAME                %VARCHR%(64)              ,

DISP_SEQ                          %INT%                     , -- 表示順序
ACCESS_AUTH                       TEXT                      ,
NOTE                              %VARCHR%(4000)            , -- 備考
DISUSE_FLAG                       %VARCHR%(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%               , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                     , -- 最終更新ユーザ

PRIMARY KEY (HARDAWRE_TYPE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_HARDAWRE_TYPE_JNL
(
JOURNAL_SEQ_NO                    %INT%                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%               , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)               , -- 履歴用変更種別

HARDAWRE_TYPE_ID                  %INT%                     ,

HARDAWRE_TYPE_NAME                %VARCHR%(64)              ,

DISP_SEQ                          %INT%                     , -- 表示順序
ACCESS_AUTH                       TEXT                      ,
NOTE                              %VARCHR%(4000)            , -- 備考
DISUSE_FLAG                       %VARCHR%(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%               , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_PROTOCOL
(
PROTOCOL_ID                       %INT%                     ,

PROTOCOL_NAME                     %VARCHR%(32)              ,

DISP_SEQ                          %INT%                     , -- 表示順序
ACCESS_AUTH                       TEXT                      ,
NOTE                              %VARCHR%(4000)            , -- 備考
DISUSE_FLAG                       %VARCHR%(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%               , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                     , -- 最終更新ユーザ

PRIMARY KEY (PROTOCOL_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_PROTOCOL_JNL
(
JOURNAL_SEQ_NO                    %INT%                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%               , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)               , -- 履歴用変更種別

PROTOCOL_ID                       %INT%                     ,

PROTOCOL_NAME                     %VARCHR%(32)              ,

DISP_SEQ                          %INT%                     , -- 表示順序
ACCESS_AUTH                       TEXT                      ,
NOTE                              %VARCHR%(4000)            , -- 備考
DISUSE_FLAG                       %VARCHR%(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%               , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_HOST_DESIGNATE_TYPE_LIST
(
HOST_DESIGNATE_TYPE_ID            %INT%                     ,

HOST_DESIGNATE_TYPE_NAME          %VARCHR%(32)              ,

DISP_SEQ                          %INT%                     , -- 表示順序
ACCESS_AUTH                       TEXT                      ,
NOTE                              %VARCHR%(4000)            , -- 備考
DISUSE_FLAG                       %VARCHR%(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%               , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                     , -- 最終更新ユーザ

PRIMARY KEY (HOST_DESIGNATE_TYPE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_HOST_DESIGNATE_TYPE_LIST_JNL
(
JOURNAL_SEQ_NO                    %INT%                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%               , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)               , -- 履歴用変更種別

HOST_DESIGNATE_TYPE_ID            %INT%                     ,

HOST_DESIGNATE_TYPE_NAME          %VARCHR%(32)              ,

DISP_SEQ                          %INT%                     , -- 表示順序
ACCESS_AUTH                       TEXT                      ,
NOTE                              %VARCHR%(4000)            , -- 備考
DISUSE_FLAG                       %VARCHR%(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%               , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----


-- ----更新系テーブル作成
CREATE TABLE C_STM_LIST
(
SYSTEM_ID                         %INT%                     , -- 識別シーケンス

HARDAWRE_TYPE_ID                  %INT%                     ,
HOSTNAME                          %VARCHR%(128)             ,
IP_ADDRESS                        %VARCHR%(15)              ,

ETH_WOL_MAC_ADDRESS               %VARCHR%(17)              , -- ETH_WAKE_ON_LAN
ETH_WOL_NET_DEVICE                %VARCHR%(256)              , -- ETH_WAKE_ON_LAN

PROTOCOL_ID                       %INT%                     ,
LOGIN_USER                        %VARCHR%(30)              ,
LOGIN_PW_HOLD_FLAG                %INT%                     ,
LOGIN_PW                          %VARCHR%(60)              ,
LOGIN_PW_ANSIBLE_VAULT            %VARCHR%(512)             , -- パスワード ansible-vault暗号化文字列　隠しカラム
LOGIN_AUTH_TYPE                   %INT%                     ,
WINRM_PORT                        %INT%                     , -- WinRM接続プロトコル
WINRM_SSL_CA_FILE                 %VARCHR%(256)             , -- WinRM接続 SSLサーバー証明書
OS_TYPE_ID                        %INT%                     ,
SSH_EXTRA_ARGS                    %VARCHR%(512)             , -- ssh追加パラメータ
HOSTS_EXTRA_ARGS                  %VARCHR%(512)             , -- インベントリファイル(hosts)追加パラメータ
CREDENTIAL_TYPE_ID                %INT%                     , -- Ansible-Tower認証情報　接続タイプ

--
SYSTEM_NAME                       %VARCHR%(64)              ,
COBBLER_PROFILE_ID                %INT%                     , -- FOR COBLLER
INTERFACE_TYPE                    %VARCHR%(256)             , -- FOR COBLLER
MAC_ADDRESS                       %VARCHR%(17)              , -- FOR COBLLER
NETMASK                           %VARCHR%(15)              , -- FOR COBLLER
GATEWAY                           %VARCHR%(15)              , -- FOR COBLLER
STATIC                            %VARCHR%(32)              , -- FOR COBLLER

CONN_SSH_KEY_FILE                 %VARCHR%(256)             ,

ANSTWR_INSTANCE_GROUP_NAME        %VARCHR%(512)             , -- インスタンスグループ名

DISP_SEQ                          %INT%                     , -- 表示順序
ACCESS_AUTH                       TEXT                      ,
NOTE                              %VARCHR%(4000)            , -- 備考
DISUSE_FLAG                       %VARCHR%(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%               , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                     , -- 最終更新ユーザ

PRIMARY KEY (SYSTEM_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_STM_LIST_JNL
(
JOURNAL_SEQ_NO                    %INT%                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%               , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)               , -- 履歴用変更種別

SYSTEM_ID                         %INT%                     , -- 識別シーケンス

HARDAWRE_TYPE_ID                  %INT%                     ,
HOSTNAME                          %VARCHR%(128)             ,
IP_ADDRESS                        %VARCHR%(15)              ,

ETH_WOL_MAC_ADDRESS               %VARCHR%(17)              , -- ETH_WAKE_ON_LAN
ETH_WOL_NET_DEVICE                %VARCHR%(256)             , -- ETH_WAKE_ON_LAN

PROTOCOL_ID                       %INT%                     ,
LOGIN_USER                        %VARCHR%(30)              ,
LOGIN_PW_HOLD_FLAG                %INT%                     ,
LOGIN_PW                          %VARCHR%(60)              ,
LOGIN_PW_ANSIBLE_VAULT            %VARCHR%(512)             , -- パスワード ansible-vault暗号化文字列　隠しカラム
LOGIN_AUTH_TYPE                   %INT%                     ,
WINRM_PORT                        %INT%                     , -- WinRM接続プロトコル
WINRM_SSL_CA_FILE                 %VARCHR%(256)             , -- WinRM接続 SSLサーバー証明書
OS_TYPE_ID                        %INT%                     ,
SSH_EXTRA_ARGS                    %VARCHR%(512)             , -- ssh追加パラメータ
HOSTS_EXTRA_ARGS                  %VARCHR%(512)             , -- インベントリファイル(hosts)追加パラメータ
CREDENTIAL_TYPE_ID                %INT%                     , -- Ansible-Tower認証情報　接続タイプ

SYSTEM_NAME                       %VARCHR%(64)              ,
COBBLER_PROFILE_ID                %INT%                     , -- FOR COBLLER
INTERFACE_TYPE                    %VARCHR%(256)             , -- FOR COBLLER
MAC_ADDRESS                       %VARCHR%(17)              , -- FOR COBLLER
NETMASK                           %VARCHR%(15)              , -- FOR COBLLER
GATEWAY                           %VARCHR%(15)              , -- FOR COBLLER
STATIC                            %VARCHR%(32)              , -- FOR COBLLER

CONN_SSH_KEY_FILE                 %VARCHR%(256)             ,

ANSTWR_INSTANCE_GROUP_NAME        %VARCHR%(512)             , -- インスタンスグループ名

DISP_SEQ                          %INT%                     , -- 表示順序
ACCESS_AUTH                       TEXT                      ,
NOTE                              %VARCHR%(4000)            , -- 備考
DISUSE_FLAG                       %VARCHR%(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%               , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_PATTERN_PER_ORCH
(
PATTERN_ID                        %INT%                            ,

PATTERN_NAME                      %VARCHR%(256)                    ,
ITA_EXT_STM_ID                    %INT%                            ,
TIME_LIMIT                        %INT%                            ,

ANS_HOST_DESIGNATE_TYPE_ID        %INT%                            ,
ANS_PARALLEL_EXE                  %INT%                            ,
ANS_WINRM_ID                      %INT%                            ,
ANS_PLAYBOOK_HED_DEF              %VARCHR%(512)                    , -- legacy Playbook.ymlのヘッダ定義
ANS_EXEC_OPTIONS                  %VARCHR%(512)                    ,
ANS_VIRTUALENV_NAME               %VARCHR%(512)                    , 
OPENST_TEMPLATE                   %VARCHR%(256)                    ,
OPENST_ENVIRONMENT                %VARCHR%(256)                    ,
TERRAFORM_WORKSPACE_ID            %INT%                            , -- Terraform利用情報

DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (PATTERN_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_PATTERN_PER_ORCH_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

PATTERN_ID                        %INT%                            ,

PATTERN_NAME                      %VARCHR%(256)                    ,
ITA_EXT_STM_ID                    %INT%                            ,
TIME_LIMIT                        %INT%                            ,

ANS_HOST_DESIGNATE_TYPE_ID        %INT%                            ,
ANS_PARALLEL_EXE                  %INT%                            ,
ANS_WINRM_ID                      %INT%                            ,
ANS_PLAYBOOK_HED_DEF              %VARCHR%(512)                    , -- legacy Playbook.ymlのヘッダ定義
ANS_EXEC_OPTIONS                  %VARCHR%(512)                    ,
ANS_VIRTUALENV_NAME               %VARCHR%(512)                    , 
OPENST_TEMPLATE                   %VARCHR%(256)                    ,
OPENST_ENVIRONMENT                %VARCHR%(256)                    ,
TERRAFORM_WORKSPACE_ID            %INT%                            , -- Terraform利用情報

DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_OPERATION_LIST
(
OPERATION_NO_UAPK                 %INT%                      ,

OPERATION_NAME                    %VARCHR%(256)              ,
OPERATION_DATE                    %DATETIME6%                ,
OPERATION_NO_IDBH                 %INT%                      ,
LAST_EXECUTE_TIMESTAMP            %DATETIME6%                ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (OPERATION_NO_UAPK)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_OPERATION_LIST_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

OPERATION_NO_UAPK                 %INT%                      ,

OPERATION_NAME                    %VARCHR%(256)              ,
OPERATION_DATE                    %DATETIME6%                ,
OPERATION_NO_IDBH                 %INT%                      ,
LAST_EXECUTE_TIMESTAMP            %DATETIME6%                ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ReMiTicket3115----
CREATE TABLE C_SYMPHONY_IF_INFO
(
SYMPHONY_IF_INFO_ID               %INT%                      , -- 識別シーケンス

SYMPHONY_STORAGE_PATH_ITA         %VARCHR%(256)              , -- ITA側のSymphonyインスタンス毎の共有ディレクトリ
SYMPHONY_REFRESH_INTERVAL         %INT%                      , -- 状態監視周期(単位ミリ秒)

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (SYMPHONY_IF_INFO_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_SYMPHONY_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

SYMPHONY_IF_INFO_ID               %INT%                      , -- 識別シーケンス

SYMPHONY_STORAGE_PATH_ITA         %VARCHR%(256)              , -- ITA側のSymphonyインスタンス毎の共有ディレクトリ
SYMPHONY_REFRESH_INTERVAL         %INT%                      , -- 状態監視周期(単位ミリ秒)

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----
-- ----ReMiTicket3115

-- ----更新系テーブル作成
CREATE TABLE C_SYMPHONY_CLASS_MNG
(
SYMPHONY_CLASS_NO                 %INT%                      ,

SYMPHONY_NAME                     %VARCHR%(256)              ,
DESCRIPTION                       %VARCHR%(4000)             ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (SYMPHONY_CLASS_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_SYMPHONY_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

SYMPHONY_CLASS_NO                 %INT%                      ,

SYMPHONY_NAME                     %VARCHR%(256)              ,
DESCRIPTION                       %VARCHR%(4000)             ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_SYMPHONY_INSTANCE_MNG
(
SYMPHONY_INSTANCE_NO              %INT%                      ,

I_SYMPHONY_CLASS_NO               %INT%                      ,
I_SYMPHONY_NAME                   %VARCHR%(256)              ,
I_DESCRIPTION                     %VARCHR%(4000)             ,
OPERATION_NO_UAPK                 %INT%                      ,
I_OPERATION_NAME                  %VARCHR%(256)              , 
STATUS_ID                         %INT%                      ,
EXECUTION_USER                    %VARCHR%(80)               ,
ABORT_EXECUTE_FLAG                %INT%                      ,
TIME_BOOK                         %DATETIME6%                ,
TIME_START                        %DATETIME6%                ,
TIME_END                          %DATETIME6%                ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (SYMPHONY_INSTANCE_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_SYMPHONY_INSTANCE_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別
--
SYMPHONY_INSTANCE_NO              %INT%                      ,
--
I_SYMPHONY_CLASS_NO               %INT%                      ,
I_SYMPHONY_NAME                   %VARCHR%(256)              ,
I_DESCRIPTION                     %VARCHR%(4000)             ,
OPERATION_NO_UAPK                 %INT%                      ,
I_OPERATION_NAME                  %VARCHR%(256)              ,
STATUS_ID                         %INT%                      ,
EXECUTION_USER                    %VARCHR%(80)               ,
ABORT_EXECUTE_FLAG                %INT%                      ,
TIME_BOOK                         %DATETIME6%                ,
TIME_START                        %DATETIME6%                ,
TIME_END                          %DATETIME6%                ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_MOVEMENT_CLASS_MNG
(
MOVEMENT_CLASS_NO                 %INT%                      ,

ORCHESTRATOR_ID                   %INT%                      ,
PATTERN_ID                        %INT%                      ,
MOVEMENT_SEQ                      %INT%                      ,
NEXT_PENDING_FLAG                 %INT%                      ,
DESCRIPTION                       %VARCHR%(4000)             ,
SYMPHONY_CLASS_NO                 %INT%                      ,
OPERATION_NO_IDBH                 %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (MOVEMENT_CLASS_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_MOVEMENT_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

MOVEMENT_CLASS_NO                 %INT%                      ,

ORCHESTRATOR_ID                   %INT%                      ,
PATTERN_ID                        %INT%                      ,
MOVEMENT_SEQ                      %INT%                      ,
NEXT_PENDING_FLAG                 %INT%                      ,
DESCRIPTION                       %VARCHR%(4000)             ,
SYMPHONY_CLASS_NO                 %INT%                      ,
OPERATION_NO_IDBH                 %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_MOVEMENT_INSTANCE_MNG
(
MOVEMENT_INSTANCE_NO              %INT%                      ,
--
I_MOVEMENT_CLASS_NO               %INT%                      ,
I_ORCHESTRATOR_ID                 %INT%                      ,
I_PATTERN_ID                      %INT%                      ,
I_PATTERN_NAME                    %VARCHR%(256)              ,
I_TIME_LIMIT                      %INT%                      ,
I_ANS_HOST_DESIGNATE_TYPE_ID      %INT%                      ,
I_ANS_WINRM_ID                    %INT%                      ,

I_MOVEMENT_SEQ                    %INT%                      ,
I_NEXT_PENDING_FLAG               %INT%                      ,
I_DESCRIPTION                     %VARCHR%(4000)             ,
SYMPHONY_INSTANCE_NO              %INT%                      ,
EXECUTION_NO                      %INT%                      ,
STATUS_ID                         %INT%                      ,
ABORT_RECEPTED_FLAG               %INT%                      ,
TIME_START                        %DATETIME6%                ,
TIME_END                          %DATETIME6%                ,
RELEASED_FLAG                     %INT%                      ,

EXE_SKIP_FLAG                     %INT%                      ,
OVRD_OPERATION_NO_UAPK            %INT%                      ,
OVRD_I_OPERATION_NAME             %VARCHR%(256)              ,
OVRD_I_OPERATION_NO_IDBH          %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (MOVEMENT_INSTANCE_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_MOVEMENT_INSTANCE_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

MOVEMENT_INSTANCE_NO              %INT%                      ,

I_MOVEMENT_CLASS_NO               %INT%                      ,
I_ORCHESTRATOR_ID                 %INT%                      ,
I_PATTERN_ID                      %INT%                      ,
I_PATTERN_NAME                    %VARCHR%(256)              ,
I_TIME_LIMIT                      %INT%                      ,
I_ANS_HOST_DESIGNATE_TYPE_ID      %INT%                      ,
I_ANS_WINRM_ID                    %INT%                      ,

I_MOVEMENT_SEQ                    %INT%                      ,
I_NEXT_PENDING_FLAG               %INT%                      ,
I_DESCRIPTION                     %VARCHR%(4000)             ,
SYMPHONY_INSTANCE_NO              %INT%                      ,
EXECUTION_NO                      %INT%                      ,
STATUS_ID                         %INT%                      ,
ABORT_RECEPTED_FLAG               %INT%                      ,
TIME_START                        %DATETIME6%                ,
TIME_END                          %DATETIME6%                ,
RELEASED_FLAG                     %INT%                      ,

EXE_SKIP_FLAG                     %INT%                      ,
OVRD_OPERATION_NO_UAPK            %INT%                      ,
OVRD_I_OPERATION_NAME             %VARCHR%(256)              ,
OVRD_I_OPERATION_NO_IDBH          %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_SYM_EXE_STATUS
(
SYM_EXE_STATUS_ID                 %INT%                      ,

SYM_EXE_STATUS_NAME               %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (SYM_EXE_STATUS_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_SYM_EXE_STATUS_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

SYM_EXE_STATUS_ID                 %INT%                      ,

SYM_EXE_STATUS_NAME               %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----


-- ----更新系テーブル作成
CREATE TABLE B_SYM_ABORT_FLAG
(
SYM_ABORT_FLAG_ID                 %INT%                      ,

SYM_ABORT_FLAG_NAME               %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (SYM_ABORT_FLAG_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_SYM_ABORT_FLAG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

SYM_ABORT_FLAG_ID                 %INT%                      ,

SYM_ABORT_FLAG_NAME               %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_MOV_EXE_STATUS
(
MOV_EXE_STATUS_ID                 %INT%                      ,

MOV_EXE_STATUS_NAME               %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (MOV_EXE_STATUS_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_MOV_EXE_STATUS_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

MOV_EXE_STATUS_ID                 %INT%                      ,

MOV_EXE_STATUS_NAME               %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_MOV_ABT_RECEPT_FLAG
(
MOV_ABT_RECEPT_FLAG_ID            %INT%                      ,

MOV_ABT_RECEPT_FLAG_NAME          %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (MOV_ABT_RECEPT_FLAG_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_MOV_ABT_RECEPT_FLAG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

MOV_ABT_RECEPT_FLAG_ID            %INT%                      ,

MOV_ABT_RECEPT_FLAG_NAME          %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_MOV_RELEASED_FLAG
(
MOV_RELEASED_FLAG_ID              %INT%                      ,

MOV_RELEASED_FLAG_NAME            %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (MOV_RELEASED_FLAG_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_MOV_RELEASED_FLAG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

MOV_RELEASED_FLAG_ID              %INT%                      ,

MOV_RELEASED_FLAG_NAME            %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_MOV_NEXT_PENDING_FLAG
(
MOV_NEXT_PENDING_FLAG_ID          %INT%                      ,

MOV_NEXT_PENDING_FLAG_NAME        %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (MOV_NEXT_PENDING_FLAG_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_MOV_NEXT_PENDING_FLAG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

MOV_NEXT_PENDING_FLAG_ID          %INT%                      ,

MOV_NEXT_PENDING_FLAG_NAME        %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_LOGIN_AUTH_TYPE
(
LOGIN_AUTH_TYPE_ID                %INT%                      , -- 識別シーケンス

LOGIN_AUTH_TYPE_NAME              %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (LOGIN_AUTH_TYPE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_LOGIN_AUTH_TYPE_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

LOGIN_AUTH_TYPE_ID                %INT%                      , -- 識別シーケンス

LOGIN_AUTH_TYPE_NAME              %VARCHR%(32)               ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- -------------------------------------------------------
-- --● (プルダウン用)　TABLE
-- -------------------------------------------------------
CREATE TABLE D_FLAG_LIST_01
(
FLAG_ID                           %INT%                            , -- 識別シーケンス

FLAG_NAME                         %VARCHR%(32)                      , -- 表示名

DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (FLAG_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE D_FLAG_LIST_01_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

FLAG_ID                           %INT%                            , -- 識別シーケンス

FLAG_NAME                         %VARCHR%(32)                     , -- 表示名

DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- - データポータビリティ
CREATE TABLE B_DP_HIDE_MENU_LIST
(
HIDE_ID                           %INT%                             , -- 識別シーケンス

MENU_ID                           %INT%                             , -- メニューID

PRIMARY KEY (HIDE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_DP_STATUS
(
TASK_ID                           %INT%                             , -- タスクID

TASK_STATUS                       %INT%                             , -- ステータス
DP_TYPE                           %INT%                             , -- 処理種別
IMPORT_TYPE                       %INT%                             , -- インポート種別
FILE_NAME                         %VARCHR%(64)                      , -- ファイル名
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (TASK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_DP_STATUS_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別

TASK_ID                           %INT%                             , -- 識別シーケンス
TASK_STATUS                       %INT%                             , -- ステータス
DP_TYPE                           %INT%                             , -- 処理種別
IMPORT_TYPE                       %INT%                             , -- インポート種別
FILE_NAME                         %VARCHR%(64)                      , -- ファイル名
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_DP_STATUS_MASTER
(
TASK_ID                           %INT%                             , -- 識別シーケンス
TASK_STATUS                       %VARCHR%(64)                      , -- ステータス
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (TASK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_DP_STATUS_MASTER_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別

TASK_ID                           %INT%                             , -- 識別シーケンス
TASK_STATUS                       %VARCHR%(64)                      , -- ステータス
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (TASK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_DP_TYPE
(
ROW_ID                            %INT%                             , -- 識別シーケンス
DP_TYPE                           %VARCHR%(64)                      , -- 処理種別
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_DP_TYPE_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別
--
ROW_ID                            %INT%                             , -- 識別シーケンス
DP_TYPE                           %VARCHR%(64)                      , -- 処理種別
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_DP_IMPORT_TYPE
(
ROW_ID                            %INT%                             , -- 識別シーケンス
IMPORT_TYPE                       %VARCHR%(64)                      , -- インポート種別
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_DP_IMPORT_TYPE_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別
--
ROW_ID                            %INT%                             , -- 識別シーケンス
IMPORT_TYPE                       %VARCHR%(64)                      , -- インポート種別
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- - データポータビリティ

-- - ActiveDirectory連携
CREATE TABLE A_AD_GROUP_JUDGEMENT
(
GROUP_JUDGE_ID                    %INT%                             , -- 識別シーケンス

AD_GROUP_SID                      %VARCHR%(256)                     , -- ADグループ識別子
ITA_ROLE_ID                       %INT%                             , -- ITAロールID

DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ

PRIMARY KEY (GROUP_JUDGE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_AD_GROUP_JUDGEMENT_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別

GROUP_JUDGE_ID                    %INT%                             , -- 識別シーケンス

AD_GROUP_SID                      %VARCHR%(256)                     , -- ADグループ識別子
ITA_ROLE_ID                       %INT%                             , -- ITAロールID

DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_AD_USER_JUDGEMENT
(
USER_JUDGE_ID                     %INT%                             , -- 識別シーケンス

AD_USER_SID                       %VARCHR%(256)                     , -- ADユーザ識別子
ITA_USER_ID                       %INT%                             , -- ITAユーザID

DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ

PRIMARY KEY (USER_JUDGE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_AD_USER_JUDGEMENT_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別

USER_JUDGE_ID                     %INT%                             , -- 識別シーケンス

AD_USER_SID                       %VARCHR%(256)                     , -- ADユーザ識別子
ITA_USER_ID                       %INT%                             , -- ITAユーザID

DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- ActiveDirectory連携 -

-- グラフ画面対応 -
CREATE TABLE A_RELATE_STATUS
(
RELATE_STATUS_ID                  %INT%                             , -- 識別シーケンス

MENU_ID                           %VARCHR%(256)                     , -- 表示画面名称
STATUS_TAB_NAME                   %VARCHR%(256)                     , -- 各メニューのステータステーブル
COMPLETE_ID                       %INT%                             , -- 完了ステータスID
FAILED_ID                         %INT%                             , -- 完了（異常）ステータスID
UNEXPECTED_ID                     %INT%                             , -- 想定外エラーステータスID
EMERGENCY_ID                      %INT%                             , -- 緊急停止ステータスID
CANCEL_ID                         %INT%                             , -- 予約取消ステータスID

DISP_SEQ                          %INT%                             , -- 表示順序
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (RELATE_STATUS_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_RELATE_STATUS_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別

RELATE_STATUS_ID                  %INT%                             , -- 識別シーケンス

MENU_ID                           %VARCHR%(256)                     , -- 表示画面名称
STATUS_TAB_NAME                   %VARCHR%(256)                     , -- 各メニューのステータステーブル
COMPLETE_ID                       %INT%                             , -- 完了ステータスID
FAILED_ID                         %INT%                             , -- 完了（異常）ステータスID
UNEXPECTED_ID                     %INT%                             , -- 想定外エラーステータスID
EMERGENCY_ID                      %INT%                             , -- 緊急停止ステータスID
CANCEL_ID                         %INT%                             , -- 予約取消ステータスID

DISP_SEQ                          %INT%                             , -- 表示順序
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- グラフ画面対応 -

-- メインメニューパネル化対応 -
CREATE TABLE A_SORT_MENULIST
(
SORT_MENULIST_ID                  %INT%                             , -- ID

USER_NAME                         %VARCHR% (768)                    , -- ユーザー名
MENU_ID_LIST                      %VARCHR% (768)                    , -- メニューIDのリスト
SORT_ID_LIST                      %VARCHR% (768)                    , -- 並び順のリスト
DISPLAY_MODE                      %VARCHR% (20)                     , -- 表示モード

DISP_SEQ                          %INT%                             , -- 表示順序
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (USER_NAME)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_SORT_MENULIST_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別

SORT_MENULIST_ID                  %INT%                             , -- ID

USER_NAME                         %VARCHR% (768)                    , -- ユーザー名
MENU_ID_LIST                      %VARCHR% (768)                    , -- メニューIDのリスト
SORT_ID_LIST                      %VARCHR% (768)                    , -- 並び順のリスト
DISPLAY_MODE                      %VARCHR% (20)                     , -- 表示モード

DISP_SEQ                          %INT%                             , -- 表示順序
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- メインメニューパネル化対応 -


-- -------------------------------------------------------
-- --Symphony/オペレーション エクスポート/インポート機能用
-- -------------------------------------------------------
-- エクスポート/インポート管理 -
CREATE TABLE B_DP_SYM_OPE_STATUS
(
TASK_ID                           %INT%                             , -- タスクID
--
TASK_STATUS                       %INT%                             , -- ステータス
DP_TYPE                           %INT%                             , -- 処理種別
FILE_NAME                         %VARCHR%(64)                      , -- ファイル名
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (TASK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_DP_SYM_OPE_STATUS_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別
--
TASK_ID                           %INT%                             , -- 識別シーケンス
TASK_STATUS                       %INT%                             , -- ステータス
DP_TYPE                           %INT%                             , -- 処理種別
FILE_NAME                         %VARCHR%(64)                      , -- ファイル名
DISP_SEQ                          %INT%                             , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- エクスポート/インポート管理 -

-- Symphonyエクスポート紐付 -
CREATE TABLE B_SYMPHONY_EXPORT_LINK
(
ROW_ID                          %INT%                               , -- ID

HIERARCHY                       %INT%                               , -- 階層
SRC_ROW_ID                      %INT%                               , -- 検索元項番
SRC_ITEM                        %VARCHR%(128)                       , -- 検索元項目名
DEST_MENU_ID                    %INT%                               , -- 検索先メニュー
DEST_ITEM                       %VARCHR%(128)                       , -- 検索先項目名
OTHER_CONDITION                 %VARCHR%(1024)                      , -- その他検索条件
SPECIAL_SELECT_FUNC             %VARCHR%(1024)                      , -- 検索専用特別関数

PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Symphonyエクスポート紐付 -

-- オペレーションエクスポート紐付 -
CREATE TABLE B_OPERATION_EXPORT_LINK
(
ROW_ID                          %INT%                               , -- ID

HIERARCHY                       %INT%                               , -- 階層
SRC_ROW_ID                      %INT%                               , -- 検索元項番
SRC_ITEM                        %VARCHR%(128)                       , -- 検索元項目名
DEST_MENU_ID                    %INT%                               , -- 検索先メニュー
DEST_ITEM                       %VARCHR%(128)                       , -- 検索先項目名
OTHER_CONDITION                 %VARCHR%(1024)                      , -- その他検索条件
SPECIAL_SELECT_FUNC             %VARCHR%(1024)                      , -- 検索専用特別関数

PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- オペレーションエクスポート紐付 -

-- Symオペインポート時停止サービス -
CREATE TABLE B_SVC_TO_STOP_IMP_SYM_OPE
(
ROW_ID                          %INT%                               , -- ID

SERVICE_NAME                    %VARCHR%(128)                       , -- サービス名

PRIMARY KEY (ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Symオペインポート時停止サービス -

-- -------------------------------------------------------
-- --定期作業実行用
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE C_REGULARLY_LIST
(
REGULARLY_ID                      %INT%                        ,
SYMPHONY_CLASS_NO                 %INT%                        ,
OPERATION_NO_IDBH                 %INT%                        ,
SYMPHONY_INSTANCE_NO              %INT%                        ,
STATUS_ID                         %INT%                        ,
NEXT_EXECUTION_DATE               %DATETIME6%                  ,
START_DATE                        %DATETIME6%                  ,
END_DATE                          %DATETIME6%                  ,
EXECUTION_STOP_START_DATE         %DATETIME6%                  ,
EXECUTION_STOP_END_DATE           %DATETIME6%                  ,
EXECUTION_INTERVAL                %INT%                        ,
REGULARLY_PERIOD_ID               %INT%                        ,
PATTERN_TIME                      %VARCHR%(5)                  ,
PATTERN_DAY                       %INT%                        ,
PATTERN_DAY_OF_WEEK               %INT%                        ,
PATTERN_WEEK_NUMBER               %INT%                        ,
EXECUTION_USER_ID                 %INT%                        ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,

PRIMARY KEY (REGULARLY_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_REGULARLY_LIST_JNL
(
JOURNAL_SEQ_NO                    %INT%                        ,
JOURNAL_REG_DATETIME              %DATETIME6%                  ,
JOURNAL_ACTION_CLASS              %VARCHR%(8)                  ,

REGULARLY_ID                      %INT%                        ,
SYMPHONY_CLASS_NO                 %INT%                        ,
OPERATION_NO_IDBH                 %INT%                        ,
SYMPHONY_INSTANCE_NO              %INT%                        ,
STATUS_ID                         %INT%                        ,
NEXT_EXECUTION_DATE               %DATETIME6%                  ,
START_DATE                        %DATETIME6%                  ,
END_DATE                          %DATETIME6%                  ,
EXECUTION_STOP_START_DATE         %DATETIME6%                  ,
EXECUTION_STOP_END_DATE           %DATETIME6%                  ,
EXECUTION_INTERVAL                %INT%                        ,
REGULARLY_PERIOD_ID               %INT%                        ,
PATTERN_TIME                      %VARCHR%(5)                  ,
PATTERN_DAY                       %INT%                        ,
PATTERN_DAY_OF_WEEK               %INT%                        ,
PATTERN_WEEK_NUMBER               %INT%                        ,
EXECUTION_USER_ID                 %INT%                        ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,

PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_REGULARLY_STATUS
(
REGULARLY_STATUS_ID               %INT%                        ,
REGULARLY_STATUS_NAME             %VARCHR%(32)                 ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,

PRIMARY KEY (REGULARLY_STATUS_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_REGULARLY_STATUS_JNL
(
JOURNAL_SEQ_NO                    %INT%                        ,
JOURNAL_REG_DATETIME              %DATETIME6%                  ,
JOURNAL_ACTION_CLASS              %VARCHR%(8)                  ,

REGULARLY_STATUS_ID               %INT%                        ,
REGULARLY_STATUS_NAME             %VARCHR%(32)                 ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_REGULARLY_PERIOD
(
REGULARLY_PERIOD_ID               %INT%                        ,
REGULARLY_PERIOD_NAME             %VARCHR%(32)                 ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,

PRIMARY KEY (REGULARLY_PERIOD_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_REGULARLY_PERIOD_JNL
(
JOURNAL_SEQ_NO                    %INT%                        ,
JOURNAL_REG_DATETIME              %DATETIME6%                  ,
JOURNAL_ACTION_CLASS              %VARCHR%(8)                  ,

REGULARLY_PERIOD_ID               %INT%                        ,
REGULARLY_PERIOD_NAME             %VARCHR%(32)                 ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,

PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_DAY_OF_WEEK
(
DAY_OF_WEEK_ID                    %INT%                        ,
DAY_OF_WEEK_NAME                  %VARCHR%(16)                 ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,

PRIMARY KEY (DAY_OF_WEEK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_DAY_OF_WEEK_JNL
(
JOURNAL_SEQ_NO                    %INT%                        ,
JOURNAL_REG_DATETIME              %DATETIME6%                  ,
JOURNAL_ACTION_CLASS              %VARCHR%(8)                  ,

DAY_OF_WEEK_ID                    %INT%                        ,
DAY_OF_WEEK_NAME                  %VARCHR%(16)                 ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,

PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_WEEK_NUMBER
(
WEEK_NUMBER_ID                    %INT%                        ,
WEEK_NUMBER_NAME                  %VARCHR%(16)                 ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,

PRIMARY KEY (WEEK_NUMBER_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_WEEK_NUMBER_JNL
(
JOURNAL_SEQ_NO                    %INT%                        ,
JOURNAL_REG_DATETIME              %DATETIME6%                  ,
JOURNAL_ACTION_CLASS              %VARCHR%(8)                  ,

WEEK_NUMBER_ID                    %INT%                        ,
WEEK_NUMBER_NAME                  %VARCHR%(16)                 ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,

PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----



-- -------------------------------------------------------
-- --Conductro用
-- -------------------------------------------------------

-- ----Conductorインターフェース
CREATE TABLE C_CONDUCTOR_IF_INFO
(
CONDUCTOR_IF_INFO_ID               %INT%                      , -- 識別シーケンス

CONDUCTOR_STORAGE_PATH_ITA         %VARCHR%(256)              , -- ITA側のCONDUCTORインスタンス毎の共有ディレクトリ
CONDUCTOR_REFRESH_INTERVAL         %INT%                      , -- 状態監視周期(単位ミリ秒)

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (CONDUCTOR_IF_INFO_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_CONDUCTOR_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

CONDUCTOR_IF_INFO_ID               %INT%                      , -- 識別シーケンス

CONDUCTOR_STORAGE_PATH_ITA         %VARCHR%(256)              , -- ITA側のCONDUCTORインスタンス毎の共有ディレクトリ
CONDUCTOR_REFRESH_INTERVAL         %INT%                      , -- 状態監視周期(単位ミリ秒)

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Conductorインターフェース----

-- ----Conductorクラス(編集用)
CREATE TABLE C_CONDUCTOR_EDIT_CLASS_MNG
(
CONDUCTOR_CLASS_NO                %INT%                      ,

CONDUCTOR_NAME                    %VARCHR%(256)              ,
DESCRIPTION                       %VARCHR%(4000)             ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (CONDUCTOR_CLASS_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_CONDUCTOR_EDIT_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

CONDUCTOR_CLASS_NO                %INT%                      ,

CONDUCTOR_NAME                    %VARCHR%(256)              ,
DESCRIPTION                       %VARCHR%(4000)             ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Conductorクラス(編集用)----

-- ----Nodeクラス(編集用)
CREATE TABLE C_NODE_EDIT_CLASS_MNG
(
NODE_CLASS_NO                     %INT%                      ,

NODE_NAME                         %VARCHR%(256)              ,
NODE_TYPE_ID                      %INT%                      ,
ORCHESTRATOR_ID                   %INT%                      ,
PATTERN_ID                        %INT%                      ,
CONDUCTOR_CALL_CLASS_NO           %INT%                      ,
DESCRIPTION                       %VARCHR%(4000)             ,
CONDUCTOR_CLASS_NO                %INT%                      ,
OPERATION_NO_IDBH                 %INT%                      ,
SKIP_FLAG                         %INT%                      ,
NEXT_PENDING_FLAG                 %INT%                      ,
POINT_X                           %INT%                      ,
POINT_Y                           %INT%                      ,
POINT_W                           %INT%                      ,
POINT_H                           %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (NODE_CLASS_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_NODE_EDIT_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

NODE_CLASS_NO                     %INT%                      ,

NODE_NAME                         %VARCHR%(256)              ,
NODE_TYPE_ID                      %INT%                      ,
ORCHESTRATOR_ID                   %INT%                      ,
PATTERN_ID                        %INT%                      ,
CONDUCTOR_CALL_CLASS_NO           %INT%                      ,
DESCRIPTION                       %VARCHR%(4000)             ,
CONDUCTOR_CLASS_NO                %INT%                      ,
OPERATION_NO_IDBH                 %INT%                      ,
SKIP_FLAG                         %INT%                      ,
NEXT_PENDING_FLAG                 %INT%                      ,
POINT_X                           %INT%                      ,
POINT_Y                           %INT%                      ,
POINT_W                           %INT%                      ,
POINT_H                           %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Nodeクラス(編集用)----

-- ----Terminalクラス(編集用)
CREATE TABLE C_NODE_TERMINALS_EDIT_CLASS_MNG
(
TERMINAL_CLASS_NO                 %INT%                      ,

TERMINAL_CLASS_NAME               %VARCHR%(256)              ,
TERMINAL_TYPE_ID                  %INT%                      ,
NODE_CLASS_NO                     %INT%                      ,
CONDUCTOR_CLASS_NO                %INT%                      ,
CONNECTED_NODE_NAME               %VARCHR%(256)              ,
LINE_NAME                         %VARCHR%(256)              ,
TERMINAL_NAME                     %VARCHR%(256)              ,
CONDITIONAL_ID                    %VARCHR%(256)              ,
CASE_NO                           %INT%                      ,
DESCRIPTION                       %VARCHR%(4000)             ,
POINT_X                           %INT%                      ,
POINT_Y                           %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (TERMINAL_CLASS_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_NODE_TERMINALS_EDIT_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

TERMINAL_CLASS_NO                 %INT%                      ,

TERMINAL_CLASS_NAME               %VARCHR%(256)              ,
TERMINAL_TYPE_ID                  %INT%                      ,
NODE_CLASS_NO                     %INT%                      ,
CONDUCTOR_CLASS_NO                %INT%                      ,
CONNECTED_NODE_NAME               %VARCHR%(256)              ,
LINE_NAME                         %VARCHR%(256)              ,
TERMINAL_NAME                     %VARCHR%(256)              ,
CONDITIONAL_ID                    %VARCHR%(256)              ,
CASE_NO                           %INT%                      ,
DESCRIPTION                       %VARCHR%(4000)             ,
POINT_X                           %INT%                      ,
POINT_Y                           %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Terminalクラス(編集用)----

-- ----Conductorクラス
CREATE TABLE C_CONDUCTOR_CLASS_MNG
(
CONDUCTOR_CLASS_NO                %INT%                      ,

CONDUCTOR_NAME                    %VARCHR%(256)              ,
DESCRIPTION                       %VARCHR%(4000)             ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (CONDUCTOR_CLASS_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_CONDUCTOR_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

CONDUCTOR_CLASS_NO                %INT%                      ,

CONDUCTOR_NAME                    %VARCHR%(256)              ,
DESCRIPTION                       %VARCHR%(4000)             ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Conductorクラス----

-- ----Nodeクラス
CREATE TABLE C_NODE_CLASS_MNG
(
NODE_CLASS_NO                     %INT%                      ,

NODE_NAME                         %VARCHR%(256)              ,
NODE_TYPE_ID                      %INT%                      ,
ORCHESTRATOR_ID                   %INT%                      ,
PATTERN_ID                        %INT%                      ,
CONDUCTOR_CALL_CLASS_NO           %INT%                      ,
DESCRIPTION                       %VARCHR%(4000)             ,
CONDUCTOR_CLASS_NO                %INT%                      ,
OPERATION_NO_IDBH                 %INT%                      ,
SKIP_FLAG                         %INT%                      ,
NEXT_PENDING_FLAG                 %INT%                      ,
POINT_X                           %INT%                      ,
POINT_Y                           %INT%                      ,
POINT_W                           %INT%                      ,
POINT_H                           %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (NODE_CLASS_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_NODE_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

NODE_CLASS_NO                     %INT%                      ,

NODE_NAME                         %VARCHR%(256)              ,
NODE_TYPE_ID                      %INT%                      ,
ORCHESTRATOR_ID                   %INT%                      ,
PATTERN_ID                        %INT%                      ,
CONDUCTOR_CALL_CLASS_NO           %INT%                      ,
DESCRIPTION                       %VARCHR%(4000)             ,
CONDUCTOR_CLASS_NO                %INT%                      ,
OPERATION_NO_IDBH                 %INT%                      ,
SKIP_FLAG                         %INT%                      ,
NEXT_PENDING_FLAG                 %INT%                      ,
POINT_X                           %INT%                      ,
POINT_Y                           %INT%                      ,
POINT_W                           %INT%                      ,
POINT_H                           %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Nodeクラス----

-- ----Terminalクラス
CREATE TABLE C_NODE_TERMINALS_CLASS_MNG
(
TERMINAL_CLASS_NO                 %INT%                      ,

TERMINAL_CLASS_NAME               %VARCHR%(256)              ,
TERMINAL_TYPE_ID                  %INT%                      ,
NODE_CLASS_NO                     %INT%                      ,
CONDUCTOR_CLASS_NO                %INT%                      ,
CONNECTED_NODE_NAME               %VARCHR%(256)              ,
LINE_NAME                         %VARCHR%(256)              ,
TERMINAL_NAME                     %VARCHR%(256)              ,
CONDITIONAL_ID                    %VARCHR%(256)              ,
CASE_NO                           %INT%                      ,
DESCRIPTION                       %VARCHR%(4000)             ,
POINT_X                           %INT%                      ,
POINT_Y                           %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (TERMINAL_CLASS_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_NODE_TERMINALS_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別

TERMINAL_CLASS_NO                 %INT%                      ,

TERMINAL_CLASS_NAME               %VARCHR%(256)              ,
TERMINAL_TYPE_ID                  %INT%                      ,
NODE_CLASS_NO                     %INT%                      ,
CONDUCTOR_CLASS_NO                %INT%                      ,
CONNECTED_NODE_NAME               %VARCHR%(256)              ,
LINE_NAME                         %VARCHR%(256)              ,
TERMINAL_NAME                     %VARCHR%(256)              ,
CONDITIONAL_ID                    %VARCHR%(256)              ,
CASE_NO                           %INT%                      ,
DESCRIPTION                       %VARCHR%(4000)             ,
POINT_X                           %INT%                      ,
POINT_Y                           %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Terminalクラス----


-- ----Conductorインスタンス
CREATE TABLE C_CONDUCTOR_INSTANCE_MNG
(
CONDUCTOR_INSTANCE_NO             %INT%                      ,

I_CONDUCTOR_CLASS_NO              %INT%                      ,
I_CONDUCTOR_NAME                  %VARCHR%(256)              ,
I_DESCRIPTION                     %VARCHR%(4000)             ,
OPERATION_NO_UAPK                 %INT%                      ,
I_OPERATION_NAME                  %VARCHR%(256)              , 
STATUS_ID                         %INT%                      ,
EXECUTION_USER                    %VARCHR%(80)               ,
ABORT_EXECUTE_FLAG                %INT%                      ,
CONDUCTOR_CALL_FLAG               %INT%                      ,
CONDUCTOR_CALLER_NO               %INT%                      ,
TIME_BOOK                         %DATETIME6%                ,
TIME_START                        %DATETIME6%                ,
TIME_END                          %DATETIME6%                ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (CONDUCTOR_INSTANCE_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_CONDUCTOR_INSTANCE_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別
--
CONDUCTOR_INSTANCE_NO             %INT%                      ,
--
I_CONDUCTOR_CLASS_NO              %INT%                      ,
I_CONDUCTOR_NAME                   %VARCHR%(256)              ,
I_DESCRIPTION                     %VARCHR%(4000)             ,
OPERATION_NO_UAPK                 %INT%                      ,
I_OPERATION_NAME                  %VARCHR%(256)              ,
STATUS_ID                         %INT%                      ,
EXECUTION_USER                    %VARCHR%(80)               ,
ABORT_EXECUTE_FLAG                %INT%                      ,
CONDUCTOR_CALL_FLAG               %INT%                      ,
CONDUCTOR_CALLER_NO               %INT%                      ,
TIME_BOOK                         %DATETIME6%                ,
TIME_START                        %DATETIME6%                ,
TIME_END                          %DATETIME6%                ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Conductorインスタンス----

-- ----Nodeインスタンス
CREATE TABLE C_NODE_INSTANCE_MNG
(
NODE_INSTANCE_NO                  %INT%                      ,

I_NODE_CLASS_NO                   %INT%                      ,
I_NODE_TYPE_ID                    %INT%                      ,
I_ORCHESTRATOR_ID                 %INT%                      ,
I_PATTERN_ID                      %INT%                      ,
I_PATTERN_NAME                    %VARCHR%(256)              ,
I_TIME_LIMIT                      %INT%                      ,
I_ANS_HOST_DESIGNATE_TYPE_ID      %INT%                      ,
I_ANS_WINRM_ID                    %INT%                      ,
I_DSC_RETRY_TIMEOUT               %INT%                      ,
I_MOVEMENT_SEQ                    %INT%                      ,
I_NEXT_PENDING_FLAG               %INT%                      ,
I_DESCRIPTION                     %VARCHR%(4000)             ,
CONDUCTOR_INSTANCE_NO             %INT%                      ,
CONDUCTOR_INSTANCE_CALL_NO        %INT%                      ,
EXECUTION_NO                      %INT%                      ,
STATUS_ID                         %INT%                      ,
ABORT_RECEPTED_FLAG               %INT%                      ,
TIME_START                        %DATETIME6%                ,
TIME_END                          %DATETIME6%                ,
RELEASED_FLAG                     %INT%                      ,

EXE_SKIP_FLAG                     %INT%                      ,
OVRD_OPERATION_NO_UAPK            %INT%                      ,
OVRD_I_OPERATION_NAME             %VARCHR%(256)              ,
OVRD_I_OPERATION_NO_IDBH          %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ

PRIMARY KEY (NODE_INSTANCE_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_NODE_INSTANCE_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                , -- 履歴用変更種別
NODE_INSTANCE_NO                  %INT%                      ,

I_NODE_CLASS_NO                   %INT%                      ,
I_NODE_TYPE_ID                    %INT%                      ,
I_ORCHESTRATOR_ID                 %INT%                      ,
I_PATTERN_ID                      %INT%                      ,
I_PATTERN_NAME                    %VARCHR%(256)              ,
I_TIME_LIMIT                      %INT%                      ,
I_ANS_HOST_DESIGNATE_TYPE_ID      %INT%                      ,
I_ANS_WINRM_ID                    %INT%                      ,
I_DSC_RETRY_TIMEOUT               %INT%                      ,
I_MOVEMENT_SEQ                    %INT%                      ,
I_NEXT_PENDING_FLAG               %INT%                      ,
I_DESCRIPTION                     %VARCHR%(4000)             ,
CONDUCTOR_INSTANCE_NO             %INT%                      ,
CONDUCTOR_INSTANCE_CALL_NO        %INT%                      ,
EXECUTION_NO                      %INT%                      ,
STATUS_ID                         %INT%                      ,
ABORT_RECEPTED_FLAG               %INT%                      ,
TIME_START                        %DATETIME6%                ,
TIME_END                          %DATETIME6%                ,
RELEASED_FLAG                     %INT%                      ,

EXE_SKIP_FLAG                     %INT%                      ,
OVRD_OPERATION_NO_UAPK            %INT%                      ,
OVRD_I_OPERATION_NAME             %VARCHR%(256)              ,
OVRD_I_OPERATION_NO_IDBH          %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
ACCESS_AUTH                       TEXT                       ,
NOTE                              %VARCHR%(4000)             , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Nodeインスタンス----


-- ----NODEタイプマスタ
CREATE TABLE B_NODE_TYPE_MASTER
(
NODE_TYPE_ID                      %INT%                             ,

NODE_TYPE_NAME                    %VARCHR%(64)                      ,

DISP_SEQ                          %INT%                             , -- 表示順序, 
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ

PRIMARY KEY (NODE_TYPE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_NODE_TYPE_MASTER_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別

NODE_TYPE_ID                      %INT%                             ,

NODE_TYPE_NAME                    %VARCHR%(64)                      ,

DISP_SEQ                          %INT%                             , -- 表示順序, 
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- NODEタイプマスタ----

-- ----TERMINALタイプマスタ
CREATE TABLE B_TERMINAL_TYPE_MASTER
(
TERMINAL_TYPE_ID                  %INT%                             , 

TERMINAL_TYPE_NAME                %VARCHR%(64)                      ,

DISP_SEQ                          %INT%                             , -- 表示順序, 
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ

PRIMARY KEY (TERMINAL_TYPE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_TERMINAL_TYPE_MASTER_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別

TERMINAL_TYPE_ID                  %INT%                             ,

TERMINAL_TYPE_NAME                %VARCHR%(64)                      ,

DISP_SEQ                          %INT%                             , -- 表示順序, 
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
--TERMINALタイプマスタ ----

-- ----SensitiveFマスタ
CREATE TABLE B_SENSITIVE_FLAG
(
VARS_SENSITIVE                    %INT%                            ,
VARS_SENSITIVE_SELECT             %VARCHR%(16)                     ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (VARS_SENSITIVE)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_SENSITIVE_FLAG_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
VARS_SENSITIVE                    %INT%                            ,
VARS_SENSITIVE_SELECT             %VARCHR%(16)                     ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- SensitiveFマスタ----

-- -------------------------------------------------------
-- --定期作業実行用(Conductor)
-- -------------------------------------------------------
-- ----定期作業実行用(Conductor)
CREATE TABLE C_REGULARLY2_LIST
(
REGULARLY_ID                      %INT%                        ,
CONDUCTOR_CLASS_NO                %INT%                        ,
OPERATION_NO_IDBH                 %INT%                        ,
CONDUCTOR_INSTANCE_NO             %INT%                        ,
STATUS_ID                         %INT%                        ,
NEXT_EXECUTION_DATE               %DATETIME6%                  ,
START_DATE                        %DATETIME6%                  ,
END_DATE                          %DATETIME6%                  ,
EXECUTION_STOP_START_DATE         %DATETIME6%                  ,
EXECUTION_STOP_END_DATE           %DATETIME6%                  ,
EXECUTION_INTERVAL                %INT%                        ,
REGULARLY_PERIOD_ID               %INT%                        ,
PATTERN_TIME                      %VARCHR%(5)                  ,
PATTERN_DAY                       %INT%                        ,
PATTERN_DAY_OF_WEEK               %INT%                        ,
PATTERN_WEEK_NUMBER               %INT%                        ,
EXECUTION_USER_ID                 %INT%                        ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,

PRIMARY KEY (REGULARLY_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE C_REGULARLY2_LIST_JNL
(
JOURNAL_SEQ_NO                    %INT%                        ,
JOURNAL_REG_DATETIME              %DATETIME6%                  ,
JOURNAL_ACTION_CLASS              %VARCHR%(8)                  ,

REGULARLY_ID                      %INT%                        ,
CONDUCTOR_CLASS_NO                %INT%                        ,
OPERATION_NO_IDBH                 %INT%                        ,
CONDUCTOR_INSTANCE_NO             %INT%                        ,
STATUS_ID                         %INT%                        ,
NEXT_EXECUTION_DATE               %DATETIME6%                  ,
START_DATE                        %DATETIME6%                  ,
END_DATE                          %DATETIME6%                  ,
EXECUTION_STOP_START_DATE         %DATETIME6%                  ,
EXECUTION_STOP_END_DATE           %DATETIME6%                  ,
EXECUTION_INTERVAL                %INT%                        ,
REGULARLY_PERIOD_ID               %INT%                        ,
PATTERN_TIME                      %VARCHR%(5)                  ,
PATTERN_DAY                       %INT%                        ,
PATTERN_DAY_OF_WEEK               %INT%                        ,
PATTERN_WEEK_NUMBER               %INT%                        ,
EXECUTION_USER_ID                 %INT%                        ,
DISP_SEQ                          %INT%                        ,
ACCESS_AUTH                       TEXT                         ,
NOTE                              %VARCHR%(4000)               ,
DISUSE_FLAG                       %VARCHR%(1)                  ,
LAST_UPDATE_TIMESTAMP             %DATETIME6%                  ,
LAST_UPDATE_USER                  %INT%                        ,

PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 定期作業実行用(Conductor)----





-- *****************************************************************************
-- *** ITA-BASE Tables *****                                                 ***
-- *****************************************************************************



-- *****************************************************************************
-- *** ***** COBBLER Tables                                                  ***
-- *****************************************************************************
-- ----更新系テーブル作成
CREATE TABLE C_COBBLER_PROFILE
(
COBBLER_PROFILE_ID                %INT%                            , -- 識別シーケンス0051

COBBLER_PROFILE_NAME              %VARCHR%(256)                    ,

DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (COBBLER_PROFILE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----



-- ----履歴系テーブル作成
CREATE TABLE C_COBBLER_PROFILE_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

COBBLER_PROFILE_ID                %INT%                            , -- 識別シーケンス0051

COBBLER_PROFILE_NAME              %VARCHR%(256)                    ,

DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- *****************************************************************************
-- *** COBBLER Tables *****                                                  ***
-- *****************************************************************************

-- *****************************************************************************
-- *** *****  WEB-DBCORE Views                                               ***
-- *****************************************************************************
-- ここからWEB-DBCORE用
CREATE VIEW D_ACCOUNT_LIST AS 
SELECT TAB_A.USER_ID              ,
       TAB_A.USERNAME             ,
       TAB_A.PASSWORD             ,
       TAB_A.USERNAME_JP          ,
       TAB_A.MAIL_ADDRESS         ,
       TAB_A.PW_LAST_UPDATE_TIME  ,
       TAB_B.LOCK_ID              ,
       TAB_B.MISS_INPUT_COUNTER   ,
       TAB_B.LOCKED_TIMESTAMP     ,
       [%CONCAT_HEAD/%]TAB_A.USER_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.USERNAME[%CONCAT_TAIL/%] USER_PULLDOWN,
       TAB_C.USER_JUDGE_ID        ,
       TAB_C.AD_USER_SID          ,
       TAB_A.AUTH_TYPE            ,
       TAB_A.PROVIDER_ID          ,
       TAB_A.PROVIDER_USER_ID     ,
       TAB_A.ACCESS_AUTH          ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_01,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM   A_ACCOUNT_LIST TAB_A
LEFT JOIN A_ACCOUNT_LOCK TAB_B ON (TAB_A.USER_ID = TAB_B.USER_ID)
LEFT JOIN A_AD_USER_JUDGEMENT TAB_C ON (TAB_A.USER_ID = TAB_C.ITA_USER_ID)
WHERE  TAB_A.USER_ID > 0;

CREATE VIEW D_ACCOUNT_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.USER_ID              ,
       TAB_A.USERNAME             ,
       TAB_A.PASSWORD             ,
       TAB_A.USERNAME_JP          ,
       TAB_A.MAIL_ADDRESS         ,
       TAB_A.PW_LAST_UPDATE_TIME  ,
       TAB_B.LOCK_ID              ,
       TAB_B.MISS_INPUT_COUNTER   ,
       TAB_B.LOCKED_TIMESTAMP     ,
       [%CONCAT_HEAD/%]TAB_A.USER_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.USERNAME[%CONCAT_TAIL/%] USER_PULLDOWN,
       TAB_C.USER_JUDGE_ID        ,
       TAB_C.AD_USER_SID          ,
       TAB_A.AUTH_TYPE            ,
       TAB_A.PROVIDER_ID          ,
       TAB_A.PROVIDER_USER_ID     ,
       TAB_A.ACCESS_AUTH          ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_01,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM   A_ACCOUNT_LIST_JNL TAB_A
LEFT JOIN A_ACCOUNT_LOCK TAB_B ON (TAB_A.USER_ID = TAB_B.USER_ID)
LEFT JOIN A_AD_USER_JUDGEMENT TAB_C ON (TAB_A.USER_ID = TAB_C.ITA_USER_ID)
WHERE  TAB_A.USER_ID > 0;

CREATE VIEW D_MENU_GROUP_LIST AS 
SELECT TAB_A.MENU_GROUP_ID        ,
       TAB_A.MENU_GROUP_NAME      ,
       TAB_A.MENU_GROUP_ID          MENU_GROUP_ID_CLONE,
       [%CONCAT_HEAD/%]TAB_A.MENU_GROUP_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_GROUP_NAME[%CONCAT_TAIL/%] MENU_GROUP_PULLDOWN,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
FROM   A_MENU_GROUP_LIST TAB_A;

CREATE VIEW D_MENU_GROUP_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.MENU_GROUP_ID        ,
       TAB_A.MENU_GROUP_NAME      ,
       TAB_A.MENU_GROUP_ID          MENU_GROUP_ID_CLONE,
       [%CONCAT_HEAD/%]TAB_A.MENU_GROUP_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_GROUP_NAME[%CONCAT_TAIL/%] MENU_GROUP_PULLDOWN,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
FROM   A_MENU_GROUP_LIST_JNL TAB_A;

CREATE VIEW D_ROLE_LIST AS 
SELECT TAB_A.ROLE_ID              ,
       TAB_A.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       [%CONCAT_HEAD/%]TAB_A.ROLE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.ROLE_NAME[%CONCAT_TAIL/%] ROLE_PULLDOWN,
       TAB_B.GROUP_JUDGE_ID       ,
       TAB_B.AD_GROUP_SID         ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER     ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01   
FROM   A_ROLE_LIST TAB_A
LEFT JOIN A_AD_GROUP_JUDGEMENT TAB_B ON (TAB_A.ROLE_ID = TAB_B.ITA_ROLE_ID);

CREATE VIEW D_ROLE_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.ROLE_ID              ,
       TAB_A.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       [%CONCAT_HEAD/%]TAB_A.ROLE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.ROLE_NAME[%CONCAT_TAIL/%] ROLE_PULLDOWN,
       TAB_B.GROUP_JUDGE_ID       ,
       TAB_B.AD_GROUP_SID         ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER     ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01   
FROM   A_ROLE_LIST_JNL TAB_A
LEFT JOIN A_AD_GROUP_JUDGEMENT TAB_B ON (TAB_A.ROLE_ID = TAB_B.ITA_ROLE_ID);


CREATE VIEW D_MENU_LIST AS 
SELECT TAB_A.MENU_ID              ,
       TAB_A.MENU_GROUP_ID        ,
       TAB_B.MENU_GROUP_NAME      ,
       TAB_A.MENU_GROUP_ID          MENU_GROUP_ID_CLONE,
       TAB_A.MENU_NAME            ,
       [%CONCAT_HEAD/%]TAB_A.MENU_GROUP_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_B.MENU_GROUP_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_NAME[%CONCAT_TAIL/%] MENU_PULLDOWN,
       TAB_A.LOGIN_NECESSITY      ,
       TAB_A.SERVICE_STATUS       ,
       TAB_A.AUTOFILTER_FLG       ,
       TAB_A.INITIAL_FILTER_FLG   ,
       TAB_A.WEB_PRINT_LIMIT      ,
       TAB_A.WEB_PRINT_CONFIRM    ,
       TAB_A.XLS_PRINT_LIMIT      ,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER     ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM   A_MENU_LIST TAB_A
LEFT JOIN A_MENU_GROUP_LIST TAB_B ON (TAB_A.MENU_GROUP_ID = TAB_B.MENU_GROUP_ID);
-- 紐づいたメニューグループが廃止されているメニューも選択できるようにするため、WHERE句で活性済レコードのみ、と絞り込まない。


CREATE VIEW D_MENU_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.MENU_ID              ,
       TAB_A.MENU_GROUP_ID        ,
       TAB_B.MENU_GROUP_NAME      ,
       TAB_A.MENU_GROUP_ID          MENU_GROUP_ID_CLONE,
       TAB_A.MENU_NAME            ,
       [%CONCAT_HEAD/%]TAB_A.MENU_GROUP_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_B.MENU_GROUP_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_NAME[%CONCAT_TAIL/%] MENU_PULLDOWN,
       TAB_A.LOGIN_NECESSITY      ,
       TAB_A.SERVICE_STATUS       ,
       TAB_A.AUTOFILTER_FLG       ,
       TAB_A.INITIAL_FILTER_FLG   ,
       TAB_A.WEB_PRINT_LIMIT      ,
       TAB_A.WEB_PRINT_CONFIRM    ,
       TAB_A.XLS_PRINT_LIMIT      ,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER     ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM   A_MENU_LIST_JNL TAB_A
LEFT JOIN A_MENU_GROUP_LIST TAB_B ON (TAB_A.MENU_GROUP_ID = TAB_B.MENU_GROUP_ID);

CREATE VIEW D_ROLE_MENU_LINK_LIST AS 
SELECT TAB_A.LINK_ID              ,
       TAB_A.ROLE_ID              ,
       TAB_D.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       TAB_B.MENU_GROUP_ID        ,
       TAB_C.MENU_GROUP_NAME      ,
       TAB_A.MENU_ID              ,
       TAB_B.MENU_NAME            ,
       TAB_A.MENU_ID                MENU_ID_CLONE,
       TAB_A.PRIVILEGE            ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER 
FROM A_ROLE_MENU_LINK_LIST TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
LEFT JOIN A_ROLE_LIST TAB_D ON (TAB_A.ROLE_ID = TAB_D.ROLE_ID);

CREATE VIEW D_ROLE_MENU_LINK_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.LINK_ID              ,
       TAB_A.ROLE_ID              ,
       TAB_D.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       TAB_B.MENU_GROUP_ID        ,
       TAB_C.MENU_GROUP_NAME      ,
       TAB_A.MENU_ID              ,
       TAB_B.MENU_NAME            ,
       TAB_A.MENU_ID                MENU_ID_CLONE,
       TAB_A.PRIVILEGE            ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER 
FROM A_ROLE_MENU_LINK_LIST_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
LEFT JOIN A_ROLE_LIST TAB_D ON (TAB_A.ROLE_ID = TAB_D.ROLE_ID);

CREATE VIEW D_ROLE_ACCOUNT_LINK_LIST AS 
SELECT TAB_A.LINK_ID              ,
       TAB_A.ROLE_ID              ,
       TAB_C.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       TAB_A.USER_ID              ,
       TAB_B.USERNAME             ,
       TAB_A.USER_ID                USER_ID_CLONE,
       TAB_A.DEF_ACCESS_AUTH_FLAG ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER 
FROM A_ROLE_ACCOUNT_LINK_LIST TAB_A
LEFT JOIN A_ACCOUNT_LIST TAB_B ON (TAB_A.USER_ID = TAB_B.USER_ID)
LEFT JOIN A_ROLE_LIST TAB_C ON (TAB_A.ROLE_ID = TAB_C.ROLE_ID)
WHERE TAB_A.USER_ID > 0;

CREATE VIEW D_ROLE_ACCOUNT_LINK_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.LINK_ID              ,
       TAB_A.ROLE_ID              ,
       TAB_C.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       TAB_A.USER_ID              ,
       TAB_B.USERNAME             ,
       TAB_A.USER_ID                USER_ID_CLONE,
       TAB_A.DEF_ACCESS_AUTH_FLAG ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER 
FROM A_ROLE_ACCOUNT_LINK_LIST_JNL TAB_A
LEFT JOIN A_ACCOUNT_LIST TAB_B ON (TAB_A.USER_ID = TAB_B.USER_ID)
LEFT JOIN A_ROLE_LIST TAB_C ON (TAB_A.ROLE_ID = TAB_C.ROLE_ID)
WHERE TAB_A.USER_ID > 0;

CREATE VIEW D_PROVIDER_LIST AS
SELECT TAB_A.PROVIDER_ID,
       TAB_A.PROVIDER_NAME,
       TAB_A.LOGO,
       TAB_A.AUTH_TYPE,
       TAB_A.VISIBLE_FLAG,
       TAB_A.ACCESS_AUTH,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_LIST TAB_A;

CREATE VIEW D_PROVIDER_LIST_JNL AS
SELECT TAB_A.JOURNAL_SEQ_NO,
       TAB_A.JOURNAL_REG_DATETIME,
       TAB_A.JOURNAL_ACTION_CLASS,
       TAB_A.PROVIDER_ID,
       TAB_A.PROVIDER_NAME,
       TAB_A.LOGO,
       TAB_A.AUTH_TYPE,
       TAB_A.VISIBLE_FLAG,
       TAB_A.ACCESS_AUTH,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_LIST_JNL TAB_A;

CREATE VIEW D_PROVIDER_ATTRIBUTE_LIST AS
SELECT TAB_A.PROVIDER_ATTRIBUTE_ID,
       TAB_A.PROVIDER_ID,
       TAB_A.NAME,
       TAB_A.VALUE,
       TAB_A.ACCESS_AUTH,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_ATTRIBUTE_LIST TAB_A;

CREATE VIEW D_PROVIDER_ATTRIBUTE_LIST_JNL AS
SELECT TAB_A.JOURNAL_SEQ_NO,
       TAB_A.JOURNAL_REG_DATETIME,
       TAB_A.JOURNAL_ACTION_CLASS,
       TAB_A.PROVIDER_ATTRIBUTE_ID,
       TAB_A.PROVIDER_ID,
       TAB_A.NAME,
       TAB_A.VALUE,
       TAB_A.ACCESS_AUTH,
       TAB_A.NOTE,
       TAB_A.DISUSE_FLAG,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM A_PROVIDER_ATTRIBUTE_LIST_JNL TAB_A;

CREATE VIEW D_SEQUENCE AS 
SELECT TAB_A.NAME                 ,
       TAB_A.VALUE                ,
       TAB_B.MENU_NAME            ,
       TAB_B.MENU_GROUP_NAME      ,
       TAB_A.DISP_SEQ             ,
       TAB_A.NOTE                 ,
       '0' as DISUSE_FLAG         ,
       TAB_A.LAST_UPDATE_TIMESTAMP
FROM A_SEQUENCE  as TAB_A
     LEFT JOIN D_MENU_LIST as TAB_B on TAB_A.MENU_ID = TAB_B.MENU_ID
WHERE TAB_A.MENU_ID IS NOT NULL AND
      TAB_B.DISUSE_FLAG = '0';

-- *****************************************************************************
-- *** WEB-DBCORE Views *****                                                ***
-- *****************************************************************************



-- *****************************************************************************
-- *** ***** ITA-BASE Views                                                  ***
-- *****************************************************************************
CREATE VIEW E_STM_LIST 
AS 

SELECT TAB_A.SYSTEM_ID                        SYSTEM_ID                     ,
       TAB_A.HARDAWRE_TYPE_ID                 HARDAWRE_TYPE_ID              ,
       TAB_A.HOSTNAME                         HOSTNAME                      ,
       [%CONCAT_HEAD/%]TAB_A.SYSTEM_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.HOSTNAME[%CONCAT_TAIL/%] HOST_PULLDOWN,
       TAB_A.IP_ADDRESS                       IP_ADDRESS                    ,
       TAB_A.PROTOCOL_ID                      PROTOCOL_ID                   ,
       TAB_A.LOGIN_USER                       LOGIN_USER                    ,
       TAB_A.LOGIN_PW_HOLD_FLAG               LOGIN_PW_HOLD_FLAG            ,
       TAB_A.LOGIN_PW                         LOGIN_PW                      ,
       TAB_A.ETH_WOL_MAC_ADDRESS              ETH_WOL_MAC_ADDRESS           ,
       TAB_A.ETH_WOL_NET_DEVICE               ETH_WOL_NET_DEVICE            ,
       TAB_A.LOGIN_AUTH_TYPE                  LOGIN_AUTH_TYPE               ,
       TAB_A.WINRM_PORT                       WINRM_PORT                    ,
       TAB_A.OS_TYPE_ID                       OS_TYPE_ID                    ,
       TAB_A.HOSTNAME                         SYSTEM_NAME                   ,
       TAB_A.COBBLER_PROFILE_ID               COBBLER_PROFILE_ID            ,
       TAB_A.INTERFACE_TYPE                   INTERFACE_TYPE                ,
       TAB_A.MAC_ADDRESS                      MAC_ADDRESS                   ,
       TAB_A.NETMASK                          NETMASK                       ,
       TAB_A.GATEWAY                          GATEWAY                       ,
       TAB_A.STATIC                           STATIC                        ,

       TAB_A.CONN_SSH_KEY_FILE                CONN_SSH_KEY_FILE             ,

       TAB_A.DISP_SEQ                         DISP_SEQ                      ,
       TAB_A.ACCESS_AUTH                      ACCESS_AUTH                   ,
       TAB_A.NOTE                             NOTE                          ,
       TAB_A.DISUSE_FLAG                      DISUSE_FLAG                   ,
       TAB_A.LAST_UPDATE_TIMESTAMP            LAST_UPDATE_TIMESTAMP         ,
       TAB_A.LAST_UPDATE_USER                 LAST_UPDATE_USER

FROM C_STM_LIST TAB_A;

CREATE VIEW E_STM_LIST_JNL 
AS 

SELECT TAB_A.JOURNAL_SEQ_NO                   JOURNAL_SEQ_NO                ,
       TAB_A.JOURNAL_REG_DATETIME             JOURNAL_REG_DATETIME          ,
       TAB_A.JOURNAL_ACTION_CLASS             JOURNAL_ACTION_CLASS          ,

       TAB_A.SYSTEM_ID                        SYSTEM_ID                     ,
       TAB_A.HARDAWRE_TYPE_ID                 HARDAWRE_TYPE_ID              ,
       TAB_A.HOSTNAME                         HOSTNAME                      ,
       [%CONCAT_HEAD/%]TAB_A.SYSTEM_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.HOSTNAME[%CONCAT_TAIL/%] HOST_PULLDOWN,
       TAB_A.IP_ADDRESS                       IP_ADDRESS                    ,
       TAB_A.PROTOCOL_ID                      PROTOCOL_ID                   ,
       TAB_A.LOGIN_USER                       LOGIN_USER                    ,
       TAB_A.LOGIN_PW_HOLD_FLAG               LOGIN_PW_HOLD_FLAG            ,
       TAB_A.LOGIN_PW                         LOGIN_PW                      ,
       TAB_A.ETH_WOL_MAC_ADDRESS              ETH_WOL_MAC_ADDRESS           ,
       TAB_A.ETH_WOL_NET_DEVICE               ETH_WOL_NET_DEVICE            ,
       TAB_A.LOGIN_AUTH_TYPE                  LOGIN_AUTH_TYPE               ,
       TAB_A.WINRM_PORT                       WINRM_PORT                    ,
       TAB_A.OS_TYPE_ID                       OS_TYPE_ID                    ,
       TAB_A.HOSTNAME                         SYSTEM_NAME                   ,
       TAB_A.COBBLER_PROFILE_ID               COBBLER_PROFILE_ID            ,
       TAB_A.INTERFACE_TYPE                   INTERFACE_TYPE                ,
       TAB_A.MAC_ADDRESS                      MAC_ADDRESS                   ,
       TAB_A.NETMASK                          NETMASK                       ,
       TAB_A.GATEWAY                          GATEWAY                       ,
       TAB_A.STATIC                           STATIC                        ,

       TAB_A.CONN_SSH_KEY_FILE                CONN_SSH_KEY_FILE             ,

       TAB_A.DISP_SEQ                         DISP_SEQ                      ,
       TAB_A.ACCESS_AUTH                      ACCESS_AUTH                   ,
       TAB_A.NOTE                             NOTE                          ,
       TAB_A.DISUSE_FLAG                      DISUSE_FLAG                   ,
       TAB_A.LAST_UPDATE_TIMESTAMP            LAST_UPDATE_TIMESTAMP         ,
       TAB_A.LAST_UPDATE_USER                 LAST_UPDATE_USER

FROM C_STM_LIST_JNL TAB_A;

CREATE VIEW E_OPERATION_LIST 
AS 
SELECT TAB_A.OPERATION_NO_UAPK    ,
       TAB_A.OPERATION_NAME       ,
       TAB_A.OPERATION_DATE       ,
       TAB_A.OPERATION_NO_IDBH    ,
       [%CONCAT_HEAD/%]TAB_A.OPERATION_NO_IDBH[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.OPERATION_NAME[%CONCAT_TAIL/%] OPERATION,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
FROM C_OPERATION_LIST TAB_A;

CREATE VIEW E_OPERATION_LIST_JNL 
AS 
SELECT TAB_A.JOURNAL_SEQ_NO       ,
       TAB_A.JOURNAL_REG_DATETIME ,
       TAB_A.JOURNAL_ACTION_CLASS ,
       TAB_A.OPERATION_NO_UAPK    ,
       TAB_A.OPERATION_NAME       ,
       TAB_A.OPERATION_DATE       ,
       TAB_A.OPERATION_NO_IDBH    ,
       [%CONCAT_HEAD/%]TAB_A.OPERATION_NO_IDBH[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.OPERATION_NAME[%CONCAT_TAIL/%] OPERATION,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
FROM C_OPERATION_LIST_JNL TAB_A;

-- *****************************************************************************
-- *** ITA-BASE Views *****                                                  ***
-- *****************************************************************************


-- *****************************************************************************
-- *** ***** COBBLER Views                                                   ***
-- *****************************************************************************

-- *****************************************************************************
-- *** COBBLER Views *****                                                   ***
-- *****************************************************************************


CREATE VIEW G_OPERATION_LIST AS
SELECT OPERATION_NO_IDBH                             OPERATION_ID           ,
       OPERATION_NAME                                                       ,
       [%CONCAT_HEAD/%][%TO_CHAR%]( OPERATION_DATE, '%Y/%m/%d %H:%i' )[%CONCAT_MID/%]'_'[%CONCAT_MID/%]OPERATION_NO_IDBH[%CONCAT_MID/%]':'[%CONCAT_MID/%]OPERATION_NAME[%CONCAT_TAIL/%] OPERATION_ID_N_NAME,
       CASE
           WHEN LAST_EXECUTE_TIMESTAMP IS NULL THEN OPERATION_DATE
           ELSE LAST_EXECUTE_TIMESTAMP
       END BASE_TIMESTAMP,
       OPERATION_DATE                                                       ,
       [%TO_CHAR%]( OPERATION_DATE, '%Y/%m/%d %H:%i' ) OPERATION_DATE_DISP  ,
       LAST_EXECUTE_TIMESTAMP                                               ,
       ACCESS_AUTH                                                          ,
       NOTE                                                                 ,
       DISUSE_FLAG                                                          ,
       LAST_UPDATE_TIMESTAMP                                                ,
       LAST_UPDATE_USER
FROM   C_OPERATION_LIST;

-- *****************************************************************************
-- *** ***** 代入値自動登録設定関連                                          ***
-- *****************************************************************************
-- -------------------------------------------------------
-- --「紐付対象メニュー」メニュー用
-- -------------------------------------------------------
CREATE TABLE B_CMDB_MENU_LIST (
MENU_LIST_ID                   %INT%                   , -- 識別シーケンス
MENU_ID                        %INT%                   , -- メニューID

SHEET_TYPE                     %INT%                   , -- シートタイプ　null/1:ホスト/オペレーションを含む　2:ホストのみ
ACCESS_AUTH_FLG                %INT%                   , -- アクセス許可ロール有無(メニューにアクセス許可ロールがあるかどうか　1:あり,それ以外:なし)

DISP_SEQ                       %INT%                   , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           %VARCHR%(4000)          , -- 備考
DISUSE_FLAG                    %VARCHR%(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%             , -- 最終更新日時
LAST_UPDATE_USER               %INT%                   , -- 最終更新ユーザ
PRIMARY KEY(MENU_LIST_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_CMDB_MENU_LIST_JNL (
JOURNAL_SEQ_NO                 %INT%                   , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           %DATETIME6%             , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           %VARCHR%(8)             , -- 履歴用変更種別

MENU_LIST_ID                   %INT%                   , -- 識別シーケンス
MENU_ID                        %INT%                   , -- メニューID

SHEET_TYPE                     %INT%                   , -- シートタイプ　null/1:ホスト/オペレーションを含む　2:ホストのみ
ACCESS_AUTH_FLG                %INT%                   , -- アクセス許可ロール有無(メニューにアクセス許可ロールがあるかどうか　1:あり,それ以外:なし)

DISP_SEQ                       %INT%                   , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           %VARCHR%(4000)          , -- 備考
DISUSE_FLAG                    %VARCHR%(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%             , -- 最終更新日時
LAST_UPDATE_USER               %INT%                   , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE VIEW D_CMDB_MENU_LIST AS 
SELECT 
       TAB_A.MENU_LIST_ID                   , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_B.MENU_GROUP_ID                  ,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_B.MENU_NAME                      ,
       [%CONCAT_HEAD/%]TAB_B.MENU_GROUP_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_C.MENU_GROUP_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_B.MENU_NAME[%CONCAT_TAIL/%] MENU_PULLDOWN,
       TAB_A.SHEET_TYPE                     ,
       TAB_A.ACCESS_AUTH_FLG                ,
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER               ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01  ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02 
FROM B_CMDB_MENU_LIST TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
WHERE TAB_B.DISUSE_FLAG = '0'
;

CREATE VIEW D_CMDB_MENU_LIST_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                 ,
       TAB_A.JOURNAL_REG_DATETIME           ,
       TAB_A.JOURNAL_ACTION_CLASS           ,
       TAB_A.MENU_LIST_ID                   , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_B.MENU_GROUP_ID                  ,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_B.MENU_NAME                      ,
       [%CONCAT_HEAD/%]TAB_B.MENU_GROUP_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_C.MENU_GROUP_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_B.MENU_NAME[%CONCAT_TAIL/%] MENU_PULLDOWN,
       TAB_A.SHEET_TYPE                     ,
       TAB_A.ACCESS_AUTH_FLG                ,
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER               ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01  ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02 
FROM B_CMDB_MENU_LIST_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
WHERE TAB_B.DISUSE_FLAG = '0'
;

-- -------------------------------------------------------
-- --「紐付対象メニューテーブル管理」メニュー用
-- -------------------------------------------------------
CREATE TABLE B_CMDB_MENU_TABLE (
TABLE_ID                       %INT%                   , -- 識別シーケンス
MENU_ID                        %INT%                   , -- メニューID
TABLE_NAME                     %VARCHR%(64)            , -- テーブル名
PKEY_NAME                      %VARCHR%(64)            , -- 主キーカラム名

DISP_SEQ                       %INT%                   , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           %VARCHR%(4000)          , -- 備考
DISUSE_FLAG                    %VARCHR%(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%             , -- 最終更新日時
LAST_UPDATE_USER               %INT%                   , -- 最終更新ユーザ
PRIMARY KEY(TABLE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_CMDB_MENU_TABLE_JNL
(
JOURNAL_SEQ_NO                 %INT%                   , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           %DATETIME6%             , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           %VARCHR%(8)             , -- 履歴用変更種別

TABLE_ID                       %INT%                   , -- 識別シーケンス
MENU_ID                        %INT%                   , -- メニューID
TABLE_NAME                     %VARCHR%(64)            , -- テーブル名
PKEY_NAME                      %VARCHR%(64)            , -- 主キーカラム名

DISP_SEQ                       %INT%                   , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           %VARCHR%(4000)          , -- 備考
DISUSE_FLAG                    %VARCHR%(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%             , -- 最終更新日時
LAST_UPDATE_USER               %INT%                   , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------------------------------------
-- --「紐付対象メニューカラム管理」メニュー用
-- -------------------------------------------------------
CREATE TABLE B_CMDB_MENU_COLUMN  (
COLUMN_LIST_ID                 %INT%                   , -- 識別シーケンス
MENU_ID                        %INT%                   , -- メニューID
COL_NAME                       %VARCHR%(64)            , -- メニュー　カラム名
COL_CLASS                      %VARCHR%(64)            , -- メニュー　カラムクラス
COL_TITLE                      %VARCHR%(4096)          , -- メニュー　カラムタイトル
COL_TITLE_DISP_SEQ             %INT%                   , -- メニュー　カラム　代入値自動登録 表示順
REF_TABLE_NAME                 %VARCHR%(64)            , -- 参照テーブル名
REF_PKEY_NAME                  %VARCHR%(64)            , -- 参照テーブル主キー
REF_COL_NAME                   %VARCHR%(64)            , -- 参照テーブルカラム名

DISP_SEQ                       %INT%                   , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           %VARCHR%(4000)          , -- 備考
DISUSE_FLAG                    %VARCHR%(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%             , -- 最終更新日時
LAST_UPDATE_USER               %INT%                   , -- 最終更新ユーザ
PRIMARY KEY(COLUMN_LIST_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_CMDB_MENU_COLUMN_JNL
(
JOURNAL_SEQ_NO                 %INT%                   , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           %DATETIME6%             , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           %VARCHR%(8)             , -- 履歴用変更種別

COLUMN_LIST_ID                 %INT%                   , -- 識別シーケンス
MENU_ID                        %INT%                   , -- メニューID
COL_NAME                       %VARCHR%(64)            , -- メニュー　カラム名
COL_CLASS                      %VARCHR%(64)            , -- メニュー　カラムクラス
COL_TITLE                      %VARCHR%(4096)          , -- メニュー　カラムタイトル
COL_TITLE_DISP_SEQ             %INT%                   , -- メニュー　カラム　代入値自動登録 表示順
REF_TABLE_NAME                 %VARCHR%(64)            , -- 参照テーブル名
REF_PKEY_NAME                  %VARCHR%(64)            , -- 参照テーブル主キー
REF_COL_NAME                   %VARCHR%(64)            , -- 参照テーブルカラム名

DISP_SEQ                       %INT%                   , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           %VARCHR%(4000)          , -- 備考
DISUSE_FLAG                    %VARCHR%(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%             , -- 最終更新日時
LAST_UPDATE_USER               %INT%                   , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------------------------------------
-- --代入値自動登録設定の「登録方式」用
-- -------------------------------------------------------
CREATE TABLE B_CMDB_MENU_COL_TYPE
(
COLUMN_TYPE_ID                    %INT%                            , -- 識別シーケンス

COLUMN_TYPE_NAME                  %VARCHR%(32)                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型

DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (COLUMN_TYPE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_CMDB_MENU_COL_TYPE_JNL
(            
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

COLUMN_TYPE_ID                    %INT%                            , -- 識別シーケンス

COLUMN_TYPE_NAME                  %VARCHR%(32)                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型

DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------------------------------------
-- --非対象紐付メニューグループ一覧用
-- -------------------------------------------------------
CREATE TABLE B_CMDB_HIDE_MENU_GRP
(
HIDE_ID                           %INT%                            , -- 識別シーケンス
MENU_GROUP_ID                     %INT%                            , -- 非対象メニューグループID

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (MENU_GROUP_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_CMDB_HIDE_MENU_GRP_JNL
(            
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

HIDE_ID                           %INT%                            , -- 識別シーケンス
MENU_GROUP_ID                     %INT%                            , -- 非対象メニューグループID

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------------------------------------
-- --メニュー作成情報の「メニューグループ」用
-- -------------------------------------------------------
CREATE VIEW D_CMDB_MENU_GRP_LIST AS 
SELECT *
FROM   A_MENU_GROUP_LIST TAB_A
WHERE  MENU_GROUP_ID NOT IN 
(SELECT MENU_GROUP_ID 
 FROM  B_CMDB_HIDE_MENU_GRP);

CREATE VIEW D_CMDB_MENU_GRP_LIST_JNL AS 
SELECT *
FROM   A_MENU_GROUP_LIST_JNL TAB_A
WHERE  MENU_GROUP_ID NOT IN 
(SELECT MENU_GROUP_ID 
 FROM  B_CMDB_HIDE_MENU_GRP);

-- -------------------------------------------------------
-- --紐付対象メニューの「メニューグループ:メニュー」用
-- -------------------------------------------------------
CREATE VIEW D_CMDB_TARGET_MENU_LIST AS 
SELECT 
  TAB_A.MENU_ID,
  TAB_A.MENU_NAME,
  TAB_B.MENU_GROUP_ID,
  TAB_B.MENU_GROUP_NAME,
  [%CONCAT_HEAD/%]TAB_B.MENU_GROUP_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_B.MENU_GROUP_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_NAME[%CONCAT_TAIL/%] MENU_PULLDOWN,
  TAB_A.DISUSE_FLAG,
  TAB_A.ACCESS_AUTH,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM 
  ( A_MENU_LIST TAB_A
    INNER JOIN A_MENU_GROUP_LIST TAB_B ON TAB_B.MENU_GROUP_ID = TAB_A.MENU_GROUP_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_B.MENU_GROUP_ID IN (SELECT MENU_GROUP_ID FROM D_CMDB_MENU_GRP_LIST)
;

CREATE VIEW D_CMDB_TARGET_MENU_LIST_JNL AS 
SELECT 
  TAB_A.MENU_ID,
  TAB_A.MENU_NAME,
  TAB_B.MENU_GROUP_ID,
  TAB_B.MENU_GROUP_NAME,
  [%CONCAT_HEAD/%]TAB_B.MENU_GROUP_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_B.MENU_GROUP_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MENU_NAME[%CONCAT_TAIL/%] MENU_PULLDOWN,
  TAB_A.DISUSE_FLAG,
  TAB_A.ACCESS_AUTH,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM 
  ( A_MENU_LIST_JNL TAB_A
    INNER JOIN A_MENU_GROUP_LIST TAB_B ON TAB_B.MENU_GROUP_ID = TAB_A.MENU_GROUP_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_B.MENU_GROUP_ID IN (SELECT MENU_GROUP_ID FROM D_CMDB_MENU_GRP_LIST)
;

-- -------------------------------------------------------
-- --代入値自動登録設定のExcel、REST用「メニューグループ:メニュー:項目」
-- -------------------------------------------------------
CREATE VIEW D_CMDB_MG_MU_COL_LIST AS 
SELECT
  TAB_A.COLUMN_LIST_ID                 , 
  CONCAT(TAB_D.MENU_GROUP_ID,':',TAB_D.MENU_GROUP_NAME,':',TAB_C.MENU_ID,':',TAB_C.MENU_NAME,':',TAB_A.COLUMN_LIST_ID,':',TAB_A.COL_TITLE) MENU_COL_TITLE_PULLDOWN,
  TAB_C.MENU_ID                        ,
  TAB_B.SHEET_TYPE                     ,
  TAB_A.COL_TITLE_DISP_SEQ             ,
  TAB_A.DISP_SEQ                       ,
  TAB_A.ACCESS_AUTH                    ,
  TAB_A.NOTE                           ,
  TAB_A.DISUSE_FLAG                    ,
  TAB_A.LAST_UPDATE_TIMESTAMP          ,
  TAB_A.LAST_UPDATE_USER               ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01  ,
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02  ,
  TAB_D.ACCESS_AUTH AS ACCESS_AUTH_03
FROM        B_CMDB_MENU_COLUMN TAB_A
  LEFT JOIN B_CMDB_MENU_LIST       TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
  LEFT JOIN A_MENU_LIST            TAB_C ON (TAB_A.MENU_ID       = TAB_C.MENU_ID)
  LEFT JOIN A_MENU_GROUP_LIST      TAB_D ON (TAB_C.MENU_GROUP_ID = TAB_D.MENU_GROUP_ID)
WHERE
   TAB_A.DISUSE_FLAG = '0' AND
   TAB_B.DISUSE_FLAG = '0' AND
   TAB_C.DISUSE_FLAG = '0' AND
   TAB_D.DISUSE_FLAG = '0';

CREATE VIEW D_CMDB_MG_MU_COL_LIST_JNL AS 
SELECT 
  TAB_A.COLUMN_LIST_ID                 , 
  CONCAT(TAB_D.MENU_GROUP_ID,':',TAB_D.MENU_GROUP_NAME,':',TAB_C.MENU_ID,':',TAB_C.MENU_NAME,':',TAB_A.COLUMN_LIST_ID,':',TAB_A.COL_TITLE) MENU_COL_PULLDOWN,
  TAB_C.MENU_ID                        ,
  TAB_B.SHEET_TYPE                     ,
  TAB_A.COL_TITLE_DISP_SEQ             ,
  TAB_A.DISP_SEQ                       ,
  TAB_A.ACCESS_AUTH                    ,
  TAB_A.NOTE                           ,
  TAB_A.DISUSE_FLAG                    ,
  TAB_A.LAST_UPDATE_TIMESTAMP          ,
  TAB_A.LAST_UPDATE_USER               ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01  ,
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02  ,
  TAB_D.ACCESS_AUTH AS ACCESS_AUTH_03
FROM        B_CMDB_MENU_COLUMN_JNL TAB_A
  LEFT JOIN B_CMDB_MENU_LIST           TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
  LEFT JOIN A_MENU_LIST                TAB_C ON (TAB_A.MENU_ID       = TAB_C.MENU_ID)
  LEFT JOIN A_MENU_GROUP_LIST          TAB_D ON (TAB_C.MENU_GROUP_ID = TAB_D.MENU_GROUP_ID)
WHERE
   TAB_A.DISUSE_FLAG = '0' AND
   TAB_B.DISUSE_FLAG = '0' AND
   TAB_C.DISUSE_FLAG = '0' AND
   TAB_D.DISUSE_FLAG = '0';

-- -------------------------------------------------------
-- --代入値自動登録設定の「メニューグループ:メニュー:項目」SHEET_TYPE=1用
-- -------------------------------------------------------
CREATE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_1 AS
SELECT
 *
FROM D_CMDB_MENU_LIST TAB_A
WHERE (SHEET_TYPE IS NULL OR SHEET_TYPE = 1)
;

CREATE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_1_JNL AS
SELECT
 *
FROM D_CMDB_MENU_LIST_JNL TAB_A
WHERE (SHEET_TYPE IS NULL OR SHEET_TYPE = 1)
;

CREATE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_1 AS
SELECT
 *
FROM D_CMDB_MG_MU_COL_LIST TAB_A
WHERE (SHEET_TYPE IS NULL OR SHEET_TYPE = 1)
;

CREATE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_1_JNL AS
SELECT
 *
FROM D_CMDB_MG_MU_COL_LIST_JNL TAB_A
WHERE (SHEET_TYPE IS NULL OR SHEET_TYPE = 1)
;

CREATE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_1 AS
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

CREATE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_1_JNL AS
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

CREATE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_1_PIONEER AS
SELECT
  TAB_B.*,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01,
  TAB_A.ACCESS_AUTH_01 AS ACCESS_AUTH_02,
  TAB_A.ACCESS_AUTH_02 AS ACCESS_AUTH_03
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_1         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.COL_CLASS   <>  'MultiTextColumn' AND
  TAB_B.DISUSE_FLAG = '0'
;

CREATE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_1_PIONEER_JNL AS
SELECT
  TAB_B.*,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01,
  TAB_A.ACCESS_AUTH_01 AS ACCESS_AUTH_02,
  TAB_A.ACCESS_AUTH_02 AS ACCESS_AUTH_03
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_1_JNL         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN_JNL TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.COL_CLASS   <>  'MultiTextColumn' AND
  TAB_B.DISUSE_FLAG = '0'
;

CREATE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_1_PIONEER AS
SELECT 
  TBL_A.*
FROM 
  D_CMDB_MENU_LIST_SHEET_TYPE_1 TBL_A
WHERE
  (SELECT 
     COUNT(*) 
   FROM 
     B_CMDB_MENU_COLUMN TBL_B
   WHERE
     TBL_A.MENU_ID     =   TBL_B.MENU_ID     AND
     TBL_B.COL_CLASS   <>  'MultiTextColumn' AND
     TBL_B.DISUSE_FLAG =   '0'
  ) <> 0 
;

CREATE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_1_PIONEER_JNL AS
SELECT 
  TBL_A.*
FROM 
  D_CMDB_MENU_LIST_SHEET_TYPE_1_JNL TBL_A
WHERE
  (SELECT 
     COUNT(*) 
   FROM 
     B_CMDB_MENU_COLUMN_JNL TBL_B
   WHERE
     TBL_A.MENU_ID     =   TBL_B.MENU_ID     AND
     TBL_B.COL_CLASS   <>  'MultiTextColumn' AND
     TBL_B.DISUSE_FLAG =   '0'
  ) <> 0
;

CREATE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_1_PIONEER AS
SELECT
  TAB_A.COLUMN_LIST_ID                 ,
  CONCAT(TAB_D.MENU_GROUP_ID,':',TAB_D.MENU_GROUP_NAME,':',TAB_C.MENU_ID,':',TAB_C.MENU_NAME,':',TAB_A.COLUMN_LIST_ID,':',TAB_A.COL_TITLE) MENU_COL_TITLE_PULLDOWN,
  TAB_C.MENU_ID                        ,
  TAB_A.COL_TITLE_DISP_SEQ             ,
  TAB_B.ACCESS_AUTH                    ,
  TAB_A.DISP_SEQ                       ,
  TAB_A.NOTE                           ,
  TAB_A.DISUSE_FLAG                    ,
  TAB_A.LAST_UPDATE_TIMESTAMP          ,
  TAB_A.LAST_UPDATE_USER               ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01  ,
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM        D_CMDB_MENU_COLUMN_SHEET_TYPE_1_PIONEER    TAB_A
  LEFT JOIN D_CMDB_MENU_LIST_SHEET_TYPE_1_PIONEER      TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
  LEFT JOIN A_MENU_LIST                                TAB_C ON (TAB_A.MENU_ID       = TAB_C.MENU_ID)
  LEFT JOIN A_MENU_GROUP_LIST                          TAB_D ON (TAB_C.MENU_GROUP_ID = TAB_D.MENU_GROUP_ID)
WHERE
   TAB_A.DISUSE_FLAG = '0' AND
   TAB_B.DISUSE_FLAG = '0' AND
   TAB_C.DISUSE_FLAG = '0' AND
   TAB_D.DISUSE_FLAG = '0'
;

CREATE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_1_PIONEER_JNL AS
SELECT
  TAB_A.COLUMN_LIST_ID                 ,
  CONCAT(TAB_D.MENU_GROUP_ID,':',TAB_D.MENU_GROUP_NAME,':',TAB_C.MENU_ID,':',TAB_C.MENU_NAME,':',TAB_A.COLUMN_LIST_ID,':',TAB_A.COL_TITLE) MENU_COL_PULLDOWN,
  TAB_C.MENU_ID                        ,
  TAB_A.COL_TITLE_DISP_SEQ             ,
  TAB_B.ACCESS_AUTH                    ,
  TAB_A.DISP_SEQ                       ,
  TAB_A.NOTE                           ,
  TAB_A.DISUSE_FLAG                    ,
  TAB_A.LAST_UPDATE_TIMESTAMP          ,
  TAB_A.LAST_UPDATE_USER               ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01  ,
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM        D_CMDB_MENU_COLUMN_SHEET_TYPE_1_PIONEER_JNL TAB_A
  LEFT JOIN D_CMDB_MENU_LIST_SHEET_TYPE_1_PIONEER       TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
  LEFT JOIN A_MENU_LIST                                 TAB_C ON (TAB_A.MENU_ID       = TAB_C.MENU_ID)
  LEFT JOIN A_MENU_GROUP_LIST                           TAB_D ON (TAB_C.MENU_GROUP_ID = TAB_D.MENU_GROUP_ID)
WHERE
   TAB_A.DISUSE_FLAG = '0' AND
   TAB_B.DISUSE_FLAG = '0' AND
   TAB_C.DISUSE_FLAG = '0' AND
   TAB_D.DISUSE_FLAG = '0'
;

-- -------------------------------------------------------
-- --代入値自動登録設定の「メニューグループ:メニュー:項目」SHEET_TYPE=3用
-- -------------------------------------------------------
CREATE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_3 AS
SELECT
 *
FROM D_CMDB_MENU_LIST TAB_A
WHERE SHEET_TYPE = 3
;

CREATE VIEW D_CMDB_MENU_LIST_SHEET_TYPE_3_JNL AS
SELECT
 *
FROM D_CMDB_MENU_LIST_JNL TAB_A
WHERE SHEET_TYPE = 3
;

CREATE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_3 AS
SELECT
 *
FROM D_CMDB_MG_MU_COL_LIST TAB_A
WHERE SHEET_TYPE = 3
;

CREATE VIEW D_CMDB_MG_MU_COL_LIST_SHEET_TYPE_3_JNL AS
SELECT
 *
FROM D_CMDB_MG_MU_COL_LIST_JNL TAB_A
WHERE SHEET_TYPE = 3
;

CREATE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_3 AS
SELECT
  TAB_B.*,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01   ,
  TAB_A.ACCESS_AUTH_01 AS ACCESS_AUTH_02,
  TAB_A.ACCESS_AUTH_02 AS ACCESS_AUTH_03
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_3         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.DISUSE_FLAG = '0'
;

CREATE VIEW D_CMDB_MENU_COLUMN_SHEET_TYPE_3_JNL AS
SELECT
  TAB_B.*,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01   ,
  TAB_A.ACCESS_AUTH_01 AS ACCESS_AUTH_02,
  TAB_A.ACCESS_AUTH_02 AS ACCESS_AUTH_03
FROM
  D_CMDB_MENU_LIST_SHEET_TYPE_3_JNL         TAB_A
  LEFT JOIN B_CMDB_MENU_COLUMN_JNL TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
WHERE
  TAB_B.DISUSE_FLAG = '0'
;

-- *****************************************************************************
-- *** ***** 削除関連
-- *****************************************************************************
-- -------------------------------------------------------
-- --オペレーション削除管理
-- -------------------------------------------------------
CREATE TABLE A_DEL_OPERATION_LIST (
ROW_ID                          %INT%                       , -- 識別シーケンス
LG_DAYS                         %INT%                       , -- 論理削除日数
PH_DAYS                         %INT%                       , -- 物理削除日数
TABLE_NAME                      %VARCHR%(256)               , -- テーブル名
PKEY_NAME                       %VARCHR%(256)               , -- 主キーカラム名
OPE_ID_COL_NAME                 %VARCHR%(256)               , -- オペレーションIDカラム名
GET_DATA_STRAGE_SQL             %VARCHR%(1024)              , -- データストレージパス取得SQL
DATA_PATH_1                     %VARCHR%(1024)              , -- 履歴データパス1
DATA_PATH_2                     %VARCHR%(1024)              , -- 履歴データパス2
DATA_PATH_3                     %VARCHR%(1024)              , -- 履歴データパス3
DATA_PATH_4                     %VARCHR%(1024)              , -- 履歴データパス4

ACCESS_AUTH                     TEXT                        ,
NOTE                            %VARCHR%(4000)              , -- 備考
DISUSE_FLAG                     %VARCHR%(1)                 , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP           %DATETIME6%                 , -- 最終更新日時
LAST_UPDATE_USER                %INT%                       , -- 最終更新ユーザ
PRIMARY KEY(ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_DEL_OPERATION_LIST_JNL (
JOURNAL_SEQ_NO                  %INT%                       , -- 履歴用シーケンス
JOURNAL_REG_DATETIME            %DATETIME6%                 , -- 履歴用変更日時
JOURNAL_ACTION_CLASS            %VARCHR%(8)                 , -- 履歴用変更種別

ROW_ID                          %INT%                       , -- 識別シーケンス
LG_DAYS                         %INT%                       , -- 論理削除日数
PH_DAYS                         %INT%                       , -- 物理削除日数
TABLE_NAME                      %VARCHR%(256)               , -- テーブル名
PKEY_NAME                       %VARCHR%(256)               , -- 主キーカラム名
OPE_ID_COL_NAME                 %VARCHR%(256)               , -- オペレーションIDカラム名
GET_DATA_STRAGE_SQL             %VARCHR%(1024)              , -- データストレージパス取得SQL
DATA_PATH_1                     %VARCHR%(1024)              , -- 履歴データパス1
DATA_PATH_2                     %VARCHR%(1024)              , -- 履歴データパス2
DATA_PATH_3                     %VARCHR%(1024)              , -- 履歴データパス3
DATA_PATH_4                     %VARCHR%(1024)              , -- 履歴データパス4

ACCESS_AUTH                     TEXT                        ,
NOTE                            %VARCHR%(4000)              , -- 備考
DISUSE_FLAG                     %VARCHR%(1)                 , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP           %DATETIME6%                 , -- 最終更新日時
LAST_UPDATE_USER                %INT%                       , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


-- -------------------------------------------------------
-- --ファイル削除管理
-- -------------------------------------------------------
CREATE TABLE A_DEL_FILE_LIST (
ROW_ID                         %INT%                        , -- 識別シーケンス
DEL_DAYS                       %INT%                        , -- 削除日数
TARGET_DIR                     %VARCHR%(1024)               , -- 削除対象ディレクトリ
TARGET_FILE                    %VARCHR%(1024)               , -- 削除対象ファイル
DEL_SUB_DIR_FLG                %INT%                        , -- サブディレクトリ削除有無

ACCESS_AUTH                    TEXT                         ,
NOTE                           %VARCHR%(4000)               , -- 備考
DISUSE_FLAG                    %VARCHR%(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
LAST_UPDATE_USER               %INT%                        , -- 最終更新ユーザ
PRIMARY KEY(ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_DEL_FILE_LIST_JNL
(
JOURNAL_SEQ_NO                 %INT%                        , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           %DATETIME6%                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           %VARCHR%(8)                  , -- 履歴用変更種別

ROW_ID                         %INT%                        , -- 識別シーケンス
DEL_DAYS                       %INT%                        , -- 削除日数
TARGET_DIR                     %VARCHR%(1024)               , -- 削除対象ディレクトリ
TARGET_FILE                    %VARCHR%(1024)               , -- 削除対象ファイル
DEL_SUB_DIR_FLG                %INT%                        , -- サブディレクトリ削除有無

ACCESS_AUTH                    TEXT                         ,
NOTE                           %VARCHR%(4000)               , -- 備考
DISUSE_FLAG                    %VARCHR%(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%                  , -- 最終更新日時
LAST_UPDATE_USER               %INT%                        , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- ここまでITA-BASE用----

-- VIEW作成

CREATE UNIQUE INDEX IND_A_ACCOUNT_LIST_01           ON A_ACCOUNT_LIST           ( USER_ID, DISUSE_FLAG                      );
CREATE        INDEX IND_A_ACCOUNT_LOCK_01           ON A_ACCOUNT_LOCK           ( USER_ID                                   );
CREATE        INDEX IND_A_ACCOUNT_LOCK_02           ON A_ACCOUNT_LOCK           ( USER_ID, DISUSE_FLAG                      );
CREATE        INDEX IND_A_ROLE_LIST_01              ON A_ROLE_LIST              ( DISUSE_FLAG                               );
CREATE UNIQUE INDEX IND_A_ROLE_LIST_02              ON A_ROLE_LIST              ( ROLE_ID, DISUSE_FLAG                      );
CREATE UNIQUE INDEX IND_A_MENU_GROUP_LIST_01        ON A_MENU_GROUP_LIST        ( MENU_GROUP_ID, DISUSE_FLAG                );
CREATE UNIQUE INDEX IND_A_MENU_LIST_01              ON A_MENU_LIST              ( MENU_ID, DISUSE_FLAG                      );
CREATE        INDEX IND_A_MENU_LIST_02              ON A_MENU_LIST              ( MENU_GROUP_ID                             );
CREATE        INDEX IND_A_MENU_LIST_03              ON A_MENU_LIST              ( LOGIN_NECESSITY                           );
CREATE        INDEX IND_A_MENU_LIST_04              ON A_MENU_LIST              ( SERVICE_STATUS                            );
CREATE        INDEX IND_A_ROLE_ACC_LINK_LIST_01     ON A_ROLE_ACCOUNT_LINK_LIST ( ROLE_ID, DISUSE_FLAG                      );
CREATE        INDEX IND_A_ROLE_ACC_LINK_LIST_02     ON A_ROLE_ACCOUNT_LINK_LIST ( USER_ID, DISUSE_FLAG                      );
CREATE        INDEX IND_A_ROLE_ACC_LINK_LIST_03     ON A_ROLE_ACCOUNT_LINK_LIST ( ROLE_ID, USER_ID, DISUSE_FLAG             );
CREATE        INDEX IND_A_ROLE_MENU_LINK_LIST_01    ON A_ROLE_MENU_LINK_LIST    ( ROLE_ID, DISUSE_FLAG                      );
CREATE        INDEX IND_A_ROLE_MENU_LINK_LIST_02    ON A_ROLE_MENU_LINK_LIST    ( MENU_ID, DISUSE_FLAG                      );
CREATE        INDEX IND_A_ROLE_MENU_LINK_LIST_03    ON A_ROLE_MENU_LINK_LIST    ( ROLE_ID, MENU_ID, DISUSE_FLAG             );
CREATE UNIQUE INDEX IND_B_CMDB_MENU_TABLE_01        ON B_CMDB_MENU_TABLE        ( MENU_ID                                   );
CREATE UNIQUE INDEX IND_C_OPERATION_LIST_01         ON C_OPERATION_LIST         ( OPERATION_NO_IDBH                         );

CREATE TABLE B_VALID_INVALID_MASTER
(
FLAG_ID                           %INT%                            , -- 識別シーケンス

FLAG_NAME                         %VARCHR%(32)                     , -- 表示名

DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (FLAG_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_VALID_INVALID_MASTER_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

FLAG_ID                           %INT%                            , -- 識別シーケンス

FLAG_NAME                         %VARCHR%(32)                     , -- 表示名

DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_PROC_LOADED_LIST
(
ROW_ID                  %INT%               ,
PROC_NAME               %VARCHR%(64)        ,
LOADED_FLG              %VARCHR%(1)         ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%         ,
PRIMARY KEY(ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
