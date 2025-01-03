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

use phpMWSLP\app\classes\cWebLogCommon;
use GeoIp2\Database\Reader;
use \Datetime;

class cWebLogParser extends cWebLogCommon {

    public $oGeoDBreader;
    public $oDateTime;
    
    // Using in method fModuleBrowsers()
    public $aPatternBrowsers = array(
        "Chrome" => "Chrome",
        "Firefox" => "Firefox",
        "Opera" => "Opera",
        "YaBrowser" => "YaBrowser",
        "Edge" => "Edge",
        "Safari" => "Safari",
    );
    // Using in method fModuleReferalLinks()
    public $aPatternSocialNetworks = array(
        "facebook" => "facebook.com",
        "twitter" => "twitter.com",
        "instagram" => "instagram.com",
        "telegram" => "telegram.org",
        "t.me" => "telegram.org",
        "vk.com" => "vk.com",
        "ok.ru" => "ok.ru",
        "dzen.ru" => "dzen.ru",
        "t.co" => "twitter.com",
        "youtube.com" => "youtube.com"
    );

    // Using in method fModuleReferalLinks()
    public $aPatternSearchEngines = array(
        "google" => "google",
        "yandex" => "yandex",
        "ya.ru" => "yandex",
        "bing" => "bing",
        "duckduckgo" => "duckduckgo",
        "yahoo" => "yahoo",
        "msn.com" => "msn.com",
        "rambler" => "rambler",
        "mail.ru" => "mail.ru",
        "baidu.com" => "baidu.com"
    );

    // Using in method fModuleReferalLinks()
    // Which domain will be excluded from output
    public $aPatternExcludeDomains = [
	PHP_MWSLP_DOMAIN_TO_EXLUDE
    ];

    // Using in method fModuleDeviceType()
    public $aPatternDeviceList = [
        "Android" => "Android",
        "iPhone" => "iPhone",
        "iPad" => "iPad",
        "Macintosh" => "Mac",
        "Windows" => "Windows",
        "X11" => "Linux"
    ];

    // Using in method fModuleDeviceType()
    // Windows versions
    public $aPatternWindowsVersions = [
        "Windows NT 10.0" => "Windows 10",
        "Windows NT 6.1" => "Windows 7",
        "Windows NT 6.3" => "Windows 8",
        "Windows NT 5.1" => "Windows XP",
        "Windows NT 5.0" => "Windows 2000",
        "Windows NT 6.2" => "Windows 8.1",
        "Windows NT 6.0" => "Windows Vista"
    ];

    // Using in method fModuleOSTypeAndVersions()
    public $aPatternOSVersions = array("Linux" => 60, "Windows" => 40, "Mac OS X" => 40);

    // Using in fUpdateSQL() method 
    // Modules and their column names in database
    public $aPatternSQLNames = [
        'module_browsers_names' => 'browsers_names',
        'module_browsers_versions' => 'browsers_versions',
        'module_ip_unique_count' => 'ip_uniq',
        'module_ip_top_100' => 'ip_top100',
        'module_search_engines' => 'search_sys',
        'module_all_requests' => 'query_top100',
        'module_all_referal_links' => 'referal_top100',
        'module_all_referal_links_exclude_known' => 'request_exclude_known',
        //'module_os_type' => 'os',
        'module_device_type' => 'os',
        'module_display_resolutions' => 'display_top100',
        'module_countries' => 'countries',
        'module_countriesISO' => 'countries_id',
        'module_cities' => 'cities',
        'module_10min_online_users_count' => '10min_traffic',
        'module_windows_version' => 'windows_version',
        'module_social_networks' => 'social'
    ];

    // Using in fUpdateSQL() method 
    // Which modules results will be cutting
    public $aPatternArrayToSlice = [
        'module_browsers_versions' => 100,
        'module_ip_top_100' => 100,
        'module_all_requests' => 100,
        'module_all_referal_links' => 100,
        'module_display_resolutions' => 100,
        'module_all_referal_links_exclude_known' => 100
    ];

    // Using in fUpdateSQL() method 
    // Which modules results will storage data as JSON string
    public $aPatternArrayToJSON = [
        'module_browsers_versions' => 'browsers_versions',
        'module_browsers_names' => 'browsers_names',
        'module_ip_top_100' => 'ip_top100',
        'module_search_engines' => 'search_sys',
        'module_all_requests' => 'query_top100',
        'module_all_referal_links' => 'referal_top100',
        'module_all_referal_links_exclude_known' => 'request_exclude_known',
        //'module_os_type' => 'os',
        'module_device_type' => 'os',
        'module_display_resolutions' => 'display_top100',
        'module_countries' => 'countries',
        'module_countriesISO' => 'country_id',
        'module_cities' => 'cities',
        'module_10min_online_users_count' => '10min_traffic',
        'module_windows_version' => 'windows_version',
        'module_social_networks' => 'social'
    ];

    function __construct($sDate) {
        $this->fPDO();
        $this->oDateTime=new DateTime();
        $this->oGeoDBreader = new Reader(PHP_MWSLP_GEO_DB);
        if (strlen($sDate) > 0) {
            $this->fVariablesSet('date', $sDate);
            $this->fDatePrepare($sDate);
        }
    }
    
    /**
     * Prepare data arrays
     */
    public function fInit() {
        if ($this->fVariablesGet('gzip') == true) {
            $aWebLog = gzfile($this->fVariablesGet('weblog_file'));
        } else {
            $aWebLog = file($this->fVariablesGet('weblog_file'));
        }
        $sWebLogCount = count((array)$aWebLog);

        // Create 10min period array
        $this->fCreate10MinArray();

        if (count((array)$this->fVariablesGet('modules_to_parse')) > 0) {
            $sCount = 0;
            /*
             * $sStr variable contains (for exmaple):
             * 
             *  Sofisticated case:
             * 
             *  192.168.1.2 - - [11/Oct/2023:03:34:02 +0300] "GET /stat?s=1;x;x393x873x24;xhttps%3A//192.168.1.1/ HTTP/1.0" 200 49 "https://192.168.1.1/test.html" "Mozilla/5.0 (Linux; arm_64; Android 13; 21081111RG) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 YaBrowser/23.7.5.95.00 SA/3 Mobile Safari/537.36"  "192.168.1.2"
             * 
             *  Ordinary case:
             * 
             *  192.168.1.2 - - [11/Oct/2023:03:34:02 +0300] "GET https%3A//192.168.1.1/test.html HTTP/1.0" 200 49 "https://192.168.1.1/test.html" "Mozilla/5.0 (Linux; arm_64; Android 13; 21081111RG) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 YaBrowser/23.7.5.95.00 SA/3 Mobile Safari/537.36"  "192.168.1.2"
             * 
             *  String explanation:
             * 
             *  NGINX config variable    = Content
             * 
             *  $remote_addr             = 192.168.1.2
             *  -                        =  -
             *  remote_user              =  -
             *  [$time_local]            = [11/Oct/2023:03:34:02 +0300]
             *  "$request"               = "GET /stat?s=1;x;x393x873x24;xhttps%3A//192.168.1.1/ HTTP/1.0"
             *  $status                  = 200
             *  $body_bytes_sent         = 49
             *  "$http_referer"          = "https://192.168.1.1/test.html"
             *  "$http_user_agent"       = "Mozilla/5.0 (Linux; arm_64; Android 13; 21081111RG) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 YaBrowser/23.7.5.95.00 SA/3 Mobile Safari/537.36"
             *  "$http_x_forwarded_for"  = "192.168.1.2"
             * 
             */
            foreach ($aWebLog as $sStr) {
                $sStr = urldecode($sStr);

                /*  $aSplittedString variable contains (for exmaple):
                 * 
                 *  $aSplittedString[0]  = 192.168.1.2
                 *  $aSplittedString[1]  = -
                 *  $aSplittedString[2]  = -
                 *  $aSplittedString[3]  = [11/Oct/2023:03:34:02
                 *  $aSplittedString[4]  =
                 *  $aSplittedString[5]  = +0300]
                 *  $aSplittedString[6]  = "GET
                 *  $aSplittedString[7]  = /stat?s=1;x;x393x873x24;xhttps%3A//192.168.1.1/
                 *  $aSplittedString[8]  = HTTP/1.0"
                 *  $aSplittedString[9]  = 200
                 *  $aSplittedString[10] = 49
                 *  $aSplittedString[11] = "https://192.168.1.1/test.html"
                 *  $aSplittedString[12] = "Mozilla/5.0
                 *  $aSplittedString[13] = (Linux;
                 *  $aSplittedString[14] = arm_64;
                 *  $aSplittedString[15] = Android
                 *  $aSplittedString[16] = 13;
                 *  $aSplittedString[17] = 21081111RG)
                 *  $aSplittedString[18] = AppleWebKit/537.36
                 *  $aSplittedString[19] = (KHTML,
                 *  $aSplittedString[20] = like
                 *  $aSplittedString[21] = Gecko)
                 *  $aSplittedString[22] = Chrome/114.0.0.0
                 *  $aSplittedString[23] = YaBrowser/23.7.5.95.00
                 *  $aSplittedString[24] = SA/3
                 *  $aSplittedString[25] = Mobile
                 *  $aSplittedString[26] = Safari/537.36"
                 *  $aSplittedString[27] = "192.168.1.2"
                 * 
                 */

                $aSplittedString = explode(" ", $sStr);

                // Expression in quotation marks

                /*
                  $aMatches contains (for exmaple):

                  [1] => Array
                  (
                  [0] => GET /stat?s=1;xhttps://192.168.1.1/index.php;x393x873x24;xhttps://192.168.1.1/test.html HTTP/1.0
                  [1] => https://192.168.1./test.html
                  [2] => Mozilla/5.0 (Linux; arm_64; Android 13; 21081111RG) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 YaBrowser/23.7.5.95.00 SA/3 Mobile Safari/537.36
                  [3] => 192.168.1.2
                  )

                  ..or in ordinary request case:

                  [1] => Array
                  (
                  [0] => GET https://192.168.1.1/index.php HTTP/1.0
                  [1] => https://192.168.1./test.html
                  [2] => Mozilla/5.0 (Linux; arm_64; Android 13; 21081111RG) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 YaBrowser/23.7.5.95.00 SA/3 Mobile Safari/537.36
                  [3] => 192.168.1.2
                  )

                 */

                preg_match_all('/"(.*?)"/', $sStr, $aMatches);

                if (preg_match("/stat\?s=1/", $sStr)) {
                    $sReferalFlag = "sofisticated";

                    /*
                      $aRequestArray contains (for exmaple):

                      Array
                      (
                      [0] /stat?s=1;
                      [1] xhttps://192.168.1.1/index.php;
                      [2] x393x873x24;
                      [3] xhttps://192.168.1.1/test.html;
                      )
                     */
                    $aRequestArray = explode(";", $aSplittedString[7]);

                } else {
                    $sReferalFlag = "ordinary";
                    $aRequestArray = $aMatches;
                }

                $sDatePattern = $this->fVariablesGet('date_pattern');
                (strlen($sDatePattern) > 0) ? "" : $sDatePattern = "";

                if (preg_match("#" . $sDatePattern . "#", $sStr)) {

                    $sIP = str_replace("\"", "", $aSplittedString[0]);
                    $sIP = str_replace(["\r", "\n"], "", $sIP);

                    // Validate IP
                    $sValidateIPFlag=false;
                    if (!filter_var($sIP, FILTER_VALIDATE_IP)) {
                       $sIP_=trim($aSplittedString[1]);
                       if (filter_var($sIP_, FILTER_VALIDATE_IP)) {
                           $sIP=$sIP_;
                           $sValidateIPFlag=true;
                       }
                    } else {
                        $sValidateIPFlag=true;
                    }
                    
                    if ($sValidateIPFlag) {

                	$aUniqueIP[$sIP] = $sIP;
                	$aIPCount[$sIP]++;

                        $sCodePos = strpos($sStr, $aMatches[1][0]);
                        if ($sCodePos > 0) {
                            $sStrLength = strlen($aMatches[1][0]) + 1;
                            $sPageStatus = trim(substr($sStr, ($sCodePos + $sStrLength), 5));
                        } else {
                            $sPageStatus = 0;
                        }

                        // Expression in brackets (OS type and version)
                        // (Linux; arm_64; Android 13; 21081111RG)
                        preg_match_all('/\((.*?)\)/', $aMatches[1][2], $aOsTypeAndVersion);

                        // Module router
                        foreach ($this->fVariablesGet('modules_to_parse') as $sModules) {
                            switch ($sModules) {
                                case 'module_browsers_names':
                                case 'module_browsers_versions':
                                    // Browser versions
                                    $aMethodToExecute['fModuleBrowsers'] = 1;
                                    break;
                                case 'module_display_resolutions':
                                    // Display resolutions
                                    $aMethodToExecute['fModuleDisplayResolutions'] = 1;
                                    break;
                                case 'module_all_referal_links':
                                case 'module_all_referal_links_exclude_known':
                                case 'module_all_requests':
                                case 'module_search_engines':
                                case 'module_search_engines_google':
                                case 'module_social_networks':
                                case 'module_status_404_requests':
                                case 'module_status_200_requests':
                                    // Referal links + Social networks + Search engines
                                    $aMethodToExecute['fModuleReferalLinks'] = 1;
                                    break;
                                case 'module_device_type':
                                case 'module_iphone_version':
                                case 'module_ipad_version':
                                case 'module_macintosh_version':
                                case 'module_android_version':
                                case 'module_windows_version':
                                    $aMethodToExecute['fModuleDeviceType'] = 1;
                                    break;
                                case 'module_os_type':
                                case 'module_os_info':
                                    // OS info and type
                                    $aMethodToExecute['fModuleOSTypeAndVersions'] = 1;
                                    break;
                                case 'module_cities':
                                case 'module_countriesISO':
                                case 'module_countries':
                                    // Cities and Countries
                                    $aMethodToExecute['fModuleCitiesAndCountries'] = 1;
                                    break;
                                case 'module_10min_online_users_count':
                                    // 10min online users count
                                    $aMethodToExecute['fOnlineUsers10min'] = 1;
                                    break;
                            }
                        } //foreach modules_to_parse
                        
                        foreach($aMethodToExecute as $sMethod => $sValue) {
                            switch ($sMethod) {
                                case 'fModuleBrowsers':
                                    $this->fModuleBrowsers($sStr, $sIP);
                                    break;                                
                                case 'fModuleDisplayResolutions':
                                    $this->fModuleDisplayResolutions($aRequestArray, $sIP);
                                    break;
                                case 'fModuleReferalLinks':
                                    $this->fModuleReferalLinks($aRequestArray, $sReferalFlag, $sPageStatus, $sIP);
                                    break;
                                case 'fModuleDeviceType':
                                    $this->fModuleDeviceType($aOsTypeAndVersion[1][0], $sIP);
                                    break;
                                case 'fModuleOSTypeAndVersions':
                                    $this->fModuleOSTypeAndVersions($aOsTypeAndVersion[1][0], $sIP);
                                    break;
                                case 'fModuleCitiesAndCountries':
                                    $this->fModuleCitiesAndCountries($sIP);
                                    break;
                                case 'fOnlineUsers10min':
                                    $aDateTime[] = $this->fOnlineUsers10min($sStr, $sIP);
                                    break;
                            } // Switch
                        } // Foreach
                    } // Validate IP
                } // Preg_match
                $sCount++;
                if ($this->fVariablesGet('show_module_counter')) {
                    print $sCount . " of " . $sWebLogCount . "\n";
                }
            } //foreach aWebLog

            foreach ($this->fVariablesGet('modules_to_parse') as $sModules) {
                switch ($sModules) {
                    case 'module_ip_top_100':
                        (array)$aTOP100IP = array_slice((array)$aIPCount, 0, $this->aPatternArrayToSlice[$sModules]);
                        is_countable($aTOP100IP) ? arsort($aTOP100IP) : "";
                        $aModulesResult[$sModules] = $aTOP100IP;
                        $this->fVariablesSet('module_ip_top_100', $aModulesResult[$sModules]);
                        break;
                    case 'module_ip_unique_count':
                        $aModulesResult[$sModules] = count((array)$aUniqueIP);
                        $this->fVariablesSet('module_ip_unique_count', $aModulesResult[$sModules]);
                        break;
                    case 'module_10min_online_users_count':
                        $aPeriodEvery10Min = $this->fVariablesGet('10min_period');
                        foreach ($aDateTime as $sKey => $aValTimeStamp) {
                            foreach ($aValTimeStamp as $sIP_TimeStamp => $aCountTimeStamp) {
                                foreach($aCountTimeStamp as $sIP_Number => $sIP_Count) {
                                    $aDateTimeNew[$sIP_TimeStamp][$sIP_Number]=1;
                                }
                            }
                        }
                        foreach ($aPeriodEvery10Min as $sKey => $aVal) {
                            $aOnlineUsersPer10min[$sKey] = count((array)$aDateTimeNew[$sKey]);
                        }
                        is_countable($aOnlineUsersPer10min) ? ksort($aOnlineUsersPer10min) : "";
                        $this->fVariablesUnset($sModules);
                        $this->fVariablesSet($sModules, $aOnlineUsersPer10min);
                        break;
                    default:
                        $this->fVariablesSet($sModules."_raw_data", $this->fVariablesGet($sModules));
                        $aModulesResult[$sModules] = $this->fCalcSumArray($this->fVariablesGet($sModules));
                        is_countable($aModulesResult[$sModules]) ? arsort($aModulesResult[$sModules]) : "";
                        $this->fVariablesUnset($sModules);
                        $this->fVariablesSet($sModules, $aModulesResult[$sModules]);
                        break;
                }

                if ($this->fVariablesGet('show_module_output')) {
                    print "--------------------------------------\n";
                    print $sModules . ": \n";
                    print "--------------------------------------\n";
                    print_r($aModulesResult[$sModules]) . "\n";
                }
                
            }
            // insert to SQLite
        } // modules count >0
    } // init

    /**
     * Collect information about browsers
     * 
     * @param string $sStr
     * @param string $sIP
     */    
    
    public function fModuleBrowsers($sStr, $sIP) {
        $aSplittedString = explode(" ", $sStr);
        for ($l = 0; $l < count((array)$aSplittedString); $l++) {
            // Browsers names count
            foreach ($this->aPatternBrowsers as $sBrowserName => $sBrowserName) {
                $sBrowserStringClean=str_replace("\"", "", trim($aSplittedString[$l]));
                if (preg_match("/" . $sBrowserName . "/", $sBrowserStringClean)) {
                    // Only browser name
                    $this->fVariablesSet('module_browsers_names', $sBrowserName, $sIP);
                    $this->fVariablesSet('module_browsers_versions', $sBrowserStringClean, $sIP);
                }
            }
        }
    }    
    
    /**
     * Collect information about display resolutions
     * 
     * @param array $aRequestArray
     * @param string $sIP
     */
    
    private function fModuleDisplayResolutions($aRequestArray, $sIP) {
        $sDisplayCount = substr($aRequestArray[2], 1);
        if (preg_match("#^\d{1,}\*\d{1,}\*\d{1,}$#", $sDisplayCount)) {
            $this->fVariablesSet('module_display_resolutions', $sDisplayCount, $sIP);
        }
    }
    
    /**
     * Collect information about referal links
     * 
     * @param array $aRequestArray
     * @param string $sReferalFlag
     * @param integer $sPageStatus
     * @param string $sIP
     */
    private function fModuleReferalLinks($aRequestArray, $sReferalFlag, $sPageStatus, $sIP) {

        if ($sReferalFlag === "ordinary") {
            /*
              $aRequestArray contains (for exmaple):

              [1] => Array
              (
              [0] => GET https://192.168.1.1/index.php HTTP/1.0
              [1] => https://192.168.1./test.html
              [2] => Mozilla/5.0 (Linux; arm_64; Android 13; 21081111RG) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 YaBrowser/23.7.5.95.00 SA/3 Mobile Safari/537.36
              [3] => 192.168.1.2
              )

             */
            // GET https://192.168.1.1/index.php HTTP/1.0 => https://192.168.1.1/index.php
            $sRequest = trim(substr($aRequestArray[1][0], 4, -8));
            $sReferalLink = urldecode($aRequestArray[1][1]);
        } else {
            /*
              $aRequestArray contains (for exmaple):

              Array
              (
              [0] /stat?s=1;
              [1] xhttps://192.168.1.1/index.php;
              [2] x393x873x24;
              [3] xhttps://192.168.1.1/test.html;
              )
             */

            $sReferalLink = urldecode(substr($aRequestArray[1], 1));
            $sRequest = urldecode(substr($aRequestArray[3], 1));
        }

        ($this->fVariablesGet('url_length')) > 0 ? $sUrlLength = $this->fVariablesGet('url_length') : $sUrlLength = 100;

        $sRequest = $this->fSecurity($sRequest, $sUrlLength, array(".", "-", "_", "?", "|", "$", "!",
            ",", "@", "#", "%", "^", "&", "*", "[", "]", "/", ":",
            "(", ")", "+", "=", "{", "}"));

        if ((int) $sPageStatus == 404) {
            $this->fVariablesSet('module_status_404_requests', substr($sRequest, 0, $sUrlLength), $sIP);
        }
        if ((int) $sPageStatus == 200) {
            $this->fVariablesSet('module_status_200_requests', substr($sRequest, 0, $sUrlLength), $sIP);
        }
        if (strlen($sRequest) > 3) {
            $this->fVariablesSet('module_all_requests', substr($sRequest, 0, $sUrlLength), $sIP);
        }
        if (strlen($sReferalLink) > 0) {
            $aReferalCount[$sIP] = substr($sReferalLink, 0, $sUrlLength);
            // All referal links (unique IP)
            $this->fVariablesSet('module_all_referal_links', substr($sReferalLink, 0, $sUrlLength), $sIP);
        }

        foreach ($aReferalCount as $sKey => $sVal) {
            unset($sSocialNetworksFlag, $sSearchEnginesFlag);
            if (strlen($sVal) > 1) {
                // Social networks only                    
                foreach ($this->aPatternSocialNetworks as $sSocialNetwork => $sSocialNetworkName) {
                    if (strripos($sVal, $sSocialNetwork) > 0) {
                        $this->fVariablesSet('module_social_networks', $sSocialNetworkName, $sKey);
                        $sSocialNetworksFlag = true;
                    }
                }
                // Exclude domains
                foreach ($this->aPatternExcludeDomains as $sExcludeDomain) {
                    if (strripos($sVal, $sExcludeDomain) > 0) {
                        $sExcludeDomainsFlag = true;
                    }
                }
                // Search engines only
                foreach ($this->aPatternSearchEngines as $sSearchEngine => $sSearchEngineName) {
                    if (strripos($sVal, $sSearchEngine) > 0) {
                        $this->fVariablesSet('module_search_engines', $sSearchEngineName, $sKey);
                        $sSearchEnginesFlag = true;
                    }
                }
                // Google only
                if (strripos($sVal, 'google') > 0) {
                    $aGoogleReferall[$sVal] = $sVal;
                    $this->fVariablesSet('module_search_engines_google', $sVal, $sKey);
                    $sSearchEnginesFlag = true;
                }
            }
            if ($sSearchEnginesFlag !== true && $sSocialNetworksFlag !== true && $sExcludeDomainsFlag !== true) {
                $this->fVariablesSet('module_all_referal_links_exclude_known', $sVal, $sKey);
            }
        }
    }
    
    /**
     * Collect information about Os type and versions
     * 
     * @param string $sStr
     * @param string $sIP
     */
    private function fModuleOSTypeAndVersions($sStr, $sIP) {

        foreach ($this->aPatternOSVersions as $sOSVersion => $sSubStringLength) {
            $StrPos[$sOSVersion] = strripos($sStr, $sOSVersion);
            if ($StrPos[$sOSVersion] > 0) {
                $aSplittedString = explode(";", $sStr);
                $this->fVariablesSet('module_os_type', $aSplittedString[0], $sIP);
                $this->fVariablesSet('module_os_info', $aSplittedString[1], $sIP);
            }
        }
    }
    
    /**
     * Collect information about device type
     * 
     * @param string $sStr
     * @param string $sIP
     */
    private function fModuleDeviceType($sStr, $sIP) {

        $aSplittedString = explode(";", $sStr);
        for ($l = 0; $l < count((array)$aSplittedString); $l++) {
            foreach ($this->aPatternDeviceList as $sDevice => $sDeviceName) {
                if (preg_match("/" . $sDevice . "/", $aSplittedString[$l])) {
                    $this->fVariablesSet('module_device_type', $sDeviceName, $sIP);
                    if (preg_match("/iPhone/", $aSplittedString[$l])) {
                        $this->fVariablesSet('module_iphone_version', $sStr, $sIP);
                    }
                    if (preg_match("/iPad/", $aSplittedString[$l])) {
                        $this->fVariablesSet('module_ipad_version', $sStr, $sIP);
                    }
                    if (preg_match("/Macintosh/", $aSplittedString[$l])) {
                        $this->fVariablesSet('module_macintosh_version', $sStr, $sIP);
                    }
                    if (preg_match("/Android/", $aSplittedString[$l])) {
                        $this->fVariablesSet('module_android_version', $sStr, $sIP);
                    }
                    if (preg_match("/Windows/", $aSplittedString[$l])) {
                        foreach ($this->aPatternWindowsVersions as $sWinPattern => $sWinVersion) {
                            if (preg_match("#" . $sWinPattern . "#", $sStr)) {
                                $this->fVariablesSet('module_windows_version', $sWinVersion . " (" . $sStr . ")", $sIP);
                            }
                            $k++;
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Collect information about Cities and Countries
     * 
     * @param string $sIP
     */
    private function fModuleCitiesAndCountries($sIP) {
        
            try {

                $sRecord = $this->oGeoDBreader->city($sIP);
                $sCountryName = $this->fSecurity($sRecord->country->name, 150, array(".", "-", "_"));
                $sRegionName = $this->fSecurity($sRecord->mostSpecificSubdivision->name, 150, array(".", "-", "_"));
                $sCity = $this->fSecurity($sRecord->city->name, 150, array(".", "-", "_"));

                if (strlen($sRecord->country->name) > 1) {
                    $this->fVariablesSet('module_cities', $sCity, $sIP);
                    $this->fVariablesSet('module_countries', $sCountryName, $sIP);
                    $this->fVariablesSet('module_regions', $sRegionName, $sIP);
                    $this->fVariablesSet('module_countriesISO', $sRecord->country->isoCode, $sIP);
                }
                
                $this->fVariablesSet('geo_ip_array', $sIP);
            } catch (\Throwable $e) {
                print "! Error: " . $e->getMessage() . "\n";
            }
    }

    /**
     * Calculate unique array sum
     * 
     * @param array $aArray
     * @return array
     */
    public function fCalcSumArray($aArray) {
        foreach ($aArray as $sKey => $sVal) {
            if (strlen($sVal) > 1) {
                $aArrayNew[$sVal]++;
            }
        }
        
        return $aArrayNew;
    }
    
    /**
     * Manipulation with date and time
     * 
     * @param string $sDate
     */
    public function fDatePrepare($sDate) {
        $oFormattedDate = $this->oDateTime->createFromFormat('d.m.Y', $sDate);
        $sDateSQL = $oFormattedDate->format('Y'). "-" . $oFormattedDate->format('m') . "-" . $oFormattedDate->format('d') . " 00:00:00";
        $sMonthName = date('M', mktime(0, 0, 0, $oFormattedDate->format('m'), $oFormattedDate->format('d'), $aDataTime['year']));
        $sDatePlusOneDay = date('Y-m-d', mktime(0, 0, 0, $oFormattedDate->format('m'), $oFormattedDate->format('d') + 1, $oFormattedDate->format('Y')));
        $sTimeStampPlusDay = mktime(0, 0, 0, $oFormattedDate->format('m'), $oFormattedDate->format('d') + 1, $oFormattedDate->format('Y'));
  
        // Date pattern like 01/Mar/2000
        $this->fVariablesSet('date_pattern', $oFormattedDate->format('d') . "\/" . $sMonthName . "\/" . $oFormattedDate->format('Y'));
        $this->fVariablesSet('date_sql', $sDateSQL);
        $this->fVariablesSet('date_sql_short', substr($sDateSQL, 0, 10));
        $this->fVariablesSet('date_plus_one_day', $sDatePlusOneDay);
        $this->fVariablesSet('date_plus_one_day_timestamp', $sTimeStampPlusDay);
    }

    /**
     * Create 10 minut step array
     */
    private function fCreate10MinArray() {
        $sDate = substr($this->fVariablesGet('date_sql'), 0, 10);
        $s = "00";
        $sCycle = 0;
        for ($t = 0; $t <= 23; $t++) {
            $t < 10 ? $h = "0" . $t : $h = $t;
            for ($g = 0; $g <= 50; $g += 10) {
                $g < 10 ? $m = "00" : $m = $g;
                $sCalcTime = $h . ":" . $m . ":" . $s;
                $aCalcTimeStamp[$sCycle] = strtotime($sDate . " " . $sCalcTime);
                $aTimeCount10Min[$sCycle] = $h . $m;
                $aPeriodEvery10Min[$h . $m] = 0;
                $sCycle++;
            }
        }

        $this->fVariablesSet('10min_count', $aTimeCount10Min);
        $this->fVariablesSet('10min_period', $aPeriodEvery10Min);
        $this->fVariablesSet('10min_timestamp', $aCalcTimeStamp);
    }
    
    /**
     * Collect information about online users by 10 minutes period
     * 
     * @param string $sStr
     * @param string $sIP
     * @param string $aDateTime
     * @return array
     */
    private function fOnlineUsers10min($sStr, $sIP) {

        preg_match_all("/\\[(.*?)\\]/", $sStr, $aTimeDateMatches);
        $sTimeDate = str_replace(array("[", "]"), "", $aTimeDateMatches[0][0]);
        if (preg_match("#^[0-9][0-9]/[A-Z]([a-z]{2})/\d{4}+#", $sTimeDate)) {
            preg_match_all("#^([0-9][0-9])/[A-Z]([a-z]{2})/\d{4}#", $sTimeDate, $aDateMatches);

            $sDate = $aDateMatches[0][0];

            // Time
            preg_match_all("#:\d{2}:\d{2}:\d{2}+#", $sTimeDate, $aTimeMatches);
            $oFormattedDate = $this->oDateTime->createFromFormat('d/M/Y', $sDate);
            $sTime = substr($aTimeMatches[0][0], 1);
            $sMonth = date('n', strtotime($oFormattedDate->format('M')));

            $sDay = $oFormattedDate->format('d');
            $sYear = $oFormattedDate->format('Y');
            $sTimeStamp = strtotime($sYear . "-" . $sMonth . "-" . $sDay . " " . $sTime);
            $aTimeCount10Min = $this->fVariablesGet('10min_count');
            $aCalcTimeStamp = $this->fVariablesGet('10min_timestamp');
            for ($rg = 0; $rg < (count((array)$aCalcTimeStamp)); $rg++) {
                $sTimeTG = $aTimeCount10Min[$rg];
                if (($rg + 1) >= count((array)$aCalcTimeStamp)) {
                    $sCalcNextStamp = $this->fVariablesGet('date_plus_one_day_timestamp');
                } else {
                    $sCalcNextStamp = $aCalcTimeStamp[$rg + 1];
                }
                if (($sTimeStamp >= $aCalcTimeStamp[$rg]) &&
                        ($sTimeStamp <= $sCalcNextStamp)) {
                    $aResult[$sTimeTG][$sIP]++;
                }
            }
        }
        return $aResult;
    }

    /**
     * Get date by ID
     * 
     * @param integer $sID
     * @return date
     */
    public function fGetDateByID($sID) {

        $sQuery = "SELECT 
                              strftime(\"%d.%m.%Y\", sdate, 'localtime') as sdate
                       FROM
                              " . PHP_MWSLP_SQL_TABLE . "
                       WHERE 
                              id=" . $sID;
        try {
            $oResults = $this->oPDO->query($sQuery);
            foreach ($oResults->fetchAll() as $aRow) {
                $sDate = $aRow['sdate'];
            }
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
        }
        return $sDate;
    }
    
    /**
     * Get last ID
     * 
     * @param date $sDate
     * @return integer
     */
    public function fGetLastSQL_ID($sDate = '') {
        if (strlen($sDate) > 0) {
            $sQuery = "SELECT id FROM "
                    . "" . PHP_MWSLP_SQL_TABLE . " "
                    . "WHERE sdate like '%" . $sDate . "%'";
        } else {
            $sQuery = "SELECT id FROM "
                    . "" . PHP_MWSLP_SQL_TABLE . " "
                    . " ORDER BY id ASC";
        }
        try {
            $oResults = $this->oPDO->query($sQuery);
            foreach ($oResults->fetchAll() as $aRow) {
                $sLastSQL_ID = $aRow['id'];
            }
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
        }
        return $sLastSQL_ID;
    }
    
    /**
     * Insert data to SQL
     */
    public function fInsertToSQL() {
        $sQuery = "INSERT INTO
                   " . PHP_MWSLP_SQL_TABLE . " (
                    `browsers_names`,
                    `browsers_versions`,
                    `ip_uniq`,
                    `ip_top100`,
                    `search_sys`,
                    `query_top100`,
                    `referal_top100`,
                    `request_exclude_known`,
                    `os`,
                    `display_top100`,
                    `countries`,
                    `countries_id`,
                    `cities`,
                    `10min_traffic`,
                    `windows_version`,
                    `social`,
                    `sdate`)
                VALUES  (
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '" . $this->fVariablesGet('date_sql') . "'
                );";
    	    ($this->fVariablesGet('show_sql_query') == true) ? print $sQuery . "\n" : "";
        try {
            $this->oPDO->query($sQuery);
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
        }
    }
    /**
     * Update SQL table by ID
     * 
     * @param integer $sSQL_ID
     */
    public function fUpdateSQL($sSQL_ID) {

        foreach ($this->fVariablesGet('modules_to_parse') as $sModules) {
            if ($this->aPatternArrayToSlice[$sModules]) {
                $aValueToSQL = array_slice((array)$this->fVariablesGet($sModules), 0, $this->aPatternArrayToSlice[$sModules]);
            } else {
                $aValueToSQL = $this->fVariablesGet($sModules);
            }
            foreach ($this->aPatternSQLNames as $sSQL_Modules => $sSQL_Names) {
                if ($sSQL_Modules == $sModules) {
                    if ($this->aPatternArrayToJSON[$sModules]) {
                        //print "JSON: " . $JSON."\n";
                        $sQuery = "UPDATE "
                            . "" . PHP_MWSLP_SQL_TABLE . " "
                            . "SET `" . $sSQL_Names . "`= '" . json_encode($aValueToSQL, JSON_INVALID_UTF8_IGNORE) . "'"
                            . " WHERE id=" . $sSQL_ID;
                    } else {
                        $sQuery = "UPDATE "
                            . "" . PHP_MWSLP_SQL_TABLE . " "
                            . "SET `" . $sSQL_Names . "`= '" . $aValueToSQL . "'"
                            . " WHERE id=" . $sSQL_ID;
                    }
                    ($this->fVariablesGet('show_sql_query') == true) ? print $sQuery . "\n" : "";
                    try {
                        $this->oPDO->query($sQuery);
                    } catch (PDOException $e) {
                        print "Error: " . $e->getMessage();
                    }
                }
            }
        }
    }
    
    /**
     * Save data to file
     */
    public function fSaveToFile() {
        foreach ($this->fVariablesGet('modules_to_parse') as $sModules) {
            if ($this->aPatternArrayToSlice[$sModules]) {
                $aValueToSQL = array_slice((array)$this->fVariablesGet($sModules."_raw_data"), 0, $this->aPatternArrayToSlice[$sModules]);
            } else {
                $aValueToSQL = $this->fVariablesGet($sModules."_raw_data");
            }
            foreach ($this->aPatternSQLNames as $sSQL_Modules => $sSQL_Names) {
                if ($sSQL_Modules == $sModules) {
                    if ($this->aPatternArrayToJSON[$sModules]) {
                        if ($this->fVariablesGet('show_module_output')) {
                        }
                        file_put_contents(PHP_MWSLP_ROOT."/tmp/".$sModules."_".$this->fVariablesGet('out_file_name'), json_encode($aValueToSQL, JSON_INVALID_UTF8_IGNORE));
                    } else {
                        if ($this->fVariablesGet('show_module_output')) {
                        }
                        file_put_contents(PHP_MWSLP_ROOT."/tmp/".$sModules."_".$this->fVariablesGet('out_file_name'), $aValueToSQL);
                    }
                }
            }
        }
    }
}