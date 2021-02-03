-- ---- カラムの追加および削除
★★★ALTER_COLUMN★★★

-- ----表示用VIEW作成(ホストグループ＋ホスト)
CREATE OR REPLACE VIEW G_★★★TABLE★★★_HG AS
SELECT TAB_A.ROW_ID                     ,
       TAB_A.KY_KEY                     ,
       TAB_A.OPERATION_ID               AS OPERATION_ID_DISP,
       TAB_A.OPERATION_ID               AS OPERATION_ID_NAME_DISP,
       TAB_A.OPERATION_ID               ,
       TAB_B.BASE_TIMESTAMP             ,
       TAB_B.LAST_EXECUTE_TIMESTAMP     ,
       TAB_B.OPERATION_NAME             ,
       TAB_B.OPERATION_DATE             ,

-- 個別項目
★★★COLUMN★★★
-- 個別項目

       TAB_A.ACCESS_AUTH                ,
       TAB_A.NOTE                       ,
       TAB_A.DISUSE_FLAG                ,
       TAB_A.LAST_UPDATE_TIMESTAMP      ,
       TAB_A.LAST_UPDATE_USER
FROM      F_★★★TABLE★★★_HG       TAB_A
LEFT JOIN G_OPERATION_LIST        TAB_B ON ( TAB_A.OPERATION_ID = TAB_B.OPERATION_ID AND
                                             TAB_B.DISUSE_FLAG = '0' )
;


-- ----履歴系VIEW作成(ホストグループ＋ホスト)
CREATE OR REPLACE VIEW G_★★★TABLE★★★_HG_JNL AS
SELECT TAB_A.JOURNAL_SEQ_NO             ,
       TAB_A.JOURNAL_REG_DATETIME       ,
       TAB_A.JOURNAL_ACTION_CLASS       ,
       TAB_A.ROW_ID                     ,
       TAB_A.KY_KEY                     ,
       TAB_A.OPERATION_ID               AS OPERATION_ID_DISP,
       TAB_A.OPERATION_ID               AS OPERATION_ID_NAME_DISP,
       TAB_A.OPERATION_ID               ,
       TAB_B.BASE_TIMESTAMP             ,
       TAB_B.LAST_EXECUTE_TIMESTAMP     ,
       TAB_B.OPERATION_NAME             ,
       TAB_B.OPERATION_DATE             ,

-- 個別項目
★★★COLUMN★★★
-- 個別項目

       TAB_A.ACCESS_AUTH                ,
       TAB_A.NOTE                       ,
       TAB_A.DISUSE_FLAG                ,
       TAB_A.LAST_UPDATE_TIMESTAMP      ,
       TAB_A.LAST_UPDATE_USER
FROM      F_★★★TABLE★★★_HG_JNL   TAB_A
LEFT JOIN G_OPERATION_LIST        TAB_B ON ( TAB_A.OPERATION_ID = TAB_B.OPERATION_ID AND
                                             TAB_B.DISUSE_FLAG = '0' )
;
