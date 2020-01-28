    $objVldt = new SingleTextValidator(0,★★★SIZE★★★,false);
    ★★★PREG_MATCH★★★
    $c★★★NUMBER★★★ = new TextColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★');
    $c★★★NUMBER★★★->setHiddenMainTableColumn(true);
    $c★★★NUMBER★★★->setDescription('★★★INFO★★★');//エクセル・ヘッダでの説明
    $c★★★NUMBER★★★->setValidator($objVldt);
    ★★★REQUIRED★★★
    ★★★UNIQUED★★★
