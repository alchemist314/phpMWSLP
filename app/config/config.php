<?php

ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_DEPRECATED);
//ini_set("error_reporting", E_ERROR);
ini_set('memory_limit', '4096M');

date_default_timezone_set("Europe/Moscow");

// Path to root (app)
define('PHP_MWSLP_ROOT', '/var/www/html/git/phpMWSLP/app');
//Path to public 
define('PHP_MWSLP_HTTP', 'https://192.168.1.3/git/phpMWSLP/public');
// Path to GEO DB
define('PHP_MWSLP_GEO_DB', '/usr/share/GeoIP/GeoLite2-City.mmdb');

// SQLite tablename
define('PHP_MWSLP_PDO_TABLENAME', 'webserverlog');

// SQLite path
define('PHP_MWSLP_SQLITE_PATH', PHP_MWSLP_ROOT."/sql/".PHP_MWSLP_PDO_TABLENAME.".db");
        
// Show horizontal line on the chart
define('PHP_MWSLP_CHART_YGRID_LINE', false);
// Show module output
define('PHP_MWSLP_SHOW_MODULE_OUTPUT', true);
// If true SQL query will displayed
define('PHP_MWSLP_SHOW_SQL_QUERY', true);
// If true you will see counter for every log line
define('PHP_MWSLP_SHOW_LINE_COUNTER', false);
// URL length for output
define('PHP_MWSLP_URL_LENGTH', 100);
// If true the log file will reading from gz archive
define('PHP_MWSLP_LOG_IS_GZIP', true);


