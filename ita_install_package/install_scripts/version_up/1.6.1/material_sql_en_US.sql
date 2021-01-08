CREATE OR REPLACE VIEW G_FILE_MASTER AS 
SELECT TAB_A.FILE_ID                                                             ,
       TAB_A.FILE_ID AS FILE_ID_CLONE                                            ,
       TAB_A.FILE_NAME                                                           ,
       TAB_A.DIR_ID                                                              ,
       CONCAT(TAB_B.DIR_NAME_FULLPATH,TAB_A.FILE_NAME) AS FILE_NAME_FULLPATH                                                 ,
       TAB_A.AUTO_RETURN_FLAG                                                    ,
       TAB_A.CHMOD                                                               ,
       TAB_A.GROUP_AUTH                                                          ,
       TAB_A.USER_AUTH                                                           ,
       TAB_A.DIR_USAGE                                                           ,
       TAB_A.ACCESS_AUTH                                                         ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01                                       ,
       TAB_A.NOTE                                                                ,
       TAB_A.DISUSE_FLAG                                                         ,
       TAB_A.LAST_UPDATE_TIMESTAMP                                               ,
       TAB_A.LAST_UPDATE_USER 
FROM   F_FILE_MASTER TAB_A 
       LEFT JOIN F_DIR_MASTER TAB_B
       ON        (TAB_A.DIR_ID = TAB_B.DIR_ID)                                   ;

CREATE OR REPLACE VIEW G_FILE_MASTER_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                                                      ,
       TAB_A.JOURNAL_REG_DATETIME                                                ,
       TAB_A.JOURNAL_ACTION_CLASS                                                ,
       TAB_A.FILE_ID                                                             ,
       TAB_A.FILE_ID AS FILE_ID_CLONE                                        ,       
       TAB_A.FILE_NAME                                                           ,
       TAB_A.DIR_ID                                                              ,
       CONCAT(TAB_B.DIR_NAME_FULLPATH,TAB_A.FILE_NAME) AS FILE_NAME_FULLPATH                                                 ,
       TAB_A.AUTO_RETURN_FLAG                                                    ,
       TAB_A.CHMOD                                                               ,
       TAB_A.GROUP_AUTH                                                          ,
       TAB_A.USER_AUTH                                                           ,
       TAB_A.DIR_USAGE                                                           ,
       TAB_A.ACCESS_AUTH                                                         ,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01                                       ,
       TAB_A.NOTE                                                                ,
       TAB_A.DISUSE_FLAG                                                         ,
       TAB_A.LAST_UPDATE_TIMESTAMP                                               ,
       TAB_A.LAST_UPDATE_USER 
FROM   F_FILE_MASTER_JNL TAB_A 
       LEFT JOIN F_DIR_MASTER TAB_B
       ON        (TAB_A.DIR_ID = TAB_B.DIR_ID)                                   ;


CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_3 AS 
SELECT TAB_A.FILE_M_ID             ,
       TAB_A.FILE_STATUS_ID        ,
       TAB_A.FILE_ID               ,
       TAB_A.FILE_ID AS FILE_ID_CLONE,
       TAB_B.FILE_NAME_FULLPATH    ,
       TAB_A.REQUIRE_DATE          ,
       TAB_A.REQUIRE_USER_ID       ,
       TAB_A.REQUIRE_TICKET        ,
       TAB_A.REQUIRE_ABSTRUCT      ,
       TAB_A.REQUIRE_SCHEDULEDATE  ,
       TAB_A.ASSIGN_DATE           ,
       TAB_A.ASSIGN_USER_ID        ,
       TAB_A.ASSIGN_FILE           ,
       TAB_A.ASSIGN_REVISION       ,
       TAB_A.RETURN_DATE           ,
       TAB_A.RETURN_USER_ID        ,
       TAB_A.RETURN_FILE           ,
       TAB_A.RETURN_DIFF           ,
       TAB_A.RETURN_TESTCASES      ,
       TAB_A.RETURN_EVIDENCES      ,
       TAB_A.CLOSE_DATE            ,
       TAB_A.CLOSE_USER_ID         ,
       TAB_A.CLOSE_REVISION        ,
       TAB_A.ACCESS_AUTH           ,
       TAB_A.NOTE                  ,
       TAB_A.DISUSE_FLAG           ,
       TAB_A.LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER 
FROM   F_FILE_MANAGEMENT TAB_A
LEFT JOIN G_FILE_MASTER TAB_B ON (TAB_A.FILE_ID = TAB_B.FILE_ID) 
WHERE  FILE_STATUS_ID 
IN     (SELECT FILE_STATUS_ID 
        FROM   F_MM_STATUS_MASTER 
        WHERE  DISUSE_FLAG = '0' 
        AND    DISP_FLAG_3 = 1 )   ;


CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO        ,
       TAB_A.JOURNAL_REG_DATETIME  ,
       TAB_A.JOURNAL_ACTION_CLASS  ,
       TAB_A.FILE_M_ID             ,
       TAB_A.FILE_STATUS_ID        ,
       TAB_A.FILE_ID               ,
       TAB_A.FILE_ID               FILE_ID_CLONE,
       TAB_B.FILE_NAME_FULLPATH    ,
       TAB_A.REQUIRE_DATE          ,
       TAB_A.REQUIRE_USER_ID       ,
       TAB_A.REQUIRE_TICKET        ,
       TAB_A.REQUIRE_ABSTRUCT      ,
       TAB_A.REQUIRE_SCHEDULEDATE  ,
       TAB_A.ASSIGN_DATE           ,
       TAB_A.ASSIGN_USER_ID        ,
       TAB_A.ASSIGN_FILE           ,
       TAB_A.ASSIGN_REVISION       ,
       TAB_A.RETURN_DATE           ,
       TAB_A.RETURN_USER_ID        ,
       TAB_A.RETURN_FILE           ,
       TAB_A.RETURN_DIFF           ,
       TAB_A.RETURN_TESTCASES      ,
       TAB_A.RETURN_EVIDENCES      ,
       TAB_A.CLOSE_DATE            ,
       TAB_A.CLOSE_USER_ID         ,
       TAB_A.CLOSE_REVISION        ,
       TAB_A.ACCESS_AUTH           ,
       TAB_A.NOTE                  ,
       TAB_A.DISUSE_FLAG           ,
       TAB_A.LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER 
FROM F_FILE_MANAGEMENT_JNL TAB_A
LEFT JOIN G_FILE_MASTER TAB_B ON (TAB_A.FILE_ID = TAB_B.FILE_ID) 
WHERE FILE_STATUS_ID 
IN    (SELECT FILE_STATUS_ID 
       FROM   F_MM_STATUS_MASTER 
       WHERE  DISUSE_FLAG = '0' 
       AND    DISP_FLAG_1 = 1 )    ;


CREATE OR REPLACE VIEW G_FILE_MANAGEMENT_NEWEST AS 
SELECT TAB_A.FILE_M_ID             ,
       TAB_A.FILE_M_ID AS FILE_M_ID_CLONE,
       TAB_A.FILE_ID               ,
       TAB_A.RETURN_FILE           ,
       TAB_C.FILE_NAME_FULLPATH    ,
       TAB_A.CLOSE_DATE            ,
       TAB_A.RETURN_USER_ID        ,
       TAB_A.CLOSE_REVISION        ,
       TAB_A.ACCESS_AUTH           ,
       TAB_C.ACCESS_AUTH AS ACCESS_AUTH_01,
       TAB_A.NOTE                  ,
       TAB_A.DISUSE_FLAG           ,
       TAB_A.LAST_UPDATE_TIMESTAMP ,
       TAB_A.LAST_UPDATE_USER      ,
       IF(TAB_A.CLOSE_DATE = (SELECT MAX(TAB_B.CLOSE_DATE) FROM G_FILE_MANAGEMENT_UNION TAB_B WHERE TAB_A.FILE_ID = TAB_B.FILE_ID AND TAB_B.DISUSE_FLAG='0'), "●", "") NEWEST_FLAG
FROM   G_FILE_MANAGEMENT_UNION TAB_A
LEFT JOIN G_FILE_MASTER TAB_C ON (TAB_A.FILE_ID = TAB_C.FILE_ID) 
WHERE TAB_C.DISUSE_FLAG='0'
;
