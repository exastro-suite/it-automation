#!/bin/sh

ITA_DIRECTORY=$1

# リリースファイルの削除
RELEASE_FILE_LIST=("${ITA_DIRECTORY}/ita-root/libs/release/ita_hostgroup2"
                   "${ITA_DIRECTORY}/ita-root/libs/release/ita_hostgroup3"
                  )

for ITEM in ${RELEASE_FILE_LIST[@]}; do
    if test -e ${ITEM} ; then
        rm -rf ${ITEM}
    fi
done

#サービスの削除
SERVICE_FILE_LIST=("ky_hostgroup_make_var.service"
                   "ky_hostgroup_regist_var_legacy.service"
                   "ky_hostgroup_regist_var_legacy_role.service"
                  )

for ITEM in ${SERVICE_FILE_LIST[@]}; do
    if test -e "/usr/lib/systemd/system/${ITEM}" ; then
        systemctl disable ${ITEM}
        rm -rf "/usr/lib/systemd/system/${ITEM}"
    fi
done
