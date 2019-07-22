#//////////////////////////////////////////////////////////////////////
#//    
#//  【概要】
#//		ターゲット ノードからLCM状態に関する情報を取得します。
#//
#//  【入力パラメータ】
#//     $tagtname   :   Target node IP
#//     $username   :   Target node Logon 認証用
#//     $passwd     :   Target node Logon 認証用
#//     
#//
#//  【返却パラメータ】
#//      0:正常         MOFファイル生成正常終了;Start-DSCConfigurationによる構成適用処理正常終了
#//
#//     55:異常         Cimセッション作成前のターゲットの認証用パスワードの暗号化処理に失敗した
#//     56:異常         Credential オブジェクトのインスタンス生成処理に失敗した
#//     57:異常         Cimセッションの生成処理に失敗した
#//     59:異常         Get-DSCconfiguration（構成情報取得）処理に失敗した
#//
#//   Logs:             PowerShellの実行ログ(標準出力/標準エラー出力）を配列（$arry_out）として返す
#//   Return Value:     処理結果を整数値で返す
#//
#//////////////////////////////////////////////////////////////////////

#設定情報
$tagtname          = $args[0]
$username          = $args[1]
$passwd            = $args[2]

Write-Output "ITA Message: LCMLCM状態情報取得処理を開始します"    #ITA Message

Try{
    # クリアテキストをセキュリティで保護された文字列に変換
    $sec_str = ConvertTo-SecureString -AsPlainText -Force -String $passwd -Verbose -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    Write-Output "ITA Message: Cimセッション引数の暗号化に失敗しました"    #ITA Message
    $ec = 55
    Exit $ec
    
}Finally{}

Try{
    # Credentialオブジェクト インスタンス生成 (認証用ユーザー名/パスワード(暗号化済)）
    $PSobj = New-Object System.Management.Automation.PsCredential($username, $sec_str) -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    Write-Output "ITA Message: Credential オブジェクト生成に失敗しました"
    $ec = 56
    Exit $ec

}Finally{}

Try{
    # ターゲットノードへのCimセッション生成
    $cimsession = New-CimSession -ComputerName $tagtname -Credential $PSobj -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    Write-Output "ITA Message: Cimセッション生成に失敗しました"
    $ec = 57
    Exit $ec
    
}Finally{}

#################
# 構成情報取得   # 
#################
try{
    $LCMParameter = Get-DscLocalConfigurationManager -Verbose -CimSession $cimsession -ErrorAction Stop
    
}Catch{
    $myException = $_
    $StopProcessResult = "False"
    Write-Output $myException
    Write-Output $StopProcessResult "ITA Message: ターゲットノードのLCM情報を取得できませんでした"   #ITA Message
    Remove-CimSession -CimSession $cimsession
    $ec = 59
    Exit $ec

}Finally{$StopProcessResult = "True"}
$LCMParameter
$LcmStatus = $LCMParameter.LCMState
$ConsoleOutPut = "LCMStatus: " + $LcmStatus
Write-Output $ConsoleOutPut "ITA Message: ターゲットノードのLCM状態情報を取得しました"   #ITA Message

# Cimセッション解除
Remove-CimSession -CimSession $cimsession

if( $LcmStatus.Equals("Busy"))     #ﾘﾓｰﾄLCMがDSC適用処理最中
{
    $ec = 0
    Exit $ec
}elseif($LcmStatus.Equals("Idle")) #ﾘﾓｰﾄLCMがDSC適用処理待機中
{
    $ec = 1
    Exit $ec
}else
{
    Exit $ec 
}
