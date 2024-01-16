<?php


/**
 * MIT License

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

include "web_log_parser_header.php";

// Use multiple parts of log file
if (strlen($argv[2])>0) {
    $oWebLogParser->fVariablesSet('weblog_file', $aConfig['log_tmp_parts']."/".$argv[2]);
    $oWebLogParser->fVariablesSet('out_file_name', $argv[2]);
} else {
    $oWebLogParser->fVariablesSet('weblog_file', $aConfig['log_path']."/".$aConfig['log_date']);
}

if (!isset($argv[1])) {
    print "\nError: please set any of module block: 0,1,2,3\n";
}

$sModules .= file_get_contents(PHP_MWSLP_ROOT."/modules/modules_core".$argv[1]); // 0 1 2 3  - for multiple core running
$sModules = str_replace(["\r", "\n", "'"], "", $sModules);
$aModules = explode(",", $sModules);

// Import modules
$oWebLogParser->fVariablesSet('modules_to_parse', $aModules);

// Check date
$sDateByID=$oWebLogParser->fGetDateByID($sSQLID);
if ($sDateByID!==$sDate) {
	$sErrorMsg ="Error: check dates! \n";
	$sErrorMsg .="Date from DB ($sDateByID) not equal input date ($sDate)! \n";
	print $sErrorMsg;
	exit;
}

// Start parsing
$oWebLogParser->fInit();

// Save to file
$oWebLogParser->fSaveToFile();
