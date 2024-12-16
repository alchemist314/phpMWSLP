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

use \PDO;

class cWebLogCommon {
    
    // SQLite object
    public $oPDO;
    // Variables storage
    public $aVariables = [];
    public $aModulesArray = array(
        'module_browsers_versions',
        'module_browsers_names',
        'module_ip_unique_count',
        'module_ip_top_100',
        'module_search_engines',
        'module_all_requests',
        'module_all_referal_links',
        'module_all_referal_links_exclude_known',
        'module_device_type',
        'module_display_resolutions',
        'module_countries',
        'module_countriesISO',
        'module_cities',
        'module_10min_online_users_count',
        'module_windows_version',
        'module_social_networks',
        'module_day_online_users_count'
    );
    
    /**
     * Variable storage
     * 
     * @param string $sVariableName
     * @param integer or string $sVariableValue
     * @param integer $sKey
     */
    public function fVariablesSet($sVariableName, $sVariableValue, $sKey = NULL) {
        if (count((array)$this->aVariables[$sVariableName]) > 0) {
            if (strlen($sKey) > 0) {
                $this->aVariables[$sVariableName][$sKey] = $sVariableValue;
            }
        } else {
            $this->aVariables[$sVariableName][] = $sVariableValue;
        }
    }
    
    /**
     * Return variable by name
     * 
     * @param string $sVariableName
     * @return string or integer
     */
    public function fVariablesGet($sVariableName) {
        if (count((array)$this->aVariables[$sVariableName]) > 1) {
            return $this->aVariables[$sVariableName];
        } else {
            return $this->aVariables[$sVariableName][0];
        }
    }
    /**
     * Erase variable by name
     * 
     * @param string $sVariableName
     */
    public function fVariablesUnset($sVariableName) {
        unset($this->aVariables[$sVariableName]);
    }

    public function fPDO() {
        try {
            $this->oPDO = new PDO("sqlite:".PHP_MWSLP_SQLITE_PATH);
            $this->oPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            define('PHP_MWSLP_SQL_TABLE',  '`'.PHP_MWSLP_PDO_TABLENAME.'`');
        } catch(PDOException $e) {
            print "Error: ".$e->getMessage();
        }
    }
    
    /** Sanitizer
     *  @param $sString string, $sLong int, $sExcep array
     *  @return sanitize string
     */
    
    public function fSecurity($sString, $sLong, $sExcep = "") {

        $sString = trim($sString);
        $sString = substr($sString, 0, $sLong);
        $sString = strip_tags($sString);
        $aCheckArray = array("<", ">", "?", "%", ";", "+", "-", "=", "(",
            ")", "*", "&", "#", "@", "`", "\"", "'", "|",
            ",", ".", "{", "}", "/", "^", "\\", "_");

        !is_array($sExcep) ? $sExcep = str_split($sExcep) : '';
        $aNewCheckArray = array_diff($aCheckArray, $sExcep);
        $sString = str_replace($aNewCheckArray, "", $sString);
        return $sString;
    }
}

?>
