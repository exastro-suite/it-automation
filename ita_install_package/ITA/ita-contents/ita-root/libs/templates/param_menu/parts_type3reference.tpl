    $url1 = '★★★LINK_ID_URL1★★★';
    $url2 = '★★★LINK_ID_URL2★★★';
    $url = $url1 . rawurlencode($url2) . '=';
    $c★★★NUMBER★★★ = new LinkIDColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★','★★★ID_TABLE_NAME★★★','OPERATION_ID','★★★ID_COL_NAME★★★',$url, false, false, '', '', '', '');
    $c★★★NUMBER★★★->setHiddenMainTableColumn(false);
    $c★★★NUMBER★★★->setAllowSendFromFile(false); //excelでの更新を禁止
    $c★★★NUMBER★★★->setDescription('★★★INFO★★★');//エクセル・ヘッダでの説明
    $c★★★NUMBER★★★->getOutputType("update_table")->setVisible(false);
    $c★★★NUMBER★★★->getOutputType("register_table")->setVisible(false);
    $c★★★NUMBER★★★->getOutputType("delete_table")->setVisible(false);
    $c★★★NUMBER★★★->getOutputType("excel")->setVisible(true);
    $c★★★NUMBER★★★->getOutputType("csv")->setVisible(false);
    $c★★★NUMBER★★★->setDateFormat(★★★DATE_FORMAT★★★);
    ★★★REFERENCE_ITEM_PASSWORD★★★
