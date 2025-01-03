#!/bin/bash

# This file is a part of phpMWSLP project. 
# For more information see README.md file.

# Multiple date parse.

# 1. Change path to directories: -----------------------------

APP_PATH="/var/www/html/git/phpMWSLP/app"
CONFIG_PATH="${APP_PATH}/scripts/config"

# Don't forget to change the path to log files folder
LOG_FOLDER="/var/logs/12.2024"
# ------------------------------------------------------------

# 2. Modify list of dates: -----------------------------------

dates="
01.02.2024
02.02.2024
03.02.2024
04.02.2024
05.02.2024
06.02.2024
07.02.2024
08.02.2024
09.02.2024
10.02.2024
11.02.2024
12.02.2024
13.02.2024
14.02.2024
15.02.2024
16.02.2024
17.02.2024
"
# ------------------------------------------------------------

IFS=$'\n'

for date in $dates
    do
	# 3. Configure variables: --------------------------------------------------------

	echo "start date: $date"
	echo "log_date=$date" > $CONFIG_PATH
        echo "log_path=$LOG_FOLDER" >> $CONFIG_PATH    
	echo "log_prefix=access.log" >> $CONFIG_PATH
	echo "log_tmp="${APP_PATH}/tmp" >> $CONFIG_PATH
	echo "log_tmp_parts="${APP_PATH}/tmp/parts" >> $CONFIG_PATH

	# --------------------------------------------------------------------------------

	echo "start 1_log_glue.sh"
	/bin/bash $APP_PATH/scripts/1_log_glue.sh
	echo "start 2_grep_date.sh"
	/bin/bash $APP_PATH/scripts/2_grep_date.sh
	echo "start 3_split_file.sh"
	/bin/bash $APP_PATH/scripts/3_split_file.sh
	echo "start 4_first.sh"
	/bin/bash $APP_PATH/scripts/4_first.sh
	echo "start 5_parse_core.sh"
	/bin/bash $APP_PATH/scripts/5_parse_core.sh 2>/dev/null > /dev/null
	echo "start 6_parse_to_file.sh"
	/bin/bash $APP_PATH/scripts/6_parse_to_file.sh 2>/dev/null > /dev/null
	echo "start while loop..."
	
	sWhileStop=0
	while [ $sWhileStop -le 1 ]
	do 
	    PID1=`ps -aux | grep web_log_parser.php | grep -v 'grep web_log_parser.php'`
	    PID2=`ps -aux | grep web_log_parse_to_file.php | grep -v 'grep web_log_parse_to_file.php'`
	    if [ -z "$PID1" ] && [ -z "$PID2" ]; then
		echo "start 7_merge.sh"
		/bin/bash $APP_PATH/scripts/7_merge.sh 2>/dev/null > /dev/null
		sWhileStop=$((sWhileStop + 1))
	    else
		echo "parser still work!"
	    fi
	    echo "sleep 60 seconds..."
	    sleep 60
	done
done
