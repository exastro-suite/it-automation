    $objVldt = new SingleTextValidator(0,★★★LINK_MAX_LENGTH★★★,false);
    $c★★★NUMBER★★★ = new HostInsideLinkTextColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★');
    $c★★★NUMBER★★★->setHiddenMainTableColumn(true);
    $c★★★NUMBER★★★->setDescription('★★★INFO★★★');//エクセル・ヘッダでの説明
    $c★★★NUMBER★★★->setValidator($objVldt);
    $c★★★NUMBER★★★->setDefaultValue('register_table', '★★★DEFAULT_VALUE★★★');
    ★★★REQUIRED★★★
    ★★★UNIQUED★★★
