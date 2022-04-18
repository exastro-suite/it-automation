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
#############################################################
#
# 【概要】
#    ・ITA環境構築に必要なライブラリを収集
#    ・ITA環境を構築
#    ・ITAインストーラーを実行
#
#
#############################################################

################################################################################
# generic functions (should have no dependencies on global variables)

log() {
    echo "["`date +"%Y-%m-%d %H:%M:%S"`"] $1" | tee -a "$ITA_BUILDER_LOG_FILE"
}


backup_suffix() {
    echo "."`date +%Y%m%d-%H%M%S.bak`
}


list_pear_package() {
    local dst_dir=$1

    if [ -d $dst_dir ]; then
        find $dst_dir -type f | grep -E '\.(tgz|tar.gz)$' | tr "\n" " "
    fi
}


list_yum_package() {
    local dst_dir=$1

    if [ -d $dst_dir ]; then
        find $dst_dir -type f | grep -E '\.rpm$' | tr "\n" " "
    fi
}


list_pecl_package() {
    local dst_dir=$1

    if [ -d $dst_dir ]; then
        find $dst_dir -type f | grep -E '\.tgz$' | tr "\n" " "
    fi
}


list_pip_package() {
    local dst_dir=$1

    if [ -d $dst_dir ]; then
        find $dst_dir -type f | grep -E '\.(whl|tar.gz)$' | tr "\n" " "
    fi
}


copy_and_backup() {
    local src=$1
    local dst=$2
    local dstfile=$dst/`basename "$src"`
    if [ "${dst: -1}" == "/" ]; then
        local dst_dir=$dst
    else
        local dst_dir=`dirname "$dst"`
    fi

    if [ ! -e "$dst_dir" ]; then
        mkdir -p "$dst_dir" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    fi
    
    diff "$src" "$dstfile" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    if [ $? != 0 ]; then
        \cp -p -b --suffix=`backup_suffix` "$src" "$dst" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    fi
}


yum_install() {
    if [ "${MODE}" == "remote" -o "$LINUX_OS" == "RHEL7" -o "$LINUX_OS" == "CentOS7" ]; then
        if [ $# -gt 0 ]; then
            echo "----------Installation[$@]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            #Installation
            yum install -y "$@" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        
            #Check installation
            for key in $@; do
                echo "----------Check installation[$key]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
                yum install -y "$key" >> "$ITA_BUILDER_LOG_FILE" 2>&1
                if [ $? != 0 ]; then
                    log "ERROR:Installation failed[$key]"
                    ERR_FLG="false"
                    func_exit_and_delete_file
                fi
            done
        fi
    fi
}


yum_package_check() {
    if [ $# -gt 0 ];then
        for key in $@; do
            echo "----------Check Installed packages[$key]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            yum list installed | grep -i "$key" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            if [ $? -ne 0 ]; then
                log "ERROR:Package not installed [$key]"
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
        done
    fi
}


create_repo_check(){
    if [ $# -gt 0 ];then
        for key in $@; do
            echo "----------Check Creation repository[$key]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            yum repolist | grep -i "$key" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            if [ $? -eq 0 ]; then
                echo "Successful repository acquisition" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            else
                return 1
            fi
        done
    fi
}


download_check() {
    DOWNLOAD_CHK=`echo $?`
    if [ $DOWNLOAD_CHK -ne 0 ]; then
        log "ERROR:Download of file failed"
        ERR_FLG="false"
        func_exit_and_delete_file
    fi
}

error_check() {
    DOWNLOAD_CHK=`echo $?`
    if [ $DOWNLOAD_CHK -ne 0 ]; then
        log "ERROR:Stop installation"
        ERR_FLG="false"
        func_exit_and_delete_file
    fi
}

# enable yum repository
# ex.
#   yum_repository http://example.com/example-repo.rpm --enable test-repo
yum_repository() {
    if [ $# -gt 0 ]; then
        local repo=$1
        
        # no repo to be installed if the first argument starts "-".
        if [[ "$repo" =~ ^[^-] ]]; then
            if [ "$LINUX_OS" == "RHEL7" ]; then
                rpm -ivh "$repo" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            else
                yum install -y "$repo" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            fi

            # Check Creating repository
            if [[ "$repo" =~ .*epel-release.* ]]; then
                yum-config-manager --enable epel >> "$ITA_BUILDER_LOG_FILE" 2>&1
                create_repo_check epel >> "$ITA_BUILDER_LOG_FILE" 2>&1
            elif [[ "$repo" =~ .*remi-release-7.* ]]; then
                create_repo_check remi-safe >> "$ITA_BUILDER_LOG_FILE" 2>&1
            fi
            if [ $? -ne 0 ]; then
                log "ERROR:Failed to get repository"
                ERR_FLG="false"
                func_exit_and_delete_file
            fi

            shift
        fi

        if [ $# -gt 0 ]; then
            if [ "${LINUX_OS}" == "CentOS7" -o "${LINUX_OS}" == "RHEL7" ]; then
                yum-config-manager "$@" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            elif [ "${LINUX_OS}" == "RHEL8" ]; then
                dnf config-manager "$@" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            elif [ "${LINUX_OS}" == "CentOS8" ]; then
                yum repolist all > repolist_all.tmp 2>&1
                POWERTOOLS_NAME=`grep -i powertools repolist_all.tmp | grep -iv powertools- | cut -f 1  --delim=" "`
                dnf config-manager --set-enabled "${POWERTOOLS_NAME}" >> "$ITA_BUILDER_LOG_FILE" 2>&1
                rm -rf repolist_all.tmp
            fi

            # Check Creating repository
            if [ "${REPOSITORY}" != "yum_all" ]; then
               case "${LINUX_OS}" in
                    "CentOS7") create_repo_check remi-php74 >> "$ITA_BUILDER_LOG_FILE" 2>&1 ;;
                    "RHEL7") 
                        if [ "${CLOUD_REPO}" == "RHEL7_RHUI2" ]; then
                            create_repo_check remi-php74 rhui-rhel-7-server-rhui-optional-rpms >> "$ITA_BUILDER_LOG_FILE" 2>&1
                        elif [ "${CLOUD_REPO}" == "RHEL7_RHUI2_AWS" ]; then
                            create_repo_check remi-php74 rhui-REGION-rhel-server-optional >> "$ITA_BUILDER_LOG_FILE" 2>&1
                        elif [ "${CLOUD_REPO}" == "RHEL7_RHUI3" ]; then
                            create_repo_check remi-php74 rhel-7-server-rhui-optional-rpms >> "$ITA_BUILDER_LOG_FILE" 2>&1
                        else
                            create_repo_check remi-php74 rhel-7-server-optional-rpms >> "$ITA_BUILDER_LOG_FILE" 2>&1
                        fi
                    ;;
                    "CentOS8") create_repo_check powertools >> "$ITA_BUILDER_LOG_FILE" 2>&1 ;;
                    "RHEL8")
                        if [ "${CLOUD_REPO}" == "RHEL8_RHUI" ]; then
                            create_repo_check codeready-builder-for-rhel-8-rhui-rpms >> "$ITA_BUILDER_LOG_FILE" 2>&1
                        else
                            create_repo_check codeready-builder-for-rhel-8 >> "$ITA_BUILDER_LOG_FILE" 2>&1
                        fi
                    ;;
                esac 
                if [ $? -ne 0 ]; then
                   log "ERROR:Failed to get repository"
                   ERR_FLG="false"
                   func_exit_and_delete_file
                fi
            fi
            yum clean all >> "$ITA_BUILDER_LOG_FILE" 2>&1
        fi
    fi
}


# enable mariadb repository
mariadb_repository() {
    #Not used for offline installation
    if [ "${REPOSITORY}" != "yum_all" ]; then
        if [ "${distro_mariadb}" == "no" ]; then
            local repo=$1

            curl -sS "$repo" | bash >> "$ITA_BUILDER_LOG_FILE" 2>&1

            # Check Creating repository
            create_repo_check mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
            if [ $? -ne 0 ]; then
                log "ERROR:Failed to get repository"
                ERR_FLG="false"
                func_exit_and_delete_file
            fi

            yum clean all >> "$ITA_BUILDER_LOG_FILE" 2>&1
        fi
    fi
}


cat_tar_gz() {
    local location=$1

    if [[ "$location" =~ ^(http|https|ftp):// ]]; then
        curl -L "$location" -sS
    else
        cat "$location"
    fi
}

setting_file_format_check(){
    if [ `echo "$line" | LANG=C grep -v '^[[:cntrl:][:print:]]*$'` ];then
        log "ERROR : Double-byte characters cannot be used in the setting files"
        log "Applicable line : $line"
        ERR_FLG="false"
        func_exit_and_delete_file
    fi
}

cloud_repo_setting(){
    yum repolist all &> /tmp/ita_repolist.txt 
    if [ -e /tmp/ita_repolist.txt ]; then
        if [ "${LINUX_OS}" == "RHEL8" ]; then
            if grep -q codeready-builder-for-rhel-8-rhui-rpms /tmp/ita_repolist.txt ; then
                CLOUD_REPO="RHEL8_RHUI"
            elif grep -q codeready-builder-for-rhel-8-"${ARCH}"-rpms /tmp/ita_repolist.txt ; then
                CLOUD_REPO="physical"
            else
                log "ERROR : The repository required to install ITA cannot be found.
codeready-builder-for-rhel-8-${ARCH}-rpms
codeready-builder-for-rhel-8-rhui-rpms"
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
        elif [ "${LINUX_OS}" == "RHEL7" ]; then
            if grep -q rhui-rhel-7-server-rhui-optional-rpms /tmp/ita_repolist.txt ; then
                CLOUD_REPO="RHEL7_RHUI2"
            elif grep -q rhui-REGION-rhel-server-optional /tmp/ita_repolist.txt ; then
                CLOUD_REPO="RHEL7_RHUI2_AWS"
            elif grep -q rhel-7-server-rhui-optional-rpms /tmp/ita_repolist.txt ; then
                CLOUD_REPO="RHEL7_RHUI3"
            elif grep -q rhel-7-server-optional-rpms /tmp/ita_repolist.txt ; then
                CLOUD_REPO="physical"
            else 
                log "ERROR : The repository required to install ITA cannot be found.
rhui-rhel-7-server-rhui-optional-rpms
rhui-REGION-rhel-server-optional
rhel-7-server-rhui-optional-rpms
rhel-7-server-optional-rpms"
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
        fi
    else
        log 'ERROR:Failed to create /tmp/ita_repolist.txt.'
        ERR_FLG="false"
        func_exit_and_delete_file
    fi
}

################################################################################
# configuration functions

# read setting file
read_setting_file() {
    local setting_file=$1
    local setting_text=$(cat $setting_file)
    #IFSバックアップ
    SRC_IFS="$IFS"
    IFS="
"
    for line in $setting_text;do
        # convert "foo: bar" to "foo=bar", and keep comment 
        if [ "$(echo "$line"|grep -E '^[^#: ]+:[ ]*[^ ]+[ ]*$')" != "" ];then
            setting_file_format_check
            key="$(echo "$line" | sed 's/[[:space:]]*$//' | sed -E "s/^([^:]+):[[:space:]]*(.+)$/\1/")"
            val="$(echo "$line" | sed 's/[[:space:]]*$//' | sed -E "s/^([^:]+):[[:space:]]*(.+)$/\2/")"
            val=$(echo "$val"|sed -E "s/'/'\\\"'\\\"'/g")
            command="$key='$val'"
            eval "$command"
        fi
    done

    #IFSリストア
    IFS="$SRC_IFS"

    
}


# create local yum repository
configure_yum_env() {

    #mirror list is Japan only.
    if [ ! -e /etc/yum/pluginconf.d/fastestmirror.conf ]; then
        if [ "$LINUX_OS" == "RHEL7" ]; then
            yum --enablerepo=rhel-7-server-optional-rpms info yum-plugin-fastestmirror >> "$ITA_BUILDER_LOG_FILE" 2>&1
            ls /etc/yum/pluginconf.d/fastestmirror.conf >> "$ITA_BUILDER_LOG_FILE" 2>&1 | xargs grep "include_only=.jp" >> "$ITA_BUILDER_LOG_FILE" 2>&1

            if [ $? -ne 0 ]; then
                sed -i '$a\include_only=.jp' /etc/yum/pluginconf.d/fastestmirror.conf >> "$ITA_BUILDER_LOG_FILE" 2>&1
            fi
        fi
    fi

    # install yum-utils and createrepo
    if [ "${LINUX_OS}" == "CentOS7" -o "${LINUX_OS}" == "RHEL7" ]; then
        log "yum-utils and createrepo install"
        if [ "${MODE}" == "remote" ]; then
            yum_install ${YUM__ENV_PACKAGE}
        else
            # initialize /var/lib/ita
            if [ ! -e $LOCAL_BASE_DIR ]; then
                mkdir -p $LOCAL_BASE_DIR >> "$ITA_BUILDER_LOG_FILE" 2>&1
            fi
            cp -R "$DOWNLOAD_BASE_DIR"/* "$ITA_EXT_FILE_DIR" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            cp -R $ITA_EXT_FILE_DIR/yum/ $LOCAL_BASE_DIR >> "$ITA_BUILDER_LOG_FILE" 2>&1

            yum localinstall -y --nogpgcheck ${YUM__ENV_PACKAGE} >> "$ITA_BUILDER_LOG_FILE" 2>&1

            ls /etc/yum.repos.d/ita.repo 2>&1 | tee -a "$ITA_BUILDER_LOG_FILE" 2>&1 | xargs grep -s "yum_all" >> "$ITA_BUILDER_LOG_FILE" 2>&1

            if [ $? != 0 ]; then
                echo "["yum_all"]
name="yum_all"
baseurl=file://"${YUM_ALL_PACKAGE_LOCAL_DIR}"
gpgcheck=0
enabled=0
" > /etc/yum.repos.d/ita.repo

                #create repository "ita_repo"
                createrepo "${YUM_ALL_PACKAGE_LOCAL_DIR}" >> "$ITA_BUILDER_LOG_FILE" 2>&1
                CREATEREPO_CHK=`echo $?`
                if [ "${CREATEREPO_CHK}" -ne 0 ]; then
                    log "ERROR:Repository creation failure"
                    ERR_FLG="false"
                    func_exit_and_delete_file
                fi
            else
                log "Already exist[/etc/yum.repos.d/ita.repo]"
                log "nothing to do"
            fi

            # disable yum repository
            sed -i s/"enabled.*$"/"enabled=0"/g /etc/yum.repos.d/* >> "$ITA_BUILDER_LOG_FILE" 2>&1

            yum_repository ${YUM_REPO_PACKAGE["yum-env-enable-repo"]}
            yum_repository ${YUM_REPO_PACKAGE["yum-env-disable-repo"]}
        fi
        yum_package_check yum-utils createrepo
    fi

    if [ "${MODE}" == "remote" ]; then
        yum_repository ${YUM_REPO_PACKAGE["yum-env-enable-repo"]}
        yum_repository ${YUM_REPO_PACKAGE["yum-env-disable-repo"]}
    fi
    yum clean all >> "$ITA_BUILDER_LOG_FILE" 2>&1
}


# RPM install
install_rpm() {
    RPM_INSTALL_CMD="rpm -Uvh --replacepkgs --nodeps"
    LOOP_CNT=0

    #get name of RPM
    for pathfile in ${YUM_ALL_PACKAGE_DOWNLOAD_DIR}/*.rpm; do
        RPM_INSTALL_CMD="${RPM_INSTALL_CMD} ${pathfile}"
        LOOP_CNT=$(( LOOP_CNT+1 ))
    done

    #RPM install
    if [ ${LOOP_CNT} -gt 0 ]; then
        ${RPM_INSTALL_CMD} >> "$ITA_BUILDER_LOG_FILE" 2>&1
        error_check
    fi
}


# OS
configure_os() {

    # stop and disable firewalld
    #--------CentOS7/8,RHEL7/8--------
    systemctl stop firewalld >> "$ITA_BUILDER_LOG_FILE" 2>&1
    systemctl disable firewalld >> "$ITA_BUILDER_LOG_FILE" 2>&1

    # disable SELinux
    setenforce 0 >> "$ITA_BUILDER_LOG_FILE" 2>&1
    sed -i`backup_suffix` -e 's/^SELINUX *=.*$/SELINUX=disabled/' /etc/selinux/config >> "$ITA_BUILDER_LOG_FILE" 2>&1
}


# MariaDB
configure_mariadb() {

    # make log directory
    if [ ! -e /var/log/mariadb ]; then
        mkdir -p -m 777 /var/log/mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
    fi

    # check MariaDB is installed
    # The command "yum list" is case insensitive, so both "mariadb-server" and "MariaDB-Server" will be matched.
    yum list installed mariadb-server >> "$ITA_BUILDER_LOG_FILE" 2>&1
    if [ $? -ne 0 ]; then
        log "Install and initialize MariaDB"
        install_mariadb
        initialize_mariadb
    else
        log "Confirm whether root password has been changed"
        env MYSQL_PWD="$db_root_password" mysql -uroot -e "show databases" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        if [ $? == 0 ]; then
            log "Root password of MariaDB is already setting."
        else
            log "Initialize MariaDB"
            initialize_mariadb
        fi
    fi
}


# MariaDB (install)
install_mariadb() {
    # Determine RPM repository and package names
    if [ "${distro_mariadb}" = "yes" ]; then
        local MARIADB_PACKAGE_NAMES=(mariadb mariadb-server expect)
    else
        mariadb_repository ${YUM_REPO_PACKAGE_MARIADB[${REPOSITORY}]}
        local MARIADB_PACKAGE_NAMES=(MariaDB MariaDB-server expect)
    fi

    # Install MariaDB packages
    echo "----------Installation[MariaDB]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    yum install -y "${MARIADB_PACKAGE_NAMES[@]}" >> "$ITA_BUILDER_LOG_FILE" 2>&1

    # Check yum status
    if [ $? != 0 ]; then
        log "ERROR:Installation failed[MariaDB]"
        ERR_FLG="false"
        func_exit_and_delete_file
    fi

    # Check installation status
    yum_package_check "${MARIADB_PACKAGE_NAMES[@]}"
}

# MariaDB (initialize)
initialize_mariadb() {
    # enable and start MariaDB Server
    systemctl enable mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
    error_check
    systemctl start mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
    error_check

    # mariadb-secure-installationへ送信するdb_root_passwordのエスケープをしておく
    local send_db_root_password="$db_root_password"
    send_db_root_password=$(echo "$send_db_root_password"|sed -e 's/\\/\\\\\\\\/g')
    send_db_root_password=$(echo "$send_db_root_password"|sed -e 's/\$/\\\\\\$/g')
    send_db_root_password=$(echo "$send_db_root_password"|sed -e 's/"/\\\\\\"/g')
    send_db_root_password=$(echo "$send_db_root_password"|sed -e 's/\[/\\\\\\[/g')
    send_db_root_password=$(echo "$send_db_root_password"|sed -e 's/\t/\\011/g')

    which mysql_secure_installation >> "$ITA_BUILDER_LOG_FILE" 2>&1
    if [ $? -eq 0 ]; then
        SECURE_COMMAND="mysql_secure_installation"
    else
        SECURE_COMMAND="mariadb-secure-installation"
    fi

    # Exec mariadb-secure-installation with expect
    #   see https://mariadb.com/kb/en/authentication-plugin-unix-socket/
    if [ "${distro_mariadb}" = "yes" ] && [ "${LINUX_OS}" == "CentOS8" -o "${LINUX_OS}" == "RHEL8" ]; then
        # Exactly say, MariaDB 10.4.2 or lower
        expect -c "
            set timeout -1
            spawn ${SECURE_COMMAND}
            expect \"Enter current password for root \\(enter for none\\):\"
            send \"\\r\"
            expect { 
                -re \"Switch to unix_socket authentication.* $\" {
                    send \"n\\r\"
                    expect -re \"Change the root password\\?.* $\"
                    send \"Y\\r\"
                }
                -re \"Set root password\\?.* $\" {
                    send \"Y\\r\"
                }
            }
            expect \"New password:\"
            send \""${send_db_root_password}\\r"\"
            expect \"Re-enter new password:\"
            send \""${send_db_root_password}\\r"\"
            expect -re \"Remove anonymous users\\?.* $\"
            send \"Y\\r\"
            expect -re \"Disallow root login remotely\\?.* $\"
            send \"Y\\r\"
            expect -re \"Remove test database and access to it\\?.* $\"
            send \"Y\\r\"
            expect -re \"Reload privilege tables now\\?.* $\"
            send \"Y\\r\"
        " >> "$ITA_BUILDER_LOG_FILE" 2>&1
    else
        # Exactly say, MariaDB 10.4.3 or higher
        expect -c "
            set timeout -1
            spawn ${SECURE_COMMAND}
            expect \"Enter current password for root \\(enter for none\\):\"
            send \"\\r\"
            expect -re \"Switch to unix_socket authentication.* $\"
            send \"n\\r\"
            expect -re \"Change the root password\\?.* $\"
            send \"Y\\r\"
            expect \"New password:\"
            send \""${send_db_root_password}\\r"\"
            expect \"Re-enter new password:\"
            send \""${send_db_root_password}\\r"\"
            expect -re \"Remove anonymous users\\?.* $\"
            send \"Y\\r\"
            expect -re \"Disallow root login remotely\\?.* $\"
            send \"Y\\r\"
            expect -re \"Remove test database and access to it\\?.* $\"
            send \"Y\\r\"
            expect -re \"Reload privilege tables now\\?.* $\"
            send \"Y\\r\"
        " >> "$ITA_BUILDER_LOG_FILE" 2>&1
    fi

    # copy MariaDB charset file
    copy_and_backup $ITA_EXT_FILE_DIR/etc_my.cnf.d/server.cnf /etc/my.cnf.d/ >> "$ITA_BUILDER_LOG_FILE" 2>&1
    
    # restart MariaDB Server
    systemctl restart mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
    error_check
}

# Apache HTTP Server
configure_httpd() {
    # install some packages
    yum_install ${YUM_PACKAGE["httpd"]}
    # Check installation httpd packages
    yum_package_check ${YUM_PACKAGE["httpd"]}

    # enable and start Apache HTTP Server
    #--------CentOS7/8,RHEL7/8--------
    systemctl enable httpd >> "$ITA_BUILDER_LOG_FILE" 2>&1

}

# PHP
configure_php() {
    echo "${CLOUD_REPO}" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    # enable yum repository
    if [ "${REPOSITORY}" != "yum_all" ]; then
        if [ "${LINUX_OS}" == "CentOS7" -o "${LINUX_OS}" == "RHEL7" ]; then
            yum-config-manager --disable remi-php72 >> "$ITA_BUILDER_LOG_FILE" 2>&1
        fi

        if [ "${CLOUD_REPO}" != "physical" ]; then
            yum_repository ${YUM_REPO_PACKAGE["php_cloud"]}
        else
            yum_repository ${YUM_REPO_PACKAGE["php"]}
        fi
    fi

    # Install php package.
    if [ "$exec_mode" == 3 ]; then
        if [ "${LINUX_OS}" == "CentOS8" -o "${LINUX_OS}" == "RHEL8" ]; then
            dnf module -y reset php >> "$ITA_BUILDER_LOG_FILE" 2>&1
            dnf module -y install php:7.4 >> "$ITA_BUILDER_LOG_FILE" 2>&1
        fi
    fi

    # Install some packages.
    yum_install ${YUM_PACKAGE["php"]}
    # Check installation php packages
    yum_package_check ${YUM_PACKAGE["php"]}

    # Install some pear packages.
    echo "----------Installation[HTML_AJAX]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    pear install ${PEAR_PACKAGE_HTML_AJAX} >> "$ITA_BUILDER_LOG_FILE" 2>&1

    # Check installation HTML_AJAX
    echo "----------Check Installed packages[HTML_AJAX]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    pear list | grep HTML_AJAX >> "$ITA_BUILDER_LOG_FILE" 2>&1
    if [ $? -eq 0 ]; then
        echo "Success pear Install" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    else
       log "ERROR:Installation failed[HTML_AJAX]"
       ERR_FLG="false"
       func_exit_and_delete_file
    fi

    # WORKAROUND! Symbolic link must exist.
    ln -s /usr/share/pear-data/HTML_AJAX/js /usr/share/pear/HTML/js >> "$ITA_BUILDER_LOG_FILE" 2>&1 

    # Change timeout of HTML_AJAX.
    sed -i 's/timeout: 20000,/timeout: 600000,/g' /usr/share/pear-data/HTML_AJAX/js/HTML_AJAX.js >> "$ITA_BUILDER_LOG_FILE" 2>&1 
    sed -i 's/timeout: 20000,/timeout: 600000,/g' /usr/share/pear-data/HTML_AJAX/js/HTML_AJAX_lite.js >> "$ITA_BUILDER_LOG_FILE" 2>&1 

    # Install php-yaml.
    echo "----------Installation[php-yaml]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    if [ "${exec_mode}" == "3" ]; then
        pecl channel-update pecl.php.net >> "$ITA_BUILDER_LOG_FILE" 2>&1
        pecl uninstall  ${PHP_TAR_GZ_PACKAGE["yaml"]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
        echo "" | pecl install ${PHP_TAR_GZ_PACKAGE["yaml"]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
    else
        echo "" | pecl install ${PHP_TAR_GZ_PACKAGE["yaml"]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
    fi

    #Check installation php-yaml
    pecl list | grep yaml >> "$ITA_BUILDER_LOG_FILE" 2>&1
    if [ $? -ne 0 ]; then
       log "ERROR:Installation failed[php-yaml]"
       ERR_FLG="false"
       func_exit_and_delete_file
    fi

    # Install Composer.
    if [ "${exec_mode}" == "3" ]; then
        echo "----------Installation[Composer]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        curl -sS $COMPOSER | php -- --install-dir=/usr/bin --version=1.10.16 >> "$ITA_BUILDER_LOG_FILE" 2>&1       
        # install check Composer.
        if [ ! -e /usr/bin/composer.phar ]; then
            log "ERROR:Installation failed[Composer]"
            ERR_FLG="false"
            func_exit_and_delete_file
        fi
    fi

    # Install PhpSpreadsheet.
    echo "----------Installation[PhpSpreadsheet]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    if [ "${exec_mode}" == "3" ]; then
        /usr/bin/composer.phar require $PHPSPREADSHEET >> "$ITA_BUILDER_LOG_FILE" 2>&1
        # install check PhpSpreadsheet.
        if [ $? -ne 0 ]; then
            log "ERROR:Installation failed[PhpSpreadsheet]"
            ERR_FLG="false"
            func_exit_and_delete_file
        fi       
        mv vendor /usr/share/php/  >> "$ITA_BUILDER_LOG_FILE" 2>&1;
    else
        mkdir -p /usr/share/php/vendor >> "$ITA_BUILDER_LOG_FILE" 2>&1
        cat_tar_gz ${PHPSPREADSHEET_TAR_GZ_PACKAGE_DOWNLOAD_DIR}/vendor.tar.gz | tar zx --strip-components=1 -C /usr/share/php/vendor >> "$ITA_BUILDER_LOG_FILE" 2>&1
        # install check  PhpSpreadsheet.
        if [ $? -ne 0 ]; then
            log "ERROR:Installation failed[PhpSpreadsheet]"
            ERR_FLG="false"
            func_exit_and_delete_file
        fi       
    fi

    #clean
    rm -rf composer.json composer.lock vendor
}


# Git
configure_git() {
    # Install some packages.
    yum_install ${YUM_PACKAGE["git"]}
    # Check installation git packages.
    echo "----------Check Installed packages[$key]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    yum list installed "$key" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    if [ $? != 0 ]; then
        log "ERROR:Package not installed [$key]"
        ERR_FLG="false"
        func_exit_and_delete_file
    fi

}


# Ansible
configure_ansible() {
    yum_install ${YUM_PACKAGE["ansible"]}
    # Check installation yum ansible packages.
    yum_package_check ${YUM_PACKAGE["ansible"]}

    # Replace Ansible config file.
    copy_and_backup "$ITA_EXT_FILE_DIR/etc_ansible/ansible.cfg" "/etc/ansible/"
    
    # Install some pip packages.
    if [ "${exec_mode}" == "3" ]; then
        pip3 install --upgrade pip requests >> "$ITA_BUILDER_LOG_FILE" 2>&1
        for key in ${PIP_PACKAGE["ansible"]}; do
            echo "----------Installation[$key]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            pip3 install $key >> "$ITA_BUILDER_LOG_FILE" 2>&1
            if [ $? -ne 0 ]; then
                log "ERROR:Installation failed[$key]"
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
        done
    else
        echo "----------Installation[${PIP_PACKAGE[pip]}]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        pip3 install --ignore-installed --no-index --find-links=${PIP_PACKAGE_DOWNLOAD_DIR["pip"]} ${PIP_PACKAGE["pip"]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
        if [ $? -ne 0 ]; then
            log "ERROR:Installation failed pip packages."
            ERR_FLG="false"
            func_exit_and_delete_file
        fi

        for key in ${PIP_PACKAGE["ansible"]}; do
            echo "----------Installation[$key]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            pip3 install --ignore-installed --no-index --find-links=${PIP_PACKAGE_DOWNLOAD_DIR["ansible"]} $key >> "$ITA_BUILDER_LOG_FILE" 2>&1
            if [ $? -ne 0 ]; then
                log "ERROR:Installation failed pip packages."
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
        done
    fi

    # Check installation some pip packages.
    for key in ${PIP_PACKAGE_ANSIBLE["remote"]}; do
        echo "----------Check Installed packages[$key]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        pip3 list --format=columns 2>> "$ITA_BUILDER_LOG_FILE" | grep $key >> "$ITA_BUILDER_LOG_FILE" 2>&1
            if [ $? -ne 0 ]; then
                log "ERROR:Package not installed [$key]"
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
    done

}

# Terraform
configure_terraform() {
    
    # Install some pip packages.
    if [ "${exec_mode}" == "3" ]; then
        pip3 install --upgrade pip requests >> "$ITA_BUILDER_LOG_FILE" 2>&1
        for key in ${PIP_PACKAGE["terraform"]}; do
            echo "----------Installation[$key]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            pip3 install $key >> "$ITA_BUILDER_LOG_FILE" 2>&1
            if [ $? -ne 0 ]; then
                log "ERROR:Installation failed[$key]"
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
        done
    else
        for key in ${PIP_PACKAGE["terraform"]}; do
            echo "----------Installation[$key]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            pip3 install --ignore-installed --no-index --find-links=${PIP_PACKAGE_DOWNLOAD_DIR["terraform"]} $key >> "$ITA_BUILDER_LOG_FILE" 2>&1
            if [ $? -ne 0 ]; then
                log "ERROR:Installation failed pip packages."
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
        done
    fi

    # Check installation some pip packages.
    for key in ${PIP_PACKAGE_TERRAFORM["remote"]}; do
        echo "----------Check Installed packages[$key]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        pip3 list --format=columns 2>> "$ITA_BUILDER_LOG_FILE" | grep $key >> "$ITA_BUILDER_LOG_FILE" 2>&1
            if [ $? -ne 0 ]; then
                log "ERROR:Package not installed [$key]"
                ERR_FLG="false"
                func_exit_and_delete_file
            fi
    done

}


# ITA
configure_ita() {
    # Creating a sudo configuration file
    cat << EOS > /etc/sudoers.d/it-automation
apache       ALL=(ALL)  NOPASSWD:ALL
EOS

    #Check create a sudo configuration file
    if [ -e /etc/sudoers.d/it-automation ]; then
        grep -E "^\s*apache\s+ALL=\(ALL\)\s+NOPASSWD:ALL\s*" /etc/sudoers.d/it-automation >> "$ITA_BUILDER_LOG_FILE" 2>&1
        local apache_txt=`echo $?`

        if [ $apache_txt -ne 0 ]; then
            log 'ERROR:Failed to create configuration text in /etc/sudoers.d/it-automation.'
            ERR_FLG="false"
            func_exit_and_delete_file
        fi
    else
        log 'ERROR:Failed to create /etc/sudoers.d/it-automation.'
        ERR_FLG="false"
        func_exit_and_delete_file
    fi

    chmod 440 /etc/sudoers.d/it-automation >> "$ITA_BUILDER_LOG_FILE" 2>&1

    # Comment out "Defaults requiretty" in /etc/sudoers
    grep -v '^\s*#' /etc/sudoers | grep " requiretty" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    if [ $? -eq 0 ]; then
        cp -p /etc/sudoers /etc/sudoers`backup_suffix` >> "$ITA_BUILDER_LOG_FILE" 2>&1
        sed -i -e '/^.*Defaults.*requiretty/ s/^/# /g' /etc/sudoers >> "$ITA_BUILDER_LOG_FILE" 2>&1

        #Check comment out "Defaults requiretty"
        grep '^#' /etc/sudoers | grep -E "^.*Defaults.*requiretty" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        if [ $? -ne 0 ]; then
            log "ERROR:Defaults requiretty is not commented out"
            ERR_FLG="false"
            func_exit_and_delete_file
        fi
    fi

    # install ITA
    source "$ITA_INSTALL_SCRIPTS_DIR/bin/install.sh"

}


################################################################################
# make ITA

make_ita() {

    # configure_yum_env() will setup repository.
    log "Set up repository"
    configure_yum_env

    # offline install(RHEL8 or CentOS8)
    if [ "$LINUX_OS" == "RHEL8" -o "$LINUX_OS" == "CentOS8" ]; then
        if [ "${MODE}" == "local" ]; then
            log "RPM install"
            install_rpm
        fi
    fi
    
    log "OS setting"
    configure_os
    
    log "MariaDB install and setting"
    configure_mariadb

    log "Apache install and setting"
    configure_httpd
    
    log "php install and setting"
    configure_php
        
    if [ "$ansible_driver" == "yes" ] || [ "$cicd_for_iac" == "yes" ]; then
        log "git install and setting"
        configure_git
    fi
    
    if [ "$ansible_driver" == "yes" ]; then
        log "ansible install and setting"
        configure_ansible
    fi

    if [ "$terraform_driver" == "yes" ]; then
        log "packages for terraform install and setting"
        configure_terraform
    fi

    log "Running the ITA installer"
    configure_ita
}


################################################################################
# download ita dependencies

download() {
    # First yum-utils and createrepo must be downloaded, because dependencies
    # are not downloaded if they are already installed.

    # Download yum-utils and createrepo
    if [ "${LINUX_OS}" == "CentOS7" -o "${LINUX_OS}" == "RHEL7" ]; then
        for key in ${YUM__ENV_PACKAGE}; do
                log "Download packages[$key]"
                yum install -y --downloadonly --downloaddir=${YUM_ENV_PACKAGE_DOWNLOAD_DIR["yum-env"]} $key >> "$ITA_BUILDER_LOG_FILE" 2>&1
                download_check
        done
    fi

    # configure_yum_env() will setup repository.
    log "Set up repository"
    configure_yum_env

    # Enable all yum repositories(Other than mariadb).
    log "Enable the required yum repositories."
    yum_repository ${YUM_REPO_PACKAGE["yum-env-enable-repo"]}
    yum_repository ${YUM_REPO_PACKAGE["yum-env-disable-repo"]}
    if [ "${CLOUD_REPO}" != "physical" ]; then
        yum_repository ${YUM_REPO_PACKAGE["php_cloud"]}
    else
        yum_repository ${YUM_REPO_PACKAGE["php"]}
    fi
    # Enable mariadb repositories.
    mariadb_repository ${YUM_REPO_PACKAGE_MARIADB[${REPOSITORY}]}
    
    # MariaDB package names.
    if [ "${distro_mariadb}" = "yes" ]; then
        local MARIADB_PACKAGE_NAMES=(mariadb mariadb-server expect)
    else
        local MARIADB_PACKAGE_NAMES=(MariaDB MariaDB-server expect)
    fi

    # MariaDB download packages.
    log "Download packages[${MARIADB_PACKAGE_NAMES[*]}]"
    if [ "${LINUX_OS}" == "CentOS8" -o "${LINUX_OS}" == "RHEL8" ]; then
        dnf download --resolve --destdir ${YUM_ALL_PACKAGE_DOWNLOAD_DIR} "${MARIADB_PACKAGE_NAMES[@]}" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    elif [ "${LINUX_OS}" == "CentOS7" -o "${LINUX_OS}" == "RHEL7" ]; then
        yumdownloader --resolve --destdir ${YUM_ALL_PACKAGE_DOWNLOAD_DIR} "${MARIADB_PACKAGE_NAMES[@]}" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    fi
    download_check

    # Download php.
    if [ "${LINUX_OS}" == "CentOS8" -o "${LINUX_OS}" == "RHEL8" ]; then
        dnf module -y reset php >> "$ITA_BUILDER_LOG_FILE" 2>&1
        dnf module install --downloadonly --resolve --downloaddir=${YUM_ALL_PACKAGE_DOWNLOAD_DIR} php:7.4 >> "$ITA_BUILDER_LOG_FILE" 2>&1
    fi

    # Download packages.
    for key in ${!YUM_PACKAGE[@]}; do
        log "Download packages[${YUM_PACKAGE[${key}]}]"
        if [ "${LINUX_OS}" == "CentOS8" -o "${LINUX_OS}" == "RHEL8" ]; then
            dnf download --resolve --destdir ${YUM_ALL_PACKAGE_DOWNLOAD_DIR} ${YUM_PACKAGE[${key}]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
        elif [ "${LINUX_OS}" == "CentOS7" -o "${LINUX_OS}" == "RHEL7" ]; then
            yumdownloader --resolve --destdir ${YUM_ALL_PACKAGE_DOWNLOAD_DIR} ${YUM_PACKAGE[${key}]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
        fi
        download_check
    done

    #----------------------------------------------------------------------
    # Download PHP tar.gz packages
    yum_install php-pear

    for key in ${!PHP_TAR_GZ_PACKAGE[@]}; do
        local download_dir="${PHP_TAR_GZ_PACKAGE_DOWNLOAD_DIR[$key]}" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        mkdir -p "$download_dir" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        cd "$download_dir" >> "$ITA_BUILDER_LOG_FILE" 2>&1;
    
        log "Download packages[php-yaml]"
        pecl channel-update pecl.php.net >> "$ITA_BUILDER_LOG_FILE" 2>&1
        pecl download ${PHP_TAR_GZ_PACKAGE[$key]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
        download_check
    done
    cd $ITA_INSTALL_SCRIPTS_DIR >> "$ITA_BUILDER_LOG_FILE" 2>&1;
    
    #----------------------------------------------------------------------
    # Download pip packages.
    
    #pip install
    yum_install python3
    pip3 install --upgrade pip requests >> "$ITA_BUILDER_LOG_FILE" 2>&1
    
    for key in ${!PIP_PACKAGE[@]}; do
        local download_dir="${DOWNLOAD_DIR["pip"]}/$key" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        mkdir -p "$download_dir" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        log "Download packages[${PIP_PACKAGE[$key]}]"
        pip3 download -d "$download_dir" ${PIP_PACKAGE[$key]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
        download_check
    done

    #----------------------------------------------------------------------
    # Download PhpSpreadsheet tar.gz packages
    
    #Composer install
    yum_install php php-json php-zip php-xml php-gd php-mbstring unzip
    
    mkdir -p vendor/composer
    curl -sS $COMPOSER | php -- --install-dir=vendor/composer --version=1.10.16 >> "$ITA_BUILDER_LOG_FILE" 2>&1
    # install check Composer.
    if [ ! -e ./vendor/composer/composer.phar ]; then
        log "ERROR:Installation failed[Composer]"
        ERR_FLG="false"
        func_exit_and_delete_file
    fi

    local download_dir="${PHPSPREADSHEET_TAR_GZ_PACKAGE_DOWNLOAD_DIR}" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    mkdir -p "$download_dir" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    
    log "Download packages[phpspreadsheet]"
    ./vendor/composer/composer.phar require $PHPSPREADSHEET >> "$ITA_BUILDER_LOG_FILE" 2>&1
    download_check
    tar -zcvf "$download_dir"/vendor.tar.gz vendor >> "$ITA_BUILDER_LOG_FILE" 2>&1

    rm -rf composer.json composer.lock vendor

    #----------------------------------------------------------------------
    #Create the installer archive
    ITA_VERSION=`cat $ITA_INSTALL_PACKAGE_DIR/ITA/ita-releasefiles/ita_base | cut -f 7 -d " "`
    DATE=`date +"%Y%m%d%H%M%S"`

    OFFLINE_INSTALL_FILE="ita_Ver"$ITA_VERSION"_offline_"$DATE".tar.gz"

    log "Create an offline installer archive in [$ITA_PACKAGE_OPEN_DIR/$OFFLINE_INSTALL_FILE]"
    (
        if [ ! -e "ITA_PACKAGE_OPEN_DIR/$OFFLINE_INSTALL_FILE" ]; then
            cd $ITA_PACKAGE_OPEN_DIR >> "$ITA_BUILDER_LOG_FILE" 2>&1;
            tar zcf $OFFLINE_INSTALL_FILE ita_install_package >> "$ITA_BUILDER_LOG_FILE" 2>&1
        else
            log "Already exist[$OFFLINE_INSTALL_FILE]"
            log "nothing to do"
        fi
    )
    
}

################################################################################
# global variables

ITA_INSTALL_SCRIPTS_DIR=$(cd $(dirname $0);pwd)
ITA_INSTALL_PACKAGE_DIR=$(cd $(dirname $ITA_INSTALL_SCRIPTS_DIR);pwd)
ITA_PACKAGE_OPEN_DIR=$(cd $(dirname $ITA_INSTALL_PACKAGE_DIR);pwd)

ITA_ANSWER_FILE=$ITA_INSTALL_SCRIPTS_DIR/ita_answers.txt

if [ ! -e "$ITA_INSTALL_SCRIPTS_DIR""/log/" ]; then
    mkdir -m 755 "$ITA_INSTALL_SCRIPTS_DIR""/log/"
fi

if [ "${exec_mode}" == "1" ]; then
    ITA_BUILDER_LOG_FILE=$ITA_INSTALL_SCRIPTS_DIR/log/ita_gather.log
else
    ITA_BUILDER_LOG_FILE=$ITA_INSTALL_SCRIPTS_DIR/log/ita_builder.log
fi

# Authorization check.
log "INFO : Authorization check."
if [ ${EUID:-${UID}} -ne 0 ]; then
    log 'ERROR : Execute with root authority.'
    log 'INFO : Abort installation.'
    ERR_FLG="false"
    func_exit_and_delete_file
fi

#read answer file
log "read answer file"
read_setting_file "$ITA_ANSWER_FILE"

if [ "${exec_mode}" == "2" -o "${exec_mode}" == "3" ]; then
    #check (ita_answers.txt)-----
    if [ "${cicd_for_iac}" != 'yes' -a "${cicd_for_iac}" != 'no' ]; then
        log "ERROR:cicd_for_iac should be set to yes or no"
        ERR_FLG="false"
        func_exit_and_delete_file
    fi

    if [ "${ansible_driver}" != 'yes' -a "${ansible_driver}" != 'no' ]; then
        log "ERROR:ansible_driver should be set to yes or no"
        ERR_FLG="false"
        func_exit_and_delete_file
    fi

    if [ "${terraform_driver}" != 'yes' -a "${terraform_driver}" != 'no' ]; then
       log "ERROR:terraform_driver should be set to yes or no"
       ERR_FLG="false"
       func_exit_and_delete_file
    fi

    if [ ! -n "$db_root_password" ]; then
        log "ERROR:should be set[db_root_password]"
        ERR_FLG="false"
        func_exit_and_delete_file
    fi
fi

if [ "${exec_mode}" == "1" ]; then
    ACTION="Download"
elif [ "${exec_mode}" == "2" -o "${exec_mode}" == "3" ]; then
    ACTION="Install"
fi

if [ "${exec_mode}" == "1" -o "${exec_mode}" == "3" ]; then
    MODE="remote"
elif [ "${exec_mode}" == "2" ]; then
    MODE="local"
fi

if [ "${exec_mode}" == "1" -o "${exec_mode}" == "3" ]; then
    REPOSITORY="${LINUX_OS}"
elif [ "${exec_mode}" == "2" ]; then
    REPOSITORY="yum_all"
fi

if [ "${LINUX_OS}" == "CentOS8" -o "${LINUX_OS}" == "RHEL8" ]; then
    ITA_EXT_FILE_DIR=$ITA_INSTALL_PACKAGE_DIR/ext_files_for_CentOS8.x
elif [ "${LINUX_OS}" == "CentOS7" -o "${LINUX_OS}" == "RHEL7" ]; then
    ITA_EXT_FILE_DIR=$ITA_INSTALL_PACKAGE_DIR/ext_files_for_CentOS7.x
fi
ITA_EXT_FILE_COMMON_DIR=$ITA_INSTALL_PACKAGE_DIR/ext_files


#クラウド環境用リポジトリフラグ設定
ARCH=$(arch)
CLOUD_REPO="physical"
# オフラインインストール時以外かつRHEL8、RHEL7の場合は、インストール環境のyum repolist allをgrepする。
if [ "${exec_mode}" == "1" -o "${exec_mode}" == "3" ]; then
    if [ "${LINUX_OS}" == "RHEL8" -o "${LINUX_OS}" == "RHEL7" ]; then
        cloud_repo_setting
    fi
fi

# OSディストリビューションのMariaDBを使うかどうか
# 使う(distro_mariadb=yes)と指定されていても、CentOS7/RHEL7の場合は公式のMariaDBを利用する
if [ "${distro_mariadb}" = "no" ] || [ "$LINUX_OS" == "RHEL7" ] || [ "$LINUX_OS" == "CentOS7" ]; then
    distro_mariadb=no
else
    distro_mariadb=yes
fi

################################################################################
# base

LOCAL_BASE_DIR=/var/lib/ita

declare -A LOCAL_DIR;
LOCAL_DIR=(
    ["yum"]="$LOCAL_BASE_DIR/yum"
    ["pip"]="$ITA_EXT_FILE_DIR/pip"
    ["php-tar-gz"]="$ITA_EXT_FILE_DIR/php-tar-gz"
    ["phpspreadsheet-tar-gz"]="$ITA_EXT_FILE_DIR/phpspreadsheet-tar-gz"
)

DOWNLOAD_BASE_DIR=$ITA_INSTALL_SCRIPTS_DIR/rpm_files

declare -A DOWNLOAD_DIR;
DOWNLOAD_DIR=(
    ["yum"]="$DOWNLOAD_BASE_DIR/yum"
    ["php-tar-gz"]="$DOWNLOAD_BASE_DIR/php-tar-gz"
    ["pip"]="$DOWNLOAD_BASE_DIR/pip"
    ["phpspreadsheet-tar-gz"]="$DOWNLOAD_BASE_DIR/phpspreadsheet-tar-gz"
)

#-----------------------------------------------------------
# package

# yum repository package (for yum-env-enable-repo)
declare -A YUM_REPO_PACKAGE_YUM_ENV_ENABLE_REPO;
YUM_REPO_PACKAGE_YUM_ENV_ENABLE_REPO=(
    ["RHEL8"]="https://dl.fedoraproject.org/pub/epel/epel-release-latest-8.noarch.rpm"
    ["RHEL7"]="https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm"
    ["CentOS8"]="epel-release"
    ["CentOS7"]="epel-release"
    ["yum_all"]="--enable yum_all"
)

# yum repository package (for yum-env-disable-repo)
declare -A YUM_REPO_PACKAGE_YUM_ENV_DISABLE_REPO;
YUM_REPO_PACKAGE_YUM_ENV_DISABLE_REPO=(
    ["RHEL8"]=""
    ["RHEL7"]=""
    ["CentOS8"]=""
    ["CentOS7"]=""
    ["yum_all"]="--disable base extras updates epel"
)

# yum repository package (for mariadb)
declare -A YUM_REPO_PACKAGE_MARIADB;
YUM_REPO_PACKAGE_MARIADB=(
    ["RHEL7"]="https://downloads.mariadb.com/MariaDB/mariadb_repo_setup"
    ["RHEL8"]="https://downloads.mariadb.com/MariaDB/mariadb_repo_setup"
    ["CentOS7"]="https://downloads.mariadb.com/MariaDB/mariadb_repo_setup"
    ["CentOS8"]="https://downloads.mariadb.com/MariaDB/mariadb_repo_setup"
    ["yum_all"]=""
)

# yum repository package (for php)
declare -A YUM_REPO_PACKAGE_PHP;
YUM_REPO_PACKAGE_PHP=(
    ["RHEL8"]="--set-enabled codeready-builder-for-rhel-8-${ARCH}-rpms"
    ["RHEL7"]="http://rpms.remirepo.net/enterprise/remi-release-7.rpm --enable remi-php74 --enable rhel-7-server-optional-rpms"
    ["CentOS8"]="--set-enabled dummy"
    ["CentOS7"]="http://rpms.remirepo.net/enterprise/remi-release-7.rpm --enable remi-php74"
    ["yum_all"]=""
)

declare -A YUM_REPO_PACKAGE_PHP_CLOUD;
YUM_REPO_PACKAGE_PHP_CLOUD=(
    ["RHEL8_RHUI"]="--set-enabled codeready-builder-for-rhel-8-rhui-rpms"
    ["RHEL7_RHUI2"]="http://rpms.remirepo.net/enterprise/remi-release-7.rpm --enable remi-php74 --enable rhui-rhel-7-server-rhui-optional-rpms"
    ["RHEL7_RHUI2_AWS"]="http://rpms.remirepo.net/enterprise/remi-release-7.rpm --enable remi-php74 --enable rhui-REGION-rhel-server-optional"
    ["RHEL7_RHUI3"]="http://rpms.remirepo.net/enterprise/remi-release-7.rpm --enable remi-php74 --enable rhel-7-server-rhui-optional-rpms"
    ["physical"]=""
)

# all yum repository packages
declare -A YUM_REPO_PACKAGE;
YUM_REPO_PACKAGE=(
    ["yum-env-enable-repo"]=${YUM_REPO_PACKAGE_YUM_ENV_ENABLE_REPO[${REPOSITORY}]}
    ["yum-env-disable-repo"]=${YUM_REPO_PACKAGE_YUM_ENV_DISABLE_REPO[${REPOSITORY}]}
    ["php"]=${YUM_REPO_PACKAGE_PHP[${LINUX_OS}]}
    ["php_cloud"]=${YUM_REPO_PACKAGE_PHP_CLOUD[${CLOUD_REPO}]}
)


################################################################################
# yum package

#-----------------------------------------------------------
# directory

YUM_ENV_PACKAGE_LOCAL_DIR="${LOCAL_DIR["yum"]}/yum-env"
YUM_ALL_PACKAGE_LOCAL_DIR="${LOCAL_DIR["yum"]}/yum_all"

YUM_ENV_PACKAGE_DOWNLOAD_DIR="${DOWNLOAD_DIR["yum"]}/yum-env"
YUM_ALL_PACKAGE_DOWNLOAD_DIR="${DOWNLOAD_DIR["yum"]}/yum_all"

#-----------------------------------------------------------
# package

# yum package (for yum)
declare -A YUM_PACKAGE_YUM_ENV;
YUM_PACKAGE_YUM_ENV=(
    ["remote"]="yum-utils createrepo"
    ["local"]="`list_yum_package ${YUM_ENV_PACKAGE_DOWNLOAD_DIR}`"
)

# yum first install packages
YUM__ENV_PACKAGE="${YUM_PACKAGE_YUM_ENV[${MODE}]}"

# yum install packages
declare -A YUM_PACKAGE;
YUM_PACKAGE=(
    ["httpd"]="httpd mod_ssl"
    ["php"]="php php-bcmath php-cli php-ldap php-mbstring php-mysqlnd php-pear php-pecl-zip php-process php-snmp php-xml zip telnet mailx unzip php-json php-gd python3 python3-pip php-devel libyaml libyaml-devel make sudo crontabs"
    ["git"]="git"
    ["ansible"]="sshpass expect nc"
)


################################################################################
# PEAR packages

# PEAR packages directory
PEAR_PACKAGE_DIR="${ITA_EXT_FILE_COMMON_DIR}/pear"

# HTML_AJAX package path
PEAR_PACKAGE_HTML_AJAX="${PEAR_PACKAGE_DIR}/HTML_AJAX-0.5.8.tgz"


################################################################################
# PHP tar.gz packages

#-----------------------------------------------------------
# directory

# local directory
declare -A PHP_TAR_GZ_PACKAGE_LOCAL_DIR;
PHP_TAR_GZ_PACKAGE_LOCAL_DIR=(
    ["yaml"]="${LOCAL_DIR["php-tar-gz"]}/YAML"
)

# download directory
declare -A PHP_TAR_GZ_PACKAGE_DOWNLOAD_DIR;
PHP_TAR_GZ_PACKAGE_DOWNLOAD_DIR=(
    ["yaml"]="${DOWNLOAD_DIR["php-tar-gz"]}/YAML"
)

#-----------------------------------------------------------
# package

# YAML
declare -A PHP_TAR_GZ_PACKAGE_YAML;
PHP_TAR_GZ_PACKAGE_YAML=(
    ["remote"]="YAML"
    ["local"]="-O `list_pecl_package ${PHP_TAR_GZ_PACKAGE_DOWNLOAD_DIR["yaml"]}`"
)

# all php tar.gz packages
declare -A PHP_TAR_GZ_PACKAGE;
PHP_TAR_GZ_PACKAGE=(
    ["yaml"]=${PHP_TAR_GZ_PACKAGE_YAML[${MODE}]}
)


################################################################################
# PIP package

#-----------------------------------------------------------
# directory

# download directory
declare -A PIP_PACKAGE_DOWNLOAD_DIR;
PIP_PACKAGE_DOWNLOAD_DIR=(
    ["pip"]="${DOWNLOAD_DIR["pip"]}/pip"
    ["ansible"]="${DOWNLOAD_DIR["pip"]}/ansible"
    ["terraform"]="${DOWNLOAD_DIR["pip"]}/terraform"
)

#-----------------------------------------------------------
# package

# pip package (for ansible)
declare -A PIP_PACKAGE_PIP;
PIP_PACKAGE_PIP=(
    ["remote"]="pip"
    ["local"]=`list_pip_package ${PIP_PACKAGE_DOWNLOAD_DIR["pip"]}`
)

# pip package (for ansible)
declare -A PIP_PACKAGE_ANSIBLE;
PIP_PACKAGE_ANSIBLE=(
    ["remote"]="ansible pexpect pywinrm boto3 paramiko boto"
    ["local"]=`list_pip_package ${PIP_PACKAGE_DOWNLOAD_DIR["ansible"]}`
)

# pip package (for terraform)
declare -A PIP_PACKAGE_TERRAFORM;
PIP_PACKAGE_TERRAFORM=(
    ["remote"]="python-hcl2"
    ["local"]=`list_pip_package ${PIP_PACKAGE_DOWNLOAD_DIR["terraform"]}`
)

# all pip packages
declare -A PIP_PACKAGE;
PIP_PACKAGE=(
    ["pip"]=${PIP_PACKAGE_PIP[${MODE}]}
    ["ansible"]=${PIP_PACKAGE_ANSIBLE[${MODE}]}
    ["terraform"]=${PIP_PACKAGE_TERRAFORM[${MODE}]}
)


################################################################################
# PHPSPREADSHEET tar.gz packages

#-----------------------------------------------------------
# directory

# download directory
PHPSPREADSHEET_TAR_GZ_PACKAGE_DOWNLOAD_DIR="${DOWNLOAD_DIR["php-tar-gz"]}/PhpSpreadsheet"

#-----------------------------------------------------------
# package

# Composer
COMPOSER=https://getcomposer.org/installer

# PhpSpreadsheet
PHPSPREADSHEET=""phpoffice/phpspreadsheet":"1.18.0""

################################################################################
# main

#yum update

if [ "$ACTION" == "Install" ]; then
    if [ "$exec_mode" == 2 ]; then
        log "==========[START ITA BUILDER OFFLINE]=========="
        END_MESSAGE="==========[END ITA BUILDER OFFLINE]=========="
        
    elif [ "$exec_mode" == 3 ]; then
        log "==========[START ITA BUILDER ONLINE]=========="
        END_MESSAGE="==========[END ITA BUILDER ONLINE]=========="
    fi
    
    make_ita
elif [ "$ACTION" == "Download" ]; then
    log "==========[START ITA GATHER LIBRARY]=========="
    END_MESSAGE="==========[END ITA GATHER LIBRARY]=========="
    download
else
    log "Unknown parameter \"$ACTION\"" | tee -a "$ITA_BUILDER_LOG_FILE"
fi

log "$END_MESSAGE"

