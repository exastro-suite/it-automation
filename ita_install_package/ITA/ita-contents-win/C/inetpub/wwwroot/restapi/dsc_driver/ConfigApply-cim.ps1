#//////////////////////////////////////////////////////////////////////
#//    
#//  【概要】
#//		ConfigurationファイルからMOFファイルを生成し、MOFファイルを対象ノードへ適用する
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
#//     21:異常         データリレイストレージのConfigurationファイルが見つからない
#//     22:異常         MOF出力先パス作成処理に失敗した
#//     23:異常         Configurationファイルのコンパイル処理に失敗した
#//     24:異常         MOFファイルが出力先パスに存在しない
#//     25:異常         Cimセッション作成前のターゲットの認証用パスワードの暗号化処理に失敗した
#//     26:異常         Credential オブジェクトのインスタンス生成処理に失敗した
#//     27:異常         Cimセッションの生成処理に失敗した
#//     28:異常         MOFファイルの親ディレクトリパスの作成処理に失敗した
#//     29:異常         Start-DSCconfiguration（構成適用）処理に失敗した
#//
#//   Logs:             PowerShellの実行ログ(標準出力/標準エラー出力）を配列（$arry_out）として返す
#//
#//  【説明/備考】
#//                     PowerShellでカレントをConfigurationファイルがあるディレクトリへ移動し、configuration名を入力すると
#//                     カレントディレクトリにconfigurationと同名のディレクトリが生成され、中に"適用対象ノードIP.mof"ファイルが生成される                     
#//
#//////////////////////////////////////////////////////////////////////

#設定情報
$tagtname          = $args[0]
$username          = $args[1]
$passwd            = $args[2]
$config_file       = $args[3]
$config_name       = $args[4]

Write-Output "ITA Message: 構成適用処理を開始します"   #ITA Message
# Configurationコンパイル出力先の親ディレクトリパスの作成 
# コンパイル時にカレントを移動させるために作成する。
# "データリレイストレージパス(DSC)"\dsc\ns\"作業No"\in\
$Config_dir = Split-Path $config_file -Parent
if( (Invoke-Expression ("Test-Path -Path $Config_dir")) -eq $false ){ # $true or $false
  Write-Output "ITA Message: Configurationファイルの格納ディレクトリにアクセスできません" #ITA Message
  Write-Output $error[0]
  $ec = 22
  Exit $ec
}

# current dir を Configuration fileの dirへ移動 
Push-Location $Config_dir
$Current_dir = Get-Location

if( !( $Current_dir.ProviderPath -eq $Config_dir) ){ # $true or $false
  Write-Output "ITA Message: カレントディレクトリがConfigurationファイルの格納ディレクトリではありません。" #ITA Message
  Write-Output $error[0]
  Pop-Location
  $ec = 20
  Exit $ec
}

if( (Invoke-Expression ("Test-Path -Path $config_file -PathType Leaf")) -eq $false ){ # $true or $false
  Write-Output "ITA Message: DSCサーバのConfigurationファイルにアクセスできません" #ITA Message
  Write-Output $error[0]
  Pop-Location
  $ec = 21
  Exit $ec
}

################################
# DSC Configuration コンパイル #
################################
Try{
    $Config_Result = Invoke-Expression ". ./$config_name -OutputPath ." -ErrorAction Stop
}Catch{
     
    Write-Output "ITA Message: Configurationファイルのコンパイルに失敗しました" #ITA Message
    Write-Output $error[0]
    Pop-Location
    $ec = 23
    Exit $ec

}Finally{}

# MOF File Exists check
if((Invoke-Expression ("Test-Path $Config_Result -PathType Leaf")) -eq $false ){ # $true or $false
    Write-Output "ITA Message: 適用するMOF ファイルがありません" #ITA Message
    Write-Output $error[0]
    Pop-Location
    $ec = 24
    Exit $ec
}

Try{
    # クリアテキストをセキュリティで保護された文字列に変換
    $sec_str = ConvertTo-SecureString -AsPlainText -Force -String $passwd -Verbose -ErrorAction Stop

}Catch{
     
    Write-Output "ITA Message: Cimセッション引数の暗号化に失敗しました" #ITA Message
    Write-Output $error[0]
    Pop-Location
    #RestAPI通知エラーコード: "セキュリティ保護文字列パラメータ生成エラー" 
    $ec = 25
    Exit $ec
    
}Finally{}

Try{
    # Credentialオブジェクト インスタンス生成 (認証用ユーザー名/パスワード(暗号化済)）
    $PSobj = New-Object System.Management.Automation.PsCredential($username, $sec_str) -ErrorAction Stop

}Catch{
     
    Write-Output "ITA Message: Credential オブジェクト生成で例外処理が発生しました" #ITA Message
    Write-Output $error[0]
    Pop-Location
    #RestAPI通知エラーコード: "Credential オブジェクト生成エラー" 
    $ec = 26
    Exit $ec

}Finally{}

Try{
    # ターゲットノードへのCimセッション生成
    $cimsession = New-CimSession -ComputerName $tagtname -Credential $PSobj -ErrorAction Stop

}Catch{
     
    Write-Output "ITA Message: Cimセッション生成：例外処理が発生しました" #ITA Message
    Write-Output $error[0]
    Pop-Location
    $ec = 27
    Exit $ec
    
}Finally{}

# MOF ディレクトリパス作成チェック Start-DscConfigurationの-Path引数はMOFファイルの親ディレクトリ引数のため
$MOF_dir = Split-Path $Config_Result -Parent
if( $MOF_dir -eq $false ){
    Write-Output  "ITA Message: MOF ディレクトリパス引数の作成に失敗しました" # ITA Message
    Write-Output $error[0]
    Pop-Location
    $ec = 28
    Exit $ec 
}

########################
# DSC構成 適用処理   　# -Waitｵﾌﾟｼｮﾝで処理が完了するまで次のステップへ進まない
######################## Start-DscConfiguration　はNULL値を返してくる↓Memberメソッドが呼び出せない。$MOF_dir
Try{
    Start-DscConfiguration -Force -Verbose -Wait -Path $MOF_dir -CimSession $cimsession -ErrorAction stop -WarningAction Continue

}Catch{
    $myException = $_
    $Apply_result = "False"
    Write-Output $myException
    Write-Output $Apply_result "ITA Message: 構成適用に失敗しました"   #ITA Message
    Remove-CimSession -CimSession $cimsession
    Pop-Location
    $ec = 29
    Exit $ec

}Finally{ $Apply_result = "True" }

# Cimセッション解除
Remove-CimSession -CimSession $cimsession

# respapi dirへ戻る
Pop-Location
#Get-Location                                      # Debug

Write-Output $Apply_result "ITA Message: 構成適用に成功しました"   #ITA Message
$ec = 0
Exit $ec 
