#!/bin/bash

. config

day=`echo $log_date | cut -d "." -f 1`
month=`echo $log_date | cut -d "." -f 2`
year=`echo $log_date | cut -d "." -f 3`
month_name=`LANG=en_us_88591; date -d $year/$month/$day +%b`
zgrep $day/$month_name/$year $log_path/tmp.gz > $log_path/$log_date