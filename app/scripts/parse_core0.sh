#!/bin/bash

# Using core 0
taskset -c 0 /usr/bin/php -q ../includes/web_log_parser.php 0
