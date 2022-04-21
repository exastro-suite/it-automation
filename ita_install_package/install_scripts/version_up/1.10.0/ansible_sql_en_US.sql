ALTER TABLE B_ANSIBLE_IF_INFO ADD COLUMN ANS_GIT_HOSTNAME VARCHAR(128)  AFTER ANSIBLE_TAILLOG_LINES;
ALTER TABLE B_ANSIBLE_IF_INFO ADD COLUMN ANS_GIT_USER VARCHAR(128)  AFTER ANS_GIT_HOSTNAME;
ALTER TABLE B_ANSIBLE_IF_INFO ADD COLUMN ANS_GIT_SSH_KEY_FILE VARCHAR(256)  AFTER ANS_GIT_USER;
ALTER TABLE B_ANSIBLE_IF_INFO ADD COLUMN ANS_GIT_SSH_KEY_FILE_PASSPHRASE TEXT  AFTER ANS_GIT_SSH_KEY_FILE;
ALTER TABLE B_ANSIBLE_IF_INFO_JNL ADD COLUMN ANS_GIT_HOSTNAME VARCHAR(128)  AFTER ANSIBLE_TAILLOG_LINES;
ALTER TABLE B_ANSIBLE_IF_INFO_JNL ADD COLUMN ANS_GIT_USER VARCHAR(128)  AFTER ANS_GIT_HOSTNAME;
ALTER TABLE B_ANSIBLE_IF_INFO_JNL ADD COLUMN ANS_GIT_SSH_KEY_FILE VARCHAR(256)  AFTER ANS_GIT_USER;
ALTER TABLE B_ANSIBLE_IF_INFO_JNL ADD COLUMN ANS_GIT_SSH_KEY_FILE_PASSPHRASE TEXT  AFTER ANS_GIT_SSH_KEY_FILE;



-- ------------------------------
-- -- Tower 実行環境マスタ
-- ------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_TWR_EXECUTION_ENVIRONMENT ( 
  ROW_ID                          INT                               , 
  EXECUTION_ENVIRONMENT_NAME      VARCHAR (512)                     , 
  EXECUTION_ENVIRONMENT_NO        INT                               , 
  DISP_SEQ                        INT                               , 
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    , 
  DISUSE_FLAG                     VARCHAR (1)                       , 
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , 
  LAST_UPDATE_USER                INT                               , 
  PRIMARY KEY (ROW_ID) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_TWR_EXECUTION_ENVIRONMENT_JNL ( 
  JOURNAL_SEQ_NO                  INT                               , 
  JOURNAL_REG_DATETIME            DATETIME(6)                       , 
  JOURNAL_ACTION_CLASS            VARCHAR (8)                       , 
  ROW_ID                          INT                               , 
  EXECUTION_ENVIRONMENT_NAME      VARCHAR (512)                     , 
  EXECUTION_ENVIRONMENT_NO        INT                               , 
  DISP_SEQ                        INT                               , 
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    , 
  DISUSE_FLAG                     VARCHAR (1)                       , 
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , 
  LAST_UPDATE_USER                INT                               , 
  PRIMARY KEY (JOURNAL_SEQ_NO) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 履歴系テーブル作成----


CREATE OR REPLACE VIEW D_ANSIBLE_TOWER_IF_INFO AS 
SELECT 
  TAB_A.*,
  TAB_B.ANSTWR_HOSTNAME,
  TAB_B.ANSTWR_LOGIN_AUTH_TYPE,
  TAB_B.ANSTWR_LOGIN_USER,
  TAB_B.ANSTWR_LOGIN_PASSWORD,
  TAB_B.ANSTWR_LOGIN_SSH_KEY_FILE,
  TAB_B.ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE,
  TAB_B.ANSTWR_ISOLATED_TYPE
FROM
  B_ANSIBLE_IF_INFO           TAB_A
  LEFT JOIN (
             SELECT * 
             FROM B_ANS_TWR_HOST 
             WHERE DISUSE_FLAG = '0'
            ) TAB_B ON ( TAB_A.ANSTWR_HOST_ID = TAB_B.ANSTWR_HOST_ID );
  
CREATE OR REPLACE VIEW D_ANSIBLE_TOWER_IF_INFO_JNL AS 
SELECT 
  TAB_A.*,
  TAB_B.ANSTWR_HOSTNAME,
  TAB_B.ANSTWR_LOGIN_AUTH_TYPE,
  TAB_B.ANSTWR_LOGIN_USER,
  TAB_B.ANSTWR_LOGIN_PASSWORD,
  TAB_B.ANSTWR_LOGIN_SSH_KEY_FILE,
  TAB_B.ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE,
  TAB_B.ANSTWR_ISOLATED_TYPE
FROM
  B_ANSIBLE_IF_INFO_JNL         TAB_A
  LEFT JOIN (
             SELECT * 
             FROM B_ANS_TWR_HOST_JNL
             WHERE DISUSE_FLAG = '0'
            ) TAB_B ON ( TAB_A.ANSTWR_HOST_ID = TAB_B.ANSTWR_HOST_ID );


ALTER TABLE C_ANSIBLE_LNS_EXE_INS_MNG ADD COLUMN I_EXECUTION_ENVIRONMENT_NAME VARCHAR(512)  AFTER I_ENGINE_VIRTUALENV_NAME;
ALTER TABLE C_ANSIBLE_LNS_EXE_INS_MNG ADD COLUMN I_ANSIBLE_CONFIG_FILE VARCHAR(512)  AFTER I_EXECUTION_ENVIRONMENT_NAME;
ALTER TABLE C_ANSIBLE_LNS_EXE_INS_MNG_JNL ADD COLUMN I_EXECUTION_ENVIRONMENT_NAME VARCHAR(512)  AFTER I_ENGINE_VIRTUALENV_NAME;
ALTER TABLE C_ANSIBLE_LNS_EXE_INS_MNG_JNL ADD COLUMN I_ANSIBLE_CONFIG_FILE VARCHAR(512)  AFTER I_EXECUTION_ENVIRONMENT_NAME;


CREATE OR REPLACE VIEW D_ANSIBLE_LNS_IF_INFO     AS 
SELECT * 
FROM B_ANSIBLE_IF_INFO;

CREATE OR REPLACE VIEW D_ANSIBLE_LNS_IF_INFO_JNL AS 
SELECT * 
FROM B_ANSIBLE_IF_INFO_JNL;


CREATE OR REPLACE VIEW E_ANSIBLE_LNS_PATTERN AS 
SELECT 
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_WINRM_ID                  ,
        ANS_PLAYBOOK_HED_DEF          ,
        ANS_EXEC_OPTIONS              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 3;

CREATE OR REPLACE VIEW E_ANSIBLE_LNS_PATTERN_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                ,
        JOURNAL_REG_DATETIME          ,
        JOURNAL_ACTION_CLASS          ,
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_WINRM_ID                  ,
        ANS_PLAYBOOK_HED_DEF          ,
        ANS_EXEC_OPTIONS              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH_JNL TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 3;

CREATE OR REPLACE VIEW E_ANSIBLE_LNS_EXE_INS_MNG AS
SELECT 
         TAB_A.EXECUTION_NO              ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM C_ANSIBLE_LNS_EXE_INS_MNG       TAB_A
LEFT JOIN E_ANSIBLE_LNS_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_LNS_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_LNS_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;

CREATE OR REPLACE VIEW E_ANSIBLE_LNS_EXE_INS_MNG_JNL AS 
SELECT 
         TAB_A.JOURNAL_SEQ_NO            ,
         TAB_A.JOURNAL_REG_DATETIME      ,
         TAB_A.JOURNAL_ACTION_CLASS      ,
         TAB_A.EXECUTION_NO              ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF    ,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER           
FROM C_ANSIBLE_LNS_EXE_INS_MNG_JNL   TAB_A
LEFT JOIN E_ANSIBLE_LNS_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_LNS_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_LNS_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;


ALTER TABLE C_ANSIBLE_PNS_EXE_INS_MNG ADD COLUMN I_EXECUTION_ENVIRONMENT_NAME VARCHAR(512)  AFTER I_ENGINE_VIRTUALENV_NAME;
ALTER TABLE C_ANSIBLE_PNS_EXE_INS_MNG ADD COLUMN I_ANSIBLE_CONFIG_FILE VARCHAR(512)  AFTER I_EXECUTION_ENVIRONMENT_NAME;
ALTER TABLE C_ANSIBLE_PNS_EXE_INS_MNG_JNL ADD COLUMN I_EXECUTION_ENVIRONMENT_NAME VARCHAR(512)  AFTER I_ENGINE_VIRTUALENV_NAME;
ALTER TABLE C_ANSIBLE_PNS_EXE_INS_MNG_JNL ADD COLUMN I_ANSIBLE_CONFIG_FILE VARCHAR(512)  AFTER I_EXECUTION_ENVIRONMENT_NAME;


CREATE OR REPLACE VIEW D_ANSIBLE_PNS_IF_INFO     AS 
SELECT * 
FROM B_ANSIBLE_IF_INFO;

CREATE OR REPLACE VIEW D_ANSIBLE_PNS_IF_INFO_JNL AS 
SELECT * 
FROM B_ANSIBLE_IF_INFO_JNL;


CREATE OR REPLACE VIEW E_ANSIBLE_PNS_PATTERN AS 
SELECT 
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        ANS_EXEC_OPTIONS              ,
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 4;

CREATE OR REPLACE VIEW E_ANSIBLE_PNS_PATTERN_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                ,
        JOURNAL_REG_DATETIME          ,
        JOURNAL_ACTION_CLASS          ,
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        ANS_EXEC_OPTIONS              ,
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH_JNL TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 4;

CREATE OR REPLACE VIEW E_ANSIBLE_PNS_EXE_INS_MNG AS
SELECT 
         TAB_A.EXECUTION_NO              ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF    ,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM C_ANSIBLE_PNS_EXE_INS_MNG       TAB_A
LEFT JOIN E_ANSIBLE_PNS_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_PNS_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_PNS_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;

CREATE OR REPLACE VIEW E_ANSIBLE_PNS_EXE_INS_MNG_JNL AS 
SELECT 
         TAB_A.JOURNAL_SEQ_NO            ,
         TAB_A.JOURNAL_REG_DATETIME      ,
         TAB_A.JOURNAL_ACTION_CLASS      ,
         TAB_A.EXECUTION_NO              ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF    ,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER           
FROM C_ANSIBLE_PNS_EXE_INS_MNG_JNL   TAB_A
LEFT JOIN E_ANSIBLE_PNS_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_PNS_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_PNS_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;


ALTER TABLE C_ANSIBLE_LRL_EXE_INS_MNG ADD COLUMN I_EXECUTION_ENVIRONMENT_NAME VARCHAR(512)  AFTER I_ENGINE_VIRTUALENV_NAME;
ALTER TABLE C_ANSIBLE_LRL_EXE_INS_MNG ADD COLUMN I_ANSIBLE_CONFIG_FILE VARCHAR(512)  AFTER I_EXECUTION_ENVIRONMENT_NAME;
ALTER TABLE C_ANSIBLE_LRL_EXE_INS_MNG_JNL ADD COLUMN I_EXECUTION_ENVIRONMENT_NAME VARCHAR(512)  AFTER I_ENGINE_VIRTUALENV_NAME;
ALTER TABLE C_ANSIBLE_LRL_EXE_INS_MNG_JNL ADD COLUMN I_ANSIBLE_CONFIG_FILE VARCHAR(512)  AFTER I_EXECUTION_ENVIRONMENT_NAME;


CREATE OR REPLACE VIEW E_ANSIBLE_LRL_PATTERN AS 
SELECT 
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_WINRM_ID                  ,
        ANS_PLAYBOOK_HED_DEF          ,
        ANS_EXEC_OPTIONS              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 5;

CREATE OR REPLACE VIEW E_ANSIBLE_LRL_PATTERN_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                ,
        JOURNAL_REG_DATETIME          ,
        JOURNAL_ACTION_CLASS          ,
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_WINRM_ID                  ,
        ANS_PLAYBOOK_HED_DEF          ,
        ANS_EXEC_OPTIONS              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH_JNL TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 5;

CREATE OR REPLACE VIEW E_ANSIBLE_LRL_EXE_INS_MNG AS
SELECT 
         TAB_A.EXECUTION_NO              ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF    ,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM C_ANSIBLE_LRL_EXE_INS_MNG       TAB_A
LEFT JOIN E_ANSIBLE_LRL_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_LRL_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_LRL_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;

CREATE OR REPLACE VIEW E_ANSIBLE_LRL_EXE_INS_MNG_JNL AS 
SELECT 
         TAB_A.JOURNAL_SEQ_NO            ,
         TAB_A.JOURNAL_REG_DATETIME      ,
         TAB_A.JOURNAL_ACTION_CLASS      ,
         TAB_A.EXECUTION_NO              ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF    ,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER           
FROM C_ANSIBLE_LRL_EXE_INS_MNG_JNL TAB_A
LEFT JOIN E_ANSIBLE_LRL_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_LRL_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_LRL_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;


INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_EXECUTION_ENVIRONMENT_RIC',1,NULL,2100290029,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_EXECUTION_ENVIRONMENT_JSQ',1,NULL,2100290030,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));


UPDATE A_MENU_LIST SET  MENU_NAME='Ansible Automation Controller host list' WHERE MENU_ID=2100040708;
UPDATE A_MENU_LIST_JNL SET  MENU_NAME='Ansible Automation Controller host list' WHERE MENU_ID=2100040708;


UPDATE A_ACCOUNT_LIST SET USERNAME_JP='Ansible Automation Controller data sync procedure' WHERE USER_ID=-121006;
UPDATE A_ACCOUNT_LIST_JNL SET USERNAME_JP='Ansible Automation Controller data sync procedure' WHERE USER_ID=-121006;


UPDATE B_ANSIBLE_IF_INFO SET ANS_GIT_USER='awx', ANS_GIT_SSH_KEY_FILE='rsa_awx_key' WHERE ANSIBLE_IF_INFO_ID=1;
UPDATE B_ANSIBLE_IF_INFO_JNL SET ANS_GIT_USER='awx', ANS_GIT_SSH_KEY_FILE='rsa_awx_key' WHERE ANSIBLE_IF_INFO_ID=1;


UPDATE B_ANSIBLE_EXEC_MODE SET NAME='Ansible Core' WHERE ID=1;
UPDATE B_ANSIBLE_EXEC_MODE_JNL SET NAME='Ansible Core' WHERE ID=1;
INSERT INTO B_ANSIBLE_EXEC_MODE (ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Ansible Automation Controller',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_EXEC_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'Ansible Automation Controller',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
