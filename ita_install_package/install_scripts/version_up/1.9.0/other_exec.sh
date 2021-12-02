#!/bin/sh

CURRENT_DIR=`dirname $0`
ITA_DIRECTORY=$1
NOW_VERSION=$2

# メニュー作成で作成したメニューのloadTableの変数名誤り修正
find ${ITA_DIRECTORY}/ita-root/webconfs/sheets/ -type f | xargs sed -i "s/\$ntRowLength/\$intRowLength/g"
