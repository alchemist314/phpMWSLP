#!/bin/bash

file_list="
a_part_of_file_aa
a_part_of_file_ab
a_part_of_file_ac
a_part_of_file_ad
"

IFS=$'\n'

for line in $file_list
do
    echo "start web server log parse $line in background"
    /usr/bin/php -q ../includes/web_log_parse_to_file.php 3 UPDATE_BY_DATE $line & 2> /dev/null > /dev/null
    #/usr/bin/php -q ../includes/web_log_parse_to_file.php 3 UPDATE_BY_LAST_DATE $line & 2> /dev/null > /dev/null
    #/usr/bin/php -q ../includes/web_log_parse_to_file.php 3 $line & 2> /dev/null > /dev/null
    sleep 1
done
