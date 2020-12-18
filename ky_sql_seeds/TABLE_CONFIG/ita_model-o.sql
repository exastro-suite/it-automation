-- *****************************************************************************
-- *** ***** Terraform Common Tables                                         ***
-- *****************************************************************************
-- ----更新系テーブル作成
-- インタフェース情報
CREATE TABLE B_TERRAFORM_IF_INFO
(
TERRAFORM_IF_INFO_ID              %INT%                            ,
TERRAFORM_HOSTNAME                %VARCHR%(256)                    ,
TERRAFORM_TOKEN                   %VARCHR%(512)                    ,
TERRAFORM_REFRESH_INTERVAL        %INT%                            ,
TERRAFORM_TAILLOG_LINES           %INT%                            ,
NULL_DATA_HANDLING_FLG            %INT%                            , -- Null値の連携 1:有効　2:無効
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (TERRAFORM_IF_INFO_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
-- インタフェース情報(履歴)
CREATE TABLE B_TERRAFORM_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
TERRAFORM_IF_INFO_ID              %INT%                            ,
TERRAFORM_HOSTNAME                %VARCHR%(256)                    ,
TERRAFORM_TOKEN                   %VARCHR%(512)                    ,
TERRAFORM_REFRESH_INTERVAL        %INT%                            ,
TERRAFORM_TAILLOG_LINES           %INT%                            ,
NULL_DATA_HANDLING_FLG            %INT%                            , -- Null値の連携 1:有効　2:無効
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成----
--ステータス
CREATE TABLE B_TERRAFORM_STATUS
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
--ステータス(履歴)
CREATE TABLE B_TERRAFORM_STATUS_JNL
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

-- ----更新系テーブル作成----
-- 実行モード情報
CREATE TABLE B_TERRAFORM_RUN_MODE
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
-- 更新系テーブル作成----

-- ----履歴系テーブル作成----
-- 実行モード情報(履歴)
CREATE TABLE B_TERRAFORM_RUN_MODE_JNL
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
-- 履歴系テーブル作成----

-- ----更新系テーブル作成----
-- HCLフラグ
CREATE TABLE B_TERRAFORM_HCL_FLAG
(
HCL_FLAG                          %INT%                            ,
HCL_FLAG_SELECT                   %VARCHR%(32)                     ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (HCL_FLAG)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成----
-- HCLフラグ(履歴)
CREATE TABLE B_TERRAFORM_HCL_FLAG_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
HCL_FLAG                          %INT%                            ,
HCL_FLAG_SELECT                   %VARCHR%(32)                     ,
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
-- *** ***** Terraform Common Tables                                         ***
-- *****************************************************************************

-- *****************************************************************************
-- *** ***** Terraform Tables                                                ***
-- *****************************************************************************
-- ----更新系テーブル作成
--Organizations管理
CREATE TABLE B_TERRAFORM_ORGANIZATIONS
(
ORGANIZATION_ID                   %INT%                            ,
ORGANIZATION_NAME                 %VARCHR%(40)                     ,
EMAIL_ADDRESS                     %VARCHR%(128)                    ,
CHECK_RESULT                      %VARCHR%(8)                      ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (ORGANIZATION_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--Organizations管理(履歴)
CREATE TABLE B_TERRAFORM_ORGANIZATIONS_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
ORGANIZATION_ID                   %INT%                            ,
ORGANIZATION_NAME                 %VARCHR%(40)                     ,
EMAIL_ADDRESS                     %VARCHR%(128)                    ,
CHECK_RESULT                      %VARCHR%(8)                      ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
--Workspaces管理
CREATE TABLE B_TERRAFORM_WORKSPACES
(
WORKSPACE_ID                      %INT%                            ,
ORGANIZATION_ID                   %INT%                            ,
WORKSPACE_NAME                    %VARCHR%(90)                     ,
TERRAFORM_VERSION                 %VARCHR%(32)                     ,
CHECK_RESULT                      %VARCHR%(8)                      ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (WORKSPACE_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--Workspaces管理(履歴)
CREATE TABLE B_TERRAFORM_WORKSPACES_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
WORKSPACE_ID                      %INT%                            ,
ORGANIZATION_ID                   %INT%                            ,
WORKSPACE_NAME                    %VARCHR%(90)                     ,
TERRAFORM_VERSION                 %VARCHR%(32)                     ,
CHECK_RESULT                      %VARCHR%(8)                      ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
--Module素材
CREATE TABLE B_TERRAFORM_MODULE
(
MODULE_MATTER_ID                  %INT%                            ,
MODULE_MATTER_NAME                %VARCHR%(256)                    ,
MODULE_MATTER_FILE                %VARCHR%(256)                    ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (MODULE_MATTER_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--Module素材(履歴)
CREATE TABLE B_TERRAFORM_MODULE_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
MODULE_MATTER_ID                  %INT%                            ,
MODULE_MATTER_NAME                %VARCHR%(256)                    ,
MODULE_MATTER_FILE                %VARCHR%(256)                    ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
--Policy管理
CREATE TABLE B_TERRAFORM_POLICY
(
POLICY_ID                         %INT%                            ,   
POLICY_NAME                       %VARCHR%(256)                    , 
POLICY_MATTER_FILE                %VARCHR%(256)                    ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (POLICY_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--Policy管理(履歴)
CREATE TABLE B_TERRAFORM_POLICY_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
POLICY_ID                         %INT%                            ,
POLICY_NAME                       %VARCHR%(256)                    , 
POLICY_MATTER_FILE                %VARCHR%(256)                    ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


-- ----更新系テーブル作成
--PolicySet管理
CREATE TABLE B_TERRAFORM_POLICY_SETS
(
POLICY_SET_ID                     %INT%                            ,
POLICY_SET_NAME                   %VARCHR%(256)                    ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (POLICY_SET_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--PolicySet管理(履歴)
CREATE TABLE B_TERRAFORM_POLICY_SETS_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
POLICY_SET_ID                     %INT%                            ,
POLICY_SET_NAME                   %VARCHR%(256)                    ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- ----更新系テーブル作成
--PolicySet-Policy紐付管理
CREATE TABLE B_TERRAFORM_POLICYSET_POLICY_LINK
(
POLICYSET_POLICY_LINK_ID          %INT%                            ,
POLICY_SET_ID                     %INT%                            ,
POLICY_ID                         %INT%                            ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (POLICYSET_POLICY_LINK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--PolicySet-Policy紐付管理(履歴)
CREATE TABLE B_TERRAFORM_POLICYSET_POLICY_LINK_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
POLICYSET_POLICY_LINK_ID          %INT%                            ,
POLICY_SET_ID                     %INT%                            ,
POLICY_ID                         %INT%                            ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- ----更新系テーブル作成
--PolicySet-Workspace紐付管理
CREATE TABLE B_TERRAFORM_POLICYSET_WORKSPACE_LINK
(
POLICYSET_WORKSPACE_LINK_ID       %INT%                            ,
POLICY_SET_ID                     %INT%                            ,
WORKSPACE_ID                      %INT%                            ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (POLICYSET_WORKSPACE_LINK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--PolicySet-Workspace紐付管理(履歴)
CREATE TABLE B_TERRAFORM_POLICYSET_WORKSPACE_LINK_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
POLICYSET_WORKSPACE_LINK_ID       %INT%                            ,
POLICY_SET_ID                     %INT%                            ,
WORKSPACE_ID                      %INT%                            ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;


-- ----更新系テーブル作成
--作業パターン詳細
CREATE TABLE B_TERRAFORM_PATTERN_LINK
(
LINK_ID                           %INT%                            ,
PATTERN_ID                        %INT%                            ,
MODULE_MATTER_ID                  %INT%                            ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (LINK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--作業パターン詳細(履歴)
CREATE TABLE B_TERRAFORM_PATTERN_LINK_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
LINK_ID                           %INT%                            ,
PATTERN_ID                        %INT%                            ,
MODULE_MATTER_ID                  %INT%                            ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- ----更新系テーブル作成
--実行管理
CREATE TABLE C_TERRAFORM_EXE_INS_MNG
(
EXECUTION_NO                      %INT%                            ,
EXECUTION_USER                    %VARCHR%(80)                     ,
SYMPHONY_NAME                     %VARCHR%(256)                    ,
STATUS_ID                         %INT%                            ,
SYMPHONY_INSTANCE_NO              %INT%                            ,
PATTERN_ID                        %INT%                            ,
I_PATTERN_NAME                    %VARCHR%(256)                    ,
I_TIME_LIMIT                      %INT%                            ,
I_TERRAFORM_RUN_ID                %VARCHR%(32)                     ,
I_TERRAFORM_WORKSPACE_ID          %INT%                            ,
OPERATION_NO_UAPK                 %INT%                            ,
I_OPERATION_NAME                  %VARCHR%(256)                    ,
I_OPERATION_NO_IDBH               %INT%                            ,
CONDUCTOR_NAME                    %VARCHR%(256)                    , -- コンダクタ名
CONDUCTOR_INSTANCE_NO             %INT%                            , -- コンダクタ インスタンスID
TIME_BOOK                         %DATETIME6%                      ,
TIME_START                        %DATETIME6%                      ,
TIME_END                          %DATETIME6%                      ,
FILE_INPUT                        %VARCHR%(1024)                   ,
FILE_RESULT                       %VARCHR%(1024)                   ,
RUN_MODE                          %INT%                            ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (EXECUTION_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--実行管理(履歴)
CREATE TABLE C_TERRAFORM_EXE_INS_MNG_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
EXECUTION_NO                      %INT%                            ,
EXECUTION_USER                    %VARCHR%(80)                     ,
SYMPHONY_NAME                     %VARCHR%(256)                    ,
STATUS_ID                         %INT%                            ,
SYMPHONY_INSTANCE_NO              %INT%                            ,
PATTERN_ID                        %INT%                            ,
I_PATTERN_NAME                    %VARCHR%(256)                    ,
I_TIME_LIMIT                      %INT%                            ,
I_TERRAFORM_RUN_ID                %VARCHR%(32)                     ,
I_TERRAFORM_WORKSPACE_ID          %INT%                            ,
OPERATION_NO_UAPK                 %INT%                            ,
I_OPERATION_NAME                  %VARCHR%(256)                    ,
I_OPERATION_NO_IDBH               %INT%                            ,
CONDUCTOR_NAME                    %VARCHR%(256)                    , -- コンダクタ名
CONDUCTOR_INSTANCE_NO             %INT%                            , -- コンダクタ インスタンスID
TIME_BOOK                         %DATETIME6%                      ,
TIME_START                        %DATETIME6%                      ,
TIME_END                          %DATETIME6%                      ,
FILE_INPUT                        %VARCHR%(1024)                   ,
FILE_RESULT                       %VARCHR%(1024)                   ,
RUN_MODE                          %INT%                            ,
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
--代入値管理
CREATE TABLE B_TERRAFORM_VARS_ASSIGN
(
ASSIGN_ID                         %INT%                            ,
OPERATION_NO_UAPK                 %INT%                            , -- オペレーションID
PATTERN_ID                        %INT%                            , -- パターンID
MODULE_VARS_LINK_ID               %INT%                            , -- 代入値リンクID
VARS_ENTRY                        text                             ,
HCL_FLAG                          %VARCHR%(1)                      , -- HCL設定
SENSITIVE_FLAG                    %VARCHR%(1)                      , -- Sensitive設定
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (ASSIGN_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
----代入値管理(履歴)
CREATE TABLE B_TERRAFORM_VARS_ASSIGN_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
ASSIGN_ID                         %INT%                            ,
OPERATION_NO_UAPK                 %INT%                            , -- オペレーションID
PATTERN_ID                        %INT%                            , -- パターンID
MODULE_VARS_LINK_ID               %INT%                            , -- 代入値リンクID
VARS_ENTRY                        text                             ,
HCL_FLAG                          %VARCHR%(1)                      , -- HCL設定
SENSITIVE_FLAG                    %VARCHR%(1)                      , -- Sensitive設定
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
--代入値自動登録設定
CREATE TABLE B_TERRAFORM_VAL_ASSIGN (
COLUMN_ID                         %INT%                   , -- 識別シーケンス
MENU_ID                           %INT%                   , -- メニューID
COLUMN_LIST_ID                    %INT%                   , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                          %INT%                   , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                        %INT%                   , -- 作業パターンID
VAL_VARS_LINK_ID                  %INT%                   , -- Value値　作業パターン変数紐付
KEY_VARS_LINK_ID                  %INT%                   , -- Key値　作業パターン変数紐付
HCL_FLAG                          %VARCHR%(1)             , -- HCL設定
NULL_DATA_HANDLING_FLG            %INT%                   , -- Null値の連携
DISP_SEQ                          %INT%                   , -- 表示順序
ACCESS_AUTH                       TEXT                    ,
NOTE                              %VARCHR%(4000)          , -- 備考
DISUSE_FLAG                       %VARCHR%(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%             , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                   , -- 最終更新ユーザ
PRIMARY KEY(COLUMN_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--代入値自動登録設定(履歴)
CREATE TABLE B_TERRAFORM_VAL_ASSIGN_JNL
(
JOURNAL_SEQ_NO                    %INT%                   , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%             , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)             , -- 履歴用変更種別
COLUMN_ID                         %INT%                   , -- 識別シーケンス
MENU_ID                           %INT%                   , -- メニューID
COLUMN_LIST_ID                    %INT%                   , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                          %INT%                   , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                        %INT%                   , -- 作業パターンID
VAL_VARS_LINK_ID                  %INT%                   , -- Value値　作業パターン変数紐付
KEY_VARS_LINK_ID                  %INT%                   , -- Key値　作業パターン変数紐付
HCL_FLAG                          %VARCHR%(1)             , -- HCL設定
NULL_DATA_HANDLING_FLG            %INT%                   , -- Null値の連携
DISP_SEQ                          %INT%                   , -- 表示順序
ACCESS_AUTH                       TEXT                    ,
NOTE                              %VARCHR%(4000)          , -- 備考
DISUSE_FLAG                       %VARCHR%(1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%             , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                   , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
--Module変数紐付管理
CREATE TABLE B_TERRAFORM_MODULE_VARS_LINK
(
MODULE_VARS_LINK_ID               %INT%                            ,
MODULE_MATTER_ID                  %INT%                            ,
VARS_NAME                         %VARCHR%(256)                    ,
VARS_DESCRIPTION                  %VARCHR%(256)                    ,
DISP_SEQ                          %INT%                            , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (MODULE_VARS_LINK_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
--Module変数紐付管理(履歴)
CREATE TABLE B_TERRAFORM_MODULE_VARS_LINK_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
MODULE_VARS_LINK_ID               %INT%                            ,
MODULE_MATTER_ID                  %INT%                            ,
VARS_NAME                         %VARCHR%(256)                    ,
VARS_DESCRIPTION                  %VARCHR%(256)                    ,
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
-- *** Terraform Tables *****                                                ***
-- *****************************************************************************



-- *****************************************************************************
-- *** ***** Terraform Views                                                 ***
-- *****************************************************************************
-- インターフェース情報 VIEW
CREATE VIEW D_TERRAFORM_IF_INFO AS 
SELECT * 
FROM B_TERRAFORM_IF_INFO;

CREATE VIEW D_TERRAFORM_IF_INFO_JNL AS 
SELECT * 
FROM B_TERRAFORM_IF_INFO_JNL;

-- ステータス VIEW
CREATE VIEW D_TERRAFORM_INS_STATUS AS 
SELECT * 
FROM B_TERRAFORM_STATUS;

CREATE VIEW D_TERRAFORM_INS_STATUS_JNL AS 
SELECT * 
FROM B_TERRAFORM_STATUS_JNL;

--実行モード情報 VIEW
CREATE VIEW D_TERRAFORM_INS_RUN_MODE AS 
SELECT * 
FROM B_TERRAFORM_RUN_MODE;

CREATE VIEW D_TERRAFORM_INS_RUN_MODE_JNL AS 
SELECT * 
FROM B_TERRAFORM_RUN_MODE_JNL;

--Organizations-Workspaces紐付 VIEW
CREATE VIEW D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK AS
SELECT
        TAB_B.ORGANIZATION_ID         ,
        TAB_B.ORGANIZATION_NAME ORGANIZATION_NAME       ,
        TAB_A.WORKSPACE_ID            ,
        TAB_A.WORKSPACE_NAME WORKSPACE_NAME          ,
        [%CONCAT_HEAD/%]ORGANIZATION_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]WORKSPACE_NAME[%CONCAT_TAIL/%] ORGANIZATION_WORKSPACE,
        TAB_A.DISP_SEQ             ,
        TAB_A.ACCESS_AUTH          ,
        TAB_A.NOTE                 ,
        TAB_A.DISUSE_FLAG          ,
        TAB_A.LAST_UPDATE_TIMESTAMP,
        TAB_A.LAST_UPDATE_USER     ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM B_TERRAFORM_WORKSPACES TAB_A
LEFT JOIN B_TERRAFORM_ORGANIZATIONS TAB_B ON ( TAB_A.ORGANIZATION_ID = TAB_B.ORGANIZATION_ID )
;
CREATE VIEW D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK_JNL AS
SELECT 
        TAB_A.JOURNAL_SEQ_NO                ,
        TAB_A.JOURNAL_REG_DATETIME          ,
        TAB_A.JOURNAL_ACTION_CLASS          ,
        TAB_B.ORGANIZATION_ID         ,
        TAB_B.ORGANIZATION_NAME ORGANIZATION_NAME       ,
        TAB_A.WORKSPACE_ID            ,
        TAB_A.WORKSPACE_NAME WORKSPACE_NAME          ,
        [%CONCAT_HEAD/%]ORGANIZATION_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]WORKSPACE_NAME[%CONCAT_TAIL/%] ORGANIZATION_WORKSPACE,
        TAB_A.DISP_SEQ             ,
        TAB_A.ACCESS_AUTH          ,
        TAB_A.NOTE                 ,
        TAB_A.DISUSE_FLAG          ,
        TAB_A.LAST_UPDATE_TIMESTAMP,
        TAB_A.LAST_UPDATE_USER     ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM B_TERRAFORM_WORKSPACES_JNL TAB_A
LEFT JOIN B_TERRAFORM_ORGANIZATIONS_JNL TAB_B ON ( TAB_A.ORGANIZATION_ID = TAB_B.ORGANIZATION_ID )
;

--作業パターン詳細 VIEW
CREATE VIEW E_TERRAFORM_PATTERN AS 
SELECT 
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        [%CONCAT_HEAD/%]PATTERN_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]PATTERN_NAME[%CONCAT_TAIL/%] PATTERN,
        ITA_EXT_STM_ID                ,
        TERRAFORM_WORKSPACE_ID        ,
        TIME_LIMIT                    ,
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 10;

CREATE VIEW E_TERRAFORM_PATTERN_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                ,
        JOURNAL_REG_DATETIME          ,
        JOURNAL_ACTION_CLASS          ,
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        [%CONCAT_HEAD/%]PATTERN_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]PATTERN_NAME[%CONCAT_TAIL/%] PATTERN,
        ITA_EXT_STM_ID                ,
        TERRAFORM_WORKSPACE_ID        ,
        TIME_LIMIT                    ,
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH_JNL TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 10;


--作業管理 VIEW
CREATE VIEW E_TERRAFORM_EXE_INS_MNG AS
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
         TAB_A.I_TERRAFORM_RUN_ID        ,
         TAB_A.I_TERRAFORM_WORKSPACE_ID  ,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM C_TERRAFORM_EXE_INS_MNG       TAB_A
LEFT JOIN E_TERRAFORM_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_TERRAFORM_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_TERRAFORM_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
;

CREATE VIEW E_TERRAFORM_EXE_INS_MNG_JNL AS 
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
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_TERRAFORM_RUN_ID        ,
         TAB_A.I_TERRAFORM_WORKSPACE_ID  ,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER           
FROM C_TERRAFORM_EXE_INS_MNG_JNL   TAB_A
LEFT JOIN E_TERRAFORM_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_TERRAFORM_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_TERRAFORM_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
;

--代入値管理 VIEW
CREATE VIEW D_TERRAFORM_VARS_ASSIGN AS
SELECT
  TAB_A.*,
  TAB_A.MODULE_VARS_LINK_ID            REST_MODULE_VARS_LINK_ID
FROM
  B_TERRAFORM_VARS_ASSIGN TAB_A
;

CREATE VIEW D_TERRAFORM_VARS_ASSIGN_JNL AS
SELECT
  TAB_A.*,
  TAB_A.MODULE_VARS_LINK_ID            REST_MODULE_VARS_LINK_ID
FROM
  B_TERRAFORM_VARS_ASSIGN_JNL TAB_A
;

--代入値自動登録設定メニュー用　VIEW
CREATE VIEW D_TERRAFORM_VAL_ASSIGN AS 
SELECT 
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.HCL_FLAG                       , -- HCL設定
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_B.MENU_NAME                      ,
       TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.VAL_VARS_LINK_ID  REST_VAL_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.KEY_VARS_LINK_ID  REST_KEY_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_TERRAFORM_VAL_ASSIGN TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

CREATE VIEW D_TERRAFORM_VAL_ASSIGN_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                 ,
       TAB_A.JOURNAL_REG_DATETIME           ,
       TAB_A.JOURNAL_ACTION_CLASS           ,
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.HCL_FLAG                       , -- HCL設定
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_B.MENU_NAME                      ,
       TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.VAL_VARS_LINK_ID  REST_VAL_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.KEY_VARS_LINK_ID  REST_KEY_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_TERRAFORM_VAL_ASSIGN_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

--Module変数紐付管理 VIEW
CREATE VIEW D_TERRAFORM_PTN_VARS_LINK AS 
SELECT 
        TAB_A.MODULE_VARS_LINK_ID           ,
        TAB_B.PATTERN_ID                    ,
        TAB_C.PATTERN_NAME                  ,
        TAB_A.VARS_NAME                     ,
        [%CONCAT_HEAD/%]TAB_A.MODULE_VARS_LINK_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.VARS_NAME[%CONCAT_TAIL/%] VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_TERRAFORM_MODULE_VARS_LINK     TAB_A
LEFT JOIN B_TERRAFORM_PATTERN_LINK  TAB_B ON ( TAB_A.MODULE_MATTER_ID = TAB_B.MODULE_MATTER_ID )
LEFT JOIN E_TERRAFORM_PATTERN       TAB_C ON ( TAB_B.PATTERN_ID = TAB_C.PATTERN_ID )
;

CREATE VIEW D_TERRAFORM_PTN_VARS_LINK_JNL AS 
SELECT
        JOURNAL_SEQ_NO                      ,
        JOURNAL_REG_DATETIME                ,
        JOURNAL_ACTION_CLASS                ,
        TAB_A.MODULE_VARS_LINK_ID           ,
        TAB_B.PATTERN_ID                    ,
        TAB_C.PATTERN_NAME                  ,
        TAB_A.VARS_NAME                     ,
        [%CONCAT_HEAD/%]TAB_A.MODULE_VARS_LINK_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.VARS_NAME[%CONCAT_TAIL/%] VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_TERRAFORM_MODULE_VARS_LINK_JNL     TAB_A
LEFT JOIN B_TERRAFORM_PATTERN_LINK  TAB_B ON ( TAB_A.MODULE_MATTER_ID = TAB_B.MODULE_MATTER_ID )
LEFT JOIN E_TERRAFORM_PATTERN       TAB_C ON ( TAB_B.PATTERN_ID = TAB_C.PATTERN_ID )
;

--Module変数紐付プルダウン用 VIEW
CREATE VIEW D_TERRAFORM_PTN_VARS_LINK_VFP AS 
SELECT 
        TAB_A.MODULE_VARS_LINK_ID      ,
        TAB_B.PATTERN_ID              ,
        TAB_C.PATTERN_NAME            ,
        TAB_A.VARS_NAME               ,
        [%CONCAT_HEAD/%]TAB_A.MODULE_VARS_LINK_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.VARS_NAME[%CONCAT_TAIL/%] VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.ACCESS_AUTH             ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER        ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_TERRAFORM_MODULE_VARS_LINK     TAB_A
LEFT JOIN B_TERRAFORM_PATTERN_LINK  TAB_B ON ( TAB_A.MODULE_MATTER_ID = TAB_B.MODULE_MATTER_ID )
LEFT JOIN E_TERRAFORM_PATTERN       TAB_C ON ( TAB_B.PATTERN_ID = TAB_C.PATTERN_ID )
WHERE TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;

--変数データ紐付(backyard処理用) VIEW
CREATE VIEW D_TERRAFORM_VARS_DATA AS
SELECT 
         TAB_A.ASSIGN_ID                 ,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.PATTERN_ID                ,
         TAB_A.MODULE_VARS_LINK_ID       ,
         TAB_B.VARS_NAME                 ,
         TAB_A.VARS_ENTRY                ,
         TAB_A.HCL_FLAG                  ,
         TAB_A.SENSITIVE_FLAG            ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM B_TERRAFORM_VARS_ASSIGN         TAB_A
LEFT JOIN D_TERRAFORM_PTN_VARS_LINK  TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID AND TAB_B.MODULE_VARS_LINK_ID = TAB_A.MODULE_VARS_LINK_ID )
;


-- Operationプルダウン VIEW
CREATE VIEW E_OPE_FOR_PULLDOWN_TERRAFORM
AS 
SELECT TAB_A.OPERATION_NO_UAPK    ,
       TAB_A.OPERATION_NAME       ,
       TAB_A.OPERATION_DATE       ,
       TAB_A.OPERATION_NO_IDBH    ,
       TAB_A.OPERATION            ,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER
FROM 
    E_OPERATION_LIST TAB_A
WHERE
    TAB_A.DISUSE_FLAG IN ('0') 
;

-- -------------------------------------------------------
-- Terraform 代入値管理/代入値自動登録用 REST API対応
--        Movement+変数名  リスト用 View
-- -------------------------------------------------------
CREATE VIEW E_TERRAFORM_PTN_VAR_LIST AS
SELECT DISTINCT
  TAB_A.MODULE_VARS_LINK_ID              ,
  TAB_A.PATTERN_ID                       ,
  TAB_A.PATTERN_NAME                     ,
  TAB_A.VARS_NAME                        ,
  TAB_A.VARS_LINK_PULLDOWN               ,
  TAB_A.DISP_SEQ                         ,
  TAB_C.ACCESS_AUTH                      ,
  TAB_A.NOTE                             ,
  TAB_A.DISUSE_FLAG                      ,
  TAB_A.LAST_UPDATE_TIMESTAMP            ,
  TAB_A.LAST_UPDATE_USER                 ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01    ,
  TAB_A.ACCESS_AUTH_01 AS ACCESS_AUTH_02 ,
  TAB_A.ACCESS_AUTH_02 AS ACCESS_AUTH_03 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_04    ,
  [%CONCAT_HEAD/%]TAB_A.PATTERN_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_C.PATTERN_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MODULE_VARS_LINK_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_B.VARS_NAME[%CONCAT_TAIL/%] PTN_VAR_PULLDOWN
FROM
  D_TERRAFORM_PTN_VARS_LINK          TAB_A
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK TAB_B ON ( TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
  LEFT JOIN C_PATTERN_PER_ORCH           TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';

CREATE VIEW E_TERRAFORM_PTN_VAR_LIST_JNL AS
SELECT DISTINCT
  TAB_A.MODULE_VARS_LINK_ID              ,
  TAB_A.PATTERN_ID                       ,
  TAB_A.PATTERN_NAME                     ,
  TAB_A.VARS_NAME                        ,
  TAB_A.VARS_LINK_PULLDOWN               ,
  TAB_A.DISP_SEQ                         ,
  TAB_C.ACCESS_AUTH                      ,
  TAB_A.NOTE                             ,
  TAB_A.DISUSE_FLAG                      ,
  TAB_A.LAST_UPDATE_TIMESTAMP            ,
  TAB_A.LAST_UPDATE_USER                 ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01    ,
  TAB_A.ACCESS_AUTH_01 AS ACCESS_AUTH_02 ,
  TAB_A.ACCESS_AUTH_02 AS ACCESS_AUTH_03 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_04    ,
  [%CONCAT_HEAD/%]TAB_A.PATTERN_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_C.PATTERN_NAME[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_A.MODULE_VARS_LINK_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]TAB_B.VARS_NAME[%CONCAT_TAIL/%] PTN_VAR_PULLDOWN
FROM
  D_TERRAFORM_PTN_VARS_LINK_JNL     TAB_A
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK_JNL TAB_B ON ( TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
  LEFT JOIN C_PATTERN_PER_ORCH_JNL      TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';


--Module素材 VIEW
CREATE VIEW D_TERRAFORM_MODULE AS
SELECT  MODULE_MATTER_ID      ,
        MODULE_MATTER_NAME    ,
        [%CONCAT_HEAD/%]MODULE_MATTER_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]MODULE_MATTER_NAME[%CONCAT_TAIL/%] MODULE,
        MODULE_MATTER_FILE    ,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_MODULE;

CREATE VIEW D_TERRAFORM_MODULE_JNL AS
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        MODULE_MATTER_ID      ,
        MODULE_MATTER_NAME    ,
        [%CONCAT_HEAD/%]MODULE_MATTER_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]MODULE_MATTER_NAME[%CONCAT_TAIL/%] MODULE,
        MODULE_MATTER_FILE    ,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_MODULE_JNL;

--Policy管理 VIEW
CREATE VIEW D_TERRAFORM_POLICY AS
SELECT  POLICY_ID      ,
        POLICY_NAME    ,
        [%CONCAT_HEAD/%]POLICY_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]POLICY_NAME[%CONCAT_TAIL/%] POLICY,
        POLICY_MATTER_FILE    ,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_POLICY;

CREATE VIEW D_TERRAFORM_POLICY_JNL AS
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        POLICY_ID             ,
        POLICY_NAME           ,
        [%CONCAT_HEAD/%]POLICY_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]POLICY_NAME[%CONCAT_TAIL/%] POLICY,
        POLICY_MATTER_FILE    ,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_POLICY_JNL;

--PolicySet管理 VIEW
CREATE VIEW D_TERRAFORM_POLICY_SETS AS
SELECT  POLICY_SET_ID      ,
        POLICY_SET_NAME    ,
        [%CONCAT_HEAD/%]POLICY_SET_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]POLICY_SET_NAME[%CONCAT_TAIL/%] POLICY_SET,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_POLICY_SETS;

CREATE VIEW D_TERRAFORM_POLICY_SETS_JNL AS
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        POLICY_SET_ID             ,
        POLICY_SET_NAME           ,
        [%CONCAT_HEAD/%]POLICY_SET_ID[%CONCAT_MID/%]':'[%CONCAT_MID/%]POLICY_SET_NAME[%CONCAT_TAIL/%] POLICY_SET,
        DISP_SEQ              ,
        ACCESS_AUTH           ,
        NOTE                  ,
        DISUSE_FLAG           ,
        LAST_UPDATE_TIMESTAMP ,
        LAST_UPDATE_USER
FROM    B_TERRAFORM_POLICY_SETS_JNL;

-- *****************************************************************************
-- *** Terraform Views *****                                                 ***
-- *****************************************************************************

