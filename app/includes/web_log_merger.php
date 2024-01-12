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
include "web_log_parser_actions.php";

// Load files
$aDir= scandir($aConfig['log_tmp']);
for($l=2; $l<count($aDir); $l++) {
    $aPartArrayFile[]=$aDir[$l];
}

foreach ($aPartArrayFile as $sFileName) {
    $sModulesNameExplode = explode("_a_part", $sFileName);
    $aModules[] = $sModulesNameExplode[0];
    $aArrayPart[$sModulesNameExplode[0]][] = file($aConfig['log_tmp'] . "/" . $sFileName);
}

$aModules = array_unique($aModules);

foreach ($aModules as $sModulesName) {
    $aModulesResult[$sModulesName] = [];
    foreach ($aArrayPart[$sModulesName] as $sNumber => $sValue[0]) {
        $aJSON = json_decode($sValue[0][0], true);
        $aModulesResult[$sModulesName] = array_merge($aModulesResult[$sModulesName], $aJSON);
    }
    $aModulesResult[$sModulesName] = $oWebLogParser->fCalcSumArray($aModulesResult[$sModulesName]);
    arsort($aModulesResult[$sModulesName]);
    $oWebLogParser->fVariablesSet($sModulesName, $aModulesResult[$sModulesName]);
}

// Import modules
$oWebLogParser->fVariablesSet('modules_to_parse', $aModules);

// Check SQL date
$sDateByID = $oWebLogParser->fGetDateByID($sSQLID);
if ($sDateByID !== $sDate) {
    $sErrorMsg = "Error: check dates! \n";
    $sErrorMsg .= "Date from DB ($sDateByID) not equal input date ($sDate)! \n";
    print $sErrorMsg;
    exit;
}

// Update SQL by ID
$oWebLogParser->fUpdateSQL($sSQLID);

?>