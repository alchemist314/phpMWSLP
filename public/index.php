<?php
/*
  MIT License

  Copyright (c) 2023-2025 Golovanov Grigoriy
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
	<script>
	    var aNewArr=[];
	    var aArr=[];
	    aArr=[<?php print $oWebLogChart->fVariablesGet('html_line_chart_data'); ?>[]];
	    aArr.forEach((k) => {
    		aStr=k.toString().split(',');
    		k.reverse();
    		k.slice(0, -1);
    		k.unshift(aStr[0]);
    		aNewArr.push(k);
	    });

	</script>
        <form action="index.php" method="post" id="frm_main">
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
            &nbsp;Data limit: <input type="text" value="<?php !isset($_REQUEST['frm_count_limit']) ? print '20':print $_REQUEST['frm_count_limit']; ?>" name="frm_count_limit" size="3" title="Count limit">
            &nbsp;Chart limit: <input type="text" value="<?php !isset($_REQUEST['frm_chart_limit']) ? print '5':print $_REQUEST['frm_chart_limit']; ?>" name="frm_chart_limit" size="3" title="Chart limit">
            <br><input type="checkbox" name="frm_sma_enable" id="frm_sma_enable" <?php print $oWebLogChart->fVariablesGet('html_form_sma_checkbox'); ?>> Enable SMA: <input type="text" value="<?php !isset($_REQUEST['frm_sma_count']) ? print '7':print $_REQUEST['frm_sma_count']; ?>" name="frm_sma_count" id="frm_sma_count" size="3" title="Simple moving average count">
            <input type="checkbox" name="frm_subchart_enable" id="frm_subchart_enable" <?php print $oWebLogChart->fVariablesGet('html_form_subchart_checkbox'); ?>> Enable subchart
            <input type="button" value="OK" name="frm_submit" onclick="fFormSubmit()">

        </form>

        <?php print $oWebLogChart->fVariablesGet('html_div_module_list'); ?>

        <script src="<?= PHP_MWSLP_HTTP ?>/js/action.js?161224"></script>
        <script src="<?= PHP_MWSLP_HTTP ?>/js/d3.v5.min.js?19012023" charset="utf-8"></script>
        <script src="<?= PHP_MWSLP_HTTP ?>/js/c3.min.js?19012023"></script>
        <script type="text/javascript">

        <?php print $oWebLogChart->fVariablesGet('html_pie_char'); ?>
        <?php print $oWebLogChart->fVariablesGet('html_table'); ?>
        <?php print $oWebLogChart->fVariablesGet('html_line_chart_legend_table'); ?>
        <?php print $oWebLogChart->fVariablesGet('html_line_char'); ?>
        <?php print $oWebLogChart->fVariablesGet('html_bar_chart_legend_table'); ?>
        <?php print $oWebLogChart->fVariablesGet('html_bar_char'); ?>


      function fChangeData(val, prefix) {
        if (prefix=='line') {
	    if (val.length>0) {
        	<?php print $oWebLogChart->fVariablesGet('module_name'); ?>_line.load({
        	    unload: true,
            	    columns: fArrayReverse(aArr, val, prefix)
        	});
    	    } else {
        	<?php print $oWebLogChart->fVariablesGet('module_name'); ?>_line.load({
            	    unload: true,
            	    columns:[]
        	});
    	    }
	}
        if (prefix=='bar') {
	    if (val.length>0) {
        	<?php print $oWebLogChart->fVariablesGet('module_name'); ?>_bar.load({
        	    unload: true,
            	    columns: fArrayReverse(aArr, val, prefix)
        	});
    	    } else {
        	<?php print $oWebLogChart->fVariablesGet('module_name'); ?>_bar.load({
            	    unload: true,
            	    columns:[]
        	});
    	    }
	}
     }
     function fTouchDataArray() {
         <?php
         if (($oWebLogChart->fVariablesGet('chart_name')=="Line") && 
             ($oWebLogChart->fVariablesGet('module_name')!=="module_day_online_users_count") &&
             ($oWebLogChart->fVariablesGet('module_name')!=="module_10min_online_users_count")) {
            print "setTimeout(fChangeData(aArr[1][0], 'line'), 1);";
         }
         ?>
     }

    document.getElementById('frm_subchart_enable').addEventListener('change', function() {
        var sSubChart = this.checked;
        <?php 
            if ($oWebLogChart->fVariablesGet('chart_name')=="Line") {
                print $oWebLogChart->fVariablesGet('module_name')."_line.subchart.show(sSubChart);";
            }
            if ($oWebLogChart->fVariablesGet('chart_name')=="Bar") {
                print $oWebLogChart->fVariablesGet('module_name')."_bar.subchart.show(sSubChart);";
            }
        ?>
        });
    
    fTouchDataArray();     
        </script>

    </body>
</html>
