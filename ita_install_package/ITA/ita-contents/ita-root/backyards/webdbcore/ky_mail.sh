#!/bin/sh
#   Copyright 2019 NEC Corporation
#
#   Licensed under the Apache License, Version 2.0 (the "License");
#   you may not use this file except in compliance with the License.
#   You may obtain a copy of the License at
#
#       http://www.apache.org/licenses/LICENSE-2.0
#
#   Unless required by applicable law or agreed to in writing, software
#   distributed under the License is distributed on an "AS IS" BASIS,
#   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#   See the License for the specific language governing permissions and
#   limitations under the License.
#
#
######################################################################
##
##
##  【概要】
##      システムメールを送信する
##
######################################################################

#----------------------------------------------------#
# ルートディレクトリ取得
#----------------------------------------------------#
ROOT_DIR_PATH=`dirname ${0} | awk -F'ita-root' '{print $1}'`'ita-root'

#----------------------------------------------------#
# 各種パラメータ定義
#----------------------------------------------------#
# メールのテンプレートが記述されたファイルのプレフィックス
sysmail_sender_mailfile_prefix='sysmail_body_'
# メールのテンプレートが記述されたファイルのポストフィックス
sysmail_sender_mailfile_postfix='.txt'
# メール設定ファイル配置ディレクトリ
sysmail_sender_conf_dir=${ROOT_DIR_PATH}'/confs/backyardconfs'
# メールのテンプレートファイルと利用権限などを記述する設定ファイル
sysmail_sender_conf_file=${sysmail_sender_conf_dir}'/sysmail.list'
# mailxコマンドに付加したいオプションを設定するファイル
sysmail_mailx_option=${sysmail_sender_conf_dir}'/mailx_option.txt'
# メールのテンプレートへ埋め込むデータを記述したファイル(送信依頼ファイル)を配置するディレクトリ
sysmail_sender_que_bnk_dir=${ROOT_DIR_PATH}'/temp/ky_mail_queues/ky_sysmail_0_queue'
# メールのテンプレートへ埋め込むデータを記述したファイル(送信依頼ファイル)を正常に処理して送信が完了した場合に保管するディレクトリ
sysmail_sender_que_suc_dir=${ROOT_DIR_PATH}'/temp/ky_mail_queues/ky_sysmail_1_success'
# メールのテンプレートへ埋め込むデータを記述したファイル(送信依頼ファイル)を正常に処理できず送信が完了しなかった場合に保管するディレクトリ
sysmail_sender_que_err_dir=${ROOT_DIR_PATH}'/temp/ky_mail_queues/ky_sysmail_2_error'
# メールのテンプレートへ埋め込むデータを記述したファイル(送信依頼ファイル)のプレフィックス
sysmail_sender_quefile_prefix='sysmail_'
# 一時ファイルを配置するディレクトリ
sysmail_sender_work_dir=${ROOT_DIR_PATH}'/temp'
# 一時ファイル名
sysmail_sender_work_file='WORKFILE.txt'

#----------------------------------------------------#
# ログファイル名を作成
#----------------------------------------------------#
# SH_SCRIPT名から拡張子を削除
SH_LOG_NAME_PREFIX=`basename ${SH_SCRIPT_NAME%.*}`

#----------------------------------------------------#
# グローバル変数宣言
#----------------------------------------------------#
export SH_LOG_NAME_PREFIX

#----------------------------------------------------#
# 各種ファンクション定義
#----------------------------------------------------#
#----------------------------------------------------#
# ログ出力＆プロセス終了ファンクション
#
# [動作]
# 本ファンクションは2つの役割を持つ。
#   ・ログ出力
#   ・プロセス終了
# ログレベルと終了コードにより動作が確定される。
# [終了コード] [ログレベル] ⇒ [プロセス終了] [ログ出力]
#  -1           NORMAL      ⇒  ×             ×
#  -1           DEBUG       ⇒  ×             ○
#  -2           NORMAL      ⇒  ×             ×
#  -2           DEBUG       ⇒  ×             ○
#  -1,-2以外    NORMAL      ⇒  ○             ○
#  -1,-2以外    DEBUG       ⇒  ○             ○
#----------------------------------------------------#
function shcommonFunction
{
    # パラメータ格納
    P_RetCode=$1    # 終了コード
                    # ("-1"の場合は終了しない。またLOG_LEVELが'NORMAL'の場合はログを出さない)
    P_Message=$2    # メッセージ本文
    
    if [ ${P_RetCode} -ne -1 -a ${P_RetCode} -ne -2 -o "${LOG_LEVEL}" = 'DEBUG' ]
    then
        # ログファイル名を作成
        LOG_NAME=${LOG_DIR}"/"${SH_LOG_NAME_PREFIX}"_"`date '+%Y%m%d'`".log"
        
        # メッセージ出力
        MESSAGE="["`date '+%Y/%m/%d %H:%M:%S'`"][${SH_SCRIPT_NAME}][$$]${P_Message}"
        echo ${MESSAGE} >> ${LOG_NAME}
    fi
    
    if [ ${P_RetCode} -ne -1 -a ${P_RetCode} -ne -2 ]
    then
        MESSAGE="["`date '+%Y/%m/%d %H:%M:%S'`"][${SH_SCRIPT_NAME}][$$]Shell script is terminated.(Error：[line no.]${P_RetCode})"
        echo ${MESSAGE} >> ${LOG_NAME}
        exit ${P_RetCode}
    fi
}

#----------------------------------------------------#
# send_mail
# パラメータ1：FROM
# パラメータ2：TO
# パラメータ3：件名
# パラメータ4：本文のファイル名
# パラメータ5：付加オプション
# パラメータ6：CC
#----------------------------------------------------#
send_mail()
{
    ret_code=1
    from=$1
    to=$2
    subject_org=$3
    body_file=$4
    option=$5
    cc=$6
    
    export LANG=ja_JP.UTF-8
    if [ ${NUM_OF_WORDS} = "-" ]
    then
        if [ -n "$cc" ]
        then
            cat ${body_file} | mailx -s "$subject_org" -c "$cc" -r "$from" "$to"
            ret_code=$?
        else
            cat ${body_file} | mailx -s "$subject_org"          -r "$from" "$to"
            ret_code=$?
        fi
    else
        if [ -n "$cc" ]
        then
            cat ${body_file} | mailx -s "$subject_org" $option -c "$cc" -r "$from" "$to"
            ret_code=$?
        else
            cat ${body_file} | mailx -s "$subject_org" $option          -r "$from" "$to"
            ret_code=$?
        fi
    fi
    
    return $ret_code
}

#----------------------------------------------------#
# 各種変数設定
#----------------------------------------------------#
ret_result=0
warn_count=0

# 処理対象件数
input_tgt_count=0
# 正常処理件数
success_count=0
# 警告処理件数
send_warn_count=0
# 異常処理件数
send_error_count=0

#----------------------------------------------------#
# シェルスクリプト実行
#----------------------------------------------------#
shcommonFunction -1 "Process : Start"

#----------------------------------------------------#
# 各種チェック
#----------------------------------------------------#
if [ ! -d ${sysmail_sender_que_bnk_dir} ]
then
    shcommonFunction ${LINENO} "Process : [Error]sysmail_sender_que_bnk_dir is not exist."
fi
if [ ! -d ${sysmail_sender_que_suc_dir} ]
then
    shcommonFunction ${LINENO} "Process : [Error]sysmail_sender_que_suc_dir is not exist."
fi
if [ ! -d ${sysmail_sender_que_err_dir} ]
then
    shcommonFunction ${LINENO} "Process : [Error]sysmail_sender_que_err_dir is not exist."
fi
if [ ! -d ${sysmail_sender_work_dir} ]
then
    shcommonFunction ${LINENO} "Process : [Error]sysmail_sender_work_dir is not exist."
fi
if [ ! -d ${sysmail_sender_conf_dir} ]
then
    shcommonFunction ${LINENO} "Process : [Error]sysmail_sender_conf_dir is not exist."
fi
if [ ! -f ${sysmail_sender_conf_file} ]
then
    shcommonFunction -2 "Process : [Info]sysmail_sender_conf_file is not exist."
else
    #----------------------------------------------------#
    # mailxコマンド付加オプションファイル読込
    #----------------------------------------------------#
    if [ -f ${sysmail_mailx_option} ]
    then
        MAILX_OPTION=`cat ${sysmail_mailx_option}`
    else
        MAILX_OPTION="-"
    fi
    
    #----------------------------------------------------#
    # ----（ここから）キューファイル処理ループ
    #----------------------------------------------------#
    for QUEUE_LIST in `ls ${sysmail_sender_que_bnk_dir}"/"${sysmail_sender_quefile_prefix}* 2>/dev/null`; do
        
        # ベースネーム取得
        QUEUE_BASENAME=`basename ${QUEUE_LIST}`
        
        # ディレクトリが混じっている場合はスキップ
        if [ -d ${QUEUE_LIST} ]
        then
            # ----キューファイルの保管ディレクトリに、フォルダが入っていた場合は、警告する
            let send_warn_count=send_warn_count+1
            let warn_count=warn_count+1
            shcommonFunction -1 "[${QUEUE_BASENAME}][Warning]${QUEUE_LIST} (at sysmail_sender_que_bnk_dir) is Directory."
            # 次のループへ
            continue
        fi
        
        # QUEUEファイル書込み途中判定
        /sbin/fuser ${QUEUE_LIST} > /dev/null 2>&1
        fuser_ret=$?
        if [ ${fuser_ret} -eq 0 ]
        then
            # インフォメッセージ出力
            let send_warn_count=send_warn_count+1
            let warn_count=warn_count+1
            shcommonFunction -1 "[${QUEUE_BASENAME}][Info]${QUEUE_BASENAME} is in writing by other program, so this process is skipped."
            # 次のループへ
            continue
        fi
        
        # 処理対象件数インクリメント
        input_tgt_count=`expr ${input_tgt_count} + 1`
        
        # メール本文IDを取得
        MAILBODY_ID=`echo ${QUEUE_BASENAME} | cut -f 2 -d "_"`
        if [ -z "${MAILBODY_ID}" ]
        then
            let send_error_count=send_error_count+1
            let ret_result=ret_result+1
            shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]MAILBODY_ID is not found in ${QUEUE_BASENAME}."
            mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
            continue
        else
            if [[ "${MAILBODY_ID}" != [0-9][0-9][0-9] ]]
            then
                let send_error_count=send_error_count+1
                let ret_result=ret_result+1
                shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]Format of MAILBODY_ID is wrong."
                mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                continue
            fi
        fi
        
        # 設定ファイルに該当のメール本文IDの設定が定義されているかチェック
        if [ `cat ${sysmail_sender_conf_file} | grep "^${MAILBODY_ID}" | wc -l` -ne 1 ]
        then
            let send_error_count=send_error_count+1
            let ret_result=ret_result+1
            shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]MAILBODY_ID is not registered in ${sysmail_sender_conf_file}."
            mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
            continue
        fi
        
        # 設定ファイルから該当メール本文IDの置換単語数を取得
        NUM_OF_WORDS=`cat ${sysmail_sender_conf_file} | grep "^${MAILBODY_ID}" | cut -f 2`
        if [ -z ${NUM_OF_WORDS} ]
        then
            let send_error_count=send_error_count+1
            let ret_result=ret_result+1
            shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]Number of substitution words is not found in ${sysmail_sender_conf_file}."
            mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
            continue
        fi
        
        IMPORT_MODE=0
        if [ ${NUM_OF_WORDS} = "X" ]
        then
            IMPORT_MODE=1
        else
            # 置換単語数フォーマットチェック
            
            NUM_OF_WORDS_FOR_CHECK=`echo ${NUM_OF_WORDS} | sed 's/[^0-9]//g'`
            if [ -z ${NUM_OF_WORDS_FOR_CHECK} ]
            then
                let send_error_count=send_error_count+1
                let ret_result=ret_result+1
                shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]Format of Number of substitution words (in ${sysmail_sender_conf_file}) is wrong".
                mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                continue
            else
                if [ ${NUM_OF_WORDS} != ${NUM_OF_WORDS_FOR_CHECK} ]
                then
                    let send_error_count=send_error_count+1
                    let ret_result=ret_result+1
                    shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]Format of Number of substitution words (${NUM_OF_WORDS}) (in ${sysmail_sender_conf_file}) is wrong".
                    mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                    continue
                fi
            fi
        fi
        
        # 設定ファイルのフィールド数をチェック
        NUM_OF_FIELDS=`cat ${sysmail_sender_conf_file} | grep "^${MAILBODY_ID}" | awk '{print NF}'`
        
        # FROM,TO(,CC)アドレスは設定ファイルから引用
        if [ ${NUM_OF_FIELDS} -eq 5 ]
        then
            if [ ${IMPORT_MODE} -eq 1 ]
            then
                #01(タイトル行)
                #02(本文)
                GYO_SU=`cat ${QUEUE_LIST} | wc -l`
                if [ ${GYO_SU} -lt 2 ]
                then
                    let send_error_count=send_error_count+1
                    let ret_result=ret_result+1
                    shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]Mail body strings are not found (Number of substitution words : ${NUM_OF_WORDS})."
                    mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                    continue
                fi
            else
                # キューファイルの本文行数と置換単語数を比較し「≠」の場合はエラー扱い
                GYO_SU=`cat ${QUEUE_LIST} | wc -l`
                if [ `expr ${NUM_OF_WORDS} + 1` -ne ${GYO_SU} ]
                then
                    let send_error_count=send_error_count+1
                    let ret_result=ret_result+1
                    shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]Number of substitution words are not equal (QueueFile:`expr ${GYO_SU} - 1`, Number of substitution words:${NUM_OF_WORDS})."
                    mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                    continue
                fi
            fi
            
            # メール件名を取得
            MAIL_SUBJECT=`sed -n "1,1p" ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME}`
            
            # 設定ファイルから該当メール本文IDの差出メールアドレスを取得
            FROM_MAIL_ADDRESS=`cat ${sysmail_sender_conf_file} | grep "^${MAILBODY_ID}" | cut -f 3`
            if [ -z "${FROM_MAIL_ADDRESS}" ]
            then
                let send_error_count=send_error_count+1
                let ret_result=ret_result+1
                shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]FROM_MAIL_ADDRESS is not found in ${sysmail_sender_conf_file}."
                mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                continue
            fi
            
            # 設定ファイルから該当メール本文IDの宛先メールアドレスを取得
            TO_MAIL_ADDRESS=`cat ${sysmail_sender_conf_file} | grep "^${MAILBODY_ID}" | cut -f 4`
            if [ -z "${TO_MAIL_ADDRESS}" ]
            then
                let send_error_count=send_error_count+1
                let ret_result=ret_result+1
                shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]TO_MAIL_ADDRESS is not found in ${sysmail_sender_conf_file}."
                mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                continue
            fi
            
            # 設定ファイルから該当メール本文IDのCCメールアドレスを取得
            TEMP_CC_MAIL_ADDRESS=`cat ${sysmail_sender_conf_file} | grep "^${MAILBODY_ID}" | cut -f 5`
            if [ -z "${TEMP_CC_MAIL_ADDRESS}" ]
            then
                let send_error_count=send_error_count+1
                let ret_result=ret_result+1
                shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]CC_MAIL_ADDRESS is not found in ${sysmail_sender_conf_file}."
                mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                continue
            fi
            if [ ${TEMP_CC_MAIL_ADDRESS} = "null" ]
            then
                CC_MAIL_ADDRESS="";
            else
                CC_MAIL_ADDRESS=${TEMP_CC_MAIL_ADDRESS};
            fi
            
            # 編集用テンポラリファイル名を作成
            TEMP_FILENAME=${sysmail_sender_work_dir}"/"${sysmail_sender_mailfile_prefix}${MAILBODY_ID}${sysmail_sender_mailfile_postfix}"_"`date '+%Y%m%d%H%M%S'"_"`${RANDOM}
            
            if [ ${IMPORT_MODE} -eq 1 ]
            then
                # 編集用テンポラリファイルが存在している場合は削除する
                if [ -f ${TEMP_FILENAME} ]
                then
                    rm -f ${TEMP_FILENAME}
                fi
                
                # 送信キューファイルの2行目以降を本文としてコピーする
                for i in `seq 2 ${GYO_SU}`
                do
                    echo `sed -n "${i},${i}p" ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME}` >> ${TEMP_FILENAME}
                done
            else
                # 編集用にメール本文ファイルをコピーする
                cp ${sysmail_sender_conf_dir}"/"${sysmail_sender_mailfile_prefix}${MAILBODY_ID}${sysmail_sender_mailfile_postfix} ${TEMP_FILENAME}
                
                # 本文行数(先頭1行(件名を除く)だけ置換を繰り返す
                for i in `seq 2 ${GYO_SU}`
                do
                    # 置換対象文字列を生成
                    j=`expr ${i} - 1`
                    TARGET_STRING="%%"`printf "%03d\n" ${j}`"%%"
                    
                    # 置換文字列を生成
                    AFTER_STRING=`sed -n "${i},${i}p" ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME}`
                    # 「\」混入対策
                    AFTER_STRING_A=${AFTER_STRING//\\/\\\\}
                    # 「/」混入対策
                    AFTER_STRING_B=${AFTER_STRING_A//\//\\/}
                    # 「&」混入対策
                    AFTER_STRING_C=${AFTER_STRING_B//\&/\\&}
                    
                    # 置換対象文字列→置換文字列に置換
                    sed "s/${TARGET_STRING}/${AFTER_STRING_C}/g" ${TEMP_FILENAME} > ${sysmail_sender_work_dir}"/"${sysmail_sender_work_file}
                    cat ${sysmail_sender_work_dir}"/"${sysmail_sender_work_file} > ${TEMP_FILENAME}
                done
            fi
        
        # 件名,FROM,TO,CCアドレスは入力フォーム等からユーザが指定
        elif [ ${NUM_OF_FIELDS} -eq 2 ]
        then
            if [ ${IMPORT_MODE} -eq 1 ]
            then
                GYO_SU=`cat ${QUEUE_LIST} | wc -l`
                #01(タイトル行)
                #02(From)
                #03(To)
                #04(Cc)
                #05(本文)
                if [ ${GYO_SU} -lt 5 ]
                then
                    let send_error_count=send_error_count+1
                    let ret_result=ret_result+1
                    shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]]Mail body strings are not found (Number of substitution words : ${NUM_OF_WORDS})."
                    mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                    continue
                fi
            else
                # キューファイルの本文行数と置換単語数を比較し「≠」の場合はエラー扱い
                GYO_SU=`cat ${QUEUE_LIST} | wc -l`
                if [ `expr ${NUM_OF_WORDS} + 4` -ne ${GYO_SU} ]
                then
                    let send_error_count=send_error_count+1
                    let ret_result=ret_result+1
                    shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]Number of substitution words are not equal (QueueFile:`expr ${GYO_SU} - 4`, Number of substitution words:${NUM_OF_WORDS})."
                    mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                    continue
                fi
            fi
            MAIL_SUBJECT=`sed -n "1,1p" ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME}`
            FROM_MAIL_ADDRESS=`sed -n "2,2p" ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME}`
            TO_MAIL_ADDRESS=`sed -n "3,3p" ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME}`
            CC_MAIL_ADDRESS=`sed -n "4,4p" ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME}`
            
            # FROM_MAIL_ADDRESSの書式妥当性チェック
            echo ${FROM_MAIL_ADDRESS} | grep -E '^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+$' > /dev/null 2>&1
            FROM_MAIL_VALIDITY=$?
            if [ ${FROM_MAIL_VALIDITY} -ne 0 ]
            then
                let send_error_count=send_error_count+1
                let ret_result=ret_result+1
                shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]FROM_MAIL_ADDRESS is not found in ${QUEUE_BASENAME}."
                mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                continue
            fi
            
            # TO_MAIL_ADDRESSの書式妥当性チェック
            echo ${TO_MAIL_ADDRESS} |  grep -E '^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+(,[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+)*$' > /dev/null 2>&1
            TO_MAIL_VALIDITY=$?
            if [ ${TO_MAIL_VALIDITY} -ne 0 ]
            then
                let send_error_count=send_error_count+1
                let ret_result=ret_result+1
                shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]TO_MAIL_ADDRESS is not found in ${QUEUE_BASENAME}."
                mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                continue
            fi
            
            # CC_MAIL_ADDRESSの書式妥当性チェック
            echo ${CC_MAIL_ADDRESS} |  grep -E '^$|^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+(,[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+)*$' > /dev/null 2>&1
            CC_MAIL_VALIDITY=$?
            if [ ${CC_MAIL_VALIDITY} -ne 0 ]
            then
                let send_error_count=send_error_count+1
                let ret_result=ret_result+1
                shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]CC_MAIL_ADDRESS is not found in ${QUEUE_BASENAME}."
                mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
                continue
            fi
            
            # 編集用テンポラリファイル名を作成
            TEMP_FILENAME=${sysmail_sender_work_dir}"/"${sysmail_sender_mailfile_prefix}${MAILBODY_ID}${sysmail_sender_mailfile_postfix}"_"`date '+%Y%m%d%H%M%S'"_"`${RANDOM}
            
            if [ ${IMPORT_MODE} -eq 1 ]
            then
                # 編集用テンポラリファイルが存在している場合は削除する
                if [ -f ${TEMP_FILENAME} ]
                then
                    rm -f ${TEMP_FILENAME}
                fi
                
                # 送信キューファイルの5行目以降を本文としてコピーする
                for i in `seq 5 ${GYO_SU}`
                do
                    echo `sed -n "${i},${i}p" ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME}` >> ${TEMP_FILENAME}
                done
            else
                # 編集用にメール本文ファイルをコピーする
                cp ${sysmail_sender_conf_dir}"/"${sysmail_sender_mailfile_prefix}${MAILBODY_ID}${sysmail_sender_mailfile_postfix} ${TEMP_FILENAME}
                
                # 本文行数(先頭4行(件名,from,to,cc)を除く)だけ置換を繰り返す
                for i in `seq 5 ${GYO_SU}`
                do
                    # 置換対象文字列を生成
                    j=`expr ${i} - 4`
                    TARGET_STRING="%%"`printf "%03d\n" ${j}`"%%"
                    
                    # 置換文字列を生成
                    AFTER_STRING=`sed -n "${i},${i}p" ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME}`
                    # 「\」混入対策
                    AFTER_STRING_A=${AFTER_STRING//\\/\\\\}
                    # 「/」混入対策
                    AFTER_STRING_B=${AFTER_STRING_A//\//\\/}
                    # 「&」混入対策
                    AFTER_STRING_C=${AFTER_STRING_B//\&/\\&}
                    
                    # 置換対象文字列→置換文字列に置換
                    sed "s/${TARGET_STRING}/${AFTER_STRING_C}/g" ${TEMP_FILENAME} > ${sysmail_sender_work_dir}"/"${sysmail_sender_work_file}
                    cat ${sysmail_sender_work_dir}"/"${sysmail_sender_work_file} > ${TEMP_FILENAME}
                done
            fi
        
        # フィールド数の過不足(本来2or5個)
        else
            let send_error_count=send_error_count+1
            let ret_result=ret_result+1
            shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]Number of fields is wrong at ${sysmail_sender_conf_file}."
            mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
            continue
        fi
        
        # メールを送付する
        send_mail "${FROM_MAIL_ADDRESS}" "${TO_MAIL_ADDRESS}" "${MAIL_SUBJECT}" "${TEMP_FILENAME}" "${MAILX_OPTION}" "${CC_MAIL_ADDRESS}"
        if [ $? -eq 0 ]
        then
            success_count=`expr ${success_count} + 1`
            shcommonFunction -1 "[${QUEUE_BASENAME}][Info][${success_count}]Send Mail Success."
            mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_suc_dir}"/"${QUEUE_BASENAME}
            if [ $? -eq 0 ]
            then
                shcommonFunction -1 "[${QUEUE_BASENAME}][Info][${success_count}]Queue File(${QUEUE_BASENAME}) are moved to repository for success queue."
            else
                shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${success_count}]Queue File(${QUEUE_BASENAME}) can not be moved to repository for success queue."
            fi
        else
            let send_error_count=send_error_count+1
            let ret_result=ret_result+1
            shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${ret_result}]Send Mail Failure."
            mv ${sysmail_sender_que_bnk_dir}"/"${QUEUE_BASENAME} ${sysmail_sender_que_err_dir}"/"${QUEUE_BASENAME}
            if [ $? -eq 0 ]
            then
                shcommonFunction -1 "[${QUEUE_BASENAME}][Info][${ret_result}]Queue File(${QUEUE_BASENAME}) are moved to repository for failure queue."
            else
                shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${success_count}]Queue File(${QUEUE_BASENAME}) can not be moved to repository for failure queue."
            fi
        fi
        
        # テンポラリファイルを削除
        rm -f ${TEMP_FILENAME}
        if [ $? -ne 0 ]
        then
            shcommonFunction -1 "[${QUEUE_BASENAME}][Error][${success_count}]${TEMP_FILENAME} can not be removed."
        fi
        
    done

    #----------------------------------------------------#
    # ワークファイル削除(存在している場合)
    #----------------------------------------------------#
    if [ -f "${sysmail_sender_work_dir}"/"${sysmail_sender_work_file}" ]
    then
        rm -f ${sysmail_sender_work_dir}"/"${sysmail_sender_work_file}
        if [ $? -ne 0 ]
        then
            shcommonFunction ${LINENO} "[Error]${sysmail_sender_work_dir}/${sysmail_sender_work_file} can not be removed."
        fi
    fi

    #----------------------------------------------------#
    # 件数表示
    #----------------------------------------------------#
    shcommonFunction -1 "[Info]Targets[${input_tgt_count}], Success[${success_count}], Warning[${send_warn_count}], Failure[${send_error_count}]"

fi


#----------------------------------------------------#
# シェルスクリプト終了
#----------------------------------------------------#
shcommonFunction -1 "Process : Finish"

exit 0
