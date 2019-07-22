#//////////////////////////////////////////////////////////////////////
#//    
#//  【概要】
#//		Configurationによって生成されたMOFファイルを対象ノードへ適用する
#//
#//  【入力パラメータ】
#//     $tagtname   :   Targetノード IP
#//     $username   :   Targetノード ログオン認証用
#//     $passwd     :   Targetノード ログオン認証用
#//     $config_name:   Configuration名
#//     $config_file:   データリレイストレージで連携されたConfigurationファイルへのパス(フルパス)
#//
#//  【返却パラメータ】
#//      0:正常         Start-DSCConfigurationによる構成適用処理正常終了
#//
#//     12:異常         MOF出力先パスのアクセスに失敗
#//     15:異常         Cimセッション作成前のターゲットの認証用パスワードの暗号化処理に失敗
#//     16:異常         Credential オブジェクトのインスタンス生成処理に失敗
#//     17:異常         Cimセッションの生成処理に失敗（入力情報誤りの可能性）
#//     18:異常         Start-DSCconfiguration（構成適用）処理自体失敗した
#//
#//   Logs:             PowerShellの実行ログ(標準出力/標準エラー出力）を配列（$arry_out）として返す
#//
#//  【説明/備考】
#//                     MOFファイルを適用対象先のノードへCimセッション接続経由で送信し、リモートLCMで実行させる。
#//                     
#//////////////////////////////////////////////////////////////////////

#設定情報
Param(
    [Parameter(Mandatory)][string]$tagtname,
    [string]$username,
    [string]$passwd,
    [string]$MOF_dir
)

# MOFファイル出力先の親ディレクトリパスの作成 MOF_dir
# "データリレイストレージパス(DSC)"\dsc\ns\"作業No"\in\"コンフィグ名"

# MOFファイル出力先の親ディレクトリの確認
if(( Test-Path -Path $MOF_dir -PathType Container ) -eq $false ){    # $true or $false

    # Write-Output "ITA Message: MOFファイルの格納ディレクトリにアクセスできません。"    #ITA Message
    Write-Output $error[0]
    $ec = 12
    Exit $ec
}

# Cim-session引数 Credentialオブジェクト生成（文字列暗号化）
Try{
    # クリアテキストをセキュリティで保護された文字列に変換
    $sec_str = ConvertTo-SecureString -AsPlainText -Force -String $passwd -Verbose -ErrorAction Stop

}Catch{
     
    # Write-Output "ITA Message: Cimセッション引数の暗号化に失敗しました。"    #ITA Message
    Write-Output $error[0]
    #RestAPI通知エラーコード: "セキュリティ保護文字列パラメータ生成エラー" 
    $ec = 15
    Exit $ec
    
}Finally{}

Try{
    # Credentialオブジェクト インスタンス生成 (認証用ユーザー名/パスワード(暗号化済)）
    $PSobj = New-Object System.Management.Automation.PsCredential($username, $sec_str) -ErrorAction Stop
    
}Catch{
     
    # Write-Output "ITA Message: Credentialオブジェクト生成で例外処理が発生しました。" #ITA Message
    Write-Output $error[0]
    #RestAPI通知エラーコード: "Credential オブジェクト生成エラー" 
    $ec = 16
    Exit $ec

}Finally{}

Try{
    # ターゲットノードへのCimセッション生成
    Write-Output $tagtname
    $cimsession = New-CimSession -ComputerName $tagtname -Credential $PSobj -ErrorAction Stop

}Catch{
    # ターゲットノードへの認証不成功 目的のサーバへ正しいID/PASSWORDで認証をかけたかが疑われる 
    # Write-Output "ITA Message: Cimセッション生成で例外処理が発生しました。" #ITA Message
    Write-Output $error[0]
    $ec = 17
    Exit $ec
    
}Finally{}

########################
# DSC構成 適用処理   　  # 複数ノード同時並行処理対応により構成適用対象のリモートノードからの実行結果を待たずに、次のステップへ進む(-wait オプションの廃止)
######################## 注意：Start-DscConfiguration　はNULL値を返してくる。Memberメソッドが呼び出せない。
Try{
    Start-DscConfiguration -Force -Verbose -Path $MOF_dir -CimSession $cimsession -ErrorAction stop -WarningAction Continue

}Catch{

    $myException = $_
    $Apply_result = "False"
    Write-Output $myException
    # Write-Output $Apply_result "ITA Message: 対象ノードへの構成適用処理に失敗しました。"   #ITA Message
    Remove-CimSession -CimSession $cimsession
    $ec = 18
    Exit $ec

}Finally{ $Apply_result = "True" }

# Cimセッション解除
Remove-CimSession -CimSession $cimsession

# Write-Output $Apply_result "ITA Message: 対象のノード $tagtname への構成変更情報の転送を完了しました。"   #ITA Message

$ec = 0
Exit $ec