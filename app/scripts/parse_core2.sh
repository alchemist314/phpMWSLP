#!/bin/bash

# Using core 2
taskset -c 2 /usr/bin/php -q ../includes/web_log_parser.php 2
