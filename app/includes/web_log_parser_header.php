<?php

/*
  MIT License

  Copyright (c) 2023-2024 Golovanov Grigoriy
  Contact e-mail: magentrum@gmail.com


  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.

 */

include "../config/config.php";
include "../classes/c_web_log_common.php";
include "../classes/c_web_log_parser.php";


// GeoIP modules
require PHP_MWSLP_ROOT.'/vendor/autoload.php';

use phpMWSLP\app\classes\cWebLogParser;

// Read the config
$aConfigFile=file(PHP_MWSLP_ROOT."/scripts/config");
foreach($aConfigFile as $sStr) {
    $aSplitString=explode("=", $sStr);
    $aConfig[$aSplitString[0]]=str_replace(["\r", "\n", "\"", "'"], "", $aSplitString[1]);
}

// If sDate is set script will collect only string contain this date
$sDate=$aConfig['log_date'];
$oWebLogParser = new cWebLogParser($sDate);

//Get SQL ID by date from config
$sSQLID = $oWebLogParser->fGetLastSQL_ID($oWebLogParser->fVariablesGet('date_sql_short'));

// Show output
$oWebLogParser->fVariablesSet('show_module_output', PHP_MWSLP_SHOW_MODULE_OUTPUT);
// If true SQL query will displayed
$oWebLogParser->fVariablesSet('show_sql_query', PHP_MWSLP_SHOW_SQL_QUERY);
// If true you will see counter for every log line
$oWebLogParser->fVariablesSet('show_module_counter', PHP_MWSLP_SHOW_LINE_COUNTER);
// URL length for output
$oWebLogParser->fVariablesSet('url_length', PHP_MWSLP_URL_LENGTH);
// If true the log file will reading from gz archive
$oWebLogParser->fVariablesSet('gzip', PHP_MWSLP_LOG_IS_GZIP);

