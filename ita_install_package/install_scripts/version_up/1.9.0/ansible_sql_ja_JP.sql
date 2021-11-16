ALTER TABLE B_ANSIBLE_LNS_VARS_ASSIGN ADD COLUMN VARS_ENTRY_FILE TEXT AFTER VARS_ENTRY;
ALTER TABLE B_ANSIBLE_LNS_VARS_ASSIGN_JNL ADD COLUMN VARS_ENTRY_FILE TEXT AFTER VARS_ENTRY;


ALTER TABLE C_ANSIBLE_LNS_EXE_INS_MNG MODIFY COLUMN I_VIRTUALENV_NAME VARCHAR(512);
ALTER TABLE C_ANSIBLE_LNS_EXE_INS_MNG ADD COLUMN I_ENGINE_VIRTUALENV_NAME VARCHAR(512) AFTER I_VIRTUALENV_NAME;
ALTER TABLE C_ANSIBLE_LNS_EXE_INS_MNG_JNL MODIFY COLUMN I_VIRTUALENV_NAME VARCHAR(512);
ALTER TABLE C_ANSIBLE_LNS_EXE_INS_MNG_JNL ADD COLUMN I_ENGINE_VIRTUALENV_NAME VARCHAR(512) AFTER I_VIRTUALENV_NAME;

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


ALTER TABLE B_ANSIBLE_PNS_VARS_ASSIGN ADD COLUMN VARS_ENTRY_FILE TEXT AFTER VARS_ENTRY;
ALTER TABLE B_ANSIBLE_PNS_VARS_ASSIGN_JNL ADD COLUMN VARS_ENTRY_FILE TEXT AFTER VARS_ENTRY;


ALTER TABLE C_ANSIBLE_PNS_EXE_INS_MNG MODIFY COLUMN I_VIRTUALENV_NAME VARCHAR(512);
ALTER TABLE C_ANSIBLE_PNS_EXE_INS_MNG ADD COLUMN I_ENGINE_VIRTUALENV_NAME VARCHAR(512) AFTER I_VIRTUALENV_NAME;
ALTER TABLE C_ANSIBLE_PNS_EXE_INS_MNG_JNL MODIFY COLUMN I_VIRTUALENV_NAME VARCHAR(512);
ALTER TABLE C_ANSIBLE_PNS_EXE_INS_MNG_JNL ADD COLUMN I_ENGINE_VIRTUALENV_NAME VARCHAR(512) AFTER I_VIRTUALENV_NAME;


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


ALTER TABLE C_ANSIBLE_LRL_EXE_INS_MNG MODIFY COLUMN I_VIRTUALENV_NAME VARCHAR(512);
ALTER TABLE C_ANSIBLE_LRL_EXE_INS_MNG ADD COLUMN I_ENGINE_VIRTUALENV_NAME VARCHAR(512) AFTER I_VIRTUALENV_NAME;
ALTER TABLE C_ANSIBLE_LRL_EXE_INS_MNG_JNL MODIFY COLUMN I_VIRTUALENV_NAME VARCHAR(512);
ALTER TABLE C_ANSIBLE_LRL_EXE_INS_MNG_JNL ADD COLUMN I_ENGINE_VIRTUALENV_NAME VARCHAR(512) AFTER I_VIRTUALENV_NAME;


ALTER TABLE B_ANSIBLE_LRL_VARS_ASSIGN ADD COLUMN VARS_ENTRY_FILE TEXT AFTER VARS_ENTRY;
ALTER TABLE B_ANSIBLE_LRL_VARS_ASSIGN_JNL ADD COLUMN VARS_ENTRY_FILE TEXT AFTER VARS_ENTRY;


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


CREATE OR REPLACE VIEW D_B_ANSIBLE_LNS_VARS_ASSIGN AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID
FROM
  B_ANSIBLE_LNS_VARS_ASSIGN TAB_A;
CREATE OR REPLACE VIEW D_B_ANSIBLE_LNS_VARS_ASSIGN_JNL AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID
FROM
  B_ANSIBLE_LNS_VARS_ASSIGN_JNL TAB_A;


CREATE OR REPLACE VIEW D_B_ANSIBLE_PNS_VARS_ASSIGN AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID
FROM
  B_ANSIBLE_PNS_VARS_ASSIGN TAB_A;
CREATE OR REPLACE VIEW D_B_ANSIBLE_PNS_VARS_ASSIGN_JNL AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID
FROM
  B_ANSIBLE_PNS_VARS_ASSIGN_JNL TAB_A;


CREATE OR REPLACE VIEW D_B_ANSIBLE_LRL_VARS_ASSIGN AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID,
  TAB_A.COL_SEQ_COMBINATION_ID  REST_COL_SEQ_COMBINATION_ID
FROM
  B_ANSIBLE_LRL_VARS_ASSIGN TAB_A;
  
CREATE OR REPLACE VIEW D_B_ANSIBLE_LRL_VARS_ASSIGN_JNL AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID,
  TAB_A.COL_SEQ_COMBINATION_ID  REST_COL_SEQ_COMBINATION_ID
FROM
  B_ANSIBLE_LRL_VARS_ASSIGN_JNL TAB_A;


CREATE OR REPLACE VIEW D_ANSIBLE_EXE_INS_MNG     AS 
SELECT
  'Legacy'      as DRIVER_NAME, 'L' as DRIVER_ID, EXECUTION_NO, STATUS_ID, TIME_BOOK, DISUSE_FLAG, LAST_UPDATE_TIMESTAMP
FROM
  C_ANSIBLE_LNS_EXE_INS_MNG
WHERE
  DISUSE_FLAG = '0'
UNION
SELECT
  'Pioneer'     as DRIVER_NAME, 'P' as DRIVER_ID, EXECUTION_NO, STATUS_ID, TIME_BOOK, DISUSE_FLAG, LAST_UPDATE_TIMESTAMP
FROM
  C_ANSIBLE_PNS_EXE_INS_MNG
WHERE
  DISUSE_FLAG = '0'
UNION
SELECT
  'Legacy-Role' as DRIVER_NAME, 'R' as DRIVER_ID, EXECUTION_NO, STATUS_ID, TIME_BOOK, DISUSE_FLAG, LAST_UPDATE_TIMESTAMP
FROM
  C_ANSIBLE_LRL_EXE_INS_MNG
WHERE
  DISUSE_FLAG = '0';
