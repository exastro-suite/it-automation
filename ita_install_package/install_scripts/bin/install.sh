#!/bin/bash
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
############################################################
#
# 【概要】
#    ITAをデプロイする
#
############################################################



##################################
#     -----関数定義ここから-----     #
##################################
############################################################
# ログ出力
# @param    $1    string    ログに出力する文字列
# @return   なし
############################################################
log() {
    echo "["`date +"%Y-%m-%d %H:%M:%S"`"] $1" | tee -a "$LOG_FILE"
}

############################################################
# エンコード
# @param    $1     string    エンコードする文字列
# @return   STR    string    エンコードした文字列
############################################################
func_str_encode() {
    STR=$(echo -n "$1" | base64 2>> "$LOG_FILE")
    STR=$(echo "$STR" | tr '[A-Za-z]' '[N-ZA-Mn-za-m]' 2>> "$LOG_FILE")
    echo "$STR"
}

############################################################
# answerファイルのフォーマット確認
# @param    なし
# @return   なし
############################################################
func_answer_format_check() {
    if [ "$val" != 'yes' -a "$val" != 'no' ]; then
        log "ERROR : $key should be set to yes or no."
        log 'INFO : Abort installation.'
        ERR_FLG="false"
        func_exit_and_delete_file
    fi
    for VALUE in "${ARR_DRIVER_CHK[@]}"; do
        if [ "$VALUE" = "$key" ]; then
            ANSWER_DRIVER_CNT=$((ANSWER_DRIVER_CNT+1))
        fi
    done
}

############################################################
# 処理合計数設定
# @param    なし
# @return   なし
############################################################
func_set_total_cnt() {
    
    PROCCESS_TOTAL_CNT=0

    if [ "$BASE_FLG" -eq 1 ]; then
        PROCCESS_TOTAL_CNT=$((PROCCESS_TOTAL_CNT+20))
    fi

    if [ "$ANSIBLE_FLG" -eq 1 ]; then
        PROCCESS_TOTAL_CNT=$((PROCCESS_TOTAL_CNT+6))
    fi

    if [ "$COBBLER_FLG" -eq 1 ]; then
        PROCCESS_TOTAL_CNT=$((PROCCESS_TOTAL_CNT+5))
    fi

    if [ "$TERRAFORM_FLG" -eq 1 ]; then
        PROCCESS_TOTAL_CNT=$((PROCCESS_TOTAL_CNT+3))
    fi

    if [ "$CREATEPARAM_FLG" -eq 1 ]; then
        PROCCESS_TOTAL_CNT=$((PROCCESS_TOTAL_CNT+3))
    fi

    if [ "$CREATEPARAM2_FLG" -eq 1 ]; then
        PROCCESS_TOTAL_CNT=$((PROCCESS_TOTAL_CNT+2))
    fi

    if [ "$CREATEPARAM3_FLG" -eq 1 ]; then
        PROCCESS_TOTAL_CNT=$((PROCCESS_TOTAL_CNT+1))
    fi

    if [ "$HOSTGROUP_FLG" -eq 1 ]; then
        PROCCESS_TOTAL_CNT=$((PROCCESS_TOTAL_CNT+3))
    fi

    if [ "$CICD_FLG" -eq 1 ]; then
        PROCCESS_TOTAL_CNT=$((PROCCESS_TOTAL_CNT+4))
    fi

    echo $PROCCESS_TOTAL_CNT
}
############################################################
# インストールメッセージ作成
# @param    $1     string       各ドライバフラグ(例:ANSIBLE_FLG)
# @return   なし
############################################################
func_install_messasge() {
    MESSAGE=""
    if [ BASE_FLG = ${1} ]; then
        MESSAGE="ita_base"
    fi

    if [ ANSIBLE_FLG = ${1} ]; then
        MESSAGE="Ansible driver"
    fi

    if [ COBBLER_FLG = ${1} ]; then
        MESSAGE="Cobbler driver"
    fi

    if [ TERRAFORM_FLG = ${1} ]; then
        MESSAGE="Terraform driver"
    fi

    if [ CREATEPARAM_FLG = ${1} ]; then
        MESSAGE="Createparam"
    fi

    if [ CREATEPARAM2_FLG = ${1} ]; then
        MESSAGE="Createparam2"
    fi

    if [ CREATEPARAM3_FLG = ${1} ]; then
        MESSAGE="Createparam3"
    fi

    if [ HOSTGROUP_FLG = ${1} ]; then
        MESSAGE="Hostgroup"
    fi

    if [ CICD_FLG = ${1} ]; then
        MESSAGE="CI/CD for IaC"
    fi

    echo "$MESSAGE"
}

############################################################
# データリレイストレージ作成
# @param    $1     string       各ドライバフラグ(例:ANSIBLE_FLG)
# @return   なし
############################################################
func_create_datarelaystorage() {
    MESSAGE=`func_install_messasge ${1}`
    DRIVER=`echo ${1} | cut -d "_" -f 1 | sed 's/^ \(.*\) $/\1/'`
    
    if [ ${!1} -eq 1 ]; then
        log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Create data relay storage for $MESSAGE."
        if test -d "$ITA_DIRECTORY"/data_relay_storage/${DRIVER,,}_driver ; then
            log "INFO : $ITA_DIRECTORY/data_relay_storage/${DRIVER,,}_driver already exists."
        else
            mkdir -m 777 -p "$ITA_DIRECTORY"/data_relay_storage/${DRIVER,,}_driver 2>> "$LOG_FILE"
        fi
        PROCCESS_CNT=$((PROCCESS_CNT+1))
    fi
}

############################################################
# データベースに各ドライバのテーブルを作成
# @param    $1     string       各ドライバフラグ(例:ANSIBLE_FLG)
# @return   なし
############################################################
func_create_tables() {
    MESSAGE=`func_install_messasge ${1}`
    DRIVER=`echo ${1} | cut -d "_" -f 1 | sed 's/^ \(.*\) $/\1/'`
    
    if [ ${!1} -eq 1 ]; then
        log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Create tables for $MESSAGE."
        if ! test -e "$LIST_DIR/${DRIVER,,}_table_list.txt" ; then
            log "WARNING : ${DRIVER,,}_table_list.txt does not be found."
        else
            source "$BIN_DIR/create-tables-and-views.sh" "${DRIVER,,}_table_list.txt" "$DB_USERNAME" "$DB_PASSWORD_ON_CMD" "$DB_NAME" "$ITA_LANGUAGE" "$ITA_DIRECTORY" 2>> "$LOG_FILE"
            while read LINE; do
                FILE_PATH="$LOG_DIR/$LINE.log"
                if ! test -e "$FILE_PATH" ; then
                    log "WARNING : $FILE_PATH does not be found."
                else
                    FILE_SIZE=`wc -c < "$FILE_PATH"`
                    if [ "$FILE_SIZE" -ne 0 ]; then
                        log "WARNING : The size of $FILE_PATH is incorrect."
                    fi
                fi
            done < "$LIST_DIR/${DRIVER,,}_table_list.txt"
        fi
        PROCCESS_CNT=$((PROCCESS_CNT+1))
    fi
}

############################################################
# リリースファイル設置
# @param    $1     string       各種リリースファイル
# @return   なし
############################################################
func_release_place() {
	DRIVER=`echo ${1} | cut -d "_" -f 2 | sed 's/^ \(.*\) $/\1/'`

	FLG=`echo ${DRIVER^^} | cut -d "-" -f 1 | sed 's/^ \(.*\) $/\1/'`_FLG
	MESSAGE=`func_install_messasge ${FLG}`

    if [ ${!FLG} -eq 1 ]; then
        log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Place release file for $MESSAGE."
        cp -p ../ITA/ita-releasefiles/${1} "$ITA_DIRECTORY"/ita-root/libs/release/ 2>> "$LOG_FILE"
        if ! test -e "$ITA_DIRECTORY"/ita-root/libs/release/${1} ; then
            log "WARNING : Failed to place $ITA_DIRECTORY/ita-root/libs/release/${1}."
        fi
        PROCCESS_CNT=$((PROCCESS_CNT+1))
    fi
}

############################################################
# コンフィグファイル設置確認
# @param    $1     string       各ドライバフラグ(例:ANSIBLE_FLG)
# @return   なし
############################################################
func_config_place() {
    MESSAGE=`func_install_messasge ${1}`
    DRIVER=`echo ${1} | cut -d "_" -f 1 | sed 's/^ \(.*\) $/\1/'`
    
    if [ ${!1} -eq 1 ]; then
        log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Place config files for $MESSAGE."
        if ! test -e "$LIST_DIR/${DRIVER,,}_config_list.txt" ; then
            log "WARNING : ${DRIVER,,}_config_list.txt does not be found."
        else
            while read LINE; do
                if ! test -e "$ITA_DIRECTORY""$LINE" ; then
                    log "WARNING : Failed to place $LINE."
                fi
            done < "$LIST_DIR/${DRIVER,,}_config_list.txt"
        fi
        PROCCESS_CNT=$((PROCCESS_CNT+1))
    fi
}

############################################################
# サービスの登録
# @param    $1     string       各ドライバフラグ(例:ANSIBLE_FLG)
# @return   なし
############################################################
func_services_set() {
    MESSAGE=`func_install_messasge ${1}`
    DRIVER=`echo ${1} | cut -d "_" -f 1 | sed 's/^ \(.*\) $/\1/'`
        
    if [ ${!1} -eq 1 ]; then
        log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Set up services for $MESSAGE."
        
        if ! test -e "$LIST_DIR/${DRIVER,,}_service_list.txt"; then
            log "WARNING : ${DRIVER,,}_service_list.txt does not be found."
        else
            sed -i -e '/^$/d' "$LIST_DIR/${DRIVER,,}_service_list.txt" 2>> "$LOG_FILE"
            source "$BIN_DIR/register-services_RHEL.sh" "$LIST_DIR/${DRIVER,,}_service_list.txt" "$ITA_DIRECTORY" 2>> "$LOG_FILE"
            while read LINE; do
                service=`basename ${LINE}`
                RES=`systemctl | grep "$service" | grep 'running'`
                if [ ${#RES} -eq 0 ]; then
                    log "WARNING : Failed to set up $LINE."
                fi
            done < "$LIST_DIR/${DRIVER,,}_service_list.txt"
        fi
        PROCCESS_CNT=$((PROCCESS_CNT+1))
    fi
}


############################################################
# クーロン登録
# @param    $1     string       各ドライバフラグ(例:ANSIBLE_FLG)
# @return   なし
############################################################
func_crontab_set() {
    MESSAGE=`func_install_messasge ${1}`
    DRIVER=`echo ${1} | cut -d "_" -f 1 | sed 's/^ \(.*\) $/\1/'`
    
    if [ ${!1} -eq 1 ]; then
        log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Set up crontab for $MESSAGE."
        if ! test -e "$LIST_DIR/${DRIVER,,}_crontab_list.txt" ; then
            log "WARNING : ${DRIVER,,}_crontab_list.txt does not be found."
        else
            cp "$LIST_DIR/${DRIVER,,}_crontab_list.txt" "/tmp/" 2>> "$LOG_FILE"
            sed -i -e "s:${REPLACE_CHAR["ita_directory"]}:$ITA_DIRECTORY:g" "/tmp/${DRIVER,,}_crontab_list.txt" 2>> "$LOG_FILE"
            sed -i -e '/^$/d' "/tmp/${DRIVER,,}_crontab_list.txt" 2>> "$LOG_FILE"
            source "$BIN_DIR/register-crontab.sh" "${DRIVER,,}_crontab_list.txt" 2>> "$LOG_FILE"
            while read LINE; do
                LINE=${LINE//\'/}
                LINE=${LINE//* /}
                LINE=${LINE//*\//}
                RES=`crontab -l | grep "$LINE"`
                if [ ${#RES} -eq 0 ]; then
                    log "WARNING : Failed to set up $LINE."
                fi
            done < "/tmp/${DRIVER,,}_crontab_list.txt"
            rm -f /tmp/${DRIVER,,}_crontab_list.txt
        fi
        PROCCESS_CNT=$((PROCCESS_CNT+1))
    fi
}

##################################
#     -----関数定義ここまで-----     #
##################################

##################################
#      -----関数用配列ここから-----  #
##################################

#データリレイストレージ作成関数用配列
#データリレイストレージを作成するドライバを記載する
CREATE_DATARELAYSTORAGE=(
    ANSIBLE_FLG
    COBBLER_FLG
)
#テーブル作成作成関数用配列
#テーブルを作成するドライバを記載する
CREATE_TABLES=(
    ANSIBLE_FLG
    COBBLER_FLG
    TERRAFORM_FLG
    CREATEPARAM_FLG
    CREATEPARAM2_FLG
    CREATEPARAM3_FLG
    HOSTGROUP_FLG
    CICD_FLG
)

#リリースファイル設置作成関数用配列
#リリースファイルを設置するドライバのリリースファイル名を記載する
RELEASE_PLASE=(
    ita_ansible-driver
    ita_cobbler-driver
    ita_terraform-driver
    ita_createparam
    ita_hostgroup
    ita_cicd
)

#コンフィグファイル設置確認作成関数用配列
#コンフィグファイルを設置するドライバを記載する
CONFIG_PLACE=(
    ANSIBLE_FLG
    COBBLER_FLG
    CICD_FLG
)

#サービスの登録作成関数用配列
#サービスの登録するドライバを記載する
SERVICES_SET=(
    BASE_FLG
    ANSIBLE_FLG
    COBBLER_FLG
    TERRAFORM_FLG
    CREATEPARAM_FLG
    CREATEPARAM2_FLG
    HOSTGROUP_FLG
    CICD_FLG
)

#クーロンタブ設定関数用配列
#クーロンタブを設定するドライバを記載する
CRONTAB_SET=(
    BASE_FLG
)


##################################
#    -----関数用配列ここまで-----   #
##################################

############################################################
log 'INFO : -----MODE[INSTALL] START-----'
############################################################

BASE_FLG=0
ANSIBLE_FLG=0
COBBLER_FLG=0
TERRAFORM_FLG=0
CREATEPARAM_FLG=0
CREATEPARAM2_FLG=0
CREATEPARAM3_FLG=0
HOSTGROUP_FLG=0
CICD_FLG=0

declare -A REPLACE_CHAR;
REPLACE_CHAR=(
    ["ita_directory"]="%%%%%ITA_DIRECTORY%%%%%"
    ["ita_domain"]="%%%%%ITA_DOMAIN%%%%%"
    ["certificate"]="%%%%%CERTIFICATE_FILE%%%%%"
    ["private_key"]="%%%%%PRIVATE_KEY_FILE%%%%%"
)

DRIVER_CNT=0
ANSWER_DRIVER_CNT=0
ARR_DRIVER_CHK=('ita_base' 'ansible_driver' 'cobbler_driver' 'terraform_driver' 'createparam' 'hostgroup' 'cicd_for_iac')

CERTIFICATE_FILE=''
PRIVATE_KEY_FILE=''
CSR_FILE=''

#answerファイル読み取り
while read LINE; do
    if [ "$LINE" ]; then
        DRIVER=`echo $LINE | tr -d " "`

        if [ `echo "$DRIVER" | cut -c 1` = "#" ]; then
            continue
        elif [ `echo "$DRIVER" | wc -l` -eq 0 ]; then
            continue
        fi

        key=`echo $DRIVER | cut -d ":" -f 1 | sed 's/^ \(.*\) $/\1/'`
        val=`echo $DRIVER | cut -d ":" -f 2 | sed 's/^ \(.*\) $/\1/'`
       
        if [ "$key" = 'ita_base' -a "$val" = 'no' ]; then
            func_answer_format_check
            if ! test -d "$ITA_DIRECTORY"/ita-root ; then
                log 'ERROR : It is necessary to install ITA main functions (ita_base).'
                log 'INFO : Abort installation.'
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
        elif [ "$key" = 'ita_base' ]; then
            func_answer_format_check
            if [ "$val" = 'yes' ]; then
                if test -d "$ITA_DIRECTORY"/ita-root ; then
                    log 'ERROR : ITA main functions (ita_base) have already been installed.'
                    log 'INFO : Abort installation.'
                    ERR_FLG="false"
                    func_exit_and_delete_file
                fi
            fi
            
            BASE_FLG=1
        elif [ "$key" = 'ansible_driver' ]; then
            func_answer_format_check
            if [ "$val" = 'yes' ]; then
                ANSIBLE_FLG=1
            fi
        elif [ "$key" = 'cobbler_driver' ]; then
            func_answer_format_check
            if [ "$val" = 'yes' ]; then
                COBBLER_FLG=1
            fi
        elif [ "$key" = 'terraform_driver' ]; then
            func_answer_format_check
            if [ "$val" = 'yes' ]; then
                TERRAFORM_FLG=1
            fi
        elif [ "$key" = 'createparam' ]; then
            func_answer_format_check
            if [ "$val" = 'yes' ]; then
                CREATEPARAM_FLG=1
            fi
        elif [ "$key" = 'hostgroup' ]; then
            func_answer_format_check
            if [ "$val" = 'yes' ]; then
                HOSTGROUP_FLG=1
            fi
        elif [ "$key" = 'cicd_for_iac' ]; then
            func_answer_format_check
            if [ "$val" = 'yes' ]; then
                CICD_FLG=1
            fi
        fi
    fi
done < "$COPY_ANSWER_FILE"

#フォーマットが正しくなかった場合は処理終了
if [ "$ANSWER_DRIVER_CNT" -ne ${#ARR_DRIVER_CHK[@]} ]; then
    log 'ERROR : The format of Answer-file is incorrect.'
    log 'INFO : Abort installation.'
    ERR_FLG="false"
    func_exit_and_delete_file
fi

# /tmp/ita_answers.txtを削除
rm -f "$COPY_ANSWER_FILE" 2>> "$LOG_FILE"

# ITA本体、ドライバがすべてnoの場合は処理を終了
for VAL in ${CREATE_TABLES[@]}; do
    DRIVER_CNT=$((DRIVER_CNT+${!VAL}))
done
INSTALL_CNT=$((DRIVER_CNT+BASE_FLG))
if [ "$INSTALL_CNT" -eq 0 ]; then
    log 'ERROR : No installation target has been selected.'
    log 'INFO : Abort installation.'
    ERR_FLG="false"
    func_exit_and_delete_file

fi

if [ $BASE_FLG -eq 1 ]; then
    log "INFO : Installation target : ita_base"
fi
if [ $ANSIBLE_FLG -eq 1 ]; then
    log "INFO : Installation target : ansible_driver"
fi
if [ $COBBLER_FLG -eq 1 ]; then
    log "INFO : Installation target : cobbler_driver"
fi
if [ $TERRAFORM_FLG -eq 1 ]; then
    log "INFO : Installation target : terraform_driver"
fi
if [ $CREATEPARAM_FLG -eq 1 ]; then
    log "INFO : Installation target : create_param"
fi
if [ $HOSTGROUP_FLG -eq 1 ]; then
    log "INFO : Installation target : hostgroup"
fi
if [ $CICD_FLG -eq 1 ]; then
    log "INFO : Installation target : CI/CD for IaC"
fi


#ドライバがインストールされているか確認
if [ "$ANSIBLE_FLG" -eq 1 ]; then
    if test -e "$ITA_DIRECTORY"/ita-root/libs/release/ita_ansible-driver ; then
        log 'WARNING : Ansible driver has already been installed.'
        ANSIBLE_FLG=0
    fi
fi

if [ "$COBBLER_FLG" -eq 1 ]; then
    if test -e "$ITA_DIRECTORY"/ita-root/libs/release/ita_cobbler-driver ; then
        log 'WARNING : Cobbler driver has already been installed.'
        COBBLER_FLG=0
    fi
fi

if [ "$TERRAFORM_FLG" -eq 1 ]; then
    if test -e "$ITA_DIRECTORY"/ita-root/libs/release/ita_terraform-driver ; then
        log 'WARNING : Terraform driver has already been installed.'
        TERRAFORM_FLG=0
    fi
fi

if [ -e "$ITA_DIRECTORY/ita-root/libs/release/ita_createparam" ] &&  [ -e "$ITA_DIRECTORY/ita-root/libs/release/ita_ansible-driver" ] ; then
    CREATEPARAM2_FLG=0
elif [ -e "$ITA_DIRECTORY/ita-root/libs/release/ita_ansible-driver" ] && [ "$CREATEPARAM_FLG" -eq 1 ] ; then
    CREATEPARAM2_FLG=1
elif [ -e "$ITA_DIRECTORY/ita-root/libs/release/ita_createparam" ] && [ "$ANSIBLE_FLG" -eq 1 ] ; then
    CREATEPARAM2_FLG=1
elif [ "$ANSIBLE_FLG" -eq 1 ] && [ "$CREATEPARAM_FLG" -eq 1 ] ; then
    CREATEPARAM2_FLG=1
fi

if [ -e "$ITA_DIRECTORY/ita-root/libs/release/ita_createparam" ] &&  [ -e "$ITA_DIRECTORY/ita-root/libs/release/ita_hostgroup" ] ; then
    CREATEPARAM3_FLG=0
elif [ -e "$ITA_DIRECTORY/ita-root/libs/release/ita_hostgroup" ] && [ "$CREATEPARAM_FLG" -eq 1 ] ; then
    CREATEPARAM3_FLG=1
elif [ -e "$ITA_DIRECTORY/ita-root/libs/release/ita_createparam" ] && [ "$HOSTGROUP_FLG" -eq 1 ] ; then
    CREATEPARAM3_FLG=1
elif [ "$HOSTGROUP_FLG" -eq 1 ] && [ "$CREATEPARAM_FLG" -eq 1 ] ; then
    CREATEPARAM3_FLG=1
fi

if [ "$CREATEPARAM_FLG" -eq 1 ]; then
    if test -e "$ITA_DIRECTORY"/ita-root/libs/release/ita_createparam ; then
        log 'WARNING : Createparam has already been installed.'
        CREATEPARAM_FLG=0
    fi
fi

if [ "$HOSTGROUP_FLG" -eq 1 ]; then
    if test -e "$ITA_DIRECTORY"/ita-root/libs/release/ita_hostgroup ; then
        log 'WARNING : Hostgroup has already been installed.'
        HOSTGROUP_FLG=0
    fi
fi

if [ "$CICD_FLG" -eq 1 ]; then
    if test -e "$ITA_DIRECTORY"/ita-root/libs/release/ita_cicd ; then
        log 'WARNING : CI/CD for IaC has already been installed.'
        CICD_FLG=0
    fi
fi

#秘密鍵と証明書のファイル名を取得（ITA自己証明書を作成する場合は証明書署名要求ファイル名も設定）
if [ "$CERTIFICATE_PATH" != "" -a "$PRIVATE_KEY_PATH" != "" ]; then
    CERTIFICATE_FILE=$(echo $(basename ${CERTIFICATE_PATH})) 2>> "$LOG_FILE"
    PRIVATE_KEY_FILE=$(echo $(basename ${PRIVATE_KEY_PATH})) 2>> "$LOG_FILE"
else
    CERTIFICATE_FILE="$ITA_DOMAIN.crt"
    PRIVATE_KEY_FILE="$ITA_DOMAIN.key"
    CSR_FILE="$ITA_DOMAIN.csr"
    echo "subjectAltName=DNS:$ITA_DOMAIN" > /tmp/san.txt
fi

PROCCESS_TOTAL_CNT=`func_set_total_cnt`

PROCCESS_CNT=1
if [ "$BASE_FLG" -eq 1 ]; then
    #################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Set Language."
    #################################################################################################
    if ! test -e "../ITA/ita-confs/commonconfs/app_msg_language.txt" ; then
        log 'ERROR : app_msg_language.txt does not be found.'
    else
        if [ ${ITA_LANGUAGE} = 'en_US' ]; then
            sed -i -e "s/ja_JP/en_US/g" ../ITA/ita-confs/commonconfs/app_msg_language.txt 2>> "$LOG_FILE" 
        else
            sed -i -e "s/en_US/ja_JP/g" ../ITA/ita-confs/commonconfs/app_msg_language.txt 2>> "$LOG_FILE"
        fi
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    #################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Create database and user for ITA."
    #################################################################################################
    if ! test -e "$SQL_DIR/create-db-and-user_for_MySQL.sql" ; then
        log 'ERROR : create-db-and-user_for_MySQL.sql does not be found.'
    else
        cp "$SQL_DIR/create-db-and-user_for_MySQL.sql" /tmp/ 2>> "$LOG_FILE"
        sed -i -e "s/ITA_DB/$DB_NAME/g" /tmp/create-db-and-user_for_MySQL.sql 2>> "$LOG_FILE"
        sed -i -e "s/ITA_USER/$DB_USERNAME/g" /tmp/create-db-and-user_for_MySQL.sql 2>> "$LOG_FILE"
        sed -i -e "s/ITA_PASSWD/$DB_PASSWORD/g" /tmp/create-db-and-user_for_MySQL.sql 2>> "$LOG_FILE"
        RES=$(env MYSQL_PWD="$DB_ROOT_PASSWORD" mysql -uroot < /tmp/create-db-and-user_for_MySQL.sql 2>&1 | tee -a "$LOG_FILE")
        if echo "$RES" | grep ERROR ; then
            log 'ERROR : Failed to connect to the database.'
            log 'INFO : Abort installation.'
            ERR_FLG="false"
            func_exit_and_delete_file
        fi
        rm -f /tmp/create-db-and-user_for_MySQL.sql
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    ##################################################################################################
    #Create tables for ita_base functions."
    ##################################################################################################
    if ! test -e "$BIN_DIR/create-tables-and-views.sh" ; then
        log 'WARNING : create-tables-and-views.sh does not be found.'
    else
        func_create_tables BASE_FLG
    fi

    #################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Write exastro-it-automation in /etc/hosts."
    #################################################################################################
    echo "127.0.0.1     $ITA_DOMAIN exastro-it-automation" >> /etc/hosts 2>> "$LOG_FILE"
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    #################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Place the certificate and private-key for https access."
    #################################################################################################
    if [ "${CERTIFICATE_PATH}" != "" -a "${PRIVATE_KEY_PATH}" != "" ]; then
        # CERTIFICATE_PATH と PRIVATE_KEY_PATH がita_answers.txtに両方入力されている場合は、ユーザー指定の証明書・秘密鍵を設置
        # ユーザー指定証明書・秘密鍵設置
        if test -e "${CERTIFICATE_PATH}" ; then
            if test -e "${PRIVATE_KEY_PATH}" ; then
                # 両方の指定のパスにファイルが存在する場合のみ/etc/pki/tls/certs/にファイルをコピー
                cp -p "${CERTIFICATE_PATH}" /etc/pki/tls/certs/ 2>> "$LOG_FILE"
                cp -p "${PRIVATE_KEY_PATH}" /etc/pki/tls/certs/ 2>> "$LOG_FILE"
            else
                # 指定のパスにファイルがない場合は異常終了
                log "ERORR : ${PRIVATE_KEY_PATH} does not be found."
                log 'INFO : Abort installation.'
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
        else 
            # 指定のパスにファイルがない場合は異常終了
            log "ERORR : ${CERTIFICATE_PATH} does not be found."
            log 'INFO : Abort installation.'
            ERR_FLG="false"
            func_exit_and_delete_file
        fi
    elif [ "${CERTIFICATE_PATH}" = "" -a "${PRIVATE_KEY_PATH}" = "" ]; then
        # CERTIFICATE_PATH と PRIVATE_KEY_PATH がどちらも入力されていない場合は、ITAで作成する自己証明書・秘密鍵を設置 
        # 秘密鍵を生成
        openssl genrsa 2048 > /tmp/"$PRIVATE_KEY_FILE" 2>> "$LOG_FILE"
        # 証明書署名要求を生成
        expect -c "
        set timeout -1
        spawn openssl req -new -key /tmp/${PRIVATE_KEY_FILE} -out /tmp/${CSR_FILE}
        expect \"Country Name\"
        send \"JP\\r\"
        expect \"State or Province Name\"
        send \"\\r\"
        expect \"Locality Name\"
        send \"\\r\"
        expect \"Organization Name\"
        send \"\\r\"
        expect \"Organizational Unit Name\"
        send \"\\r\"
        expect \"Common Name\"
        send \"${ITA_DOMAIN}\\r\"
        expect \"Email Address\"
        send \"\\r\"
        expect \"A challenge password\"
        send \"\\r\"
        expect \"An optional company name\"
        send \"\\r\"
        interact
        " >> "$LOG_FILE" 2>&1
        # サーバ証明書を生成
        openssl x509 -days 3650 -req -signkey /tmp/"$PRIVATE_KEY_FILE" -extfile /tmp/san.txt < /tmp/"$CSR_FILE" > /tmp/"$CERTIFICATE_FILE" 2>> "$LOG_FILE"
        # 作成した証明書署名要求を削除
        rm -f /tmp/"$CSR_FILE" 2>> "$LOG_FILE"
        rm -f /tmp/san.txt 2>> "$LOG_FILE"
        # 作成した秘密鍵とサーバ証明書を/etc/pki/tls/certs/へ移動
        mv /tmp/"$PRIVATE_KEY_FILE" /etc/pki/tls/certs/ 2>> "$LOG_FILE"
        mv /tmp/"$CERTIFICATE_FILE" /etc/pki/tls/certs/ 2>> "$LOG_FILE"
    else
        # CERTIFICATE_PATH と PRIVATE_KEY_PATH どちらか一方だけ入力されている場合は異常終了
        if [ "${CERTIFICATE_PATH}" = "" ]; then
            log "ERORR : Should be Enter [certificate_path]."
            log 'INFO : Abort installation.'
            ERR_FLG="false"
            func_exit_and_delete_file
        elif [ "${PRIVATE_KEY_PATH}" = "" ]; then
            log "ERORR : Should be Enter [private_key_path]."
            log 'INFO : Abort installation.'
            ERR_FLG="false"
            func_exit_and_delete_file
        fi
    fi

    # /etc/pki/tls/certs/ に秘密鍵とサーバ証明書が設置できたかをチェック
    if ! test -e /etc/pki/tls/certs/"$PRIVATE_KEY_FILE" ; then
        log "WARNING : Failed to place /etc/pki/tls/certs/$PRIVATE_KEY_FILE."
    fi
    if ! test -e /etc/pki/tls/certs/"$CERTIFICATE_FILE" ; then
        log "WARNING : Failed to place /etc/pki/tls/certs/$CERTIFICATE_FILE."
    fi

    PROCCESS_CNT=$((PROCCESS_CNT+1))

    #################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Place PHP configuration file."
    #################################################################################################
    mv /etc/php.ini /etc/php.ini_original 2>> "$LOG_FILE"
    if ! test -e /etc/php.ini_original ; then
        log 'WARNING : Failed to place /etc/php.ini_original.'
    fi

    if [ ${LINUX_OS} = 'RHEL7' -o ${LINUX_OS} = 'CentOS7' ]; then
        cp -p ../ext_files_for_CentOS7.x/etc/php.ini /etc/ 2>> "$LOG_FILE"
    else
        cp -p ../ext_files_for_CentOS8.x/etc/php.ini /etc/ 2>> "$LOG_FILE"
    fi
    if ! test -e /etc/php.ini ; then
        log 'WARNING : Failed to place /etc/php.ini.'
    fi
    
    if [ ${LINUX_OS} = 'RHEL8' -o ${LINUX_OS} = 'CentOS8' ]; then
        mv /etc/php-fpm.d/www.conf /etc/php-fpm.d/www.conf_original 2>> "$LOG_FILE"
        if ! test -e /etc/php-fpm.d/www.conf_original ; then
            log 'WARNING : Failed to place /etc/php-fpm.d/www.conf_original.'
        fi
        cp -p ../ext_files_for_CentOS8.x/etc_php-fpm.d/www.conf /etc/php-fpm.d/ 2>> "$LOG_FILE"
        if test -e /etc/php-fpm.d/www.conf ; then
            sed -i -e "s:${REPLACE_CHAR["ita_directory"]}:$ITA_DIRECTORY:g" /etc/php-fpm.d/www.conf 2>> "$LOG_FILE"
        else
            log 'WARNING : Failed to place /etc/php-fpm.d/www.conf.'
        fi
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    #################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Place Apache(httpd) configuration file."
    #################################################################################################
    if [ ${LINUX_OS} = 'RHEL7' -o ${LINUX_OS} = 'CentOS7' ]; then
        cp -p ../ext_files_for_CentOS7.x/etc_httpd_conf.d/vhosts_exastro-it-automation.conf /etc/httpd/conf.d/ 2>> "$LOG_FILE"
    else
        cp -p ../ext_files_for_CentOS8.x/etc_httpd_conf.d/vhosts_exastro-it-automation.conf /etc/httpd/conf.d/ 2>> "$LOG_FILE"
    fi
    if test -e /etc/httpd/conf.d/vhosts_exastro-it-automation.conf ; then
        sed -i -e "s:${REPLACE_CHAR["ita_directory"]}:$ITA_DIRECTORY:g" /etc/httpd/conf.d/vhosts_exastro-it-automation.conf 2>> "$LOG_FILE"
        sed -i -e "s:${REPLACE_CHAR["ita_domain"]}:$ITA_DOMAIN:g" /etc/httpd/conf.d/vhosts_exastro-it-automation.conf 2>> "$LOG_FILE"
        sed -i -e "s:${REPLACE_CHAR["certificate"]}:$CERTIFICATE_FILE:g" /etc/httpd/conf.d/vhosts_exastro-it-automation.conf 2>> "$LOG_FILE"
        sed -i -e "s:${REPLACE_CHAR["private_key"]}:$PRIVATE_KEY_FILE:g" /etc/httpd/conf.d/vhosts_exastro-it-automation.conf 2>> "$LOG_FILE"
    else
        log 'WARNING : Failed to place /etc/httpd/conf.d/vhosts_exastro-it-automation.conf.'
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    #################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Create directory to place ITA."
    #################################################################################################
    if ! test -d "$ITA_DIRECTORY" ; then
        mkdir -p "$ITA_DIRECTORY" 2>> "$LOG_FILE"
        if ! test -d "$ITA_DIRECTORY" ; then
            log "ERROR : Failed to make $ITA_DIRECTORY directory."
            log 'INFO : Abort installation.'
            ERR_FLG="false"
            func_exit_and_delete_file
        fi
    else
        log "INFO : $ITA_DIRECTORY already exists."
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    #################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Check the execute permission of the parent directory of ITA."
    #################################################################################################
    #ITAディレクトリの親ディレクトリ取得
    PARENT_DIR=$(dirname "$ITA_DIRECTORY")
    #親ディレクトリの権限のOthersに実行権限があるかチェックする(PARENT_DIRが"/"になるまで繰り返し)
    while [ "$PARENT_DIR" != "/" ] ; do
        ls -ld "$PARENT_DIR" | awk '{print substr($0, 8, 3)}' | grep -q x 
        if [ $? != 0 ]; then
            log "ERROR : The parent directory of ITA does not have execute permission for \"Other users\".(dir:$PARENT_DIR)"
            log 'INFO : Abort installation.'
            ERR_FLG="false"
            func_exit_and_delete_file
        fi
        #権限をチェックしたディレクトリの親ディレクトリを取得する。
        PARENT_DIR=$(dirname "$PARENT_DIR")
    done
    #ルートディレクトリのOthersに実行権限があるかチェックする。
    ls -ld / | awk '{print substr($0, 8, 3)}' | grep -q x
    if [ $? != 0 ]; then
        log "ERROR : The parent directory of ITA does not have execute permission for \"Other users\".(dir:$PARENT_DIR)"
        log 'INFO : Abort installation.'
        ERR_FLG="false"
        func_exit_and_delete_file
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    ################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Create directory to store session files."
    ################################################################################################
    if ! test -d "$ITA_DIRECTORY"/ita_sessions ; then
        mkdir -m 777 "$ITA_DIRECTORY"/ita_sessions 2>> "$LOG_FILE"
        if ! test -d "$ITA_DIRECTORY"/ita_sessions ; then
            log "WARNING : Failed to make $ITA_DIRECTORY/ita_sessions directory."
        fi
    else
        log "INFO : $ITA_DIRECTORY/ita_sessions already exists."
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    ################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Create data relay storage for symphony."
    ################################################################################################
    if test -d "$ITA_DIRECTORY"/data_relay_storage/symphony ; then
        log "INFO : $ITA_DIRECTORY/data_relay_storage/symphony already exists."
    else
        mkdir -m 777 -p "$ITA_DIRECTORY"/data_relay_storage/symphony 2>> "$LOG_FILE"
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    ################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Create data relay storage for conductor."
    ################################################################################################
    if test -d "$ITA_DIRECTORY"/data_relay_storage/conductor ; then
        log "INFO : $ITA_DIRECTORY/data_relay_storage/conductor already exists."
    else
        mkdir -m 777 -p "$ITA_DIRECTORY"/data_relay_storage/conductor 2>> "$LOG_FILE"
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    #################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Place ITA full functions."
    #################################################################################################
    cp -rp ../ITA/ita-contents/ita-root "$ITA_DIRECTORY"/ 2>> "$LOG_FILE"
    if ! test -d "$ITA_DIRECTORY"/ita-root ; then
        log 'WARNING : Failed to place ITA full functions.'
    fi
    
    if ! test -e "$LIST_DIR/create_dir_list.txt" ; then
        log "WARNING : create_dir_list.txt does not be found."
    else
        while read LINE; do
            mkdir -p "$ITA_DIRECTORY""$LINE"
            if [ ! -e "$ITA_DIRECTORY""$LINE" ]; then
                log "WARNING : Failed to create $LINE."
            fi
        done < "$LIST_DIR/create_dir_list.txt"
    fi

    if ! test -e "$LIST_DIR/777_list.txt" ; then
        log "WARNING : 777_list.txt does not be found."
    else
        while read LINE; do
            chmod -- 777 "$ITA_DIRECTORY""$LINE"
            LINE_1="${LINE%\/*}"
            LINE_2="${LINE##*/}"
            RES=`ls -l "$ITA_DIRECTORY""$LINE_1" 2> /dev/null | grep -- "$LINE_2" | grep rwxrwxrwx`
            if [ "${#RES}" -eq 0 ]; then
                log "WARNING : Failed to place $LINE."
            fi
        done < "$LIST_DIR/777_list.txt"
    fi
    if ! test -e "$LIST_DIR/755_list.txt" ; then
        log "WARNING : 755_list.txt does not be found."
    else
        while read LINE; do
            chmod -- 755 "$ITA_DIRECTORY""$LINE"
            LINE_1="${LINE%\/*}"
            LINE_2="${LINE##*/}"
            RES=`ls -l "$ITA_DIRECTORY""$LINE_1" 2> /dev/null | grep -- "$LINE_2" | grep rwxr-xr-x`
            if [ "${#RES}" -eq 0 ]; then
                log "WARNING : Failed to place $LINE."
            fi
        done < "$LIST_DIR/755_list.txt"
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    ##################################################################################################
    #Place ITA release file for base functions."
    ##################################################################################################
    func_release_place ita_base
    
    ################################################################################################
    #Place ITA config files for base functions."
    ################################################################################################
    cp -rp ../ITA/ita-confs/* "$ITA_DIRECTORY"/ita-root/confs/ 2>> "$LOG_FILE"
    
    for file in `find "$ITA_DIRECTORY"/ita-root/confs/ -type f`; do
        sed -i -e "s:${REPLACE_CHAR["ita_directory"]}:$ITA_DIRECTORY:g" "$file" 2>> "$LOG_FILE"
    done
    
    func_config_place BASE_FLG

    DB_NAME_ENC=`func_str_encode "mysql:dbname=$DB_NAME;host=localhost"`
    echo "$DB_NAME_ENC" > "$ITA_DIRECTORY"/ita-root/confs/commonconfs/db_connection_string.txt
    if ! test -e "$ITA_DIRECTORY"/ita-root/confs/commonconfs/db_connection_string.txt ; then
        log "WARNING : Failed to place db_connection_string.txt."
    fi

    DB_USERNAME_ENC=`func_str_encode "$DB_USERNAME"`
    echo "$DB_USERNAME_ENC" > "$ITA_DIRECTORY"/ita-root/confs/commonconfs/db_username.txt
    if ! test -e "$ITA_DIRECTORY"/ita-root/confs/commonconfs/db_username.txt ; then
        log "WARNING : Failed to place db_username.txt."
    fi

    DB_PASSWORD_ENC=`func_str_encode "$DB_PASSWORD_ON_CMD"`
    echo "$DB_PASSWORD_ENC" > "$ITA_DIRECTORY"/ita-root/confs/commonconfs/db_password.txt
    if ! test -e "$ITA_DIRECTORY"/ita-root/confs/commonconfs/db_password.txt ; then
        log "WARNING : Failed to place db_password.txt."
    fi
    #PROCCESS_CNT=$((PROCCESS_CNT+1))

    #################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Create symbolic link of ITA environment file."
    #################################################################################################
    sed -i -e "s:${REPLACE_CHAR["ita_directory"]}:$ITA_DIRECTORY:g" "$ITA_DIRECTORY"/ita-root/confs/backyardconfs/ita_env 2>> "$LOG_FILE"
    ln -s "$ITA_DIRECTORY"/ita-root/confs/backyardconfs/ita_env /etc/sysconfig/ita_env 2>> "$LOG_FILE"
    if [ ! -L /etc/sysconfig/ita_env ]; then
        log 'WARNING : Failed to create symbolic link /etc/sysconfig/ita_env.'
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))
fi

#################################################################################################
#Check ITA version of installer and already installed ITA.
#################################################################################################
if test -e "$ITA_DIRECTORY"/ita-root/libs/release/ita_base ; then
    diff ../ITA/ita-releasefiles/ita_base "$ITA_DIRECTORY"/ita-root/libs/release/ita_base >> "$LOG_FILE" 2>&1
    if [ $? -ne 0 ]; then
        log "ERROR : The version of the installer and the already installed ITA are different."
        log "INFO : Abort installation."
        ERR_FLG="false"
        func_exit_and_delete_file
    fi
fi


###################################################
#データリレイストレージ作成
###################################################
for VAL in ${CREATE_DATARELAYSTORAGE[@]}; do
    func_create_datarelaystorage $VAL
done

###################################################
#テーブル作成
###################################################
if ! test -e "$BIN_DIR/create-tables-and-views.sh" ; then
    log "WARNING : create-tables-and-views.sh does not be found."
else
    for VAL in ${CREATE_TABLES[@]}; do
        func_create_tables $VAL
    done

fi

###################################################
#Ansible-driver用awxユーザ、sshキー作成
log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Create awx user and ssh key for Ansible driver."
###################################################
if [ "$ANSIBLE_FLG" -eq 1 ]; then

    #check awx user
    cat /etc/group | grep ^awx: >> "$LOG_FILE" 2>&1
    if [ $? -ne 0 ]; then
        #create awx user
        useradd awx >> "$LOG_FILE" 2>&1
    fi

    #create ssh key
    if test -e /home/awx/.ssh/rsa_awx_key ; then
        rm -f /home/awx/.ssh/rsa_awx_key*
    fi
    su - awx -c 'ssh-keygen -t rsa -b 4096 -C "" -f ~/.ssh/rsa_awx_key -N ""' >> "$LOG_FILE" 2>&1
    su - awx -c 'cat ~/.ssh/rsa_awx_key.pub >> ~/.ssh/authorized_keys' >> "$LOG_FILE" 2>&1
    chmod 600 /home/awx/.ssh/authorized_keys >> "$LOG_FILE" 2>&1
    cat /home/awx/.ssh/rsa_awx_key | base64 | tr '[A-Za-z]' '[N-ZA-Mn-za-m]' > "$ITA_DIRECTORY"/ita-root/uploadfiles/2100040702/ANS_GIT_SSH_KEY_FILE/0000000001/rsa_awx_key
    cat /home/awx/.ssh/rsa_awx_key | base64 | tr '[A-Za-z]' '[N-ZA-Mn-za-m]' > "$ITA_DIRECTORY"/ita-root/uploadfiles/2100040702/ANS_GIT_SSH_KEY_FILE/0000000001/old/0000000001/rsa_awx_key

    PROCCESS_CNT=$((PROCCESS_CNT+1))
fi

###################################################
#リリースファイル設置
###################################################
for VAL in ${RELEASE_PLASE[@]}; do
    func_release_place $VAL
done

###################################################
#コンフィグファイル設置確認
###################################################
for VAL in ${CONFIG_PLACE[@]}; do
    func_config_place $VAL
done

###################################################
#サービス登録
###################################################
if [ ! -e "$BIN_DIR/register-services_RHEL.sh" ]; then
    log 'WARNING : register-services_RHEL.sh does not be found.'
else
    for VAL in ${SERVICES_SET[@]}; do
        func_services_set $VAL
    done

fi
###################################################
#クーロンタブ設定
###################################################
if ! test -e "$BIN_DIR/register-crontab.sh" ; then
    log "WARNING : register-crontab.sh does not be found."
else
    for VAL in ${CRONTAB_SET[@]}; do
        func_crontab_set $VAL
    done
fi


if [ "$BASE_FLG" -eq 1 ]; then
    ################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Modify Apache(httpd) configuration file."
    ################################################################################################
    if [ ${LINUX_OS} = 'RHEL7' -o ${LINUX_OS} = 'CentOS7' ]; then
        RES=`cat /etc/sysconfig/httpd | grep "^LANG=\"*ja_JP.UTF-8\"*" -c`
        if [ "$RES" -eq 0 ]; then
            sed -i -e '/^LANG/s/^/# /g' '/etc/sysconfig/httpd' 2>> "$LOG_FILE"
            echo -e "LANG=\"ja_JP.UTF-8\"\n" >> /etc/sysconfig/httpd
        fi
    else
        cp -p /usr/lib/systemd/system/httpd.service /etc/systemd/system/ 2>> "$LOG_FILE"
        RES=`cat /etc/systemd/system/httpd.service | grep "^LANG=\"*ja_JP.UTF-8\"*" -c`
        if [ "$RES" -eq 0 ]; then
            sed -i -e 's/Environment=LANG=C/Environment=LANG=ja_JP.UTF-8/g' /etc/systemd/system/httpd.service 2>> "$LOG_FILE"
        fi
    fi
    PROCCESS_CNT=$((PROCCESS_CNT+1))

    ################################################################################################
    log "INFO : `printf %02d $PROCCESS_CNT`/$PROCCESS_TOTAL_CNT Restart Apache(httpd) service."
    ################################################################################################
    systemctl daemon-reload 2>&1 >> "$LOG_FILE"
    systemctl restart httpd 2>> "$LOG_FILE" | tee -a "$LOG_FILE"
    systemctl status httpd 2>&1 >> "$LOG_FILE"
    if [ $? -ne 0 ]; then
        log "WARNING : Failed to restart Apache(httpd) service."
    fi

    if [ ${LINUX_OS} = 'RHEL8' -o ${LINUX_OS} = 'CentOS8' ]; then
        systemctl restart php-fpm 2>> "$LOG_FILE" | tee -a "$LOG_FILE"
        systemctl status php-fpm 2>&1 >> "$LOG_FILE"
        if [ $? -ne 0 ]; then
            log "WARNING : Failed to restart php-fpm service."
        fi
    fi
fi

log 'INFO : Installation complete!'
