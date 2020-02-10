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

func_exit() {
    if [ -e /tmp/pear ]; then
        rm -rf /tmp/pear >> "$ITA_BUILDER_LOG_FILE" 2>&1
    fi
    exit
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
                    func_exit
                fi
            done
        fi
    fi
}

download_check() {
    DOWNLOAD_CHK=`echo $?`
    if [ $DOWNLOAD_CHK -ne 0 ]; then
        log "ERROR:Download of file failed"
        func_exit
    fi
}

error_check() {
    DOWNLOAD_CHK=`echo $?`
    if [ $DOWNLOAD_CHK -ne 0 ]; then
        log "ERROR:Stop installation"
        exit
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
                CREATEREPO_CHK=`echo $?`
            else
                yum install -y "$repo" >> "$ITA_BUILDER_LOG_FILE" 2>&1
                CREATEREPO_CHK=`echo $?`
            fi
            
            if [ $CREATEREPO_CHK == 0 ] || [ $CREATEREPO_CHK == 1 ]; then
                echo "Successful repository acquisition" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            else
                log "ERROR:Failed to get repository"
                func_exit
            fi
            shift
        fi

        if [ $# -gt 0 ]; then
            if [ "${LINUX_OS}" == "CentOS7" -o "${LINUX_OS}" == "RHEL7" ]; then
                yum-config-manager "$@" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            elif [ "${LINUX_OS}" == "CentOS8" -o "${LINUX_OS}" == "RHEL8" ]; then
                dnf config-manager "$@" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            fi

            yum clean all >> "$ITA_BUILDER_LOG_FILE" 2>&1
        fi
    fi
}


# enable mariadb repository
mariadb_repository() {
    #Not used for offline installation
    if [ "${REPOSITORY}" != "yum_all" ]; then
        if [ "${LINUX_OS}" == "CentOS7" -o "${LINUX_OS}" == "RHEL7" ]; then
            local repo=$1

            curl -sS "$repo" | bash >> "$ITA_BUILDER_LOG_FILE" 2>&1
            CREATEREPO_CHK=`echo $?`

            if [ $CREATEREPO_CHK == 0 ] || [ $CREATEREPO_CHK == 1 ]; then
                echo "Successful repository acquisition" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            else
                log "ERROR:Failed to get repository"
                func_exit
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

################################################################################
# configuration functions

# read setting file
read_setting_file() {
    local setting_file=$1

    while read line; do
        # convert "foo: bar" to "foo=bar", and keep comment 
        command=`echo $line | sed -E 's/^([^#][^:]*+): *(.*)/\1=\2/'`
        eval $command
    done < $setting_file
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
            rm -rf $LOCAL_BASE_DIR >> "$ITA_BUILDER_LOG_FILE" 2>&1
            mkdir -p $LOCAL_BASE_DIR >> "$ITA_BUILDER_LOG_FILE" 2>&1
            cp -R "$DOWNLOAD_BASE_DIR"/* "$ITA_EXT_FILE_DIR" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            cp -R $ITA_EXT_FILE_DIR/yum/ $LOCAL_BASE_DIR >> "$ITA_BUILDER_LOG_FILE" 2>&1

            yum localinstall -y --nogpgcheck ${YUM__ENV_PACKAGE} >> "$ITA_BUILDER_LOG_FILE" 2>&1

            ls /etc/yum.repos.d/ita.repo >> "$ITA_BUILDER_LOG_FILE" 2>&1 | xargs grep "yum_all" >> "$ITA_BUILDER_LOG_FILE" 2>&1

            if [ $? != 0 ]; then
                echo "["yum_all"]
name="yum_all"
baseurl=file://"${YUM_ALL_PACKAGE_LOCAL_DIR}"
gpgcheck=0
enabled=0
" >> /etc/yum.repos.d/ita.repo

                #create repository "ita_repo"
                createrepo "${YUM_ALL_PACKAGE_LOCAL_DIR}" >> "$ITA_BUILDER_LOG_FILE" 2>&1
                CREATEREPO_CHK=`echo $?`
                if [ "${CREATEREPO_CHK}" -ne 0 ]; then
                    log "ERROR:Repository creation failure"
                    func_exit
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
    fi

    if [ "${MODE}" == "remote" ]; then
        yum_repository ${YUM_REPO_PACKAGE["yum-env-enable-repo"]}
        yum_repository ${YUM_REPO_PACKAGE["yum-env-disable-repo"]}
    fi
    yum clean all >> "$ITA_BUILDER_LOG_FILE" 2>&1
}


# RPM install
install_rpm() {
    RPM_INSTALL_CMD="rpm -ivh --replacepkgs"
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

    if [ "$LINUX_OS" == "RHEL7" -o "$LINUX_OS" == "CentOS7" ]; then
        #Confirm whether it is installed
        yum list installed mariadb-server >> "$ITA_BUILDER_LOG_FILE" 2>&1
        if [ $? == 0 ]; then
            log "MariaDB has already been installed."

            systemctl enable mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
            error_check
            systemctl start mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
            error_check

            #Confirm whether root password has been changed
            mysql -uroot -p$db_root_password -e "show databases" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            if [ $? == 0 ]; then
                log "Root password of MariaDB is already setting."
            else
                expect -c "
                    set timeout -1
                    spawn mysql_secure_installation
                    expect \"Enter current password for root \\(enter for none\\):\"
                    send \"\\r\"
                    expect -re \"Switch to unix_socket authentication.* $\"
                    send \"\\r\"
                    expect -re \"Change the root password\\?.* $\"
                    send \"\\r\"
                    expect \"New password:\"
                    send \""${db_root_password}\\r"\"
                    expect \"Re-enter new password:\"
                    send \""${db_root_password}\\r"\"
                    expect -re \"Remove anonymous users\\?.* $\"
                    send \"Y\\r\"
                    expect -re \"Disallow root login remotely\\?.* $\"
                    send \"Y\\r\"
                    expect -re \"Remove test database and access to it\\?.* $\"
                    send \"Y\\r\"
                    expect -re \"Reload privilege tables now\\?.* $\"
                    send \"Y\\r\"
                " >> "$ITA_BUILDER_LOG_FILE" 2>&1
                
                # copy MariaDB charset file
                copy_and_backup $ITA_EXT_FILE_DIR/etc_my.cnf.d/server.cnf /etc/my.cnf.d/ >> "$ITA_BUILDER_LOG_FILE" 2>&1
                
                # restart MariaDB Server
                #--------CentOS7/8,RHEL7/8--------
                systemctl restart mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
                error_check
            fi
            
        else
            # enable MariaDB repository
            mariadb_repository ${YUM_REPO_PACKAGE_MARIADB[${REPOSITORY}]}

            # install some packages
            echo "----------Installation[MariaDB]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            #Installation
            yum install -y MariaDB MariaDB-server expect >> "$ITA_BUILDER_LOG_FILE" 2>&1

            #Check installation
            if [ $? != 0 ]; then
                log "ERROR:Installation failed[MariaDB]"
                func_exit
            fi
            
            # enable and start (initialize) MariaDB Server
            #--------CentOS7,RHEL7--------
            systemctl enable mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
            error_check
            systemctl start mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
            error_check
            
            expect -c "
                set timeout -1
                spawn mysql_secure_installation
                expect \"Enter current password for root \\(enter for none\\):\"
                send \"\\r\"
                expect -re \"Switch to unix_socket authentication.* $\"
                send \"n\\r\"
                expect -re \"Change the root password\\?.* $\"
                send \"Y\\r\"
                expect \"New password:\"
                send \""${db_root_password}\\r"\"
                expect \"Re-enter new password:\"
                send \""${db_root_password}\\r"\"
                expect -re \"Remove anonymous users\\?.* $\"
                send \"Y\\r\"
                expect -re \"Disallow root login remotely\\?.* $\"
                send \"Y\\r\"
                expect -re \"Remove test database and access to it\\?.* $\"
                send \"Y\\r\"
                expect -re \"Reload privilege tables now\\?.* $\"
                send \"Y\\r\"
            " >> "$ITA_BUILDER_LOG_FILE" 2>&1
            
            # copy MariaDB charset file
            copy_and_backup $ITA_EXT_FILE_DIR/etc_my.cnf.d/server.cnf /etc/my.cnf.d/ >> "$ITA_BUILDER_LOG_FILE" 2>&1
            
            # restart MariaDB Server
            #--------CentOS7,RHEL7--------
            systemctl restart mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
            error_check

        fi
    fi

    if [ "$LINUX_OS" == "RHEL8" -o "$LINUX_OS" == "CentOS8" ]; then
        #Confirm whether it is installed
        yum list installed mariadb-server >> "$ITA_BUILDER_LOG_FILE" 2>&1
        if [ $? == 0 ]; then
            log "MariaDB has already been installed."

            systemctl enable mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
            error_check
            systemctl start mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
            error_check

            #Confirm whether root password has been changed
            mysql -uroot -p$db_root_password -e "show databases" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            if [ $? == 0 ]; then
                log "Root password of MariaDB is already setting."
            else
                expect -c "
                    set timeout -1
                    spawn mysql_secure_installation
                    expect \"Enter current password for root \\(enter for none\\):\"
                    send \"\\r\"
                    expect -re \"Set root password\\?.* $\"
                    send \"Y\\r\"
                    expect \"New password:\"
                    send \""${db_root_password}\\r"\"
                    expect \"Re-enter new password:\"
                    send \""${db_root_password}\\r"\"
                    expect -re \"Remove anonymous users\\?.* $\"
                    send \"Y\\r\"
                    expect -re \"Disallow root login remotely\\?.* $\"
                    send \"Y\\r\"
                    expect -re \"Remove test database and access to it\\?.* $\"
                    send \"Y\\r\"
                    expect -re \"Reload privilege tables now\\?.* $\"
                    send \"Y\\r\"
                " >> "$ITA_BUILDER_LOG_FILE" 2>&1
                
                # copy MariaDB charset file
                copy_and_backup $ITA_EXT_FILE_DIR/etc_my.cnf.d/server.cnf /etc/my.cnf.d/ >> "$ITA_BUILDER_LOG_FILE" 2>&1
                
                # restart MariaDB Server
                #--------CentOS8,RHEL8--------
                systemctl restart mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
                error_check
            fi
            
        else
            # install some packages
            echo "----------Installation[MariaDB]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
            #Installation
            yum install -y mariadb mariadb-server expect >> "$ITA_BUILDER_LOG_FILE" 2>&1

            #Check installation
            if [ $? != 0 ]; then
                log "ERROR:Installation failed[MariaDB]"
                func_exit
            fi
            
            # enable and start (initialize) MariaDB Server
            #--------CentOS8,RHEL8--------
            systemctl enable mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
            error_check
            systemctl start mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
            error_check
            
            expect -c "
                set timeout -1
                spawn mysql_secure_installation
                expect \"Enter current password for root \\(enter for none\\):\"
                send \"\\r\"
                expect -re \"Set root password\\?.* $\"
                send \"Y\\r\"
                expect \"New password:\"
                send \""${db_root_password}\\r"\"
                expect \"Re-enter new password:\"
                send \""${db_root_password}\\r"\"
                expect -re \"Remove anonymous users\\?.* $\"
                send \"Y\\r\"
                expect -re \"Disallow root login remotely\\?.* $\"
                send \"Y\\r\"
                expect -re \"Remove test database and access to it\\?.* $\"
                send \"Y\\r\"
                expect -re \"Reload privilege tables now\\?.* $\"
                send \"Y\\r\"
            " >> "$ITA_BUILDER_LOG_FILE" 2>&1
            
            # copy MariaDB charset file
            copy_and_backup $ITA_EXT_FILE_DIR/etc_my.cnf.d/server.cnf /etc/my.cnf.d/ >> "$ITA_BUILDER_LOG_FILE" 2>&1
            
            # restart MariaDB Server
            #--------CentOS8,RHEL8--------
            systemctl restart mariadb >> "$ITA_BUILDER_LOG_FILE" 2>&1
            error_check

        fi
    fi
}

# Apache HTTP Server
configure_httpd() {
    # install some packages
    yum_install ${YUM_PACKAGE["httpd"]}

    # enable and start Apache HTTP Server
    #--------CentOS7/8,RHEL7/8--------
    systemctl enable httpd >> "$ITA_BUILDER_LOG_FILE" 2>&1

}

# PHP
configure_php() {
    # enable yum repository
    yum_repository ${YUM_REPO_PACKAGE["php"]}

    # Install some packages.
    yum_install ${YUM_PACKAGE["php"]}

    # Install some pear packages.
    pear install ${PEAR_PACKAGE["php"]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
    PEAR_INSTALL_CHECK=`echo $?`
    echo "----------Installation[${PEAR_PACKAGE["php"]}]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1

    if [ $PEAR_INSTALL_CHECK == 1 ] || [ $PEAR_INSTALL_CHECK == 0 ]; then
        echo "Success pear Install" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    else
        log "ERROR:Installation failed[${PEAR_PACKAGE["php"]}]"
        func_exit
    fi

    # WORKAROUND! Symbolic link must exist.
    ln -s /usr/share/pear-data/HTML_AJAX/js /usr/share/pear/HTML/js >> "$ITA_BUILDER_LOG_FILE" 2>&1 

    # Auth.php file modification 
    sed -i 's/$obj =& new $storage_class($options);/$obj = new $storage_class($options);/g' /usr/share/pear/Auth.php

    # Array.php file modification
    sed -i 's/function fetchData($user, $pass)/function fetchData($user=null, $pass=null, $username=null, $password=null, $isChallengeResponse = false)/g' /usr/share/pear/Auth/Container/Array.php

    # Install Spyc.
    echo "----------Installation[Spyc]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    mkdir -p /usr/share/php/spyc-master >> "$ITA_BUILDER_LOG_FILE" 2>&1
    cat_tar_gz ${PHP_TAR_GZ_PACKAGE["spyc"]} | tar zx --strip-components=1 -C /usr/share/php/spyc-master >> "$ITA_BUILDER_LOG_FILE" 2>&1

    # Install Composer.
    if [ "${exec_mode}" == "3" ]; then
        echo "----------Installation[Composer]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        curl -sS $COMPOSER | php -- --install-dir=/usr/bin  >> "$ITA_BUILDER_LOG_FILE" 2>&1
    fi

    # Install PhpSpreadsheet.
    echo "----------Installation[PhpSpreadsheet]----------" >> "$ITA_BUILDER_LOG_FILE" 2>&1
    if [ "${exec_mode}" == "3" ]; then
        /usr/bin/composer.phar require $PHPSPREADSHEET >> "$ITA_BUILDER_LOG_FILE" 2>&1
        mv vendor /usr/share/php/  >> "$ITA_BUILDER_LOG_FILE" 2>&1;
    else
        mkdir -p /usr/share/php/vendor >> "$ITA_BUILDER_LOG_FILE" 2>&1
        cat_tar_gz ${PHPSPREADSHEET_TAR_GZ_PACKAGE_DOWNLOAD_DIR}/vendor.tar.gz | tar zx --strip-components=1 -C /usr/share/php/vendor >> "$ITA_BUILDER_LOG_FILE" 2>&1
    fi

    #clean
    rm -rf /tmp/pear
    rm -rf composer.json composer.lock vendor
}


# Git
configure_git() {
    # Install some packages.
    yum_install ${YUM_PACKAGE["git"]}
}


# Ansible
configure_ansible() {
    yum_install ${YUM_PACKAGE["ansible"]}
    
    # Replace Ansible config file.
    copy_and_backup "$ITA_EXT_FILE_DIR/etc_ansible/ansible.cfg" "/etc/ansible/"
    
    # Install some pip packages.
    pip3 install ${PIP_PACKAGE["ansible"]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
}


# ITA
configure_ita() {
    # Replace sudoers config file.
    copy_and_backup "$ITA_EXT_FILE_DIR/etc/sudoers" "/etc/"

    # install ITA
    "$ITA_INSTALL_SCRIPTS_DIR/ita_installer.sh"
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
        
    if [ "$material" == "yes" ]; then
        log "git install and setting"
        configure_git
    fi
    
    if [ "$ansible_driver" == "yes" ]; then
        log "ansible install and setting"
        configure_ansible
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
    for key in ${!YUM_REPO_PACKAGE[@]}; do
        yum_repository ${YUM_REPO_PACKAGE[$key]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
    done
    # Enable mariadb repositories.
    mariadb_repository ${YUM_REPO_PACKAGE_MARIADB[${REPOSITORY}]}
    
    # MriaDB download packages.
    if [ "${LINUX_OS}" == "CentOS8" -o "${LINUX_OS}" == "RHEL8" ]; then
        log "Download packages[mariadb mariadb-server expect]"
        dnf download --resolve --destdir ${YUM_ALL_PACKAGE_DOWNLOAD_DIR} mariadb mariadb-server expect >> "$ITA_BUILDER_LOG_FILE" 2>&1
    elif [ "${LINUX_OS}" == "CentOS7" -o "${LINUX_OS}" == "RHEL7" ]; then
        log "Download packages[MariaDB MariaDB-server expect]"
        yumdownloader --resolve --destdir ${YUM_ALL_PACKAGE_DOWNLOAD_DIR} MariaDB MariaDB-server expect >> "$ITA_BUILDER_LOG_FILE" 2>&1
    fi
    download_check

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
    # Download pear packages.
    yum_install php-pear

    for key in ${!PEAR_PACKAGE[@]}; do
        local download_dir="${PEAR_PACKAGE_DOWNLOAD_DIR[$key]}" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        mkdir -p "$download_dir" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        cd "$download_dir" >> "$ITA_BUILDER_LOG_FILE" 2>&1;
        
        log "Download packages[${PEAR_PACKAGE[$key]}]"
        pear download ${PEAR_PACKAGE[$key]} >> "$ITA_BUILDER_LOG_FILE" 2>&1
        download_check
    done
    cd $ITA_INSTALL_SCRIPTS_DIR >> "$ITA_BUILDER_LOG_FILE" 2>&1;

    #----------------------------------------------------------------------
    # Download PHP tar.gz packages
    for key in ${!PHP_TAR_GZ_PACKAGE[@]}; do
        local download_dir="${PHP_TAR_GZ_PACKAGE_DOWNLOAD_DIR[$key]}" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        mkdir -p "$download_dir" >> "$ITA_BUILDER_LOG_FILE" 2>&1
        cd "$download_dir" >> "$ITA_BUILDER_LOG_FILE" 2>&1;
    
        log "Download packages[$key]"
        curl -L ${PHP_TAR_GZ_PACKAGE[$key]} -O >> "$ITA_BUILDER_LOG_FILE" 2>&1
        download_check
    done
    cd $ITA_INSTALL_SCRIPTS_DIR >> "$ITA_BUILDER_LOG_FILE" 2>&1;
    
    #----------------------------------------------------------------------
    # Download pip packages.
    
    #pip install
    yum_install python3
    
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
    curl -sS $COMPOSER | php -- --install-dir=vendor/composer >> "$ITA_BUILDER_LOG_FILE" 2>&1

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
ITA_BUILDER_SETTING_FILE=$ITA_INSTALL_SCRIPTS_DIR/ita_builder_setting.txt

if [ ! -e "$ITA_INSTALL_SCRIPTS_DIR""/log/" ]; then
    mkdir -m 755 "$ITA_INSTALL_SCRIPTS_DIR""/log/"
fi

if [ "${exec_mode}" == "1" ]; then
    ITA_BUILDER_LOG_FILE=$ITA_INSTALL_SCRIPTS_DIR/log/ita_gather.log
else
    ITA_BUILDER_LOG_FILE=$ITA_INSTALL_SCRIPTS_DIR/log/ita_builder.log
fi

#read setting file and answer file
log "read setting file"
read_setting_file "$ITA_BUILDER_SETTING_FILE"

#check (ita_builder_setting.txt)
if [ "${linux_os}" != 'CentOS7' -a "${linux_os}" != 'CentOS8' -a "${linux_os}" != 'RHEL7' -a "${linux_os}" != 'RHEL8' -a "${linux_os}" != 'RHEL7_AWS' -a "${linux_os}" != 'RHEL8_AWS' ]; then
    log "ERROR:should be set to CentOS7 or CentOS8 or RHEL7 or RHEL8 or RHEL7_AWS or RHEL8_AWS"
    func_exit
else
    LINUX_OS="${linux_os}"
fi

if [ "${linux_os}" == 'RHEL7_AWS' ]; then
    LINUX_OS='RHEL7'
    AWS_FLG='yes'
elif [ "${linux_os}" == 'RHEL8_AWS' ]; then
    LINUX_OS='RHEL8'
    AWS_FLG='yes'
else
    AWS_FLG='no'
fi

if [ "$LINUX_OS" == "RHEL8" -o "$LINUX_OS" == "RHEL7" ] && [ $AWS_FLG == 'no' ]; then
    if [ ! -n "$redhat_user_name" ]; then
        log "ERROR:should be set[redhat_user_name]"
        func_exit
    fi

    if [ ! -n "$redhat_user_password" ]; then
        log "ERROR:should be set[redhat_user_password]"
        func_exit
    fi

    if [ ! -n "$pool_id" ]; then
        log "ERROR:should be set[pool_id]"
        func_exit
    fi
fi

#read answer file
if [ "${exec_mode}" == "2" -o "${exec_mode}" == "3" ]; then
    log "read answer file"
    read_setting_file "$ITA_ANSWER_FILE"
    #check (ita_answers.txt)-----
    if [ "${material}" != 'yes' -a "${material}" != 'no' ]; then
        log "ERROR:material should be set to yes or no"
        func_exit
    fi

    if [ "${ansible_driver}" != 'yes' -a "${ansible_driver}" != 'no' ]; then
        log "ERROR:ansible_driver should be set to yes or no"
        func_exit
    fi

    if [ "${cobbler_driver}" != 'yes' -a "${cobbler_driver}" != 'no' ]; then
       log "ERROR:cobbler_driver should be set to yes or no"
       func_exit
    fi

    if [ ! -n "$db_root_password" ]; then
        log "ERROR:should be set[db_root_password]"
        func_exit
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

################################################################################
# set subscription
if [ "$exec_mode" != "2" ]; then
    if [ "$LINUX_OS" == "RHEL8" -o "$LINUX_OS" == "RHEL7" ] && [ $AWS_FLG == 'no' ]; then

        log "setting subscriction of RHEL"

        #IDPW
        REDHAT_USER_NAME="${redhat_user_name}"
        REDHAT_USER_PASSWORD="${redhat_user_password}"
        POOL_ID="${pool_id}"

        #Subscription registration
        subscription-manager register --username=${REDHAT_USER_NAME} --password=${REDHAT_USER_PASSWORD} >> "$ITA_BUILDER_LOG_FILE" 2>&1
        REGISTER_CHK=`echo $?`
        
        if [ "${REGISTER_CHK}" -ne 0 -a "${REGISTER_CHK}" -ne 64 ]; then
            log "ERROR:The Red Hat user is not available."
            func_exit
        fi

        #Check consumed
        CONSUMED_POOL_ID=`subscription-manager list --consumed | grep "$POOL_ID" | sed "s/ //g" | cut -f 2 -d ":"`

        if [ "${CONSUMED_POOL_ID}" != "" ]; then
            echo "Subscription is already attached." >> "$ITA_BUILDER_LOG_FILE" 2>&1
        else
            #Check available
            SUBSCRIPTION_POOL_ID=`subscription-manager list --available | grep "$POOL_ID" | sed "s/ //g" | cut -f 2 -d ":"`

            if [ "${SUBSCRIPTION_POOL_ID}" != "" ]; then
                #Attach
                subscription-manager attach --pool="${POOL_ID}" >> "$ITA_BUILDER_LOG_FILE" 2>&1
                if [ $? -ne 0 ]; then
                    log "ERROR:Command[subscription-manager attach] is failed."
                    func_exit
                fi
            else
                log "ERROR:No subscriptions are available from the pool with ID \"${POOL_ID}\"."
                func_exit
            fi
        fi
    fi
fi

################################################################################
# base

LOCAL_BASE_DIR=/var/lib/ita

declare -A LOCAL_DIR;
LOCAL_DIR=(
    ["yum"]="$LOCAL_BASE_DIR/yum"
    ["pear"]="$ITA_EXT_FILE_DIR/pear"
    ["pip"]="$ITA_EXT_FILE_DIR/pip"
    ["php-tar-gz"]="$ITA_EXT_FILE_DIR/php-tar-gz"
    ["phpspreadsheet-tar-gz"]="$ITA_EXT_FILE_DIR/phpspreadsheet-tar-gz"
)

DOWNLOAD_BASE_DIR=$ITA_INSTALL_SCRIPTS_DIR/rpm_files

declare -A DOWNLOAD_DIR;
DOWNLOAD_DIR=(
    ["yum"]="$DOWNLOAD_BASE_DIR/yum"
    ["pear"]="$DOWNLOAD_BASE_DIR/pear"
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
    ["CentOS7"]="https://downloads.mariadb.com/MariaDB/mariadb_repo_setup"
    ["yum_all"]=""
)

# yum repository package (for php)
declare -A YUM_REPO_PACKAGE_PHP;
YUM_REPO_PACKAGE_PHP=(
    ["RHEL7"]="http://rpms.remirepo.net/enterprise/remi-release-7.rpm --enable remi-php72"
    ["CentOS7"]="http://rpms.remirepo.net/enterprise/remi-release-7.rpm --enable remi-php72"
    ["yum_all"]=""
)

# all yum repository packages
declare -A YUM_REPO_PACKAGE;
YUM_REPO_PACKAGE=(
    ["yum-env-enable-repo"]=${YUM_REPO_PACKAGE_YUM_ENV_ENABLE_REPO[${REPOSITORY}]}
    ["yum-env-disable-repo"]=${YUM_REPO_PACKAGE_YUM_ENV_DISABLE_REPO[${REPOSITORY}]}
    ["php"]=${YUM_REPO_PACKAGE_PHP[${REPOSITORY}]}
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
    ["php"]="php php-bcmath php-cli php-ldap php-mbstring php-mysqlnd php-pear php-pecl-zip php-process php-snmp php-xml zip telnet mailx unzip php-json php-zip php-gd python3"
    ["git"]="git"
    ["ansible"]="sshpass expect"
)


################################################################################
# PEAR packages

#-----------------------------------------------------------
# directory

# local directory
declare -A PEAR_PACKAGE_LOCAL_DIR;
PEAR_PACKAGE_LOCAL_DIR=(
    ["php"]="${LOCAL_DIR["pear"]}/php"
)

# download directory
declare -A PEAR_PACKAGE_DOWNLOAD_DIR;
PEAR_PACKAGE_DOWNLOAD_DIR=(
    ["php"]="${DOWNLOAD_DIR["pear"]}/php"
)

#-----------------------------------------------------------
# package

# pear package (for php)
declare -A PEAR_PACKAGE_PHP;
PEAR_PACKAGE_PHP=(
    ["remote"]="Auth HTML_AJAX-beta"
    ["local"]="-O `list_pear_package ${PEAR_PACKAGE_DOWNLOAD_DIR["php"]}`"
)

# all pear packages
declare -A PEAR_PACKAGE;
PEAR_PACKAGE=(
    ["php"]="${PEAR_PACKAGE_PHP[${MODE}]}"
)


################################################################################
# PHP tar.gz packages

#-----------------------------------------------------------
# directory

# local directory
declare -A PHP_TAR_GZ_PACKAGE_LOCAL_DIR;
PHP_TAR_GZ_PACKAGE_LOCAL_DIR=(
    ["spyc"]="${LOCAL_DIR["php-tar-gz"]}/Spyc"
)

# download directory
declare -A PHP_TAR_GZ_PACKAGE_DOWNLOAD_DIR;
PHP_TAR_GZ_PACKAGE_DOWNLOAD_DIR=(
    ["spyc"]="${DOWNLOAD_DIR["php-tar-gz"]}/Spyc"
)

#-----------------------------------------------------------
# package

# Spyc
declare -A PHP_TAR_GZ_PACKAGE_SPYC;
PHP_TAR_GZ_PACKAGE_SPYC=(
    ["remote"]="https://github.com/mustangostang/spyc/archive/0.6.2.tar.gz"
    ["local"]="${PHP_TAR_GZ_PACKAGE_DOWNLOAD_DIR["spyc"]}/0.6.2.tar.gz"
)

# all php tar.gz packages
declare -A PHP_TAR_GZ_PACKAGE;
PHP_TAR_GZ_PACKAGE=(
    ["spyc"]=${PHP_TAR_GZ_PACKAGE_SPYC[${MODE}]}
)


################################################################################
# PIP package

#-----------------------------------------------------------
# directory

# download directory
declare -A PIP_PACKAGE_DOWNLOAD_DIR;
PIP_PACKAGE_DOWNLOAD_DIR=(
    ["ansible"]="${DOWNLOAD_DIR["pip"]}/ansible"
)

#-----------------------------------------------------------
# package

# pip package (for ansible)
declare -A PIP_PACKAGE_ANSIBLE;
PIP_PACKAGE_ANSIBLE=(
    ["remote"]="ansible pexpect pywinrm"
    ["local"]=`list_pip_package ${PIP_PACKAGE_DOWNLOAD_DIR["ansible"]}`
)

# all pip packages
declare -A PIP_PACKAGE;
PIP_PACKAGE=(
    ["ansible"]=${PIP_PACKAGE_ANSIBLE[${MODE}]}
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
PHPSPREADSHEET=""phpoffice/phpspreadsheet":"*""

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

func_exit

