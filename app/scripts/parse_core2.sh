#!/bin/bash

# This file is a part of phpMWSLP project. 
# For more information see README.md file.

# Using core 2
taskset -c 2 /usr/bin/php -q ../includes/web_log_parser.php 2
