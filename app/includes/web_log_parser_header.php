<?php

/*
  MIT License

  Copyright (c) 2023 Golovanov Grigoriy
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

// If sDate is set script will calculate only string contain this date
//$sDate="20.12.2023";
$sDate=$aConfig['log_date'];
$oWebLogParser = new cWebLogParser($sDate);
//exit;

// Show output
$oWebLogParser->fVariablesSet('show_module_output', true);

// If 'show_sql_query'==true SQL query will displayed
$oWebLogParser->fVariablesSet('show_sql_query', true);

// If 'show_module_counter'==true you will see counter line for log file
//$oWebLogParser->fVariablesSet('show_module_counter', true);

// URL length for output
$oWebLogParser->fVariablesSet('url_length', 100);

// If 'gzip'==true a file will reading from gz archive
//$oWebLogParser->fVariablesSet('gzip', false);
//$oWebLogParser->fVariablesSet('gzip', true);


