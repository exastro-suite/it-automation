#!/bin/sh

CURRENT_DIR=`dirname $0`
ITA_DIRECTORY=$1
NOW_VERSION=$2

# リリースファイルの削除
RELEASE_FILE_LIST=("${ITA_DIRECTORY}/ita-root/libs/release/ita_hostgroup2"
                   "${ITA_DIRECTORY}/ita-root/libs/release/ita_hostgroup3"
                  )

for ITEM in ${RELEASE_FILE_LIST[@]}; do
    if test -e ${ITEM} ; then
        rm -rf ${ITEM}
    fi
done

# ソースファイルの削除
RELEASE_FILE_LIST=("${ITA_DIRECTORY}/ita-root/webconfs/systems/2100000211_loadTable.php"
                   "${ITA_DIRECTORY}/ita-root/webconfs/systems/2100000212_loadTable.php"
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

# ファイルの暗号化
if [ "${NOW_VERSION}" != "1.7.0" ] ; then
    PHP_MODULE=`cat "${ITA_DIRECTORY}/ita-root/confs/backyardconfs/path_PHP_MODULE.txt"`
    ${PHP_MODULE} "${CURRENT_DIR}/other_exec.php" ${ITA_DIRECTORY}
fi
