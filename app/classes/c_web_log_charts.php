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

namespace phpMWSLP\app\classes;

class cWebLogChart extends cWebLogCommon {

    /**
     * Prepare data arrays
     */
    public function fInit() {

        $this->fPDO();

        if (isset($_REQUEST['frm_date_stop_id'])) {
            $_REQUEST['frm_date_stop_id'] = $this->fSecurity($_REQUEST['frm_date_stop_id'], 100);
            $_REQUEST['frm_date_start_id'] = $this->fSecurity($_REQUEST['frm_date_start_id'], 100);
            $_SESSION['frm_date_stop_id'] = $_REQUEST['frm_date_stop_id'];
            $_SESSION['frm_date_start_id'] = $_REQUEST['frm_date_start_id'];
        }

        foreach ($this->aModulesArray as $sModuleName) {
            if ($sModuleName == $_REQUEST['frm_modules']) {
                $sFormDivModuleList .= "<div id='" . $sModuleName . "_pie'></div>\n";
                $sFormDivModuleList .= "<div id='" . $sModuleName . "_table'></div>\n";
                $sFormDivModuleList .= "<div id='" . $sModuleName . "_line'></div>\n";
                $sFormDivModuleList .= "<div id='" . $sModuleName . "_bar'></div>\n";

                $sFormSelectModules .= "<option value='" . $sModuleName . "' selected>" . $sModuleName . "</option>";
            } else {
                $sFormSelectModules .= "<option value='" . $sModuleName . "'>" . $sModuleName . "</option>";
            }
        }

        $this->fVariablesSet('html_form_select_modules', $sFormSelectModules);
        $this->fVariablesSet('html_div_module_list', $sFormDivModuleList);

        $vQuery = "SELECT *,
			    strftime(\"%d.%m.%Y\", sdate, 'localtime') as sdate,
                            strftime(\"%Y-%m-%d\", sdate, 'localtime') as sqldate,
                            strftime(\"%Y%m%d\", sdate, 'localtime') as datetonumber
                     FROM
                            " . PHP_MWSLP_SQL_TABLE . "
                ORDER BY
                sdate DESC";
        try {
            $oResult = $this->oPDO->query($vQuery);

            foreach ($oResult->fetchAll() as $aRow) {

                if (($aRow['datetonumber'] >= $_REQUEST['frm_date_start_id']) &&
                        ($aRow['datetonumber'] <= $_REQUEST['frm_date_stop_id'])) {

                    $aBrowsersNames[$aRow['sdate']] = $aRow['browsers_names'];
                    $aBrowsersVersions[$aRow['sdate']] = $aRow['browsers_versions'];
                    $aUniqIP[$aRow['sdate']] = $aRow['ip_uniq'];
                    $aRequest[$aRow['sdate']] = $aRow['query_top100'];
                    $aRequestExcludeKnown[$aRow['sdate']] = $aRow['request_exclude_known'];
                    $aSearchSys[$aRow['sdate']] = $aRow['search_sys'];
                    $aCities[$aRow['sdate']] = $aRow['cities'];
                    $aCountries[$aRow['sdate']] = $aRow['countries'];
                    $aCountriesISO[$aRow['sdate']] = $aRow['countries_id'];
                    $aSocial[$aRow['sdate']] = $aRow['social'];
                    $aOS[$aRow['sdate']] = $aRow['os'];
                    $aOnlineUsersCount[$aRow['sqldate']] = $aRow['10min_traffic'];
                    strlen($aRow['windows_version']) > 0 ? $aWindowsVersions[$aRow['sdate']] = $aRow['windows_version'] : "";
                    $aDisplay[$aRow['sdate']] = $aRow['display_top100'];
                    $aReferals[$aRow['sdate']] = $aRow['referal_top100'];
                    $aIP_top100[$aRow['sdate']] = $aRow['ip_top100'];
                    $DateToDateNumber[$aRow['sqldate']] = $aRow['datetonumber'];
                }
                if ($aRow['datetonumber'] == $_REQUEST['frm_date_start_id']) {
                    $sStartSQLDate = ($aRow['datetonumber']);
                }
                if ($aRow['datetonumber'] == $_REQUEST['frm_date_stop_id']) {
                    $sStopSQLDate = ($aRow['datetonumber']);
                    $jBrowsersNames = $aRow['browsers_names'];
                    $jBrowsersVersions = $aRow['browsers_versions'];
                    $jUniqIP = $aRow['ip_uniq'];
                    $jSearch = $aRow['search_sys'];
                    $jTraf = json_decode($aRow['10min_traffic']);
                    $jOS = $aRow['os'];
                    $jCountries = $aRow['countries'];
                    $jCities = $aRow['cities'];
                    $jIP_top100 = $aRow['ip_top100'];
                    $jRequestTop100 = $aRow['query_top100'];
                    $jReferals = $aRow['referal_top100'];
                    $jWinVer = $aRow['windows_version'];
                    $jSocial = $aRow['social'];
                    $jCountriesISO = $aRow['countries_id'];
                    $jDisplay = $aRow['display_top100'];
                    $jRequestExcludeKnown = $aRow['request_exclude_known'];
                }

                if ($_SESSION['frm_date_start_id'] == $aRow['datetonumber']) {
                    $sFormDateStartID .= "<option value='" . $aRow['datetonumber'] . "' selected>" . $aRow['sdate'] . "</option>\n";
                } else {
                    $sFormDateStartID .= "<option value='" . $aRow['datetonumber'] . "'>" . $aRow['sdate'] . "</option>\n";
                }
                if ($_SESSION['frm_date_stop_id'] == $aRow['datetonumber']) {
                    $sFormDateID .= "<option value='" . $aRow['datetonumber'] . "' selected>" . $aRow['sdate'] . "</option>\n";
                } else {
                    $sFormDateID .= "<option value='" . $aRow['datetonumber'] . "'>" . $aRow['sdate'] . "</option>\n";
                }
            }
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage() . "\n";
        }

        $this->fVariablesSet('html_form_select_date_start_id', $sFormDateStartID);
        $this->fVariablesSet('html_form_select_date_stop_id', $sFormDateID);

        $aFrmChartChekBoxArray = [
            'Line' => 'frm_chart_line',
            'Pie' => 'frm_chart_pie',
            'Bar' => 'frm_chart_bar',
            'Table' => 'frm_chart_table'
        ];
        $aFrmChartTooltip = [
            'Line' => "Line chart",
            'Pie' => "Pie chart",
            'Bar' => "Bar chart",
            'Table' => "Table chart"
        ];
        foreach ($aFrmChartTooltip as $sChartName => $sFrmChartToolTip) {
            unset($sCheckBoxCheckedFlag);
            if (preg_match("/on/", $_REQUEST[$aFrmChartChekBoxArray[$sChartName]]) == true) {
                $aCheckBoxCheckedFlag[$sChartName] = "checked";
            }
            $sFormCheckBoxList .= "<input type='checkbox' id='".$aFrmChartChekBoxArray[$sChartName]."' name='" . $aFrmChartChekBoxArray[$sChartName] . "' title='" . $sFrmChartToolTip . "' " . $aCheckBoxCheckedFlag[$sChartName] . ">" . $sChartName . "\n";
        }
        $this->fVariablesSet('html_form_checkbox_list', $sFormCheckBoxList);

        if (preg_match("/on/", $_REQUEST['frm_sma_enable']) == true) {
            $this->fVariablesSet('html_form_sma_checkbox', "checked");
        }

        if ($_REQUEST['frm_date_stop_id'] < 1) {
            $_REQUEST['frm_date_stop_id'] = 9;
        }

        //Traffic every 10 minut
        foreach ($jTraf as $sKey => $sVal) {
            $sHour = substr($sKey, 0, 2);
            $sMinut = substr($sKey, 2, 2);
            $aStr['traf10'] .= "['" . $sHour . ":" . $sMinut . "', " . $sVal . "],\n";
            $aOnlineUsers10minCount[$sHour . ":" . $sMinut] = $sVal;

            //C3 chart
            $aStr['users_online_10_min_time'] .= " ,'" . $sHour . ":" . $sMinut . "'";
            $aStr['users_online_10_min_value'] .= ", " . $sVal;
        }

        foreach ($aOnlineUsersCount as $sDate => $jStr) {
            $sDateToNumber = $DateToDateNumber[$sDate];
            if (($sDateToNumber >= $sStartSQLDate) && ($sDateToNumber <= $sStopSQLDate)) {
                $aStrOnlineUsers = json_decode($jStr, true);
                foreach ($aStrOnlineUsers as $sTime => $sCount) {
                    $sHour = substr($sTime, 0, 2);
                    $sMinut = substr($sTime, 2, 2);
                    $aOnlineUsersCountFinal[$sDate . " " . $sHour . ":" . $sMinut] = $sCount;
                }
            }
        }

        $aModulesAndArrays = array(
            'module_browsers_names' => &$aBrowsersNames,
            'module_browsers_versions' => &$aBrowsersVersions,
            'module_ip_unique_count' => &$aUniqIP,
            'module_ip_top_100' => &$aIP_top100,
            'module_search_engines' => &$aSearchSys,
            'module_all_requests' => &$aRequest,
            'module_all_referal_links' => &$aReferals,
            'module_all_referal_links_exclude_known' => &$aRequestExcludeKnown,
            'module_device_type' => &$aOS,
            'module_display_resolutions' => &$aDisplay,
            'module_countries' => &$aCountries,
            'module_countriesISO' => &$aCountriesISO,
            'module_cities' => &$aCities,
            'module_10min_online_users_count' => &$aOnlineUsers10minCount,
            'module_windows_version' => &$aWindowsVersions,
            'module_social_networks' => &$aSocial,
            'module_day_online_users_count' => &$aOnlineUsersCountFinal
        );

        $aModulesAndJSONStrings = array(
            'module_browsers_names' => $jBrowsersNames,
            'module_browsers_versions' => $jBrowsersVersions,
            'module_ip_top_100' => $jIP_top100,
            'module_search_engines' => $jSearch,
            'module_all_requests' => $jRequestTop100,
            'module_all_referal_links' => $jReferals,
            'module_all_referal_links_exclude_known' => $jRequestExcludeKnown,
            'module_device_type' => $jOS,
            'module_display_resolutions' => $jDisplay,
            'module_countries' => $jCountries,
            'module_countriesISO' => $jCountriesISO,
            'module_cities' => $jCities,
            'module_windows_version' => $jWinVer,
            'module_social_networks' => $jSocial,
            'module_day_online_users_count'
        );

        $aModulesLineArrayToSlice = array(
            'module_ip_unique_count' => 1000,
            'module_ip_top_100' => 100,
            'module_day_online_users_count' => 10000
        );

        $aModulesAndLegends = array(
            'module_ip_unique_count' => "unique IP count",
            'module_10min_online_users_count' => "count",
            'module_day_online_users_count' => "online users count"
        );

        foreach ($aModulesAndArrays as $sModuleName => $aDataArray) {
            if ($_REQUEST['frm_modules'] == $sModuleName) {
                $this->fVariablesSet('module_name', $sModuleName);
                foreach ($aFrmChartTooltip as $sChartName => $sNULL) {
                    if ($aCheckBoxCheckedFlag[$sChartName] === "checked") {
                        if ($sChartName === "Pie") {
                            $sPieChart .= $this->fPieChartGenerator($sModuleName, $aModulesAndJSONStrings[$sModuleName], $_REQUEST['frm_count_limit']);
                        }
                        if ($sChartName === "Line") {
                            $aResult = $this->fPrepareDataForLineChart($aDataArray, $aModulesLineArrayToSlice[$sModuleName], $aModulesAndLegends[$sModuleName]);
                            $sLineChart .= $this->fLineChartGenerator($sModuleName, $aResult);
                            $sLineChartLegendTable = $this->fLineChartLegendTable($sModuleName, json_decode($aModulesAndJSONStrings[$sModuleName], true), 'line');
                        }
                        if ($sChartName === "Bar") {
                            $aResult = $this->fPrepareDataForLineChart($aDataArray, $aModulesLineArrayToSlice[$sModuleName], $aModulesAndLegends[$sModuleName]);
                            $sBarChart .= $this->fBarChartGenerator($sModuleName, $aResult);
                            $sBarChartLegendTable = $this->fLineChartLegendTable($sModuleName, json_decode($aModulesAndJSONStrings[$sModuleName], true), 'bar');
                        }
                        if ($sChartName === "Table") {
                            $sTable = $this->fTableGenerator($sModuleName, json_decode($aModulesAndJSONStrings[$sModuleName], true));
                        }
                    }
                }
                break;
            }
        }

        $this->fVariablesSet('html_pie_char', $sPieChart);
        $this->fVariablesSet('html_line_char', $sLineChart);
        $this->fVariablesSet('html_line_chart_legend_table', $sLineChartLegendTable);
        $this->fVariablesSet('html_bar_chart_legend_table', $sBarChartLegendTable);
        $this->fVariablesSet('html_bar_char', $sBarChart);
        $this->fVariablesSet('html_table', $sTable);

    }

    /**
     * Generate html table
     *
     * @param string array $sModuleName
     * @param array $aResult
     * @return string
     */

    private function fTableGenerator($sModuleName, $aResult) {
        $sTable = "<table align=\"center\">";
        foreach ($aResult as $sDate => $sValue) {
            $sTable .= "<tr><td>" . $sDate . "</td><td>" . $sValue . "</td></tr>";
        }
        $sTable .= "</table>";
        return "document.getElementById('" . $sModuleName . "_table').innerHTML='" . $sTable . "'\n";
    }

    /**
     * Generate line chart legend table
     *
     * @param string array $sModuleName
     * @param array $aResult
     * @param string $sChartPrefix
     * @return string
     */
    private function fLineChartLegendTable($sModuleName, $aResult, $sChartPrefix='') {
        $sTable="<div style=\"overflow:auto; height:200px; background-color:#efefef;\"><table align=\"center\">";
        foreach ($aResult as $sName => $sValue) {
            $sTable .="<tr><td><input type=\"checkbox\" id=\"".$sChartPrefix."_".$sName."\" onchange=\"fChangeData(this.id, \'".$sChartPrefix."\')\"></td><td>".$sName."</td><td>".$sValue."</td></tr>\\\n";
        }
        $sTable .="</table></div>";
        return "document.getElementById('".$sModuleName."_table').innerHTML='".$sTable."';\n";
    }


    /**
     * Generate javascript data for pie chart
     *
     * @param string array $sModuleName
     * @param string JSON $jResult
     * @param integer $sCutArray
     * @return string
     */
    private function fPieChartGenerator($sModuleName, $jResult, $sCutArray = NULL) {

        $sPieChart = "var " . $sModuleName . "_pie = c3.generate({
                bindto: '#" . $sModuleName . "_pie',
                data: {
                        columns: [
                        ";
        $sCutArray > 0 ? $aDataArray = array_slice((array)json_decode($jResult, true), 0, $sCutArray) : $aDataArray = json_decode($jResult, true);
        foreach ($aDataArray as $sName => $sValue) {
            $sPieChart .= "['" . $sName . "', " . $sValue . "],\n";
        }

        $sPieChart .= "
                        ],
                        type : 'pie'
                },
                pie: {
            	    label: {
            		format: function(value, ration, id) {
            		    return value;
            		}
            	    }
                }
                });
               \n";
        return $sPieChart;
    }

    /**
     * Generate javascript data for line chart
     *
     * @param string array $sModuleName
     * @param array $aResult
     * @return string
     */
    private function fLineChartGenerator($sModuleName, $aResult) {

        $sLineChart = "var " . $sModuleName . "_line = c3.generate({
                bindto: '#" . $sModuleName . "_line',
                data: {";

        if (PHP_MWSLP_CHART_YGRID_LINE) {
            $sLineChart .= "onmouseover :  fMouseOverLine,";
        }

        $sLineChart .= "x : 'x',
                        columns: fChartLimit(".$_REQUEST['frm_chart_limit'].", 'line')
                        ";

        $sStartString = "['x',";
        $sEndString = "],";
        $l = 0;
        // Date
        foreach ($aResult['dates'] as $sDate) {
            if ($l > 0) {
                $sLineChartData .= ", '" . $sDate . "'";
            } else {
                $sLineChartData .= $sStartString . "'" . $sDate . "'";
            }
            $l++;
        }
        $sLineChartData .= $sEndString;
        // Request link
        foreach (array_slice(array_unique((array)$aResult['names']), 0, $_REQUEST['frm_count_limit']) as $sRequestName) {
            $sLineChartData .= "\n['" . $sRequestName . "', ";
            // Date
            $n = 0;
            foreach ($aResult['dates'] as $sDate) {
                strlen($aResult['dates_names_values'][$sDate][$sRequestName]) > 0 ? $sRequestValue = $aResult['dates_names_values'][$sDate][$sRequestName] : $sRequestValue = 'null';
                if ($n > 0) {
                    $sLineChartData .=", " . $sRequestValue;
                } else {
                    $sLineChartData .= $sRequestValue;
                }
                $n++;
            }
            $sLineChartData .= $sEndString;
        }
        $sLineChart .= "
                },

                axis : {
                    x : {
                      //type : 'timeseries',
                        type : 'category',
                            tick : {
                                rotate: 0,
                                //count: 10,
                                multiline: false,
                                culling: true,
                                /*
                                culling: {
                            	    max: 10
                                }
                                */
                                format : '%d.%m.%Y',
                            }
                        },
                },
                /*
                zoom: {
            	    enabled: true,
            	    nitianRange: [30,60]
                },
                subchart: { show:true }
                */
                });

        function fMouseOverLine(e) {
            " . $sModuleName . "_line.ygrids([{value: e.value}]);
        }

\n";
        $this->fVariablesSet('html_line_chart_data', $sLineChartData);
        return $sLineChart;
    }

    /**
     * Generate javascript data for bar chart
     *
     * @param string array $sModuleName
     * @param array $aResult
     * @return string
     */
    private function fBarChartGenerator($sModuleName, $aResult) {

        $sBarChart = "var " . $sModuleName . "_bar = c3.generate({
                bindto: '#" . $sModuleName . "_bar',
                data: {";

        if (PHP_MWSLP_CHART_YGRID_LINE) {
            $sBarChart .= "onmouseover :  fMouseOverBar,";
        }

        $sBarChart .= "
    			x : 'x',
                        columns: fChartLimit(".$_REQUEST['frm_chart_limit'].", 'bar')
                        ";

        $sStartString = "['x',";
        $sEndString = "],";
        $l = 0;
        // Date
        foreach ($aResult['dates'] as $sDate) {
            $l > 0 ? $sBarChartData .= ", '" . $sDate . "'" : $sBarChartData .= $sStartString . "'" . $sDate . "'";
            $l++;
        }
        $sBarChartData .= $sEndString;
        // Request link
        foreach (array_slice(array_unique((array)$aResult['names']), 0, $_REQUEST['frm_count_limit']) as $sRequestName) {
            $sBarChartData .= "\n['" . $sRequestName . "', ";
            // Date
            $n = 0;
            foreach ($aResult['dates'] as $sDate) {
                strlen($aResult['dates_names_values'][$sDate][$sRequestName]) > 0 ? $sRequestValue = $aResult['dates_names_values'][$sDate][$sRequestName] : $sRequestValue = 'null';
                $n > 0 ? $sBarChartData .= ", " . $sRequestValue : $sBarChartData .= $sRequestValue;
                $n++;
            }
            $sBarChartData .= $sEndString;
        }
        $sBarChart .= "
                        ,
                type: 'bar',
                },

                bar: {
                  space: 0.01
                },

                axis : {
                    x : {
                      //type : 'timeseries',
                        type : 'category',
                            tick : {
                                rotate: 0,
                                //count: 10,
                                multiline: false,
                                culling: true,
                                /*
                                culling: {
                            	    max: 10
                                }
                                */
                                format : '%d.%m.%Y',
                            }
                        },
                },
                /*
                zoom: {
            	    enabled: true,
            	    nitianRange: [30,60]
                },
                subchart: { show:true }
                */
                });

        function fMouseOverBar(e) {
            " . $sModuleName . "_bar.ygrids([{value: e.value}]);
        }
\n";

        $this->fVariablesSet('html_line_chart_data', $sBarChartData);
        return $sBarChart;
    }

    /**
     * Prepare data for charts
     *
     * @param array $aDataArray
     * @param integer $sCutArray
     * @param string $sLegendName
     * @return array
     */
    private function fPrepareDataForLineChart($aDataArray, $sCutArray = NULL, $sLegendName = NULL) {
        $sCutArray > 0 ? $aDataArray = array_slice((array)$aDataArray, 0, $sCutArray) : "";
        foreach ($aDataArray as $sDate => $jString) {
            $aDataFromJSON = json_decode($jString, true);
            // jString is not a valid JSON string, try to pass it as a variable
            if (json_last_error() > 0) {
                $aDataFromJSON = $jString;
            }
            if (is_array($aDataFromJSON)) {
                foreach ($aDataFromJSON as $sName => $sValue) {
                    $aResult['dates_names_values'][$sDate][$sName] = $sValue;
                    $aResult['names'][] = $sName;
                }
            } else {
                $aResult['dates_names_values'][$sDate][$sLegendName] = $aDataFromJSON;
                $aResult['names'][$sLegendName] = $sLegendName;
            }
            $aResult['dates'][] = $sDate;
        }
        return $aResult;
    }
}

?>
