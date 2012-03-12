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
v xv001|WSW:|:
v hi001|345:|:
v ht001|3:52pm:|:
v lo001|29:|:
v lt001|10:56am:|:
v va001|249:|:
v da001|0:|:
v ma001|0:|:
v ya001|0:|:
v vr001|4.8:|:
v rh001|112.6:|:
v rt001|3:52pm:|:
v rl001|-173.1:|:
v rs001|12:20pm:|:
v mh001|360:|:
v md001|3/2/12:|:
v ml001|1:|:
v me001|3/2/12:|:
v yh001|360:|:
v yd001|1/1/12:|:
v yl001|0:|:
v ye001|1/9/12:|:
v zh001|315:|:
v zt001|10:39am:|:
v zl001|29:|:
v zs001|10:56am:|:
v hv001|170:|:
v dv001|49:|:
u ni001|&deg;:|:
v xv002|9:|:
v hi002|24:|:
v ht002|11:57am:|:
v lo002|0:|:
v lt002|7:49am:|:
v va002|7:|:
v da002|6:|:
v ma002|6:|:
v ya002|4:|:
v vr002|1.8:|:
v rh002|16.0:|:
v rt002|11:57am:|:
v rl002|-7.3:|:
v rs002|2:17pm:|:
v mh002|36:|:
v md002|3/7/12:|:
v ml002|0:|:
v me002|3/1/12:|:
v yh002|36:|:
v yd002|3/7/12:|:
v yl002|0:|:
v ye002|1/1/12:|:
v zh002|21:|:
v zt002|12:40am:|:
v zl002|0:|:
v zs002|7:49am:|:
v hv002|5:|:
v dv002|-2:|:
u ni002|mph:|:
v xv003|15:|:
v hi003|24:|:
v ht003|11:57am:|:
v lo003|2:|:
v lt003|9:40am:|:
v va003|14:|:
v da003|16:|:
v ma003|14:|:
v ya003|10:|:
v vr003|0.7:|:
v rh003|15.6:|:
v rt003|11:57am:|:
v rl003|-11.8:|:
v rs003|2:30pm:|:
v mh003|36:|:
v md003|3/7/12:|:
v ml003|0:|:
v me003|3/1/12:|:
v yh003|36:|:
v yd003|3/7/12:|:
v yl003|0:|:
v ye003|1/3/12:|:
v zh003|21:|:
v zt003|12:40am:|:
v zl003|2:|:
v zs003|9:40am:|:
v hv003|10:|:
v dv003|5:|:
u ni003|mph:|:
v xv004|39:|:
v hi004|41:|:
v ht004|12:09pm:|:
v lo004|36:|:
v lt004|12:00am:|:
v va004|39:|:
v da004|39:|:
v ma004|34:|:
v ya004|33:|:
v vr004|-0.4:|:
v rh004|1.0:|:
v rt004|12:09pm:|:
v rl004|-1.1:|:
v rs004|4:10pm:|:
v mh004|41:|:
v md004|3/12/12:|:
v ml004|27:|:
v me004|3/9/12:|:
v yh004|42:|:
v yd004|2/24/12:|:
v yl004|24:|:
v ye004|1/19/12:|:
v zh004|40:|:
v zt004|10:28am:|:
v zl004|36:|:
v zs004|12:00am:|:
v hv004|35:|:
v dv004|4:|:
u ni004|%:|:
v xv005|69:|:
v hi005|97:|:
v ht005|4:20am:|:
v lo005|65:|:
v lt005|12:40am:|:
v va005|71:|:
v da005|85:|:
v ma005|65:|:
v ya005|74:|:
v vr005|-2.1:|:
v rh005|2.3:|:
v rt005|4:17pm:|:
v rl005|-4.0:|:
v rs005|2:57pm:|:
v mh005|97:|:
v md005|3/12/12:|:
v ml005|20:|:
v me005|3/11/12:|:
v yh005|100:|:
v yd005|1/23/12:|:
v yl005|20:|:
v ye005|3/11/12:|:
v zh005|97:|:
v zt005|4:20am:|:
v zl005|65:|:
v zs005|12:40am:|:
v hv005|29:|:
v dv005|40:|:
u ni005|%:|:
v xv006|74.5:|:
v hi006|74.5:|:
v ht006|4:58pm:|:
v lo006|71.4:|:
v lt006|3:50am:|:
v va006|74.1:|:
v da006|72.7:|:
v ma006|71.5:|:
v ya006|71.1:|:
v vr006|0.41:|:
v rh006|0.76:|:
v rt006|4:16pm:|:
v rl006|-0.20:|:
v rs006|12:23pm:|:
v mh006|75.6:|:
v md006|3/6/12:|:
v ml006|67.4:|:
v me006|3/5/12:|:
v yh006|75.6:|:
v yd006|3/6/12:|:
v yl006|65.6:|:
v ye006|1/20/12:|:
v zh006|73.1:|:
v zt006|12:00am:|:
v zl006|71.4:|:
v zs006|3:50am:|:
v hv006|73.1:|:
v dv006|1.4:|:
u ni006|&deg;F:|:
v xv007|60.9:|:
v hi007|64.0:|:
v ht007|2:48pm:|:
v lo007|51.0:|:
v lt007|2:20am:|:
v va007|61.4:|:
v da007|57.4:|:
v ma007|41.3:|:
v ya007|32.4:|:
v vr007|-0.49:|:
v rh007|1.02:|:
v rt007|2:06pm:|:
v rl007|-1.68:|:
v rs007|4:17pm:|:
v mh007|68.6:|:
v md007|3/11/12:|:
v ml007|18.4:|:
v me007|3/5/12:|:
v yh007|68.6:|:
v yd007|3/11/12:|:
v yl007|2.8:|:
v ye007|1/20/12:|:
v zh007|60.5:|:
v zt007|10:37am:|:
v zl007|51.0:|:
v zs007|2:20am:|:
v hv007|65.2:|:
v dv007|-4.3:|:
u ni007|&deg;F:|:
v xv008|28.97:|:
v hi008|29.23:|:
v ht008|12:00am:|:
v lo008|28.94:|:
v lt008|3:03pm:|:
v va008|28.97:|:
v da008|29.07:|:
v ma008|29.22:|:
v ya008|29.26:|:
v vr008|0.003:|:
v rh008|0.010:|:
v rt008|4:57pm:|:
v rl008|-0.025:|:
v rs008|2:47pm:|:
v mh008|29.76:|:
v md008|3/9/12:|:
v ml008|28.50:|:
v me008|3/2/12:|:
v yh008|29.76:|:
v yd008|3/9/12:|:
v yl008|28.50:|:
v ye008|3/2/12:|:
v zh008|29.23:|:
v zt008|12:00am:|:
v zl008|29.03:|:
v zs008|10:42am:|:
v hv008|29.32:|:
v dv008|-0.35:|:
u ni008|in:|:
v xv009|3.35:|:
v hi009|3.35:|:
v ht009|7:31am:|:
v lo009|3.06:|:
v lt009|12:00am:|:
v va009|3.35:|:
v da009|3.30:|:
v ma009|2.92:|:
v ya009|1.35:|:
v vr009|0.000:|:
v rh009|0.000:|:
v rt009|:|:
v rl009|0.000:|:
v rs009|:|:
v mh009|3.35:|:
v md009|3/12/12:|:
v ml009|2.69:|:
v me009|3/1/12:|:
v yh009|3.35:|:
v yd009|3/12/12:|:
v yl009|0.01:|:
v ye009|1/1/12:|:
v zh009|3.35:|:
v zt009|7:31am:|:
v zl009|3.06:|:
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
v rt010|:|:
v rl010|0.00:|:
v rs010|:|:
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
v rt011|:|:
v rl011|0.0:|:
v rs011|:|:
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
v rt012|:|:
v rl012|0.00:|:
v rs012|:|:
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
v rt013|:|:
v rl013|0.0:|:
v rs013|:|:
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
v rt014|:|:
v rl014|0.00:|:
v rs014|:|:
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
v rt015|:|:
v rl015|0.0:|:
v rs015|:|:
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
v xv016|0.04:|:
v hi016|0.04:|:
v ht016|5:00pm:|:
v lo016|0.00:|:
v lt016|12:10am:|:
v va016|0.04:|:
v da016|0.01:|:
v ma016|0.03:|:
v ya016|0.02:|:
v vr016|0.005:|:
v rh016|0.009:|:
v rt016|3:00pm:|:
v rl016|0.000:|:
v rs016|:|:
v mh016|0.18:|:
v md016|3/6/12:|:
v ml016|0.00:|:
v me016|3/1/12:|:
v yh016|0.18:|:
v yd016|3/6/12:|:
v yl016|0.00:|:
v ye016|1/1/12:|:
v zh016|0.01:|:
v zt016|10:00am:|:
v zl016|0.00:|:
v zs016|12:10am:|:
v hv016|0.11:|:
v dv016|-0.07:|:
u ni016|in:|:
v xv017|0.0:|:
v hi017|2.0:|:
v ht017|1:55pm:|:
v lo017|0.0:|:
v lt017|12:00am:|:
v va017|0.3:|:
v da017|0.3:|:
v ma017|0.4:|:
v ya017|0.2:|:
v vr017|-0.34:|:
v rh017|1.17:|:
v rt017|1:55pm:|:
v rl017|-1.48:|:
v rs017|12:03pm:|:
v mh017|3.5:|:
v md017|3/11/12:|:
v ml017|0.0:|:
v me017|3/1/12:|:
v yh017|3.5:|:
v yd017|3/11/12:|:
v yl017|0.0:|:
v ye017|1/1/12:|:
v zh017|1.8:|:
v zt017|10:27am:|:
v zl017|0.0:|:
v zs017|12:00am:|:
v hv017|0.0:|:
v dv017|0.0:|:
u ni017|:|:
v xv018|105:|:
v hi018|587:|:
v ht018|10:28am:|:
v lo018|0:|:
v lt018|12:00am:|:
v va018|141:|:
v da018|72:|:
v ma018|125:|:
v ya018|77:|:
v vr018|-36.0:|:
v rh018|301.2:|:
v rt018|1:55pm:|:
v rl018|-198.7:|:
v rs018|12:06pm:|:
v mh018|947:|:
v md018|3/5/12:|:
v ml018|0:|:
v me018|3/1/12:|:
v yh018|969:|:
v yd018|2/27/12:|:
v yl018|0:|:
v ye018|1/1/12:|:
v zh018|587:|:
v zt018|10:28am:|:
v zl018|0:|:
v zs018|12:00am:|:
v hv018|118:|:
v dv018|-13:|:
u ni018|W/sqm:|:
v xv019|59.8:|:
v hi019|64.0:|:
v ht019|2:48pm:|:
v lo019|48.9:|:
v lt019|2:20am:|:
v va019|60.8:|:
v da019|56.5:|:
v ma019|38.3:|:
v ya019|29.2:|:
v vr019|-1.01:|:
v rh019|1.47:|:
v rt019|2:06pm:|:
v rl019|-3.63:|:
v rs019|4:46pm:|:
v mh019|68.6:|:
v md019|3/11/12:|:
v ml019|9.2:|:
v me019|3/5/12:|:
v yh019|68.6:|:
v yd019|3/11/12:|:
v yl019|-10.7:|:
v ye019|1/19/12:|:
v zh019|60.5:|:
v zt019|10:38am:|:
v zl019|48.9:|:
v zs019|2:20am:|:
v hv019|65.2:|:
v dv019|-4.3:|:
u ni019|&deg;F:|:
v xv020|74.3:|:
v hi020|74.3:|:
v ht020|4:58pm:|:
v lo020|71.1:|:
v lt020|3:50am:|:
v va020|74.0:|:
v da020|72.5:|:
v ma020|71.0:|:
v ya020|70.4:|:
v vr020|0.39:|:
v rh020|0.76:|:
v rt020|4:16pm:|:
v rl020|-0.20:|:
v rs020|12:23pm:|:
v mh020|75.4:|:
v md020|3/7/12:|:
v ml020|66.7:|:
v me020|3/5/12:|:
v yh020|75.4:|:
v yd020|3/7/12:|:
v yl020|64.5:|:
v ye020|1/20/12:|:
v zh020|72.9:|:
v zt020|7:39am:|:
v zl020|71.1:|:
v zs020|3:50am:|:
v hv020|72.6:|:
v dv020|1.7:|:
u ni020|&deg;F:|:
v xv021|63.7:|:
v hi021|67.3:|:
v ht021|2:39pm:|:
v lo021|57.0:|:
v lt021|1:50am:|:
v va021|64.5:|:
v da021|62.5:|:
v ma021|43.9:|:
v ya021|36.0:|:
v vr021|-0.75:|:
v rh021|0.92:|:
v rt021|2:39pm:|:
v rl021|-1.56:|:
v rs021|4:56pm:|:
v mh021|67.6:|:
v md021|3/6/12:|:
v ml021|23.0:|:
v me021|3/5/12:|:
v yh021|67.6:|:
v yd021|3/6/12:|:
v yl021|4.2:|:
v ye021|1/19/12:|:
v zh021|65.8:|:
v zt021|10:30am:|:
v zl021|57.0:|:
v zs021|1:50am:|:
v hv021|64.3:|:
v dv021|-0.6:|:
u ni021|&deg;F:|:
v xv022|50.7:|:
v hi022|57.3:|:
v ht022|10:30am:|:
v lo022|43.7:|:
v lt022|12:40am:|:
v va022|52.0:|:
v da022|52.8:|:
v ma022|28.8:|:
v ya022|24.3:|:
v vr022|-1.28:|:
v rh022|0.76:|:
v rt022|2:39pm:|:
v rl022|-2.04:|:
v rs022|4:56pm:|:
v mh022|57.3:|:
v md022|3/12/12:|:
v ml022|3.1:|:
v me022|3/9/12:|:
v yh022|57.3:|:
v yd022|3/12/12:|:
v yl022|-12.0:|:
v ye022|1/19/12:|:
v zh022|57.3:|:
v zt022|10:30am:|:
v zl022|43.7:|:
v zs022|12:40am:|:
v hv022|32.2:|:
v dv022|18.5:|:
u ni022|&deg;F:|:
v xv023|29.77:|:
v hi023|30.04:|:
v ht023|12:00am:|:
v lo023|29.74:|:
v lt023|3:03pm:|:
v va023|29.77:|:
v da023|29.87:|:
v ma023|30.03:|:
v ya023|30.07:|:
v vr023|0.003:|:
v rh023|0.010:|:
v rt023|4:57pm:|:
v rl023|-0.026:|:
v rs023|2:47pm:|:
v mh023|30.58:|:
v md023|3/9/12:|:
v ml023|29.28:|:
v me023|3/2/12:|:
v yh023|30.58:|:
v yd023|3/9/12:|:
v yl023|29.28:|:
v ye023|3/2/12:|:
v zh023|30.04:|:
v zt023|12:00am:|:
v zl023|29.83:|:
v zs023|10:42am:|:
v hv023|30.13:|:
v dv023|-0.36:|:
u ni023|in:|:
v xv024|887:|:
u ni024|ft:|:
v xv025|2557:|:
u ni025|ft:|:
v xv026|1237:|:
u ni026|ft:|:
v xv027|63.4:|:
u ni027|&deg;F:|:
v xv028|0.37:|:
u ni028|in:|:
v xv121|0.29:|:
u ni121|in:|:
v xv122|0.00:|:
u ni122|in:|:
v xv123|0.30:|:
u ni123|in:|:
v xv124|0.000:|:
u ni124|in/hr:|:
v xv125|101:|:
u ni125|miles:|:
v xv126|5.2:|:
u ni126|&deg;F:|:
v xv127|0.0:|:
u ni127|&deg;F:|:
v xv128|20:|:
u ni128|:|:
v xv129|0.66:|:
u ni129|in:|:
v xv130|233.7:|:
u ni130|&deg;F:|:
v xv131|0.7:|:
u ni131|&deg;F:|:
v xv132|1379:|:
u ni132|miles:|:
v xv133|2084.1:|:
u ni133|&deg;F:|:
v xv134|0.7:|:
u ni134|&deg;F:|:
v xv135|6394:|:
u ni135|miles:|:
v st136|---:|:
u ni136|:|:
v st137|Cool:|:
u ni137|:|:
v st138|Mostly cloudy and cooler. Precip possible within 12 hours possibly heavy at times. Windy:|:
u ni138|:|:
v st139|Steady:|:
u ni139|:|:
v st140|Steady:|:
u ni140|:|:
v st141|Gentle Breeze:|:
u ni141|:|:
v st142|3/12/12:|:
u ni142|:|:
v st143|5:24pm:|:
u ni143|:|:
v st144|7:06am:|:
u ni144|:|:
v st145|6:54pm:|:
u ni145|:|:
v st146|---:|:
u ni146|:|:
v st147|9:24am:|:
u ni147|:|:
v st148|West South West:|:
u ni148|:|:
m oon_percent|76%:|:
m oon_day|20:|:
v ervws|V14.01:|:
w station|Davis Instruments Vantage Pro2:|:
w sdescription|NapervilleWeather.com:|:
w slocation|Naperville, IL:|:
w staturl|http://www.ambientweather.com/dawest.html:|:
w slong|-88.1200027:|:
w slat|41.7900009:|:
w orld_id|us:|:
t emp_color|69ff53:|:
t emp_color_hi|b9ff53:|:
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
a lmhi1|63:|:
a lmdatehi1|2007:|:
a lmlo1|3:|:
a lmdatelo1|1998:|:
a lmnormhi1|46:|:
a lmnormlo1|27:|:
a lmytd1|2.61:|:
a lmmtd1|0.49:|:
t emp_rec_hi1|9fff53:|:
t emp_rec_lo1|ffffff:|:
t emp_norm_hi1|53d3b9:|:
t emp_norm_lo1|7953ff:|:
c limate_cconds1|Overcast
:|:
c limate_icon1|cloudy:|:
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
f orecast_conds1_1|Chance T-storms:|:
f orecast_icon1_1|chancetstorms:|:
t emp_for_hi1_1|ffff53:|:
t emp_for_lo1_1|53d3d2:|:
f orecast_day1_2|Tue:|:
f orecast_hi1_2|68:|:
f orecast_lo1_2|48:|:
f orecast_conds1_2|Clear:|:
f orecast_icon1_2|clear:|:
t emp_for_hi1_2|ffff53:|:
t emp_for_lo1_2|53d386:|:
f orecast_day1_3|Wed:|:
f orecast_hi1_3|73:|:
f orecast_lo1_3|61:|:
f orecast_conds1_3|Partly Cloudy:|:
f orecast_icon1_3|partlycloudy:|:
t emp_for_hi1_3|ffff53:|:
t emp_for_lo1_3|6cff53:|:
f orecast_day1_4|Thu:|:
f orecast_hi1_4|75:|:
f orecast_lo1_4|52:|:
f orecast_conds1_4|Chance T-storms:|:
f orecast_icon1_4|chancetstorms:|:
t emp_for_hi1_4|ffff53:|:
t emp_for_lo1_4|53ec53:|:
f orecast_day1_5|Fri:|:
f orecast_hi1_5|75:|:
f orecast_lo1_5|45:|:
f orecast_conds1_5|Chance T-storms:|:
f orecast_icon1_5|chancetstorms:|:
t emp_for_hi1_5|ffff53:|:
t emp_for_lo1_5|53d3d2:|:
f orecast_nexrad2|LOT:|:
f orecast_country2|US:|:
f orecast_radregion2|a4:|:
f orecast_tzname2|America/Chicago:|:
f orecast_wmo2|:|:
f orecast_day2_1|Mon:|:
f orecast_hi2_1|68:|:
f orecast_lo2_1|45:|:
f orecast_conds2_1|Chance T-storms:|:
f orecast_icon2_1|chancetstorms:|:
t emp_for_hi2_1|ffffff:|:
t emp_for_lo2_1|ffffff:|:
f orecast_day2_2|Tue:|:
f orecast_hi2_2|68:|:
f orecast_lo2_2|48:|:
f orecast_conds2_2|Clear:|:
f orecast_icon2_2|clear:|:
t emp_for_hi2_2|ffffff:|:
t emp_for_lo2_2|ffffff:|:
f orecast_day2_3|Wed:|:
f orecast_hi2_3|73:|:
f orecast_lo2_3|61:|:
f orecast_conds2_3|Partly Cloudy:|:
f orecast_icon2_3|partlycloudy:|:
t emp_for_hi2_3|ffffff:|:
t emp_for_lo2_3|ffffff:|:
f orecast_day2_4|Thu:|:
f orecast_hi2_4|75:|:
f orecast_lo2_4|52:|:
f orecast_conds2_4|Chance T-storms:|:
f orecast_icon2_4|chancetstorms:|:
t emp_for_hi2_4|ffffff:|:
t emp_for_lo2_4|ffffff:|:
f orecast_day2_5|Fri:|:
f orecast_hi2_5|75:|:
f orecast_lo2_5|45:|:
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
f orecast_conds3_1|Chance T-storms:|:
f orecast_icon3_1|chancetstorms:|:
t emp_for_hi3_1|53d3d2:|:
t emp_for_lo3_1|ffffff:|:
f orecast_day3_2|Tue:|:
f orecast_hi3_2|68:|:
f orecast_lo3_2|48:|:
f orecast_conds3_2|Clear:|:
f orecast_icon3_2|clear:|:
t emp_for_hi3_2|53d386:|:
t emp_for_lo3_2|ffffff:|:
f orecast_day3_3|Wed:|:
f orecast_hi3_3|73:|:
f orecast_lo3_3|61:|:
f orecast_conds3_3|Partly Cloudy:|:
f orecast_icon3_3|partlycloudy:|:
t emp_for_hi3_3|6cff53:|:
t emp_for_lo3_3|ffffff:|:
f orecast_day3_4|Thu:|:
f orecast_hi3_4|75:|:
f orecast_lo3_4|52:|:
f orecast_conds3_4|Chance T-storms:|:
f orecast_icon3_4|chancetstorms:|:
t emp_for_hi3_4|53ec53:|:
t emp_for_lo3_4|ffffff:|:
f orecast_day3_5|Fri:|:
f orecast_hi3_5|75:|:
f orecast_lo3_5|45:|:
f orecast_conds3_5|Chance T-storms:|:
f orecast_icon3_5|chancetstorms:|:
t emp_for_hi3_5|53d3d2:|:
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
m eso_time1|03/12 17:19:55:|:
m eso_temp1|61.2:|:
m eso_dew1|50.0:|:
m eso_rh1|68:|:
m eso_dir1|WNW:|:
m eso_wspeed1|12:|:
m eso_gust1|14:|:
m eso_barom1|28.94:|:
m eso_rrate1|0.00:|:
m eso_stat2|KILNAPER21:|:
m eso_loc2|Naperville:|:
m eso_neigh2|Saybrook:|:
m eso_url2|:|:
m eso_URLtext2|:|:
m eso_historyURL2|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER21:|:
m eso_lon2|-88.14:|:
m eso_lat2|41.79:|:
m eso_time2|03/12 17:00:01:|:
m eso_temp2|60.5:|:
m eso_dew2|52.0:|:
m eso_rh2|74:|:
m eso_dir2|West:|:
m eso_wspeed2|8:|:
m eso_gust2|17:|:
m eso_barom2|29.76:|:
m eso_rrate2|0.00:|:
m eso_stat3|KILNAPER19:|:
m eso_loc3|Naperville:|:
m eso_neigh3|Brookdale Lakes:|:
m eso_url3|:|:
m eso_URLtext3|:|:
m eso_historyURL3|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER19:|:
m eso_lon3|-88.20:|:
m eso_lat3|41.78:|:
m eso_time3|03/12 17:19:59:|:
m eso_temp3|61.4:|:
m eso_dew3|52.0:|:
m eso_rh3|72:|:
m eso_dir3|West:|:
m eso_wspeed3|1:|:
m eso_gust3|8:|:
m eso_barom3|29.74:|:
m eso_rrate3|0.00:|:
m eso_stat4|KILNAPER7:|:
m eso_loc4|Naperville:|:
m eso_neigh4|NapervilleWeather.com Near Ogden and Naper Blvd:|:
m eso_url4|http://NapervilleWeather.com:|:
m eso_URLtext4|NapervilleWeather.com Web Page:|:
m eso_historyURL4|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER7:|:
m eso_lon4|-88.12:|:
m eso_lat4|41.79:|:
m eso_time4|03/12 17:20:05:|:
m eso_temp4|60.7:|:
m eso_dew4|50.0:|:
m eso_rh4|69:|:
m eso_dir4|SSW:|:
m eso_wspeed4|10:|:
m eso_gust4|10:|:
m eso_barom4|29.77:|:
m eso_rrate4|0.00:|:
m eso_stat5|KILWARRE2:|:
m eso_loc5|Warrenville:|:
m eso_neigh5|Warrenville Neighborhood Weather:|:
m eso_url5|http://weather.binkerd.net:|:
m eso_URLtext5|Binkerd Weather:|:
m eso_historyURL5|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWARRE2:|:
m eso_lon5|-88.17:|:
m eso_lat5|41.82:|:
m eso_time5|03/12 17:19:27:|:
m eso_temp5|60.6:|:
m eso_dew5|52.0:|:
m eso_rh5|74:|:
m eso_dir5|NNW:|:
m eso_wspeed5|4:|:
m eso_gust5|12:|:
m eso_barom5|29.77:|:
m eso_rrate5|0.00:|:
m eso_stat6|KILNAPER9:|:
m eso_loc6|Naperville:|:
m eso_neigh6|75th St. /  Modaff Area:|:
m eso_url6|http://mistybeagle.com/wx/:|:
m eso_URLtext6|Misty The Weather Beagle:|:
m eso_historyURL6|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER9:|:
m eso_lon6|-88.14:|:
m eso_lat6|41.74:|:
m eso_time6|03/12 17:20:04:|:
m eso_temp6|60.7:|:
m eso_dew6|53.0:|:
m eso_rh6|75:|:
m eso_dir6|WSW:|:
m eso_wspeed6|4:|:
m eso_gust6|16:|:
m eso_barom6|29.74:|:
m eso_rrate6|0.00:|:
m eso_stat7|KILWHEAT8:|:
m eso_loc7|Wheaton:|:
m eso_neigh7|Wheaton Sanitary District:|:
m eso_url7|http://www.wsd.dst.il.us/:|:
m eso_URLtext7|:|:
m eso_historyURL7|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT8:|:
m eso_lon7|-88.14:|:
m eso_lat7|41.85:|:
m eso_time7|03/12 17:08:07:|:
m eso_temp7|60.6:|:
m eso_dew7|52.0:|:
m eso_rh7|73:|:
m eso_dir7|ENE:|:
m eso_wspeed7|7:|:
m eso_gust7|19:|:
m eso_barom7|29.75:|:
m eso_rrate7|0.00:|:
m eso_stat8|KILLISLE3:|:
m eso_loc8|Lisle:|:
m eso_neigh8|Main and Short Lisle:|:
m eso_url8|:|:
m eso_URLtext8|:|:
m eso_historyURL8|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLISLE3:|:
m eso_lon8|-88.07:|:
m eso_lat8|41.79:|:
m eso_time8|03/12 17:20:02:|:
m eso_temp8|60.6:|:
m eso_dew8|50.0:|:
m eso_rh8|68:|:
m eso_dir8|West:|:
m eso_wspeed8|0:|:
m eso_gust8|0:|:
m eso_barom8|29.61:|:
m eso_rrate8|0.00:|:
m eso_stat9|KILAUROR16:|:
m eso_loc9|Aurora:|:
m eso_neigh9|Oakhurst:|:
m eso_url9|:|:
m eso_URLtext9|:|:
m eso_historyURL9|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILAUROR16:|:
m eso_lon9|-88.24:|:
m eso_lat9|41.75:|:
m eso_time9|03/12 17:20:04:|:
m eso_temp9|62.3:|:
m eso_dew9|54.0:|:
m eso_rh9|75:|:
m eso_dir9|WSW:|:
m eso_wspeed9|5:|:
m eso_gust9|0:|:
m eso_barom9|29.04:|:
m eso_rrate9|0.00:|:
m eso_stat10|KILWHEAT5:|:
m eso_loc10|Wheaton:|:
m eso_neigh10|N9ABF - West Wheaton:|:
m eso_url10|http://n9abf.com:|:
m eso_URLtext10|N9ABF.com Amateur Radio:|:
m eso_historyURL10|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT5:|:
m eso_lon10|-88.13:|:
m eso_lat10|41.87:|:
m eso_time10|03/12 17:07:57:|:
m eso_temp10|59.4:|:
m eso_dew10|45.0:|:
m eso_rh10|59:|:
m eso_dir10|West:|:
m eso_wspeed10|5:|:
m eso_gust10|0:|:
m eso_barom10|29.78:|:
m eso_rrate10|0.00:|:
m eso_stat11|MD1973:|:
m eso_loc11|Downers Grove:|:
m eso_neigh11|APRSWXNET Woodridge                  IL US:|:
m eso_url11|http://madis.noaa.gov:|:
m eso_URLtext11|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL11|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MD1973:|:
m eso_lon11|-88.06:|:
m eso_lat11|41.77:|:
m eso_time11|03/12 17:03:00:|:
m eso_temp11|60.0:|:
m eso_dew11|52.0:|:
m eso_rh11|76:|:
m eso_dir11|West:|:
m eso_wspeed11|6:|:
m eso_gust11|15:|:
m eso_barom11|29.74:|:
m eso_rrate11|0.00:|:
m eso_stat12|KILWHEAT7:|:
m eso_loc12|Wheaton:|:
m eso_neigh12|South Wheaton:|:
m eso_url12|:|:
m eso_URLtext12|:|:
m eso_historyURL12|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT7:|:
m eso_lon12|-88.08:|:
m eso_lat12|41.86:|:
m eso_time12|03/12 17:10:22:|:
m eso_temp12|60.1:|:
m eso_dew12|52.0:|:
m eso_rh12|74:|:
m eso_dir12|SSW:|:
m eso_wspeed12|6:|:
m eso_gust12|6:|:
m eso_barom12|29.71:|:
m eso_rrate12|0.00:|:
m eso_stat13|MUP431:|:
m eso_loc13|West Chicago:|:
m eso_neigh13|MesoWest West Chicago                     IL US UPR:|:
m eso_url13|http://madis.noaa.gov:|:
m eso_URLtext13|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL13|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MUP431:|:
m eso_lon13|-88.18:|:
m eso_lat13|41.88:|:
m eso_time13|03/12 15:45:00:|:
m eso_temp13|58.0:|:
m eso_dew13|0.0:|:
m eso_rh13|0:|:
m eso_dir13|North:|:
m eso_wspeed13|0:|:
m eso_gust13|0:|:
m eso_barom13|0.00:|:
m eso_rrate13|0.00:|:
m eso_stat14|KILWOODR4:|:
m eso_loc14|Woodridge:|:
m eso_neigh14|Woodridge Neighborhood Weather:|:
m eso_url14|:|:
m eso_URLtext14|:|:
m eso_historyURL14|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWOODR4:|:
m eso_lon14|-88.05:|:
m eso_lat14|41.76:|:
m eso_time14|03/12 17:07:43:|:
m eso_temp14|61.2:|:
m eso_dew14|53.0:|:
m eso_rh14|74:|:
m eso_dir14|West:|:
m eso_wspeed14|6:|:
m eso_gust14|13:|:
m eso_barom14|29.73:|:
m eso_rrate14|0.00:|:
m eso_stat15|KILWOODR2:|:
m eso_loc15|Woodridge:|:
m eso_neigh15|Woodridge:|:
m eso_url15|http://www.tkhuman.com:|:
m eso_URLtext15|TKHuman.com:|:
m eso_historyURL15|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWOODR2:|:
m eso_lon15|-88.04:|:
m eso_lat15|41.77:|:
m eso_time15|03/12 17:19:59:|:
m eso_temp15|60.6:|:
m eso_dew15|51.0:|:
m eso_rh15|71:|:
m eso_dir15|WSW:|:
m eso_wspeed15|6:|:
m eso_gust15|11:|:
m eso_barom15|29.29:|:
m eso_rrate15|0.00:|:
m eso_stat16|KILAUROR20:|:
m eso_loc16|Aurora:|:
m eso_neigh16|Columbia Station:|:
m eso_url16|:|:
m eso_URLtext16|:|:
m eso_historyURL16|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILAUROR20:|:
m eso_lon16|-88.25:|:
m eso_lat16|41.72:|:
m eso_time16|03/12 17:20:00:|:
m eso_temp16|63.7:|:
m eso_dew16|55.0:|:
m eso_rh16|74:|:
m eso_dir16|West:|:
m eso_wspeed16|11:|:
m eso_gust16|11:|:
m eso_barom16|29.62:|:
m eso_rrate16|0.00:|:
m eso_stat17|KILGLENE1:|:
m eso_loc17|Glen Ellyn:|:
m eso_neigh17|Butterfield West:|:
m eso_url17|http://www.dupageweather.com:|:
m eso_URLtext17|:|:
m eso_historyURL17|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILGLENE1:|:
m eso_lon17|-88.06:|:
m eso_lat17|41.85:|:
m eso_time17|03/12 17:13:22:|:
m eso_temp17|60.4:|:
m eso_dew17|50.0:|:
m eso_rh17|68:|:
m eso_dir17|WNW:|:
m eso_wspeed17|8:|:
m eso_gust17|8:|:
m eso_barom17|29.77:|:
m eso_rrate17|0.00:|:
m eso_stat18|KILWHEAT9:|:
m eso_loc18|Wheaton:|:
m eso_neigh18|NE Wheaton:|:
m eso_url18|:|:
m eso_URLtext18|:|:
m eso_historyURL18|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT9:|:
m eso_lon18|-88.09:|:
m eso_lat18|41.87:|:
m eso_time18|03/12 17:20:06:|:
m eso_temp18|61.5:|:
m eso_dew18|52.0:|:
m eso_rh18|70:|:
m eso_dir18|West:|:
m eso_wspeed18|11:|:
m eso_gust18|12:|:
m eso_barom18|28.94:|:
m eso_rrate18|0.00:|:
m eso_stat19|KILDOWNE2:|:
m eso_loc19|Downers Grove:|:
m eso_neigh19|Prentiss Creek:|:
m eso_url19|:|:
m eso_URLtext19|:|:
m eso_historyURL19|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDOWNE2:|:
m eso_lon19|-88.04:|:
m eso_lat19|41.77:|:
m eso_time19|03/12 17:20:03:|:
m eso_temp19|60.8:|:
m eso_dew19|43.0:|:
m eso_rh19|51:|:
m eso_dir19|WSW:|:
m eso_wspeed19|10:|:
m eso_gust19|10:|:
m eso_barom19|29.73:|:
m eso_rrate19|0.00:|:
m eso_stat20|KILBOLIN10:|:
m eso_loc20|Bolingbrook:|:
m eso_neigh20|Bolingbrook/Clow Airport:|:
m eso_url20|:|:
m eso_URLtext20|:|:
m eso_historyURL20|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBOLIN10:|:
m eso_lon20|-88.11:|:
m eso_lat20|41.70:|:
m eso_time20|03/12 17:20:01:|:
m eso_temp20|61.9:|:
m eso_dew20|51.0:|:
m eso_rh20|68:|:
m eso_dir20|WSW:|:
m eso_wspeed20|3:|:
m eso_gust20|9:|:
m eso_barom20|29.06:|:
m eso_rrate20|0.00:|:
m eso_stat21|KILDOWNE3:|:
m eso_loc21|Downers Grove:|:
m eso_neigh21|Maple Woods West:|:
m eso_url21|:|:
m eso_URLtext21|:|:
m eso_historyURL21|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDOWNE3:|:
m eso_lon21|-88.03:|:
m eso_lat21|41.79:|:
m eso_time21|03/12 17:20:02:|:
m eso_temp21|61.4:|:
m eso_dew21|55.0:|:
m eso_rh21|79:|:
m eso_dir21|WNW:|:
m eso_wspeed21|7:|:
m eso_gust21|11:|:
m eso_barom21|29.81:|:
m eso_rrate21|0.00:|:
m eso_stat22|MD0023:|:
m eso_loc22|Glen Ellyn:|:
m eso_neigh22|APRSWXNET Glen Ellyn                 IL US:|:
m eso_url22|http://madis.noaa.gov:|:
m eso_URLtext22|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL22|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MD0023:|:
m eso_lon22|-88.05:|:
m eso_lat22|41.84:|:
m eso_time22|03/12 16:56:00:|:
m eso_temp22|61.0:|:
m eso_dew22|50.0:|:
m eso_rh22|68:|:
m eso_dir22|WNW:|:
m eso_wspeed22|8:|:
m eso_gust22|0:|:
m eso_barom22|29.77:|:
m eso_rrate22|0.00:|:
m eso_stat23|MAU162:|:
m eso_loc23|Glen Ellyn:|:
m eso_neigh23|APRSWXNET Glen Ellyn                   IL US:|:
m eso_url23|http://madis.noaa.gov:|:
m eso_URLtext23|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL23|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MAU162:|:
m eso_lon23|-88.08:|:
m eso_lat23|41.87:|:
m eso_time23|03/12 16:55:00:|:
m eso_temp23|59.0:|:
m eso_dew23|51.0:|:
m eso_rh23|76:|:
m eso_dir23|WNW:|:
m eso_wspeed23|4:|:
m eso_gust23|0:|:
m eso_barom23|29.80:|:
m eso_rrate23|0.00:|:
m eso_stat24|MC0472:|:
m eso_loc24|Glen Ellyn:|:
m eso_neigh24|APRSWXNET Wheaton                    IL US:|:
m eso_url24|http://madis.noaa.gov:|:
m eso_URLtext24|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL24|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MC0472:|:
m eso_lon24|-88.09:|:
m eso_lat24|41.88:|:
m eso_time24|03/12 17:02:00:|:
m eso_temp24|60.0:|:
m eso_dew24|40.0:|:
m eso_rh24|47:|:
m eso_dir24|East:|:
m eso_wspeed24|0:|:
m eso_gust24|0:|:
m eso_barom24|28.94:|:
m eso_rrate24|0.00:|:
m eso_stat25|KILWHEAT3:|:
m eso_loc25|Wheaton:|:
m eso_neigh25|Main and Geneva:|:
m eso_url25|http://www.mychicagogarden.com:|:
m eso_URLtext25|My Chicago Garden:|:
m eso_historyURL25|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT3:|:
m eso_lon25|-88.10:|:
m eso_lat25|41.88:|:
m eso_time25|03/12 17:19:46:|:
m eso_temp25|60.3:|:
m eso_dew25|40.0:|:
m eso_rh25|48:|:
m eso_dir25|NW:|:
m eso_wspeed25|6:|:
m eso_gust25|10:|:
m eso_barom25|29.76:|:
m eso_rrate25|0.00:|:
m eso_stat26|MC5020:|:
m eso_loc26|Glen Ellyn:|:
m eso_neigh26|APRSWXNET Wheaton                    IL US:|:
m eso_url26|http://madis.noaa.gov:|:
m eso_URLtext26|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL26|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MC5020:|:
m eso_lon26|-88.09:|:
m eso_lat26|41.88:|:
m eso_time26|03/12 17:03:00:|:
m eso_temp26|60.0:|:
m eso_dew26|50.0:|:
m eso_rh26|69:|:
m eso_dir26|WNW:|:
m eso_wspeed26|0:|:
m eso_gust26|5:|:
m eso_barom26|29.74:|:
m eso_rrate26|0.00:|:
m eso_stat27|KILBOLIN8:|:
m eso_loc27|Bolingbrook:|:
m eso_neigh27|Bolingbrook Neighborhood Weather:|:
m eso_url27|:|:
m eso_URLtext27|:|:
m eso_historyURL27|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBOLIN8:|:
m eso_lon27|-88.09:|:
m eso_lat27|41.69:|:
m eso_time27|03/12 17:20:05:|:
m eso_temp27|61.5:|:
m eso_dew27|52.0:|:
m eso_rh27|72:|:
m eso_dir27|WNW:|:
m eso_wspeed27|5:|:
m eso_gust27|6:|:
m eso_barom27|29.78:|:
m eso_rrate27|0.00:|:
m eso_stat28|KILWESTC8:|:
m eso_loc28|West Chicago:|:
m eso_neigh28|Ingalton Hills Estates:|:
m eso_url28|:|:
m eso_URLtext28|:|:
m eso_historyURL28|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWESTC8:|:
m eso_lon28|-88.20:|:
m eso_lat28|41.91:|:
m eso_time28|03/12 17:20:05:|:
m eso_temp28|60.0:|:
m eso_dew28|50.0:|:
m eso_rh28|69:|:
m eso_dir28|WSW:|:
m eso_wspeed28|13:|:
m eso_gust28|13:|:
m eso_barom28|29.84:|:
m eso_rrate28|0.00:|:
m eso_stat29|KILDARIE3:|:
m eso_loc29|Darien:|:
m eso_neigh29|Darien, Near 75th St and Lemont Rd (60561):|:
m eso_url29|:|:
m eso_URLtext29|:|:
m eso_historyURL29|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDARIE3:|:
m eso_lon29|-88.01:|:
m eso_lat29|41.74:|:
m eso_time29|03/12 17:18:32:|:
m eso_temp29|61.8:|:
m eso_dew29|49.0:|:
m eso_rh29|62:|:
m eso_dir29|NE:|:
m eso_wspeed29|1:|:
m eso_gust29|4:|:
m eso_barom29|29.65:|:
m eso_rrate29|0.00:|:
m eso_stat30|KILBATAV1:|:
m eso_loc30|Batavia:|:
m eso_neigh30|Tri-Cities on the Fox River:|:
m eso_url30|http://www.mdweather.com:|:
m eso_URLtext30|mdweather.com Weather Page:|:
m eso_historyURL30|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBATAV1:|:
m eso_lon30|-88.31:|:
m eso_lat30|41.85:|:
m eso_time30|03/12 17:19:59:|:
m eso_temp30|61.0:|:
m eso_dew30|50.0:|:
m eso_rh30|68:|:
m eso_dir30|WSW:|:
m eso_wspeed30|4:|:
m eso_gust30|20:|:
m eso_barom30|29.56:|:
m eso_rrate30|0.00:|:
m eso_stat31|KILWESTC5:|:
m eso_loc31|West Chicago:|:
m eso_neigh31|THEBROWNHOUSE.ORG:|:
m eso_url31|http://www.thebrownhouse.org:|:
m eso_URLtext31|http://www.thebrownhouse.org:|:
m eso_historyURL31|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWESTC5:|:
m eso_lon31|-88.21:|:
m eso_lat31|41.92:|:
m eso_time31|03/12 17:20:03:|:
m eso_temp31|62.1:|:
m eso_dew31|48.0:|:
m eso_rh31|60:|:
m eso_dir31|North:|:
m eso_wspeed31|0:|:
m eso_gust31|0:|:
m eso_barom31|29.77:|:
m eso_rrate31|0.00:|:
m eso_stat32|KILLOMBA5:|:
m eso_loc32|Lombard:|:
m eso_neigh32|Lombard (East of Sunset Knoll):|:
m eso_url32|http://www.knottme.com/weather/lombard:|:
m eso_URLtext32|Dee and Audreys Lombard Weather:|:
m eso_historyURL32|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLOMBA5:|:
m eso_lon32|-88.02:|:
m eso_lat32|41.87:|:
m eso_time32|03/12 17:20:01:|:
m eso_temp32|60.4:|:
m eso_dew32|49.0:|:
m eso_rh32|67:|:
m eso_dir32|West:|:
m eso_wspeed32|6:|:
m eso_gust32|15:|:
m eso_barom32|29.89:|:
m eso_rrate32|0.00:|:
m eso_stat33|KILGLENE5:|:
m eso_loc33|Glen Ellyn:|:
m eso_neigh33|Near North Ave. and Main St. Glen Ellyn:|:
m eso_url33|http://glenellyn.weather.patmulcahy.com:|:
m eso_URLtext33|Father Pat's Glen Ellyn Weather:|:
m eso_historyURL33|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILGLENE5:|:
m eso_lon33|-88.06:|:
m eso_lat33|41.90:|:
m eso_time33|03/12 17:19:53:|:
m eso_temp33|61.3:|:
m eso_dew33|46.0:|:
m eso_rh33|57:|:
m eso_dir33|ESE:|:
m eso_wspeed33|3:|:
m eso_gust33|3:|:
m eso_barom33|28.97:|:
m eso_rrate33|0.00:|:
m eso_stat34|KILOSWEG9:|:
m eso_loc34|Oswego:|:
m eso_neigh34|Farmington Lakes:|:
m eso_url34|:|:
m eso_URLtext34|:|:
m eso_historyURL34|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILOSWEG9:|:
m eso_lon34|-88.31:|:
m eso_lat34|41.71:|:
m eso_time34|03/12 17:08:17:|:
m eso_temp34|61.6:|:
m eso_dew34|51.0:|:
m eso_rh34|69:|:
m eso_dir34|NNW:|:
m eso_wspeed34|2:|:
m eso_gust34|5:|:
m eso_barom34|29.55:|:
m eso_rrate34|0.00:|:
m eso_stat35|KILPLAIN19:|:
m eso_loc35|Plainfield:|:
m eso_neigh35|North Plainfield:|:
m eso_url35|:|:
m eso_URLtext35|:|:
m eso_historyURL35|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILPLAIN19:|:
m eso_lon35|-88.21:|:
m eso_lat35|41.66:|:
m eso_time35|03/12 17:12:58:|:
m eso_temp35|61.4:|:
m eso_dew35|52.0:|:
m eso_rh35|71:|:
m eso_dir35|West:|:
m eso_wspeed35|10:|:
m eso_gust35|14:|:
m eso_barom35|29.77:|:
m eso_rrate35|0.00:|:
m eso_stat36|KILROMEO6:|:
m eso_loc36|Romeoville:|:
m eso_neigh36|RAH:|:
m eso_url36|http://rahweather.com/wxindex.php:|:
m eso_URLtext36|RAH Weather:|:
m eso_historyURL36|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILROMEO6:|:
m eso_lon36|-88.09:|:
m eso_lat36|41.66:|:
m eso_time36|03/12 17:20:05:|:
m eso_temp36|62.8:|:
m eso_dew36|47.0:|:
m eso_rh36|56:|:
m eso_dir36|West:|:
m eso_wspeed36|19:|:
m eso_gust36|27:|:
m eso_barom36|29.80:|:
m eso_rrate36|0.00:|:
m eso_stat37|KILSTCHA8:|:
m eso_loc37|St. Charles:|:
m eso_neigh37|Ronzheimer Ave. & S. Tyler Rd.:|:
m eso_url37|:|:
m eso_URLtext37|:|:
m eso_historyURL37|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILSTCHA8:|:
m eso_lon37|-88.29:|:
m eso_lat37|41.91:|:
m eso_time37|03/12 17:20:02:|:
m eso_temp37|61.2:|:
m eso_dew37|51.0:|:
m eso_rh37|69:|:
m eso_dir37|SSW:|:
m eso_wspeed37|8:|:
m eso_gust37|16:|:
m eso_barom37|29.74:|:
m eso_rrate37|0.00:|:
m eso_stat38|KILBATAV3:|:
m eso_loc38|Batavia:|:
m eso_neigh38|West Batavia:|:
m eso_url38|:|:
m eso_URLtext38|:|:
m eso_historyURL38|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBATAV3:|:
m eso_lon38|-88.36:|:
m eso_lat38|41.83:|:
m eso_time38|03/12 17:06:22:|:
m eso_temp38|60.2:|:
m eso_dew38|52.0:|:
m eso_rh38|75:|:
m eso_dir38|WSW:|:
m eso_wspeed38|5:|:
m eso_gust38|16:|:
m eso_barom38|29.74:|:
m eso_rrate38|0.00:|:
m eso_stat39|KILROMEO4:|:
m eso_loc39|Romeoville:|:
m eso_neigh39|Lakewood Estates:|:
m eso_url39|:|:
m eso_URLtext39|:|:
m eso_historyURL39|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILROMEO4:|:
m eso_lon39|-88.10:|:
m eso_lat39|41.65:|:
m eso_time39|03/12 17:15:04:|:
m eso_temp39|63.5:|:
m eso_dew39|35.0:|:
m eso_rh39|35:|:
m eso_dir39|WSW:|:
m eso_wspeed39|9:|:
m eso_gust39|17:|:
m eso_barom39|24.00:|:
m eso_rrate39|0.00:|:
m eso_stat40|KILLOMBA4:|:
m eso_loc40|Lombard:|:
m eso_neigh40|Madison Meadow:|:
m eso_url40|:|:
m eso_URLtext40|:|:
m eso_historyURL40|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLOMBA4:|:
m eso_lon40|-88.00:|:
m eso_lat40|41.88:|:
m eso_time40|03/12 17:15:05:|:
m eso_temp40|61.5:|:
m eso_dew40|38.0:|:
m eso_rh40|41:|:
m eso_dir40|NNW:|:
m eso_wspeed40|4:|:
m eso_gust40|4:|:
m eso_barom40|29.85:|:
m eso_rrate40|0.00:|:
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
