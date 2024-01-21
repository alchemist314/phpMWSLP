#!/bin/bash

# This file is a part of phpMWSLP project. 
# For more information see README.md file


file_list="
a_part_of_file_aa
a_part_of_file_ab
a_part_of_file_ac
a_part_of_file_ad
"

IFS=$'\n'

i=0
for line in $file_list
do
    echo "start web server log parse $line on core $i in background"
    # Using multi-core
    taskset -c $i /usr/bin/php -q ../includes/web_log_parse_to_file.php 3 $line & 2> /dev/null > /dev/null
    # Using one core
    #/usr/bin/php -q ../includes/web_log_parse_to_file.php 3 $line & 2> /dev/null > /dev/null
    i=$((i+1))
    sleep 1
done
