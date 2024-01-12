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



$aActionsFlag = [
    'UPDATE_BY_LAST_DATE',
    'UPDATE_BY_DATE',
    'UPDATE_BY_SQL_ID'
];


if (!isset($argv[2]) or (!in_array($argv[2], $aActionsFlag))) {
    print "\nError: please set action flag!\n";
    print "\nallow actions: \n\n";
    print "    UPDATE_BY_LAST_DATE\n";
    print "    UPDATE_BY_DATE\n";
    print "    UPDATE_BY_SQL_ID\n\n";
    exit;
}

// Return SQLID by flag action
switch ($argv[2]) {
    case 'UPDATE_BY_DATE':
	//Get SQL ID by date
	$sSQLID = $oWebLogParser->fGetLastSQL_ID($oWebLogParser->fVariablesGet('date_sql_short'));
	break;
    case 'UPDATE_BY_LAST_DATE':
	//Get last SQL ID
	$sSQLID=$oWebLogParser->fGetLastSQL_ID();
	break;
    case 'UPDATE_BY_SQL_ID':
	// Update SQL by ID
	if (!preg_match("/^\d+$/", $argv[3])) {
	    print "\nError: please set SQL id!\n";
	    exit;
	} else {
	    $sSQLID=trim($argv[2]);
	}
	break;
}
