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
use phpMWSLP\app\classes\cWebLogChart;
use phpMWSLP\app\classes\cWebLogCommon;

include "../app/config/config.php";
include "../app/classes/c_web_log_common.php";
include "../app/classes/c_web_log_charts.php";

$oWebLogChart = new cWebLogChart($oPDO);
$oWebLogChart->fInit();


?>
<html>
    <head>
        <link href="<?= PHP_MWSLP_HTTP ?>/css/c3.css?19012023" rel="stylesheet" type="text/css">
        <link href="<?= PHP_MWSLP_HTTP ?>/css/style.css?19012023" rel="stylesheet" type="text/css">
    </head>

    <body>
        <form action="index.php" method="post">
            <select name="frm_modules">
                <?php print $oWebLogChart->fVariablesGet('html_form_select_modules'); ?>
            </select>
            <select name="frm_date_start_id">
                <?php print $oWebLogChart->fVariablesGet('html_form_select_date_start_id'); ?>
            </select>
            <select name="frm_date_stop_id">
                <?php print $oWebLogChart->fVariablesGet('html_form_select_date_stop_id'); ?>
            </select>
            <br>
            <?php print $oWebLogChart->fVariablesGet('html_form_checkbox_list'); ?>
            &nbsp;<input type="text" value="20" name="frm_count_limit" size="3" title="Count limit">
            <input type="submit" value="OK" name="frm_submit">
            
        </form>

        <?php print $oWebLogChart->fVariablesGet('html_div_module_list'); ?>
        
        <script src="<?= PHP_MWSLP_HTTP ?>/js/d3.v5.min.js?19012023" charset="utf-8"></script>
        <script src="<?= PHP_MWSLP_HTTP ?>/js/c3.min.js?19012023"></script>
        <script type="text/javascript">
        <?php print $oWebLogChart->fVariablesGet('html_pie_char'); ?>
        <?php print $oWebLogChart->fVariablesGet('html_table'); ?>
        <?php print $oWebLogChart->fVariablesGet('html_line_char'); ?>
        <?php print $oWebLogChart->fVariablesGet('html_bar_char'); ?>
        </script>
       
    </body>
</html>




