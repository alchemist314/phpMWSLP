#!/bin/bash

for i in 0 1 2
do
    echo "start web server log parse with core $i in background"
    /usr/bin/php -q ../includes/web_log_parser.php $i UPDATE_BY_LAST_DATE & 2> /dev/null > /dev/null
    #/usr/bin/php -q ../includes/ web_log_parser.php $i UPDATE_BY_DATE & 2> /dev/null > /dev/null
    #/usr/bin/php -q ../includes/ web_log_parser.php $i UPDATE_BY_SQL_ID & 2> /dev/null > /dev/null
    sleep 1
done