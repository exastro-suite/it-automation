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
PRIMARY KEY(NAME)
)%%TABLE_CREATE_OUT_TAIL%%;

-- 更新系テーブル作成
CREATE TABLE A_ACCOUNT_LIST
(
USER_ID                 %INT%                   ,
USERNAME                %VARCHR%(30)            ,
PASSWORD                %VARCHR%(32)            ,
USERNAME_JP             %VARCHR%(80)            ,
MAIL_ADDRESS            %VARCHR%(256)           ,
PW_LAST_UPDATE_TIME     %DATETIME6%             ,
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
IP_INFO                 %VARCHR%(64)            ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(PERMISSIONS_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_ROLE_LIST
(
ROLE_ID                 %INT%                   ,
ROLE_NAME               %VARCHR%(64)            ,
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(ROLE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE A_MENU_GROUP_LIST
(
MENU_GROUP_ID           %INT%                   ,
MENU_GROUP_NAME         %VARCHR%(64)            ,
MENU_GROUP_ICON         %VARCHR%(256)           ,
DISP_SEQ                %INT%                   ,
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
MENU_NAME               %VARCHR%(64)            ,
LOGIN_NECESSITY         %INT%                   ,
SERVICE_STATUS          %INT%                   ,
AUTOFILTER_FLG          %INT%                   ,
INITIAL_FILTER_FLG      %INT%                   ,
WEB_PRINT_LIMIT         %INT%                   ,
WEB_PRINT_CONFIRM       %INT%                   ,
XLS_PRINT_LIMIT         %INT%                   ,
DISP_SEQ                %INT%                   ,
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
NOTE                    %VARCHR%(4000)          ,
DISUSE_FLAG             %VARCHR%(1)             ,
LAST_UPDATE_TIMESTAMP   %DATETIME6%             ,
LAST_UPDATE_USER        %INT%                   ,
PRIMARY KEY(FLAG)
)%%TABLE_CREATE_OUT_TAIL%%;

-- 履歴系テーブル作成
CREATE TABLE A_ACCOUNT_LIST_JNL
(
JOURNAL_SEQ_NO          %INT%                   ,
JOURNAL_REG_DATETIME    %DATETIME6%             ,
JOURNAL_ACTION_CLASS    %VARCHR%(8)             ,
USER_ID                 %INT%                   ,
USERNAME                %VARCHR%(30)            ,
PASSWORD                %VARCHR%(32)            ,
USERNAME_JP             %VARCHR%(80)            ,
MAIL_ADDRESS            %VARCHR%(256)           ,
PW_LAST_UPDATE_TIME     %DATETIME6%             ,
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
IP_INFO                 %VARCHR%(64)            ,
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
ROLE_NAME               %VARCHR%(64)            ,
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
MENU_GROUP_NAME         %VARCHR%(64)            ,
MENU_GROUP_ICON         %VARCHR%(256)           ,
DISP_SEQ                %INT%                   ,
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
MENU_NAME               %VARCHR%(64)            ,
LOGIN_NECESSITY         %INT%                   ,
SERVICE_STATUS          %INT%                   ,
AUTOFILTER_FLG          %INT%                   ,
INITIAL_FILTER_FLG      %INT%                   ,
WEB_PRINT_LIMIT         %INT%                   ,
WEB_PRINT_CONFIRM       %INT%                   ,
XLS_PRINT_LIMIT         %INT%                   ,
DISP_SEQ                %INT%                   ,
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
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
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
DISP_SEQ                        %INT%                        ,
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
DISP_SEQ                        %INT%                        ,
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
NOTE                              %VARCHR%(4000)            , -- 備考
DISUSE_FLAG                       %VARCHR%(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%               , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_OS_TYPE
(
OS_TYPE_ID                        %INT%                     ,

OS_TYPE_NAME                      %VARCHR%(128)             ,
HARDAWRE_TYPE_SV                  %INT%                     ,
HARDAWRE_TYPE_ST                  %INT%                     ,
HARDAWRE_TYPE_NW                  %INT%                     ,

DISP_SEQ                          %INT%                     , -- 表示順序
NOTE                              %VARCHR%(4000)            , -- 備考
DISUSE_FLAG                       %VARCHR%(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%               , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                     , -- 最終更新ユーザ

PRIMARY KEY (OS_TYPE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_OS_TYPE_JNL
(
JOURNAL_SEQ_NO                    %INT%                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%               , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)               , -- 履歴用変更種別

OS_TYPE_ID                        %INT%                     ,

OS_TYPE_NAME                      %VARCHR%(128)             ,
HARDAWRE_TYPE_SV                  %INT%                     ,
HARDAWRE_TYPE_ST                  %INT%                     ,
HARDAWRE_TYPE_NW                  %INT%                     ,

DISP_SEQ                          %INT%                     , -- 表示順序
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
ETH_WOL_NET_DEVICE                %VARCHR%(32)              , -- ETH_WAKE_ON_LAN

PROTOCOL_ID                       %INT%                     ,
LOGIN_USER                        %VARCHR%(30)              ,
LOGIN_PW_HOLD_FLAG                %INT%                     ,
LOGIN_PW                          %VARCHR%(60)              ,
LOGIN_AUTH_TYPE                   %INT%                     ,
WINRM_PORT                        %INT%                     , -- WinRM接続プロトコル
WINRM_SSL_CA_FILE                 %VARCHR%(256)             , -- WinRM接続 SSLサーバー証明書
OS_TYPE_ID                        %INT%                     ,
SSH_EXTRA_ARGS                    %VARCHR%(512)             , -- ssh追加パラメータ
HOSTS_EXTRA_ARGS                  %VARCHR%(512)             , -- インベントリファイル(hosts)追加パラメータ
--
SYSTEM_NAME                       %VARCHR%(64)              ,
COBBLER_PROFILE_ID                %INT%                     , -- FOR COBLLER
INTERFACE_TYPE                    %VARCHR%(64)              , -- FOR COBLLER
MAC_ADDRESS                       %VARCHR%(17)              , -- FOR COBLLER
NETMASK                           %VARCHR%(15)              , -- FOR COBLLER
GATEWAY                           %VARCHR%(15)              , -- FOR COBLLER
STATIC                            %VARCHR%(32)              , -- FOR COBLLER

CONN_SSH_KEY_FILE                 %VARCHR%(256)             ,

DSC_CERTIFICATE_FILE              %VARCHR%(256)             , -- DSC利用情報 認証キーファイル
DSC_CERTIFICATE_THUMBPRINT        %VARCHR%(256)             , -- DSC利用情報 サムプリント

ANSTWR_INSTANCE_GRP_ITA_MNG_ID    %INT%                     , -- AnsibleTower利用情報 インスタンスグループID


DISP_SEQ                          %INT%                     , -- 表示順序
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
ETH_WOL_NET_DEVICE                %VARCHR%(32)              , -- ETH_WAKE_ON_LAN

PROTOCOL_ID                       %INT%                     ,
LOGIN_USER                        %VARCHR%(30)              ,
LOGIN_PW_HOLD_FLAG                %INT%                     ,
LOGIN_PW                          %VARCHR%(60)              ,
LOGIN_AUTH_TYPE                   %INT%                     ,
WINRM_PORT                        %INT%                     , -- WinRM接続プロトコル
WINRM_SSL_CA_FILE                 %VARCHR%(256)             , -- WinRM接続 SSLサーバー証明書
OS_TYPE_ID                        %INT%                     ,
SSH_EXTRA_ARGS                    %VARCHR%(512)             , -- ssh追加パラメータ
HOSTS_EXTRA_ARGS                  %VARCHR%(512)             , -- インベントリファイル(hosts)追加パラメータ

SYSTEM_NAME                       %VARCHR%(64)              ,
COBBLER_PROFILE_ID                %INT%                     , -- FOR COBLLER
INTERFACE_TYPE                    %VARCHR%(64)              , -- FOR COBLLER
MAC_ADDRESS                       %VARCHR%(17)              , -- FOR COBLLER
NETMASK                           %VARCHR%(15)              , -- FOR COBLLER
GATEWAY                           %VARCHR%(15)              , -- FOR COBLLER
STATIC                            %VARCHR%(32)              , -- FOR COBLLER

CONN_SSH_KEY_FILE                 %VARCHR%(256)             ,

DSC_CERTIFICATE_FILE              %VARCHR%(256)             , -- DSC利用情報 認証キーファイル
DSC_CERTIFICATE_THUMBPRINT        %VARCHR%(256)             , -- DSC利用情報 サムプリント

ANSTWR_INSTANCE_GRP_ITA_MNG_ID    %INT%                     , -- AnsibleTower利用情報 インスタンスグループID


DISP_SEQ                          %INT%                     , -- 表示順序
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
OPENST_TEMPLATE                   %VARCHR%(256)                    ,
OPENST_ENVIRONMENT                %VARCHR%(256)                    ,

DSC_RETRY_TIMEOUT                 %INT%                            , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add

DISP_SEQ                          %INT%                            , -- 表示順序
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
OPENST_TEMPLATE                   %VARCHR%(256)                    ,
OPENST_ENVIRONMENT                %VARCHR%(256)                    ,

DSC_RETRY_TIMEOUT                 %INT%                            , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add

DISP_SEQ                          %INT%                            , -- 表示順序
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

OPERATION_NAME                    %VARCHR%(128)              ,
OPERATION_DATE                    %DATETIME6%                ,
OPERATION_NO_IDBH                 %INT%                      ,
LAST_EXECUTE_TIMESTAMP            %DATETIME6%                ,

DISP_SEQ                          %INT%                      , -- 表示順序
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

OPERATION_NAME                    %VARCHR%(128)              ,
OPERATION_DATE                    %DATETIME6%                ,
OPERATION_NO_IDBH                 %INT%                      ,
LAST_EXECUTE_TIMESTAMP            %DATETIME6%                ,

DISP_SEQ                          %INT%                      , -- 表示順序
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

SYMPHONY_NAME                     %VARCHR%(128)              ,
DESCRIPTION                       %VARCHR%(4000)             ,

DISP_SEQ                          %INT%                      , -- 表示順序
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

SYMPHONY_NAME                     %VARCHR%(128)              ,
DESCRIPTION                       %VARCHR%(4000)             ,

DISP_SEQ                          %INT%                      , -- 表示順序
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
I_SYMPHONY_NAME                   %VARCHR%(128)              ,
I_DESCRIPTION                     %VARCHR%(4000)             ,
OPERATION_NO_UAPK                 %INT%                      ,
I_OPERATION_NAME                  %VARCHR%(128)              , 
STATUS_ID                         %INT%                      ,
EXECUTION_USER                    %VARCHR%(80)               ,
ABORT_EXECUTE_FLAG                %INT%                      ,
TIME_BOOK                         %DATETIME6%                ,
TIME_START                        %DATETIME6%                ,
TIME_END                          %DATETIME6%                ,

DISP_SEQ                          %INT%                      , -- 表示順序
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
I_SYMPHONY_NAME                   %VARCHR%(128)              ,
I_DESCRIPTION                     %VARCHR%(4000)             ,
OPERATION_NO_UAPK                 %INT%                      ,
I_OPERATION_NAME                  %VARCHR%(128)              ,
STATUS_ID                         %INT%                      ,
EXECUTION_USER                    %VARCHR%(80)               ,
ABORT_EXECUTE_FLAG                %INT%                      ,
TIME_BOOK                         %DATETIME6%                ,
TIME_START                        %DATETIME6%                ,
TIME_END                          %DATETIME6%                ,

DISP_SEQ                          %INT%                      , -- 表示順序
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

I_DSC_RETRY_TIMEOUT               %INT%                      , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add

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
OVRD_I_OPERATION_NAME             %VARCHR%(128)              ,
OVRD_I_OPERATION_NO_IDBH          %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
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

I_DSC_RETRY_TIMEOUT               %INT%                      , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add

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
OVRD_I_OPERATION_NAME             %VARCHR%(128)              ,
OVRD_I_OPERATION_NO_IDBH          %INT%                      ,

DISP_SEQ                          %INT%                      , -- 表示順序
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
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
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
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
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
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
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
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
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
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER 
FROM A_ROLE_ACCOUNT_LINK_LIST_JNL TAB_A
LEFT JOIN A_ACCOUNT_LIST TAB_B ON (TAB_A.USER_ID = TAB_B.USER_ID)
LEFT JOIN A_ROLE_LIST TAB_C ON (TAB_A.ROLE_ID = TAB_C.ROLE_ID)
WHERE TAB_A.USER_ID > 0;
-- *****************************************************************************
-- *** WEB-DBCORE Views *****                                                ***
-- *****************************************************************************



-- *****************************************************************************
-- *** ***** ITA-BASE Views                                                  ***
-- *****************************************************************************
CREATE VIEW D_OS_TYPE 
AS 
SELECT * 
FROM B_OS_TYPE;

CREATE VIEW D_OS_TYPE_JNL 
AS 
SELECT * 
FROM B_OS_TYPE_JNL;

CREATE VIEW D_OS_TYPE_SV 
AS 
SELECT * 
FROM B_OS_TYPE 
WHERE HARDAWRE_TYPE_SV=1;

CREATE VIEW D_OS_TYPE_SV_JNL 
AS 
SELECT * 
FROM B_OS_TYPE_JNL 
WHERE HARDAWRE_TYPE_SV=1;

CREATE VIEW D_OS_TYPE_ST 
AS 
SELECT * 
FROM B_OS_TYPE 
WHERE HARDAWRE_TYPE_ST=1;

CREATE VIEW D_OS_TYPE_ST_JNL 
AS 
SELECT * 
FROM B_OS_TYPE_JNL 
WHERE HARDAWRE_TYPE_ST=1;

CREATE VIEW D_OS_TYPE_NW 
AS 
SELECT * 
FROM B_OS_TYPE 
WHERE HARDAWRE_TYPE_NW=1;

CREATE VIEW D_OS_TYPE_NW_JNL 
AS 
SELECT * 
FROM B_OS_TYPE_JNL 
WHERE HARDAWRE_TYPE_NW=1;

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

       TAB_A.DSC_CERTIFICATE_FILE             DSC_CERTIFICATE_FILE          ,
       TAB_A.DSC_CERTIFICATE_THUMBPRINT       DSC_CERTIFICATE_THUMBPRINT    ,

       TAB_A.DISP_SEQ                         DISP_SEQ                      ,
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

       TAB_A.DSC_CERTIFICATE_FILE             DSC_CERTIFICATE_FILE          ,
       TAB_A.DSC_CERTIFICATE_THUMBPRINT       DSC_CERTIFICATE_THUMBPRINT    ,

       TAB_A.DISP_SEQ                         DISP_SEQ                      ,
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

DISP_SEQ                       %INT%                   , -- 表示順序
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

DISP_SEQ                       %INT%                   , -- 表示順序
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
       TAB_A.DISP_SEQ                       ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_CMDB_MENU_LIST TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

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
       TAB_A.DISP_SEQ                       ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_CMDB_MENU_LIST_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

-- -------------------------------------------------------
-- --「紐付対象メニューテーブル管理」メニュー用
-- -------------------------------------------------------
CREATE TABLE B_CMDB_MENU_TABLE (
TABLE_ID                       %INT%                   , -- 識別シーケンス
MENU_ID                        %INT%                   , -- メニューID
TABLE_NAME                     %VARCHR%(64)            , -- テーブル名
PKEY_NAME                      %VARCHR%(64)            , -- 主キーカラム名

DISP_SEQ                       %INT%                   , -- 表示順序
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
COL_NAME                       %VARCHR%(64)            , -- テーブル　カラム名
COL_TITLE                      %VARCHR%(256)           , -- メニュー　カラム名
COL_TITLE_DISP_SEQ             %INT%                   , -- メニュー　カラム名 代入値自動登録 表示順
REF_TABLE_NAME                 %VARCHR%(64)            , -- 参照テーブル名
REF_PKEY_NAME                  %VARCHR%(64)            , -- 参照テーブル主キー
REF_COL_NAME                   %VARCHR%(64)            , -- 参照テーブルカラム名

DISP_SEQ                       %INT%                   , -- 表示順序
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
COL_NAME                       %VARCHR%(64)            , -- テーブル　カラム名
COL_TITLE                      %VARCHR%(256)           , -- メニュー　カラム名
COL_TITLE_DISP_SEQ             %INT%                   , -- メニュー　カラム名 代入値自動登録 表示順
REF_TABLE_NAME                 %VARCHR%(64)            , -- 参照テーブル名
REF_PKEY_NAME                  %VARCHR%(64)            , -- 参照テーブル主キー
REF_COL_NAME                   %VARCHR%(64)            , -- 参照テーブルカラム名

DISP_SEQ                       %INT%                   , -- 表示順序
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
  TAB_A.DISUSE_FLAG
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
  TAB_A.DISUSE_FLAG
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
  TAB_A.COL_TITLE_DISP_SEQ             ,
  TAB_A.DISP_SEQ                       ,
  TAB_A.NOTE                           ,
  TAB_A.DISUSE_FLAG                    ,
  TAB_A.LAST_UPDATE_TIMESTAMP          ,
  TAB_A.LAST_UPDATE_USER 
FROM        B_CMDB_MENU_COLUMN TAB_A
  LEFT JOIN B_CMDB_MENU_LIST   TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
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
  TAB_A.COL_TITLE_DISP_SEQ             ,
  TAB_A.DISP_SEQ                       ,
  TAB_A.NOTE                           ,
  TAB_A.DISUSE_FLAG                    ,
  TAB_A.LAST_UPDATE_TIMESTAMP          ,
  TAB_A.LAST_UPDATE_USER 
FROM        B_CMDB_MENU_COLUMN_JNL TAB_A
  LEFT JOIN B_CMDB_MENU_LIST       TAB_B ON (TAB_A.MENU_ID       = TAB_B.MENU_ID)
  LEFT JOIN A_MENU_LIST                TAB_C ON (TAB_A.MENU_ID       = TAB_C.MENU_ID)
  LEFT JOIN A_MENU_GROUP_LIST          TAB_D ON (TAB_C.MENU_GROUP_ID = TAB_D.MENU_GROUP_ID)
WHERE
   TAB_A.DISUSE_FLAG = '0' AND
   TAB_B.DISUSE_FLAG = '0' AND
   TAB_C.DISUSE_FLAG = '0' AND
   TAB_D.DISUSE_FLAG = '0';

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

CREATE TABLE B_VALID_INVALID_MASTER
(
FLAG_ID                           %INT%                            , -- 識別シーケンス

FLAG_NAME                         %VARCHR%(32)                     , -- 表示名

DISP_SEQ                          %INT%                            , -- 表示順序
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
