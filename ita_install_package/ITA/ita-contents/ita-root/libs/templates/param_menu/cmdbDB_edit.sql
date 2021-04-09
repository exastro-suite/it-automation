-- ---- カラムの追加および削除
★★★ALTER_COLUMN★★★

-- ----表示用VIEW作成(データシート)
CREATE OR REPLACE VIEW G_★★★TABLE★★★_H AS
SELECT TAB_A.ROW_ID                     ,

-- 個別項目
★★★COLUMN★★★
★★★REFERENCE★★★
-- 個別項目

       TAB_A.ACCESS_AUTH                ,
       TAB_A.NOTE                       ,
       TAB_A.DISUSE_FLAG                ,
       TAB_A.LAST_UPDATE_TIMESTAMP      ,
       TAB_A.LAST_UPDATE_USER
FROM      F_★★★TABLE★★★_H       TAB_A
;

-- ----履歴系VIEW作成(データシート)
CREATE OR REPLACE VIEW G_★★★TABLE★★★_H_JNL AS
SELECT TAB_A.JOURNAL_SEQ_NO             ,
       TAB_A.JOURNAL_REG_DATETIME       ,
       TAB_A.JOURNAL_ACTION_CLASS       ,
       TAB_A.ROW_ID                     ,

-- 個別項目
★★★COLUMN★★★
★★★REFERENCE★★★
-- 個別項目

       TAB_A.ACCESS_AUTH                ,
       TAB_A.NOTE                       ,
       TAB_A.DISUSE_FLAG                ,
       TAB_A.LAST_UPDATE_TIMESTAMP      ,
       TAB_A.LAST_UPDATE_USER
FROM   F_★★★TABLE★★★_H_JNL TAB_A
;
