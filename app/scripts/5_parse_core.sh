#!/bin/bash

for i in 0 1 2
do
    echo "start web server log parse with core $i in background"
    # Using multi-core
    taskset -c $i /usr/bin/php -q ../includes/web_log_parser.php $i & 2> /dev/null > /dev/null
    # Using one core
    #/usr/bin/php -q ../includes/web_log_parser.php $i & 2> /dev/null > /dev/null
    sleep 1
done