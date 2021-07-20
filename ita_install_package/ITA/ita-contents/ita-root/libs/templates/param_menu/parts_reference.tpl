
    $c★★★REFERENCE_NUMBER★★★ = new IDColumn('★★★CLONE_VALUE_NAME★★★','★★★CLONE_DISP_NAME★★★','★★★CLONE_ID_TABLE_NAME★★★','★★★CLONE_PRI_KEY_NAME★★★','★★★CLONE_ID_COL_NAME★★★','');
    $c★★★REFERENCE_NUMBER★★★->setHiddenMainTableColumn(false);
    $c★★★REFERENCE_NUMBER★★★->setAllowSendFromFile(false); //excelでの更新を禁止
    $c★★★REFERENCE_NUMBER★★★->setDescription('★★★CLONE_INFO★★★');//エクセル・ヘッダでの説明
    $c★★★REFERENCE_NUMBER★★★->getOutputType("update_table")->setVisible(false);
    $c★★★REFERENCE_NUMBER★★★->getOutputType("register_table")->setVisible(false);
    $c★★★REFERENCE_NUMBER★★★->getOutputType("delete_table")->setVisible(false);
    $c★★★REFERENCE_NUMBER★★★->getOutputType("excel")->setVisible(true);
    $c★★★REFERENCE_NUMBER★★★->getOutputType("csv")->setVisible(false);
    $c★★★REFERENCE_NUMBER★★★->setDateFormat(★★★REFERENCE_DATE_FORMAT★★★);
    ★★★REFERENCE_ITEM_PASSWORD★★★
