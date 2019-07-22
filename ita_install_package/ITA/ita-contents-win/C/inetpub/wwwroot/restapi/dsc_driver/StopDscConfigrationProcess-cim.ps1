#//////////////////////////////////////////////////////////////////////
#//    
#//  【概要】
#//		ターゲット ノードで処理中のDSCプロセスを緊急停止させる
#//
#//  【入力パラメータ】
#//     $tagtname   :   Target node IP
#//     $username   :   Target node Logon 認証用
#//     $passwd     :   Target node Logon 認証用
#//     
#//  【返却パラメータ】
#//      0:正常         ターゲットノードLCMのDSCプロセス強制処理を完了した
#//
#//     45:異常         Cimセッション作成前のターゲットの認証用パスワードの暗号化処理に失敗
#//     46:異常         Credential オブジェクトのインスタンス生成処理に失敗
#//     47:異常         Cimセッションの生成処理に失敗
#//     48:異常         Stop-DSCconfiguration（強制停止）処理に失敗
#//
#//   Logs:             PowerShellの実行ログ(標準出力/標準エラー出力）を配列（$arry_out）として返す
#//   Return Value:     処理結果を整数値で返す
#//
#//////////////////////////////////////////////////////////////////////

# パラメータ情報
Param(
    [Parameter(Mandatory)][string]$tagtname,
    [string]$username,
    [string]$passwd
)

# Write-Output "ITA Message: $tagtname 緊急停止処理を開始します。"    #ITA Message

Try{
    # クリアテキストをセキュリティで保護された文字列に変換
    $sec_str = ConvertTo-SecureString -AsPlainText -Force -String $passwd -Verbose -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    # Write-Output "ITA Message: $tagtname Cimセッション引数のパスワードの暗号化に失敗しました。"    #ITA Message
    $ec = 45
    Exit $ec
    
}Finally{}

Try{
    # Credentialオブジェクト インスタンス生成 (認証用ユーザー名/パスワード(暗号化済)）
    $PSobj = New-Object System.Management.Automation.PsCredential($username, $sec_str) -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    # Write-Output "ITA Message: $tagtname Credentialオブジェクト生成に失敗しました。"    #ITA Message
    $ec = 46
    Exit $ec

}Finally{}

Try{
    # ターゲットノードへのCimセッション生成
    $cimsession = New-CimSession -ComputerName $tagtname -Credential $PSobj -ErrorAction Stop

}Catch{
    Write-Output $error[0]
    # Write-Output "ITA Message: $tagtname Cimセッション生成に失敗しました。"    #ITA Message
    $ec = 47
    Exit $ec
    
}Finally{}

#############################
# Target-Node LCM処理強制停止    # 
#############################
try{
    Stop-DscConfiguration -Force -Verbose -CimSession $cimsession -ErrorAction Stop -WarningAction Continue
    
}Catch{
    $myException = $_
    $StopProcessResult = "False"
    Write-Output $myException
    # Write-Output $ProcessKillResult "ITA Message: $tagtname 緊急停止処理でエラーが発生しました。"   #ITA Message
    Remove-CimSession -CimSession $cimsession
    $ec = 48
    Exit $ec

}Finally{$StopProcessResult = "True"}

# Cimセッション解除
Remove-CimSession -CimSession $cimsession

# Write-Output "ITA Message: $tagtname 緊急停止処理を完了します。"   #ITA Message
$ec = 0
Exit $ec