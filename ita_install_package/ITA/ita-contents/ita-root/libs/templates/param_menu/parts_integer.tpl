    $objVldt = new IntNumValidator(★★★INT_MIN★★★,★★★INT_MAX★★★,false);
    ★★★PREG_MATCH★★★
    $c★★★NUMBER★★★ = new NumColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★');
    $c★★★NUMBER★★★->setHiddenMainTableColumn(true);
    $c★★★NUMBER★★★->setDescription('★★★INFO★★★');//エクセル・ヘッダでの説明
    $c★★★NUMBER★★★->setValidator($objVldt);
    $c★★★NUMBER★★★->setSubtotalFlag(false);
    $c★★★NUMBER★★★->setDefaultValue('register_table', '★★★DEFAULT_VALUE★★★');
    ★★★REQUIRED★★★
    ★★★UNIQUED★★★
