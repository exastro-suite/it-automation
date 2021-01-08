CREATE OR REPLACE VIEW D_ANS_LNS_PTN_VARS_LINK AS 
SELECT 
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LNS_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_LNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;
CREATE OR REPLACE VIEW D_ANS_LNS_PTN_VARS_LINK_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                      ,
        JOURNAL_REG_DATETIME                ,
        JOURNAL_ACTION_CLASS                ,
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LNS_PTN_VARS_LINK_JNL TAB_A
LEFT JOIN E_ANSIBLE_LNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;

CREATE OR REPLACE VIEW D_ANS_LNS_PTN_VARS_LINK_VFP AS 
SELECT 
        TAB_A.VARS_LINK_ID            ,
        TAB_A.PATTERN_ID              ,
        TAB_B.PATTERN_NAME            ,
        TAB_A.VARS_NAME_ID            ,
        TAB_C.VARS_NAME               ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.ACCESS_AUTH             ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER        ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LNS_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_LNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
WHERE TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;


CREATE OR REPLACE VIEW D_ANS_PNS_PTN_VARS_LINK AS 
SELECT 
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02 
FROM B_ANS_PNS_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_PNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_PNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;

CREATE OR REPLACE VIEW D_ANS_PNS_PTN_VARS_LINK_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                      ,
        JOURNAL_REG_DATETIME                ,
        JOURNAL_ACTION_CLASS                ,
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02 
FROM B_ANS_PNS_PTN_VARS_LINK_JNL TAB_A
LEFT JOIN E_ANSIBLE_PNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_PNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;

CREATE OR REPLACE VIEW D_ANS_PNS_PTN_VARS_LINK_VFP AS 
SELECT 
        TAB_A.VARS_LINK_ID            ,
        TAB_A.PATTERN_ID              ,
        TAB_B.PATTERN_NAME            ,
        TAB_A.VARS_NAME_ID            ,
        TAB_C.VARS_NAME               ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.ACCESS_AUTH             ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER        ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_PNS_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_PNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_PNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
WHERE TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;


CREATE OR REPLACE VIEW E_OPE_FOR_PULLDOWN_PNS
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
       TAB_A.LAST_UPDATE_USER     ,
       TAB_B.PHO_LINK_ID          ,
       TAB_B.DISUSE_FLAG           DISUSE_FLAG_2,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM 
    E_OPERATION_LIST TAB_A
    LEFT JOIN B_ANSIBLE_PNS_PHO_LINK TAB_B ON (TAB_A.OPERATION_NO_UAPK = TAB_B.OPERATION_NO_UAPK)
WHERE
    TAB_A.DISUSE_FLAG IN ('0') 
    AND
    TAB_B.PHO_LINK_ID IS NOT NULL 
    AND 
    TAB_B.DISUSE_FLAG IN ('0')
;


CREATE OR REPLACE VIEW D_ANSIBLE_LRL_ROLE_PKG_LIST AS 
SELECT 
        TAB_A.*                       ,
        CONCAT(TAB_A.ROLE_PACKAGE_ID,':',TAB_A.ROLE_PACKAGE_NAME) ROLE_PACKAGE_NAME_PULLDOWN
FROM B_ANSIBLE_LRL_ROLE_PACKAGE TAB_A
;
CREATE OR REPLACE VIEW D_ANSIBLE_LRL_ROLE_PKG_LIST_JNL AS 
SELECT
        TAB_A.*                       ,
        CONCAT(TAB_A.ROLE_PACKAGE_ID,':',TAB_A.ROLE_PACKAGE_NAME) ROLE_PACKAGE_NAME_PULLDOWN
FROM B_ANSIBLE_LRL_ROLE_PACKAGE_JNL TAB_A
;


CREATE OR REPLACE VIEW D_ANSIBLE_LRL_ROLE_LIST AS 
SELECT 
        TAB_A.*                       ,
        CONCAT(TAB_A.ROLE_ID,':',TAB_A.ROLE_NAME) ROLE_NAME_PULLDOWN
FROM B_ANSIBLE_LRL_ROLE     TAB_A
;


CREATE OR REPLACE VIEW D_ANSIBLE_LRL_ROLE_LIST_JNL AS 
SELECT 
        TAB_A.*                       ,
        CONCAT(TAB_A.ROLE_ID,':',TAB_A.ROLE_NAME) ROLE_NAME_PULLDOWN
FROM B_ANSIBLE_LRL_ROLE_JNL TAB_A
;


CREATE OR REPLACE VIEW D_ANS_LRL_PTN_VARS_LINK_VFP AS 
SELECT 
        TAB_A.VARS_LINK_ID            ,
        TAB_A.PATTERN_ID              ,
        TAB_B.PATTERN_NAME            ,
        TAB_A.VARS_NAME_ID            ,
        TAB_C.VARS_NAME               ,
        TAB_C.VARS_ATTRIBUTE_01       ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.ACCESS_AUTH             ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER        ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LRL_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_LRL_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
WHERE TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;


CREATE OR REPLACE VIEW D_ANS_LRL_PTN_VARS_LINK AS 
SELECT 
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        TAB_C.VARS_ATTRIBUTE_01             ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LRL_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_LRL_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;


CREATE OR REPLACE VIEW D_ANS_LRL_PTN_VARS_LINK_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                      ,
        JOURNAL_REG_DATETIME                ,
        JOURNAL_ACTION_CLASS                ,
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        TAB_C.VARS_ATTRIBUTE_01             ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LRL_PTN_VARS_LINK_JNL TAB_A
LEFT JOIN E_ANSIBLE_LRL_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;


CREATE OR REPLACE VIEW D_ANS_LRL_CHILD_VARS AS 
SELECT 
        TAB_A.CHILD_VARS_NAME_ID            ,
        TAB_A.CHILD_VARS_NAME               ,
        TAB_A.PARENT_VARS_NAME_ID           ,
        TAB_B.VARS_NAME                     ,
        TAB_B.VARS_ATTRIBUTE_01             ,
        TAB_C.VARS_LINK_ID                  ,
        CONCAT(TAB_A.CHILD_VARS_NAME_ID,':',TAB_A.CHILD_VARS_NAME) CHILD_VARS_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANSIBLE_LRL_CHILD_VARS         TAB_A
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER   TAB_B ON ( TAB_A.PARENT_VARS_NAME_ID = TAB_B.VARS_NAME_ID )
LEFT JOIN B_ANS_LRL_PTN_VARS_LINK TAB_C ON ( TAB_B.VARS_NAME_ID = TAB_C.VARS_NAME_ID)
WHERE TAB_B.VARS_ATTRIBUTE_01 IN (3)
;


CREATE OR REPLACE VIEW D_ANS_LRL_CHILD_VARS_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                      ,
        JOURNAL_REG_DATETIME                ,
        JOURNAL_ACTION_CLASS                ,
        TAB_A.CHILD_VARS_NAME_ID            ,
        TAB_A.CHILD_VARS_NAME               ,
        TAB_A.PARENT_VARS_NAME_ID           ,
        TAB_B.VARS_NAME                     ,
        TAB_B.VARS_ATTRIBUTE_01             ,
        TAB_C.VARS_LINK_ID                  ,
        CONCAT(TAB_A.CHILD_VARS_NAME_ID,':',TAB_A.CHILD_VARS_NAME) CHILD_VARS_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANSIBLE_LRL_CHILD_VARS_JNL     TAB_A
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER   TAB_B ON ( TAB_A.PARENT_VARS_NAME_ID = TAB_B.VARS_NAME_ID )
LEFT JOIN B_ANS_LRL_PTN_VARS_LINK TAB_C ON ( TAB_B.VARS_NAME_ID = TAB_C.VARS_NAME_ID)
WHERE TAB_B.VARS_ATTRIBUTE_01 IN (3)
;


CREATE OR REPLACE VIEW E_OPE_FOR_PULLDOWN_LRL
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
       TAB_A.LAST_UPDATE_USER     ,
       TAB_B.PHO_LINK_ID          ,
       TAB_B.DISUSE_FLAG           DISUSE_FLAG_2,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM 
    E_OPERATION_LIST TAB_A
    LEFT JOIN B_ANSIBLE_LRL_PHO_LINK TAB_B ON (TAB_A.OPERATION_NO_UAPK = TAB_B.OPERATION_NO_UAPK)
WHERE
    TAB_A.DISUSE_FLAG IN ('0') 
    AND
    TAB_B.PHO_LINK_ID IS NOT NULL 
    AND 
    TAB_B.DISUSE_FLAG IN ('0')
;


CREATE OR REPLACE VIEW D_ANS_LNS_VAL_ASSIGN AS 
SELECT 
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
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
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
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
FROM B_ANS_LNS_VAL_ASSIGN TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

CREATE OR REPLACE VIEW D_ANS_LNS_VAL_ASSIGN_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                 ,
       TAB_A.JOURNAL_REG_DATETIME           ,
       TAB_A.JOURNAL_ACTION_CLASS           ,
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
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
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
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
FROM B_ANS_LNS_VAL_ASSIGN_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);


CREATE OR REPLACE VIEW D_ANS_LRL_VAL_ASSIGN AS 
SELECT 
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.VAL_COL_SEQ_COMBINATION_ID     , -- 多次元変数配列組合せ管理 Pkey
       TAB_A.VAL_ASSIGN_SEQ                 , -- Value値　代入順序
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.KEY_COL_SEQ_COMBINATION_ID     , -- 多次元変数配列組合せ管理 Pkey
       TAB_A.KEY_ASSIGN_SEQ                 , -- Key値　代入順序
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
       TAB_B.MENU_NAME                      ,
       TAB_A.COLUMN_LIST_ID             REST_COLUMN_LIST_ID,             -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.VAL_VARS_LINK_ID           REST_VAL_VARS_LINK_ID,           -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.VAL_COL_SEQ_COMBINATION_ID REST_VAL_COL_SEQ_COMBINATION_ID, -- REST/EXCEL/CSV用　Value値　多次元変数配列組合せ管理 Pkey
       TAB_A.KEY_VARS_LINK_ID           REST_KEY_VARS_LINK_ID,           -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.KEY_COL_SEQ_COMBINATION_ID REST_KEY_COL_SEQ_COMBINATION_ID, -- REST/EXCEL/CSV用　Key値　多次元変数配列組合せ管理 Pkey
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_ANS_LRL_VAL_ASSIGN TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

CREATE OR REPLACE VIEW D_ANS_LRL_VAL_ASSIGN_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                 ,
       TAB_A.JOURNAL_REG_DATETIME           ,
       TAB_A.JOURNAL_ACTION_CLASS           ,
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.VAL_COL_SEQ_COMBINATION_ID     , -- 多次元変数配列組合せ管理 Pkey
       TAB_A.VAL_ASSIGN_SEQ                 , -- Value値　代入順序
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.KEY_COL_SEQ_COMBINATION_ID     , -- 多次元変数配列組合せ管理 Pkey
       TAB_A.KEY_ASSIGN_SEQ                 , -- Key値　代入順序
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
       TAB_B.MENU_NAME                      ,
       TAB_A.COLUMN_LIST_ID             REST_COLUMN_LIST_ID,             -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.VAL_VARS_LINK_ID           REST_VAL_VARS_LINK_ID,           -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.VAL_COL_SEQ_COMBINATION_ID REST_VAL_COL_SEQ_COMBINATION_ID, -- REST/EXCEL/CSV用　Value値　多次元変数配列組合せ管理 Pkey
       TAB_A.KEY_VARS_LINK_ID           REST_KEY_VARS_LINK_ID,           -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.KEY_COL_SEQ_COMBINATION_ID REST_KEY_COL_SEQ_COMBINATION_ID, -- REST/EXCEL/CSV用　Key値　多次元変数配列組合せ管理 Pkey
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_ANS_LRL_VAL_ASSIGN_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);


CREATE OR REPLACE VIEW D_ANS_PNS_VAL_ASSIGN AS 
SELECT 
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
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
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
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
FROM B_ANS_PNS_VAL_ASSIGN TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

CREATE OR REPLACE VIEW D_ANS_PNS_VAL_ASSIGN_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                 ,
       TAB_A.JOURNAL_REG_DATETIME           ,
       TAB_A.JOURNAL_ACTION_CLASS           ,
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
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
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
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
FROM B_ANS_PNS_VAL_ASSIGN_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);


CREATE OR REPLACE VIEW E_ANS_PNS_PTN_VAR_LIST AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_PNS_PTN_VARS_LINK             TAB_A
  LEFT JOIN B_ANSIBLE_PNS_VARS_MASTER TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';


CREATE OR REPLACE VIEW E_ANS_PNS_PTN_VAR_LIST_JNL AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_PNS_PTN_VARS_LINK_JNL             TAB_A
  LEFT JOIN B_ANSIBLE_PNS_VARS_MASTER_JNL TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH_JNL        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';


CREATE OR REPLACE VIEW E_ANS_PNS_STM_LIST AS
SELECT
  TAB_A.*                             ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_PNS_PHO_LINK  TAB_A
  LEFT JOIN C_STM_LIST    TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW E_ANS_PNS_STM_LIST_JNL AS
SELECT
  TAB_A.*                             ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_PNS_PHO_LINK_JNL TAB_A
  LEFT JOIN C_STM_LIST_JNL   TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';


CREATE OR REPLACE VIEW E_ANS_LNS_PTN_VAR_LIST AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_LNS_PTN_VARS_LINK             TAB_A
  LEFT JOIN B_ANSIBLE_LNS_VARS_MASTER TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW E_ANS_LNS_PTN_VAR_LIST_JNL AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_LNS_PTN_VARS_LINK_JNL             TAB_A
  LEFT JOIN B_ANSIBLE_LNS_VARS_MASTER_JNL TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH_JNL        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';


CREATE OR REPLACE VIEW E_ANS_LNS_STM_LIST AS
SELECT
  TAB_A.*                              ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01  ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_LNS_PHO_LINK  TAB_A
  LEFT JOIN C_STM_LIST    TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW E_ANS_LNS_STM_LIST_JNL AS
SELECT
  TAB_A. *                             ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01  ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_LNS_PHO_LINK_JNL TAB_A
  LEFT JOIN C_STM_LIST_JNL TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';


CREATE OR REPLACE VIEW E_ANS_LRL_PKG_ROLE_LIST AS
SELECT
  TAB_A.*                             ,
  TAB_B.ROLE_ID                       ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_A.ROLE_PACKAGE_ID,':',TAB_A.ROLE_PACKAGE_NAME,':',TAB_B.ROLE_ID,':',TAB_B.ROLE_NAME) ROLE_PACKAGE_PULLDOWN
FROM
  B_ANSIBLE_LRL_ROLE_PACKAGE   TAB_A
  LEFT JOIN B_ANSIBLE_LRL_ROLE TAB_B ON ( TAB_B.ROLE_PACKAGE_ID = TAB_A.ROLE_PACKAGE_ID )
WHERE
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_A.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW E_ANS_LRL_PKG_ROLE_LIST_JNL AS 
SELECT
  TAB_A.*                             ,
  TAB_B.ROLE_ID                       ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_A.ROLE_PACKAGE_ID,':',TAB_A.ROLE_PACKAGE_NAME,':',TAB_B.ROLE_ID,':',TAB_B.ROLE_NAME) ROLE_PACKAGE_PULLDOWN
FROM
  B_ANSIBLE_LRL_ROLE_PACKAGE_JNL   TAB_A
  LEFT JOIN B_ANSIBLE_LRL_ROLE_JNL TAB_B ON ( TAB_B.ROLE_PACKAGE_ID = TAB_A.ROLE_PACKAGE_ID )
WHERE
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_A.DISUSE_FLAG = '0';


CREATE OR REPLACE VIEW E_ANS_LRL_PTN_VAR_LIST AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_LRL_PTN_VARS_LINK             TAB_A
  LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW E_ANS_LRL_PTN_VAR_LIST_JNL AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_LRL_PTN_VARS_LINK_JNL             TAB_A
  LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER_JNL TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH_JNL        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';


CREATE OR REPLACE VIEW E_ANS_LRL_VAR_MEMBER_LIST AS
SELECT DISTINCT
  TAB_A.*,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.VARS_NAME,'.',TAB_A.COL_SEQ_COMBINATION_ID,':',TAB_A.COL_COMBINATION_MEMBER_ALIAS) VAR_MEMBER_PULLDOWN
FROM
  B_ANS_LRL_MEMBER_COL_COMB               TAB_A
  LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER     TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW E_ANS_LRL_VAR_MEMBER_LIST_JNL AS
SELECT DISTINCT
  TAB_A.*,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.VARS_NAME,'.',TAB_A.COL_SEQ_COMBINATION_ID,':',TAB_A.COL_COMBINATION_MEMBER_ALIAS) VAR_MEMBER_PULLDOWN
FROM
  B_ANS_LRL_MEMBER_COL_COMB_JNL           TAB_A
  LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER_JNL TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';


CREATE OR REPLACE VIEW E_ANS_LRL_STM_LIST AS
SELECT
  TAB_A.*  ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_LRL_PHO_LINK  TAB_A
  LEFT JOIN C_STM_LIST    TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW E_ANS_LRL_STM_LIST_JNL AS
SELECT
  TAB_A.*  ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_LRL_PHO_LINK_JNL TAB_A
  LEFT JOIN C_STM_LIST_JNL TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';
