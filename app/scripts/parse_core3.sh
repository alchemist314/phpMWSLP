#!/bin/bash

# Using core 3
taskset -c 3 /usr/bin/php -q ../includes/web_log_parser.php 3
