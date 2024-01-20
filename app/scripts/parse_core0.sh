#!/bin/bash

# This file is a part of phpMWSLP project. 
# For more information see README.md file.

# Using core 0
taskset -c 0 /usr/bin/php -q ../includes/web_log_parser.php 0
