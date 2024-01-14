#!/bin/bash

. config

day=`echo $log_date | cut -d "." -f 1`
month=`echo $log_date | cut -d "." -f 2`
year=`echo $log_date | cut -d "." -f 3`

date_minus_one_day=`date '+%Y%m%d' -d "$year/$month/$day -1 days"`
setting_date=`date '+%Y%m%d' -d "$year/$month/$day"`
date_plus_one_day=`date '+%Y%m%d' -d "$year/$month/$day +1 days"`

cat \
$log_path/$log_prefix-$date_minus_one_day.gz \
$log_path/$log_prefix-$setting_date.gz \
$log_path/$log_prefix-$date_plus_one_day.gz \
> $log_path/tmp.gz