    $objVldt = new SingleTextValidator(0,★★★PW_MAX_LENGTH★★★,false);
    $c★★★NUMBER★★★ = new PasswordColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★');
    $c★★★NUMBER★★★->setHiddenMainTableColumn(true);
    $c★★★NUMBER★★★->setDescription('★★★INFO★★★');//エクセル・ヘッダでの説明
    $c★★★NUMBER★★★->setValidator($objVldt);
    $c★★★NUMBER★★★->setEncodeFunctionName("ky_encrypt");
