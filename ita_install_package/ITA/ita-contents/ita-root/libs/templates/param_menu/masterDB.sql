DROP TABLE IF EXISTS F_★★★TABLE★★★;
DROP TABLE IF EXISTS F_★★★TABLE★★★_JNL;

-- ----更新系テーブル作成(ホストのみ)
CREATE TABLE F_★★★TABLE★★★ (
ROW_ID                        INT             ,

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


-- ----履歴系テーブル作成(ホストのみ)
CREATE TABLE F_★★★TABLE★★★_JNL (
JOURNAL_SEQ_NO                INT             ,
JOURNAL_REG_DATETIME          DATETIME(6)     ,
JOURNAL_ACTION_CLASS          VARCHAR(8)      ,
ROW_ID                        INT             ,

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

-- ----シーケンスオブジェクト作成
INSERT IGNORE INTO A_SEQUENCE (NAME, VALUE) VALUES ('F_★★★TABLE★★★_RIC' , 1);
INSERT IGNORE INTO A_SEQUENCE (NAME, VALUE) VALUES ('F_★★★TABLE★★★_JSQ' , 1);

UPDATE A_SEQUENCE SET VALUE = 1 WHERE NAME = 'F_★★★TABLE★★★_RIC';
UPDATE A_SEQUENCE SET VALUE = 1 WHERE NAME = 'F_★★★TABLE★★★_JSQ';
