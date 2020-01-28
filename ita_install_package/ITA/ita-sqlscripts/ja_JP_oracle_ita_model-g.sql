-- *****************************************************************************
-- *** ***** DSC Common Tables                                               ***
-- *****************************************************************************
-- ----更新系テーブル作成----
-- ステータステーブル
CREATE TABLE B_DSC_STATUS
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
-- 更新系テーブル作成----

-- ----履歴系テーブル作成----
-- ステータステーブル(履歴)
CREATE TABLE B_DSC_STATUS_JNL
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
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- インターフェース情報
CREATE TABLE B_DSC_IF_INFO
(
DSC_IF_INFO_ID                    NUMBER                           ,

DSC_STORAGE_PATH_LNX              VARCHAR2(256)                    , -- ITAデータストレージ
DSC_STORAGE_PATH_DSC              VARCHAR2(256)                    , -- DSCデータストレージ
SYMPHONY_STORAGE_PATH_DSC         VARCHAR2(256)                    , -- Symphonyインスタンスデータリレイストレージパス(DSC)
DSC_PROTOCOL                      VARCHAR2(8)                      , -- プロトコル
DSC_HOSTNAME                      VARCHAR2(128)                    , -- ホスト名称
DSC_PORT                          NUMBER                           , -- ポート
DSC_ACCESS_KEY_ID                 VARCHAR2(64)                     , -- アクセスキー
DSC_SECRET_ACCESS_KEY             VARCHAR2(64)                     , -- パスワード
DSC_REFRESH_INTERVAL              NUMBER                           , -- 状態監視周期
DSC_TAILLOG_LINES                 NUMBER                           , -- 進行状態表示桁数

DISP_SEQ                          NUMBER                           , -- 表示順序
NULL_DATA_HANDLING_FLG            NUMBER                           , -- Null値の連携 1:有効　2:無効
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (DSC_IF_INFO_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- インターフェース情報(履歴)
CREATE TABLE B_DSC_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

DSC_IF_INFO_ID                    NUMBER                           ,

DSC_STORAGE_PATH_LNX              VARCHAR2(256)                    , -- ITAデータストレージ
DSC_STORAGE_PATH_DSC              VARCHAR2(256)                    , -- DSCデータストレージ
SYMPHONY_STORAGE_PATH_DSC         VARCHAR2(256)                    , -- Symphonyインスタンスデータリレイストレージパス(DSC)
DSC_PROTOCOL                      VARCHAR2(8)                      , -- プロトコル
DSC_HOSTNAME                      VARCHAR2(128)                    , -- ホスト名称
DSC_PORT                          NUMBER                           , -- ポート
DSC_ACCESS_KEY_ID                 VARCHAR2(64)                     , -- アクセスキー
DSC_SECRET_ACCESS_KEY             VARCHAR2(64)                     , -- パスワード
DSC_REFRESH_INTERVAL              NUMBER                           , -- 状態監視周期
DSC_TAILLOG_LINES                 NUMBER                           , -- 進行状態表示桁数

DISP_SEQ                          NUMBER                           , -- 表示順序
NULL_DATA_HANDLING_FLG            NUMBER                           , -- Null値の連携 1:有効　2:無効
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----
-- 実行モード情報
CREATE TABLE B_DSC_RUN_MODE
(
RUN_MODE_ID                       NUMBER                           , -- 識別シーケンス

RUN_MODE_NAME                     VARCHAR2(32)                     , -- 動作モード名称

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (RUN_MODE_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 実行モード情報(履歴)
CREATE TABLE B_DSC_RUN_MODE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

RUN_MODE_ID                       NUMBER                           , -- 識別シーケンス

RUN_MODE_NAME                     VARCHAR2(32)                     , -- 動作モード名称

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- *****************************************************************************
-- *** ***** DSC Common Tables                                               ***
-- *****************************************************************************

-- *****************************************************************************
-- *** ***** DSC Tables                                                      ***
-- *****************************************************************************

-- ----更新系テーブル作成
-- リソース素材テーブル
CREATE TABLE B_DSC_RESOURCE
(
RESOURCE_MATTER_ID                NUMBER                           ,

RESOURCE_MATTER_NAME              VARCHAR2(32)                     , -- リソース名称
RESOURCE_MATTER_FILE              VARCHAR2(256)                    , -- リソースファイルパス

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (RESOURCE_MATTER_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- リソース素材テーブル(履歴)
CREATE TABLE B_DSC_RESOURCE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

RESOURCE_MATTER_ID                NUMBER                           ,

RESOURCE_MATTER_NAME              VARCHAR2(32)                     , -- リソース名称
RESOURCE_MATTER_FILE              VARCHAR2(256)                    , -- リソースファイルパス

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- 作業パターン詳細テーブル
CREATE TABLE B_DSC_PATTERN_LINK
(
LINK_ID                           NUMBER                           ,

PATTERN_ID                        NUMBER                           , -- パターンID
RESOURCE_MATTER_ID                NUMBER                           , -- リソースID
INCLUDE_SEQ                       NUMBER                           , -- INCLUDE順序

POWERSHELL_FILE_ID                NUMBER                           , -- ファイルID(PowerShell)
PARAM_FILE_ID                     NUMBER                           , -- ファイルID(Param)
IMPORT_FILE_ID                    NUMBER                           , -- ファイルID(Import)
CONFIGDATA_FILE_ID                NUMBER                           , -- ファイルID(ConfigData)
CMPOPTION_FILE_ID                 NUMBER                           , -- ファイルID(CompileOption)


DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (LINK_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 作業パターン詳細テーブル(履歴)
CREATE TABLE B_DSC_PATTERN_LINK_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

LINK_ID                           NUMBER                           ,

PATTERN_ID                        NUMBER                           , -- パターンID
RESOURCE_MATTER_ID                NUMBER                           , -- リソースID
INCLUDE_SEQ                       NUMBER                           , -- INCLUDE順序

POWERSHELL_FILE_ID                NUMBER                           , -- ファイルID(PowerShell)
PARAM_FILE_ID                     NUMBER                           , -- ファイルID(Param)
IMPORT_FILE_ID                    NUMBER                           , -- ファイルID(Import)
CONFIGDATA_FILE_ID                NUMBER                           , -- ファイルID(ConfigData)
CMPOPTION_FILE_ID                 NUMBER                           , -- ファイルID(CompileOption)

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- 作業対象ホストテーブル
CREATE TABLE B_DSC_PHO_LINK
(
PHO_LINK_ID                       NUMBER                           ,

OPERATION_NO_UAPK                 NUMBER                           , -- オペレーションID
PATTERN_ID                        NUMBER                           , -- パターンID
SYSTEM_ID                         NUMBER                           , -- ホストID

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (PHO_LINK_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 作業対象ホストテーブル(履歴)
CREATE TABLE B_DSC_PHO_LINK_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

PHO_LINK_ID                       NUMBER                           ,

OPERATION_NO_UAPK                 NUMBER                           , -- オペレーションID
PATTERN_ID                        NUMBER                           , -- パターンID
SYSTEM_ID                         NUMBER                           , -- ホストID

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- 代入値マスタテーブル
CREATE TABLE B_DSC_VARS_MASTER
(
VARS_NAME_ID                      NUMBER                           ,

VARS_NAME                         VARCHAR2(256)                    , -- 代入値名称
VARS_DESCRIPTION                  VARCHAR2(256)                    , -- 代入値

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (VARS_NAME_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 代入値マスタテーブル(履歴)
CREATE TABLE B_DSC_VARS_MASTER_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

VARS_NAME_ID                      NUMBER                           ,

VARS_NAME                         VARCHAR2(256)                    , -- 代入値名称
VARS_DESCRIPTION                  VARCHAR2(256)                    , -- 代入値

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- 代入値紐付テーブル
CREATE TABLE B_DSC_PTN_VARS_LINK
(
VARS_LINK_ID                      NUMBER                           ,

PATTERN_ID                        NUMBER                           , -- パターンID
VARS_NAME_ID                      NUMBER                           , -- 代入値名称

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (VARS_LINK_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 代入値紐付テーブル(履歴)
CREATE TABLE B_DSC_PTN_VARS_LINK_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

VARS_LINK_ID                      NUMBER                           ,

PATTERN_ID                        NUMBER                           , -- パターンID
VARS_NAME_ID                      NUMBER                           , -- 代入値名称

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- 代入値管理テーブル
CREATE TABLE B_DSC_VARS_ASSIGN
(
ASSIGN_ID                         NUMBER                           ,

OPERATION_NO_UAPK                 NUMBER                           , -- オペレーションID
PATTERN_ID                        NUMBER                           , -- パターンID
SYSTEM_ID                         NUMBER                           , -- ホストID
VARS_LINK_ID                      NUMBER                           , -- 代入値リンクID
VARS_ENTRY                        VARCHAR2(1024)                   ,
ASSIGN_SEQ                        NUMBER                           ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (ASSIGN_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 代入値管理テーブル(履歴)
CREATE TABLE B_DSC_VARS_ASSIGN_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

ASSIGN_ID                         NUMBER                           ,

OPERATION_NO_UAPK                 NUMBER                           , -- オペレーションID
PATTERN_ID                        NUMBER                           , -- パターンID
SYSTEM_ID                         NUMBER                           , -- ホストID
VARS_LINK_ID                      NUMBER                           , -- 代入値リンクID
VARS_ENTRY                        VARCHAR2(1024)                   ,
ASSIGN_SEQ                        NUMBER                           ,

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----


-- -------------------------------------------------------
-- T-0001 作業インスタンス
-- -------------------------------------------------------
-- ----更新系テーブル作成
-- 実行管理テーブル
CREATE TABLE C_DSC_EXE_INS_MNG
(
EXECUTION_NO                      NUMBER                           ,

STATUS_ID                         NUMBER                           , -- 実行ステータスID
EXECUTION_USER                    VARCHAR2(80)                     , -- 実行ユーザ
SYMPHONY_NAME                     VARCHAR2(256)                    , -- シンフォニークラス名
SYMPHONY_INSTANCE_NO              NUMBER                           , -- Symphonyインスタンス番号
PATTERN_ID                        NUMBER                           , -- パターンID
I_PATTERN_NAME                    VARCHAR2(256)                    ,
I_TIME_LIMIT                      NUMBER                           ,
I_ANS_HOST_DESIGNATE_TYPE_ID      NUMBER                           ,
I_ANS_PARALLEL_EXE                NUMBER                           ,
I_DSC_RETRY_TIMEOUT               NUMBER                           , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add
OPERATION_NO_UAPK                 NUMBER                           , -- オペレーションI
I_OPERATION_NAME                  VARCHAR2(256)                    ,
I_OPERATION_NO_IDBH               NUMBER                           ,
TIME_BOOK                         TIMESTAMP                        ,
TIME_START                        TIMESTAMP                        , -- 開始時間
TIME_END                          TIMESTAMP                        , -- 終了時間
FILE_INPUT                        VARCHAR2(1024)                   , -- 入力パス
FILE_RESULT                       VARCHAR2(1024)                   , -- 出力パス
RUN_MODE                          NUMBER                           , -- ドライランモード 1:通常 2:ドライラン

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (EXECUTION_NO)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 実行管理テーブル(履歴)
CREATE TABLE C_DSC_EXE_INS_MNG_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

EXECUTION_NO                      NUMBER                           ,

STATUS_ID                         NUMBER                           , -- 実行ステータスID
EXECUTION_USER                    VARCHAR2(80)                     , -- 実行ユーザ
SYMPHONY_NAME                     VARCHAR2(256)                    , -- シンフォニークラス名
SYMPHONY_INSTANCE_NO              NUMBER                           , -- Symphonyインスタンス番号
PATTERN_ID                        NUMBER                           , -- パターンID
I_PATTERN_NAME                    VARCHAR2(256)                    ,
I_TIME_LIMIT                      NUMBER                           ,
I_ANS_HOST_DESIGNATE_TYPE_ID      NUMBER                           ,
I_ANS_PARALLEL_EXE                NUMBER                           ,
I_DSC_RETRY_TIMEOUT               NUMBER                           , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add
OPERATION_NO_UAPK                 NUMBER                           , -- オペレーションI
I_OPERATION_NAME                  VARCHAR2(256)                    ,
I_OPERATION_NO_IDBH               NUMBER                           ,
TIME_BOOK                         TIMESTAMP                        ,
TIME_START                        TIMESTAMP                        , -- 開始時間
TIME_END                          TIMESTAMP                        , -- 終了時間
FILE_INPUT                        VARCHAR2(1024)                   , -- 入力パス
FILE_RESULT                       VARCHAR2(1024)                   , -- 出力パス
RUN_MODE                          NUMBER                           , -- ドライランモード 1:通常 2:ドライラン

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);

-- -------------------------------------------------------
-- --T4-0004 DSC 代入値自動登録設定
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_DSC_VAL_ASSIGN (
COLUMN_ID                      NUMBER                  , -- 識別シーケンス
MENU_ID                        NUMBER                  , -- メニューID
COLUMN_LIST_ID                 NUMBER                  , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                       NUMBER                  , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                     NUMBER                  , -- 作業パターンID
VAL_VARS_LINK_ID               NUMBER                  , -- Value値　作業パターン変数紐付
VAL_CHILD_VARS_LINK_ID         NUMBER                  , -- Value値　作業パターンメンバー変数紐付
VAL_ASSIGN_SEQ                 NUMBER                  , -- Value値　代入順序
VAL_CHILD_VARS_COL_SEQ         NUMBER                  , -- Value値　列順序
KEY_VARS_LINK_ID               NUMBER                  , -- Key値　作業パターン変数紐付
KEY_CHILD_VARS_LINK_ID         NUMBER                  , -- Key値　作業パターンメンバー変数紐付
KEY_ASSIGN_SEQ                 NUMBER                  , -- Key値　代入順序
KEY_CHILD_VARS_COL_SEQ         NUMBER                  , -- Key値　列順序
NULL_DATA_HANDLING_FLG         NUMBER                  , -- Null値の連携

DISP_SEQ                       NUMBER                  , -- 表示順序
NOTE                           VARCHAR2(4000)          , -- 備考
DISUSE_FLAG                    VARCHAR2(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          TIMESTAMP               , -- 最終更新日時
LAST_UPDATE_USER               NUMBER                  , -- 最終更新ユーザ
PRIMARY KEY(COLUMN_ID)
);
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_DSC_VAL_ASSIGN_JNL
(
JOURNAL_SEQ_NO                 NUMBER                  , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           TIMESTAMP               , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR2(8)             , -- 履歴用変更種別

COLUMN_ID                      NUMBER                  , -- 識別シーケンス
MENU_ID                        NUMBER                  , -- メニューID
COLUMN_LIST_ID                 NUMBER                  , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                       NUMBER                  , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                     NUMBER                  , -- 作業パターンID
VAL_VARS_LINK_ID               NUMBER                  , -- Value値　作業パターン変数紐付
VAL_CHILD_VARS_LINK_ID         NUMBER                  , -- Value値　作業パターンメンバー変数紐付
VAL_ASSIGN_SEQ                 NUMBER                  , -- Value値　代入順序
VAL_CHILD_VARS_COL_SEQ         NUMBER                  , -- Value値　列順序
KEY_VARS_LINK_ID               NUMBER                  , -- Key値　作業パターン変数紐付
KEY_CHILD_VARS_LINK_ID         NUMBER                  , -- Key値　作業パターンメンバー変数紐付
KEY_ASSIGN_SEQ                 NUMBER                  , -- Key値　代入順序
KEY_CHILD_VARS_COL_SEQ         NUMBER                  , -- Key値　列順序
NULL_DATA_HANDLING_FLG         NUMBER                  , -- Null値の連携

DISP_SEQ                       NUMBER                  , -- 表示順序
NOTE                           VARCHAR2(4000)          , -- 備考
DISUSE_FLAG                    VARCHAR2(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          TIMESTAMP               , -- 最終更新日時
LAST_UPDATE_USER               NUMBER                  , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 履歴系テーブル作成----

-- ----PowerShell素材ファイル更新系テーブル作成
CREATE TABLE B_DSC_POWERSHELL_FILE
(
POWERSHELL_FILE_ID                NUMBER                           , -- ファイルID
POWERSHELL_NAME                   VARCHAR2(128)                    , -- PowerShell素材名
POWERSHELL_FILE                   VARCHAR2(256)                    , -- PowerShell設定ファイル名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (POWERSHELL_FILE_ID)
);
-- PowerShell素材ファイル更新系テーブル作成----

-- ----PowerShell素材ファイル履歴系テーブル作成
CREATE TABLE B_DSC_POWERSHELL_FILE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

POWERSHELL_FILE_ID                NUMBER                           , -- ファイルID
POWERSHELL_NAME                   VARCHAR2(128)                    , -- PowerShell素材名
POWERSHELL_FILE                   VARCHAR2(256)                    , -- PowerShell設定ファイル名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- PowerShell素材ファイル履歴系テーブル作成----

-- ----Param素材ファイル更新系テーブル作成
CREATE TABLE B_DSC_PARAM_FILE
(
PARAM_FILE_ID                     NUMBER                           , -- ファイルID
PARAM_NAME                        VARCHAR2(128)                    , -- Param素材名
PARAM_FILE                        VARCHAR2(256)                    , -- Param設定ファイル名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (PARAM_FILE_ID)
);
-- Param素材ファイル更新系テーブル作成----

-- ----Param素材ファイル履歴系テーブル作成
CREATE TABLE B_DSC_PARAM_FILE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

PARAM_FILE_ID                     NUMBER                           , -- ファイルID
PARAM_NAME                        VARCHAR2(128)                    , -- Param素材名
PARAM_FILE                        VARCHAR2(256)                    , -- Param設定ファイル名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- Param素材ファイル履歴系テーブル作成----

-- ----Import素材ファイル更新系テーブル作成
CREATE TABLE B_DSC_IMPORT_FILE
(
IMPORT_FILE_ID                    NUMBER                           , -- ファイルID
IMPORT_NAME                       VARCHAR2(128)                    , -- Import素材名
IMPORT_FILE                       VARCHAR2(256)                    , -- Import設定ファイル名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (IMPORT_FILE_ID)
);
-- Import 設定ファイル更新系テーブル作成----

-- ----Import素材ファイル履歴系テーブル作成
CREATE TABLE B_DSC_IMPORT_FILE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

IMPORT_FILE_ID                    NUMBER                           , -- ファイルID
IMPORT_NAME                       VARCHAR2(128)                    , -- Import素材名
IMPORT_FILE                       VARCHAR2(256)                    , -- Import設定ファイル名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- Import設定ファイル履歴系テーブル作成----

-- ----ConfigData素材ファイル更新系テーブル作成
CREATE TABLE B_DSC_CONFIGDATA_FILE
(
CONFIGDATA_FILE_ID                NUMBER                           , -- ファイルID
CONFIGDATA_NAME                   VARCHAR2(128)                    , -- ConfigData素材名
CONFIGDATA_FILE                   VARCHAR2(256)                    , -- ConfigData設定ファイル名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (CONFIGDATA_FILE_ID)
);
-- ConfigData素材ファイル更新系テーブル作成----

-- ----ConfigData素材ファイル履歴系テーブル作成
CREATE TABLE B_DSC_CONFIGDATA_FILE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

CONFIGDATA_FILE_ID                NUMBER                           , -- ファイルID
CONFIGDATA_NAME                   VARCHAR2(128)                    , -- ConfigData素材名
CONFIGDATA_FILE                   VARCHAR2(256)                    , -- ConfigData設定ファイル名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- ConfigData素材ファイル履歴系テーブル作成----

-- ----CompileOption素材ファイル更新系テーブル作成
CREATE TABLE B_DSC_CMPOPTION_FILE
(
CMPOPTION_FILE_ID                 NUMBER                           , -- ファイルID
CMPOPTION_NAME                    VARCHAR2(128)                    , -- CompileOption素材名
CMPOPTION_FILE                    VARCHAR2(256)                    , -- CompileOption設定ファイル名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (CMPOPTION_FILE_ID)
);
-- CompileOption素材ファイル更新系テーブル作成----

-- ----CompileOption素材ファイル履歴系テーブル作成
CREATE TABLE B_DSC_CMPOPTION_FILE_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

CMPOPTION_FILE_ID                 NUMBER                           , -- ファイルID
CMPOPTION_NAME                    VARCHAR2(128)                    , -- CompileOption素材名
CMPOPTION_FILE                    VARCHAR2(256)                    , -- CompileOption設定ファイル名

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- CompileOption素材ファイル履歴系テーブル作成----

-- ----資格情報更新系テーブル作成
CREATE TABLE B_DSC_CREDENTIAL
(
CREDENTIAL_ID                     NUMBER                           , -- 資格ID
CREDENTIAL_VARS_NAME              VARCHAR2(128)                    , -- 資格情報埋込変数名
SYSTEM_ID                         NUMBER                           , -- ホストID
CREDENTIAL_USER                   VARCHAR2(60)                     , -- アカウント
CREDENTIAL_PW                     VARCHAR2(60)                     , -- パスワード

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ

PRIMARY KEY (CREDENTIAL_ID)
);
-- 資格情報更新系テーブル作成----

-- ----資格情報履歴系テーブル作成
CREATE TABLE B_DSC_CREDENTIAL_JNL
(
JOURNAL_SEQ_NO                    NUMBER                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              TIMESTAMP                        , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR2(8)                      , -- 履歴用変更種別

CREDENTIAL_ID                     NUMBER                           , -- 資格ID
CREDENTIAL_VARS_NAME              VARCHAR2(128)                    , -- 資格情報埋込変数名
SYSTEM_ID                         NUMBER                           , -- ホストID
CREDENTIAL_USER                   VARCHAR2(60)                     , -- アカウント
CREDENTIAL_PW                     VARCHAR2(60)                     , -- パスワード

DISP_SEQ                          NUMBER                           , -- 表示順序
NOTE                              VARCHAR2(4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR2(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             TIMESTAMP                        , -- 最終更新日時
LAST_UPDATE_USER                  NUMBER                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
);
-- 資格情報履歴系テーブル作成----

-- *****************************************************************************
-- *** DSC Tables *****                                                      ***
-- *****************************************************************************

-- *****************************************************************************
-- *** ***** DSC Views                                                       ***
-- *****************************************************************************
-- ステータステーブル VIEW
CREATE VIEW D_DSC_INS_STATUS     AS 
SELECT * 
FROM B_DSC_STATUS;

CREATE VIEW D_DSC_INS_STATUS_JNL AS 
SELECT * 
FROM B_DSC_STATUS_JNL;

-- インターフェース情報 VIEW
CREATE VIEW D_DSC_IF_INFO     AS 
SELECT * 
FROM B_DSC_IF_INFO;

CREATE VIEW D_DSC_IF_INFO_JNL AS 
SELECT * 
FROM B_DSC_IF_INFO_JNL;

-- 実行モード情報 VIEW
CREATE VIEW D_DSC_INS_RUN_MODE     AS 
SELECT * 
FROM B_DSC_RUN_MODE;

CREATE VIEW D_DSC_INS_RUN_MODE_JNL AS 
SELECT * 
FROM B_DSC_RUN_MODE_JNL;

-- リソース素材テーブル VIEW
CREATE VIEW D_DSC_RESOURCE AS 
SELECT  RESOURCE_MATTER_ID      ,
        RESOURCE_MATTER_NAME    ,
        RESOURCE_MATTER_ID || ':' || RESOURCE_MATTER_NAME RESOURCE,
        RESOURCE_MATTER_FILE    ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM    B_DSC_RESOURCE;

CREATE VIEW D_DSC_RESOURCE_JNL AS 
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        RESOURCE_MATTER_ID      ,
        RESOURCE_MATTER_NAME    ,
        RESOURCE_MATTER_ID || ':' || RESOURCE_MATTER_NAME RESOURCE,
        RESOURCE_MATTER_FILE    ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM B_DSC_RESOURCE_JNL;

-- 作業パターン詳細テーブル VIEW
CREATE VIEW E_DSC_PATTERN AS 
SELECT 
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        PATTERN_ID || ':' || PATTERN_NAME PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        DSC_RETRY_TIMEOUT             ,   -- 2018.05.11 Add
        DISP_SEQ                      ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 8;

CREATE VIEW E_DSC_PATTERN_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                ,
        JOURNAL_REG_DATETIME          ,
        JOURNAL_ACTION_CLASS          ,
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        PATTERN_ID || ':' || PATTERN_NAME PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        DSC_RETRY_TIMEOUT             ,
        DISP_SEQ                      ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH_JNL TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 8;


-- 代入値紐付テーブル VIEW
CREATE VIEW D_DSC_PTN_VARS_LINK AS 
SELECT 
        TAB_A.VARS_LINK_ID            ,
        TAB_A.PATTERN_ID              ,
        TAB_B.PATTERN_NAME            ,
        TAB_A.VARS_NAME_ID            ,
        TAB_C.VARS_NAME               ,
        TAB_A.VARS_LINK_ID || ':' || TAB_C.VARS_NAME VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER
FROM B_DSC_PTN_VARS_LINK     TAB_A

LEFT JOIN E_DSC_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_DSC_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;

CREATE VIEW D_DSC_PTN_VARS_LINK_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                ,
        JOURNAL_REG_DATETIME          ,
        JOURNAL_ACTION_CLASS          ,
        TAB_A.VARS_LINK_ID            ,
        TAB_A.PATTERN_ID              ,
        TAB_B.PATTERN_NAME            ,
        TAB_A.VARS_NAME_ID            ,
        TAB_C.VARS_NAME               ,
        TAB_A.VARS_LINK_ID || ':' || TAB_C.VARS_NAME VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER

FROM B_DSC_PTN_VARS_LINK_JNL TAB_A

LEFT JOIN E_DSC_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_DSC_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;
-- 構造名ポストフィックス(_VFS)=「View-For-P(ulldownSelect)」
-- 登録/更新用なので、結合するテーブルのレコードが廃止されていたら、レコードとして扱わない
CREATE VIEW D_DSC_PTN_VARS_LINK_VFP AS 
SELECT 
        TAB_A.VARS_LINK_ID            ,
        TAB_A.PATTERN_ID              ,
        TAB_B.PATTERN_NAME            ,
        TAB_A.VARS_NAME_ID            ,
        TAB_C.VARS_NAME               ,
        TAB_A.VARS_LINK_ID || ':' || TAB_C.VARS_NAME VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER
FROM B_DSC_PTN_VARS_LINK     TAB_A

LEFT JOIN E_DSC_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_DSC_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
WHERE TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;

-- 実行管理テーブル VIEW
CREATE VIEW E_DSC_EXE_INS_MNG AS
SELECT 
         TAB_A.EXECUTION_NO              ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.SYMPHONY_INSTANCE_NO      , -- Symphonyインスタンス番号
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_DSC_RETRY_TIMEOUT       ,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM C_DSC_EXE_INS_MNG       TAB_A
LEFT JOIN E_DSC_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_DSC_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_DSC_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
;

CREATE VIEW E_DSC_EXE_INS_MNG_JNL AS 
SELECT 
         TAB_A.JOURNAL_SEQ_NO            ,
         TAB_A.JOURNAL_REG_DATETIME      ,
         TAB_A.JOURNAL_ACTION_CLASS      ,
         TAB_A.EXECUTION_NO              ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.SYMPHONY_INSTANCE_NO      , -- Symphonyインスタンス番号
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_DSC_RETRY_TIMEOUT       ,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER           
FROM C_DSC_EXE_INS_MNG_JNL   TAB_A
LEFT JOIN E_DSC_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_DSC_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_DSC_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
;

-- プルダウン VIEW
CREATE VIEW E_OPE_FOR_PULLDOWN_DSC
AS 
SELECT TAB_A.OPERATION_NO_UAPK    ,
       TAB_A.OPERATION_NAME       ,
       TAB_A.OPERATION_DATE       ,
       TAB_A.OPERATION_NO_IDBH    ,
       TAB_A.OPERATION            ,
       TAB_A.DISP_SEQ             ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER     ,
       TAB_B.PHO_LINK_ID          ,
       TAB_B.DISUSE_FLAG           DISUSE_FLAG_2
FROM 
    E_OPERATION_LIST TAB_A
    LEFT JOIN B_DSC_PHO_LINK TAB_B ON (TAB_A.OPERATION_NO_UAPK = TAB_B.OPERATION_NO_UAPK)
WHERE
    TAB_A.DISUSE_FLAG IN ('0') 
    AND
    TAB_B.PHO_LINK_ID IS NOT NULL 
    AND 
    TAB_B.DISUSE_FLAG IN ('0')
;

-- 代入値管理テーブル VIEW
CREATE VIEW D_DSC_VARS_ASSIGN AS
SELECT 
         TAB_A.ASSIGN_ID                 ,
         --
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.PATTERN_ID                ,
         TAB_A.SYSTEM_ID                 ,
         TAB_A.VARS_LINK_ID              ,
         TAB_B.VARS_NAME_ID              ,
         TAB_B.VARS_NAME                 ,
         TAB_A.VARS_ENTRY                ,
         TAB_A.ASSIGN_SEQ                ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM B_DSC_VARS_ASSIGN         TAB_A
LEFT JOIN D_DSC_PTN_VARS_LINK  TAB_B ON ( TAB_B.VARS_LINK_ID = TAB_A.VARS_LINK_ID )
;

-- -------------------------------------------------------
-- --V4-0006 DSC 代入値自動登録設定メニュー用　VIEW
-- -------------------------------------------------------
CREATE VIEW D_DSC_VAL_ASSIGN AS 
SELECT 
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COLUMN_LIST_ID AS MENU_COLUMN_LIST_ID, -- CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.VAL_CHILD_VARS_LINK_ID         , -- Value値　作業パターンメンバー変数紐付
       TAB_A.VAL_ASSIGN_SEQ                 , -- Value値　代入順序
       TAB_A.VAL_CHILD_VARS_COL_SEQ         , -- Value値　列順序
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.KEY_CHILD_VARS_LINK_ID         , -- Key値　作業パターンメンバー変数紐付
       TAB_A.KEY_ASSIGN_SEQ                 , -- Key値　代入順序
       TAB_A.KEY_CHILD_VARS_COL_SEQ         , -- Key値　列順序
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_B.MENU_NAME                      ,
       TAB_A.DISP_SEQ                       ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_DSC_VAL_ASSIGN TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

CREATE VIEW D_DSC_VAL_ASSIGN_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                 ,
       TAB_A.JOURNAL_REG_DATETIME           ,
       TAB_A.JOURNAL_ACTION_CLASS           ,
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COLUMN_LIST_ID AS MENU_COLUMN_LIST_ID, -- CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.VAL_CHILD_VARS_LINK_ID         , -- Value値　作業パターンメンバー変数紐付
       TAB_A.VAL_ASSIGN_SEQ                 , -- Value値　代入順序
       TAB_A.VAL_CHILD_VARS_COL_SEQ         , -- Value値　列順序
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.KEY_CHILD_VARS_LINK_ID         , -- Key値　作業パターンメンバー変数紐付
       TAB_A.KEY_ASSIGN_SEQ                 , -- Key値　代入順序
       TAB_A.KEY_CHILD_VARS_COL_SEQ         , -- Key値　列順序
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_B.MENU_NAME                      ,
       TAB_A.DISP_SEQ                       ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_DSC_VAL_ASSIGN_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

-- PowerShell設定ファイルテーブル VIEW
CREATE VIEW D_DSC_POWERSHELL_FILE AS 
SELECT  POWERSHELL_FILE_ID      ,
        POWERSHELL_NAME         ,
        POWERSHELL_FILE_ID || ':' || POWERSHELL_NAME POWERSHELL,
        POWERSHELL_FILE         ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM    B_DSC_POWERSHELL_FILE;

CREATE VIEW D_DSC_POWERSHELL_FILE_JNL AS 
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        POWERSHELL_FILE_ID      ,
        POWERSHELL_NAME         ,
        POWERSHELL_FILE_ID || ':' || POWERSHELL_NAME POWERSHELL,
        POWERSHELL_FILE         ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM B_DSC_POWERSHELL_FILE_JNL;

-- Param設定ファイルテーブル VIEW
CREATE VIEW D_DSC_PARAM_FILE AS 
SELECT  PARAM_FILE_ID           ,
        PARAM_NAME              ,
        PARAM_FILE_ID || ':' || PARAM_NAME PARAM,
        PARAM_FILE              ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM    B_DSC_PARAM_FILE;

CREATE VIEW D_DSC_PARAM_FILE_JNL AS 
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        PARAM_FILE_ID           ,
        PARAM_NAME              ,
        PARAM_FILE_ID || ':' || PARAM_NAME PARAM,
        PARAM_FILE              ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM B_DSC_PARAM_FILE_JNL;

-- Import設定ファイルテーブル VIEW
CREATE VIEW D_DSC_IMPORT_FILE AS 
SELECT  IMPORT_FILE_ID          ,
        IMPORT_NAME             ,
        IMPORT_FILE_ID || ':' || IMPORT_NAME IMPORT,
        IMPORT_FILE             ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM    B_DSC_IMPORT_FILE;

CREATE VIEW D_DSC_IMPORT_FILE_JNL AS 
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        IMPORT_FILE_ID          ,
        IMPORT_NAME             ,
        IMPORT_FILE_ID || ':' || IMPORT_NAME IMPORT,
        IMPORT_FILE             ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM B_DSC_IMPORT_FILE_JNL;

-- ConfigData設定ファイルテーブル VIEW
CREATE VIEW D_DSC_CONFIGDATA_FILE AS 
SELECT  CONFIGDATA_FILE_ID      ,
        CONFIGDATA_NAME         ,
        CONFIGDATA_FILE_ID || ':' || CONFIGDATA_NAME CONFIGDATA,
        CONFIGDATA_FILE         ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM    B_DSC_CONFIGDATA_FILE;

CREATE VIEW D_DSC_CONFIGDATA_FILE_JNL AS 
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        CONFIGDATA_FILE_ID      ,
        CONFIGDATA_NAME         ,
        CONFIGDATA_FILE_ID || ':' || CONFIGDATA_NAME CONFIGDATA,
        CONFIGDATA_FILE         ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM B_DSC_CONFIGDATA_FILE_JNL;

-- CompileOption設定ファイルテーブル VIEW
CREATE VIEW D_DSC_CMPOPTION_FILE AS 
SELECT  CMPOPTION_FILE_ID       ,
        CMPOPTION_NAME          ,
        CMPOPTION_FILE_ID || ':' || CMPOPTION_NAME CMPOPTION,
        CMPOPTION_FILE          ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM    B_DSC_CMPOPTION_FILE;

CREATE VIEW D_DSC_CMPOPTION_FILE_JNL AS 
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        CMPOPTION_FILE_ID       ,
        CMPOPTION_NAME          ,
        CMPOPTION_FILE_ID || ':' || CMPOPTION_NAME CMPOPTION,
        CMPOPTION_FILE          ,
        DISP_SEQ                ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM B_DSC_CMPOPTION_FILE_JNL;


-- *****************************************************************************
-- *** DSC Views *****                                                       ***
-- *****************************************************************************


INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_IF_INFO_RIC',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_IF_INFO_JSQ',2);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_STATUS_RIC',11);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_STATUS_JSQ',11);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_RUN_MODE_RIC',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_RUN_MODE_JSQ',3);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_RESOURCE_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_RESOURCE_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_PHO_LINK_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_PHO_LINK_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_PATTERN_LINK_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_PATTERN_LINK_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_VARS_MASTER_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_VARS_MASTER_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_PTN_VARS_LINK_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_PTN_VARS_LINK_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_VARS_ASSIGN_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_VARS_ASSIGN_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_DSC_EXE_INS_MNG_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('C_DSC_EXE_INS_MNG_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_VAL_ASSIGN_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_VAL_ASSIGN_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_POWERSHELL_FILE_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_POWERSHELL_FILE_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_PARAM_FILE_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_PARAM_FILE_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_IMPORT_FILE_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_IMPORT_FILE_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_CONFIGDATA_FILE_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_CONFIGDATA_FILE_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_CMPOPTION_FILE_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_CMPOPTION_FILE_JSQ',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_CREDENTIAL_RIC',1);

INSERT INTO A_SEQUENCE (NAME,VALUE) VALUES('B_DSC_CREDENTIAL_JSQ',1);


INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060001,'DSC','dsc.png',140,'DSC','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60001,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060001,'DSC','dsc.png',140,'DSC','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060001,2100060001,'インターフェース情報',NULL,NULL,NULL,1,0,1,1,20,'if_info_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60002,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060001,2100060001,'インターフェース情報',NULL,NULL,NULL,1,0,1,1,20,'if_info_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060002,2100060001,'Movement一覧',NULL,NULL,NULL,1,0,1,1,30,'pattern_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60003,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060002,2100060001,'Movement一覧',NULL,NULL,NULL,1,0,1,1,30,'pattern_list','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060003,2100060001,'コンフィグ素材集',NULL,NULL,NULL,1,0,1,1,40,'resource_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60004,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060003,2100060001,'コンフィグ素材集',NULL,NULL,NULL,1,0,1,1,40,'resource_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060004,2100060001,'Movement詳細',NULL,NULL,NULL,1,0,1,1,50,'pattern_resource_link_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60005,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060004,2100060001,'Movement詳細',NULL,NULL,NULL,1,0,1,1,50,'pattern_resource_link_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060007,2100060001,'作業対象ホスト',NULL,NULL,NULL,1,0,1,2,80,'pattern_host_op_link_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60008,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060007,2100060001,'作業対象ホスト',NULL,NULL,NULL,1,0,1,2,80,'pattern_host_op_link_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060008,2100060001,'代入値管理',NULL,NULL,NULL,1,0,1,2,90,'vars_assign_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60009,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060008,2100060001,'代入値管理',NULL,NULL,NULL,1,0,1,2,90,'vars_assign_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060009,2100060001,'作業実行',NULL,NULL,NULL,1,0,1,1,100,'register_execution','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60010,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060009,2100060001,'作業実行',NULL,NULL,NULL,1,0,1,1,100,'register_execution','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060010,2100060001,'作業状態確認',NULL,NULL,NULL,1,0,2,2,110,'monitor_execution','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60011,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060010,2100060001,'作業状態確認',NULL,NULL,NULL,1,0,2,2,110,'monitor_execution','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060011,2100060001,'作業管理',NULL,NULL,NULL,1,0,1,2,120,'execution_management','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60012,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060011,2100060001,'作業管理',NULL,NULL,NULL,1,0,1,2,120,'execution_management','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060015,2100060001,'代入値自動登録設定',NULL,NULL,NULL,1,0,1,2,75,'col_vars_assign_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60016,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060015,2100060001,'代入値自動登録設定',NULL,NULL,NULL,1,0,1,2,75,'col_vars_assign_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060016,2100060001,'PowerShell素材集',NULL,NULL,NULL,1,0,1,1,41,'powershell_file_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60017,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060016,2100060001,'PowerShell素材集',NULL,NULL,NULL,1,0,1,1,41,'powershell_file_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060017,2100060001,'Param素材集',NULL,NULL,NULL,1,0,1,1,42,'param_file_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60018,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060017,2100060001,'Param素材集',NULL,NULL,NULL,1,0,1,1,42,'param_file_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060018,2100060001,'Import素材集',NULL,NULL,NULL,1,0,1,1,43,'import_file_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60019,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060018,2100060001,'Import素材集',NULL,NULL,NULL,1,0,1,1,43,'import_file_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060019,2100060001,'コンフィグデータ素材集',NULL,NULL,NULL,1,0,1,1,44,'configdata_file_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60020,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060019,2100060001,'コンフィグデータ素材集',NULL,NULL,NULL,1,0,1,1,44,'configdata_file_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060020,2100060001,'コンパイルオプション素材集',NULL,NULL,NULL,1,0,1,1,45,'cmpoption_file_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60021,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060020,2100060001,'コンパイルオプション素材集',NULL,NULL,NULL,1,0,1,1,45,'cmpoption_file_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060021,2100060001,'資格情報管理',NULL,NULL,NULL,1,0,1,1,46,'credential_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60022,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060021,2100060001,'資格情報管理',NULL,NULL,NULL,1,0,1,1,46,'credential_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060022,2100060001,'変数名一覧',NULL,NULL,NULL,1,0,1,2,55,'vars_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60023,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060022,2100060001,'変数名一覧',NULL,NULL,NULL,1,0,1,2,55,'vars_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100060023,2100060001,'Movement変数紐付管理',NULL,NULL,NULL,1,0,1,2,56,'pattern_vars_link_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-60024,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100060023,2100060001,'Movement変数紐付管理',NULL,NULL,NULL,1,0,1,2,56,'pattern_vars_link_master','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100801,'d3c','5ebbc37e034d6874a2af59eb04beaa52','DSC状態確認プロシージャ','sample@xxx.bbb.ccc','DSC状態確認プロシージャ','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100801,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100801,'d3c','5ebbc37e034d6874a2af59eb04beaa52','DSC状態確認プロシージャ','sample@xxx.bbb.ccc','DSC状態確認プロシージャ','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100802,'d3e','5ebbc37e034d6874a2af59eb04beaa52','DSC作業実行プロシージャ','sample@xxx.bbb.ccc','DSC作業実行プロシージャ','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100802,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100802,'d3e','5ebbc37e034d6874a2af59eb04beaa52','DSC作業実行プロシージャ','sample@xxx.bbb.ccc','DSC作業実行プロシージャ','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100803,'d3a','5ebbc37e034d6874a2af59eb04beaa52','DSC変数更新プロシージャ','sample@xxx.bbb.ccc','DSC変数更新プロシージャ','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100803,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100803,'d3a','5ebbc37e034d6874a2af59eb04beaa52','DSC変数更新プロシージャ','sample@xxx.bbb.ccc','DSC変数更新プロシージャ','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100804,'d7b','5ebbc37e034d6874a2af59eb04beaa52','DSC作業履歴定期廃止プロシージャ','sample@xxx.bbb.ccc','DSC作業履歴定期廃止プロシージャ','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100804,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100804,'d7b','5ebbc37e034d6874a2af59eb04beaa52','DSC作業履歴定期廃止プロシージャ','sample@xxx.bbb.ccc','DSC作業履歴定期廃止プロシージャ','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100806,'d3f','5ebbc37e034d6874a2af59eb04beaa52','DSC代入値自動登録設定プロシージャ','sample@xxx.bbb.ccc','DSC代入値自動登録設定プロシージャ','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100806,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',-100806,'d3f','5ebbc37e034d6874a2af59eb04beaa52','DSC代入値自動登録設定プロシージャ','sample@xxx.bbb.ccc','DSC代入値自動登録設定プロシージャ','H',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090002,1,2100060001,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90002,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090002,1,2100060001,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090003,1,2100060002,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90003,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090003,1,2100060002,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090004,1,2100060003,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90004,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090004,1,2100060003,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090005,1,2100060004,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90005,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090005,1,2100060004,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090006,1,2100060007,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90006,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090006,1,2100060007,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090007,1,2100060008,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90007,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090007,1,2100060008,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090008,1,2100060009,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90008,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090008,1,2100060009,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090009,1,2100060010,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90009,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090009,1,2100060010,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090010,1,2100060011,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90010,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090010,1,2100060011,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090014,1,2100060015,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90014,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090014,1,2100060015,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090015,1,2100060016,1,'システム管理者','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90015,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090015,1,2100060016,1,'システム管理者','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090016,1,2100060017,1,'システム管理者','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90016,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090016,1,2100060017,1,'システム管理者','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090017,1,2100060018,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90017,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090017,1,2100060018,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090018,1,2100060019,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90018,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090018,1,2100060019,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090019,1,2100060020,1,'システム管理者','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90019,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090019,1,2100060020,1,'システム管理者','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090020,1,2100060021,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90020,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090020,1,2100060021,1,'システム管理者','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090021,1,2100060022,1,'システム管理者','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90021,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090021,1,2100060022,1,'システム管理者','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100090022,1,2100060023,1,'システム管理者','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-90022,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100090022,1,2100060023,1,'システム管理者','1',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000017,3600,7200,'B_DSC_PHO_LINK','PHO_LINK_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'作業対象ホスト(DSC)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000017,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000017,3600,7200,'B_DSC_PHO_LINK','PHO_LINK_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'作業対象ホスト(DSC)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000018,3600,7200,'B_DSC_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'代入値管理(DSC)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000018,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000018,3600,7200,'B_DSC_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'代入値管理(DSC)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000019,3600,7200,'C_DSC_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK','SELECT DSC_STORAGE_PATH_LNX AS PATH FROM B_DSC_IF_INFO WHERE DISUSE_FLAG="0"','uploadfiles/2100060011/FILE_INPUT/','uploadfiles/2100060011/FILE_RESULT/','/__data_relay_storage__/',NULL,'作業管理(DSC)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000019,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2100000019,3600,7200,'C_DSC_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK','SELECT DSC_STORAGE_PATH_LNX AS PATH FROM B_DSC_IF_INFO WHERE DISUSE_FLAG="0"','uploadfiles/2100060011/FILE_INPUT/','uploadfiles/2100060011/FILE_RESULT/','/__data_relay_storage__/',NULL,'作業管理(DSC)','0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_ITA_EXT_STM_MASTER (ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'DSC','dsc_driver/ns',8,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_ITA_EXT_STM_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',8,'DSC','dsc_driver/ns',8,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060001','3','2100000002','PATTERN_ID','2100060002','PATTERN_ID',NULL,NULL);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060002','4','2100060001','PATTERN_ID','2100060004','PATTERN_ID',NULL,NULL);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060003','4','2100060001','PATTERN_ID','2100060023','PATTERN_ID',NULL,NULL);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060004','5','2100060002','RESOURCE_MATTER_ID','2100060003','RESOURCE_MATTER_ID',NULL,NULL);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060005','5','2100060002','POWERSHELL_FILE_ID','2100060016','POWERSHELL_FILE_ID',NULL,NULL);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060006','5','2100060002','PARAM_FILE_ID','2100060017','PARAM_FILE_ID',NULL,NULL);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060007','5','2100060002','IMPORT_FILE_ID','2100060018','IMPORT_FILE_ID',NULL,NULL);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060008','5','2100060002','CONFIGDATA_FILE_ID','2100060019','CONFIGDATA_FILE_ID',NULL,NULL);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060009','5','2100060002','CMPOPTION_FILE_ID','2100060020','CMPOPTION_FILE_ID',NULL,NULL);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060010','5','2100060003','VARS_NAME_ID','2100060022','VARS_NAME_ID',NULL,NULL);

INSERT INTO B_SYMPHONY_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060011','6','2100060004','RESOURCE_MATTER_FILE','2100060021','CREDENTIAL_VARS_NAME',NULL,'selectDscCredential');


INSERT INTO B_OPERATION_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060001','2','2100000001','OPERATION_NO_UAPK','2100060007','OPERATION_NO_UAPK',NULL,NULL);

INSERT INTO B_OPERATION_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060002','2','2100000001','OPERATION_NO_UAPK','2100060008','OPERATION_NO_UAPK',NULL,NULL);

INSERT INTO B_OPERATION_EXPORT_LINK (ROW_ID,HIERARCHY,SRC_ROW_ID,SRC_ITEM,DEST_MENU_ID,DEST_ITEM,OTHER_CONDITION,SPECIAL_SELECT_FUNC) VALUES('2100060003','3','2100060001','SYSTEM_ID','2100000303','SYSTEM_ID',NULL,NULL);


INSERT INTO B_DSC_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'未実行',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'未実行',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'準備中',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'準備中',2,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'実行中',3,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',3,'実行中',3,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'実行中(遅延)',4,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',4,'実行中(遅延)',4,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'完了',5,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',5,'完了',5,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'完了(異常)',6,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',6,'完了(異常)',6,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'想定外エラー',7,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',7,'想定外エラー',7,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'緊急停止',8,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',8,'緊急停止',8,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'未実行(予約)',9,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',9,'未実行(予約)',9,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'予約取消',10,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',10,'予約取消',10,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_DSC_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'通常',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'通常',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'ドライラン',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',2,'ドライラン',1,NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);

INSERT INTO B_DSC_IF_INFO (DSC_IF_INFO_ID,DSC_STORAGE_PATH_LNX,DSC_STORAGE_PATH_DSC,SYMPHONY_STORAGE_PATH_DSC,DSC_PROTOCOL,DSC_HOSTNAME,DSC_PORT,DSC_ACCESS_KEY_ID,DSC_SECRET_ACCESS_KEY,DSC_REFRESH_INTERVAL,DSC_TAILLOG_LINES,DISP_SEQ,NULL_DATA_HANDLING_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/dsc_driver','c:\\exastro\\data_relay_storage\\dsc_driver','c:\\exastro\\data_relay_storage\\symphony','https','ホスト名(またはIPアドレス)を記載','443','AccessKeyId','H2IwpzI0DJAwMKAmF2I5','3000','1000',1,'2',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);
INSERT INTO B_DSC_IF_INFO_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,DSC_IF_INFO_ID,DSC_STORAGE_PATH_LNX,DSC_STORAGE_PATH_DSC,SYMPHONY_STORAGE_PATH_DSC,DSC_PROTOCOL,DSC_HOSTNAME,DSC_PORT,DSC_ACCESS_KEY_ID,DSC_SECRET_ACCESS_KEY,DSC_REFRESH_INTERVAL,DSC_TAILLOG_LINES,DISP_SEQ,NULL_DATA_HANDLING_FLG,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),'INSERT',1,'%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/dsc_driver','c:\\exastro\\data_relay_storage\\dsc_driver','c:\\exastro\\data_relay_storage\\symphony','https','ホスト名(またはIPアドレス)を記載','443','AccessKeyId','H2IwpzI0DJAwMKAmF2I5','3000','1000',1,'2',NULL,'0',TO_TIMESTAMP('2015/04/01 00:00:00.000000','YYYY/MM/DD/ HH24:MI:SS.FF6'),1);


COMMIT;

EXIT;
