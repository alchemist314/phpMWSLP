/*
MIT License
Copyright (c) 2023-2024 Grigoriy Golovanov
Contact e-mail: magentrum@gmail.com

This file is a part of phpMWSLP project.
For more information see README.md file.
*/


function fChartLimit(limit, prefix) {
  aNewArr = [];
  l = 0;
  aArr.forEach((k) => {
    aStr = k.toString().split(',');
    if (l <= limit) {
      aNewArr.push(k);
      if (document.getElementById(prefix + "_" + aStr[0])) {
        document.getElementById(prefix + "_" + aStr[0]).checked = true;
      }
    }
    l++;
  });
  return aNewArr;
}

function fArrayReverse(aArray, sParam = '', sPrefix = '') {
  aNewArr = [];
  var aValuesArr = [];
  var aNewSMA_Array = [];
  var aStr = [];
  aArr.forEach((k) => {
    aStr = k.toString().split(',');
    if (sParam.length > 0) {
      if (aStr[0] == 'x') {
        // Dates array
        aNewArr.push(k);
      } else {
        // Values array
        aValuesArr = aStr.slice();
        if (aStr[0] !== undefined && aStr[0] !== "") {
          if (document.getElementById("line_" + aStr[0])) {
            if (document.getElementById("line_" + aStr[0]).checked) {
              // Adding values
              aNewArr.push(k);
              // Adding simply moving average
              if (document.getElementById('frm_sma_enable').checked) {
                aValuesArr.shift();
                aValuesArr.pop();
                aNewSMA_Array = fCalculateSMA(aValuesArr, parseInt(document.getElementById('frm_sma_count').value))
                aNewSMA_Array.unshift(aStr[0] + "_sma");
                aNewArr.push(aNewSMA_Array);
              }
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
  sCheckLineChart = document.getElementById('frm_chart_line');
  sCheckBarChart = document.getElementById('frm_chart_bar');

  if ((sCheckLineChart.checked === true) && (sCheckBarChart.checked === true)) {
    alert("Please select Line chart or Bar chart, not both!");
  } else {
    document.getElementById('frm_main').submit();
  }
}

// Calculate simply moving average
function fCalculateSMA(aValues, sPeriod) {
  const aSMA = [];
  for (let i = 0; i < aValues.length; i++) {
    if (i < sPeriod - 1) {
      aSMA.push('null');
    } else {
      let sSum = 0;
      for (let j = (i - sPeriod + 1); j < (i + 1); j++) {
        sSum += parseInt(aValues[j]);
      }
      const sSumFloor = Math.round(sSum / sPeriod);
      aSMA.push(sSumFloor);
    }
  }
  return aSMA;
}
