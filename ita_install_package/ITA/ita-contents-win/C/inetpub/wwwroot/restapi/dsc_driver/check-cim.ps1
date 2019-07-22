#//////////////////////////////////////////////////////////////////////
#//   
#//  【概要】
#//   Start-DSCconfigration実行後、対象ノードでの構成適用処理が想定どおり行われたか確認する
#//   Test-DSCconfiguration実行 ->ターゲットノード・LCM内の構成情報と対象ノードの構成情報に差があるか確認 
#//
#//  【入力パラメータ】
#//      
#//    $tagtname:     ターゲットノード(IP)
#//    $username:     ターゲットノード接続時の認証ユーザー名（ドメイン名\ユーザー名）
#//    $passwd:       ターゲットノード接続時の認証パスワード
#//    $config_file:  Configrationファイルのフルパスネーム
#//
#//  【返却パラメータ($return_var)】
#//     0：正常終了  Start-DSCconfigurationによる構成適用が正常に処理されたことが確認できた
#//
#//    35: 異常終了  Cimセッション作成前処理のターゲットの認証用パスワードの暗号化処理に失敗
#//    36: 異常終了  Credential オブジェクトのインスタンス生成処理に失敗
#//    37: 異常終了  Cimセッションの生成処理に失敗(誤情報入力の可能性)
#//    38: 異常終了  TEST-DSCconfiguration（構成適用後チェック）に失敗
#//                          
#//  【ログ】     
#//    logs:    PowerShellの実行ログ(標準出力/標準エラー出力）を配列としてexec（$arry_out）へ返す
#//        
#//////////////////////////////////////////////////////////////////////

# パラメータ情報
Param(
    [Parameter(Mandatory)][string]$tagtname,
    [string]$username,
    [string]$passwd,
    [string]$config_file
)

#Write-Output "ITA Message: $tagtname 構成状態のテストを開始します。"    #ITA Messag
$ec = 0

Try{
    # クリアテキストパスワードをセキュリティで保護された文字列に変換
    $test_sec_str = ConvertTo-SecureString -AsPlainText -Force -String $passwd -Verbose -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    # Write-Output "ITA Message: $tagtname Cimセッション引数のパスワードの暗号化に失敗しました。"    #ITA Message
    $ec = 35
    EXIT $ec
    
}Finally{}

Try{
    # Credential オブジェクト生成（認証用ユーザー名/パスワード(暗号化済)）
    $PSobj = New-Object System.Management.Automation.PsCredential($username, $test_sec_str) -Verbose -ErrorAction Stop

}Catch{
     
    #Write-Output "ITA Message: $tagtname Credential オブジェクト生成：例外処理が発生しました。"   #ITA message
    Write-Output $error[0]
    #RestAPI通知エラーコード入力: "Credential オブジェクト生成エラー" 
    $ec = 36
    EXIT $ec
    
}Finally{}

Try{
    # Cimセッション生成
    $cimsession = New-CimSession -SkipTestConnection -ComputerName $tagtname -Credential $PSobj -ErrorAction Stop 
    
}Catch{
     
    #Write-Output "ITA Message: $tagtname Cimセッション生成：例外処理が発生しました。" #ITA message
    Write-Output $error[0]
    #RestAPI通知エラーコード 入力: "Cimセッション生成失敗   
    $ec = 37
    EXIT $ec
    
}Finally{}

#################################################
# 構成情報⇔ターゲットノード現構成 の同一性確認 #
#################################################
$Test_result = Test-DscConfiguration -Verbose -CimSession $cimsession

# Cimセッション解放
Remove-CimSession -CimSession $cimsession

if( $Test_result -eq $true ){
    Write-Output $Test_result
    #Write-Output "ITA Message: $tagtname 構成情報とターゲットノードの構成は一致しています。" #ITA message
    exit $ec # 0
}
elseif( $Test_result -eq $false ){
    Write-Output $Test_result
    #Write-Output "ITA Message: $tagtname 構成情報とターゲットノードの構成に違いがあります。" #ITA message
    $ec = 39
    exit $ec
}