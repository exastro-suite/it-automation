#!/bin/bash
#   Copyright 2020 NEC Corporation
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
#    バージョンアップツール
#
############################################################

#-----関数定義ここから-----
############################################################
# ログ出力
# @param    $1    string    ログに出力する文字列
# @return   なし
############################################################
log() {
    echo "["`date +"%Y-%m-%d %H:%M:%S"`"] $1" | tee -a "$LOG_FILE"
}

############################################################
# 記入漏れをチェック
# @param    なし
# @return   なし
############################################################
func_answer_format_check() {
    #空白でないかチェック用
    if [ "$key" = "" -o "$val" = "" ]; then
        ANSWER_ERR_FLG=1
        log "ERROR : The format of Answer-file is incorrect.(key:$key)"
        log "INFO : Abort version up."
        ERR_FLG="false"
        func_exit_and_delete_file
    fi
    #$keyの値が正しいかチェック用
    FORMAT_CHECK_CNT=$((FORMAT_CHECK_CNT+1))
}

############################################################
# バージョンの大小チェック
# @param    バージョン1,バージョン2
# @return   0: バージョン1 < バージョン2
#           1: バージョン1 = バージョン2
#           2: バージョン1 > バージョン2
############################################################
func_compare_version() {
    VERSION1=$1
    VERSION2=$2
    VERSION1_SPLIT=(${VERSION1//./ })
    VERSION2_SPLIT=(${VERSION2//./ })

    if  [ ${VERSION1_SPLIT[0]} -lt ${VERSION2_SPLIT[0]} ] ; then
        echo 0
    elif  [ ${VERSION1_SPLIT[0]} -gt ${VERSION2_SPLIT[0]} ] ; then
        echo 2
    else
        if  [ ${VERSION1_SPLIT[1]} -lt ${VERSION2_SPLIT[1]} ] ; then
            echo 0
        elif  [ ${VERSION1_SPLIT[1]} -gt ${VERSION2_SPLIT[1]} ] ; then
            echo 2
        else
            if  [ ${VERSION1_SPLIT[2]} -lt ${VERSION2_SPLIT[2]} ] ; then
                echo 0
            elif  [ ${VERSION1_SPLIT[2]} -gt ${VERSION2_SPLIT[2]} ] ; then
                echo 2
            else
                echo 1
            fi
        fi
    fi
}

############################################################
# 記入漏れをチェック
# @param    なし
# @return   なし
############################################################
func_start_service() {
    #Apacheを起動する
    systemctl start httpd >> "$LOG_FILE" 2>&1
    systemctl start php-fpm >> "$LOG_FILE" 2>&1

    #ITAのサービス起動する
    cd /usr/lib/systemd/system
    systemctl start ky_*.service

    # ディレクトリ移動
    cd ${BASE_DIR}
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
# 全角文字をチェック
# @param    なし
# @return   なし
############################################################
setting_file_format_check() {
    if [ `echo "$LINE" | LANG=C grep -v '^[[:cntrl:][:print:]]*$'` ];then
        log "ERROR : Double-byte characters cannot be used in the setting files"
        log "Applicable line : $LINE"
        ERR_FLG="false"
        func_exit_and_delete_file
    fi
}

#-----関数定義ここまで-----

#インストール済みフラグ
BASE_FLG=0
ANSIBLE_FLG=0
COBBLER_FLG=0
TERRAFORM_FLG=0
CREATEPARAM_FLG=0
CREATEPARAM2_FLG=0
CREATEPARAM3_FLG=0
HOSTGROUP_FLG=0
CICD_FLG=0

#インストール済みフラグ配列
INSTALLED_FLG_LIST=(
    BASE_FLG
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
    ita_base
    ita_ansible-driver
    ita_cobbler-driver
    ita_terraform-driver
    ita_createparam
    ita_hostgroup
    ita_cicd
)

#ディレクトリ変数定義
BASE_DIR=$(cd $(dirname $0); pwd)
BIN_DIR="$BASE_DIR/bin"
LIST_DIR="$BASE_DIR/list"
SQL_DIR="$BASE_DIR/sql"
LOG_DIR="$BASE_DIR/log"
VERSION_UP_DIR="$BASE_DIR/version_up"
LOG_FILE="$LOG_DIR/ita_version_up.log"
ANSWER_FILE="$BASE_DIR/ita_answers.txt"
SOURCE_DIR="${BASE_DIR}/../ITA/ita-contents/ita-root"
CONFS_DIR="${BASE_DIR}/../ITA/ita-confs"

#log用ディレクトリ作成
if [ ! -e "$LOG_DIR" ]; then
    mkdir -m 755 "$LOG_DIR"
fi

############################################################
log 'INFO : -----MODE[VERSIONUP] START-----'
############################################################

############################################################
log 'INFO : Authorization check.'
############################################################
if [ ${EUID:-${UID}} -ne 0 ]; then
    log "ERROR : Execute with root authority."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi

############################################################
log 'INFO : Reading answer-file.'
############################################################

#answersファイルの存在を確認
if ! test -e "$ANSWER_FILE" ; then
    log "ERROR : Answer-file does not be found."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi

#answersファイルの内容を格納する変数を定義
COPY_ANSWER_FILE="/tmp/ita_answers.txt"
ITA_DIRECTORY=''
ITA_LANGUAGE=''
DB_ROOT_PASSWORD=''
DB_NAME=''
DB_USERNAME=''
DB_PASSWORD=''
#answersファイルのフォーマットチェック用変数リセット
FORMAT_CHECK_CNT='' 

# ita_answers.txtを/tmpにコピー
rm -f "$COPY_ANSWER_FILE" 2>> "$LOG_FILE"
cp "$ANSWER_FILE" "$COPY_ANSWER_FILE" 2>> "$LOG_FILE"
# 空行を削除
sed -i -e '/^$/d' "$COPY_ANSWER_FILE" 2>> "$LOG_FILE"
# BOMを削除
sed -i -e '1s/^\xef\xbb\xbf//' "$COPY_ANSWER_FILE" 2>> "$LOG_FILE"
# 末尾に改行を追加
echo "$(cat "$COPY_ANSWER_FILE")" 1> "$COPY_ANSWER_FILE" 2>> "$LOG_FILE"

#answersファイル読み込み
ANSWERS_TEXT=$(cat "$COPY_ANSWER_FILE")
#IFSバックアップ
SRC_IFS="$IFS"
#IFSに"\n"をセット
IFS="
"
for LINE in $ANSWERS_TEXT;do
    if [ "$(echo "$LINE"|grep -E '^[^#: ]+:[ ]*[^ ]+[ ]*$')" != "" ];then
        setting_file_format_check
        key="$(echo "$LINE" | sed 's/[[:space:]]*$//' | sed -E "s/^([^:]+):[[:space:]]*(.+)$/\1/")"
        val="$(echo "$LINE" | sed 's/[[:space:]]*$//' | sed -E "s/^([^:]+):[[:space:]]*(.+)$/\2/")"

        #ITA用のディレクトリ取得
        if [ "$key" = 'ita_directory' ]; then
            if [[ "$val" != "/"* ]]; then
                log "ERROR : Enter the absolute path in $key."
                log "INFO : Abort version up."
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
            func_answer_format_check
            ITA_DIRECTORY="$val"
        fi
    fi
done

#IFSリストア
IFS="$SRC_IFS"

#作業用アンサーファイルの削除
if ! test -e /tmp/ita_answers.txt ; then
    rm -rf /tmp/ita_answers.txt
fi

#アンサーファイルの内容が読み取れているか
if [ "$FORMAT_CHECK_CNT" != 1 ]; then
    log "ERROR : The format of Answer-file is incorrect."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi

#現在のITAバージョンを取得
NOW_VERSION_FILE="${ITA_DIRECTORY}/ita-root/libs/release/ita_base"
if ! test -e ${NOW_VERSION_FILE} ; then
    log "ERROR : ITA is not installed in [${ITA_DIRECTORY}]."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi
NOW_VERSION=`cat ${NOW_VERSION_FILE} | cut -d " " -f 7 | sed -e "s/[\r\n]\+//g"`

#言語の取得
LANGUAGE_FILE="${ITA_DIRECTORY}/ita-root/confs/commonconfs/app_msg_language.txt"
if ! test -e ${LANGUAGE_FILE} ; then
    log "ERROR : [${LANGUAGE_FILE}] does not be found."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi
ITA_LANGUAGE=`cat ${LANGUAGE_FILE} | sed -e "s/[\r\n]\+//g"`

if [ "${ITA_LANGUAGE}" != 'ja_JP' -a "${ITA_LANGUAGE}" != 'en_US' ]; then
    log "ERROR : [${LANGUAGE_FILE}] is incorrect."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi

#DB接続情報の取得
DB_CONNECT_FILE="${ITA_DIRECTORY}/ita-root/confs/commonconfs/db_connection_string.txt"
if ! test -e ${DB_CONNECT_FILE} ; then
    log "ERROR : [${DB_CONNECT_FILE}] does not be found."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi
DB_CONNECT=`cat ${DB_CONNECT_FILE} | tr '[A-Za-z]' '[N-ZA-Mn-za-m]' | base64 -d`
DB_CONNECT_SPLIT=(${DB_CONNECT//;/ })
DB_NAME_SPLIT=(${DB_CONNECT_SPLIT[0]//=/ })
DB_NAME=${DB_NAME_SPLIT[1]}
DB_HOST_SPLIT=(${DB_CONNECT_SPLIT[1]//=/ })
DB_HOST=${DB_HOST_SPLIT[1]}

#DBユーザの取得
DB_USER_FILE="${ITA_DIRECTORY}/ita-root/confs/commonconfs/db_username.txt"
if ! test -e ${DB_USER_FILE} ; then
    log "ERROR : [${DB_USER_FILE}] does not be found."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi
DB_USERNAME=`cat ${DB_USER_FILE} | tr '[A-Za-z]' '[N-ZA-Mn-za-m]' | base64 -d`

#DBパスワードの取得
DB_PASSWORD_FILE="${ITA_DIRECTORY}/ita-root/confs/commonconfs/db_password.txt"
if ! test -e ${DB_PASSWORD_FILE} ; then
    log "ERROR : [${DB_PASSWORD_FILE}] does not be found."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi
DB_PASSWORD=`cat ${DB_PASSWORD_FILE} | tr '[A-Za-z]' '[N-ZA-Mn-za-m]' | base64 -d`

############################################################
log 'INFO : Version check.'
############################################################
#現在のITAバージョンの形式チェック
if ! [[ ${NOW_VERSION} =~ [0-9]+\.[0-9]+\.[0-9]+ ]] ; then
    log "ERROR : [${NOW_VERSION_FILE}] is incorrect."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi

#現在のITAバージョンが1.4.0以降であることをチェック
RTN=`func_compare_version ${NOW_VERSION} 1.4.0`
if [ "${RTN}" -eq 0 ] ; then
    log "ERROR : Version up is support with 1.4.0 or later.The installed ITA version is [${NOW_VERSION}]."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi

#インストーラのITAバージョンを取得
INSTALLER_VERSION_FILE="${BASE_DIR}/../ITA/ita-releasefiles/ita_base"
if ! test -e ${INSTALLER_VERSION_FILE} ; then
    log "ERROR : [${INSTALLER_VERSION_FILE}] does not be found."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi
INSTALLER_VERSION=`cat ${INSTALLER_VERSION_FILE} | cut -d " " -f 7 | sed -e "s/[\r\n]\+//g"`

#インストーラのITAバージョンの形式チェック
if ! [[ ${INSTALLER_VERSION} =~ [0-9]+\.[0-9]+\.[0-9]+ ]] ; then
    log "ERROR : [${INSTALLER_VERSION_FILE}] is incorrect."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi

#現在のITAバージョンがインストーラのITAバージョンよりも低いことのチェック
RTN=`func_compare_version ${NOW_VERSION} ${INSTALLER_VERSION}`
if [ "${RTN}" -eq 1 ] || [ "${RTN}" -eq 2 ] ; then
    log "ERROR : The installed ITA has been version up."
    log "INFO : Abort version up."
    ERR_FLG="false"
    func_exit_and_delete_file
fi

#インストールされているドライバの確認
BASE_FLG=1
if test -e "${ITA_DIRECTORY}/ita-root/libs/release/ita_ansible-driver" ; then
    ANSIBLE_FLG=1
fi
if test -e "${ITA_DIRECTORY}/ita-root/libs/release/ita_cobbler-driver" ; then
    COBBLER_FLG=1
fi
if test -e "${ITA_DIRECTORY}/ita-root/libs/release/ita_terraform-driver" ; then
    TERRAFORM_FLG=1
fi
if test -e "${ITA_DIRECTORY}/ita-root/libs/release/ita_createparam" ; then
    CREATEPARAM_FLG=1
fi
if [ -e "${ITA_DIRECTORY}/ita-root/libs/release/ita_createparam" ] && [ -e "${ITA_DIRECTORY}/ita-root/libs/release/ita_ansible-driver" ] ; then
    CREATEPARAM2_FLG=1
fi
if [ -e "${ITA_DIRECTORY}/ita-root/libs/release/ita_createparam" ] &&  [ -e "${ITA_DIRECTORY}/ita-root/libs/release/ita_hostgroup" ] ; then
    CREATEPARAM3_FLG=1
fi
if test -e "${ITA_DIRECTORY}/ita-root/libs/release/ita_hostgroup" ; then
    HOSTGROUP_FLG=1
fi
if test -e "${ITA_DIRECTORY}/ita-root/libs/release/ita_cicd" ; then
    CICD_FLG=1
fi


############################################################
log 'INFO : Stopping Apache.'
############################################################
#Apacheを停止する
systemctl stop httpd >> "$LOG_FILE" 2>&1
systemctl stop php-fpm >> "$LOG_FILE" 2>&1


############################################################
log 'INFO : Stopping ITA services.'
############################################################
#ITAのサービスを停止する
cd /usr/lib/systemd/system
systemctl stop ky_*.service

# ディレクトリ移動
cd ${BASE_DIR}


#バージョンアップリストの確認
VERSION_UP_LIST_FILE="${VERSION_UP_DIR}/version_up.list"
if ! test -e ${VERSION_UP_LIST_FILE} ; then
    log "ERROR : [${VERSION_UP_LIST_FILE}] does not be found."
    log "INFO : Abort version up."
    func_start_service
    ERR_FLG="false"
    func_exit_and_delete_file
fi

#ライブラリのインストール（INSTALL_MODE = Versionup_All の時のみ）
if [ "${INSTALL_MODE}" = "Versionup_All" ] ; then
    #RHEL8用アーキテクチャ判定用変数定義
    ARCH=$(arch)
    #リポジトリを有効にする
    yum install -y yum-utils dnf-utils >> "$LOG_FILE" 2>&1
    yum-config-manager --enable rhel-7-server-optional-rpms >> "$LOG_FILE" 2>&1
    yum-config-manager --enable rhui-rhel-7-server-rhui-optional-rpms >> "$LOG_FILE" 2>&1
    yum-config-manager --enable rhui-REGION-rhel-server-optional >> "$LOG_FILE" 2>&1
    yum-config-manager --enable rhel-7-server-rhui-optional-rpms >> "$LOG_FILE" 2>&1
    dnf config-manager --set-enabled PowerTools >> "$LOG_FILE" 2>&1
    dnf config-manager --set-enabled powertools >> "$LOG_FILE" 2>&1
    dnf config-manager --set-enabled codeready-builder-for-rhel-8-${ARCH}-rpms >> "$LOG_FILE" 2>&1
    dnf config-manager --set-enabled codeready-builder-for-rhel-8-rhui-rpms >> "$LOG_FILE" 2>&1
    pip3 install --upgrade pip >> "$LOG_FILE" 2>&1

    #バージョンアップリストに記載されているバージョンごとにライブラリのインストールを行う
    EXEC_VERSION=${NOW_VERSION}
    while read LIST_VERSION || [ -n "${LIST_VERSION}" ] ; do

        #処理対象のITAバージョンがインストーラのITAバージョンよりも低いことのチェック
        RTN=`func_compare_version ${EXEC_VERSION} ${LIST_VERSION}`
        if [ "${RTN}" -eq 1 ] || [ "${RTN}" -eq 2 ] ; then
            continue
        fi

        #インストール済みのドライバごとの処理を行う
        for VAL in ${INSTALLED_FLG_LIST[@]}; do
            if [ ${!VAL} -eq 1 ] ; then
                DRIVER=`echo ${VAL} | cut -d "_" -f 1 | sed 's/^ \(.*\) $/\1/'`

                #YAMLライブラリのインストール
                YUM_LIB_FILE="${VERSION_UP_DIR}/${LIST_VERSION}/${DRIVER,,}_lib_yum.txt"
                if test -e ${YUM_LIB_FILE} ; then
                    YUM_LIB_LIST=`cat ${YUM_LIB_FILE}`

                    ############################################################
                    log "INFO : Installation yum library [${YUM_LIB_LIST}]"
                    ############################################################
                    #Check installation
                    for key in $YUM_LIB_LIST; do
                        echo "----------Installation[$key]----------" >> "$LOG_FILE" 2>&1
                        yum install -y "$key" >> "$LOG_FILE" 2>&1
                        if [ $? != 0 ]; then
                            log "ERROR : Installation failed [$key]"
                            log "INFO : Abort version up."
                            func_start_service
                            ERR_FLG="false"
                            func_exit_and_delete_file
                        fi
                    done
                fi

                #PECLライブラリのインストール
                PECL_LIB_FILE="${VERSION_UP_DIR}/${LIST_VERSION}/${DRIVER,,}_lib_pecl.txt"
                if test -e ${PECL_LIB_FILE} ; then
                    PECL_LIB_LIST=`cat ${PECL_LIB_FILE}`

                    ############################################################
                    log "INFO : Installation pecl library [${PECL_LIB_LIST}]"
                    ############################################################
                    echo "" | pecl install ${PECL_LIB_LIST} >> "$LOG_FILE" 2>&1

                    #Check installation
                    for key in $PECL_LIB_LIST; do
                        echo "----------Installation[$key]----------" >> "$LOG_FILE" 2>&1
                        pecl list | grep "$key" >> "$LOG_FILE" 2>&1
                        if [ $? != 0 ]; then
                            log "ERROR : Installation failed [$key]"
                            log "INFO : Abort version up."
                            func_start_service
                            ERR_FLG="false"
                            func_exit_and_delete_file
                        fi
                    done
                fi

                #pip3ライブラリのインストール
                PIP3_LIB_FILE="${VERSION_UP_DIR}/${LIST_VERSION}/${DRIVER,,}_lib_pip3.txt"
                if test -e ${PIP3_LIB_FILE} ; then
                    PIP3_LIB_LIST=`cat ${PIP3_LIB_FILE}`

                    ############################################################
                    log "INFO : Installation pip3 library [${PIP3_LIB_LIST}]"
                    ############################################################
                    pip3 install ${PIP3_LIB_LIST} >> "$LOG_FILE" 2>&1

                    #Check installation
                    for key in $PIP3_LIB_LIST; do
                        echo "----------Installation[$key]----------" >> "$LOG_FILE" 2>&1
                        pip3 list --format=columns 2>> "$LOG_FILE" | grep "$key" >> "$LOG_FILE" 2>&1
                        if [ $? != 0 ]; then
                            log "ERROR : Installation failed [$key]"
                            log "INFO : Abort version up."
                            func_start_service
                            ERR_FLG="false"
                            func_exit_and_delete_file
                        fi
                    done
                fi
            fi
        done
        EXEC_VERSION=${LIST_VERSION}
    done < ${VERSION_UP_LIST_FILE}
fi

#バージョンアップリストに記載されているバージョンごとにテーブルの更新を行う
############################################################
log "INFO : Updating tables."
############################################################
EXEC_VERSION=${NOW_VERSION}
while read LIST_VERSION || [ -n "${LIST_VERSION}" ] ; do

    #処理対象のITAバージョンがインストーラのITAバージョンよりも低いことのチェック
    RTN=`func_compare_version ${EXEC_VERSION} ${LIST_VERSION}`
    if [ "${RTN}" -eq 1 ] || [ "${RTN}" -eq 2 ] ; then
        continue
    fi

    #インストール済みのドライバごとの処理を行う
    for VAL in ${INSTALLED_FLG_LIST[@]}; do
        if [ ${!VAL} -eq 1 ] ; then
            DRIVER=`echo ${VAL} | cut -d "_" -f 1 | sed 's/^ \(.*\) $/\1/'`

            SQL_FILE="${VERSION_UP_DIR}/${LIST_VERSION}/${DRIVER,,}_sql_${ITA_LANGUAGE}.sql"
            if test -e ${SQL_FILE} ; then

                SQL_REPLACE="${VERSION_UP_DIR}/${LIST_VERSION}/replace_${DRIVER,,}_sql_${ITA_LANGUAGE}.sql"
                SQL_LOGFILE="${LOG_DIR}/${LIST_VERSION}_${DRIVER,,}_sql.log"

                #ディレクトリを置換
                cp "$SQL_FILE" "$SQL_REPLACE"
                sed -i -e "s:%%%%%ITA_DIRECTORY%%%%%:${ITA_DIRECTORY}:g" ${SQL_REPLACE}

                #SQLの実行
                env MYSQL_PWD=${DB_PASSWORD} mysql -u${DB_USERNAME} ${DB_NAME} -h ${DB_HOST} < "$SQL_REPLACE" 1>${SQL_LOGFILE} 2>&1

                rm -rf ${SQL_REPLACE}

                #ログファイルを確認
                if ! test -e "$SQL_LOGFILE" ; then
                    log "ERROR : [$SQL_LOGFILE] does not be found."
                    log "INFO : Abort version up."
                    func_start_service
                    ERR_FLG="false"
                    func_exit_and_delete_file
                else
                    FILE_SIZE=`wc -c < "$SQL_LOGFILE"`
                    if [ "$FILE_SIZE" -ne 0 ]; then
                        log "ERROR : SQL Error. Check logfile[$SQL_LOGFILE]."
                        log "INFO : Abort version up."
                        func_start_service
                        ERR_FLG="false"
                        func_exit_and_delete_file
                    fi
                fi
            fi
        fi
    done
    EXEC_VERSION=${LIST_VERSION}
done < ${VERSION_UP_LIST_FILE}

############################################################
log "INFO : Updating sources."
############################################################
#ITAの資材を入れ替える
cp -rp ${SOURCE_DIR} ${ITA_DIRECTORY}
cp -rpn ${CONFS_DIR}/* ${ITA_DIRECTORY}/ita-root/confs/

EXEC_VERSION=${NOW_VERSION}
while read LIST_VERSION || [ -n "${LIST_VERSION}" ] ; do

    #処理対象のITAバージョンがインストーラのITAバージョンよりも低いことのチェック
    RTN=`func_compare_version ${EXEC_VERSION} ${LIST_VERSION}`
    if [ "${RTN}" -eq 1 ] || [ "${RTN}" -eq 2 ] ; then
        continue
    fi

    #ディレクトリを作成する
    CREATE_DIR_FILE="${VERSION_UP_DIR}/${LIST_VERSION}/create_dir.txt"
    if test -e ${CREATE_DIR_FILE} ; then
        while read LINE; do
            if ! test -d "${ITA_DIRECTORY}/${LINE}" ; then
                mkdir -p "${ITA_DIRECTORY}/${LINE}" 2>> "$LOG_FILE"
            fi
        done < ${CREATE_DIR_FILE}
    fi

    #インストール済みのドライバごとの処理を行う
    for VAL in ${INSTALLED_FLG_LIST[@]}; do
        if [ ${!VAL} -eq 1 ] ; then
            DRIVER=`echo ${VAL} | cut -d "_" -f 1 | sed 's/^ \(.*\) $/\1/'`

            #サービスを追加する
            SERVICE_FILE="${VERSION_UP_DIR}/${LIST_VERSION}/${DRIVER,,}_service_list.txt"
            if test -e ${SERVICE_FILE} ; then
                while read LINE; do
                    cp -p ${ITA_DIRECTORY}/${LINE}.service /usr/lib/systemd/system/.
                    systemctl enable `basename ${LINE}`.service  2>> "$LOG_FILE"
                done < ${SERVICE_FILE}
            fi
        fi
    done

    #その他必要なスクリプトを実行する
    SHELL_FILE="${VERSION_UP_DIR}/${LIST_VERSION}/other_exec.sh"
    if test -e ${SHELL_FILE} ; then
        sh ${SHELL_FILE} ${ITA_DIRECTORY} ${NOW_VERSION} >> "$LOG_FILE" 2>&1
    fi

done < ${VERSION_UP_LIST_FILE}

#ディレクトリ、ファイルの権限を777に変更する
MOD_777_FILE="${LIST_DIR}/777_list.txt"
if test -e ${MOD_777_FILE} ; then
    while read LINE; do
        chmod -- 777 "${ITA_DIRECTORY}/${LINE}"
    done < ${MOD_777_FILE}
fi

#ディレクトリ、ファイルの権限を755に変更する
MOD_755_FILE="${LIST_DIR}/755_list.txt"
if test -e ${MOD_755_FILE} ; then
    while read LINE; do
        chmod -- 755 "${ITA_DIRECTORY}/${LINE}"
    done < ${MOD_755_FILE}
fi

#リリースファイルを変更する
for VAL in ${RELEASE_PLASE[@]}; do
    DRIVER=`echo ${VAL} | cut -d "_" -f 2 | sed 's/^ \(.*\) $/\1/'`
    FLG=`echo ${DRIVER^^} | cut -d "-" -f 1 | sed 's/^ \(.*\) $/\1/'`_FLG
    if [ ${!FLG} -eq 1 ] ; then
        cp -p "${BASE_DIR}/../ITA/ita-releasefiles/${VAL}" "${ITA_DIRECTORY}/ita-root/libs/release/${VAL}"
    fi
done

############################################################
log "INFO : Start Apache and ITA services."
############################################################
#サービスを起動する
systemctl daemon-reload
func_start_service

############################################################
log "INFO : Version up completed from [${NOW_VERSION}] to [${INSTALLER_VERSION}]."
############################################################


