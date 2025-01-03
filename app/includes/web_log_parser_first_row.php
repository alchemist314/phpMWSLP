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

unset($sErrorMsg);

// Get last SQL ID
$sSQLID=$oWebLogParser->fGetLastSQL_ID();

// Get date by ID
$sDateByID=$oWebLogParser->fGetDateByID($sSQLID);

if ($sDateByID==$sDate) {
    $sErrorMsg = "Error: check dates! \n";
    $sErrorMsg .="Date from DB ($sDateByID) equal input date ($sDate)! \n";
    print $sErrorMsg;
}

// Get row by date
$sSQLIDByDate=$oWebLogParser->fGetLastSQL_ID($oWebLogParser->fVariablesGet('date_sql_short'));

if (!empty($sSQLIDByDate)) {
    $sErrorMsg = "Error: check dates! \n";
    $sErrorMsg .="Date already exist!(".$sDate.")! \n";
    print $sErrorMsg;
}

if (empty($sErrorMsg)) {
    $oWebLogParser->fInsertToSQL();
}
