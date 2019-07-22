    $objVldt = new SingleTextValidator(0,★★★SIZE★★★,false);
    ★★★PREG_MATCH★★★
    $c = new TextColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★');
    $c->setDescription('★★★INFO★★★');//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);
