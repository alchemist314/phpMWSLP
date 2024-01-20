#!/bin/bash

# This file is a part of phpMWSLP project. 
# For more information see README.md file.

# Using core 3
taskset -c 3 /usr/bin/php -q ../includes/web_log_parser.php 3
