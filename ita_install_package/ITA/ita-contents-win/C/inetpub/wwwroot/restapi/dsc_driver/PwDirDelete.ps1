#//////////////////////////////////////////////////////////////////////
#//    
#//  【概要】
#//     in\host_vars\ 配下のpwディレクトリを削除する
#//
#//  【入力パラメータ】
#//     $pw_dir:
#//
#//  【返却パラメータ】
#//      0:正常         ディレクトリ削除処理正常終了
#//     20:異常         ディレクトリの削除に失敗
#//
#//////////////////////////////////////////////////////////////////////

# パラメータ
Param(
    [string]$pw_dir
)

############################################
# pwディレクトリ削除処理                   #
############################################
# pw dir delete
if( (Test-Path -Path $pw_dir -Pathtype Container) -eq $true ){
    try{
        Remove-Item -Path $pw_dir -Recurse -Force
    }Catch{
        $myException = $_
        $SetDLCM = "False"
        Write-Output $myException
        # Write-Output $SetDLCM "ITA Message: $tagtname のLCM設定処理に失敗しました。"   #ITA Message
        Remove-CimSession -CimSession $cimsession
        $ec = 89
        Exit $ec
    }Finally{}
}
$ec = 0

Exit $ec
