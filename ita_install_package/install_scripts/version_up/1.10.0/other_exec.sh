#!/bin/sh

CURRENT_DIR=`dirname $0`
ITA_DIRECTORY=$1
NOW_VERSION=$2

#Ansible-driver用awxユーザ、sshキー作成
if test -e "${ITA_DIRECTORY}/ita-root/libs/release/ita_ansible-driver" ; then

    #check awx user
    cat /etc/group | grep ^awx:
    if [ $? -ne 0 ]; then
        #create awx user
        useradd awx
    fi

    #create ssh key
    if test -e /home/awx/.ssh/rsa_awx_key ; then
        rm -f /home/awx/.ssh/rsa_awx_key*
    fi
    su - awx -c 'ssh-keygen -t rsa -b 4096 -C "" -f ~/.ssh/rsa_awx_key -N ""'
    su - awx -c 'cat ~/.ssh/rsa_awx_key.pub >> ~/.ssh/authorized_keys'
    chmod 600 /home/awx/.ssh/authorized_keys
    cat /home/awx/.ssh/rsa_awx_key | base64 | tr '[A-Za-z]' '[N-ZA-Mn-za-m]' > "$ITA_DIRECTORY"/ita-root/uploadfiles/2100040702/ANS_GIT_SSH_KEY_FILE/0000000001/rsa_awx_key
    cat /home/awx/.ssh/rsa_awx_key | base64 | tr '[A-Za-z]' '[N-ZA-Mn-za-m]' > "$ITA_DIRECTORY"/ita-root/uploadfiles/2100040702/ANS_GIT_SSH_KEY_FILE/0000000001/old/0000000001/rsa_awx_key
fi
