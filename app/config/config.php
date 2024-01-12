<?php

ini_set("display_errors", 1);
//ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set("error_reporting", E_ERROR);
ini_set('memory_limit', '4096M');

date_default_timezone_set("Europe/Moscow");

define('PHP_MWSLP_STORAGE', 'SQLITE');

define('PHP_MWSLP_ROOT', '/var/www/html/git/web_stat_tmp/app');
define('PHP_MWSLP_HTTP', 'https://192.168.1.3/web_stat');

define('PHP_MWSLP_GEO_DB', '/usr/share/GeoIP/GeoLite2-City.mmdb');

// SQLite tablename
define('PHP_MWSLP_PDO_TABLENAME', 'webserverlog');

// SQLite path
define('PHP_MWSLP_SQLITE_PATH', PHP_MWSLP_ROOT."/sql/".PHP_MWSLP_PDO_TABLENAME.".db");
        
