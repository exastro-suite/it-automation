--代入値自動登録設定メニュー用　VIEW
CREATE OR REPLACE VIEW D_TERRAFORM_VAL_ASSIGN_SUB AS 
SELECT
  c1.*,
  CASE WHEN c1.COL_TYPE = 3 THEN m1.MODULE_PTN_LINK_ID
    WHEN c1.COL_TYPE = 1 THEN m1.MODULE_PTN_LINK_ID
    ELSE NULL
  END AS VAL_VARS_PTN_LINK_ID,
  CASE WHEN c1.COL_TYPE = 3 THEN m2.MODULE_PTN_LINK_ID
    WHEN c1.COL_TYPE = 2 THEN m2.MODULE_PTN_LINK_ID
    ELSE NULL
  END AS KEY_VARS_PTN_LINK_ID
FROM B_TERRAFORM_VAL_ASSIGN AS c1
LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 AS m1
  ON c1.PATTERN_ID = m1.PATTERN_ID AND c1.VAL_VARS_LINK_ID = m1.MODULE_VARS_LINK_ID
LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 AS m2
  ON c1.PATTERN_ID = m2.PATTERN_ID AND c1.KEY_VARS_LINK_ID = m2.MODULE_VARS_LINK_ID
ORDER BY c1.COLUMN_ID
;

CREATE OR REPLACE VIEW D_TERRAFORM_VAL_ASSIGN AS 
SELECT
        TAB_A.COLUMN_ID                      , -- 識別シーケンス
        TAB_A.MENU_ID                        , -- メニューID
        TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
        TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
        TAB_A.PATTERN_ID                     , -- 作業パターンID
        TAB_A.VAL_VARS_LINK_ID               , -- Value値　Module変数紐付
        TAB_A.KEY_VARS_LINK_ID               , -- Key値　Module変数紐付
        TAB_A.KEY_ASSIGN_SEQ                 , -- Keyの代入順序
        TAB_A.KEY_MEMBER_VARS                , -- Keyのメンバ変数
        TAB_A.VAL_ASSIGN_SEQ                 , -- Valueの代入順序
        TAB_A.VAL_MEMBER_VARS                , -- Valueのメンバ変数
        TAB_A.VAL_VARS_PTN_LINK_ID           , -- Value値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_VARS_PTN_LINK_ID           , -- Key値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.HCL_FLAG                       , -- HCL設定
        TAB_A.VAL_VARS_HCL_FLAG              , -- Value値 HCL設定
        TAB_A.KEY_VARS_HCL_FLAG              , -- Key値 HCL設定
        TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
        TAB_B.MENU_GROUP_ID                  ,
        TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
        TAB_C.MENU_GROUP_NAME                ,
        TAB_A.MENU_ID           MENU_ID_CLONE,
        TAB_A.MENU_ID           MENU_ID_CLONE_02,
        TAB_B.MENU_NAME                      ,
        TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
        TAB_A.VAL_VARS_PTN_LINK_ID REST_VAL_VARS_LINK_ID, -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.VAL_MEMBER_VARS       REST_VAL_MEMBER_VARS, -- REST/EXCEL/CSV用　Value値　変数名+メンバー変数
        TAB_A.KEY_VARS_PTN_LINK_ID REST_KEY_VARS_LINK_ID, -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_MEMBER_VARS       REST_KEY_MEMBER_VARS, -- REST/EXCEL/CSV用　Key値　変数名+メンバー変数
        TAB_A.DISP_SEQ                       ,
        TAB_A.ACCESS_AUTH                    ,
        TAB_A.NOTE                           ,
        TAB_A.DISUSE_FLAG                    ,
        TAB_A.LAST_UPDATE_TIMESTAMP          ,
        TAB_A.LAST_UPDATE_USER 
FROM D_TERRAFORM_VAL_ASSIGN_SUB AS TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
;


CREATE OR REPLACE VIEW D_TERRAFORM_VAL_ASSIGN_SUB_JNL AS 
SELECT
  c1.*,
  CASE WHEN c1.COL_TYPE = 3 THEN m1.MODULE_PTN_LINK_ID
    WHEN c1.COL_TYPE = 1 THEN m1.MODULE_PTN_LINK_ID
    ELSE NULL
  END AS VAL_VARS_PTN_LINK_ID,
  CASE WHEN c1.COL_TYPE = 3 THEN m2.MODULE_PTN_LINK_ID
    WHEN c1.COL_TYPE = 2 THEN m2.MODULE_PTN_LINK_ID
    ELSE NULL
  END AS KEY_VARS_PTN_LINK_ID
FROM B_TERRAFORM_VAL_ASSIGN_JNL AS c1
LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 AS m1
  ON c1.PATTERN_ID = m1.PATTERN_ID AND c1.VAL_VARS_LINK_ID = m1.MODULE_VARS_LINK_ID
LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 AS m2
  ON c1.PATTERN_ID = m2.PATTERN_ID AND c1.KEY_VARS_LINK_ID = m2.MODULE_VARS_LINK_ID
ORDER BY c1.COLUMN_ID
;


CREATE OR REPLACE VIEW D_TERRAFORM_VAL_ASSIGN_JNL AS 
SELECT
        TAB_A.JOURNAL_SEQ_NO                 ,
        TAB_A.JOURNAL_REG_DATETIME           ,
        TAB_A.JOURNAL_ACTION_CLASS           ,
        TAB_A.COLUMN_ID                      , -- 識別シーケンス
        TAB_A.MENU_ID                        , -- メニューID
        TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
        TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
        TAB_A.PATTERN_ID                     , -- 作業パターンID
        TAB_A.VAL_VARS_LINK_ID               , -- Value値　Module変数紐付
        TAB_A.KEY_VARS_LINK_ID               , -- Key値　Module変数紐付
        TAB_A.KEY_ASSIGN_SEQ                 , -- Keyの代入順序
        TAB_A.KEY_MEMBER_VARS                , -- Keyのメンバ変数
        TAB_A.VAL_ASSIGN_SEQ                 , -- Valueの代入順序
        TAB_A.VAL_MEMBER_VARS                , -- Valueのメンバ変数
        TAB_A.VAL_VARS_PTN_LINK_ID           , -- Value値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_VARS_PTN_LINK_ID           , -- Key値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.HCL_FLAG                       , -- HCL設定
        TAB_A.VAL_VARS_HCL_FLAG              , -- Value値 HCL設定
        TAB_A.KEY_VARS_HCL_FLAG              , -- Key値 HCL設定
        TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
        TAB_B.MENU_GROUP_ID                  ,
        TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
        TAB_C.MENU_GROUP_NAME                ,
        TAB_A.MENU_ID           MENU_ID_CLONE,
        TAB_A.MENU_ID           MENU_ID_CLONE_02,
        TAB_B.MENU_NAME                      ,
        TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
        TAB_A.VAL_VARS_PTN_LINK_ID REST_VAL_VARS_LINK_ID, -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.VAL_MEMBER_VARS       REST_VAL_MEMBER_VARS, -- REST/EXCEL/CSV用　Value値　変数名+メンバー変数
        TAB_A.KEY_VARS_PTN_LINK_ID REST_KEY_VARS_LINK_ID, -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_MEMBER_VARS       REST_KEY_MEMBER_VARS, -- REST/EXCEL/CSV用　Key値　変数名+メンバー変数
        TAB_A.DISP_SEQ                       ,
        TAB_A.ACCESS_AUTH                    ,
        TAB_A.NOTE                           ,
        TAB_A.DISUSE_FLAG                    ,
        TAB_A.LAST_UPDATE_TIMESTAMP          ,
        TAB_A.LAST_UPDATE_USER 
FROM D_TERRAFORM_VAL_ASSIGN_SUB_JNL AS TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
;
