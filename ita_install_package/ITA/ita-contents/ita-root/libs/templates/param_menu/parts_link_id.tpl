    $url1 = '★★★LINK_ID_URL1★★★';
    $url2 = '★★★LINK_ID_URL2★★★';
    $url = $url1 . rawurlencode($url2) . '=';
    $c★★★NUMBER★★★ = new LinkIDColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★','★★★ID_TABLE_NAME★★★','★★★PRI_KEY_NAME★★★','★★★ID_COL_NAME★★★',$url, ★★★URL_OPTION★★★, false, '', '', '', '');
    $c★★★NUMBER★★★->setHiddenMainTableColumn(true);
    $c★★★NUMBER★★★->setDescription('★★★INFO★★★');//エクセル・ヘッダでの説明
    ★★★REQUIRED★★★
    ★★★UNIQUED★★★
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('★★★VALUE_NAME★★★');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'★★★ID_TABLE_NAME★★★_JNL',
                                 'TTT_SEARCH_KEY_COLUMN_ID'=>'★★★PRI_KEY_NAME★★★',
                                 'TTT_GET_TARGET_COLUMN_ID'=>'★★★ID_COL_NAME★★★',
                                 'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                                 'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                                 'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                                )
                          );
    $objOT->setTraceQuery($aryTraceQuery);
    $c★★★NUMBER★★★->setOutputType('print_journal_table',$objOT);
    $c★★★NUMBER★★★->setDateFormat(★★★DATE_FORMAT★★★);
    $c★★★NUMBER★★★->setDefaultValue('register_table', '★★★DEFAULT_VALUE★★★');
