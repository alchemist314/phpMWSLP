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

namespace phpMWSLP;

use \PDO;

include "../config/config.php";

try {
    $oPDO = new PDO("sqlite:".PHP_MWSLP_SQLITE_PATH);
    $oPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "\nError: " . $e->getMessage()."\n";
}

$vQuery = "CREATE TABLE IF NOT EXISTS `".PHP_MWSLP_PDO_TABLENAME."` (
`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
`browsers_names` VARCHAR (50) NOT NULL,
`browsers_versions` BIGTEXT (5000) NOT NULL,
`ip_uniq` INTEGER (11) NOT NULL,
`ip_top100` BIGTEXT (5000) NOT NULL,
`search_sys` BIGTEXT (5000) NOT NULL,
`query_top100` BIGTEXT (5000) NOT NULL,
`referal_top100` BIGTEXT (5000) NOT NULL,
`request_exclude_known` BIGTEXT (5000) NOT NULL,
`os` VARCHAR (50) NOT NULL,
`display_top100` BIGTEXT (5000) NOT NULL,
`countries` BIGTEXT (5000) NOT NULL,
`countries_id` BIGTEXT (5000) NOT NULL,
`cities` BIGTEXT (5000) NOT NULL,
`10min_traffic` BIGTEXT (5000) NOT NULL,
`windows_version` BIGTEXT (5000) NOT NULL,
`social` BIGTEXT (5000) NOT NULL,
`sdate` DATETIME NOT NULL,
`cdate` TIMESTAMP DATE DEFAULT (datetime('now', 'localtime')));";

try {
    $oResult = $oPDO->query($vQuery);
    echo "\nSuccess!\n";
} catch(PDOException $e) {
    echo "\nError: " . $e->getMessage()."\n";
}

