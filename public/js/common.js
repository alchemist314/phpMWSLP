
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


function fGetTodayDate() {
    document.getElementById('frm_sdate').value = "";
    var sDateToday = new Date();
    var sDate = ("0" + sDateToday.getDate()).slice(-2) + "." + ("0" + (sDateToday.getMonth() + 1)).slice(-2) + "." + sDateToday.getFullYear();
    document.getElementById('frm_sdate').value = sDate;
}

function fSignByGRD(sGRD) {
    if ((sGRD >= 0) && (sGRD <= 30)) {
        sGRD = Math.abs(0 - sGRD);
        sSign = "Ari";
    }
    if ((sGRD > 30) && (sGRD <= 60)) {
        sGRD = Math.abs(30 - sGRD);
        sSign = "Tau";
    }
    if ((sGRD > 60) && (sGRD <= 90)) {
        sGRD = Math.abs(60 - sGRD);
        sSign = "Gem";
    }
    if ((sGRD > 90) && (sGRD <= 120)) {
        sGRD = Math.abs(90 - sGRD);
        sSign = "Cnc";
    }
    if ((sGRD > 120) && (sGRD <= 150)) {
        sGRD = Math.abs(120 - sGRD);
        sSign = "Leo";
    }
    if ((sGRD > 150) && (sGRD <= 180)) {
        sGRD = Math.abs(150 - sGRD);
        sSign = "Vir";
    }
    if ((sGRD > 180) && (sGRD <= 210)) {
        sGRD = Math.abs(180 - sGRD);
        sSign = "Lib";
    }
    if ((sGRD > 210) && (sGRD <= 240)) {
        sGRD = Math.abs(210 - sGRD);
        sSign = "Sco";
    }
    if ((sGRD > 240) && (sGRD <= 270)) {
        sGRD = Math.abs(240 - sGRD);
        sSign = "Sgr";
    }
    if ((sGRD > 270) && (sGRD <= 300)) {
        sGRD = Math.abs(270 - sGRD);
        sSign = "Cap";
    }
    if ((sGRD > 300) && (sGRD <= 330)) {
        sGRD = Math.abs(300 - sGRD);
        sSign = "Aqr";
    }
    if ((sGRD > 330) && (sGRD <= 360)) {
        sGRD = Math.abs(330 - sGRD);
        sSign = "Psc";
    }
    return sSign;
}
