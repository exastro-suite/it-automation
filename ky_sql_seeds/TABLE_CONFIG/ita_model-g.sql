-- *****************************************************************************
-- *** ***** DSC Common Tables                                               ***
-- *****************************************************************************
-- ----更新系テーブル作成----
-- ステータステーブル
CREATE TABLE B_DSC_STATUS
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
-- 更新系テーブル作成----

-- ----履歴系テーブル作成----
-- ステータステーブル(履歴)
CREATE TABLE B_DSC_STATUS_JNL
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
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- インターフェース情報
CREATE TABLE B_DSC_IF_INFO
(
DSC_IF_INFO_ID                    %INT%                            ,

DSC_STORAGE_PATH_LNX              %VARCHR%(256)                    , -- ITAデータストレージ
DSC_STORAGE_PATH_DSC              %VARCHR%(256)                    , -- DSCデータストレージ
SYMPHONY_STORAGE_PATH_DSC         %VARCHR%(256)                    , -- Symphonyインスタンスデータリレイストレージパス(DSC)
DSC_PROTOCOL                      %VARCHR%(8)                      , -- プロトコル
DSC_HOSTNAME                      %VARCHR%(128)                    , -- ホスト名称
DSC_PORT                          %INT%                            , -- ポート
DSC_ACCESS_KEY_ID                 %VARCHR%(64)                     , -- アクセスキー
DSC_SECRET_ACCESS_KEY             %VARCHR%(64)                     , -- パスワード
DSC_REFRESH_INTERVAL              %INT%                            , -- 状態監視周期
DSC_TAILLOG_LINES                 %INT%                            , -- 進行状態表示桁数

DISP_SEQ                          %INT%                            , -- 表示順序
NULL_DATA_HANDLING_FLG            %INT%                            , -- Null値の連携 1:有効　2:無効
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (DSC_IF_INFO_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- インターフェース情報(履歴)
CREATE TABLE B_DSC_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

DSC_IF_INFO_ID                    %INT%                            ,

DSC_STORAGE_PATH_LNX              %VARCHR%(256)                    , -- ITAデータストレージ
DSC_STORAGE_PATH_DSC              %VARCHR%(256)                    , -- DSCデータストレージ
SYMPHONY_STORAGE_PATH_DSC         %VARCHR%(256)                    , -- Symphonyインスタンスデータリレイストレージパス(DSC)
DSC_PROTOCOL                      %VARCHR%(8)                      , -- プロトコル
DSC_HOSTNAME                      %VARCHR%(128)                    , -- ホスト名称
DSC_PORT                          %INT%                            , -- ポート
DSC_ACCESS_KEY_ID                 %VARCHR%(64)                     , -- アクセスキー
DSC_SECRET_ACCESS_KEY             %VARCHR%(64)                     , -- パスワード
DSC_REFRESH_INTERVAL              %INT%                            , -- 状態監視周期
DSC_TAILLOG_LINES                 %INT%                            , -- 進行状態表示桁数

DISP_SEQ                          %INT%                            , -- 表示順序
NULL_DATA_HANDLING_FLG            %INT%                            , -- Null値の連携 1:有効　2:無効
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----
-- 実行モード情報
CREATE TABLE B_DSC_RUN_MODE
(
RUN_MODE_ID                       %INT%                            , -- 識別シーケンス

RUN_MODE_NAME                     %VARCHR%(32)                     , -- 動作モード名称

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (RUN_MODE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 実行モード情報(履歴)
CREATE TABLE B_DSC_RUN_MODE_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

RUN_MODE_ID                       %INT%                            , -- 識別シーケンス

RUN_MODE_NAME                     %VARCHR%(32)                     , -- 動作モード名称

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
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
RESOURCE_MATTER_ID                %INT%                            ,

RESOURCE_MATTER_NAME              %VARCHR%(32)                     , -- リソース名称
RESOURCE_MATTER_FILE              %VARCHR%(256)                    , -- リソースファイルパス

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (RESOURCE_MATTER_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- リソース素材テーブル(履歴)
CREATE TABLE B_DSC_RESOURCE_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

RESOURCE_MATTER_ID                %INT%                            ,

RESOURCE_MATTER_NAME              %VARCHR%(32)                     , -- リソース名称
RESOURCE_MATTER_FILE              %VARCHR%(256)                    , -- リソースファイルパス

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- 作業パターン詳細テーブル
CREATE TABLE B_DSC_PATTERN_LINK
(
LINK_ID                           %INT%                            ,

PATTERN_ID                        %INT%                            , -- パターンID
RESOURCE_MATTER_ID                %INT%                            , -- リソースID
INCLUDE_SEQ                       %INT%                            , -- INCLUDE順序

POWERSHELL_FILE_ID                %INT%                            , -- ファイルID(PowerShell)
PARAM_FILE_ID                     %INT%                            , -- ファイルID(Param)
IMPORT_FILE_ID                    %INT%                            , -- ファイルID(Import)
CONFIGDATA_FILE_ID                %INT%                            , -- ファイルID(ConfigData)
CMPOPTION_FILE_ID                 %INT%                            , -- ファイルID(CompileOption)


DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (LINK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 作業パターン詳細テーブル(履歴)
CREATE TABLE B_DSC_PATTERN_LINK_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

LINK_ID                           %INT%                            ,

PATTERN_ID                        %INT%                            , -- パターンID
RESOURCE_MATTER_ID                %INT%                            , -- リソースID
INCLUDE_SEQ                       %INT%                            , -- INCLUDE順序

POWERSHELL_FILE_ID                %INT%                            , -- ファイルID(PowerShell)
PARAM_FILE_ID                     %INT%                            , -- ファイルID(Param)
IMPORT_FILE_ID                    %INT%                            , -- ファイルID(Import)
CONFIGDATA_FILE_ID                %INT%                            , -- ファイルID(ConfigData)
CMPOPTION_FILE_ID                 %INT%                            , -- ファイルID(CompileOption)

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- 作業対象ホストテーブル
CREATE TABLE B_DSC_PHO_LINK
(
PHO_LINK_ID                       %INT%                            ,

OPERATION_NO_UAPK                 %INT%                            , -- オペレーションID
PATTERN_ID                        %INT%                            , -- パターンID
SYSTEM_ID                         %INT%                            , -- ホストID

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (PHO_LINK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 作業対象ホストテーブル(履歴)
CREATE TABLE B_DSC_PHO_LINK_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

PHO_LINK_ID                       %INT%                            ,

OPERATION_NO_UAPK                 %INT%                            , -- オペレーションID
PATTERN_ID                        %INT%                            , -- パターンID
SYSTEM_ID                         %INT%                            , -- ホストID

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- 代入値マスタテーブル
CREATE TABLE B_DSC_VARS_MASTER
(
VARS_NAME_ID                      %INT%                            ,

VARS_NAME                         %VARCHR%(128)                    , -- 代入値名称
VARS_DESCRIPTION                  %VARCHR%(128)                    , -- 代入値

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (VARS_NAME_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 代入値マスタテーブル(履歴)
CREATE TABLE B_DSC_VARS_MASTER_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

VARS_NAME_ID                      %INT%                            ,

VARS_NAME                         %VARCHR%(128)                    , -- 代入値名称
VARS_DESCRIPTION                  %VARCHR%(128)                    , -- 代入値

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- 代入値紐付テーブル
CREATE TABLE B_DSC_PTN_VARS_LINK
(
VARS_LINK_ID                      %INT%                            ,

PATTERN_ID                        %INT%                            , -- パターンID
VARS_NAME_ID                      %INT%                            , -- 代入値名称

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (VARS_LINK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 代入値紐付テーブル(履歴)
CREATE TABLE B_DSC_PTN_VARS_LINK_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

VARS_LINK_ID                      %INT%                            ,

PATTERN_ID                        %INT%                            , -- パターンID
VARS_NAME_ID                      %INT%                            , -- 代入値名称

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
-- 代入値管理テーブル
CREATE TABLE B_DSC_VARS_ASSIGN
(
ASSIGN_ID                         %INT%                            ,

OPERATION_NO_UAPK                 %INT%                            , -- オペレーションID
PATTERN_ID                        %INT%                            , -- パターンID
SYSTEM_ID                         %INT%                            , -- ホストID
VARS_LINK_ID                      %INT%                            , -- 代入値リンクID
VARS_ENTRY                        %VARCHR%(1024)                   ,
ASSIGN_SEQ                        %INT%                            ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (ASSIGN_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 代入値管理テーブル(履歴)
CREATE TABLE B_DSC_VARS_ASSIGN_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

ASSIGN_ID                         %INT%                            ,

OPERATION_NO_UAPK                 %INT%                            , -- オペレーションID
PATTERN_ID                        %INT%                            , -- パターンID
SYSTEM_ID                         %INT%                            , -- ホストID
VARS_LINK_ID                      %INT%                            , -- 代入値リンクID
VARS_ENTRY                        %VARCHR%(1024)                   ,
ASSIGN_SEQ                        %INT%                            ,

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----


-- -------------------------------------------------------
-- T-0001 作業インスタンス
-- -------------------------------------------------------
-- ----更新系テーブル作成
-- 実行管理テーブル
CREATE TABLE C_DSC_EXE_INS_MNG
(
EXECUTION_NO                      %INT%                            ,

STATUS_ID                         %INT%                            , -- 実行ステータスID
EXECUTION_USER                    %VARCHR%(80)                     , -- 実行ユーザ
SYMPHONY_NAME                     %VARCHR%(128)                    , -- シンフォニークラス名
SYMPHONY_INSTANCE_NO              %INT%                            , -- Symphonyインスタンス番号
PATTERN_ID                        %INT%                            , -- パターンID
I_PATTERN_NAME                    %VARCHR%(256)                    ,
I_TIME_LIMIT                      %INT%                            ,
I_ANS_HOST_DESIGNATE_TYPE_ID      %INT%                            ,
I_ANS_PARALLEL_EXE                %INT%                            ,
I_DSC_RETRY_TIMEOUT               %INT%                            , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add
OPERATION_NO_UAPK                 %INT%                            , -- オペレーションI
I_OPERATION_NAME                  %VARCHR%(128)                    ,
I_OPERATION_NO_IDBH               %INT%                            ,
TIME_BOOK                         %DATETIME6%                      ,
TIME_START                        %DATETIME6%                      , -- 開始時間
TIME_END                          %DATETIME6%                      , -- 終了時間
FILE_INPUT                        %VARCHR%(1024)                   , -- 入力パス
FILE_RESULT                       %VARCHR%(1024)                   , -- 出力パス
RUN_MODE                          %INT%                            , -- ドライランモード 1:通常 2:ドライラン

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (EXECUTION_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- 実行管理テーブル(履歴)
CREATE TABLE C_DSC_EXE_INS_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

EXECUTION_NO                      %INT%                            ,

STATUS_ID                         %INT%                            , -- 実行ステータスID
EXECUTION_USER                    %VARCHR%(80)                     , -- 実行ユーザ
SYMPHONY_NAME                     %VARCHR%(128)                    , -- シンフォニークラス名
SYMPHONY_INSTANCE_NO              %INT%                            , -- Symphonyインスタンス番号
PATTERN_ID                        %INT%                            , -- パターンID
I_PATTERN_NAME                    %VARCHR%(256)                    ,
I_TIME_LIMIT                      %INT%                            ,
I_ANS_HOST_DESIGNATE_TYPE_ID      %INT%                            ,
I_ANS_PARALLEL_EXE                %INT%                            ,
I_DSC_RETRY_TIMEOUT               %INT%                            , -- DSC利用情報 リトライタイムアウト 2018.05.11. Add
OPERATION_NO_UAPK                 %INT%                            , -- オペレーションI
I_OPERATION_NAME                  %VARCHR%(128)                    ,
I_OPERATION_NO_IDBH               %INT%                            ,
TIME_BOOK                         %DATETIME6%                      ,
TIME_START                        %DATETIME6%                      , -- 開始時間
TIME_END                          %DATETIME6%                      , -- 終了時間
FILE_INPUT                        %VARCHR%(1024)                   , -- 入力パス
FILE_RESULT                       %VARCHR%(1024)                   , -- 出力パス
RUN_MODE                          %INT%                            , -- ドライランモード 1:通常 2:ドライラン

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------------------------------------
-- --T4-0004 DSC 代入値自動登録設定
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_DSC_VAL_ASSIGN (
COLUMN_ID                      %INT%                   , -- 識別シーケンス
MENU_ID                        %INT%                   , -- メニューID
COLUMN_LIST_ID                 %INT%                   , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                       %INT%                   , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                     %INT%                   , -- 作業パターンID
VAL_VARS_LINK_ID               %INT%                   , -- Value値　作業パターン変数紐付
VAL_CHILD_VARS_LINK_ID         %INT%                   , -- Value値　作業パターンメンバー変数紐付
VAL_ASSIGN_SEQ                 %INT%                   , -- Value値　代入順序
VAL_CHILD_VARS_COL_SEQ         %INT%                   , -- Value値　列順序
KEY_VARS_LINK_ID               %INT%                   , -- Key値　作業パターン変数紐付
KEY_CHILD_VARS_LINK_ID         %INT%                   , -- Key値　作業パターンメンバー変数紐付
KEY_ASSIGN_SEQ                 %INT%                   , -- Key値　代入順序
KEY_CHILD_VARS_COL_SEQ         %INT%                   , -- Key値　列順序
NULL_DATA_HANDLING_FLG         %INT%                   , -- Null値の連携

DISP_SEQ                       %INT%                   , -- 表示順序
NOTE                           %VARCHR%(4000)          , -- 備考
DISUSE_FLAG                    %VARCHR%(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%             , -- 最終更新日時
LAST_UPDATE_USER               %INT%                   , -- 最終更新ユーザ
PRIMARY KEY(COLUMN_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_DSC_VAL_ASSIGN_JNL
(
JOURNAL_SEQ_NO                 %INT%                   , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           %DATETIME6%             , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           %VARCHR%(8)             , -- 履歴用変更種別

COLUMN_ID                      %INT%                   , -- 識別シーケンス
MENU_ID                        %INT%                   , -- メニューID
COLUMN_LIST_ID                 %INT%                   , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                       %INT%                   , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                     %INT%                   , -- 作業パターンID
VAL_VARS_LINK_ID               %INT%                   , -- Value値　作業パターン変数紐付
VAL_CHILD_VARS_LINK_ID         %INT%                   , -- Value値　作業パターンメンバー変数紐付
VAL_ASSIGN_SEQ                 %INT%                   , -- Value値　代入順序
VAL_CHILD_VARS_COL_SEQ         %INT%                   , -- Value値　列順序
KEY_VARS_LINK_ID               %INT%                   , -- Key値　作業パターン変数紐付
KEY_CHILD_VARS_LINK_ID         %INT%                   , -- Key値　作業パターンメンバー変数紐付
KEY_ASSIGN_SEQ                 %INT%                   , -- Key値　代入順序
KEY_CHILD_VARS_COL_SEQ         %INT%                   , -- Key値　列順序
NULL_DATA_HANDLING_FLG         %INT%                   , -- Null値の連携

DISP_SEQ                       %INT%                   , -- 表示順序
NOTE                           %VARCHR%(4000)          , -- 備考
DISUSE_FLAG                    %VARCHR%(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          %DATETIME6%             , -- 最終更新日時
LAST_UPDATE_USER               %INT%                   , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----PowerShell素材ファイル更新系テーブル作成
CREATE TABLE B_DSC_POWERSHELL_FILE
(
POWERSHELL_FILE_ID                %INT%                            , -- ファイルID
POWERSHELL_NAME                   %VARCHR%(128)                    , -- PowerShell素材名
POWERSHELL_FILE                   %VARCHR%(256)                    , -- PowerShell設定ファイル名

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (POWERSHELL_FILE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- PowerShell素材ファイル更新系テーブル作成----

-- ----PowerShell素材ファイル履歴系テーブル作成
CREATE TABLE B_DSC_POWERSHELL_FILE_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

POWERSHELL_FILE_ID                %INT%                            , -- ファイルID
POWERSHELL_NAME                   %VARCHR%(128)                    , -- PowerShell素材名
POWERSHELL_FILE                   %VARCHR%(256)                    , -- PowerShell設定ファイル名

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- PowerShell素材ファイル履歴系テーブル作成----

-- ----Param素材ファイル更新系テーブル作成
CREATE TABLE B_DSC_PARAM_FILE
(
PARAM_FILE_ID                     %INT%                            , -- ファイルID
PARAM_NAME                        %VARCHR%(128)                    , -- Param素材名
PARAM_FILE                        %VARCHR%(256)                    , -- Param設定ファイル名

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (PARAM_FILE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Param素材ファイル更新系テーブル作成----

-- ----Param素材ファイル履歴系テーブル作成
CREATE TABLE B_DSC_PARAM_FILE_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

PARAM_FILE_ID                     %INT%                            , -- ファイルID
PARAM_NAME                        %VARCHR%(128)                    , -- Param素材名
PARAM_FILE                        %VARCHR%(256)                    , -- Param設定ファイル名

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Param素材ファイル履歴系テーブル作成----

-- ----Import素材ファイル更新系テーブル作成
CREATE TABLE B_DSC_IMPORT_FILE
(
IMPORT_FILE_ID                    %INT%                            , -- ファイルID
IMPORT_NAME                       %VARCHR%(128)                    , -- Import素材名
IMPORT_FILE                       %VARCHR%(256)                    , -- Import設定ファイル名

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (IMPORT_FILE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Import 設定ファイル更新系テーブル作成----

-- ----Import素材ファイル履歴系テーブル作成
CREATE TABLE B_DSC_IMPORT_FILE_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

IMPORT_FILE_ID                    %INT%                            , -- ファイルID
IMPORT_NAME                       %VARCHR%(128)                    , -- Import素材名
IMPORT_FILE                       %VARCHR%(256)                    , -- Import設定ファイル名

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- Import設定ファイル履歴系テーブル作成----

-- ----ConfigData素材ファイル更新系テーブル作成
CREATE TABLE B_DSC_CONFIGDATA_FILE
(
CONFIGDATA_FILE_ID                %INT%                            , -- ファイルID
CONFIGDATA_NAME                   %VARCHR%(128)                    , -- ConfigData素材名
CONFIGDATA_FILE                   %VARCHR%(256)                    , -- ConfigData設定ファイル名

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (CONFIGDATA_FILE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- ConfigData素材ファイル更新系テーブル作成----

-- ----ConfigData素材ファイル履歴系テーブル作成
CREATE TABLE B_DSC_CONFIGDATA_FILE_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

CONFIGDATA_FILE_ID                %INT%                            , -- ファイルID
CONFIGDATA_NAME                   %VARCHR%(128)                    , -- ConfigData素材名
CONFIGDATA_FILE                   %VARCHR%(256)                    , -- ConfigData設定ファイル名

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- ConfigData素材ファイル履歴系テーブル作成----

-- ----CompileOption素材ファイル更新系テーブル作成
CREATE TABLE B_DSC_CMPOPTION_FILE
(
CMPOPTION_FILE_ID                 %INT%                            , -- ファイルID
CMPOPTION_NAME                    %VARCHR%(128)                    , -- CompileOption素材名
CMPOPTION_FILE                    %VARCHR%(256)                    , -- CompileOption設定ファイル名

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (CMPOPTION_FILE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- CompileOption素材ファイル更新系テーブル作成----

-- ----CompileOption素材ファイル履歴系テーブル作成
CREATE TABLE B_DSC_CMPOPTION_FILE_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

CMPOPTION_FILE_ID                 %INT%                            , -- ファイルID
CMPOPTION_NAME                    %VARCHR%(128)                    , -- CompileOption素材名
CMPOPTION_FILE                    %VARCHR%(256)                    , -- CompileOption設定ファイル名

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- CompileOption素材ファイル履歴系テーブル作成----

-- ----資格情報更新系テーブル作成
CREATE TABLE B_DSC_CREDENTIAL
(
CREDENTIAL_ID                     %INT%                            , -- 資格ID
CREDENTIAL_VARS_NAME              %VARCHR%(128)                    , -- 資格情報埋込変数名
SYSTEM_ID                         %INT%                            , -- ホストID
CREDENTIAL_USER                   %VARCHR%(60)                     , -- アカウント
CREDENTIAL_PW                     %VARCHR%(60)                     , -- パスワード

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (CREDENTIAL_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 資格情報更新系テーブル作成----

-- ----資格情報履歴系テーブル作成
CREATE TABLE B_DSC_CREDENTIAL_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別

CREDENTIAL_ID                     %INT%                            , -- 資格ID
CREDENTIAL_VARS_NAME              %VARCHR%(128)                    , -- 資格情報埋込変数名
SYSTEM_ID                         %INT%                            , -- ホストID
CREDENTIAL_USER                   %VARCHR%(60)                     , -- アカウント
CREDENTIAL_PW                     %VARCHR%(60)                     , -- パスワード

DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
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
        [%CONCAT_HEAD/%]RESOURCE_MATTER_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]RESOURCE_MATTER_NAME[%CONCAT_TAIL/%] RESOURCE,
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
        [%CONCAT_HEAD/%]RESOURCE_MATTER_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]RESOURCE_MATTER_NAME[%CONCAT_TAIL/%] RESOURCE,
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
        [%CONCAT_HEAD/%]PATTERN_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]PATTERN_NAME[%CONCAT_TAIL/%] PATTERN,
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
        [%CONCAT_HEAD/%]PATTERN_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]PATTERN_NAME[%CONCAT_TAIL/%] PATTERN,
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
        [%CONCAT_HEAD/%]TAB_A.VARS_LINK_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_C.VARS_NAME[%CONCAT_TAIL/%] VARS_LINK_PULLDOWN,
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
        [%CONCAT_HEAD/%]TAB_A.VARS_LINK_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_C.VARS_NAME[%CONCAT_TAIL/%] VARS_LINK_PULLDOWN,
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
        [%CONCAT_HEAD/%]TAB_A.VARS_LINK_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_C.VARS_NAME[%CONCAT_TAIL/%] VARS_LINK_PULLDOWN,
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
        [%CONCAT_HEAD/%]POWERSHELL_FILE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]POWERSHELL_NAME[%CONCAT_TAIL/%] POWERSHELL,
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
        [%CONCAT_HEAD/%]POWERSHELL_FILE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]POWERSHELL_NAME[%CONCAT_TAIL/%] POWERSHELL,
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
        [%CONCAT_HEAD/%]PARAM_FILE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]PARAM_NAME[%CONCAT_TAIL/%] PARAM,
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
        [%CONCAT_HEAD/%]PARAM_FILE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]PARAM_NAME[%CONCAT_TAIL/%] PARAM,
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
        [%CONCAT_HEAD/%]IMPORT_FILE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]IMPORT_NAME[%CONCAT_TAIL/%] IMPORT,
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
        [%CONCAT_HEAD/%]IMPORT_FILE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]IMPORT_NAME[%CONCAT_TAIL/%] IMPORT,
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
        [%CONCAT_HEAD/%]CONFIGDATA_FILE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]CONFIGDATA_NAME[%CONCAT_TAIL/%] CONFIGDATA,
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
        [%CONCAT_HEAD/%]CONFIGDATA_FILE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]CONFIGDATA_NAME[%CONCAT_TAIL/%] CONFIGDATA,
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
        [%CONCAT_HEAD/%]CMPOPTION_FILE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]CMPOPTION_NAME[%CONCAT_TAIL/%] CMPOPTION,
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
        [%CONCAT_HEAD/%]CMPOPTION_FILE_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]CMPOPTION_NAME[%CONCAT_TAIL/%] CMPOPTION,
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


