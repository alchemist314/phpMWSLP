/*
MIT License
Copyright (c) Grigoriy Golovanov
Contact e-mail: magentrum@gmail.com

This file is a part of phpMWSLP project. 
For more information see README.md file.
*/


    function fChartLimit(limit, prefix) {
	aNewArr=[];
	l=0;
	aArr.forEach((k) => {
    	    aStr=k.toString().split(',');
	    if (l<=limit) {
		aNewArr.push(k);
    		if (document.getElementById(prefix+"_"+aStr[0])) {
            	    document.getElementById(prefix+"_"+aStr[0]).checked=true;
    		}
	    }
	    l++;
	});
	return aNewArr;
    }

    function fArrayReverse(aArray, sParam='', sPrefix='') {
	aNewArr=[];
	var aStr="";
	aArr.forEach((k) => {
    	    aStr=k.toString().split(',');
	    if (sParam.length>0) {
		if (aStr[0]=='x') {
		    aNewArr.push(k);
		}
		if (aStr[0]!==undefined) {
		    if (aStr[0]!=="") {
        		if (document.getElementById(sPrefix+"_"+aStr[0])) {
        		    if (document.getElementById(sPrefix+"_"+aStr[0]).checked) {
    				aNewArr.push(k);
    			    }
    			}
        	    }
        	}
	    } else {
		aNewArr.push(k);
	    }
	});
	return aNewArr;
    }

    function fFormSubmit() {
	sCheckLineChart=document.getElementById('frm_chart_line');
	sCheckBarChart=document.getElementById('frm_chart_bar');

	if ((sCheckLineChart.checked===true) && (sCheckBarChart.checked===true)) {
	    alert("Please select Line chart or Bar chart, not both!");
	} else {
	    document.getElementById('frm_main').submit();
	}
    }
