#//////////////////////////////////////////////////////////////////////
#//    
#//  【概要】
#//		Get-DscLocalConfigurationManagerコマンドレットをターゲット ノードへ
#//     実行し、ターゲットノードのLCM状態に関する情報を取得します。
#//
#//  【入力パラメータ】
#//     $tagtname   :   Target node IP
#//     $username   :   Target node Logon 認証用
#//     $passwd     :   Target node Logon 認証用
#//     
#//
#//  【返却パラメータ】
#//      0:正常         コマンドレットが正常に実行され、結果取得したLCM stateが”Idle” (DSC非実行状態)
#//     -1:正常         コマンドレットが正常に実行され、結果取得したLCM stateが”Busy” (DSC実行中状態)
#//
#//     -2:一部異常     コマンドレットが正常に実行され、結果取得したLCM stateが”PendingReboot”※LCM は、再起動を要求するリソースに到達すると、リソースの処理を停止します。
#//     59:異常         コマンドレットが正常に実行され、結果取得したLCM stateが”PendingConfiguration”(構成適用停止中)※”PendingReboot”のサブセット
#//     55:異常         Cimセッション作成前のターゲットの認証用パスワードの暗号化処理に失敗した
#//     56:異常         Credential オブジェクトのインスタンス生成処理に失敗した(構成適用以前）
#//     57:異常         Cimセッションの生成処理に失敗した(構成適用以前）
#//     58:異常         Get-DscLocalConfigurationManager（構成情報取得）処理に失敗した(構成適用以前）
#//
#//   Logs:             PowerShellの実行ログ(標準出力/標準エラー出力）を配列（$arry_out）として返す(構成適用以前）
#//   Return Value:     処理結果を符号付整数値で返す(構成適用以前）
#//
#//////////////////////////////////////////////////////////////////////

# 入力パラメータ
Param(
    [Parameter(Mandatory = $true)][string]$tagtname,
    [string]$username,
    [string]$passwd
)

#Write-Output "ITA Message:$tagtname LCM状態取得処理を開始します。"    #ITA Message
Try{
    # クリアテキストをセキュリティで保護された文字列に変換
    $sec_str = ConvertTo-SecureString -AsPlainText -Force -String $passwd -Verbose -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    # Write-Output "ITA Message: $tagtname Cimセッション引数の暗号化に失敗しました。"    #ITA Message
    $ec = 55
    Exit $ec
    
}Finally{}

Try{
    # Credentialオブジェクト インスタンス生成 (認証用ユーザー名/パスワード(暗号化済)）
    $PSobj = New-Object System.Management.Automation.PsCredential($username, $sec_str) -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    # Write-Output "ITA Message: $tagtname Credential オブジェクト生成に失敗しました。"    #ITA Message
    $ec = 56
    Exit $ec

}Finally{}

Try{
    # ターゲットノードへのCimセッション生成
    $cimsession = New-CimSession -OperationTimeoutSec 3 -ComputerName $tagtname -Credential $PSobj -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    # Write-Output "ITA Message: $tagtname Cimセッション生成に失敗しました。"    #ITA Message
    $ec = 57
    Exit $ec
    
}Finally{}

##################
# 構成情報取得   #
##################
try{
    $LCMParameter = Get-DscLocalConfigurationManager -Verbose -CimSession $cimsession -ErrorAction Stop
    
}Catch{
    $myException = $_
    $StopProcessResult = "False"
    Write-Output $myException
    # Write-Output $StopProcessResult "ITA Message: $tagtname のLCM情報を取得できませんでした。"   #ITA Message
    Remove-CimSession -CimSession $cimsession
    $ec = 58
    Exit $ec

}Finally{$StopProcessResult = "True"}

Write-Output $LCMParameter    # 

$LcmStatus = $LCMParameter.LCMState
$ConsoleOutPut = "LCMStatus: " + $LcmStatus #

# Write-Output $ConsoleOutPut "ITA Message: $tagtname のLCM状態情報を取得しました。"   #ITA Message

# Cimセッション解除
Remove-CimSession -CimSession $cimsession

if( $LcmStatus.Equals("Idle")) {                  #ﾘﾓｰﾄLCMがDSC適用処理待機中(未実行or実行完了/プロセス不在)
    $ec = 0
    Exit $ec
} elseif ($LcmStatus.Equals("Busy")) {            #ﾘﾓｰﾄLCMがDSC適用処理中(実行プロセス存在)
    Write-Output $LCMParameter.LCMStateDetail     # 
    $ec = -1
    Exit $ec
} elseif ($LcmStatus.Equals("PendingReboot")) {   #ﾘﾓｰﾄLCMが再起動要求を出して適用処理停止中
    $ec = -2
    Exit $ec 
} elseif ($LcmStatus.Equals("PendingConfiguration")) {    #ﾘﾓｰﾄLCMがDSC適用処理停止中 false
    $ec = 50
    Exit $ec 
} else { # その他のｽﾃｰﾀｽ(想定外)
    $ec = 59
    Exit $ec
}