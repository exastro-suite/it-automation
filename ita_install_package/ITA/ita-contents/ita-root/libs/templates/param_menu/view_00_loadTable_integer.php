    $objVldt = new IntNumValidator(★★★INT_MIN★★★,★★★INT_MAX★★★,false);
    ★★★PREG_MATCH★★★
    $c★★★NUMBER★★★ = new NumColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★');
    $c★★★NUMBER★★★->setHiddenMainTableColumn(true);
    $c★★★NUMBER★★★->setDescription('★★★INFO★★★');//エクセル・ヘッダでの説明
    $c★★★NUMBER★★★->getOutputType("filter_table")->setVisible(false);
    $c★★★NUMBER★★★->setValidator($objVldt);
    $c★★★NUMBER★★★->setSubtotalFlag(false);
