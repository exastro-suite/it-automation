    $c★★★NUMBER★★★ = new FileUploadColumn('★★★VALUE_NAME★★★','★★★DISP_NAME★★★');
    $c★★★NUMBER★★★->setHiddenMainTableColumn(true);
    $c★★★NUMBER★★★->setDescription('★★★INFO★★★');           //エクセル・ヘッダでの説明
    $c★★★NUMBER★★★->setFileHideMode(true);
    $c★★★NUMBER★★★->setMaxFileSize(★★★UPLOAD_FILE_SIZE★★★); //単位はバイト
    $c★★★NUMBER★★★->setAllowSendFromFile(false);                  //エクセル/CSVからのアップロードを禁止する。
    $c★★★NUMBER★★★->setAllowUploadColmnSendRestApi(true);         //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)
    $c★★★NUMBER★★★->setNRPathAnyToBranchPerFUC('/uploadfiles/★★★UPLOAD_REF_MENU_ID★★★/★★★VALUE_NAME★★★');
    ★★★REQUIRED★★★
    ★★★UNIQUED★★★
