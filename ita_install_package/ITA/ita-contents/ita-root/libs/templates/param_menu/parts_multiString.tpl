    $objVldt = new MultiTextValidator(0,★★★MULTI_MAX_LENGTH★★★,false);
    ★★★MULTI_PREG_MATCH★★★
    $c★★★NUMBER★★★ = new MultiTextColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★');
    $c★★★NUMBER★★★->setHiddenMainTableColumn(true);
    $c★★★NUMBER★★★->setDescription('★★★INFO★★★');//エクセル・ヘッダでの説明
    $c★★★NUMBER★★★->setValidator($objVldt);
    $c★★★NUMBER★★★->setDefaultValue('register_table', '★★★DEFAULT_VALUE★★★');
    ★★★REQUIRED★★★
    ★★★UNIQUED★★★
