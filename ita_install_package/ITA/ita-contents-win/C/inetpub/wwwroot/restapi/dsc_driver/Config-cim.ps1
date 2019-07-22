#//////////////////////////////////////////////////////////////////////
#//    
#//  【概要】
#//		Configurationファイルからファイルに登録されたノード分のMOFファイルを生成しする
#//
#//  【入力パラメータ】
#//     $config_name:   Configuration名
#//     $config_file:   データリレイストレージで連携されたConfigurationファイルへのパス(フルパス)
#//
#//  【返却パラメータ】
#//      0:正常         Configuration Commandlet処理正常終了
#//
#//     20:異常         Configurationファイルﾃﾞｨﾚｸﾄﾘへのカレント移動失敗
#//     21:異常         データリレイストレージ内のConfigurationファイル不在
#//     22:異常         MOF出力先パスにアクセス不可
#//     23:異常         Configurationファイルのコンパイル処理に失敗
#//     24:異常         MOFディレクトリが出力先パスに存在しない
#//
#//   Logs:             PowerShellの実行ログ(標準出力/標準エラー出力）を配列（$arry_out）として返す
#//
#//  【説明/備考】
#//                     PowerShellでカレントをConfigurationファイルがあるディレクトリへ移動し、configuration名を入力すると
#//                     カレントディレクトリにconfigurationと同名のディレクトリが生成され、中に"適用対象ノードIP.mof"ファイルが生成される                     
#//                     
#//////////////////////////////////////////////////////////////////////

# パラメータ
Param(
    [Parameter(Mandatory = $true)][string]$config_file,
    [string]$config_name
)

$ec = 0

$Config_dir = Split-Path $config_file -Parent

# Configuration Directory Exixts Check
if( (Test-Path -Path $Config_dir -Pathtype Container) -eq $false ){     # returns value -> $true or $false
  # Write-Output "ITA Message: Configurationファイルの格納ディレクトリにアクセスできません"    #ITA Message
  Write-Output $error[0]
  $ec = 22
  Exit $ec
}

# Current dir を Configuration fileの dirへ移動 
Push-Location $Config_dir
$Current_dir = Get-Location

# Location Fail Check
if( !( $Current_dir.ProviderPath -eq $Config_dir) ){ # 1 or 0
  # Write-Output "ITA Message: カレントディレクトリがConfigurationファイルの格納ディレクトリではありません。"    #ITA Message
  Write-Output $error[0]
  Pop-Location
  $ec = 20
  Exit $ec
}

#cofiguration file exists check
if( (Test-Path -Path $config_file -PathType Leaf) -eq $false ){    # $true or $false
  # Write-Output "ITA Message: データリレイストレージのConfigurationファイルにアクセスできません"    #ITA Message
  Write-Output $error[0]
  Pop-Location
  $ec = 21
  Exit $ec
}

$config_filename = Split-Path $config_file -Leaf

############################################
# DSC Configuration Script実行(コンパイル処理）#
############################################
$ErrorActionPreference = "Stop"
Try{
    $Config_Result = Invoke-Expression ". .\$config_filename -OutputPath . -Verbose"  # Configurationファイルパス直下にMOFファイルを出力
}Catch{
    # Write-Output "ITA Message: Configurationファイルのコンパイルに失敗しました"    #ITA Message
    Write-Output $error[0]
    Pop-Location
    $ec = 23
    Exit $ec
}Finally{}

# MOF Dir Exists check
$MOF_dir = Join-Path $Config_dir $config_name
if( (Test-Path -Path $MOF_dir -Pathtype Container) -eq $false ){     # returns value -> $true or $false
    #Write-Output "ITA Message: MOFファイル格納ディレクトリが作成されていません"      #ITA Message
    Pop-Location
    $ec = 24
    Exit $ec
}

# return respapi dir
Pop-Location

# Write-Output "ITA Message: $config_name のConfiguration処理が正常に終了しました。"   #ITA Message
$ec = 0

Exit $ec
