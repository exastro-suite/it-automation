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
NAME                    VARCHAR2(64)            ,
VALUE                   NUMBER                  ,
PRIMARY KEY(NAME)
);

-- 更新系テーブル作成
CREATE TABLE A_ACCOUNT_LIST
(
USER_ID                 NUMBER                  ,
USERNAME                VARCHAR2(30)            ,
PASSWORD                VARCHAR2(32)            ,
USERNAME_JP             VARCHAR2(80)            ,
MAIL_ADDRESS            VARCHAR2(256)           ,
PW_LAST_UPDATE_TIME     TIMESTAMP               ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(USER_ID)
);

CREATE TABLE A_ACCOUNT_LOCK
(
LOCK_ID                 NUMBER                  ,
USER_ID                 NUMBER                  ,
MISS_INPUT_COUNTER      NUMBER                  ,
LOCKED_TIMESTAMP        TIMESTAMP               ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(LOCK_ID)
);

CREATE TABLE A_SYSTEM_CONFIG_LIST
(
ITEM_ID                 NUMBER                  ,
CONFIG_ID               VARCHAR2(32)            ,
CONFIG_NAME             VARCHAR2(64)            ,
VALUE                   VARCHAR2(1024)          ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(ITEM_ID)
);

CREATE TABLE A_PERMISSIONS_LIST
(
PERMISSIONS_ID          NUMBER                  ,
IP_ADDRESS              VARCHAR2(15)            ,
IP_INFO                 VARCHAR2(256)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(PERMISSIONS_ID)
);

CREATE TABLE A_ROLE_LIST
(
ROLE_ID                 NUMBER                  ,
ROLE_NAME               VARCHAR2(256)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(ROLE_ID)
);

CREATE TABLE A_MENU_GROUP_LIST
(
MENU_GROUP_ID           NUMBER                  ,
MENU_GROUP_NAME         VARCHAR2(64)            ,
MENU_GROUP_ICON         VARCHAR2(256)           ,
DISP_SEQ                NUMBER                  ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(MENU_GROUP_ID)
);

CREATE TABLE A_MENU_LIST
(
MENU_ID                 NUMBER                  ,
MENU_GROUP_ID           NUMBER                  ,
MENU_NAME               VARCHAR2(64)            ,
LOGIN_NECESSITY         NUMBER                  ,
SERVICE_STATUS          NUMBER                  ,
AUTOFILTER_FLG          NUMBER                  ,
INITIAL_FILTER_FLG      NUMBER                  ,
WEB_PRINT_LIMIT         NUMBER                  ,
WEB_PRINT_CONFIRM       NUMBER                  ,
XLS_PRINT_LIMIT         NUMBER                  ,
DISP_SEQ                NUMBER                  ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(MENU_ID)
);

CREATE TABLE A_ROLE_ACCOUNT_LINK_LIST
(
LINK_ID                 NUMBER                  ,
ROLE_ID                 NUMBER                  ,
USER_ID                 NUMBER                  ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(LINK_ID)
);

CREATE TABLE A_ROLE_MENU_LINK_LIST
(
LINK_ID                 NUMBER                  ,
ROLE_ID                 NUMBER                  ,
MENU_ID                 NUMBER                  ,
PRIVILEGE               NUMBER                  ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(LINK_ID)
);

CREATE TABLE A_LOGIN_NECESSITY_LIST
(
FLAG                    NUMBER                  ,
NAME                    VARCHAR2(64)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(FLAG)
);

CREATE TABLE A_SERVICE_STATUS_LIST
(
FLAG                    NUMBER                  ,
NAME                    VARCHAR2(64)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(FLAG)
);

CREATE TABLE A_REPRESENTATIVE_LIST
(
FLAG                    NUMBER                  ,
NAME                    VARCHAR2(64)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(FLAG)
);

CREATE TABLE A_PRIVILEGE_LIST
(
FLAG                    NUMBER                  ,
NAME                    VARCHAR2(64)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(FLAG)
);

-- 履歴系テーブル作成
CREATE TABLE A_ACCOUNT_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
USER_ID                 NUMBER                  ,
USERNAME                VARCHAR2(30)            ,
PASSWORD                VARCHAR2(32)            ,
USERNAME_JP             VARCHAR2(80)            ,
MAIL_ADDRESS            VARCHAR2(256)           ,
PW_LAST_UPDATE_TIME     TIMESTAMP               ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_ACCOUNT_LOCK_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
LOCK_ID                 NUMBER                  ,
USER_ID                 NUMBER                  ,
MISS_INPUT_COUNTER      NUMBER                  ,
LOCKED_TIMESTAMP        TIMESTAMP               ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_SYSTEM_CONFIG_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
ITEM_ID                 NUMBER                  ,
CONFIG_ID               VARCHAR2(32)            ,
CONFIG_NAME             VARCHAR2(64)            ,
VALUE                   VARCHAR2(1024)          ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_PERMISSIONS_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
PERMISSIONS_ID          NUMBER                  ,
IP_ADDRESS              VARCHAR2(15)            ,
IP_INFO                 VARCHAR2(256)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_ROLE_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
ROLE_ID                 NUMBER                  ,
ROLE_NAME               VARCHAR2(256)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_MENU_GROUP_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
MENU_GROUP_ID           NUMBER                  ,
MENU_GROUP_NAME         VARCHAR2(64)            ,
MENU_GROUP_ICON         VARCHAR2(256)           ,
DISP_SEQ                NUMBER                  ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_MENU_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
MENU_ID                 NUMBER                  ,
MENU_GROUP_ID           NUMBER                  ,
MENU_NAME               VARCHAR2(64)            ,
LOGIN_NECESSITY         NUMBER                  ,
SERVICE_STATUS          NUMBER                  ,
AUTOFILTER_FLG          NUMBER                  ,
INITIAL_FILTER_FLG      NUMBER                  ,
WEB_PRINT_LIMIT         NUMBER                  ,
WEB_PRINT_CONFIRM       NUMBER                  ,
XLS_PRINT_LIMIT         NUMBER                  ,
DISP_SEQ                NUMBER                  ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_ROLE_ACCOUNT_LINK_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
LINK_ID                 NUMBER                  ,
ROLE_ID                 NUMBER                  ,
USER_ID                 NUMBER                  ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_ROLE_MENU_LINK_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
LINK_ID                 NUMBER                  ,
ROLE_ID                 NUMBER                  ,
MENU_ID                 NUMBER                  ,
PRIVILEGE               NUMBER                  ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_LOGIN_NECESSITY_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
FLAG                    NUMBER                  ,
NAME                    VARCHAR2(64)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_SERVICE_STATUS_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
FLAG                    NUMBER                  ,
NAME                    VARCHAR2(64)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_REPRESENTATIVE_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
FLAG                    NUMBER                  ,
NAME                    VARCHAR2(64)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_PRIVILEGE_LIST_JNL
(
JOURNAL_SEQ_NO          NUMBER                  ,
JOURNAL_REG_DATETIME    TIMESTAMP               ,
JOURNAL_ACTION_CLASS    VARCHAR2(8)             ,
FLAG                    NUMBER                  ,
NAME                    VARCHAR2(64)            ,
NOTE                    VARCHAR2(4000)          ,
DISUSE_FLAG             VARCHAR2(1)             ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP               ,
LAST_UPDATE_USER        NUMBER                  ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_TODO_MASTER
(
TODO_ID                           NUMBER                            , -- 識別シーケンス
TODO_STATUS                       VARCHAR2(64)                      , -- ステータス
DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (TODO_ID)
);

CREATE TABLE A_TODO_MASTER_JNL
(
JOURNAL_SEQ_NO                    NUMBER                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                         , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                       , -- 履歴用変更種別

TODO_ID                           NUMBER                            , -- 識別シーケンス
TODO_STATUS                       VARCHAR2(64)                      , -- ステータス
DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
);

-- *****************************************************************************
-- *** WEB-DBCORE Tables *****                                               ***
-- *****************************************************************************



-- *****************************************************************************
-- *** ***** ITA-BASE Tables                                                 ***
-- *****************************************************************************
-- ----更新系テーブル作成
CREATE TABLE B_ITA_EXT_STM_MASTER
(
ITA_EXT_STM_ID                  NUMBER                       ,
ITA_EXT_STM_NAME                VARCHAR2(64)                 ,
ITA_EXT_LINK_LIB_PATH           VARCHAR2(64)                 ,
DISP_SEQ                        NUMBER                       ,
NOTE                            VARCHAR2(4000)               ,
DISUSE_FLAG                     VARCHAR2(1)                  ,
LAST_UPDATE_TIMESTAMP           TIMESTAMP                    ,
LAST_UPDATE_USER                NUMBER                       ,
PRIMARY KEY ( ITA_EXT_STM_ID )
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ITA_EXT_STM_MASTER_JNL
(
JOURNAL_SEQ_NO                  NUMBER                       ,
JOURNAL_REG_DATETIME            TIMESTAMP                    ,
JOURNAL_ACTION_CLASS            VARCHAR2(8)                  ,
ITA_EXT_STM_ID                  NUMBER                       ,
ITA_EXT_STM_NAME                VARCHAR2(64)                 ,
ITA_EXT_LINK_LIB_PATH           VARCHAR2(64)                 ,
DISP_SEQ                        NUMBER                       ,
NOTE                            VARCHAR2(4000)               ,
DISUSE_FLAG                     VARCHAR2(1)                  ,
LAST_UPDATE_TIMESTAMP           TIMESTAMP                    ,
LAST_UPDATE_USER                NUMBER                       ,
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- 更新系テーブル作成----
CREATE TABLE B_HARDAWRE_TYPE
(
HARDAWRE_TYPE_ID                  NUMBER                    ,

HARDAWRE_TYPE_NAME                VARCHAR2(64)              ,

DISP_SEQ                          NUMBER                    , -- 表示順序
NOTE                              VARCHAR2(4000)            , -- 備考
DISUSE_FLAG                       VARCHAR2(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                 , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                    , -- 最終更新ユーザ

PRIMARY KEY (HARDAWRE_TYPE_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_HARDAWRE_TYPE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                    , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                 , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)               , -- 履歴用変更種別

HARDAWRE_TYPE_ID                  NUMBER                    ,

HARDAWRE_TYPE_NAME                VARCHAR2(64)              ,

DISP_SEQ                          NUMBER                    , -- 表示順序
NOTE                              VARCHAR2(4000)            , -- 備考
DISUSE_FLAG                       VARCHAR2(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                 , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                    , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_PROTOCOL
(
PROTOCOL_ID                       NUMBER                    ,

PROTOCOL_NAME                     VARCHAR2(32)              ,

DISP_SEQ                          NUMBER                    , -- 表示順序
NOTE                              VARCHAR2(4000)            , -- 備考
DISUSE_FLAG                       VARCHAR2(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                 , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                    , -- 最終更新ユーザ

PRIMARY KEY (PROTOCOL_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_PROTOCOL_JNL
(
JOURNAL_SEQ_NO                    NUMBER                    , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                 , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)               , -- 履歴用変更種別

PROTOCOL_ID                       NUMBER                    ,

PROTOCOL_NAME                     VARCHAR2(32)              ,

DISP_SEQ                          NUMBER                    , -- 表示順序
NOTE                              VARCHAR2(4000)            , -- 備考
DISUSE_FLAG                       VARCHAR2(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                 , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                    , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_HOST_DESIGNATE_TYPE_LIST
(
HOST_DESIGNATE_TYPE_ID            NUMBER                    ,

HOST_DESIGNATE_TYPE_NAME          VARCHAR2(32)              ,

DISP_SEQ                          NUMBER                    , -- 表示順序
NOTE                              VARCHAR2(4000)            , -- 備考
DISUSE_FLAG                       VARCHAR2(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                 , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                    , -- 最終更新ユーザ

PRIMARY KEY (HOST_DESIGNATE_TYPE_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_HOST_DESIGNATE_TYPE_LIST_JNL
(
JOURNAL_SEQ_NO                    NUMBER                    , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                 , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)               , -- 履歴用変更種別

HOST_DESIGNATE_TYPE_ID            NUMBER                    ,

HOST_DESIGNATE_TYPE_NAME          VARCHAR2(32)              ,

DISP_SEQ                          NUMBER                    , -- 表示順序
NOTE                              VARCHAR2(4000)            , -- 備考
DISUSE_FLAG                       VARCHAR2(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                 , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                    , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_OS_TYPE
(
OS_TYPE_ID                        NUMBER                    ,

OS_TYPE_NAME                      VARCHAR2(256)             ,
HARDAWRE_TYPE_SV                  NUMBER                    ,
HARDAWRE_TYPE_ST                  NUMBER                    ,
HARDAWRE_TYPE_NW                  NUMBER                    ,

DISP_SEQ                          NUMBER                    , -- 表示順序
NOTE                              VARCHAR2(4000)            , -- 備考
DISUSE_FLAG                       VARCHAR2(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                 , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                    , -- 最終更新ユーザ

PRIMARY KEY (OS_TYPE_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_OS_TYPE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                    , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                 , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)               , -- 履歴用変更種別

OS_TYPE_ID                        NUMBER                    ,

OS_TYPE_NAME                      VARCHAR2(256)             ,
HARDAWRE_TYPE_SV                  NUMBER                    ,
HARDAWRE_TYPE_ST                  NUMBER                    ,
HARDAWRE_TYPE_NW                  NUMBER                    ,

DISP_SEQ                          NUMBER                    , -- 表示順序
NOTE                              VARCHAR2(4000)            , -- 備考
DISUSE_FLAG                       VARCHAR2(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                 , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                    , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_STM_LIST
(
SYSTEM_ID                         NUMBER                    , -- 識別シーケンス

HARDAWRE_TYPE_ID                  NUMBER                    ,
HOSTNAME                          VARCHAR2(128)             ,
IP_ADDRESS                        VARCHAR2(15)              ,

ETH_WOL_MAC_ADDRESS               VARCHAR2(17)              , -- ETH_WAKE_ON_LAN
ETH_WOL_NET_DEVICE                VARCHAR2(256)              , -- ETH_WAKE_ON_LAN

PROTOCOL_ID                       NUMBER                    ,
LOGIN_USER                        VARCHAR2(30)              ,
LOGIN_PW_HOLD_FLAG                NUMBER                    ,
LOGIN_PW                          VARCHAR2(60)              ,
LOGIN_PW_ANSIBLE_VAULT            VARCHAR2(512)             , -- パスワード ansible-vault暗号化文字列　隠しカラム
LOGIN_AUTH_TYPE                   NUMBER                    ,
WINRM_PORT                        NUMBER                    , -- WinRM接続プロトコル
WINRM_SSL_CA_FILE                 VARCHAR2(256)             , -- WinRM接続 SSLサーバー証明書
OS_TYPE_ID                        NUMBER                    ,
SSH_EXTRA_ARGS                    VARCHAR2(512)             , -- ssh追加パラメータ
HOSTS_EXTRA_ARGS                  VARCHAR2(512)             , -- インベントリファイル(hosts)追加パラメータ
--
SYSTEM_NAME                       VARCHAR2(64)              ,
COBBLER_PROFILE_ID                NUMBER                    , -- FOR COBLLER
INTERFACE_TYPE                    VARCHAR2(256)             , -- FOR COBLLER
MAC_ADDRESS                       VARCHAR2(17)              , -- FOR COBLLER
NETMASK                           VARCHAR2(15)              , -- FOR COBLLER
GATEWAY                           VARCHAR2(15)              , -- FOR COBLLER
STATIC                            VARCHAR2(32)              , -- FOR COBLLER

CONN_SSH_KEY_FILE                 VARCHAR2(256)             ,

DSC_CERTIFICATE_FILE              VARCHAR2(256)             , -- DSC利用情報 認証キーファイル
DSC_CERTIFICATE_THUMBPRINT        VARCHAR2(256)             , -- DSC利用情報 サムプリント

ANSTWR_INSTANCE_GROUP_NAME        VARCHAR2(512)             , -- インスタンスグループ名

DISP_SEQ                          NUMBER                    , -- 表示順序
NOTE                              VARCHAR2(4000)            , -- 備考
DISUSE_FLAG                       VARCHAR2(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                 , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                    , -- 最終更新ユーザ

PRIMARY KEY (SYSTEM_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_STM_LIST_JNL
(
JOURNAL_SEQ_NO                    NUMBER                    , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                 , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)               , -- 履歴用変更種別

SYSTEM_ID                         NUMBER                    , -- 識別シーケンス

HARDAWRE_TYPE_ID                  NUMBER                    ,
HOSTNAME                          VARCHAR2(128)             ,
IP_ADDRESS                        VARCHAR2(15)              ,

ETH_WOL_MAC_ADDRESS               VARCHAR2(17)              , -- ETH_WAKE_ON_LAN
ETH_WOL_NET_DEVICE                VARCHAR2(256)             , -- ETH_WAKE_ON_LAN

PROTOCOL_ID                       NUMBER                    ,
LOGIN_USER                        VARCHAR2(30)              ,
LOGIN_PW_HOLD_FLAG                NUMBER                    ,
LOGIN_PW                          VARCHAR2(60)              ,
LOGIN_PW_ANSIBLE_VAULT            VARCHAR2(512)             , -- パスワード ansible-vault暗号化文字列　隠しカラム
LOGIN_AUTH_TYPE                   NUMBER                    ,
WINRM_PORT                        NUMBER                    , -- WinRM接続プロトコル
WINRM_SSL_CA_FILE                 VARCHAR2(256)             , -- WinRM接続 SSLサーバー証明書
OS_TYPE_ID                        NUMBER                    ,
SSH_EXTRA_ARGS                    VARCHAR2(512)             , -- ssh追加パラメータ
HOSTS_EXTRA_ARGS                  VARCHAR2(512)             , -- インベントリファイル(hosts)追加パラメータ

SYSTEM_NAME                       VARCHAR2(64)              ,
COBBLER_PROFILE_ID                NUMBER                    , -- FOR COBLLER
INTERFACE_TYPE                    VARCHAR2(256)             , -- FOR COBLLER
MAC_ADDRESS                       VARCHAR2(17)              , -- FOR COBLLER
NETMASK                           VARCHAR2(15)              , -- FOR COBLLER
GATEWAY                           VARCHAR2(15)              , -- FOR COBLLER
STATIC                            VARCHAR2(32)              , -- FOR COBLLER

CONN_SSH_KEY_FILE                 VARCHAR2(256)             ,

DSC_CERTIFICATE_FILE              VARCHAR2(256)             , -- DSC利用情報 認証キーファイル
DSC_CERTIFICATE_THUMBPRINT        VARCHAR2(256)             , -- DSC利用情報 サムプリント

ANSTWR_INSTANCE_GROUP_NAME        VARCHAR2(512)             , -- インスタンスグループ名

DISP_SEQ                          NUMBER                    , -- 表示順序
NOTE                              VARCHAR2(4000)            , -- 備考
DISUSE_FLAG                       VARCHAR2(1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                 , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                    , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_PATTERN_PER_ORCH
(
PATTERN_ID                        NUMBER                           ,

PATTERN_NAME                      VARCHAR2(256)                    ,
ITA_EXT_STM_ID                    NUMBER                           ,
TIME_LIMIT                        NUMBER                           ,

ANS_HOST_DESIGNATE_TYPE_ID        NUMBER                           ,
ANS_PARALLEL_EXE                  NUMBER                           ,
ANS_WINRM_ID                      NUMBER                           ,
ANS_PLAYBOOK_HED_DEF              VARCHAR2(512)                    , -- legacy Playbook.ymlのヘッダ定義
ANS_EXEC_OPTIONS                  VARCHAR2(512)                    ,
ANS_VIRTUALENV_NAME               VARCHAR2(512)                    , 
OPENST_TEMPLATE                   VARCHAR2(256)                    ,
OPENST_ENVIRONMENT                VARCHAR2(256)                    ,

DSC_RETRY_TIMEOUT                 NUMBER                           , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (PATTERN_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_PATTERN_PER_ORCH_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

PATTERN_ID                        NUMBER                           ,

PATTERN_NAME                      VARCHAR2(256)                    ,
ITA_EXT_STM_ID                    NUMBER                           ,
TIME_LIMIT                        NUMBER                           ,

ANS_HOST_DESIGNATE_TYPE_ID        NUMBER                           ,
ANS_PARALLEL_EXE                  NUMBER                           ,
ANS_WINRM_ID                      NUMBER                           ,
ANS_PLAYBOOK_HED_DEF              VARCHAR2(512)                    , -- legacy Playbook.ymlのヘッダ定義
ANS_EXEC_OPTIONS                  VARCHAR2(512)                    ,
ANS_VIRTUALENV_NAME               VARCHAR2(512)                    , 
OPENST_TEMPLATE                   VARCHAR2(256)                    ,
OPENST_ENVIRONMENT                VARCHAR2(256)                    ,

DSC_RETRY_TIMEOUT                 NUMBER                           , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_OPERATION_LIST
(
OPERATION_NO_UAPK                 NUMBER                     ,

OPERATION_NAME                    VARCHAR2(256)              ,
OPERATION_DATE                    TIMESTAMP                  ,
OPERATION_NO_IDBH                 NUMBER                     ,
LAST_EXECUTE_TIMESTAMP            TIMESTAMP                  ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (OPERATION_NO_UAPK)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_OPERATION_LIST_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

OPERATION_NO_UAPK                 NUMBER                     ,

OPERATION_NAME                    VARCHAR2(256)              ,
OPERATION_DATE                    TIMESTAMP                  ,
OPERATION_NO_IDBH                 NUMBER                     ,
LAST_EXECUTE_TIMESTAMP            TIMESTAMP                  ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ReMiTicket3115----
CREATE TABLE C_SYMPHONY_IF_INFO
(
SYMPHONY_IF_INFO_ID               NUMBER                     , -- 識別シーケンス

SYMPHONY_STORAGE_PATH_ITA         VARCHAR2(256)              , -- ITA側のSymphonyインスタンス毎の共有ディレクトリ
SYMPHONY_REFRESH_INTERVAL         NUMBER                     , -- 状態監視周期(単位ミリ秒)

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (SYMPHONY_IF_INFO_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_SYMPHONY_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

SYMPHONY_IF_INFO_ID               NUMBER                     , -- 識別シーケンス

SYMPHONY_STORAGE_PATH_ITA         VARCHAR2(256)              , -- ITA側のSymphonyインスタンス毎の共有ディレクトリ
SYMPHONY_REFRESH_INTERVAL         NUMBER                     , -- 状態監視周期(単位ミリ秒)

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----
-- ----ReMiTicket3115

-- ----更新系テーブル作成
CREATE TABLE C_SYMPHONY_CLASS_MNG
(
SYMPHONY_CLASS_NO                 NUMBER                     ,

SYMPHONY_NAME                     VARCHAR2(256)              ,
DESCRIPTION                       VARCHAR2(4000)             ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (SYMPHONY_CLASS_NO)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_SYMPHONY_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

SYMPHONY_CLASS_NO                 NUMBER                     ,

SYMPHONY_NAME                     VARCHAR2(256)              ,
DESCRIPTION                       VARCHAR2(4000)             ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_SYMPHONY_INSTANCE_MNG
(
SYMPHONY_INSTANCE_NO              NUMBER                     ,

I_SYMPHONY_CLASS_NO               NUMBER                     ,
I_SYMPHONY_NAME                   VARCHAR2(256)              ,
I_DESCRIPTION                     VARCHAR2(4000)             ,
OPERATION_NO_UAPK                 NUMBER                     ,
I_OPERATION_NAME                  VARCHAR2(256)              , 
STATUS_ID                         NUMBER                     ,
EXECUTION_USER                    VARCHAR2(80)               ,
ABORT_EXECUTE_FLAG                NUMBER                     ,
TIME_BOOK                         TIMESTAMP                  ,
TIME_START                        TIMESTAMP                  ,
TIME_END                          TIMESTAMP                  ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (SYMPHONY_INSTANCE_NO)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_SYMPHONY_INSTANCE_MNG_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別
--
SYMPHONY_INSTANCE_NO              NUMBER                     ,
--
I_SYMPHONY_CLASS_NO               NUMBER                     ,
I_SYMPHONY_NAME                   VARCHAR2(256)              ,
I_DESCRIPTION                     VARCHAR2(4000)             ,
OPERATION_NO_UAPK                 NUMBER                     ,
I_OPERATION_NAME                  VARCHAR2(256)              ,
STATUS_ID                         NUMBER                     ,
EXECUTION_USER                    VARCHAR2(80)               ,
ABORT_EXECUTE_FLAG                NUMBER                     ,
TIME_BOOK                         TIMESTAMP                  ,
TIME_START                        TIMESTAMP                  ,
TIME_END                          TIMESTAMP                  ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_MOVEMENT_CLASS_MNG
(
MOVEMENT_CLASS_NO                 NUMBER                     ,

ORCHESTRATOR_ID                   NUMBER                     ,
PATTERN_ID                        NUMBER                     ,
MOVEMENT_SEQ                      NUMBER                     ,
NEXT_PENDING_FLAG                 NUMBER                     ,
DESCRIPTION                       VARCHAR2(4000)             ,
SYMPHONY_CLASS_NO                 NUMBER                     ,
OPERATION_NO_IDBH                 NUMBER                     ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (MOVEMENT_CLASS_NO)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_MOVEMENT_CLASS_MNG_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

MOVEMENT_CLASS_NO                 NUMBER                     ,

ORCHESTRATOR_ID                   NUMBER                     ,
PATTERN_ID                        NUMBER                     ,
MOVEMENT_SEQ                      NUMBER                     ,
NEXT_PENDING_FLAG                 NUMBER                     ,
DESCRIPTION                       VARCHAR2(4000)             ,
SYMPHONY_CLASS_NO                 NUMBER                     ,
OPERATION_NO_IDBH                 NUMBER                     ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_MOVEMENT_INSTANCE_MNG
(
MOVEMENT_INSTANCE_NO              NUMBER                     ,
--
I_MOVEMENT_CLASS_NO               NUMBER                     ,
I_ORCHESTRATOR_ID                 NUMBER                     ,
I_PATTERN_ID                      NUMBER                     ,
I_PATTERN_NAME                    VARCHAR2(256)              ,
I_TIME_LIMIT                      NUMBER                     ,
I_ANS_HOST_DESIGNATE_TYPE_ID      NUMBER                     ,
I_ANS_WINRM_ID                    NUMBER                     ,

I_DSC_RETRY_TIMEOUT               NUMBER                     , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add

I_MOVEMENT_SEQ                    NUMBER                     ,
I_NEXT_PENDING_FLAG               NUMBER                     ,
I_DESCRIPTION                     VARCHAR2(4000)             ,
SYMPHONY_INSTANCE_NO              NUMBER                     ,
EXECUTION_NO                      NUMBER                     ,
STATUS_ID                         NUMBER                     ,
ABORT_RECEPTED_FLAG               NUMBER                     ,
TIME_START                        TIMESTAMP                  ,
TIME_END                          TIMESTAMP                  ,
RELEASED_FLAG                     NUMBER                     ,

EXE_SKIP_FLAG                     NUMBER                     ,
OVRD_OPERATION_NO_UAPK            NUMBER                     ,
OVRD_I_OPERATION_NAME             VARCHAR2(256)              ,
OVRD_I_OPERATION_NO_IDBH          NUMBER                     ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (MOVEMENT_INSTANCE_NO)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_MOVEMENT_INSTANCE_MNG_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

MOVEMENT_INSTANCE_NO              NUMBER                     ,

I_MOVEMENT_CLASS_NO               NUMBER                     ,
I_ORCHESTRATOR_ID                 NUMBER                     ,
I_PATTERN_ID                      NUMBER                     ,
I_PATTERN_NAME                    VARCHAR2(256)              ,
I_TIME_LIMIT                      NUMBER                     ,
I_ANS_HOST_DESIGNATE_TYPE_ID      NUMBER                     ,
I_ANS_WINRM_ID                    NUMBER                     ,

I_DSC_RETRY_TIMEOUT               NUMBER                     , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add

I_MOVEMENT_SEQ                    NUMBER                     ,
I_NEXT_PENDING_FLAG               NUMBER                     ,
I_DESCRIPTION                     VARCHAR2(4000)             ,
SYMPHONY_INSTANCE_NO              NUMBER                     ,
EXECUTION_NO                      NUMBER                     ,
STATUS_ID                         NUMBER                     ,
ABORT_RECEPTED_FLAG               NUMBER                     ,
TIME_START                        TIMESTAMP                  ,
TIME_END                          TIMESTAMP                  ,
RELEASED_FLAG                     NUMBER                     ,

EXE_SKIP_FLAG                     NUMBER                     ,
OVRD_OPERATION_NO_UAPK            NUMBER                     ,
OVRD_I_OPERATION_NAME             VARCHAR2(256)              ,
OVRD_I_OPERATION_NO_IDBH          NUMBER                     ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_SYM_EXE_STATUS
(
SYM_EXE_STATUS_ID                 NUMBER                     ,

SYM_EXE_STATUS_NAME               VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (SYM_EXE_STATUS_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_SYM_EXE_STATUS_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

SYM_EXE_STATUS_ID                 NUMBER                     ,

SYM_EXE_STATUS_NAME               VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----


-- ----更新系テーブル作成
CREATE TABLE B_SYM_ABORT_FLAG
(
SYM_ABORT_FLAG_ID                 NUMBER                     ,

SYM_ABORT_FLAG_NAME               VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (SYM_ABORT_FLAG_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_SYM_ABORT_FLAG_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

SYM_ABORT_FLAG_ID                 NUMBER                     ,

SYM_ABORT_FLAG_NAME               VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_MOV_EXE_STATUS
(
MOV_EXE_STATUS_ID                 NUMBER                     ,

MOV_EXE_STATUS_NAME               VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (MOV_EXE_STATUS_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_MOV_EXE_STATUS_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

MOV_EXE_STATUS_ID                 NUMBER                     ,

MOV_EXE_STATUS_NAME               VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_MOV_ABT_RECEPT_FLAG
(
MOV_ABT_RECEPT_FLAG_ID            NUMBER                     ,

MOV_ABT_RECEPT_FLAG_NAME          VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (MOV_ABT_RECEPT_FLAG_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_MOV_ABT_RECEPT_FLAG_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

MOV_ABT_RECEPT_FLAG_ID            NUMBER                     ,

MOV_ABT_RECEPT_FLAG_NAME          VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_MOV_RELEASED_FLAG
(
MOV_RELEASED_FLAG_ID              NUMBER                     ,

MOV_RELEASED_FLAG_NAME            VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (MOV_RELEASED_FLAG_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_MOV_RELEASED_FLAG_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

MOV_RELEASED_FLAG_ID              NUMBER                     ,

MOV_RELEASED_FLAG_NAME            VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_MOV_NEXT_PENDING_FLAG
(
MOV_NEXT_PENDING_FLAG_ID          NUMBER                     ,

MOV_NEXT_PENDING_FLAG_NAME        VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (MOV_NEXT_PENDING_FLAG_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_MOV_NEXT_PENDING_FLAG_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

MOV_NEXT_PENDING_FLAG_ID          NUMBER                     ,

MOV_NEXT_PENDING_FLAG_NAME        VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_LOGIN_AUTH_TYPE
(
LOGIN_AUTH_TYPE_ID                NUMBER                     , -- 識別シーケンス

LOGIN_AUTH_TYPE_NAME              VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ

PRIMARY KEY (LOGIN_AUTH_TYPE_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_LOGIN_AUTH_TYPE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                  , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                , -- 履歴用変更種別

LOGIN_AUTH_TYPE_ID                NUMBER                     , -- 識別シーケンス

LOGIN_AUTH_TYPE_NAME              VARCHAR2(32)               ,

DISP_SEQ                          NUMBER                     , -- 表示順序
NOTE                              VARCHAR2(4000)             , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                  , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- -------------------------------------------------------
-- --● (プルダウン用)　TABLE
-- -------------------------------------------------------
CREATE TABLE D_FLAG_LIST_01
(
FLAG_ID                           NUMBER                           , -- 識別シーケンス

FLAG_NAME                         VARCHAR2(32)                      , -- 表示名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (FLAG_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE D_FLAG_LIST_01_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

FLAG_ID                           NUMBER                           , -- 識別シーケンス

FLAG_NAME                         VARCHAR2(32)                     , -- 表示名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

-- - データポータビリティ
CREATE TABLE B_DP_HIDE_MENU_LIST
(
HIDE_ID                           NUMBER                            , -- 識別シーケンス

MENU_ID                           NUMBER                            , -- メニューID

PRIMARY KEY (HIDE_ID)
);

CREATE TABLE B_DP_STATUS
(
TASK_ID                           NUMBER                            , -- タスクID

TASK_STATUS                       NUMBER                            , -- ステータス
DP_TYPE                           NUMBER                            , -- 処理種別
IMPORT_TYPE                       NUMBER                            , -- インポート種別
FILE_NAME                         VARCHAR2(64)                      , -- ファイル名
DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (TASK_ID)
);

CREATE TABLE B_DP_STATUS_JNL
(
JOURNAL_SEQ_NO                    NUMBER                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                         , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                       , -- 履歴用変更種別

TASK_ID                           NUMBER                            , -- 識別シーケンス
TASK_STATUS                       NUMBER                            , -- ステータス
DP_TYPE                           NUMBER                            , -- 処理種別
IMPORT_TYPE                       NUMBER                            , -- インポート種別
FILE_NAME                         VARCHAR2(64)                      , -- ファイル名
DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
);

CREATE TABLE B_DP_STATUS_MASTER
(
TASK_ID                           NUMBER                            , -- 識別シーケンス
TASK_STATUS                       VARCHAR2(64)                      , -- ステータス
DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (TASK_ID)
);

CREATE TABLE B_DP_STATUS_MASTER_JNL
(
JOURNAL_SEQ_NO                    NUMBER                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                         , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                       , -- 履歴用変更種別

TASK_ID                           NUMBER                            , -- 識別シーケンス
TASK_STATUS                       VARCHAR2(64)                      , -- ステータス
DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (TASK_ID)
);

CREATE TABLE B_DP_TYPE
(
ROW_ID                            NUMBER                            , -- 識別シーケンス
DP_TYPE                           VARCHAR2(64)                      , -- 処理種別
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (ROW_ID)
);

CREATE TABLE B_DP_TYPE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                         , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                       , -- 履歴用変更種別
--
ROW_ID                            NUMBER                            , -- 識別シーケンス
DP_TYPE                           VARCHAR2(64)                      , -- 処理種別
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
);

CREATE TABLE B_DP_IMPORT_TYPE
(
ROW_ID                            NUMBER                            , -- 識別シーケンス
IMPORT_TYPE                       VARCHAR2(64)                      , -- インポート種別
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (ROW_ID)
);

CREATE TABLE B_DP_IMPORT_TYPE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                         , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                       , -- 履歴用変更種別
--
ROW_ID                            NUMBER                            , -- 識別シーケンス
IMPORT_TYPE                       VARCHAR2(64)                      , -- インポート種別
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
);
-- - データポータビリティ

-- - ActiveDirectory連携
CREATE TABLE A_AD_GROUP_JUDGEMENT
(
GROUP_JUDGE_ID                    NUMBER                            , -- 識別シーケンス

AD_GROUP_SID                      VARCHAR2(256)                     , -- ADグループ識別子
ITA_ROLE_ID                       NUMBER                            , -- ITAロールID

DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ

PRIMARY KEY (GROUP_JUDGE_ID)
);

CREATE TABLE A_AD_GROUP_JUDGEMENT_JNL
(
JOURNAL_SEQ_NO                    NUMBER                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                         , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                       , -- 履歴用変更種別

GROUP_JUDGE_ID                    NUMBER                            , -- 識別シーケンス

AD_GROUP_SID                      VARCHAR2(256)                     , -- ADグループ識別子
ITA_ROLE_ID                       NUMBER                            , -- ITAロールID

DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_AD_USER_JUDGEMENT
(
USER_JUDGE_ID                     NUMBER                            , -- 識別シーケンス

AD_USER_SID                       VARCHAR2(256)                     , -- ADユーザ識別子
ITA_USER_ID                       NUMBER                            , -- ITAユーザID

DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ

PRIMARY KEY (USER_JUDGE_ID)
);

CREATE TABLE A_AD_USER_JUDGEMENT_JNL
(
JOURNAL_SEQ_NO                    NUMBER                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                         , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                       , -- 履歴用変更種別

USER_JUDGE_ID                     NUMBER                            , -- 識別シーケンス

AD_USER_SID                       VARCHAR2(256)                     , -- ADユーザ識別子
ITA_USER_ID                       NUMBER                            , -- ITAユーザID

DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- ActiveDirectory連携 -

-- グラフ画面対応 -
CREATE TABLE A_RELATE_STATUS
(
RELATE_STATUS_ID                  NUMBER                            , -- 識別シーケンス

MENU_ID                           VARCHAR2(256)                     , -- 表示画面名称
STATUS_TAB_NAME                   VARCHAR2(256)                     , -- 各メニューのステータステーブル
COMPLETE_ID                       NUMBER                            , -- 完了ステータスID
FAILED_ID                         NUMBER                            , -- 完了（異常）ステータスID
UNEXPECTED_ID                     NUMBER                            , -- 想定外エラーステータスID
EMERGENCY_ID                      NUMBER                            , -- 緊急停止ステータスID
CANCEL_ID                         NUMBER                            , -- 予約取消ステータスID

DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (RELATE_STATUS_ID)
);

CREATE TABLE A_RELATE_STATUS_JNL
(
JOURNAL_SEQ_NO                    NUMBER                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                         , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                       , -- 履歴用変更種別

RELATE_STATUS_ID                  NUMBER                            , -- 識別シーケンス

MENU_ID                           VARCHAR2(256)                     , -- 表示画面名称
STATUS_TAB_NAME                   VARCHAR2(256)                     , -- 各メニューのステータステーブル
COMPLETE_ID                       NUMBER                            , -- 完了ステータスID
FAILED_ID                         NUMBER                            , -- 完了（異常）ステータスID
UNEXPECTED_ID                     NUMBER                            , -- 想定外エラーステータスID
EMERGENCY_ID                      NUMBER                            , -- 緊急停止ステータスID
CANCEL_ID                         NUMBER                            , -- 予約取消ステータスID

DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
);
-- グラフ画面対応 -

-- メインメニューパネル化対応 -
CREATE TABLE A_SORT_MENULIST
(
SORT_MENULIST_ID                  NUMBER                            , -- ID

USER_NAME                         VARCHAR2 (768)                    , -- ユーザー名
MENU_ID_LIST                      VARCHAR2 (768)                    , -- メニューIDのリスト
SORT_ID_LIST                      VARCHAR2 (768)                    , -- 並び順のリスト
DISPLAY_MODE                      VARCHAR2 (20)                     , -- 表示モード

DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (USER_NAME)
);

CREATE TABLE A_SORT_MENULIST_JNL
(
JOURNAL_SEQ_NO                    NUMBER                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                         , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                       , -- 履歴用変更種別

SORT_MENULIST_ID                  NUMBER                            , -- ID

USER_NAME                         VARCHAR2 (768)                    , -- ユーザー名
MENU_ID_LIST                      VARCHAR2 (768)                    , -- メニューIDのリスト
SORT_ID_LIST                      VARCHAR2 (768)                    , -- 並び順のリスト
DISPLAY_MODE                      VARCHAR2 (20)                     , -- 表示モード

DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
);
-- メインメニューパネル化対応 -


-- -------------------------------------------------------
-- --Symphony/オペレーション エクスポート/インポート機能用
-- -------------------------------------------------------
-- エクスポート/インポート管理 -
CREATE TABLE B_DP_SYM_OPE_STATUS
(
TASK_ID                           NUMBER                            , -- タスクID
--
TASK_STATUS                       NUMBER                            , -- ステータス
DP_TYPE                           NUMBER                            , -- 処理種別
FILE_NAME                         VARCHAR2(64)                      , -- ファイル名
DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (TASK_ID)
);

CREATE TABLE B_DP_SYM_OPE_STATUS_JNL
(
JOURNAL_SEQ_NO                    NUMBER                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                         , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                       , -- 履歴用変更種別
--
TASK_ID                           NUMBER                            , -- 識別シーケンス
TASK_STATUS                       NUMBER                            , -- ステータス
DP_TYPE                           NUMBER                            , -- 処理種別
FILE_NAME                         VARCHAR2(64)                      , -- ファイル名
DISP_SEQ                          NUMBER                            , -- 表示順序
NOTE                              VARCHAR2(4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                         , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
);
-- エクスポート/インポート管理 -

-- Symphonyエクスポート紐付 -
CREATE TABLE B_SYMPHONY_EXPORT_LINK
(
ROW_ID                          NUMBER                              , -- ID

HIERARCHY                       NUMBER                              , -- 階層
SRC_ROW_ID                      NUMBER                              , -- 検索元項番
SRC_ITEM                        VARCHAR2(128)                       , -- 検索元項目名
DEST_MENU_ID                    NUMBER                              , -- 検索先メニュー
DEST_ITEM                       VARCHAR2(128)                       , -- 検索先項目名
OTHER_CONDITION                 VARCHAR2(1024)                      , -- その他検索条件
SPECIAL_SELECT_FUNC             VARCHAR2(1024)                      , -- 検索専用特別関数

PRIMARY KEY (ROW_ID)
);
-- Symphonyエクスポート紐付 -

-- オペレーションエクスポート紐付 -
CREATE TABLE B_OPERATION_EXPORT_LINK
(
ROW_ID                          NUMBER                              , -- ID

HIERARCHY                       NUMBER                              , -- 階層
SRC_ROW_ID                      NUMBER                              , -- 検索元項番
SRC_ITEM                        VARCHAR2(128)                       , -- 検索元項目名
DEST_MENU_ID                    NUMBER                              , -- 検索先メニュー
DEST_ITEM                       VARCHAR2(128)                       , -- 検索先項目名
OTHER_CONDITION                 VARCHAR2(1024)                      , -- その他検索条件
SPECIAL_SELECT_FUNC             VARCHAR2(1024)                      , -- 検索専用特別関数

PRIMARY KEY (ROW_ID)
);
-- オペレーションエクスポート紐付 -

-- Symオペインポート時停止サービス -
CREATE TABLE B_SVC_TO_STOP_IMP_SYM_OPE
(
ROW_ID                          NUMBER                              , -- ID

SERVICE_NAME                    VARCHAR2(128)                       , -- サービス名

PRIMARY KEY (ROW_ID)
);
-- Symオペインポート時停止サービス -



-- *****************************************************************************
-- *** ITA-BASE Tables *****                                                 ***
-- *****************************************************************************



-- *****************************************************************************
-- *** ***** COBBLER Tables                                                  ***
-- *****************************************************************************
-- ----更新系テーブル作成
CREATE TABLE C_COBBLER_PROFILE
(
COBBLER_PROFILE_ID                NUMBER                           , -- 識別シーケンス0051

COBBLER_PROFILE_NAME              VARCHAR2(256)                    ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (COBBLER_PROFILE_ID)
);
-- 更新系テーブル作成----



-- ----履歴系テーブル作成
CREATE TABLE C_COBBLER_PROFILE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

COBBLER_PROFILE_ID                NUMBER                           , -- 識別シーケンス0051

COBBLER_PROFILE_NAME              VARCHAR2(256)                    ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
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
       TAB_A.USER_ID || ':' || TAB_A.USERNAME USER_PULLDOWN,
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
       TAB_A.USER_ID || ':' || TAB_A.USERNAME USER_PULLDOWN,
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
       TAB_A.MENU_GROUP_ID || ':' || TAB_A.MENU_GROUP_NAME MENU_GROUP_PULLDOWN,
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
       TAB_A.MENU_GROUP_ID || ':' || TAB_A.MENU_GROUP_NAME MENU_GROUP_PULLDOWN,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER      
FROM   A_MENU_GROUP_LIST_JNL TAB_A;

CREATE VIEW D_ROLE_LIST AS 
SELECT TAB_A.ROLE_ID              ,
       TAB_A.ROLE_NAME            ,
       TAB_A.ROLE_ID                ROLE_ID_CLONE,
       TAB_A.ROLE_ID || ':' || TAB_A.ROLE_NAME ROLE_PULLDOWN,
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
       TAB_A.ROLE_ID || ':' || TAB_A.ROLE_NAME ROLE_PULLDOWN,
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
       TAB_A.MENU_GROUP_ID || ':' || TAB_B.MENU_GROUP_NAME || ':' || TAB_A.MENU_ID || ':' || TAB_A.MENU_NAME MENU_PULLDOWN,
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
       TAB_A.MENU_GROUP_ID || ':' || TAB_B.MENU_GROUP_NAME || ':' || TAB_A.MENU_ID || ':' || TAB_A.MENU_NAME MENU_PULLDOWN,
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
       TAB_A.SYSTEM_ID || ':' || TAB_A.HOSTNAME HOST_PULLDOWN,
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
       TAB_A.SYSTEM_ID || ':' || TAB_A.HOSTNAME HOST_PULLDOWN,
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
       TAB_A.OPERATION_NO_IDBH || ':' || TAB_A.OPERATION_NAME OPERATION,
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
       TAB_A.OPERATION_NO_IDBH || ':' || TAB_A.OPERATION_NAME OPERATION,
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
       TO_CHAR( OPERATION_DATE, '%Y/%m/%d %H:%i' ) || '_' || OPERATION_NO_IDBH || ':' || OPERATION_NAME OPERATION_ID_N_NAME,
       CASE
           WHEN LAST_EXECUTE_TIMESTAMP IS NULL THEN OPERATION_DATE
           ELSE LAST_EXECUTE_TIMESTAMP
       END BASE_TIMESTAMP,
       OPERATION_DATE                                                       ,
       TO_CHAR( OPERATION_DATE, '%Y/%m/%d %H:%i' ) OPERATION_DATE_DISP  ,
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
MENU_LIST_ID                   NUMBER                  , -- 識別シーケンス
MENU_ID                        NUMBER                  , -- メニューID

DISP_SEQ                       NUMBER                  , -- 表示順序
NOTE                           VARCHAR2(4000)          , -- 備考
DISUSE_FLAG                    VARCHAR2(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          TIMESTAMP               , -- 最終更新日時
LAST_UPDATE_USER               NUMBER                  , -- 最終更新ユーザ
PRIMARY KEY(MENU_LIST_ID)
);

CREATE TABLE B_CMDB_MENU_LIST_JNL (
JOURNAL_SEQ_NO                 NUMBER                  , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           TIMESTAMP               , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR2(8)             , -- 履歴用変更種別

MENU_LIST_ID                   NUMBER                  , -- 識別シーケンス
MENU_ID                        NUMBER                  , -- メニューID

DISP_SEQ                       NUMBER                  , -- 表示順序
NOTE                           VARCHAR2(4000)          , -- 備考
DISUSE_FLAG                    VARCHAR2(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          TIMESTAMP               , -- 最終更新日時
LAST_UPDATE_USER               NUMBER                  , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE VIEW D_CMDB_MENU_LIST AS 
SELECT 
       TAB_A.MENU_LIST_ID                   , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_B.MENU_GROUP_ID                  ,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_B.MENU_NAME                      ,
       TAB_B.MENU_GROUP_ID || ':' || TAB_C.MENU_GROUP_NAME || ':' || TAB_A.MENU_ID || ':' || TAB_B.MENU_NAME MENU_PULLDOWN,
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
       TAB_B.MENU_GROUP_ID || ':' || TAB_C.MENU_GROUP_NAME || ':' || TAB_A.MENU_ID || ':' || TAB_B.MENU_NAME MENU_PULLDOWN,
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
TABLE_ID                       NUMBER                  , -- 識別シーケンス
MENU_ID                        NUMBER                  , -- メニューID
TABLE_NAME                     VARCHAR2(64)            , -- テーブル名
PKEY_NAME                      VARCHAR2(64)            , -- 主キーカラム名

DISP_SEQ                       NUMBER                  , -- 表示順序
NOTE                           VARCHAR2(4000)          , -- 備考
DISUSE_FLAG                    VARCHAR2(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          TIMESTAMP               , -- 最終更新日時
LAST_UPDATE_USER               NUMBER                  , -- 最終更新ユーザ
PRIMARY KEY(TABLE_ID)
);

CREATE TABLE B_CMDB_MENU_TABLE_JNL
(
JOURNAL_SEQ_NO                 NUMBER                  , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           TIMESTAMP               , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR2(8)             , -- 履歴用変更種別

TABLE_ID                       NUMBER                  , -- 識別シーケンス
MENU_ID                        NUMBER                  , -- メニューID
TABLE_NAME                     VARCHAR2(64)            , -- テーブル名
PKEY_NAME                      VARCHAR2(64)            , -- 主キーカラム名

DISP_SEQ                       NUMBER                  , -- 表示順序
NOTE                           VARCHAR2(4000)          , -- 備考
DISUSE_FLAG                    VARCHAR2(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          TIMESTAMP               , -- 最終更新日時
LAST_UPDATE_USER               NUMBER                  , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

-- -------------------------------------------------------
-- --「紐付対象メニューカラム管理」メニュー用
-- -------------------------------------------------------
CREATE TABLE B_CMDB_MENU_COLUMN  (
COLUMN_LIST_ID                 NUMBER                  , -- 識別シーケンス
MENU_ID                        NUMBER                  , -- メニューID
COL_NAME                       VARCHAR2(64)            , -- テーブル　カラム名
COL_TITLE                      VARCHAR2(512)           , -- メニュー　カラム名
COL_TITLE_DISP_SEQ             NUMBER                  , -- メニュー　カラム名 代入値自動登録 表示順
REF_TABLE_NAME                 VARCHAR2(64)            , -- 参照テーブル名
REF_PKEY_NAME                  VARCHAR2(64)            , -- 参照テーブル主キー
REF_COL_NAME                   VARCHAR2(64)            , -- 参照テーブルカラム名

DISP_SEQ                       NUMBER                  , -- 表示順序
NOTE                           VARCHAR2(4000)          , -- 備考
DISUSE_FLAG                    VARCHAR2(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          TIMESTAMP               , -- 最終更新日時
LAST_UPDATE_USER               NUMBER                  , -- 最終更新ユーザ
PRIMARY KEY(COLUMN_LIST_ID)
);

CREATE TABLE B_CMDB_MENU_COLUMN_JNL
(
JOURNAL_SEQ_NO                 NUMBER                  , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           TIMESTAMP               , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR2(8)             , -- 履歴用変更種別

COLUMN_LIST_ID                 NUMBER                  , -- 識別シーケンス
MENU_ID                        NUMBER                  , -- メニューID
COL_NAME                       VARCHAR2(64)            , -- テーブル　カラム名
COL_TITLE                      VARCHAR2(512)           , -- メニュー　カラム名
COL_TITLE_DISP_SEQ             NUMBER                  , -- メニュー　カラム名 代入値自動登録 表示順
REF_TABLE_NAME                 VARCHAR2(64)            , -- 参照テーブル名
REF_PKEY_NAME                  VARCHAR2(64)            , -- 参照テーブル主キー
REF_COL_NAME                   VARCHAR2(64)            , -- 参照テーブルカラム名

DISP_SEQ                       NUMBER                  , -- 表示順序
NOTE                           VARCHAR2(4000)          , -- 備考
DISUSE_FLAG                    VARCHAR2(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          TIMESTAMP               , -- 最終更新日時
LAST_UPDATE_USER               NUMBER                  , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

-- -------------------------------------------------------
-- --代入値自動登録設定の「登録方式」用
-- -------------------------------------------------------
CREATE TABLE B_CMDB_MENU_COL_TYPE
(
COLUMN_TYPE_ID                    NUMBER                           , -- 識別シーケンス

COLUMN_TYPE_NAME                  VARCHAR2(32)                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (COLUMN_TYPE_ID)
);

CREATE TABLE B_CMDB_MENU_COL_TYPE_JNL
(            
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

COLUMN_TYPE_ID                    NUMBER                           , -- 識別シーケンス

COLUMN_TYPE_NAME                  VARCHAR2(32)                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

-- -------------------------------------------------------
-- --非対象紐付メニューグループ一覧用
-- -------------------------------------------------------
CREATE TABLE B_CMDB_HIDE_MENU_GRP
(
HIDE_ID                           NUMBER                           , -- 識別シーケンス
MENU_GROUP_ID                     NUMBER                           , -- 非対象メニューグループID

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (MENU_GROUP_ID)
);

CREATE TABLE B_CMDB_HIDE_MENU_GRP_JNL
(            
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

HIDE_ID                           NUMBER                           , -- 識別シーケンス
MENU_GROUP_ID                     NUMBER                           , -- 非対象メニューグループID

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

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
  TAB_B.MENU_GROUP_ID || ':' || TAB_B.MENU_GROUP_NAME || ':' || TAB_A.MENU_ID || ':' || TAB_A.MENU_NAME MENU_PULLDOWN,
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
  TAB_B.MENU_GROUP_ID || ':' || TAB_B.MENU_GROUP_NAME || ':' || TAB_A.MENU_ID || ':' || TAB_A.MENU_NAME MENU_PULLDOWN,
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
ROW_ID                          NUMBER                      , -- 識別シーケンス
LG_DAYS                         NUMBER                      , -- 論理削除日数
PH_DAYS                         NUMBER                      , -- 物理削除日数
TABLE_NAME                      VARCHAR2(256)               , -- テーブル名
PKEY_NAME                       VARCHAR2(256)               , -- 主キーカラム名
OPE_ID_COL_NAME                 VARCHAR2(256)               , -- オペレーションIDカラム名
GET_DATA_STRAGE_SQL             VARCHAR2(1024)              , -- データストレージパス取得SQL
DATA_PATH_1                     VARCHAR2(1024)              , -- 履歴データパス1
DATA_PATH_2                     VARCHAR2(1024)              , -- 履歴データパス2
DATA_PATH_3                     VARCHAR2(1024)              , -- 履歴データパス3
DATA_PATH_4                     VARCHAR2(1024)              , -- 履歴データパス4

NOTE                            VARCHAR2(4000)              , -- 備考
DISUSE_FLAG                     VARCHAR2(1)                 , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP           TIMESTAMP                   , -- 最終更新日時
LAST_UPDATE_USER                NUMBER                      , -- 最終更新ユーザ
PRIMARY KEY(ROW_ID)
);

CREATE TABLE A_DEL_OPERATION_LIST_JNL (
JOURNAL_SEQ_NO                  NUMBER                      , -- 履歴用シーケンス
JOURNAL_REG_DATETIME            TIMESTAMP                   , -- 履歴用変更日時
JOURNAL_ACTION_CLASS            VARCHAR2(8)                 , -- 履歴用変更種別

ROW_ID                          NUMBER                      , -- 識別シーケンス
LG_DAYS                         NUMBER                      , -- 論理削除日数
PH_DAYS                         NUMBER                      , -- 物理削除日数
TABLE_NAME                      VARCHAR2(256)               , -- テーブル名
PKEY_NAME                       VARCHAR2(256)               , -- 主キーカラム名
OPE_ID_COL_NAME                 VARCHAR2(256)               , -- オペレーションIDカラム名
GET_DATA_STRAGE_SQL             VARCHAR2(1024)              , -- データストレージパス取得SQL
DATA_PATH_1                     VARCHAR2(1024)              , -- 履歴データパス1
DATA_PATH_2                     VARCHAR2(1024)              , -- 履歴データパス2
DATA_PATH_3                     VARCHAR2(1024)              , -- 履歴データパス3
DATA_PATH_4                     VARCHAR2(1024)              , -- 履歴データパス4

NOTE                            VARCHAR2(4000)              , -- 備考
DISUSE_FLAG                     VARCHAR2(1)                 , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP           TIMESTAMP                   , -- 最終更新日時
LAST_UPDATE_USER                NUMBER                      , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);


-- -------------------------------------------------------
-- --ファイル削除管理
-- -------------------------------------------------------
CREATE TABLE A_DEL_FILE_LIST (
ROW_ID                         NUMBER                       , -- 識別シーケンス
DEL_DAYS                       NUMBER                       , -- 削除日数
TARGET_DIR                     VARCHAR2(1024)               , -- 削除対象ディレクトリ
TARGET_FILE                    VARCHAR2(1024)               , -- 削除対象ファイル
DEL_SUB_DIR_FLG                NUMBER                       , -- サブディレクトリ削除有無

NOTE                           VARCHAR2(4000)               , -- 備考
DISUSE_FLAG                    VARCHAR2(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          TIMESTAMP                    , -- 最終更新日時
LAST_UPDATE_USER               NUMBER                       , -- 最終更新ユーザ
PRIMARY KEY(ROW_ID)
);

CREATE TABLE A_DEL_FILE_LIST_JNL
(
JOURNAL_SEQ_NO                 NUMBER                       , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           TIMESTAMP                    , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR2(8)                  , -- 履歴用変更種別

ROW_ID                         NUMBER                       , -- 識別シーケンス
DEL_DAYS                       NUMBER                       , -- 削除日数
TARGET_DIR                     VARCHAR2(1024)               , -- 削除対象ディレクトリ
TARGET_FILE                    VARCHAR2(1024)               , -- 削除対象ファイル
DEL_SUB_DIR_FLG                NUMBER                       , -- サブディレクトリ削除有無

NOTE                           VARCHAR2(4000)               , -- 備考
DISUSE_FLAG                    VARCHAR2(1)                  , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          TIMESTAMP                    , -- 最終更新日時
LAST_UPDATE_USER               NUMBER                       , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

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
FLAG_ID                           NUMBER                           , -- 識別シーケンス

FLAG_NAME                         VARCHAR2(32)                     , -- 表示名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (FLAG_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_VALID_INVALID_MASTER_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

FLAG_ID                           NUMBER                           , -- 識別シーケンス

FLAG_NAME                         VARCHAR2(32)                     , -- 表示名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

CREATE TABLE A_PROC_LOADED_LIST
(
ROW_ID                  NUMBER              ,
PROC_NAME               VARCHAR2(64)        ,
LOADED_FLG              VARCHAR2(1)         ,
LAST_UPDATE_TIMESTAMP   TIMESTAMP           ,
PRIMARY KEY(ROW_ID)
);
INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_SYSTEM_CONFIG_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_SYSTEM_CONFIG_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_PERMISSIONS_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_PERMISSIONS_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_MENU_GROUP_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_MENU_GROUP_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_MENU_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_MENU_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_ROLE_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_ROLE_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_ACCOUNT_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_ACCOUNT_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_ACCOUNT_LOCK',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_ACCOUNT_LOCK',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_ROLE_MENU_LINK_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_ROLE_MENU_LINK_LIST',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_ROLE_ACCOUNT_LINK_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_ROLE_ACCOUNT_LINK_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_LOGIN_NECESSITY_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_LOGIN_NECESSITY_LIST',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_SERVICE_STATUS_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_SERVICE_STATUS_LIST',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_REPRESENTATIVE_LIST',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_REPRESENTATIVE_LIST',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_PRIVILEGE_LIST',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_PRIVILEGE_LIST',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_TODO_MASTER',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_TODO_MASTER',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_ITA_EXT_STM_ID',4);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_ITA_EXT_STM_ID',4);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_HARDAWRE_TYPE_RIC',4);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_HARDAWRE_TYPE_JSQ',4);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_PROTOCOL_RIC',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_PROTOCOL_JSQ',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_HOST_DESIGNATE_TYPE_LIST_RIC',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_HOST_DESIGNATE_TYPE_LIST_JSQ',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_LOGIN_AUTH_TYPE_RIC',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_LOGIN_AUTH_TYPE_JSQ',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OS_TYPE_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_OS_TYPE_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_STM_LIST_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_STM_LIST_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_PATTERN_PER_ORCH_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_PATTERN_PER_ORCH_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_OPERATION_LIST_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_OPERATION_LIST_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_OPERATION_LIST_ANR1',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_SYMPHONY_CLASS_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_SYMPHONY_CLASS_MNG_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_SYMPHONY_INSTANCE_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_SYMPHONY_INSTANCE_MNG_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_MOVEMENT_CLASS_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_MOVEMENT_CLASS_MNG_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_MOVEMENT_INSTANCE_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_MOVEMENT_INSTANCE_MNG_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_COBBLER_PROFILE_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_COBBLER_PROFILE_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('D_FLAG_LIST_01_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('D_FLAG_LIST_01_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DP_STATUS_MASTER_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DP_STATUS_MASTER_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DP_STATUS_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DP_STATUS_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DP_SYM_OPE_STATUS_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DP_SYM_OPE_STATUS_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_AD_GROUP_JUDGEMENT',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('JSEQ_A_AD_USER_JUDGEMENT',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_AD_GROUP_JUDGEMENT',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('SEQ_A_AD_USER_JUDGEMENT',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_CMDB_MENU_LIST_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_CMDB_MENU_LIST_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_CMDB_MENU_TABLE_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_CMDB_MENU_TABLE_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_CMDB_MENU_COLUMN_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_CMDB_MENU_COLUMN_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_CMDB_MENU_COL_TYPE_RIC',4);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_CMDB_MENU_COL_TYPE_JSQ',4);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_CMDB_HIDE_MENU_GRP_RIC',22);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_CMDB_HIDE_MENU_GRP_JSQ',22);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_SYMPHONY_IF_INFO_RIC',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_SYMPHONY_IF_INFO_JSQ',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('A_DEL_OPERATION_LIST_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('A_DEL_OPERATION_LIST_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('A_DEL_FILE_LIST_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('A_DEL_FILE_LIST_JSQ',1);


INSERT INTO A_SYSTEM_CONFIG_LIST (ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000001,'IP_FILTER','IP address restrictions ',NULL,'You can enable/disable the access control that has used the IP address' || CHR(10) || 'You can edit the White list for controlling access on the IP address filter list menu ' || CHR(10) || 'Blank: disable' || CHR(10) || '1: Enable','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000001,'IP_FILTER','IP address restrictions ',NULL,'You can enable/disable the access control that has used the IP address' || CHR(10) || 'You can edit the White list for controlling access on the IP address filter list menu ' || CHR(10) || 'Blank: disable' || CHR(10) || '1: Enable','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST (ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000002,'FORBIDDEN_UPLOAD','Upload prohibition extension','.exe;.com;.php;.cgi;.sh;.sql;.vbs;.js;.pl;.ini;.htaccess','Extension to prohibit file upload' || CHR(10) || '(single byte semicolon delimiter)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000002,'FORBIDDEN_UPLOAD','Upload prohibition extension','.exe;.com;.php;.cgi;.sh;.sql;.vbs;.js;.pl;.ini;.htaccess','Extension to prohibit file upload' || CHR(10) || '(single byte semicolon delimiter)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST (ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000003,'PWL_EXPIRY','Account lock duration (seconds)','0','Time period (seconds) to maintain locked state from start date/time of account lock' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || 'Zero: Do not lock' || CHR(10) || 'Negative number: Locked account is permanently locked','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000003,'PWL_EXPIRY','Account lock duration (seconds)','0','Time period (seconds) to maintain locked state from start date/time of account lock' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || 'Zero: Do not lock' || CHR(10) || 'Negative number: Locked account is permanently locked','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST (ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000004,'PWL_THRESHOLD','Password error threshold (frequency)','3','Password failure threshold value to lock account' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || 'Zero; Cannot set' || CHR(10) || 'Negative number (integer only): Account lock function will turn OFF (not locked)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-4,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000004,'PWL_THRESHOLD','Password error threshold (frequency)','3','Password failure threshold value to lock account' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || 'Zero; Cannot set' || CHR(10) || 'Negative number (integer only): Account lock function will turn OFF (not locked)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST (ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000005,'PWL_COUNT_MAX','Password error count upper limit (frequency)','5','Maximum number of times for continuous errors in password' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || 'Zero or below: Errors are not counted','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-5,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000005,'PWL_COUNT_MAX','Password error count upper limit (frequency)','5','Maximum number of times for continuous errors in password' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || 'Zero or below: Errors are not counted','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST (ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000006,'PW_REUSE_FORBID','Password re-registration prevention period (days)','180','Time period (days) to prevent registration of same password again' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || 'Zero or less: Same password can be used as time period to prevent re-registration has expired','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-6,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000006,'PW_REUSE_FORBID','Password re-registration prevention period (days)','180','Time period (days) to prevent registration of same password again' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || 'Zero or less: Same password can be used as time period to prevent re-registration has expired','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST (ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000007,'PASSWORD_EXPIRY','Password validity period (days)','90','Validity period of password (days)' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || 'Zero or less: Permanently valid (can use)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-7,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000007,'PASSWORD_EXPIRY','Password validity period (days)','90','Validity period of password (days)' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || 'Zero or less: Permanently valid (can use)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST (ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000008,'AUTH_IDLE_EXPIRY','Authentication duration: Not operated (seconds)','3600','Time period (seconds) to maintain authentication (session) at the time of no operation' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || '(However, this value is less than the value specified in "session.gc_maxlifetime" of php.ini)' || CHR(10) || 'Zero or less: Cannot set','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-8,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000008,'AUTH_IDLE_EXPIRY','Authentication duration: Not operated (seconds)','3600','Time period (seconds) to maintain authentication (session) at the time of no operation' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || '(However, this value is less than the value specified in "session.gc_maxlifetime" of php.ini)' || CHR(10) || 'Zero or less: Cannot set','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST (ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000009,'AUTH_SES_EXPIRY','Authentication duration: Maximum period (seconds)','10800','Maximum time period (seconds) to maintain authentication (session)' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || '(However, this value is less than the value specified in "session.gc_maxlifetime" of php.ini)' || CHR(10) || 'Zero or less: Cannot set','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SYSTEM_CONFIG_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITEM_ID,CONFIG_ID,CONFIG_NAME,VALUE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-9,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000009,'AUTH_SES_EXPIRY','Authentication duration: Maximum period (seconds)','10800','Maximum time period (seconds) to maintain authentication (session)' || CHR(10) || 'Positive number (integer only): Same as above' || CHR(10) || '(However, this value is less than the value specified in "session.gc_maxlifetime" of php.ini)' || CHR(10) || 'Zero or less: Cannot set','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000001,'Exastro IT Automation',NULL,NULL,'Common menu group for users','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000001,'Exastro IT Automation',NULL,NULL,'Common menu group for users','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000002,'Management Console','kanri.png',10,'Menu group for system administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000002,'Management Console','kanri.png',10,'Menu group for system administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000003,'Basic Console','kihon.png',20,'Basic Console','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000003,'Basic Console','kihon.png',20,'Basic Console','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000101,2100000001,'Login screen',NULL,NULL,NULL,0,0,2,2,1,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-101,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000101,2100000001,'Login screen',NULL,NULL,NULL,0,0,2,2,1,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000102,2100000001,'System error',NULL,NULL,NULL,0,0,2,2,2,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-102,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000102,2100000001,'System error',NULL,NULL,NULL,0,0,2,2,2,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000103,2100000001,'Access warning against unauthorized operations',NULL,NULL,NULL,0,0,2,2,3,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-103,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000103,2100000001,'Access warning against unauthorized operations',NULL,NULL,NULL,0,0,2,2,3,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000104,2100000001,'Access warning from unauthorized terminal',NULL,NULL,NULL,0,0,2,2,4,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-104,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000104,2100000001,'Access warning from unauthorized terminal',NULL,NULL,NULL,0,0,2,2,4,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000105,2100000001,'Login ID list',NULL,NULL,NULL,0,0,2,2,5,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-105,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000105,2100000001,'Login ID list',NULL,NULL,NULL,0,0,2,2,5,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000106,2100000001,'Change Password',NULL,NULL,NULL,0,0,2,2,6,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-106,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000106,2100000001,'Change Password',NULL,NULL,NULL,0,0,2,2,6,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000107,2100000001,'Account lock error',NULL,NULL,NULL,0,0,2,2,7,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-107,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000107,2100000001,'Account lock error',NULL,NULL,NULL,0,0,2,2,7,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000202,2100000002,'System settings',NULL,NULL,NULL,1,0,1,1,2,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-202,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000202,2100000002,'System settings',NULL,NULL,NULL,1,0,1,1,2,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000203,2100000002,'IP address filter list',NULL,NULL,NULL,1,0,1,1,3,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-203,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000203,2100000002,'IP address filter list',NULL,NULL,NULL,1,0,1,1,3,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000204,2100000002,'Menu group list',NULL,NULL,NULL,1,0,1,1,4,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-204,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000204,2100000002,'Menu group list',NULL,NULL,NULL,1,0,1,1,4,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000205,2100000002,'Menu list',NULL,NULL,NULL,1,0,1,1,5,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-205,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000205,2100000002,'Menu list',NULL,NULL,NULL,1,0,1,1,5,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000207,2100000002,'Role list',NULL,NULL,NULL,1,0,1,1,7,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-207,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000207,2100000002,'Role list',NULL,NULL,NULL,1,0,1,1,7,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000208,2100000002,'User list',NULL,NULL,NULL,1,0,1,1,8,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-208,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000208,2100000002,'User list',NULL,NULL,NULL,1,0,1,1,8,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000209,2100000002,'User/Menu link list',NULL,NULL,NULL,1,0,1,1,9,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-209,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000209,2100000002,'User/Menu link list',NULL,NULL,NULL,1,0,1,1,9,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000210,2100000002,'Role/User link list',NULL,NULL,NULL,1,0,1,1,10,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-210,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000210,2100000002,'Role/User link list',NULL,NULL,NULL,1,0,1,1,10,'Cannot discard','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000302,2100000003,'OS type master',NULL,NULL,NULL,1,0,1,1,10,'os_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-302,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000302,2100000003,'OS type master',NULL,NULL,NULL,1,0,1,1,10,'os_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000303,2100000003,'Device list',NULL,NULL,NULL,1,0,1,2,20,'manage_range_system_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-303,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000303,2100000003,'Device list',NULL,NULL,NULL,1,0,1,2,20,'manage_range_system_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000501,2100000003,'Associated menu',NULL,NULL,NULL,1,0,1,2,30,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-501,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000501,2100000003,'Associated menu',NULL,NULL,NULL,1,0,1,2,30,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000502,2100000003,'Associated menu table list',NULL,NULL,NULL,1,0,1,2,31,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-502,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000502,2100000003,'Associated menu table list',NULL,NULL,NULL,1,0,1,2,31,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000503,2100000003,'Associated menu column list',NULL,NULL,NULL,1,0,1,2,32,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-503,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000503,2100000003,'Associated menu column list',NULL,NULL,NULL,1,0,1,2,32,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000304,2100000003,'Input operation list',NULL,NULL,NULL,1,0,1,2,40,'op_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-304,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000304,2100000003,'Input operation list',NULL,NULL,NULL,1,0,1,2,40,'op_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000305,2100000003,'Movement list',NULL,NULL,NULL,1,0,1,1,50,'pattern_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-305,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000305,2100000003,'Movement list',NULL,NULL,NULL,1,0,1,1,50,'pattern_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000306,2100000003,'Symphony class editor',NULL,NULL,NULL,1,0,1,1,70,'symphony_cls_edit','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-306,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000306,2100000003,'Symphony class editor',NULL,NULL,NULL,1,0,1,1,70,'symphony_cls_edit','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000307,2100000003,'Symphony class List',NULL,NULL,NULL,1,0,1,1,60,'symphony_cls_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-307,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000307,2100000003,'Symphony class List',NULL,NULL,NULL,1,0,1,1,60,'symphony_cls_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000308,2100000003,'Symphony execution',NULL,NULL,NULL,1,0,1,1,80,'symphony_ins_construct','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-308,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000308,2100000003,'Symphony execution',NULL,NULL,NULL,1,0,1,1,80,'symphony_ins_construct','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000309,2100000003,'Symphony execution checking',NULL,NULL,NULL,1,0,2,2,90,'symphony_ins_monitor','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-309,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000309,2100000003,'Symphony execution checking',NULL,NULL,NULL,1,0,2,2,90,'symphony_ins_monitor','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000310,2100000003,'Symphony execution list',NULL,NULL,NULL,1,0,1,2,100,'symphony_ins_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-310,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000310,2100000003,'Symphony execution list',NULL,NULL,NULL,1,0,1,2,100,'symphony_ins_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000401,2100000003,'Export Symphony/Operation',NULL,NULL,NULL,1,0,1,1,110,'symphony/operation export','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-401,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000401,2100000003,'Export Symphony/Operation',NULL,NULL,NULL,1,0,1,1,110,'symphony/operation export','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000402,2100000003,'Import Symphony/Operation',NULL,NULL,NULL,1,0,2,2,120,'symphony/operation import','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-402,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000402,2100000003,'Import Symphony/Operation',NULL,NULL,NULL,1,0,2,2,120,'symphony/operation import','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000403,2100000003,'Export/Import Symphony/Operation list',NULL,NULL,NULL,1,0,1,2,130,'export/import management','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-403,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000403,2100000003,'Export/Import Symphony/Operation list',NULL,NULL,NULL,1,0,1,2,130,'export/import management','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000211,2100000002,'Export menu',NULL,NULL,NULL,1,0,2,2,11,'data_export','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-211,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000211,2100000002,'Export menu',NULL,NULL,NULL,1,0,2,2,11,'data_export','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000212,2100000002,'Import menu',NULL,NULL,NULL,1,0,2,2,12,'data_import','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-212,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000212,2100000002,'Import menu',NULL,NULL,NULL,1,0,2,2,12,'data_import','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000213,2100000002,'Export/Import menu list',NULL,NULL,NULL,1,0,1,2,13,'data_import_management','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-213,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000213,2100000002,'Export/Import menu list',NULL,NULL,NULL,1,0,1,2,13,'data_import_management','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000214,2100000002,'Operation delete list',NULL,NULL,NULL,1,0,1,2,14,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-214,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000214,2100000002,'Operation delete list',NULL,NULL,NULL,1,0,1,2,14,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000215,2100000002,'File delete list',NULL,NULL,NULL,1,0,1,2,15,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-215,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000215,2100000002,'File delete list',NULL,NULL,NULL,1,0,1,2,15,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000311,2100000003,'movement associated with Symphony list',NULL,NULL,NULL,1,0,1,1,30,'movement_ins_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-311,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000311,2100000003,'movement associated with Symphony list',NULL,NULL,NULL,1,0,1,1,30,'movement_ins_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000221,2100000002,'AD group judgement',NULL,NULL,NULL,1,0,1,1,21,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-221,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000221,2100000002,'AD group judgement',NULL,NULL,NULL,1,0,1,1,21,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000222,2100000002,'AD user judgement',NULL,NULL,NULL,1,0,1,1,22,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-222,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000222,2100000002,'AD user judgement',NULL,NULL,NULL,1,0,1,1,22,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000312,2100000003,'Movement instance list',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-312,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000312,2100000003,'Movement instance list',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000313,2100000003,'Symphony Interface information',NULL,NULL,NULL,1,0,1,1,55,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-313,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000313,2100000003,'Symphony Interface information',NULL,NULL,NULL,1,0,1,1,55,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000299,2100000002,'version',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-299,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000299,2100000002,'version',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_ROLE_LIST (ROLE_ID,ROLE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'System Administrator','System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROLE_ID,ROLE_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'System Administrator','System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'administrator','5f4dcc3b5aa765d61d8327deb882cf99','System Administrator','sample@xxx.bbb.ccc','System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'administrator','5f4dcc3b5aa765d61d8327deb882cf99','System Administrator','sample@xxx.bbb.ccc','System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2,'c01','5ebbc37e034d6874a2af59eb04beaa52','Role association management procedure','sample@xxx.bbb.ccc','Role association management procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-2,'c01','5ebbc37e034d6874a2af59eb04beaa52','Role association management procedure','sample@xxx.bbb.ccc','Role association management procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-3,'c02','5ebbc37e034d6874a2af59eb04beaa52','Symphony management procedure','sample@xxx.bbb.ccc','Symphony management procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-3,'c02','5ebbc37e034d6874a2af59eb04beaa52','Symphony management procedure','sample@xxx.bbb.ccc','Symphony management procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-4,'c04','5ebbc37e034d6874a2af59eb04beaa52','Association target menu analysis procedure','sample@xxx.bbb.ccc','Association target menu analysis procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-4,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-4,'c04','5ebbc37e034d6874a2af59eb04beaa52','Association target menu analysis procedure','sample@xxx.bbb.ccc','Association target menu analysis procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100014,'a7a','5ebbc37e034d6874a2af59eb04beaa52','Work history regular discard procedure','sample@xxx.bbb.ccc','Work history regular discard procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100014,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100014,'a7a','5ebbc37e034d6874a2af59eb04beaa52','Work history regular discard procedure','sample@xxx.bbb.ccc','Work history regular discard procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100023,'a7a','5ebbc37e034d6874a2af59eb04beaa52','Execution instance history regular discard procedure','sample@xxx.bbb.ccc','Execution instance history regular discard procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100023,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100023,'a7a','5ebbc37e034d6874a2af59eb04beaa52','Execution instance history regular discard procedure','sample@xxx.bbb.ccc','Execution instance history regular discard procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100024,'a7b','5ebbc37e034d6874a2af59eb04beaa52','Data portability procedure','sample@xxx.bbb.ccc','Data portability procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100024,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100024,'a7b','5ebbc37e034d6874a2af59eb04beaa52','Data portability procedure','sample@xxx.bbb.ccc','Data portability procedure','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100031,'a06','5ebbc37e034d6874a2af59eb04beaa52','ActiveDirectory user/role synchronization management procedure','sample@xxx.bbb.ccc',NULL,'H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100031,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100031,'a06','5ebbc37e034d6874a2af59eb04beaa52','ActiveDirectory user/role synchronization management procedure','sample@xxx.bbb.ccc',NULL,'H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000202,1,2100000202,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-202,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000202,1,2100000202,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000203,1,2100000203,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-203,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000203,1,2100000203,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000204,1,2100000204,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-204,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000204,1,2100000204,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000205,1,2100000205,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-205,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000205,1,2100000205,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000207,1,2100000207,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-207,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000207,1,2100000207,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000208,1,2100000208,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-208,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000208,1,2100000208,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000209,1,2100000209,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-209,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000209,1,2100000209,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000210,1,2100000210,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-210,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000210,1,2100000210,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000302,1,2100000302,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-302,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000302,1,2100000302,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000303,1,2100000303,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-303,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000303,1,2100000303,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000304,1,2100000304,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-304,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000304,1,2100000304,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000305,1,2100000305,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-305,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000305,1,2100000305,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000306,1,2100000306,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-306,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000306,1,2100000306,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000307,1,2100000307,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-307,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000307,1,2100000307,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000308,1,2100000308,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-308,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000308,1,2100000308,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000309,1,2100000309,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-309,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000309,1,2100000309,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000310,1,2100000310,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-310,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000310,1,2100000310,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000401,1,2100000401,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-401,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000401,1,2100000401,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000402,1,2100000402,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-402,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000402,1,2100000402,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000403,1,2100000403,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-403,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000403,1,2100000403,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000501,1,2100000501,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-501,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000501,1,2100000501,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000502,1,2100000502,2,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-502,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000502,1,2100000502,2,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000503,1,2100000503,2,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-503,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000503,1,2100000503,2,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000211,1,2100000211,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-211,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000211,1,2100000211,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000212,1,2100000212,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-212,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000212,1,2100000212,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000213,1,2100000213,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-213,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000213,1,2100000213,2,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000214,1,2100000214,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-214,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000214,1,2100000214,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000215,1,2100000215,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-215,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000215,1,2100000215,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000311,1,2100000311,2,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-311,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000311,1,2100000311,2,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000221,1,2100000221,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-221,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000221,1,2100000221,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000222,1,2100000222,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-222,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000222,1,2100000222,1,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000312,1,2100000312,2,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-312,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000312,1,2100000312,2,'System Administrator','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000313,1,2100000313,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-313,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000313,1,2100000313,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000299,1,2100000299,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-299,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000299,1,2100000299,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_ROLE_ACCOUNT_LINK_LIST (LINK_ID,ROLE_ID,USER_ID,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,1,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_ACCOUNT_LINK_LIST_JNL  (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,USER_ID,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,1,1,'System Administrator','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_LOGIN_NECESSITY_LIST ( FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(0,'Not required','For maintenance of menu','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_LOGIN_NECESSITY_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS, FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',0,'Not required','For maintenance of menu','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_LOGIN_NECESSITY_LIST ( FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Required','For maintenance of menu','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_LOGIN_NECESSITY_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS, FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Required','For maintenance of menu','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_SERVICE_STATUS_LIST ( FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(0,'Service available',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SERVICE_STATUS_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS, FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',0,'Service available',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SERVICE_STATUS_LIST ( FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Menu under development','For development of menu','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SERVICE_STATUS_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS, FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Menu under development','For development of menu','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_REPRESENTATIVE_LIST ( FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(0,'Sub','For maintenance of contents file','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_REPRESENTATIVE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS, FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',0,'Sub','For maintenance of contents file','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_REPRESENTATIVE_LIST ( FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Main','For maintenance of contents file','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_REPRESENTATIVE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS, FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Main','For maintenance of contents file','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_PRIVILEGE_LIST ( FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Can perform maintenance','For maintenance of role/menu link','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_PRIVILEGE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS, FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Can perform maintenance','For maintenance of role/menu link','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_PRIVILEGE_LIST ( FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'View only','For maintenance of role/menu link','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_PRIVILEGE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS, FLAG,NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'View only','For maintenance of role/menu link','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_TODO_MASTER (TODO_ID,TODO_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Yes',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_TODO_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TODO_ID,TODO_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Yes',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_TODO_MASTER (TODO_ID,TODO_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'No',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_TODO_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TODO_ID,TODO_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'No',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_RELATE_STATUS (RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'2100000310','B_SYM_EXE_STATUS',5,7,8,6,9,1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_RELATE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'2100000310','B_SYM_EXE_STATUS',5,7,8,6,9,1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_RELATE_STATUS (RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'2100020113','D_ANSIBLE_LNS_INS_STATUS',5,6,7,8,10,2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_RELATE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'2100020113','D_ANSIBLE_LNS_INS_STATUS',5,6,7,8,10,2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_RELATE_STATUS (RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'2100020213','D_ANSIBLE_PNS_INS_STATUS',5,6,7,8,10,3,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_RELATE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',3,'2100020213','D_ANSIBLE_PNS_INS_STATUS',5,6,7,8,10,3,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_RELATE_STATUS (RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'2100020314','D_ANSIBLE_LRL_INS_STATUS',5,6,7,8,10,4,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_RELATE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',4,'2100020314','D_ANSIBLE_LRL_INS_STATUS',5,6,7,8,10,4,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_RELATE_STATUS (RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'2100060011','D_DSC_INS_STATUS',5,6,7,8,10,7,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_RELATE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',7,'2100060011','D_DSC_INS_STATUS',5,6,7,8,10,7,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_RELATE_STATUS (RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'2100070006','D_OPENST_STATUS',9,8,7,6,10,8,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_RELATE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RELATE_STATUS_ID,MENU_ID,STATUS_TAB_NAME,COMPLETE_ID,FAILED_ID,UNEXPECTED_ID,EMERGENCY_ID,CANCEL_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',8,'2100070006','D_OPENST_STATUS',9,8,7,6,10,8,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_CMDB_MENU_COL_TYPE (COLUMN_TYPE_ID,COLUMN_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Value type',100,'Value type','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_MENU_COL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,COLUMN_TYPE_ID,COLUMN_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Value type',100,'Value type','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_MENU_COL_TYPE (COLUMN_TYPE_ID,COLUMN_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Key type',200,'Key type','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_MENU_COL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,COLUMN_TYPE_ID,COLUMN_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Key type',200,'Key type','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_MENU_COL_TYPE (COLUMN_TYPE_ID,COLUMN_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Key-Value type',300,'Key-value type','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_MENU_COL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,COLUMN_TYPE_ID,COLUMN_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',3,'Key-Value type',300,'Key-value type','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'2100000001',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'2100000001',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'2100000002',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'2100000002',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'2100000003',3,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',3,'2100000003',3,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'2100000004',4,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',4,'2100000004',4,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'2100011501',5,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',5,'2100011501',5,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'2100011502',6,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',6,'2100011502',6,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'2100011601',7,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',7,'2100011601',7,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'2100011701',8,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',8,'2100011701',8,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'2100020000',9,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',9,'2100020000',9,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'2100020001',10,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',10,'2100020001',10,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,'2100020002',11,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',11,'2100020002',11,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(12,'2100020003',12,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(12,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',12,'2100020003',12,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(13,'2100030001',13,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(13,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',13,'2100030001',13,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(14,'2100040001',14,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(14,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',14,'2100040001',14,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(15,'2100050001',15,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(15,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',15,'2100050001',15,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(16,'2100060001',16,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(16,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',16,'2100060001',16,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(17,'2100070001',17,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(17,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',17,'2100070001',17,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(18,'2100120001',18,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(18,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',18,'2100120001',18,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(19,'2100130001',19,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(19,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',19,'2100130001',19,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(20,'2100130002',20,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(20,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',20,'2100130002',20,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP (HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(22,'2100011609',22,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_CMDB_HIDE_MENU_GRP_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HIDE_ID,MENU_GROUP_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(22,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',22,'2100011609',22,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_SORT_MENULIST (SORT_MENULIST_ID,USER_NAME,MENU_ID_LIST,SORT_ID_LIST,DISPLAY_MODE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'administrator',NULL,NULL,'middle_panel',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_SORT_MENULIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SORT_MENULIST_ID,USER_NAME,MENU_ID_LIST,SORT_ID_LIST,DISPLAY_MODE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'administrator',NULL,NULL,'middle_panel',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000001,3600,7200,'C_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Input operation list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000001,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000001,3600,7200,'C_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Input operation list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000002,3600,7200,'C_SYMPHONY_INSTANCE_MNG','SYMPHONY_INSTANCE_NO','OPERATION_NO_UAPK','SELECT SYMPHONY_STORAGE_PATH_ITA AS PATH FROM C_SYMPHONY_IF_INFO WHERE DISUSE_FLAG="0"','/__data_relay_storage__/symphony/',NULL,NULL,NULL,'Symphony execution list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000002,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000002,3600,7200,'C_SYMPHONY_INSTANCE_MNG','SYMPHONY_INSTANCE_NO','OPERATION_NO_UAPK','SELECT SYMPHONY_STORAGE_PATH_ITA AS PATH FROM C_SYMPHONY_IF_INFO WHERE DISUSE_FLAG="0"','/__data_relay_storage__/symphony/',NULL,NULL,NULL,'Symphony execution list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000003,3600,7200,'C_MOVEMENT_INSTANCE_MNG','MOVEMENT_INSTANCE_NO','OVRD_OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Movement instance list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000003,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000003,3600,7200,'C_MOVEMENT_INSTANCE_MNG','MOVEMENT_INSTANCE_NO','OVRD_OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Movement instance list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000001,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000001,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000001,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000002,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_export','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000002,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000002,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_export','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000003,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_import/backup','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000003,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000003,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_import/backup','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000004,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_import/import','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000004,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000004,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_import/import','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000005,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_import/upload','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000005,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000005,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_import/upload','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000006,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_import/uploadfiles','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000006,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000006,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/data_import/uploadfiles','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000007,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/event_mail','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000007,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000007,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/event_mail','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000008,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/file_up_column','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000008,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000008,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/file_up_column','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000009,30,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/ky_mail_queues/ky_sysmail_0_queue','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000009,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000009,30,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/ky_mail_queues/ky_sysmail_0_queue','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000010,30,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/ky_mail_queues/ky_sysmail_1_success','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000010,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000010,30,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/ky_mail_queues/ky_sysmail_1_success','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000011,30,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/ky_mail_queues/ky_sysmail_2_error','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000011,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000011,30,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/ky_mail_queues/ky_sysmail_2_error','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000014,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/update_by_file_error','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000014,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000014,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/update_by_file_error','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000015,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/logs/update_by_file','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000015,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000015,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/logs/update_by_file','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000016,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/sym_ope_export','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000016,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000016,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/sym_ope_export','*',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000017,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/sym_ope_import/upload','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000017,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000017,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/sym_ope_import/upload','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST (ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000018,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/sym_ope_import/import','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_FILE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DEL_DAYS,TARGET_DIR,TARGET_FILE,DEL_SUB_DIR_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000018,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000018,1,'%%%%%ITA_DIRECTORY%%%%%/ita-root/temp/sym_ope_import/import','*',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_PROC_LOADED_LIST (ROW_ID,PROC_NAME,LOADED_FLG,LAST_UPDATE_TIMESTAMP) VALUES(2100000501,'ky_cmdbmenuanalysis-workflow','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'));


INSERT INTO D_FLAG_LIST_01 (FLAG_ID,FLAG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'●',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO D_FLAG_LIST_01_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FLAG_ID,FLAG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'●',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_VALID_INVALID_MASTER (FLAG_ID,FLAG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'VALID',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_VALID_INVALID_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FLAG_ID,FLAG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'VALID',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_VALID_INVALID_MASTER (FLAG_ID,FLAG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'INVALID',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_VALID_INVALID_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FLAG_ID,FLAG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'INVALID',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO C_SYMPHONY_IF_INFO (SYMPHONY_IF_INFO_ID,SYMPHONY_STORAGE_PATH_ITA,SYMPHONY_REFRESH_INTERVAL,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/symphony',3000,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO C_SYMPHONY_IF_INFO_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYMPHONY_IF_INFO_ID,SYMPHONY_STORAGE_PATH_ITA,SYMPHONY_REFRESH_INTERVAL,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/symphony',3000,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_HARDAWRE_TYPE (HARDAWRE_TYPE_ID,HARDAWRE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'SV',1,'Server','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_HARDAWRE_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HARDAWRE_TYPE_ID,HARDAWRE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'SV',1,'Server','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_HARDAWRE_TYPE (HARDAWRE_TYPE_ID,HARDAWRE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'ST',2,'Storage','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_HARDAWRE_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HARDAWRE_TYPE_ID,HARDAWRE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'ST',2,'Storage','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_HARDAWRE_TYPE (HARDAWRE_TYPE_ID,HARDAWRE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'NW',3,'Network','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_HARDAWRE_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HARDAWRE_TYPE_ID,HARDAWRE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',3,'NW',3,'Network','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_PROTOCOL (PROTOCOL_ID,PROTOCOL_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'telnet',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_PROTOCOL_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,PROTOCOL_ID,PROTOCOL_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'telnet',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_PROTOCOL (PROTOCOL_ID,PROTOCOL_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'ssh',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_PROTOCOL_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,PROTOCOL_ID,PROTOCOL_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'ssh',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_HOST_DESIGNATE_TYPE_LIST (HOST_DESIGNATE_TYPE_ID,HOST_DESIGNATE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'IP',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_HOST_DESIGNATE_TYPE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HOST_DESIGNATE_TYPE_ID,HOST_DESIGNATE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'IP',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_HOST_DESIGNATE_TYPE_LIST (HOST_DESIGNATE_TYPE_ID,HOST_DESIGNATE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Host name',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_HOST_DESIGNATE_TYPE_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,HOST_DESIGNATE_TYPE_ID,HOST_DESIGNATE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Host name',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_LOGIN_AUTH_TYPE (LOGIN_AUTH_TYPE_ID,LOGIN_AUTH_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Key authentication',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_LOGIN_AUTH_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LOGIN_AUTH_TYPE_ID,LOGIN_AUTH_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Key authentication',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_LOGIN_AUTH_TYPE (LOGIN_AUTH_TYPE_ID,LOGIN_AUTH_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Password authentication',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_LOGIN_AUTH_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LOGIN_AUTH_TYPE_ID,LOGIN_AUTH_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Password authentication',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_DP_STATUS_MASTER (TASK_ID,TASK_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Unexecuted',NULL,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TASK_ID,TASK_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Unexecuted',NULL,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_STATUS_MASTER (TASK_ID,TASK_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Executing',NULL,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TASK_ID,TASK_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Executing',NULL,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_STATUS_MASTER (TASK_ID,TASK_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Completed',NULL,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TASK_ID,TASK_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',3,'Completed',NULL,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_STATUS_MASTER (TASK_ID,TASK_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Completed(error)',NULL,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_STATUS_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,TASK_ID,TASK_STATUS,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',4,'Completed(error)',NULL,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('1','2100000101');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('2','2100000102');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('3','2100000103');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('4','2100000104');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('5','2100000105');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('6','2100000106');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('7','2100000107');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('8','2100000211');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('9','2100000212');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('10','2100000213');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('11','2100000306');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('12','2100000308');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('13','2100000309');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('14','2100000310');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('15','2100000312');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('17','2100020111');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('18','2100020112');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('19','2100020113');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('20','2100020211');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('21','2100020212');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('22','2100020213');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('23','2100020312');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('24','2100020313');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('25','2100020314');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('26','2100040105');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('27','2100040109');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('28','2100040110');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('29','2100040111');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('30','2100040114');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('31','2100060009');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('32','2100060010');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('33','2100060011');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('34','2100070004');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('35','2100070005');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('36','2100070006');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('37','2100070007');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('38','2100150004');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('39','2100150005');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('40','2100150102');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('41','2100150103');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('42','2100150104');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('43','2100150105');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('44','2100150106');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('45','2100160003');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('46','2100160004');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('49','2100170005');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('50','2100000299');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('51','2100000401');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('52','2100000402');

INSERT INTO B_DP_HIDE_MENU_LIST (HIDE_ID,MENU_ID) VALUES('53','2100000403');


INSERT INTO B_DP_TYPE (ROW_ID,DP_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Export',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DP_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Export',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_TYPE (ROW_ID,DP_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Import',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,DP_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Import',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_DP_IMPORT_TYPE (ROW_ID,IMPORT_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Normal',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_IMPORT_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,IMPORT_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Normal',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_IMPORT_TYPE (ROW_ID,IMPORT_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Without disuse data',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DP_IMPORT_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,IMPORT_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Without disuse data',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100000001','1',NULL,NULL,'2100000307','SYMPHONY_CLASS_NO',NULL,NULL);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100000002','2','2100000001','SYMPHONY_CLASS_NO','2100000311','SYMPHONY_CLASS_NO',NULL,NULL);


INSERT INTO B_OPERATION_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100000001','1',NULL,NULL,'2100000304','OPERATION_NO_UAPK',NULL,NULL);


INSERT INTO B_SVC_TO_STOP_IMP_SYM_OPE (ROW_ID,SERVICE_NAME) VALUES('2100000001','ky_legacy_valautostup-workflow');

INSERT INTO B_SVC_TO_STOP_IMP_SYM_OPE (ROW_ID,SERVICE_NAME) VALUES('2100000002','ky_legacy_varsautolistup-workflow');

INSERT INTO B_SVC_TO_STOP_IMP_SYM_OPE (ROW_ID,SERVICE_NAME) VALUES('2100000003','ky_pioneer_valautostup-workflow');

INSERT INTO B_SVC_TO_STOP_IMP_SYM_OPE (ROW_ID,SERVICE_NAME) VALUES('2100000004','ky_pioneer_varsautolistup-workflow');

INSERT INTO B_SVC_TO_STOP_IMP_SYM_OPE (ROW_ID,SERVICE_NAME) VALUES('2100000005','ky_legacy_role_valautostup-workflow');

INSERT INTO B_SVC_TO_STOP_IMP_SYM_OPE (ROW_ID,SERVICE_NAME) VALUES('2100000006','ky_legacy_role_varsautolistup-workflow');

INSERT INTO B_SVC_TO_STOP_IMP_SYM_OPE (ROW_ID,SERVICE_NAME) VALUES('2100000007','ky_dsc_valautostup-workflow');

INSERT INTO B_SVC_TO_STOP_IMP_SYM_OPE (ROW_ID,SERVICE_NAME) VALUES('2100000008','ky_dsc_varsautolistup-workflow');


INSERT INTO B_SYM_EXE_STATUS (SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Unexecuted',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Unexecuted',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS (SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Unexecuted (schedule)',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Unexecuted (schedule)',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS (SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Executing',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',3,'Executing',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS (SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Executing (delay)',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',4,'Executing (delay)',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS (SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'Normal end',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',5,'Normal end',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS (SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'Emergency stop',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',6,'Emergency stop',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS (SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'Abend',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',7,'Abend',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS (SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'Unexpected error',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',8,'Unexpected error',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS (SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'Schedule cancellation',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_EXE_STATUS_ID,SYM_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',9,'Schedule cancellation',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_SYM_ABORT_FLAG (SYM_ABORT_FLAG_ID,SYM_ABORT_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Not issued',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_ABORT_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_ABORT_FLAG_ID,SYM_ABORT_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Not issued',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_ABORT_FLAG (SYM_ABORT_FLAG_ID,SYM_ABORT_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Issued',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_SYM_ABORT_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYM_ABORT_FLAG_ID,SYM_ABORT_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Issued',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Unexecuted',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Unexecuted',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Preparing',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Preparing',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Executing',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',3,'Executing',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Executing (delay)',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',4,'Executing (delay)',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'Execution completed',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',5,'Execution completed',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'Abend',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',6,'Abend',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'Emergency stop',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',7,'Emergency stop',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'On hold',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',8,'On hold',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'Normal end',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',9,'Normal end',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'Preparation error',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',10,'Preparation error',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,'Unexpected error',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',11,'Unexpected error',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(12,'Skip completed',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(12,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',12,'Skip completed',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(13,'On hold after skip',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(13,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',13,'On hold after skip',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS (MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(14,'End Skip',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_EXE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_EXE_STATUS_ID,MOV_EXE_STATUS_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(14,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',14,'End Skip',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_MOV_NEXT_PENDING_FLAG (MOV_NEXT_PENDING_FLAG_ID,MOV_NEXT_PENDING_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Yes',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_NEXT_PENDING_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_NEXT_PENDING_FLAG_ID,MOV_NEXT_PENDING_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Yes',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_NEXT_PENDING_FLAG (MOV_NEXT_PENDING_FLAG_ID,MOV_NEXT_PENDING_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'No',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_NEXT_PENDING_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_NEXT_PENDING_FLAG_ID,MOV_NEXT_PENDING_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'No',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_MOV_RELEASED_FLAG (MOV_RELEASED_FLAG_ID,MOV_RELEASED_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Not released',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_RELEASED_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_RELEASED_FLAG_ID,MOV_RELEASED_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Not released',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_RELEASED_FLAG (MOV_RELEASED_FLAG_ID,MOV_RELEASED_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Released',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_RELEASED_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_RELEASED_FLAG_ID,MOV_RELEASED_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Released',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_MOV_ABT_RECEPT_FLAG (MOV_ABT_RECEPT_FLAG_ID,MOV_ABT_RECEPT_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Not checked',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_ABT_RECEPT_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_ABT_RECEPT_FLAG_ID,MOV_ABT_RECEPT_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'Not checked',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_ABT_RECEPT_FLAG (MOV_ABT_RECEPT_FLAG_ID,MOV_ABT_RECEPT_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Checked',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_MOV_ABT_RECEPT_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MOV_ABT_RECEPT_FLAG_ID,MOV_ABT_RECEPT_FLAG_NAME,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'Checked',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);


COMMIT;

EXIT;
