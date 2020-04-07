    $objVldt = new DateTimeValidator(null,null);
    ★★★PREG_MATCH★★★
    $c★★★NUMBER★★★ = new DateTimeColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★');
    $c★★★NUMBER★★★->setHiddenMainTableColumn(true);
    $c★★★NUMBER★★★->setDescription('★★★INFO★★★');//エクセル・ヘッダでの説明
    $c★★★NUMBER★★★->getOutputType("filter_table")->setVisible(false);
    $c★★★NUMBER★★★->setValidator($objVldt);
