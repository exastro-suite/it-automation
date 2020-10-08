#!/bin/sh

grep '^\s*extension\s*=\s*yaml\.so\s*$' /etc/php.ini
if [ $? = 1 ]; then
    #php.iniに'extension=yaml.so'を追記する
    echo 'extension=yaml.so' >> /etc/php.ini
fi
