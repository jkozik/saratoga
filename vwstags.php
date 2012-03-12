<?php
/*
File: VWStags.htx

Purpose: load VWS variables into a $WX[] array for use with the Canada/World/USA template sets

Instructions:
Save this page as VWStags.htx and place in your c:\vws\templates directory

Use the VWS, Internet, HTML Settings panel and place a new entry to process as:

c:\vws\template\VWStags.htx               c:\vws\root\VWStags.php

then use the VWS, Internet, FTP Send(upload) File panel to  have

c:\vws\root\VWStags.php                   /

so that the processed file VWStags.php is uploaded to your website.

Author: Ken True - webmaster@saratoga-weather.org

(created by gen-VWStags.php - V1.04 - 13-Feb-2011)

These tags generated on 2011-02-13 14:02:44 GMT
From tags.txt updated 2011-02-12 00:02:41 GMT

*/
// --------------------------------------------------------------------------

// allow viewing of generated source

if (isset($_REQUEST["sce"]) and strtolower($_REQUEST["sce"]) == "view" ) {
//--self downloader --
$filenameReal = __FILE__;
$download_size = filesize($filenameReal);
header("Pragma: public");
header("Cache-Control: private");
header("Cache-Control: no-cache, must-revalidate");
header("Content-type: text/plain");
header("Accept-Ranges: bytes");
header("Content-Length: $download_size");
header("Connection: close");

readfile($filenameReal);
exit;
}
$WXsoftware = 'VWS';
$defsFile = 'VWS-defs.php';  // filename with $varnames = $WX['VWS-varnames']; equivalents

// note the embedded space in the first field.. it's there to prevent VWS from substituting
// a value for the name of the variable.  D'Oh.. Apparently, whenever it finds the string
// of characters matching one of it's variable names, it does a substitution, even without
// the surrounding caret ( ) characters.
$rawdatalines = <<<END_OF_RAW_DATA_LINES
v xv001|SSW:|:
v hi001|247:|:
v ht001|7:43am:|:
v lo001|87:|:
v lt001|7:44am:|:
v va001|200:|:
v da001|0:|:
v ma001|0:|:
v ya001|0:|:
v vr001|5.3:|:
v rh001|52.1:|:
v rt001|7:43am:|:
v rl001|-107.9:|:
v rs001|7:44am:|:
v mh001|360:|:
v md001|3/2/12:|:
v ml001|1:|:
v me001|3/2/12:|:
v yh001|360:|:
v yd001|1/1/12:|:
v yl001|0:|:
v ye001|1/9/12:|:
v zh001|324:|:
v zt001|10:57pm:|:
v zl001|35:|:
v zs001|9:32pm:|:
v hv001|200:|:
v dv001|-16:|:
u ni001|&deg;:|:
v xv002|3:|:
v hi002|21:|:
v ht002|12:40am:|:
v lo002|2:|:
v lt002|7:33am:|:
v va002|6:|:
v da002|5:|:
v ma002|6:|:
v ya002|4:|:
v vr002|-3.1:|:
v rh002|6.9:|:
v rt002|7:37am:|:
v rl002|-4.1:|:
v rs002|7:33am:|:
v mh002|36:|:
v md002|3/7/12:|:
v ml002|0:|:
v me002|3/1/12:|:
v yh002|36:|:
v yd002|3/7/12:|:
v yl002|0:|:
v ye002|1/1/12:|:
v zh002|24:|:
v zt002|11:50pm:|:
v zl002|0:|:
v zs002|12:01am:|:
v hv002|4:|:
v dv002|-1:|:
u ni002|mph:|:
v xv003|11:|:
v hi003|21:|:
v ht003|12:40am:|:
v lo003|6:|:
v lt003|7:31am:|:
v va003|14:|:
v da003|15:|:
v ma003|14:|:
v ya003|10:|:
v vr003|-3.3:|:
v rh003|4.7:|:
v rt003|6:50am:|:
v rl003|-9.2:|:
v rs003|7:31am:|:
v mh003|36:|:
v md003|3/7/12:|:
v ml003|0:|:
v me003|3/1/12:|:
v yh003|36:|:
v yd003|3/7/12:|:
v yl003|0:|:
v ye003|1/3/12:|:
v zh003|24:|:
v zt003|11:50pm:|:
v zl003|0:|:
v zs003|6:00am:|:
v hv003|6:|:
v dv003|5:|:
u ni003|mph:|:
v xv004|39:|:
v hi004|39:|:
v ht004|7:20am:|:
v lo004|36:|:
v lt004|12:00am:|:
v va004|38:|:
v da004|37:|:
v ma004|33:|:
v ya004|32:|:
v vr004|0.7:|:
v rh004|0.9:|:
v rt004|7:20am:|:
v rl004|-0.3:|:
v rs004|12:50am:|:
v mh004|40:|:
v md004|3/8/12:|:
v ml004|27:|:
v me004|3/9/12:|:
v yh004|42:|:
v yd004|2/24/12:|:
v yl004|24:|:
v ye004|1/19/12:|:
v zh004|36:|:
v zt004|9:08pm:|:
v zl004|31:|:
v zs004|8:30am:|:
v hv004|32:|:
v dv004|7:|:
u ni004|%:|:
v xv005|97:|:
v hi005|97:|:
v ht005|4:20am:|:
v lo005|65:|:
v lt005|12:40am:|:
v va005|97:|:
v da005|91:|:
v ma005|64:|:
v ya005|74:|:
v vr005|0.1:|:
v rh005|16.6:|:
v rt005|1:40am:|:
v rl005|0.1:|:
v rs005|7:47am:|:
v mh005|97:|:
v md005|3/12/12:|:
v ml005|20:|:
v me005|3/11/12:|:
v yh005|100:|:
v yd005|1/23/12:|:
v yl005|20:|:
v ye005|3/11/12:|:
v zh005|71:|:
v zt005|6:33am:|:
v zl005|20:|:
v zs005|3:09pm:|:
v hv005|70:|:
v dv005|27:|:
u ni005|%:|:
v xv006|73.1:|:
v hi006|73.1:|:
v ht006|12:00am:|:
v lo006|71.4:|:
v lt006|3:50am:|:
v va006|72.7:|:
v da006|72.3:|:
v ma006|71.5:|:
v ya006|71.1:|:
v vr006|0.42:|:
v rh006|0.52:|:
v rt006|6:40am:|:
v rl006|-0.51:|:
v rs006|2:40am:|:
v mh006|75.6:|:
v md006|3/6/12:|:
v ml006|67.4:|:
v me006|3/5/12:|:
v yh006|75.6:|:
v yd006|3/6/12:|:
v yl006|65.6:|:
v ye006|1/20/12:|:
v zh006|73.5:|:
v zt006|9:57pm:|:
v zl006|70.9:|:
v zs006|9:55am:|:
v hv006|72.3:|:
v dv006|0.8:|:
u ni006|&deg;F:|:
v xv007|56.0:|:
v hi007|56.0:|:
v ht007|7:43am:|:
v lo007|51.0:|:
v lt007|2:20am:|:
v va007|55.2:|:
v da007|53.7:|:
v ma007|40.5:|:
v ya007|32.3:|:
v vr007|0.84:|:
v rh007|0.96:|:
v rt007|4:30am:|:
v rl007|-2.63:|:
v rs007|1:30am:|:
v mh007|68.6:|:
v md007|3/11/12:|:
v ml007|18.4:|:
v me007|3/5/12:|:
v yh007|68.6:|:
v yd007|3/11/12:|:
v yl007|2.8:|:
v ye007|1/20/12:|:
v zh007|68.6:|:
v zt007|2:59pm:|:
v zl007|35.5:|:
v zs007|6:54am:|:
v hv007|36.2:|:
v dv007|19.8:|:
u ni007|&deg;F:|:
v xv008|29.08:|:
v hi008|29.23:|:
v ht008|12:00am:|:
v lo008|29.08:|:
v lt008|7:10am:|:
v va008|29.09:|:
v da008|29.14:|:
v ma008|29.23:|:
v ya008|29.26:|:
v vr008|-0.006:|:
v rh008|-0.003:|:
v rt008|7:33am:|:
v rl008|-0.032:|:
v rs008|3:30am:|:
v mh008|29.76:|:
v md008|3/9/12:|:
v ml008|28.50:|:
v me008|3/2/12:|:
v yh008|29.76:|:
v yd008|3/9/12:|:
v yl008|28.50:|:
v ye008|3/2/12:|:
v zh008|29.49:|:
v zt008|9:38am:|:
v zl008|29.24:|:
v zs008|11:50pm:|:
v hv008|29.47:|:
v dv008|-0.39:|:
u ni008|in:|:
v xv009|3.35:|:
v hi009|3.35:|:
v ht009|7:31am:|:
v lo009|3.06:|:
v lt009|12:00am:|:
v va009|3.34:|:
v da009|3.24:|:
v ma009|2.90:|:
v ya009|1.34:|:
v vr009|0.014:|:
v rh009|0.081:|:
v rt009|3:40am:|:
v rl009|0.003:|:
v rs009|1:00am:|:
v mh009|3.35:|:
v md009|3/12/12:|:
v ml009|2.69:|:
v me009|3/1/12:|:
v yh009|3.35:|:
v yd009|3/12/12:|:
v yl009|0.01:|:
v ye009|1/1/12:|:
v zh009|3.05:|:
v zt009|12:00am:|:
v zl009|3.05:|:
v zs009|12:00am:|:
v hv009|3.05:|:
v dv009|0.30:|:
u ni009|in:|:
v xv010|0.0:|:
v hi010|0.0:|:
v ht010|12:00am:|:
v lo010|0.0:|:
v lt010|12:00am:|:
v va010|0.0:|:
v da010|0.0:|:
v ma010|0.0:|:
v ya010|0.0:|:
v vr010|0.00:|:
v rh010|0.00:|:
v rt010|12:00am:|:
v rl010|0.00:|:
v rs010|12:00am:|:
v mh010|0.0:|:
v md010|3/1/12:|:
v ml010|0.0:|:
v me010|3/1/12:|:
v yh010|0.0:|:
v yd010|1/1/12:|:
v yl010|0.0:|:
v ye010|1/1/12:|:
v zh010|0.0:|:
v zt010|12:00am:|:
v zl010|0.0:|:
v zs010|12:00am:|:
v hv010|0.0:|:
v dv010|0.0:|:
u ni010|&deg;F:|:
v xv011|0:|:
v hi011|0:|:
v ht011|12:00am:|:
v lo011|0:|:
v lt011|12:00am:|:
v va011|0:|:
v da011|0:|:
v ma011|0:|:
v ya011|0:|:
v vr011|0.0:|:
v rh011|0.0:|:
v rt011|12:00am:|:
v rl011|0.0:|:
v rs011|12:00am:|:
v mh011|0:|:
v md011|3/1/12:|:
v ml011|0:|:
v me011|3/1/12:|:
v yh011|0:|:
v yd011|1/1/12:|:
v yl011|0:|:
v ye011|1/1/12:|:
v zh011|0:|:
v zt011|12:00am:|:
v zl011|0:|:
v zs011|12:00am:|:
v hv011|0:|:
v dv011|0:|:
u ni011|%:|:
v xv012|0.0:|:
v hi012|0.0:|:
v ht012|12:00am:|:
v lo012|0.0:|:
v lt012|12:00am:|:
v va012|0.0:|:
v da012|0.0:|:
v ma012|0.0:|:
v ya012|0.0:|:
v vr012|0.00:|:
v rh012|0.00:|:
v rt012|12:00am:|:
v rl012|0.00:|:
v rs012|12:00am:|:
v mh012|0.0:|:
v md012|3/1/12:|:
v ml012|0.0:|:
v me012|3/1/12:|:
v yh012|0.0:|:
v yd012|1/1/12:|:
v yl012|0.0:|:
v ye012|1/1/12:|:
v zh012|0.0:|:
v zt012|12:00am:|:
v zl012|0.0:|:
v zs012|12:00am:|:
v hv012|0.0:|:
v dv012|0.0:|:
u ni012|&deg;F:|:
v xv013|0:|:
v hi013|0:|:
v ht013|12:00am:|:
v lo013|0:|:
v lt013|12:00am:|:
v va013|0:|:
v da013|0:|:
v ma013|0:|:
v ya013|0:|:
v vr013|0.0:|:
v rh013|0.0:|:
v rt013|12:00am:|:
v rl013|0.0:|:
v rs013|12:00am:|:
v mh013|0:|:
v md013|3/1/12:|:
v ml013|0:|:
v me013|3/1/12:|:
v yh013|0:|:
v yd013|1/1/12:|:
v yl013|0:|:
v ye013|1/1/12:|:
v zh013|0:|:
v zt013|12:00am:|:
v zl013|0:|:
v zs013|12:00am:|:
v hv013|0:|:
v dv013|0:|:
u ni013|%:|:
v xv014|0.0:|:
v hi014|0.0:|:
v ht014|12:00am:|:
v lo014|0.0:|:
v lt014|12:00am:|:
v va014|0.0:|:
v da014|0.0:|:
v ma014|0.0:|:
v ya014|0.0:|:
v vr014|0.00:|:
v rh014|0.00:|:
v rt014|12:00am:|:
v rl014|0.00:|:
v rs014|12:00am:|:
v mh014|0.0:|:
v md014|3/1/12:|:
v ml014|0.0:|:
v me014|3/1/12:|:
v yh014|0.0:|:
v yd014|1/1/12:|:
v yl014|0.0:|:
v ye014|1/1/12:|:
v zh014|0.0:|:
v zt014|12:00am:|:
v zl014|0.0:|:
v zs014|12:00am:|:
v hv014|0.0:|:
v dv014|0.0:|:
u ni014|&deg;F:|:
v xv015|0:|:
v hi015|0:|:
v ht015|12:00am:|:
v lo015|0:|:
v lt015|12:00am:|:
v va015|0:|:
v da015|0:|:
v ma015|0:|:
v ya015|0:|:
v vr015|0.0:|:
v rh015|0.0:|:
v rt015|12:00am:|:
v rl015|0.0:|:
v rs015|12:00am:|:
v mh015|0:|:
v md015|3/1/12:|:
v ml015|0:|:
v me015|3/1/12:|:
v yh015|0:|:
v yd015|1/1/12:|:
v yl015|0:|:
v ye015|1/1/12:|:
v zh015|0:|:
v zt015|12:00am:|:
v zl015|0:|:
v zs015|12:00am:|:
v hv015|0:|:
v dv015|0:|:
u ni015|%:|:
v xv016|0.00:|:
v hi016|0.00:|:
v ht016|12:00am:|:
v lo016|0.00:|:
v lt016|12:10am:|:
v va016|0.00:|:
v da016|0.00:|:
v ma016|0.04:|:
v ya016|0.02:|:
v vr016|0.002:|:
v rh016|0.003:|:
v rt016|7:31am:|:
v rl016|-0.000:|:
v rs016|1:10am:|:
v mh016|0.18:|:
v md016|3/6/12:|:
v ml016|0.00:|:
v me016|3/1/12:|:
v yh016|0.18:|:
v yd016|3/6/12:|:
v yl016|0.00:|:
v ye016|1/1/12:|:
v zh016|0.15:|:
v zt016|12:00am:|:
v zl016|0.00:|:
v zs016|12:00am:|:
v hv016|0.01:|:
v dv016|-0.00:|:
u ni016|in:|:
v xv017|0.0:|:
v hi017|0.0:|:
v ht017|12:00am:|:
v lo017|0.0:|:
v lt017|12:00am:|:
v va017|0.0:|:
v da017|0.0:|:
v ma017|0.4:|:
v ya017|0.2:|:
v vr017|0.00:|:
v rh017|0.00:|:
v rt017|12:00am:|:
v rl017|0.00:|:
v rs017|12:00am:|:
v mh017|3.5:|:
v md017|3/11/12:|:
v ml017|0.0:|:
v me017|3/1/12:|:
v yh017|3.5:|:
v yd017|3/11/12:|:
v yl017|0.0:|:
v ye017|1/1/12:|:
v zh017|3.5:|:
v zt017|12:57pm:|:
v zl017|0.0:|:
v zs017|12:00am:|:
v hv017|0.0:|:
v dv017|0.0:|:
u ni017|:|:
v xv018|7:|:
v hi018|9:|:
v ht018|7:44am:|:
v lo018|0:|:
v lt018|12:00am:|:
v va018|1:|:
v da018|0:|:
v ma018|125:|:
v ya018|77:|:
v vr018|6.4:|:
v rh018|8.7:|:
v rt018|7:44am:|:
v rl018|0.0:|:
v rs018|12:00am:|:
v mh018|947:|:
v md018|3/5/12:|:
v ml018|0:|:
v me018|3/1/12:|:
v yh018|969:|:
v yd018|2/27/12:|:
v yl018|0:|:
v ye018|1/1/12:|:
v zh018|921:|:
v zt018|12:58pm:|:
v zl018|0:|:
v zs018|12:00am:|:
v hv018|23:|:
v dv018|-16:|:
u ni018|W/sqm:|:
v xv019|56.0:|:
v hi019|56.0:|:
v ht019|7:44am:|:
v lo019|48.9:|:
v lt019|2:20am:|:
v va019|53.8:|:
v da019|52.4:|:
v ma019|37.4:|:
v ya019|29.0:|:
v vr019|2.20:|:
v rh019|2.38:|:
v rt019|7:33am:|:
v rl019|-2.81:|:
v rs019|1:50am:|:
v mh019|68.6:|:
v md019|3/11/12:|:
v ml019|9.2:|:
v me019|3/5/12:|:
v yh019|68.6:|:
v yd019|3/11/12:|:
v yl019|-10.7:|:
v ye019|1/19/12:|:
v zh019|68.6:|:
v zt019|2:59pm:|:
v zl019|30.6:|:
v zs019|7:40am:|:
v hv019|32.9:|:
v dv019|23.1:|:
u ni019|&deg;F:|:
v xv020|72.9:|:
v hi020|72.9:|:
v ht020|7:39am:|:
v lo020|71.1:|:
v lt020|3:50am:|:
v va020|72.5:|:
v da020|72.0:|:
v ma020|70.9:|:
v ya020|70.4:|:
v vr020|0.48:|:
v rh020|0.55:|:
v rt020|7:39am:|:
v rl020|-0.50:|:
v rs020|3:50am:|:
v mh020|75.4:|:
v md020|3/7/12:|:
v ml020|66.7:|:
v me020|3/5/12:|:
v yh020|75.4:|:
v yd020|3/7/12:|:
v yl020|64.5:|:
v ye020|1/20/12:|:
v zh020|73.1:|:
v zt020|9:57pm:|:
v zl020|70.2:|:
v zs020|9:55am:|:
v hv020|71.6:|:
v dv020|1.3:|:
u ni020|&deg;F:|:
v xv021|62.7:|:
v hi021|62.7:|:
v ht021|7:43am:|:
v lo021|57.0:|:
v lt021|1:50am:|:
v va021|61.8:|:
v da021|59.5:|:
v ma021|43.1:|:
v ya021|35.9:|:
v vr021|0.86:|:
v rh021|1.37:|:
v rt021|4:20am:|:
v rl021|-0.51:|:
v rs021|1:30am:|:
v mh021|67.6:|:
v md021|3/6/12:|:
v ml021|23.0:|:
v me021|3/5/12:|:
v yh021|67.6:|:
v yd021|3/6/12:|:
v yl021|4.2:|:
v ye021|1/19/12:|:
v zh021|67.3:|:
v zt021|2:59pm:|:
v zl021|38.6:|:
v zs021|6:47am:|:
v hv021|39.2:|:
v dv021|23.6:|:
u ni021|&deg;F:|:
v xv022|55.2:|:
v hi022|55.2:|:
v ht022|7:43am:|:
v lo022|43.7:|:
v lt022|12:40am:|:
v va022|54.3:|:
v da022|50.8:|:
v ma022|27.8:|:
v ya022|24.1:|:
v vr022|0.89:|:
v rh022|3.62:|:
v rt022|1:10am:|:
v rl022|0.55:|:
v rs022|12:40am:|:
v mh022|55.2:|:
v md022|3/12/12:|:
v ml022|3.1:|:
v me022|3/9/12:|:
v yh022|55.2:|:
v yd022|3/12/12:|:
v yl022|-12.0:|:
v ye022|1/19/12:|:
v zh022|42.3:|:
v zt022|11:50pm:|:
v zl022|25.3:|:
v zs022|3:09pm:|:
v hv022|27.4:|:
v dv022|27.8:|:
u ni022|&deg;F:|:
v xv023|29.89:|:
v hi023|30.04:|:
v ht023|12:00am:|:
v lo023|29.89:|:
v lt023|7:10am:|:
v va023|29.89:|:
v da023|29.94:|:
v ma023|30.04:|:
v ya023|30.07:|:
v vr023|-0.007:|:
v rh023|-0.003:|:
v rt023|7:33am:|:
v rl023|-0.032:|:
v rs023|3:30am:|:
v mh023|30.58:|:
v md023|3/9/12:|:
v ml023|29.28:|:
v me023|3/2/12:|:
v yh023|30.58:|:
v yd023|3/9/12:|:
v yl023|29.28:|:
v ye023|3/2/12:|:
v zh023|30.30:|:
v zt023|9:38am:|:
v zl023|30.05:|:
v zs023|11:50pm:|:
v hv023|30.28:|:
v dv023|-0.40:|:
u ni023|in:|:
v xv024|781:|:
u ni024|ft:|:
v xv025|209:|:
u ni025|ft:|:
v xv026|786:|:
u ni026|ft:|:
v xv027|59.0:|:
u ni027|&deg;F:|:
v xv028|0.44:|:
u ni028|in:|:
v xv121|0.29:|:
u ni121|in:|:
v xv122|0.01:|:
u ni122|in:|:
v xv123|0.30:|:
u ni123|in:|:
v xv124|0.000:|:
u ni124|in/hr:|:
v xv125|41:|:
u ni125|miles:|:
v xv126|3.6:|:
u ni126|&deg;F:|:
v xv127|0.0:|:
u ni127|&deg;F:|:
v xv128|19:|:
u ni128|:|:
v xv129|0.66:|:
u ni129|in:|:
v xv130|232.1:|:
u ni130|&deg;F:|:
v xv131|0.7:|:
u ni131|&deg;F:|:
v xv132|1319:|:
u ni132|miles:|:
v xv133|2082.5:|:
u ni133|&deg;F:|:
v xv134|0.7:|:
u ni134|&deg;F:|:
v xv135|6335:|:
u ni135|miles:|:
v st136|---:|:
u ni136|:|:
v st137|Cool:|:
u ni137|:|:
v st138|Increasing clouds with little temperature change:|:
u ni138|:|:
v st139|Steady:|:
u ni139|:|:
v st140|Steady:|:
u ni140|:|:
v st141|Light Air:|:
u ni141|:|:
v st142|3/12/12:|:
u ni142|:|:
v st143|7:47am:|:
u ni143|:|:
v st144|7:06am:|:
u ni144|:|:
v st145|6:54pm:|:
u ni145|:|:
v st146|---:|:
u ni146|:|:
v st147|9:24am:|:
u ni147|:|:
v st148|South South West:|:
u ni148|:|:
m oon_percent|78%:|:
m oon_day|20:|:
v ervws|V14.01:|:
w station|Davis Instruments Vantage Pro2:|:
w sdescription|NapervilleWeather.com:|:
w slocation|Naperville, IL:|:
w staturl|http://www.ambientweather.com/dawest.html:|:
w slong|-88.1200027:|:
w slat|41.7900009:|:
w orld_id|us:|:
t emp_color|53ff53:|:
t emp_color_hi|53ff53:|:
t emp_color_lo|53df53:|:
m tr001KSJC|mtr001KSJC:|:
m tr002KSJC|mtr002KSJC:|:
m tr003KSJC|mtr003KSJC:|:
m tr004KSJC|mtr004KSJC:|:
m tr005KSJC|mtr005KSJC:|:
m tr006KSJC|mtr006KSJC:|:
m tr007KSJC|mtr007KSJC:|:
m tr008KSJC|mtr008KSJC:|:
m tr009KSJC|mtr009KSJC:|:
m tr010KSJC|mtr010KSJC:|:
m tr011KSJC|mtr011KSJC:|:
m tr012KSJC|mtr012KSJC:|:
m tr013KSJC|mtr013KSJC:|:
m tr014KSJC|mtr014KSJC:|:
m tr015KSJC|mtr015KSJC:|:
m tr016KSJC|mtr016KSJC:|:
m tr017KSJC|mtr017KSJC:|:
a lmhi1|64:|:
a lmdatehi1|2006:|:
a lmlo1|10:|:
a lmdatelo1|1998:|:
a lmnormhi1|46:|:
a lmnormlo1|27:|:
a lmytd1|2.61:|:
a lmmtd1|0.49:|:
t emp_rec_hi1|b9ff53:|:
t emp_rec_lo1|ff53d3:|:
t emp_norm_hi1|53d3b9:|:
t emp_norm_lo1|7953ff:|:
c limate_cconds1|light rain
:|:
c limate_icon1|rain:|:
c limate_city1|Joliet:|:
c limate_state1|Illinois:|:
a lmhi2|0:|:
a lmdatehi2|0:|:
a lmlo2|0:|:
a lmdatelo2|0:|:
a lmnormhi2|0:|:
a lmnormlo2|0:|:
a lmytd2|0.00:|:
a lmmtd2|0.00:|:
t emp_rec_hi2|ffffff:|:
t emp_rec_lo2|ffffff:|:
t emp_norm_hi2|ffffff:|:
t emp_norm_lo2|ffffff:|:
c limate_cconds2|:|:
c limate_icon2|:|:
c limate_city2|:|:
c limate_state2|:|:
a lmhi3|0:|:
a lmdatehi3|0:|:
a lmlo3|0:|:
a lmdatelo3|0:|:
a lmnormhi3|0:|:
a lmnormlo3|0:|:
a lmytd3|0.00:|:
a lmmtd3|0.00:|:
t emp_rec_hi3|ffffff:|:
t emp_rec_lo3|ffffff:|:
t emp_norm_hi3|ffffff:|:
t emp_norm_lo3|ffffff:|:
c limate_cconds3|:|:
c limate_icon3|:|:
c limate_city3|:|:
c limate_state3|:|:
a lmhi4|0:|:
a lmdatehi4|0:|:
a lmlo4|0:|:
a lmdatelo4|0:|:
a lmnormhi4|0:|:
a lmnormlo4|0:|:
a lmytd4|0.00:|:
a lmmtd4|0.00:|:
t emp_rec_hi4|ffffff:|:
t emp_rec_lo4|ffffff:|:
t emp_norm_hi4|ffffff:|:
t emp_norm_lo4|ffffff:|:
c limate_cconds4|:|:
c limate_icon4|:|:
c limate_city4|:|:
c limate_state4|:|:
a lmhi5|0:|:
a lmdatehi5|0:|:
a lmlo5|0:|:
a lmdatelo5|0:|:
a lmnormhi5|0:|:
a lmnormlo5|0:|:
a lmytd5|0.00:|:
a lmmtd5|0.00:|:
t emp_rec_hi5|ffffff:|:
t emp_rec_lo5|ffffff:|:
t emp_norm_hi5|ffffff:|:
t emp_norm_lo5|ffffff:|:
c limate_cconds5|:|:
c limate_icon5|:|:
c limate_city5|:|:
c limate_state5|:|:
a lmhi6|0:|:
a lmdatehi6|0:|:
a lmlo6|0:|:
a lmdatelo6|0:|:
a lmnormhi6|0:|:
a lmnormlo6|0:|:
a lmytd6|0.00:|:
a lmmtd6|0.00:|:
t emp_rec_hi6|ffffff:|:
t emp_rec_lo6|ffffff:|:
t emp_norm_hi6|ffffff:|:
t emp_norm_lo6|ffffff:|:
c limate_cconds6|:|:
c limate_icon6|:|:
c limate_city6|:|:
c limate_state6|:|:
a lmhi7|0:|:
a lmdatehi7|0:|:
a lmlo7|0:|:
a lmdatelo7|0:|:
a lmnormhi7|0:|:
a lmnormlo7|0:|:
a lmytd7|0.00:|:
a lmmtd7|0.00:|:
t emp_rec_hi7|ffffff:|:
t emp_rec_lo7|ffffff:|:
t emp_norm_hi7|ffffff:|:
t emp_norm_lo7|ffffff:|:
c limate_cconds7|:|:
c limate_icon7|:|:
c limate_city7|:|:
c limate_state7|:|:
a lmhi8|0:|:
a lmdatehi8|0:|:
a lmlo8|0:|:
a lmdatelo8|0:|:
a lmnormhi8|0:|:
a lmnormlo8|0:|:
a lmytd8|0.00:|:
a lmmtd8|0.00:|:
t emp_rec_hi8|ffffff:|:
t emp_rec_lo8|ffffff:|:
t emp_norm_hi8|ffffff:|:
t emp_norm_lo8|ffffff:|:
c limate_cconds8|:|:
c limate_icon8|:|:
c limate_city8|:|:
c limate_state8|:|:
a lmhi9|0:|:
a lmdatehi9|0:|:
a lmlo9|0:|:
a lmdatelo9|0:|:
a lmnormhi9|0:|:
a lmnormlo9|0:|:
a lmytd9|0.00:|:
a lmmtd9|0.00:|:
t emp_rec_hi9|ffffff:|:
t emp_rec_lo9|ffffff:|:
t emp_norm_hi9|ffffff:|:
t emp_norm_lo9|ffffff:|:
c limate_cconds9|:|:
c limate_icon9|:|:
c limate_city9|:|:
c limate_state9|:|:
a lmhi10|0:|:
a lmdatehi10|0:|:
a lmlo10|0:|:
a lmdatelo10|0:|:
a lmnormhi10|0:|:
a lmnormlo10|0:|:
a lmytd10|0.00:|:
a lmmtd10|0.00:|:
t emp_rec_hi10|ffffff:|:
t emp_rec_lo10|ffffff:|:
t emp_norm_hi10|ffffff:|:
t emp_norm_lo10|ffffff:|:
c limate_cconds10|:|:
c limate_icon10|:|:
c limate_city10|:|:
c limate_state10|:|:
a lmhi11|0:|:
a lmdatehi11|0:|:
a lmlo11|0:|:
a lmdatelo11|0:|:
a lmnormhi11|0:|:
a lmnormlo11|0:|:
a lmytd11|0.00:|:
a lmmtd11|0.00:|:
t emp_rec_hi11|ffffff:|:
t emp_rec_lo11|ffffff:|:
t emp_norm_hi11|ffffff:|:
t emp_norm_lo11|ffffff:|:
c limate_cconds11|:|:
c limate_icon11|:|:
c limate_city11|:|:
c limate_state11|:|:
a lmhi12|0:|:
a lmdatehi12|0:|:
a lmlo12|0:|:
a lmdatelo12|0:|:
a lmnormhi12|0:|:
a lmnormlo12|0:|:
a lmytd12|0.00:|:
a lmmtd12|0.00:|:
t emp_rec_hi12|ffffff:|:
t emp_rec_lo12|ffffff:|:
t emp_norm_hi12|ffffff:|:
t emp_norm_lo12|ffffff:|:
c limate_cconds12|:|:
c limate_icon12|:|:
c limate_city12|:|:
c limate_state12|:|:
a lmhi13|0:|:
a lmdatehi13|0:|:
a lmlo13|0:|:
a lmdatelo13|0:|:
a lmnormhi13|0:|:
a lmnormlo13|0:|:
a lmytd13|0.00:|:
a lmmtd13|0.00:|:
t emp_rec_hi13|ffffff:|:
t emp_rec_lo13|ffffff:|:
t emp_norm_hi13|ffffff:|:
t emp_norm_lo13|ffffff:|:
c limate_cconds13|:|:
c limate_icon13|:|:
c limate_city13|:|:
c limate_state13|:|:
a lmhi14|0:|:
a lmdatehi14|0:|:
a lmlo14|0:|:
a lmdatelo14|0:|:
a lmnormhi14|0:|:
a lmnormlo14|0:|:
a lmytd14|0.00:|:
a lmmtd14|0.00:|:
t emp_rec_hi14|ffffff:|:
t emp_rec_lo14|ffffff:|:
t emp_norm_hi14|ffffff:|:
t emp_norm_lo14|ffffff:|:
c limate_cconds14|:|:
c limate_icon14|:|:
c limate_city14|:|:
c limate_state14|:|:
a lmhi15|0:|:
a lmdatehi15|0:|:
a lmlo15|0:|:
a lmdatelo15|0:|:
a lmnormhi15|0:|:
a lmnormlo15|0:|:
a lmytd15|0.00:|:
a lmmtd15|0.00:|:
t emp_rec_hi15|ffffff:|:
t emp_rec_lo15|ffffff:|:
t emp_norm_hi15|ffffff:|:
t emp_norm_lo15|ffffff:|:
c limate_cconds15|:|:
c limate_icon15|:|:
c limate_city15|:|:
c limate_state15|:|:
f orecast_nexrad1|LOT:|:
f orecast_country1|US:|:
f orecast_radregion1|a4:|:
f orecast_tzname1|America/Chicago:|:
f orecast_wmo1|:|:
f orecast_day1_1|Mon:|:
f orecast_hi1_1|68:|:
f orecast_lo1_1|45:|:
f orecast_conds1_1|T-storms:|:
f orecast_icon1_1|tstorms:|:
t emp_for_hi1_1|ffff53:|:
t emp_for_lo1_1|53d3d2:|:
f orecast_day1_2|Tue:|:
f orecast_hi1_2|66:|:
f orecast_lo1_2|48:|:
f orecast_conds1_2|Clear:|:
f orecast_icon1_2|clear:|:
t emp_for_hi1_2|ecff53:|:
t emp_for_lo1_2|53d386:|:
f orecast_day1_3|Wed:|:
f orecast_hi1_3|75:|:
f orecast_lo1_3|61:|:
f orecast_conds1_3|Partly Cloudy:|:
f orecast_icon1_3|partlycloudy:|:
t emp_for_hi1_3|ffff53:|:
t emp_for_lo1_3|6cff53:|:
f orecast_day1_4|Thu:|:
f orecast_hi1_4|73:|:
f orecast_lo1_4|54:|:
f orecast_conds1_4|Mostly Cloudy:|:
f orecast_icon1_4|mostlycloudy:|:
t emp_for_hi1_4|ffff53:|:
t emp_for_lo1_4|53ff53:|:
f orecast_day1_5|Fri:|:
f orecast_hi1_5|70:|:
f orecast_lo1_5|57:|:
f orecast_conds1_5|Chance T-storms:|:
f orecast_icon1_5|chancetstorms:|:
t emp_for_hi1_5|ffff53:|:
t emp_for_lo1_5|53ff53:|:
f orecast_nexrad2|LOT:|:
f orecast_country2|US:|:
f orecast_radregion2|a4:|:
f orecast_tzname2|America/Chicago:|:
f orecast_wmo2|:|:
f orecast_day2_1|Mon:|:
f orecast_hi2_1|68:|:
f orecast_lo2_1|45:|:
f orecast_conds2_1|T-storms:|:
f orecast_icon2_1|tstorms:|:
t emp_for_hi2_1|ffffff:|:
t emp_for_lo2_1|ffffff:|:
f orecast_day2_2|Tue:|:
f orecast_hi2_2|66:|:
f orecast_lo2_2|48:|:
f orecast_conds2_2|Clear:|:
f orecast_icon2_2|clear:|:
t emp_for_hi2_2|ffffff:|:
t emp_for_lo2_2|ffffff:|:
f orecast_day2_3|Wed:|:
f orecast_hi2_3|75:|:
f orecast_lo2_3|61:|:
f orecast_conds2_3|Partly Cloudy:|:
f orecast_icon2_3|partlycloudy:|:
t emp_for_hi2_3|ffffff:|:
t emp_for_lo2_3|ffffff:|:
f orecast_day2_4|Thu:|:
f orecast_hi2_4|73:|:
f orecast_lo2_4|54:|:
f orecast_conds2_4|Mostly Cloudy:|:
f orecast_icon2_4|mostlycloudy:|:
t emp_for_hi2_4|ffffff:|:
t emp_for_lo2_4|ffffff:|:
f orecast_day2_5|Fri:|:
f orecast_hi2_5|70:|:
f orecast_lo2_5|57:|:
f orecast_conds2_5|Chance T-storms:|:
f orecast_icon2_5|chancetstorms:|:
t emp_for_hi2_5|ffffff:|:
t emp_for_lo2_5|ffffff:|:
f orecast_nexrad3|LOT:|:
f orecast_country3|US:|:
f orecast_radregion3|a4:|:
f orecast_tzname3|America/Chicago:|:
f orecast_wmo3|:|:
f orecast_day3_1|Mon:|:
f orecast_hi3_1|68:|:
f orecast_lo3_1|45:|:
f orecast_conds3_1|T-storms:|:
f orecast_icon3_1|tstorms:|:
t emp_for_hi3_1|53d3d2:|:
t emp_for_lo3_1|ffffff:|:
f orecast_day3_2|Tue:|:
f orecast_hi3_2|66:|:
f orecast_lo3_2|48:|:
f orecast_conds3_2|Clear:|:
f orecast_icon3_2|clear:|:
t emp_for_hi3_2|53d386:|:
t emp_for_lo3_2|ffffff:|:
f orecast_day3_3|Wed:|:
f orecast_hi3_3|75:|:
f orecast_lo3_3|61:|:
f orecast_conds3_3|Partly Cloudy:|:
f orecast_icon3_3|partlycloudy:|:
t emp_for_hi3_3|6cff53:|:
t emp_for_lo3_3|ffffff:|:
f orecast_day3_4|Thu:|:
f orecast_hi3_4|73:|:
f orecast_lo3_4|54:|:
f orecast_conds3_4|Mostly Cloudy:|:
f orecast_icon3_4|mostlycloudy:|:
t emp_for_hi3_4|53ff53:|:
t emp_for_lo3_4|ffffff:|:
f orecast_day3_5|Fri:|:
f orecast_hi3_5|70:|:
f orecast_lo3_5|57:|:
f orecast_conds3_5|Chance T-storms:|:
f orecast_icon3_5|chancetstorms:|:
t emp_for_hi3_5|53ff53:|:
t emp_for_lo3_5|ffffff:|:
w arning_desc1|No Warning:|:
w arning_time1|:|:
w arning_desc2|No Warning:|:
w arning_time2|:|:
w arning_desc3|No Warning:|:
w arning_time3|:|:
w arning_desc4|No Warning:|:
w arning_time4|:|:
w arning_desc5|No Warning:|:
w arning_time5|:|:
w arning_desc6|No Warning:|:
w arning_time6|:|:
w arning_desc7|No Warning:|:
w arning_time7|:|:
w arning_desc8|No Warning:|:
w arning_time8|:|:
w arning_desc9|No Warning:|:
w arning_time9|:|:
w arning_desc10|No Warning:|:
w arning_time10|:|:
w arning_desc11|No Warning:|:
w arning_time11|:|:
w arning_desc12|No Warning:|:
w arning_time12|:|:
w arning_desc13|No Warning:|:
w arning_time13|:|:
w arning_desc14|No Warning:|:
w arning_time14|:|:
w arning_desc15|No Warning:|:
w arning_time15|:|:
m eso_stat1|KILNAPER15:|:
m eso_loc1|Naperville:|:
m eso_neigh1|Indian Hill Near Jefferson JHS:|:
m eso_url1|:|:
m eso_URLtext1|:|:
m eso_historyURL1|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER15:|:
m eso_lon1|-88.14:|:
m eso_lat1|41.80:|:
m eso_time1|03/12 07:30:45:|:
m eso_temp1|56.5:|:
m eso_dew1|56.0:|:
m eso_rh1|100:|:
m eso_dir1|SW:|:
m eso_wspeed1|4:|:
m eso_gust1|6:|:
m eso_barom1|29.06:|:
m eso_rrate1|0.00:|:
m eso_stat2|KILNAPER21:|:
m eso_loc2|Naperville:|:
m eso_neigh2|Saybrook:|:
m eso_url2|:|:
m eso_URLtext2|:|:
m eso_historyURL2|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER21:|:
m eso_lon2|-88.14:|:
m eso_lat2|41.79:|:
m eso_time2|03/12 07:30:03:|:
m eso_temp2|55.9:|:
m eso_dew2|55.0:|:
m eso_rh2|96:|:
m eso_dir2|SSE:|:
m eso_wspeed2|4:|:
m eso_gust2|12:|:
m eso_barom2|29.88:|:
m eso_rrate2|0.00:|:
m eso_stat3|KILNAPER19:|:
m eso_loc3|Naperville:|:
m eso_neigh3|Brookdale Lakes:|:
m eso_url3|:|:
m eso_URLtext3|:|:
m eso_historyURL3|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER19:|:
m eso_lon3|-88.20:|:
m eso_lat3|41.78:|:
m eso_time3|03/12 07:30:53:|:
m eso_temp3|55.6:|:
m eso_dew3|54.0:|:
m eso_rh3|95:|:
m eso_dir3|ENE:|:
m eso_wspeed3|2:|:
m eso_gust3|5:|:
m eso_barom3|29.85:|:
m eso_rrate3|0.02:|:
m eso_stat4|KILNAPER7:|:
m eso_loc4|Naperville:|:
m eso_neigh4|NapervilleWeather.com Near Ogden and Naper Blvd:|:
m eso_url4|http://NapervilleWeather.com:|:
m eso_URLtext4|NapervilleWeather.com Web Page:|:
m eso_historyURL4|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER7:|:
m eso_lon4|-88.12:|:
m eso_lat4|41.79:|:
m eso_time4|03/12 07:30:53:|:
m eso_temp4|55.7:|:
m eso_dew4|55.0:|:
m eso_rh4|97:|:
m eso_dir4|WSW:|:
m eso_wspeed4|7:|:
m eso_gust4|7:|:
m eso_barom4|29.89:|:
m eso_rrate4|0.01:|:
m eso_stat5|KILWARRE2:|:
m eso_loc5|Warrenville:|:
m eso_neigh5|Warrenville Neighborhood Weather:|:
m eso_url5|http://weather.binkerd.net:|:
m eso_URLtext5|Binkerd Weather:|:
m eso_historyURL5|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWARRE2:|:
m eso_lon5|-88.17:|:
m eso_lat5|41.82:|:
m eso_time5|03/12 07:30:48:|:
m eso_temp5|55.8:|:
m eso_dew5|55.0:|:
m eso_rh5|96:|:
m eso_dir5|SSW:|:
m eso_wspeed5|4:|:
m eso_gust5|13:|:
m eso_barom5|29.89:|:
m eso_rrate5|0.01:|:
m eso_stat6|KILNAPER9:|:
m eso_loc6|Naperville:|:
m eso_neigh6|75th St. /  Modaff Area:|:
m eso_url6|http://mistybeagle.com/wx/:|:
m eso_URLtext6|Misty The Weather Beagle:|:
m eso_historyURL6|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER9:|:
m eso_lon6|-88.14:|:
m eso_lat6|41.74:|:
m eso_time6|03/12 07:30:53:|:
m eso_temp6|56.1:|:
m eso_dew6|55.0:|:
m eso_rh6|96:|:
m eso_dir6|SSW:|:
m eso_wspeed6|3:|:
m eso_gust6|16:|:
m eso_barom6|29.85:|:
m eso_rrate6|0.00:|:
m eso_stat7|KILWHEAT8:|:
m eso_loc7|Wheaton:|:
m eso_neigh7|Wheaton Sanitary District:|:
m eso_url7|http://www.wsd.dst.il.us/:|:
m eso_URLtext7|:|:
m eso_historyURL7|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT8:|:
m eso_lon7|-88.14:|:
m eso_lat7|41.85:|:
m eso_time7|03/12 07:21:22:|:
m eso_temp7|56.3:|:
m eso_dew7|55.0:|:
m eso_rh7|95:|:
m eso_dir7|North:|:
m eso_wspeed7|5:|:
m eso_gust7|10:|:
m eso_barom7|29.87:|:
m eso_rrate7|0.00:|:
m eso_stat8|KILLISLE3:|:
m eso_loc8|Lisle:|:
m eso_neigh8|Main and Short Lisle:|:
m eso_url8|:|:
m eso_URLtext8|:|:
m eso_historyURL8|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLISLE3:|:
m eso_lon8|-88.07:|:
m eso_lat8|41.79:|:
m eso_time8|03/12 07:30:42:|:
m eso_temp8|55.6:|:
m eso_dew8|52.0:|:
m eso_rh8|88:|:
m eso_dir8|SW:|:
m eso_wspeed8|0:|:
m eso_gust8|0:|:
m eso_barom8|29.72:|:
m eso_rrate8|0.00:|:
m eso_stat9|KILAUROR16:|:
m eso_loc9|Aurora:|:
m eso_neigh9|Oakhurst:|:
m eso_url9|:|:
m eso_URLtext9|:|:
m eso_historyURL9|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILAUROR16:|:
m eso_lon9|-88.24:|:
m eso_lat9|41.75:|:
m eso_time9|03/12 07:30:53:|:
m eso_temp9|56.8:|:
m eso_dew9|55.0:|:
m eso_rh9|95:|:
m eso_dir9|SSE:|:
m eso_wspeed9|1:|:
m eso_gust9|0:|:
m eso_barom9|29.14:|:
m eso_rrate9|0.01:|:
m eso_stat10|KILWHEAT5:|:
m eso_loc10|Wheaton:|:
m eso_neigh10|N9ABF - West Wheaton:|:
m eso_url10|http://n9abf.com:|:
m eso_URLtext10|N9ABF.com Amateur Radio:|:
m eso_historyURL10|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT5:|:
m eso_lon10|-88.13:|:
m eso_lat10|41.87:|:
m eso_time10|03/12 07:23:43:|:
m eso_temp10|54.8:|:
m eso_dew10|49.0:|:
m eso_rh10|82:|:
m eso_dir10|SW:|:
m eso_wspeed10|4:|:
m eso_gust10|0:|:
m eso_barom10|29.88:|:
m eso_rrate10|0.00:|:
m eso_stat11|KILWHEAT7:|:
m eso_loc11|Wheaton:|:
m eso_neigh11|South Wheaton:|:
m eso_url11|:|:
m eso_URLtext11|:|:
m eso_historyURL11|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT7:|:
m eso_lon11|-88.08:|:
m eso_lat11|41.86:|:
m eso_time11|03/12 07:30:18:|:
m eso_temp11|55.0:|:
m eso_dew11|50.0:|:
m eso_rh11|83:|:
m eso_dir11|SW:|:
m eso_wspeed11|7:|:
m eso_gust11|9:|:
m eso_barom11|29.83:|:
m eso_rrate11|0.00:|:
m eso_stat12|MUP431:|:
m eso_loc12|West Chicago:|:
m eso_neigh12|MesoWest West Chicago                     IL US UPR:|:
m eso_url12|http://madis.noaa.gov:|:
m eso_URLtext12|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL12|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MUP431:|:
m eso_lon12|-88.18:|:
m eso_lat12|41.88:|:
m eso_time12|03/12 06:55:00:|:
m eso_temp12|52.0:|:
m eso_dew12|0.0:|:
m eso_rh12|0:|:
m eso_dir12|North:|:
m eso_wspeed12|0:|:
m eso_gust12|0:|:
m eso_barom12|0.00:|:
m eso_rrate12|0.00:|:
m eso_stat13|KILWOODR4:|:
m eso_loc13|Woodridge:|:
m eso_neigh13|Woodridge Neighborhood Weather:|:
m eso_url13|:|:
m eso_URLtext13|:|:
m eso_historyURL13|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWOODR4:|:
m eso_lon13|-88.05:|:
m eso_lat13|41.76:|:
m eso_time13|03/12 07:21:17:|:
m eso_temp13|56.0:|:
m eso_dew13|55.0:|:
m eso_rh13|96:|:
m eso_dir13|SE:|:
m eso_wspeed13|5:|:
m eso_gust13|15:|:
m eso_barom13|29.85:|:
m eso_rrate13|0.00:|:
m eso_stat14|KILWOODR2:|:
m eso_loc14|Woodridge:|:
m eso_neigh14|Woodridge:|:
m eso_url14|http://www.tkhuman.com:|:
m eso_URLtext14|TKHuman.com:|:
m eso_historyURL14|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWOODR2:|:
m eso_lon14|-88.04:|:
m eso_lat14|41.77:|:
m eso_time14|03/12 07:30:53:|:
m eso_temp14|55.0:|:
m eso_dew14|52.0:|:
m eso_rh14|90:|:
m eso_dir14|South:|:
m eso_wspeed14|10:|:
m eso_gust14|10:|:
m eso_barom14|29.39:|:
m eso_rrate14|0.00:|:
m eso_stat15|KILAUROR20:|:
m eso_loc15|Aurora:|:
m eso_neigh15|Columbia Station:|:
m eso_url15|:|:
m eso_URLtext15|:|:
m eso_historyURL15|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILAUROR20:|:
m eso_lon15|-88.25:|:
m eso_lat15|41.72:|:
m eso_time15|03/12 07:30:06:|:
m eso_temp15|55.6:|:
m eso_dew15|55.0:|:
m eso_rh15|99:|:
m eso_dir15|North:|:
m eso_wspeed15|5:|:
m eso_gust15|5:|:
m eso_barom15|29.72:|:
m eso_rrate15|0.00:|:
m eso_stat16|KILGLENE1:|:
m eso_loc16|Glen Ellyn:|:
m eso_neigh16|Butterfield West:|:
m eso_url16|http://www.dupageweather.com:|:
m eso_URLtext16|:|:
m eso_historyURL16|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILGLENE1:|:
m eso_lon16|-88.06:|:
m eso_lat16|41.85:|:
m eso_time16|03/12 07:23:11:|:
m eso_temp16|55.6:|:
m eso_dew16|54.0:|:
m eso_rh16|94:|:
m eso_dir16|SSW:|:
m eso_wspeed16|9:|:
m eso_gust16|9:|:
m eso_barom16|29.88:|:
m eso_rrate16|0.00:|:
m eso_stat17|KILWHEAT9:|:
m eso_loc17|Wheaton:|:
m eso_neigh17|NE Wheaton:|:
m eso_url17|:|:
m eso_URLtext17|:|:
m eso_historyURL17|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT9:|:
m eso_lon17|-88.09:|:
m eso_lat17|41.87:|:
m eso_time17|03/12 07:30:24:|:
m eso_temp17|56.1:|:
m eso_dew17|56.0:|:
m eso_rh17|99:|:
m eso_dir17|South:|:
m eso_wspeed17|8:|:
m eso_gust17|17:|:
m eso_barom17|29.05:|:
m eso_rrate17|0.00:|:
m eso_stat18|KILDOWNE2:|:
m eso_loc18|Downers Grove:|:
m eso_neigh18|Prentiss Creek:|:
m eso_url18|:|:
m eso_URLtext18|:|:
m eso_historyURL18|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDOWNE2:|:
m eso_lon18|-88.04:|:
m eso_lat18|41.77:|:
m eso_time18|03/12 07:30:39:|:
m eso_temp18|56.3:|:
m eso_dew18|56.0:|:
m eso_rh18|98:|:
m eso_dir18|SSW:|:
m eso_wspeed18|5:|:
m eso_gust18|0:|:
m eso_barom18|29.85:|:
m eso_rrate18|0.00:|:
m eso_stat19|KILBOLIN10:|:
m eso_loc19|Bolingbrook:|:
m eso_neigh19|Bolingbrook/Clow Airport:|:
m eso_url19|:|:
m eso_URLtext19|:|:
m eso_historyURL19|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBOLIN10:|:
m eso_lon19|-88.11:|:
m eso_lat19|41.70:|:
m eso_time19|03/12 07:30:53:|:
m eso_temp19|57.4:|:
m eso_dew19|57.0:|:
m eso_rh19|100:|:
m eso_dir19|South:|:
m eso_wspeed19|6:|:
m eso_gust19|10:|:
m eso_barom19|29.14:|:
m eso_rrate19|0.00:|:
m eso_stat20|KILDOWNE3:|:
m eso_loc20|Downers Grove:|:
m eso_neigh20|Maple Woods West:|:
m eso_url20|:|:
m eso_URLtext20|:|:
m eso_historyURL20|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDOWNE3:|:
m eso_lon20|-88.03:|:
m eso_lat20|41.79:|:
m eso_time20|03/12 07:30:54:|:
m eso_temp20|56.8:|:
m eso_dew20|57.0:|:
m eso_rh20|100:|:
m eso_dir20|SSW:|:
m eso_wspeed20|5:|:
m eso_gust20|8:|:
m eso_barom20|29.92:|:
m eso_rrate20|0.01:|:
m eso_stat21|MD0023:|:
m eso_loc21|Glen Ellyn:|:
m eso_neigh21|APRSWXNET Glen Ellyn                 IL US:|:
m eso_url21|http://madis.noaa.gov:|:
m eso_URLtext21|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL21|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MD0023:|:
m eso_lon21|-88.05:|:
m eso_lat21|41.84:|:
m eso_time21|03/12 06:56:00:|:
m eso_temp21|55.0:|:
m eso_dew21|53.0:|:
m eso_rh21|94:|:
m eso_dir21|SW:|:
m eso_wspeed21|9:|:
m eso_gust21|0:|:
m eso_barom21|29.88:|:
m eso_rrate21|0.00:|:
m eso_stat22|MAU162:|:
m eso_loc22|Glen Ellyn:|:
m eso_neigh22|APRSWXNET Glen Ellyn                   IL US:|:
m eso_url22|http://madis.noaa.gov:|:
m eso_URLtext22|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL22|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MAU162:|:
m eso_lon22|-88.08:|:
m eso_lat22|41.87:|:
m eso_time22|03/12 06:54:00:|:
m eso_temp22|54.0:|:
m eso_dew22|53.0:|:
m eso_rh22|96:|:
m eso_dir22|SSW:|:
m eso_wspeed22|1:|:
m eso_gust22|0:|:
m eso_barom22|29.91:|:
m eso_rrate22|0.00:|:
m eso_stat23|MC0472:|:
m eso_loc23|Glen Ellyn:|:
m eso_neigh23|APRSWXNET Wheaton                    IL US:|:
m eso_url23|http://madis.noaa.gov:|:
m eso_URLtext23|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL23|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MC0472:|:
m eso_lon23|-88.09:|:
m eso_lat23|41.88:|:
m eso_time23|03/12 07:02:00:|:
m eso_temp23|55.0:|:
m eso_dew23|53.0:|:
m eso_rh23|93:|:
m eso_dir23|East:|:
m eso_wspeed23|0:|:
m eso_gust23|0:|:
m eso_barom23|29.06:|:
m eso_rrate23|0.00:|:
m eso_stat24|KILWHEAT3:|:
m eso_loc24|Wheaton:|:
m eso_neigh24|Main and Geneva:|:
m eso_url24|http://www.mychicagogarden.com:|:
m eso_URLtext24|My Chicago Garden:|:
m eso_historyURL24|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT3:|:
m eso_lon24|-88.10:|:
m eso_lat24|41.88:|:
m eso_time24|03/12 07:30:25:|:
m eso_temp24|55.8:|:
m eso_dew24|36.0:|:
m eso_rh24|97:|:
m eso_dir24|SSW:|:
m eso_wspeed24|3:|:
m eso_gust24|7:|:
m eso_barom24|29.88:|:
m eso_rrate24|0.00:|:
m eso_stat25|MC5020:|:
m eso_loc25|Glen Ellyn:|:
m eso_neigh25|APRSWXNET Wheaton                    IL US:|:
m eso_url25|http://madis.noaa.gov:|:
m eso_URLtext25|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL25|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MC5020:|:
m eso_lon25|-88.09:|:
m eso_lat25|41.88:|:
m eso_time25|03/12 07:03:00:|:
m eso_temp25|55.0:|:
m eso_dew25|54.0:|:
m eso_rh25|96:|:
m eso_dir25|SW:|:
m eso_wspeed25|0:|:
m eso_gust25|0:|:
m eso_barom25|29.84:|:
m eso_rrate25|0.00:|:
m eso_stat26|KILBOLIN8:|:
m eso_loc26|Bolingbrook:|:
m eso_neigh26|Bolingbrook Neighborhood Weather:|:
m eso_url26|:|:
m eso_URLtext26|:|:
m eso_historyURL26|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBOLIN8:|:
m eso_lon26|-88.09:|:
m eso_lat26|41.69:|:
m eso_time26|03/12 07:30:44:|:
m eso_temp26|56.4:|:
m eso_dew26|55.0:|:
m eso_rh26|94:|:
m eso_dir26|SW:|:
m eso_wspeed26|1:|:
m eso_gust26|7:|:
m eso_barom26|29.89:|:
m eso_rrate26|0.00:|:
m eso_stat27|KILWESTC8:|:
m eso_loc27|West Chicago:|:
m eso_neigh27|Ingalton Hills Estates:|:
m eso_url27|:|:
m eso_URLtext27|:|:
m eso_historyURL27|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWESTC8:|:
m eso_lon27|-88.20:|:
m eso_lat27|41.91:|:
m eso_time27|03/12 07:30:52:|:
m eso_temp27|55.0:|:
m eso_dew27|54.0:|:
m eso_rh27|97:|:
m eso_dir27|SSW:|:
m eso_wspeed27|6:|:
m eso_gust27|6:|:
m eso_barom27|29.98:|:
m eso_rrate27|0.02:|:
m eso_stat28|KILDARIE3:|:
m eso_loc28|Darien:|:
m eso_neigh28|Darien, Near 75th St and Lemont Rd (60561):|:
m eso_url28|:|:
m eso_URLtext28|:|:
m eso_historyURL28|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDARIE3:|:
m eso_lon28|-88.01:|:
m eso_lat28|41.74:|:
m eso_time28|03/12 07:30:32:|:
m eso_temp28|56.7:|:
m eso_dew28|57.0:|:
m eso_rh28|100:|:
m eso_dir28|ENE:|:
m eso_wspeed28|2:|:
m eso_gust28|6:|:
m eso_barom28|29.77:|:
m eso_rrate28|0.00:|:
m eso_stat29|KILBATAV1:|:
m eso_loc29|Batavia:|:
m eso_neigh29|Tri-Cities on the Fox River:|:
m eso_url29|http://www.mdweather.com:|:
m eso_URLtext29|mdweather.com Weather Page:|:
m eso_historyURL29|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBATAV1:|:
m eso_lon29|-88.31:|:
m eso_lat29|41.85:|:
m eso_time29|03/12 07:30:35:|:
m eso_temp29|55.1:|:
m eso_dew29|54.0:|:
m eso_rh29|95:|:
m eso_dir29|South:|:
m eso_wspeed29|6:|:
m eso_gust29|6:|:
m eso_barom29|29.67:|:
m eso_rrate29|0.00:|:
m eso_stat30|KILWESTC5:|:
m eso_loc30|West Chicago:|:
m eso_neigh30|THEBROWNHOUSE.ORG:|:
m eso_url30|http://www.thebrownhouse.org:|:
m eso_URLtext30|http://www.thebrownhouse.org:|:
m eso_historyURL30|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWESTC5:|:
m eso_lon30|-88.21:|:
m eso_lat30|41.92:|:
m eso_time30|03/12 07:30:45:|:
m eso_temp30|55.2:|:
m eso_dew30|54.0:|:
m eso_rh30|96:|:
m eso_dir30|SW:|:
m eso_wspeed30|6:|:
m eso_gust30|9:|:
m eso_barom30|29.87:|:
m eso_rrate30|0.00:|:
m eso_stat31|KILLOMBA5:|:
m eso_loc31|Lombard:|:
m eso_neigh31|Lombard (East of Sunset Knoll):|:
m eso_url31|http://www.knottme.com/weather/lombard:|:
m eso_URLtext31|Dee and Audreys Lombard Weather:|:
m eso_historyURL31|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLOMBA5:|:
m eso_lon31|-88.02:|:
m eso_lat31|41.87:|:
m eso_time31|03/12 07:30:50:|:
m eso_temp31|55.8:|:
m eso_dew31|55.0:|:
m eso_rh31|98:|:
m eso_dir31|South:|:
m eso_wspeed31|7:|:
m eso_gust31|16:|:
m eso_barom31|30.00:|:
m eso_rrate31|0.00:|:
m eso_stat32|KILGLENE5:|:
m eso_loc32|Glen Ellyn:|:
m eso_neigh32|Near North Ave. and Main St. Glen Ellyn:|:
m eso_url32|http://glenellyn.weather.patmulcahy.com:|:
m eso_URLtext32|Father Pat's Glen Ellyn Weather:|:
m eso_historyURL32|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILGLENE5:|:
m eso_lon32|-88.06:|:
m eso_lat32|41.90:|:
m eso_time32|03/12 07:30:53:|:
m eso_temp32|56.1:|:
m eso_dew32|55.0:|:
m eso_rh32|96:|:
m eso_dir32|ESE:|:
m eso_wspeed32|1:|:
m eso_gust32|1:|:
m eso_barom32|29.06:|:
m eso_rrate32|0.00:|:
m eso_stat33|KILOSWEG9:|:
m eso_loc33|Oswego:|:
m eso_neigh33|Farmington Lakes:|:
m eso_url33|:|:
m eso_URLtext33|:|:
m eso_historyURL33|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILOSWEG9:|:
m eso_lon33|-88.31:|:
m eso_lat33|41.71:|:
m eso_time33|03/12 07:23:45:|:
m eso_temp33|54.9:|:
m eso_dew33|54.0:|:
m eso_rh33|95:|:
m eso_dir33|SSW:|:
m eso_wspeed33|3:|:
m eso_gust33|4:|:
m eso_barom33|29.65:|:
m eso_rrate33|0.05:|:
m eso_stat34|KILPLAIN19:|:
m eso_loc34|Plainfield:|:
m eso_neigh34|North Plainfield:|:
m eso_url34|:|:
m eso_URLtext34|:|:
m eso_historyURL34|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILPLAIN19:|:
m eso_lon34|-88.21:|:
m eso_lat34|41.66:|:
m eso_time34|03/12 07:26:58:|:
m eso_temp34|56.6:|:
m eso_dew34|54.0:|:
m eso_rh34|92:|:
m eso_dir34|SSW:|:
m eso_wspeed34|4:|:
m eso_gust34|12:|:
m eso_barom34|29.88:|:
m eso_rrate34|0.00:|:
m eso_stat35|KILROMEO6:|:
m eso_loc35|Romeoville:|:
m eso_neigh35|RAH:|:
m eso_url35|http://rahweather.com/wxindex.php:|:
m eso_URLtext35|RAH Weather:|:
m eso_historyURL35|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILROMEO6:|:
m eso_lon35|-88.09:|:
m eso_lat35|41.66:|:
m eso_time35|03/12 07:30:54:|:
m eso_temp35|57.2:|:
m eso_dew35|55.0:|:
m eso_rh35|92:|:
m eso_dir35|South:|:
m eso_wspeed35|15:|:
m eso_gust35|22:|:
m eso_barom35|29.90:|:
m eso_rrate35|0.00:|:
m eso_stat36|KILSTCHA8:|:
m eso_loc36|St. Charles:|:
m eso_neigh36|Ronzheimer Ave. & S. Tyler Rd.:|:
m eso_url36|:|:
m eso_URLtext36|:|:
m eso_historyURL36|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILSTCHA8:|:
m eso_lon36|-88.29:|:
m eso_lat36|41.91:|:
m eso_time36|03/12 07:30:56:|:
m eso_temp36|54.8:|:
m eso_dew36|54.0:|:
m eso_rh36|96:|:
m eso_dir36|SW:|:
m eso_wspeed36|4:|:
m eso_gust36|10:|:
m eso_barom36|29.85:|:
m eso_rrate36|0.00:|:
m eso_stat37|KILBATAV3:|:
m eso_loc37|Batavia:|:
m eso_neigh37|West Batavia:|:
m eso_url37|:|:
m eso_URLtext37|:|:
m eso_historyURL37|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBATAV3:|:
m eso_lon37|-88.36:|:
m eso_lat37|41.83:|:
m eso_time37|03/12 07:21:23:|:
m eso_temp37|54.6:|:
m eso_dew37|54.0:|:
m eso_rh37|96:|:
m eso_dir37|SSW:|:
m eso_wspeed37|1:|:
m eso_gust37|12:|:
m eso_barom37|29.85:|:
m eso_rrate37|0.00:|:
m eso_stat38|KILROMEO4:|:
m eso_loc38|Romeoville:|:
m eso_neigh38|Lakewood Estates:|:
m eso_url38|:|:
m eso_URLtext38|:|:
m eso_historyURL38|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILROMEO4:|:
m eso_lon38|-88.10:|:
m eso_lat38|41.65:|:
m eso_time38|03/12 07:30:07:|:
m eso_temp38|57.0:|:
m eso_dew38|48.0:|:
m eso_rh38|73:|:
m eso_dir38|SSW:|:
m eso_wspeed38|8:|:
m eso_gust38|8:|:
m eso_barom38|24.00:|:
m eso_rrate38|0.00:|:
m eso_stat39|KILLOMBA4:|:
m eso_loc39|Lombard:|:
m eso_neigh39|Madison Meadow:|:
m eso_url39|:|:
m eso_URLtext39|:|:
m eso_historyURL39|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLOMBA4:|:
m eso_lon39|-88.00:|:
m eso_lat39|41.88:|:
m eso_time39|03/12 07:30:05:|:
m eso_temp39|55.9:|:
m eso_dew39|47.0:|:
m eso_rh39|73:|:
m eso_dir39|South:|:
m eso_wspeed39|4:|:
m eso_gust39|4:|:
m eso_barom39|29.98:|:
m eso_rrate39|0.00:|:
m eso_stat40|KDPA:|:
m eso_loc40|Chicago DuPage:|:
m eso_neigh40|Official Station:|:
m eso_url40|:|:
m eso_URLtext40|:|:
m eso_historyURL40|www.wunderground.com/history/airport/KDPA/2012/3/12/DailyHistory.html?FULLALMANAC=KDPA:|:
m eso_lon40|-88.25:|:
m eso_lat40|41.91:|:
m eso_time40|03/12 06:52:00:|:
m eso_temp40|55.0:|:
m eso_dew40|52.0:|:
m eso_rh40|89:|:
m eso_dir40|South:|:
m eso_wspeed40|14:|:
m eso_gust40|---:|:
m eso_barom40|29.86:|:
m eso_rrate40|---:|:
END_OF_RAW_DATA_LINES;

// end of generation script

// put data in  array
//
$WX = array();
global $WX;
$WXComment = array();
$data = explode(":|:",$rawdatalines);
$nscanned = 0;
foreach ($data as $v => $line) {
list($vname,$vval,$vcomment) = explode("|",trim($line).'|||');
$vname = substr($vname,0,1) . substr($vname,2);
if ($vname <> "") {
$WX[$vname] = trim($vval);
if($vcomment <> "") { $WXComment[$vname] = trim($vcomment); }
}
$nscanned++;
}
if(isset($_REQUEST['debug'])) {
print "<!-- loaded $nscanned $WXsoftware \$WX[] entries -->\n";
}

if (isset($_REQUEST["sce"]) and strtolower($_REQUEST["sce"]) == "dump" ) {

print "<pre>\n";
print "// \$WX[] array size = $nscanned entries.\n";
foreach ($WX as $key => $val) {
$t =  "\$WX['$key'] = '$val';";
if(isset($WXComment[$key])) {$t .=  " $WXComment[$key]"; }
print "$t\n";
}
print "</pre>\n";

}
if(file_exists("VWS-defs.php")) { include_once("VWS-defs.php"); }
?>
