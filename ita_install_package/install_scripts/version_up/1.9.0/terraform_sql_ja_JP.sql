--Movement*変数の連番振り分け用VIEW
CREATE OR REPLACE VIEW D_TERRAFORM_MODULE_PTN_VARS_LINK_1 AS 
SELECT DISTINCT
        TAB_A.PATTERN_ID                    ,
        TAB_B.MODULE_VARS_LINK_ID           ,
        TAB_B.MODULE_MATTER_ID              ,
        TAB_B.VARS_NAME                     
from E_TERRAFORM_PATTERN TAB_A 
CROSS JOIN B_TERRAFORM_MODULE_VARS_LINK TAB_B
;

CREATE OR REPLACE VIEW D_TERRAFORM_MODULE_PTN_VARS_LINK_2 AS 
SELECT
        ROW_NUMBER() OVER(ORDER BY TAB_A.PATTERN_ID, TAB_A.MODULE_VARS_LINK_ID) MODULE_PTN_LINK_ID,
        TAB_A.PATTERN_ID                    ,
        TAB_A.MODULE_VARS_LINK_ID           ,
        TAB_A.MODULE_MATTER_ID              ,
        TAB_A.VARS_NAME                     
FROM D_TERRAFORM_MODULE_PTN_VARS_LINK_1 TAB_A
;


CREATE OR REPLACE VIEW D_TERRAFORM_VARS_ASSIGN AS
SELECT
  TAB_A.*,
  TAB_B.MODULE_PTN_LINK_ID            VARS_PTN_LINK_ID,
  TAB_B.MODULE_PTN_LINK_ID            REST_MODULE_VARS_LINK_ID
FROM
  B_TERRAFORM_VARS_ASSIGN TAB_A
LEFT JOIN
  D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
;

CREATE OR REPLACE VIEW D_TERRAFORM_VARS_ASSIGN_JNL AS
SELECT
  TAB_A.*,
  TAB_B.MODULE_PTN_LINK_ID            VARS_PTN_LINK_ID,
  TAB_B.MODULE_PTN_LINK_ID            REST_MODULE_VARS_LINK_ID
FROM
  B_TERRAFORM_VARS_ASSIGN_JNL TAB_A
LEFT JOIN
  D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
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
        TAB_A.VAL_VARS_PTN_LINK_ID           , -- Value値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_VARS_PTN_LINK_ID           , -- Key値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.HCL_FLAG                       , -- HCL設定
        TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
        TAB_B.MENU_GROUP_ID                  ,
        TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
        TAB_C.MENU_GROUP_NAME                ,
        TAB_A.MENU_ID           MENU_ID_CLONE,
        TAB_A.MENU_ID           MENU_ID_CLONE_02,
        TAB_B.MENU_NAME                      ,
        TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
        TAB_A.VAL_VARS_PTN_LINK_ID REST_VAL_VARS_LINK_ID, -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_VARS_PTN_LINK_ID REST_KEY_VARS_LINK_ID, -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.DISP_SEQ                       ,
        TAB_A.ACCESS_AUTH                    ,
        TAB_A.NOTE                           ,
        TAB_A.DISUSE_FLAG                    ,
        TAB_A.LAST_UPDATE_TIMESTAMP          ,
        TAB_A.LAST_UPDATE_USER 
FROM (
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
) AS TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
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
        TAB_A.VAL_VARS_PTN_LINK_ID           , -- Value値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_VARS_PTN_LINK_ID           , -- Key値 作業パターン+変数名(作業パターン変数紐付)
        TAB_A.HCL_FLAG                       , -- HCL設定
        TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
        TAB_B.MENU_GROUP_ID                  ,
        TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
        TAB_C.MENU_GROUP_NAME                ,
        TAB_A.MENU_ID           MENU_ID_CLONE,
        TAB_A.MENU_ID           MENU_ID_CLONE_02,
        TAB_B.MENU_NAME                      ,
        TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
        TAB_A.VAL_VARS_PTN_LINK_ID REST_VAL_VARS_LINK_ID, -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.KEY_VARS_PTN_LINK_ID REST_KEY_VARS_LINK_ID, -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
        TAB_A.DISP_SEQ                       ,
        TAB_A.ACCESS_AUTH                    ,
        TAB_A.NOTE                           ,
        TAB_A.DISUSE_FLAG                    ,
        TAB_A.LAST_UPDATE_TIMESTAMP          ,
        TAB_A.LAST_UPDATE_USER 
FROM (
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
) AS TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID)
;


CREATE OR REPLACE VIEW D_TERRAFORM_PTN_VARS_LINK AS 
SELECT 
        TAB_D.MODULE_PTN_LINK_ID            ,
        TAB_A.MODULE_VARS_LINK_ID           ,
        TAB_B.PATTERN_ID                    ,
        TAB_C.PATTERN_NAME                  ,
        TAB_A.VARS_NAME                     ,
        CONCAT(TAB_A.MODULE_VARS_LINK_ID,':',TAB_A.VARS_NAME) VARS_LINK_PULLDOWN,
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
LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_D ON ( TAB_B.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_D.MODULE_VARS_LINK_ID )
;

CREATE OR REPLACE VIEW D_TERRAFORM_PTN_VARS_LINK_JNL AS 
SELECT
        JOURNAL_SEQ_NO                      ,
        JOURNAL_REG_DATETIME                ,
        JOURNAL_ACTION_CLASS                ,
        TAB_D.MODULE_PTN_LINK_ID            ,
        TAB_A.MODULE_VARS_LINK_ID           ,
        TAB_B.PATTERN_ID                    ,
        TAB_C.PATTERN_NAME                  ,
        TAB_A.VARS_NAME                     ,
        CONCAT(TAB_A.MODULE_VARS_LINK_ID,':',TAB_A.VARS_NAME) VARS_LINK_PULLDOWN,
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
LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_D ON ( TAB_B.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_D.MODULE_VARS_LINK_ID )
;


CREATE OR REPLACE VIEW D_TERRAFORM_PTN_VARS_LINK_VFP AS 
SELECT 
        TAB_D.MODULE_PTN_LINK_ID      ,
        TAB_A.MODULE_VARS_LINK_ID      ,
        TAB_B.PATTERN_ID              ,
        TAB_C.PATTERN_NAME            ,
        TAB_A.VARS_NAME               ,
        CONCAT(TAB_A.MODULE_VARS_LINK_ID,':',TAB_A.VARS_NAME) VARS_LINK_PULLDOWN,
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
LEFT JOIN D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_D ON ( TAB_B.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_VARS_LINK_ID = TAB_D.MODULE_VARS_LINK_ID  )
WHERE TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;


CREATE OR REPLACE VIEW E_TERRAFORM_PTN_VAR_LIST AS
SELECT DISTINCT
  TAB_A.MODULE_PTN_LINK_ID               ,
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
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.MODULE_VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  D_TERRAFORM_PTN_VARS_LINK_VFP          TAB_A
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK TAB_B ON ( TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
  LEFT JOIN C_PATTERN_PER_ORCH           TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
  LEFT JOIN B_TERRAFORM_PATTERN_LINK     TAB_D ON ( TAB_A.PATTERN_ID   = TAB_D.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0' AND
  TAB_D.DISUSE_FLAG = '0';

CREATE OR REPLACE VIEW E_TERRAFORM_PTN_VAR_LIST_JNL AS
SELECT DISTINCT
  TAB_A.MODULE_PTN_LINK_ID               ,
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
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.MODULE_VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  D_TERRAFORM_PTN_VARS_LINK_JNL     TAB_A
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK_JNL TAB_B ON ( TAB_A.MODULE_VARS_LINK_ID = TAB_B.MODULE_VARS_LINK_ID )
  LEFT JOIN C_PATTERN_PER_ORCH_JNL      TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
  LEFT JOIN B_TERRAFORM_PATTERN_LINK     TAB_D ON ( TAB_A.PATTERN_ID   = TAB_D.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0' AND
  TAB_D.DISUSE_FLAG = '0';


-- -------------------------------------------------------
-- Terraform Movement変数紐付ページ用 View
-- -------------------------------------------------------
CREATE OR REPLACE VIEW D_TERRAFORM_PTN_VAR_LIST AS
SELECT DISTINCT
  TAB_A.MODULE_PTN_LINK_ID               ,
  TAB_A.PATTERN_ID                       ,
  TAB_A.MODULE_VARS_LINK_ID              ,
  TAB_A.MODULE_MATTER_ID                 ,
  TAB_A.VARS_NAME                        ,
  CONCAT(TAB_A.MODULE_VARS_LINK_ID,':',TAB_A.VARS_NAME) VARS_LINK_PULLDOWN,
  TAB_B.DISP_SEQ                         ,
  TAB_B.ACCESS_AUTH                      ,
  TAB_B.NOTE                             ,
  CASE WHEN TAB_B.DISUSE_FLAG = 1 THEN 1
       WHEN TAB_C.DISUSE_FLAG = 1 THEN 1
       WHEN TAB_D.LINK_ID IS NULL THEN 1
       ELSE 0
       END AS DISUSE_FLAG                ,
  TAB_B.LAST_UPDATE_TIMESTAMP            ,
  TAB_B.LAST_UPDATE_USER                 ,    
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_01    ,
  TAB_D.ACCESS_AUTH AS ACCESS_AUTH_02
FROM
  D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_A
  LEFT JOIN E_TERRAFORM_PATTERN TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID)
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK TAB_C ON (TAB_A.MODULE_VARS_LINK_ID = TAB_C.MODULE_VARS_LINK_ID)
  LEFT JOIN B_TERRAFORM_PATTERN_LINK TAB_D ON (TAB_A.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_MATTER_ID = TAB_D.MODULE_MATTER_ID AND TAB_D.DISUSE_FLAG = 0)
ORDER BY TAB_A.MODULE_PTN_LINK_ID
;

CREATE OR REPLACE VIEW D_TERRAFORM_PTN_VAR_LIST_JNL AS
SELECT DISTINCT
  TAB_B.JOURNAL_SEQ_NO                   ,
  TAB_B.JOURNAL_REG_DATETIME             ,
  TAB_B.JOURNAL_ACTION_CLASS             ,
  TAB_A.MODULE_PTN_LINK_ID               ,
  TAB_A.PATTERN_ID                       ,
  TAB_A.MODULE_VARS_LINK_ID              ,
  TAB_A.MODULE_MATTER_ID                 ,
  TAB_A.VARS_NAME                        ,
  CONCAT(TAB_A.MODULE_VARS_LINK_ID,':',TAB_A.VARS_NAME) VARS_LINK_PULLDOWN,
  TAB_B.DISP_SEQ                         ,
  TAB_B.ACCESS_AUTH                      ,
  TAB_B.NOTE                             ,
  CASE WHEN TAB_B.DISUSE_FLAG = 1 THEN 1
       WHEN TAB_C.DISUSE_FLAG = 1 THEN 1
       WHEN TAB_D.LINK_ID IS NULL THEN 1
       ELSE 0
       END AS DISUSE_FLAG                ,
  TAB_B.LAST_UPDATE_TIMESTAMP            ,
  TAB_B.LAST_UPDATE_USER                 ,    
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_01    ,
  TAB_D.ACCESS_AUTH AS ACCESS_AUTH_02          
FROM
  D_TERRAFORM_MODULE_PTN_VARS_LINK_2 TAB_A
  LEFT JOIN E_TERRAFORM_PATTERN_JNL TAB_B ON (TAB_A.PATTERN_ID = TAB_B.PATTERN_ID)
  LEFT JOIN B_TERRAFORM_MODULE_VARS_LINK TAB_C ON (TAB_A.MODULE_VARS_LINK_ID = TAB_C.MODULE_VARS_LINK_ID)
  LEFT JOIN B_TERRAFORM_PATTERN_LINK TAB_D ON (TAB_A.PATTERN_ID = TAB_D.PATTERN_ID AND TAB_A.MODULE_MATTER_ID = TAB_D.MODULE_MATTER_ID AND TAB_D.DISUSE_FLAG = 0)
;


INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080018,2100080001,'Movement変数紐付管理',NULL,NULL,NULL,1,0,1,2,210,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-80018,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080018,2100080001,'Movement変数紐付管理',NULL,NULL,NULL,1,0,1,2,210,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100080018,1,2100080018,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-180018,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100080018,1,2100080018,2,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


INSERT INTO B_TERRAFORM_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'リソース削除',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_TERRAFORM_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'リソース削除',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
