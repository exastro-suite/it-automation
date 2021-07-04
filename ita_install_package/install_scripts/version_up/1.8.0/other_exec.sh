#!/bin/sh

CURRENT_DIR=`dirname $0`
ITA_DIRECTORY=$1
NOW_VERSION=$2

# ホストグループ用パラメータシートのパッチ
PHP_MODULE=`cat "${ITA_DIRECTORY}/ita-root/confs/backyardconfs/path_PHP_MODULE.txt"`
${PHP_MODULE} "${CURRENT_DIR}/other_exec.php" ${ITA_DIRECTORY}
