DROP TABLE IF EXISTS F_★★★TABLE★★★_CONV;
DROP TABLE IF EXISTS F_★★★TABLE★★★_CONV_JNL;
DROP VIEW  IF EXISTS G_★★★TABLE★★★_CONV;
DROP VIEW  IF EXISTS G_★★★TABLE★★★_CONV_JNL;

-- ----更新系テーブル作成(ホストグループ＋ホスト)
CREATE TABLE F_★★★TABLE★★★_CONV (
ROW_ID                        INT             ,
KY_KEY                        INT             ,
OPERATION_ID                  INT             ,
INPUT_ORDER                   INT             ,

-- 個別項目
★★★COLUMN_TYPE★★★
-- 個別項目

NOTE                          VARCHAR(4000)   ,
DISUSE_FLAG                   VARCHAR(1)      ,
LAST_UPDATE_TIMESTAMP         DATETIME(6)     ,
LAST_UPDATE_USER              INT             ,
PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8
;

-- ----表示用VIEW作成(ホストグループ＋ホスト)
CREATE OR REPLACE VIEW G_★★★TABLE★★★_CONV AS
SELECT TAB_A.ROW_ID                     ,
       TAB_A.KY_KEY                     ,
       TAB_A.OPERATION_ID               AS OPERATION_ID_DISP,
       TAB_A.OPERATION_ID               ,
       TAB_B.BASE_TIMESTAMP             ,
       TAB_B.LAST_EXECUTE_TIMESTAMP     ,
       TAB_B.OPERATION_NAME             ,
       TAB_B.OPERATION_DATE             ,
       TAB_A.INPUT_ORDER                ,

-- 個別項目
★★★COLUMN★★★
-- 個別項目

       TAB_A.NOTE                       ,
       TAB_A.DISUSE_FLAG                ,
       TAB_A.LAST_UPDATE_TIMESTAMP      ,
       TAB_A.LAST_UPDATE_USER
FROM      F_★★★TABLE★★★_CONV       TAB_A
LEFT JOIN G_OPERATION_LIST        TAB_B ON ( TAB_A.OPERATION_ID = TAB_B.OPERATION_ID AND
                                             TAB_B.DISUSE_FLAG = '0' )
;


-- ----履歴系テーブル作成(ホストグループ＋ホスト)
CREATE TABLE F_★★★TABLE★★★_CONV_JNL (
JOURNAL_SEQ_NO                INT             ,
JOURNAL_REG_DATETIME          DATETIME(6)     ,
JOURNAL_ACTION_CLASS          VARCHAR(8)      ,
ROW_ID                        INT             ,
KY_KEY                        INT             ,
OPERATION_ID                  INT             ,
INPUT_ORDER                   INT             ,

-- 個別項目
★★★COLUMN_TYPE★★★
-- 個別項目

NOTE                          VARCHAR(4000)   ,
DISUSE_FLAG                   VARCHAR(1)      ,
LAST_UPDATE_TIMESTAMP         DATETIME(6)     ,
LAST_UPDATE_USER              INT             ,
PRIMARY KEY (JOURNAL_SEQ_NO)
)
ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8
;

-- ----表示用VIEW作成(ホストグループ＋ホスト)
CREATE OR REPLACE VIEW G_★★★TABLE★★★_CONV_JNL AS
SELECT TAB_A.JOURNAL_SEQ_NO             ,
       TAB_A.JOURNAL_REG_DATETIME       ,
       TAB_A.JOURNAL_ACTION_CLASS       ,
       TAB_A.ROW_ID                     ,
       TAB_A.KY_KEY                     ,
       TAB_A.OPERATION_ID               AS OPERATION_ID_DISP,
       TAB_A.OPERATION_ID               ,
       TAB_B.BASE_TIMESTAMP             ,
       TAB_B.LAST_EXECUTE_TIMESTAMP     ,
       TAB_B.OPERATION_NAME             ,
       TAB_B.OPERATION_DATE             ,
       TAB_A.INPUT_ORDER                ,

-- 個別項目
★★★COLUMN★★★
-- 個別項目

       TAB_A.NOTE                       ,
       TAB_A.DISUSE_FLAG                ,
       TAB_A.LAST_UPDATE_TIMESTAMP      ,
       TAB_A.LAST_UPDATE_USER
FROM      F_★★★TABLE★★★_CONV_JNL   TAB_A
LEFT JOIN G_OPERATION_LIST        TAB_B ON ( TAB_A.OPERATION_ID = TAB_B.OPERATION_ID AND
                                             TAB_B.DISUSE_FLAG = '0' )
;


-- ----シーケンスオブジェクト作成
INSERT IGNORE INTO A_SEQUENCE (NAME, VALUE) VALUES ('F_★★★TABLE★★★_CONV_RIC', 1);
INSERT IGNORE INTO A_SEQUENCE (NAME, VALUE) VALUES ('F_★★★TABLE★★★_CONV_JSQ', 1);

UPDATE A_SEQUENCE SET VALUE = 1 WHERE NAME = 'F_★★★TABLE★★★_CONV_RIC';
UPDATE A_SEQUENCE SET VALUE = 1 WHERE NAME = 'F_★★★TABLE★★★_CONV_JSQ';


-- ----インデックス
CREATE INDEX IND_F_★★★TABLE★★★_CONV_01 ON F_★★★TABLE★★★_CONV (DISUSE_FLAG);
