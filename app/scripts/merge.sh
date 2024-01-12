#!/bin/bash

echo "Merge files..."
/usr/bin/php -q ../includes/web_log_merger.php 0 UPDATE_BY_DATE
#/usr/bin/php -q ../includes/web_log_merger.php 0 UPDATE_BY_LAST_DATE
