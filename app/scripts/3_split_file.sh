#!/bin/bash

# This file is a part of phpMWSLP project. 
# For more information see README.md file.

. config

rm $log_tmp/*
rm $log_tmp_parts/*
split -n r/4 $log_path/$log_date $log_tmp_parts/a_part_of_file_