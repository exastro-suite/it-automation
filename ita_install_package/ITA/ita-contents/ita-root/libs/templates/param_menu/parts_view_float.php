    $objVldt = new FloatNumValidator(★★★FLOAT_MAX★★★,★★★FLOAT_MIN★★★,★★★FLOAT_DIGIT★★★);
    ★★★PREG_MATCH★★★
    $c★★★NUMBER★★★ = new NumColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★',★★★FLOAT_DIGIT★★★);
    $c★★★NUMBER★★★->setHiddenMainTableColumn(true);
    $c★★★NUMBER★★★->setDescription('★★★INFO★★★');//エクセル・ヘッダでの説明
    $c★★★NUMBER★★★->getOutputType("filter_table")->setVisible(false);
    $c★★★NUMBER★★★->setValidator($objVldt);
    $c★★★NUMBER★★★->setSubtotalFlag(false);
