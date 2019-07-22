#//////////////////////////////////////////////////////////////////////
#//    
#//  【概要】
#//    Get-DscLocalConfigurationManagerコマンドレットをターゲット ノードへ
#//    実行し、ターゲットノードのLCM状態に関する情報を取得します。
#//    取得後、ターゲットノードのCertificateIDとパラメータのCertificateIDに
#//    差分があれば、Set-DscLocalConfigurationManagerコマンドレットを実行します。
#//
#//  【入力パラメータ】
#//     $tagtname     :   Target node IP
#//     $username     :   Target node Logon 認証用
#//     $passwd       :   Target node Logon 認証用
#//     $config_name  :   Configuration名
#//     $certificateid:   Target node CertificateID
#//     $rebootdata   :   Target node RebootNodeIfNeeded
#//
#//  【返却パラメータ】
#//      0:正常         Set-DscLocalConfigurationManagerによるLCM設定処理正常終了
#//     55:異常         Cimセッション作成前のターゲットの認証用パスワードの暗号化処理に失敗した
#//     56:異常         Credential オブジェクトのインスタンス生成処理に失敗した(構成適用以前）
#//     57:異常         Cimセッションの生成処理に失敗した(構成適用以前）
#//     58:異常         Get-DscLocalConfigurationManager（構成情報取得）処理に失敗した(構成適用以前）
#//     60:異常         Set-DscLocalConfigurationManager（LCM設定）処理に失敗した(構成適用以前）
#//
#//   Logs:             PowerShellの実行ログ(標準出力/標準エラー出力）を配列（$arry_out）として返す(構成適用以前）
#//
#//////////////////////////////////////////////////////////////////////

# 入力パラメータ
Param(
    [Parameter(Mandatory = $true)][string]$tagtname,
    [string]$username,
    [string]$passwd,
    [string]$config_name,
    [string]$certificateid,
    [string]$rebootdata
)

#Write-Output "ITA Message:$tagtname LCM状態取得処理を開始します。"    #ITA Message

# Write-Output "ITA Message: パスワードは $passwd です。"    #ITA Message

Try{
    # クリアテキストをセキュリティで保護された文字列に変換
    $sec_str = ConvertTo-SecureString -AsPlainText -Force -String $passwd -Verbose -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    #Write-Output "ITA Message: $tagtname Cimセッション引数の暗号化に失敗しました。"    #ITA Message
    $ec = 85
    Exit $ec
    
}Finally{}

Try{
    # Credentialオブジェクト インスタンス生成 (認証用ユーザー名/パスワード(暗号化済)）
    $PSobj = New-Object System.Management.Automation.PsCredential($username, $sec_str) -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    # Write-Output "ITA Message: $tagtname Credential オブジェクト生成に失敗しました。"    #ITA Message
    $ec = 86
    Exit $ec

}Finally{}

Try{
    # ターゲットノードへのCimセッション生成
    $cimsession = New-CimSession -OperationTimeoutSec 3 -ComputerName $tagtname -Credential $PSobj -ErrorAction Stop

}Catch{
    Write-Output $error[0] 
    # Write-Output "ITA Message: $tagtname Cimセッション生成に失敗しました。"    #ITA Message
    $ec = 87
    Exit $ec
    
}Finally{}

##################
# 構成情報取得   #
##################
try{
    $LCMParameter = Get-DscLocalConfigurationManager -Verbose -CimSession $cimsession -ErrorAction Stop
    
}Catch{
    $myException = $_
    $GetDLCM = "False"
    Write-Output $myException
    # Write-Output $GetDLCM "ITA Message: $tagtname のLCM情報を取得できませんでした。"   #ITA Message
    Remove-CimSession -CimSession $cimsession
    $ec = 88
    Exit $ec

}Finally{$GetDLCM = "True"}

# Write-Output $LCMParameter    # 

$LcmCertificateID = $LCMParameter.CertificateID
$LcmRebootNodeIfNeeded = $LCMParameter.RebootNodeIfNeeded

##################
# 構成情報設定   #
################## 注意：Set-DscLocalConfigurationManager は NULL値を返してくる。
$SetDLCM = "False"
if( $certificateid.Length -gt 0 ) {
    if( $LcmCertificateID -ne $certificateid ) {
        # Write-Output "ITA Message: $tagtname のSet-DscLocalConfigurationManagerを開始します。1"   #ITA Message

        try{
            Set-DscLocalConfigurationManager -Force -Verbose -Path $config_name -CimSession $cimsession -ErrorAction Stop
            $SetDLCM = "True"
            # Write-Output "SetDLCM = $SetDLCM"

        }Catch{
            $myException = $_
            $SetDLCM = "False"
            Write-Output $myException
            # Write-Output $SetDLCM "ITA Message: $tagtname のLCM設定処理に失敗しました。"   #ITA Message
            Remove-CimSession -CimSession $cimsession
            $ec = 89
            Exit $ec

        }Finally{$SetDLCM = "True"}
    }
}

if( $SetDLCM -eq "False" ) {
    if( $rebootdata.Length -gt 0) {
        if( $LCMParameter.RebootNodeIfNeeded.ToString() -ne $rebootdata ) {
            # $ConsoleOutPut = "rebootdata: " + $rebootdata #
            # Write-Output "ITA Message: $tagtname のSet-DscLocalConfigurationManagerを開始します。2"   #ITA Message

            try{
                Set-DscLocalConfigurationManager -Force -Verbose -Path $config_name -CimSession $cimsession -ErrorAction Stop
                $SetDLCM = "True"
                #Write-Output "SetDLCM = $SetDLCM"
            }Catch{
                $myException = $_
                $SetDLCM = "False"
                Write-Output $myException
                # Write-Output $Set_Result "ITA Message: $tagtname のLCM設定処理に失敗しました。"   #ITA Message
                Remove-CimSession -CimSession $cimsession
                $ec = 89
                Exit $ec
            }Finally{$SetDLCM = "True"}
        } else {
            # Write-Output "ITA Message: $tagtname のSet-DscLocalConfigurationManagerをスキップします。"   #ITA Message
        }
    } else {
        # Write-Output "ITA Message: $tagtname のSet-DscLocalConfigurationManagerをスキップします。"   #ITA Message
    }
}

# Cimセッション解除
Remove-CimSession -CimSession $cimsession

$ec = 0
Exit $ec
