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
v xv001|WNW:|:
v hi001|360:|:
v ht001|12:09am:|:
v lo001|1:|:
v lt001|12:06am:|:
v va001|323:|:
v da001|0:|:
v ma001|0:|:
v ya001|0:|:
v vr001|19.7:|:
v rh001|145.9:|:
v rt001|7:04am:|:
v rl001|-323.5:|:
v rs001|5:32am:|:
v mh001|360:|:
v md001|2/2/13:|:
v ml001|1:|:
v me001|2/2/13:|:
v yh001|360:|:
v yd001|1/1/13:|:
v yl001|1:|:
v ye001|1/1/13:|:
v zh001|360:|:
v zt001|6:40pm:|:
v zl001|1:|:
v zs001|8:09am:|:
v hv001|124:|:
v dv001|212:|:
u ni001|&deg;:|:
v xv002|2:|:
v hi002|20:|:
v ht002|2:52am:|:
v lo002|0:|:
v lt002|12:01am:|:
v va002|5:|:
v da002|5:|:
v ma002|3:|:
v ya002|4:|:
v vr002|-2.9:|:
v rh002|10.9:|:
v rt002|6:06am:|:
v rl002|-6.1:|:
v rs002|6:23am:|:
v mh002|21:|:
v md002|2/3/13:|:
v ml002|0:|:
v me002|2/1/13:|:
v yh002|35:|:
v yd002|1/19/13:|:
v yl002|0:|:
v ye002|1/1/13:|:
v zh002|13:|:
v zt002|10:40pm:|:
v zl002|0:|:
v zs002|12:01am:|:
v hv002|0:|:
v dv002|3:|:
u ni002|mph:|:
v xv003|11:|:
v hi003|21:|:
v ht003|1:44am:|:
v lo003|1:|:
v lt003|2:00am:|:
v va003|10:|:
v da003|13:|:
v ma003|8:|:
v ya003|10:|:
v vr003|1.3:|:
v rh003|8.1:|:
v rt003|6:06am:|:
v rl003|-8.9:|:
v rs003|8:00am:|:
v mh003|21:|:
v md003|2/3/13:|:
v ml003|0:|:
v me003|2/1/13:|:
v yh003|35:|:
v yd003|1/19/13:|:
v yl003|0:|:
v ye003|1/1/13:|:
v zh003|14:|:
v zt003|10:40pm:|:
v zl003|0:|:
v zs003|1:00am:|:
v hv003|6:|:
v dv003|5:|:
u ni003|mph:|:
v xv004|39:|:
v hi004|41:|:
v ht004|12:00am:|:
v lo004|38:|:
v lt004|6:11am:|:
v va004|39:|:
v da004|40:|:
v ma004|37:|:
v ya004|38:|:
v vr004|0.0:|:
v rh004|0.4:|:
v rt004|6:47am:|:
v rl004|-1.0:|:
v rs004|6:11am:|:
v mh004|41:|:
v md004|2/7/13:|:
v ml004|34:|:
v me004|2/1/13:|:
v yh004|50:|:
v yd004|1/12/13:|:
v yl004|30:|:
v ye004|1/22/13:|:
v zh004|41:|:
v zt004|12:19pm:|:
v zl004|38:|:
v zs004|12:03am:|:
v hv004|40:|:
v dv004|-1:|:
u ni004|%:|:
v xv005|87:|:
v hi005|97:|:
v ht005|12:00am:|:
v lo005|87:|:
v lt005|10:06am:|:
v va005|88:|:
v da005|90:|:
v ma005|80:|:
v ya005|74:|:
v vr005|-0.7:|:
v rh005|1.0:|:
v rt005|5:37am:|:
v rl005|-1.0:|:
v rs005|10:06am:|:
v mh005|98:|:
v md005|2/7/13:|:
v ml005|55:|:
v me005|2/1/13:|:
v yh005|100:|:
v yd005|1/11/13:|:
v yl005|28:|:
v ye005|1/9/13:|:
v zh005|98:|:
v zt005|6:03pm:|:
v zl005|83:|:
v zs005|12:00am:|:
v hv005|96:|:
v dv005|-9:|:
u ni005|%:|:
v xv006|64.6:|:
v hi006|67.9:|:
v ht006|4:34am:|:
v lo006|64.0:|:
v lt006|8:52am:|:
v va006|64.4:|:
v da006|65.3:|:
v ma006|65.4:|:
v ya006|65.9:|:
v vr006|0.20:|:
v rh006|0.24:|:
v rt006|10:24am:|:
v rl006|-1.04:|:
v rs006|7:30am:|:
v mh006|68.4:|:
v md006|2/7/13:|:
v ml006|61.8:|:
v me006|2/1/13:|:
v yh006|68.9:|:
v yd006|1/10/13:|:
v yl006|61.8:|:
v ye006|2/1/13:|:
v zh006|68.4:|:
v zt006|3:44pm:|:
v zl006|64.0:|:
v zs006|1:47am:|:
v hv006|64.1:|:
v dv006|0.5:|:
u ni006|&deg;F:|:
v xv007|26.4:|:
v hi007|32.4:|:
v ht007|12:00am:|:
v lo007|22.9:|:
v lt007|4:23am:|:
v va007|26.2:|:
v da007|25.9:|:
v ma007|21.8:|:
v ya007|25.8:|:
v vr007|0.21:|:
v rh007|0.91:|:
v rt007|9:46am:|:
v rl007|0.00:|:
v rs007|:|:
v mh007|38.8:|:
v md007|2/6/13:|:
v ml007|-0.4:|:
v me007|2/1/13:|:
v yh007|61.6:|:
v yd007|1/29/13:|:
v yl007|-1.2:|:
v ye007|1/22/13:|:
v zh007|35.3:|:
v zt007|12:51pm:|:
v zl007|32.1:|:
v zs007|4:23am:|:
v hv007|33.7:|:
v dv007|-7.3:|:
u ni007|&deg;F:|:
v xv008|29.53:|:
v hi008|29.54:|:
v ht008|10:27am:|:
v lo008|29.19:|:
v lt008|12:00am:|:
v va008|29.51:|:
v da008|29.37:|:
v ma008|29.31:|:
v ya008|29.35:|:
v vr008|0.027:|:
v rh008|0.041:|:
v rt008|7:20am:|:
v rl008|0.000:|:
v rs008|:|:
v mh008|29.64:|:
v md008|2/1/13:|:
v ml008|29.06:|:
v me008|2/5/13:|:
v yh008|29.87:|:
v yd008|1/24/13:|:
v yl008|28.43:|:
v ye008|1/30/13:|:
v zh008|29.35:|:
v zt008|1:42am:|:
v zl008|29.10:|:
v zs008|4:10pm:|:
v hv008|29.24:|:
v dv008|0.29:|:
u ni008|in:|:
v xv009|3.78:|:
v hi009|3.78:|:
v ht009|12:00am:|:
v lo009|3.78:|:
v lt009|12:00am:|:
v va009|3.78:|:
v da009|3.78:|:
v ma009|3.06:|:
v ya009|1.27:|:
v vr009|0.000:|:
v rh009|0.000:|:
v rt009|:|:
v rl009|0.000:|:
v rs009|:|:
v mh009|3.78:|:
v md009|2/7/13:|:
v ml009|2.93:|:
v me009|2/1/13:|:
v yh009|3.78:|:
v yd009|2/7/13:|:
v yl009|0.01:|:
v ye009|1/5/13:|:
v zh009|3.78:|:
v zt009|11:43pm:|:
v zl009|3.08:|:
v zs009|12:00am:|:
v hv009|3.26:|:
v dv009|0.52:|:
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
v md010|2/1/13:|:
v ml010|0.0:|:
v me010|2/1/13:|:
v yh010|0.0:|:
v yd010|1/1/13:|:
v yl010|0.0:|:
v ye010|1/1/13:|:
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
v md011|2/1/13:|:
v ml011|0:|:
v me011|2/1/13:|:
v yh011|0:|:
v yd011|1/1/13:|:
v yl011|0:|:
v ye011|1/1/13:|:
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
v md012|2/1/13:|:
v ml012|0.0:|:
v me012|2/1/13:|:
v yh012|0.0:|:
v yd012|1/1/13:|:
v yl012|0.0:|:
v ye012|1/1/13:|:
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
v md013|2/1/13:|:
v ml013|0:|:
v me013|2/1/13:|:
v yh013|0:|:
v yd013|1/1/13:|:
v yl013|0:|:
v ye013|1/1/13:|:
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
v md014|2/1/13:|:
v ml014|0.0:|:
v me014|2/1/13:|:
v yh014|0.0:|:
v yd014|1/1/13:|:
v yl014|0.0:|:
v ye014|1/1/13:|:
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
v md015|2/1/13:|:
v ml015|0:|:
v me015|2/1/13:|:
v yh015|0:|:
v yd015|1/1/13:|:
v yl015|0:|:
v ye015|1/1/13:|:
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
v lt016|12:00am:|:
v va016|0.00:|:
v da016|0.00:|:
v ma016|0.01:|:
v ya016|0.01:|:
v vr016|0.001:|:
v rh016|0.001:|:
v rt016|10:00am:|:
v rl016|0.000:|:
v rs016|:|:
v mh016|0.05:|:
v md016|2/6/13:|:
v ml016|0.00:|:
v me016|2/1/13:|:
v yh016|0.07:|:
v yd016|1/9/13:|:
v yl016|0.00:|:
v ye016|1/1/13:|:
v zh016|0.00:|:
v zt016|1:00pm:|:
v zl016|0.00:|:
v zs016|12:00am:|:
v hv016|0.00:|:
v dv016|0.00:|:
u ni016|in:|:
v xv017|0.0:|:
v hi017|0.0:|:
v ht017|12:00am:|:
v lo017|0.0:|:
v lt017|12:00am:|:
v va017|0.0:|:
v da017|0.0:|:
v ma017|0.1:|:
v ya017|0.1:|:
v vr017|0.00:|:
v rh017|0.00:|:
v rt017|:|:
v rl017|0.00:|:
v rs017|:|:
v mh017|1.9:|:
v md017|2/6/13:|:
v ml017|0.0:|:
v me017|2/1/13:|:
v yh017|1.9:|:
v yd017|2/6/13:|:
v yl017|0.0:|:
v ye017|1/1/13:|:
v zh017|0.0:|:
v zt017|12:00am:|:
v zl017|0.0:|:
v zs017|12:00am:|:
v hv017|0.0:|:
v dv017|0.0:|:
u ni017|:|:
v xv018|93:|:
v hi018|93:|:
v ht018|10:33am:|:
v lo018|0:|:
v lt018|12:00am:|:
v va018|63:|:
v da018|16:|:
v ma018|55:|:
v ya018|56:|:
v vr018|29.6:|:
v rh018|43.0:|:
v rt018|9:15am:|:
v rl018|-0.3:|:
v rs018|10:09am:|:
v mh018|821:|:
v md018|2/3/13:|:
v ml018|0:|:
v me018|2/1/13:|:
v yh018|821:|:
v yd018|2/3/13:|:
v yl018|0:|:
v ye018|1/1/13:|:
v zh018|95:|:
v zt018|12:06pm:|:
v zl018|0:|:
v zs018|12:00am:|:
v hv018|49:|:
v dv018|44:|:
u ni018|W/sqm:|:
v xv019|24.8:|:
v hi019|32.2:|:
v ht019|12:01am:|:
v lo019|10.7:|:
v lt019|3:40am:|:
v va019|20.8:|:
v da019|20.1:|:
v ma019|17.8:|:
v ya019|21.7:|:
v vr019|4.00:|:
v rh019|7.63:|:
v rt019|6:18am:|:
v rl019|-5.91:|:
v rs019|6:06am:|:
v mh019|38.8:|:
v md019|2/6/13:|:
v ml019|-19.4:|:
v me019|2/1/13:|:
v yh019|61.6:|:
v yd019|1/29/13:|:
v yl019|-19.4:|:
v ye019|2/1/13:|:
v zh019|35.3:|:
v zt019|12:51pm:|:
v zl019|23.0:|:
v zs019|11:53pm:|:
v hv019|33.7:|:
v dv019|-10.7:|:
u ni019|&deg;F:|:
v xv020|64.4:|:
v hi020|67.7:|:
v ht020|4:34am:|:
v lo020|63.8:|:
v lt020|8:52am:|:
v va020|64.2:|:
v da020|65.2:|:
v ma020|65.0:|:
v ya020|65.6:|:
v vr020|0.20:|:
v rh020|0.24:|:
v rt020|10:24am:|:
v rl020|-1.02:|:
v rs020|7:30am:|:
v mh020|68.3:|:
v md020|2/7/13:|:
v ml020|61.4:|:
v me020|2/1/13:|:
v yh020|69.2:|:
v yd020|1/11/13:|:
v yl020|61.3:|:
v ye020|1/23/13:|:
v zh020|68.3:|:
v zt020|3:44pm:|:
v zl020|63.8:|:
v zs020|1:47am:|:
v hv020|64.0:|:
v dv020|0.4:|:
u ni020|&deg;F:|:
v xv021|31.6:|:
v hi021|39.1:|:
v ht021|12:00am:|:
v lo021|28.2:|:
v lt021|4:23am:|:
v va021|31.5:|:
v da021|31.5:|:
v ma021|26.1:|:
v ya021|29.3:|:
v vr021|0.10:|:
v rh021|0.90:|:
v rt021|9:46am:|:
v rl021|0.00:|:
v rs021|:|:
v mh021|42.0:|:
v md021|2/7/13:|:
v ml021|2.6:|:
v me021|2/1/13:|:
v yh021|67.5:|:
v yd021|1/29/13:|:
v yl021|0.9:|:
v ye021|1/22/13:|:
v zh021|42.0:|:
v zt021|12:51pm:|:
v zl021|37.3:|:
v zs021|12:10am:|:
v hv021|40.3:|:
v dv021|-8.6:|:
u ni021|&deg;F:|:
v xv022|23.1:|:
v hi022|31.6:|:
v ht022|12:00am:|:
v lo022|19.9:|:
v lt022|4:23am:|:
v va022|23.1:|:
v da022|23.3:|:
v ma022|16.4:|:
v ya022|18.1:|:
v vr022|0.01:|:
v rh022|0.88:|:
v rt022|9:46am:|:
v rl022|0.00:|:
v rs022|:|:
v mh022|34.5:|:
v md022|2/7/13:|:
v ml022|-7.9:|:
v me022|2/1/13:|:
v yh022|59.3:|:
v yd022|1/29/13:|:
v yl022|-11.0:|:
v ye022|1/21/13:|:
v zh022|34.5:|:
v zt022|12:51pm:|:
v zl022|28.1:|:
v zs022|12:10am:|:
v hv022|32.7:|:
v dv022|-9.6:|:
u ni022|&deg;F:|:
v xv023|30.35:|:
v hi023|30.35:|:
v ht023|10:27am:|:
v lo023|29.99:|:
v lt023|12:00am:|:
v va023|30.32:|:
v da023|30.18:|:
v ma023|30.11:|:
v ya023|30.16:|:
v vr023|0.027:|:
v rh023|0.043:|:
v rt023|7:20am:|:
v rl023|0.000:|:
v rs023|:|:
v mh023|30.45:|:
v md023|2/1/13:|:
v ml023|29.86:|:
v me023|2/5/13:|:
v yh023|30.70:|:
v yd023|1/24/13:|:
v yl023|29.21:|:
v ye023|1/30/13:|:
v zh023|30.16:|:
v zt023|1:42am:|:
v zl023|29.90:|:
v zs023|4:10pm:|:
v hv023|30.05:|:
v dv023|0.30:|:
u ni023|in:|:
v xv024|359:|:
u ni024|ft:|:
v xv025|834:|:
u ni025|ft:|:
v xv026|-1765:|:
u ni026|ft:|:
v xv027|27.2:|:
u ni027|&deg;F:|:
v xv028|0.12:|:
u ni028|in:|:
v xv121|0.00:|:
u ni121|in:|:
v xv122|0.00:|:
u ni122|in:|:
v xv123|0.52:|:
u ni123|in:|:
v xv124|0.000:|:
u ni124|in/hr:|:
v xv125|52:|:
u ni125|miles:|:
v xv126|16.3:|:
u ni126|&deg;F:|:
v xv127|0.0:|:
u ni127|&deg;F:|:
v xv128|28:|:
u ni128|:|:
v xv129|0.85:|:
u ni129|in:|:
v xv130|287.1:|:
u ni130|&deg;F:|:
v xv131|0.0:|:
u ni131|&deg;F:|:
v xv132|516:|:
u ni132|miles:|:
v xv133|1352.0:|:
u ni133|&deg;F:|:
v xv134|0.0:|:
u ni134|&deg;F:|:
v xv135|3322:|:
u ni135|miles:|:
v st136|---:|:
u ni136|:|:
v st137|Uncomfortably Cold:|:
u ni137|:|:
v st138|Mostly clear and cooler:|:
u ni138|:|:
v st139|Rising:|:
u ni139|:|:
v st140|Rising:|:
u ni140|:|:
v st141|Light Air:|:
u ni141|:|:
v st142|2/8/13:|:
u ni142|:|:
v st143|10:35am:|:
u ni143|:|:
v st144|6:54am:|:
u ni144|:|:
v st145|5:15pm:|:
u ni145|:|:
v st146|5:26am:|:
u ni146|:|:
v st147|3:50pm:|:
u ni147|:|:
v st148|West North West:|:
u ni148|:|:
m oon_percent|4%:|:
m oon_day|28:|:
v ervws|V14.01:|:
w station|Davis Instruments Vantage Pro2:|:
w sdescription|NapervilleWeather.com:|:
w slocation|Naperville, IL:|:
w staturl|http://www.ambientweather.com/dawest.html:|:
w slong|-88.1200027:|:
w slat|41.7900009:|:
w orld_id|us:|:
t emp_color|8153ff:|:
t emp_color_hi|5371ff:|:
t emp_color_lo|ad53f7:|:
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
a lmhi1|60:|:
a lmdatehi1|1999:|:
a lmlo1|-7:|:
a lmdatelo1|2007:|:
a lmnormhi1|34:|:
a lmnormlo1|18:|:
a lmytd1|3.90:|:
a lmmtd1|0.90:|:
t emp_rec_hi1|53ff53:|:
t emp_rec_lo1|ffffff:|:
t emp_norm_hi1|5386ff:|:
t emp_norm_lo1|ec53d3:|:
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
f orecast_day1_1|Fri:|:
f orecast_hi1_1|32:|:
f orecast_lo1_1|16:|:
f orecast_conds1_1|Partly Cloudy:|:
f orecast_icon1_1|partlycloudy:|:
t emp_for_hi1_1|536cff:|:
t emp_for_lo1_1|ff53d3:|:
f orecast_day1_2|Sat:|:
f orecast_hi1_2|37:|:
f orecast_lo1_2|28:|:
f orecast_conds1_2|Partly Cloudy:|:
f orecast_icon1_2|partlycloudy:|:
t emp_for_hi1_2|53acff:|:
t emp_for_lo1_2|6c53ff:|:
f orecast_day1_3|Sun:|:
f orecast_hi1_3|46:|:
f orecast_lo1_3|27:|:
f orecast_conds1_3|Chance Sleet:|:
f orecast_icon1_3|chancesleet:|:
t emp_for_hi1_3|53d3b9:|:
t emp_for_lo1_3|7953ff:|:
f orecast_day1_4|Mon:|:
f orecast_hi1_4|37:|:
f orecast_lo1_4|23:|:
f orecast_conds1_4|Mostly Cloudy:|:
f orecast_icon1_4|mostlycloudy:|:
t emp_for_hi1_4|53acff:|:
t emp_for_lo1_4|ac53f9:|:
f orecast_day1_5|Tue:|:
f orecast_hi1_5|30:|:
f orecast_lo1_5|19:|:
f orecast_conds1_5|Partly Cloudy:|:
f orecast_icon1_5|partlycloudy:|:
t emp_for_hi1_5|5353ff:|:
t emp_for_lo1_5|df53d3:|:
f orecast_nexrad2|LOT:|:
f orecast_country2|US:|:
f orecast_radregion2|a4:|:
f orecast_tzname2|America/Chicago:|:
f orecast_wmo2|:|:
f orecast_day2_1|Fri:|:
f orecast_hi2_1|32:|:
f orecast_lo2_1|16:|:
f orecast_conds2_1|Partly Cloudy:|:
f orecast_icon2_1|partlycloudy:|:
t emp_for_hi2_1|ffffff:|:
t emp_for_lo2_1|ffffff:|:
f orecast_day2_2|Sat:|:
f orecast_hi2_2|37:|:
f orecast_lo2_2|28:|:
f orecast_conds2_2|Partly Cloudy:|:
f orecast_icon2_2|partlycloudy:|:
t emp_for_hi2_2|ffffff:|:
t emp_for_lo2_2|ffffff:|:
f orecast_day2_3|Sun:|:
f orecast_hi2_3|46:|:
f orecast_lo2_3|27:|:
f orecast_conds2_3|Chance Sleet:|:
f orecast_icon2_3|chancesleet:|:
t emp_for_hi2_3|ffffff:|:
t emp_for_lo2_3|ffffff:|:
f orecast_day2_4|Mon:|:
f orecast_hi2_4|37:|:
f orecast_lo2_4|23:|:
f orecast_conds2_4|Mostly Cloudy:|:
f orecast_icon2_4|mostlycloudy:|:
t emp_for_hi2_4|ffffff:|:
t emp_for_lo2_4|ffffff:|:
f orecast_day2_5|Tue:|:
f orecast_hi2_5|30:|:
f orecast_lo2_5|19:|:
f orecast_conds2_5|Partly Cloudy:|:
f orecast_icon2_5|partlycloudy:|:
t emp_for_hi2_5|ffffff:|:
t emp_for_lo2_5|ffffff:|:
f orecast_nexrad3|LOT:|:
f orecast_country3|US:|:
f orecast_radregion3|a4:|:
f orecast_tzname3|America/Chicago:|:
f orecast_wmo3|:|:
f orecast_day3_1|Fri:|:
f orecast_hi3_1|32:|:
f orecast_lo3_1|16:|:
f orecast_conds3_1|Partly Cloudy:|:
f orecast_icon3_1|partlycloudy:|:
t emp_for_hi3_1|ff53d3:|:
t emp_for_lo3_1|ffffff:|:
f orecast_day3_2|Sat:|:
f orecast_hi3_2|37:|:
f orecast_lo3_2|28:|:
f orecast_conds3_2|Partly Cloudy:|:
f orecast_icon3_2|partlycloudy:|:
t emp_for_hi3_2|6c53ff:|:
t emp_for_lo3_2|ffffff:|:
f orecast_day3_3|Sun:|:
f orecast_hi3_3|46:|:
f orecast_lo3_3|27:|:
f orecast_conds3_3|Chance Sleet:|:
f orecast_icon3_3|chancesleet:|:
t emp_for_hi3_3|7953ff:|:
t emp_for_lo3_3|ffffff:|:
f orecast_day3_4|Mon:|:
f orecast_hi3_4|37:|:
f orecast_lo3_4|23:|:
f orecast_conds3_4|Mostly Cloudy:|:
f orecast_icon3_4|mostlycloudy:|:
t emp_for_hi3_4|ac53f9:|:
t emp_for_lo3_4|ffffff:|:
f orecast_day3_5|Tue:|:
f orecast_hi3_5|30:|:
f orecast_lo3_5|19:|:
f orecast_conds3_5|Partly Cloudy:|:
f orecast_icon3_5|partlycloudy:|:
t emp_for_hi3_5|df53d3:|:
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
m eso_time1|02/08 10:49:57:|:
m eso_temp1|29.3:|:
m eso_dew1|24.0:|:
m eso_rh1|81:|:
m eso_dir1|NW:|:
m eso_wspeed1|0:|:
m eso_gust1|0:|:
m eso_barom1|29.44:|:
m eso_rrate1|0.00:|:
m eso_stat2|KILNAPER21:|:
m eso_loc2|Naperville:|:
m eso_neigh2|Saybrook:|:
m eso_url2|:|:
m eso_URLtext2|:|:
m eso_historyURL2|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER21:|:
m eso_lon2|-88.14:|:
m eso_lat2|41.79:|:
m eso_time2|02/08 10:30:16:|:
m eso_temp2|26.3:|:
m eso_dew2|24.0:|:
m eso_rh2|90:|:
m eso_dir2|NNW:|:
m eso_wspeed2|4:|:
m eso_gust2|12:|:
m eso_barom2|30.34:|:
m eso_rrate2|0.00:|:
m eso_stat3|KILNAPER19:|:
m eso_loc3|Naperville:|:
m eso_neigh3|Brookdale Lakes:|:
m eso_url3|:|:
m eso_URLtext3|:|:
m eso_historyURL3|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER19:|:
m eso_lon3|-88.20:|:
m eso_lat3|41.78:|:
m eso_time3|02/08 10:50:04:|:
m eso_temp3|26.5:|:
m eso_dew3|24.0:|:
m eso_rh3|89:|:
m eso_dir3|West:|:
m eso_wspeed3|1:|:
m eso_gust3|8:|:
m eso_barom3|30.25:|:
m eso_rrate3|0.00:|:
m eso_stat4|KILNAPER7:|:
m eso_loc4|Naperville:|:
m eso_neigh4|NapervilleWeather.com Near Ogden and Naper Blvd:|:
m eso_url4|http://NapervilleWeather.com:|:
m eso_URLtext4|NapervilleWeather.com Web Page:|:
m eso_historyURL4|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER7:|:
m eso_lon4|-88.12:|:
m eso_lat4|41.79:|:
m eso_time4|02/08 10:50:01:|:
m eso_temp4|26.6:|:
m eso_dew4|23.0:|:
m eso_rh4|87:|:
m eso_dir4|NW:|:
m eso_wspeed4|3:|:
m eso_gust4|11:|:
m eso_barom4|30.35:|:
m eso_rrate4|0.00:|:
m eso_stat5|KILWARRE2:|:
m eso_loc5|Warrenville:|:
m eso_neigh5|Warrenville Neighborhood Weather:|:
m eso_url5|http://weather.binkerd.net:|:
m eso_URLtext5|Binkerd Weather:|:
m eso_historyURL5|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWARRE2:|:
m eso_lon5|-88.17:|:
m eso_lat5|41.82:|:
m eso_time5|02/08 10:50:02:|:
m eso_temp5|27.0:|:
m eso_dew5|24.0:|:
m eso_rh5|90:|:
m eso_dir5|NW:|:
m eso_wspeed5|0:|:
m eso_gust5|0:|:
m eso_barom5|30.34:|:
m eso_rrate5|0.00:|:
m eso_stat6|KILNAPER23:|:
m eso_loc6|Naperville:|:
m eso_neigh6|Naper Blvd at BNSF RR:|:
m eso_url6|:|:
m eso_URLtext6|:|:
m eso_historyURL6|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER23:|:
m eso_lon6|-88.11:|:
m eso_lat6|41.79:|:
m eso_time6|02/08 10:50:02:|:
m eso_temp6|29.3:|:
m eso_dew6|23.0:|:
m eso_rh6|78:|:
m eso_dir6|NE:|:
m eso_wspeed6|2:|:
m eso_gust6|2:|:
m eso_barom6|30.25:|:
m eso_rrate6|0.00:|:
m eso_stat7|KILNAPER9:|:
m eso_loc7|Naperville:|:
m eso_neigh7|75th St. /  Modaff Area:|:
m eso_url7|http://mistybeagle.com/wx/:|:
m eso_URLtext7|Misty The Weather Beagle:|:
m eso_historyURL7|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER9:|:
m eso_lon7|-88.14:|:
m eso_lat7|41.74:|:
m eso_time7|02/08 10:50:01:|:
m eso_temp7|27.8:|:
m eso_dew7|25.0:|:
m eso_rh7|88:|:
m eso_dir7|West:|:
m eso_wspeed7|2:|:
m eso_gust7|9:|:
m eso_barom7|30.30:|:
m eso_rrate7|0.00:|:
m eso_stat8|KILWHEAT8:|:
m eso_loc8|Wheaton:|:
m eso_neigh8|Wheaton Sanitary District:|:
m eso_url8|http://www.wsd.dst.il.us/:|:
m eso_URLtext8|:|:
m eso_historyURL8|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT8:|:
m eso_lon8|-88.14:|:
m eso_lat8|41.85:|:
m eso_time8|02/08 10:46:10:|:
m eso_temp8|28.0:|:
m eso_dew8|26.0:|:
m eso_rh8|91:|:
m eso_dir8|North:|:
m eso_wspeed8|2:|:
m eso_gust8|8:|:
m eso_barom8|30.32:|:
m eso_rrate8|0.00:|:
m eso_stat9|KILLOMBA6:|:
m eso_loc9|Wheaton:|:
m eso_neigh9|Danada West:|:
m eso_url9|:|:
m eso_URLtext9|:|:
m eso_historyURL9|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLOMBA6:|:
m eso_lon9|-88.11:|:
m eso_lat9|41.84:|:
m eso_time9|02/08 10:49:12:|:
m eso_temp9|26.6:|:
m eso_dew9|24.0:|:
m eso_rh9|90:|:
m eso_dir9|NNE:|:
m eso_wspeed9|1:|:
m eso_gust9|2:|:
m eso_barom9|30.32:|:
m eso_rrate9|0.00:|:
m eso_stat10|KILLISLE3:|:
m eso_loc10|Lisle:|:
m eso_neigh10|Main and Short Lisle:|:
m eso_url10|:|:
m eso_URLtext10|:|:
m eso_historyURL10|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLISLE3:|:
m eso_lon10|-88.07:|:
m eso_lat10|41.79:|:
m eso_time10|02/08 10:45:02:|:
m eso_temp10|28.2:|:
m eso_dew10|23.0:|:
m eso_rh10|81:|:
m eso_dir10|NNW:|:
m eso_wspeed10|0:|:
m eso_gust10|0:|:
m eso_barom10|30.11:|:
m eso_rrate10|0.00:|:
m eso_stat11|KILAUROR16:|:
m eso_loc11|Aurora:|:
m eso_neigh11|Oakhurst:|:
m eso_url11|:|:
m eso_URLtext11|:|:
m eso_historyURL11|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILAUROR16:|:
m eso_lon11|-88.24:|:
m eso_lat11|41.75:|:
m eso_time11|02/08 10:49:52:|:
m eso_temp11|28.2:|:
m eso_dew11|25.0:|:
m eso_rh11|89:|:
m eso_dir11|North:|:
m eso_wspeed11|0:|:
m eso_gust11|0:|:
m eso_barom11|29.55:|:
m eso_rrate11|0.00:|:
m eso_stat12|KILNAPER22:|:
m eso_loc12|Naperville:|:
m eso_neigh12|Willow Ridge:|:
m eso_url12|:|:
m eso_URLtext12|:|:
m eso_historyURL12|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER22:|:
m eso_lon12|-88.18:|:
m eso_lat12|41.72:|:
m eso_time12|02/08 10:43:27:|:
m eso_temp12|28.0:|:
m eso_dew12|24.0:|:
m eso_rh12|84:|:
m eso_dir12|NE:|:
m eso_wspeed12|1:|:
m eso_gust12|0:|:
m eso_barom12|30.14:|:
m eso_rrate12|0.00:|:
m eso_stat13|KILWHEAT5:|:
m eso_loc13|Wheaton:|:
m eso_neigh13|N9ABF - West Wheaton:|:
m eso_url13|http://n9abf.com:|:
m eso_URLtext13|N9ABF.com Amateur Radio:|:
m eso_historyURL13|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT5:|:
m eso_lon13|-88.13:|:
m eso_lat13|41.87:|:
m eso_time13|02/08 10:39:00:|:
m eso_temp13|26.1:|:
m eso_dew13|24.0:|:
m eso_rh13|90:|:
m eso_dir13|NW:|:
m eso_wspeed13|0:|:
m eso_gust13|0:|:
m eso_barom13|30.25:|:
m eso_rrate13|0.00:|:
m eso_stat14|MD1973:|:
m eso_loc14|Downers Grove:|:
m eso_neigh14|APRSWXNET Woodridge                  IL US:|:
m eso_url14|http://madis.noaa.gov:|:
m eso_URLtext14|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL14|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MD1973:|:
m eso_lon14|-88.06:|:
m eso_lat14|41.77:|:
m eso_time14|02/08 10:14:00:|:
m eso_temp14|26.0:|:
m eso_dew14|23.0:|:
m eso_rh14|90:|:
m eso_dir14|NW:|:
m eso_wspeed14|2:|:
m eso_gust14|6:|:
m eso_barom14|30.22:|:
m eso_rrate14|0.00:|:
m eso_stat15|KILBATAV5:|:
m eso_loc15|Batavia:|:
m eso_neigh15|Fermi National Accelerator Laboratory:|:
m eso_url15|http://www.fnal.gov:|:
m eso_URLtext15|Fermilab:|:
m eso_historyURL15|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBATAV5:|:
m eso_lon15|-88.24:|:
m eso_lat15|41.85:|:
m eso_time15|02/08 10:37:18:|:
m eso_temp15|26.3:|:
m eso_dew15|23.0:|:
m eso_rh15|88:|:
m eso_dir15|ESE:|:
m eso_wspeed15|5:|:
m eso_gust15|12:|:
m eso_barom15|30.31:|:
m eso_rrate15|0.00:|:
m eso_stat16|MUP431:|:
m eso_loc16|West Chicago:|:
m eso_neigh16|MesoWest WINFL1                           IL US UPR:|:
m eso_url16|http://madis.noaa.gov:|:
m eso_URLtext16|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL16|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MUP431:|:
m eso_lon16|-88.18:|:
m eso_lat16|41.88:|:
m eso_time16|02/08 09:45:00:|:
m eso_temp16|23.0:|:
m eso_dew16|0.0:|:
m eso_rh16|0:|:
m eso_dir16|North:|:
m eso_wspeed16|0:|:
m eso_gust16|0:|:
m eso_barom16|0.00:|:
m eso_rrate16|0.00:|:
m eso_stat17|KILWHEAT7:|:
m eso_loc17|Wheaton:|:
m eso_neigh17|South Wheaton:|:
m eso_url17|:|:
m eso_URLtext17|:|:
m eso_historyURL17|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT7:|:
m eso_lon17|-88.08:|:
m eso_lat17|41.86:|:
m eso_time17|02/08 10:50:05:|:
m eso_temp17|27.0:|:
m eso_dew17|24.0:|:
m eso_rh17|90:|:
m eso_dir17|NW:|:
m eso_wspeed17|0:|:
m eso_gust17|6:|:
m eso_barom17|30.27:|:
m eso_rrate17|0.00:|:
m eso_stat18|KILWOODR4:|:
m eso_loc18|Woodridge:|:
m eso_neigh18|Woodridge Neighborhood Weather:|:
m eso_url18|:|:
m eso_URLtext18|:|:
m eso_historyURL18|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWOODR4:|:
m eso_lon18|-88.05:|:
m eso_lat18|41.76:|:
m eso_time18|02/08 10:36:07:|:
m eso_temp18|28.2:|:
m eso_dew18|26.0:|:
m eso_rh18|92:|:
m eso_dir18|NNW:|:
m eso_wspeed18|0:|:
m eso_gust18|0:|:
m eso_barom18|30.30:|:
m eso_rrate18|0.00:|:
m eso_stat19|KILWOODR2:|:
m eso_loc19|Woodridge:|:
m eso_neigh19|Woodridge:|:
m eso_url19|http://www.tkhuman.com:|:
m eso_URLtext19|TKHuman.com:|:
m eso_historyURL19|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWOODR2:|:
m eso_lon19|-88.04:|:
m eso_lat19|41.77:|:
m eso_time19|02/08 10:50:15:|:
m eso_temp19|27.7:|:
m eso_dew19|24.0:|:
m eso_rh19|84:|:
m eso_dir19|NNW:|:
m eso_wspeed19|0:|:
m eso_gust19|0:|:
m eso_barom19|29.78:|:
m eso_rrate19|0.00:|:
m eso_stat20|KILWOODR1:|:
m eso_loc20|Woodridge:|:
m eso_neigh20|75th Street/ Janes Ave:|:
m eso_url20|:|:
m eso_URLtext20|:|:
m eso_historyURL20|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWOODR1:|:
m eso_lon20|-88.05:|:
m eso_lat20|41.75:|:
m eso_time20|02/08 10:50:13:|:
m eso_temp20|27.0:|:
m eso_dew20|24.0:|:
m eso_rh20|88:|:
m eso_dir20|North:|:
m eso_wspeed20|0:|:
m eso_gust20|0:|:
m eso_barom20|29.99:|:
m eso_rrate20|0.00:|:
m eso_stat21|KILGLENE6:|:
m eso_loc21|Glen Ellyn:|:
m eso_neigh21|MANOR WOODS AREA:|:
m eso_url21|:|:
m eso_URLtext21|:|:
m eso_historyURL21|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILGLENE6:|:
m eso_lon21|-88.08:|:
m eso_lat21|41.86:|:
m eso_time21|02/08 10:49:47:|:
m eso_temp21|29.1:|:
m eso_dew21|23.0:|:
m eso_rh21|79:|:
m eso_dir21|NW:|:
m eso_wspeed21|0:|:
m eso_gust21|0:|:
m eso_barom21|29.90:|:
m eso_rrate21|0.00:|:
m eso_stat22|KILAUROR21:|:
m eso_loc22|Aurora:|:
m eso_neigh22|Hunters Run:|:
m eso_url22|:|:
m eso_URLtext22|:|:
m eso_historyURL22|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILAUROR21:|:
m eso_lon22|-88.26:|:
m eso_lat22|41.73:|:
m eso_time22|02/08 10:49:41:|:
m eso_temp22|27.9:|:
m eso_dew22|18.0:|:
m eso_rh22|67:|:
m eso_dir22|NW:|:
m eso_wspeed22|4:|:
m eso_gust22|10:|:
m eso_barom22|30.30:|:
m eso_rrate22|0.00:|:
m eso_stat23|KILAUROR20:|:
m eso_loc23|Aurora:|:
m eso_neigh23|Columbia Station:|:
m eso_url23|:|:
m eso_URLtext23|:|:
m eso_historyURL23|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILAUROR20:|:
m eso_lon23|-88.25:|:
m eso_lat23|41.72:|:
m eso_time23|02/08 10:49:29:|:
m eso_temp23|27.5:|:
m eso_dew23|26.0:|:
m eso_rh23|95:|:
m eso_dir23|SW:|:
m eso_wspeed23|0:|:
m eso_gust23|0:|:
m eso_barom23|30.13:|:
m eso_rrate23|0.00:|:
m eso_stat24|KILWHEAT9:|:
m eso_loc24|Wheaton:|:
m eso_neigh24|NE Wheaton:|:
m eso_url24|:|:
m eso_URLtext24|:|:
m eso_historyURL24|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT9:|:
m eso_lon24|-88.09:|:
m eso_lat24|41.87:|:
m eso_time24|02/08 10:49:47:|:
m eso_temp24|26.6:|:
m eso_dew24|21.0:|:
m eso_rh24|79:|:
m eso_dir24|NW:|:
m eso_wspeed24|0:|:
m eso_gust24|0:|:
m eso_barom24|29.43:|:
m eso_rrate24|0.00:|:
m eso_stat25|KILBOLIN15:|:
m eso_loc25|Bolingbrook:|:
m eso_neigh25|Winding Meadows:|:
m eso_url25|http://besphoto.com/:|:
m eso_URLtext25|BesPhoto.com:|:
m eso_historyURL25|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBOLIN15:|:
m eso_lon25|-88.13:|:
m eso_lat25|41.70:|:
m eso_time25|02/08 10:49:35:|:
m eso_temp25|28.1:|:
m eso_dew25|24.0:|:
m eso_rh25|84:|:
m eso_dir25|NW:|:
m eso_wspeed25|9:|:
m eso_gust25|9:|:
m eso_barom25|29.64:|:
m eso_rrate25|0.00:|:
m eso_stat26|KILDOWNE2:|:
m eso_loc26|Downers Grove:|:
m eso_neigh26|Prentiss Creek:|:
m eso_url26|:|:
m eso_URLtext26|:|:
m eso_historyURL26|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDOWNE2:|:
m eso_lon26|-88.04:|:
m eso_lat26|41.77:|:
m eso_time26|02/08 10:50:09:|:
m eso_temp26|28.9:|:
m eso_dew26|14.0:|:
m eso_rh26|53:|:
m eso_dir26|WNW:|:
m eso_wspeed26|5:|:
m eso_gust26|8:|:
m eso_barom26|31.86:|:
m eso_rrate26|0.00:|:
m eso_stat27|KILBOLIN10:|:
m eso_loc27|Bolingbrook:|:
m eso_neigh27|Near Clow Airport:|:
m eso_url27|:|:
m eso_URLtext27|:|:
m eso_historyURL27|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBOLIN10:|:
m eso_lon27|-88.11:|:
m eso_lat27|41.70:|:
m eso_time27|02/08 10:50:10:|:
m eso_temp27|28.3:|:
m eso_dew27|24.0:|:
m eso_rh27|85:|:
m eso_dir27|WSW:|:
m eso_wspeed27|3:|:
m eso_gust27|6:|:
m eso_barom27|29.56:|:
m eso_rrate27|0.00:|:
m eso_stat28|KILDOWNE3:|:
m eso_loc28|Downers Grove:|:
m eso_neigh28|Maple Woods West:|:
m eso_url28|:|:
m eso_URLtext28|:|:
m eso_historyURL28|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDOWNE3:|:
m eso_lon28|-88.03:|:
m eso_lat28|41.79:|:
m eso_time28|02/08 10:49:44:|:
m eso_temp28|29.1:|:
m eso_dew28|28.0:|:
m eso_rh28|97:|:
m eso_dir28|NE:|:
m eso_wspeed28|0:|:
m eso_gust28|0:|:
m eso_barom28|30.30:|:
m eso_rrate28|0.00:|:
m eso_stat29|KILBOLIN12:|:
m eso_loc29|Bolingbrook:|:
m eso_neigh29|UnderTheThunder HQ:|:
m eso_url29|http://www.weatherlink.com/user/polarpal99/:|:
m eso_URLtext29|KC9BST-1 (WeatherLink):|:
m eso_historyURL29|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBOLIN12:|:
m eso_lon29|-88.11:|:
m eso_lat29|41.70:|:
m eso_time29|02/08 10:45:55:|:
m eso_temp29|28.6:|:
m eso_dew29|25.0:|:
m eso_rh29|87:|:
m eso_dir29|North:|:
m eso_wspeed29|6:|:
m eso_gust29|6:|:
m eso_barom29|30.32:|:
m eso_rrate29|0.00:|:
m eso_stat30|KILBOLIN13:|:
m eso_loc30|Bolingbrook:|:
m eso_neigh30|UnderTheThunder.org Van:|:
m eso_url30|http://www.underthethunder.org/live/KC9BST-2/WeatherLink/Bulletin_Vantage.html:|:
m eso_URLtext30|UnderTheThunder.org Van:|:
m eso_historyURL30|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBOLIN13:|:
m eso_lon30|-88.11:|:
m eso_lat30|41.70:|:
m eso_time30|02/08 10:46:32:|:
m eso_temp30|27.5:|:
m eso_dew30|24.0:|:
m eso_rh30|87:|:
m eso_dir30|NW:|:
m eso_wspeed30|6:|:
m eso_gust30|7:|:
m eso_barom30|30.26:|:
m eso_rrate30|0.00:|:
m eso_stat31|MAU162:|:
m eso_loc31|Glen Ellyn:|:
m eso_neigh31|APRSWXNET Glen Ellyn                   IL US:|:
m eso_url31|http://madis.noaa.gov:|:
m eso_URLtext31|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL31|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MAU162:|:
m eso_lon31|-88.08:|:
m eso_lat31|41.87:|:
m eso_time31|02/08 10:13:00:|:
m eso_temp31|25.0:|:
m eso_dew31|23.0:|:
m eso_rh31|91:|:
m eso_dir31|West:|:
m eso_wspeed31|1:|:
m eso_gust31|0:|:
m eso_barom31|30.35:|:
m eso_rrate31|0.00:|:
m eso_stat32|MC0472:|:
m eso_loc32|Glen Ellyn:|:
m eso_neigh32|APRSWXNET Wheaton                    IL US:|:
m eso_url32|http://madis.noaa.gov:|:
m eso_URLtext32|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL32|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MC0472:|:
m eso_lon32|-88.09:|:
m eso_lat32|41.88:|:
m eso_time32|02/08 09:32:00:|:
m eso_temp32|26.0:|:
m eso_dew32|19.0:|:
m eso_rh32|73:|:
m eso_dir32|ENE:|:
m eso_wspeed32|0:|:
m eso_gust32|0:|:
m eso_barom32|29.41:|:
m eso_rrate32|0.00:|:
m eso_stat33|MC5020:|:
m eso_loc33|Glen Ellyn:|:
m eso_neigh33|APRSWXNET Wheaton                    IL US:|:
m eso_url33|http://madis.noaa.gov:|:
m eso_URLtext33|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL33|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MC5020:|:
m eso_lon33|-88.09:|:
m eso_lat33|41.88:|:
m eso_time33|02/08 10:13:00:|:
m eso_temp33|26.0:|:
m eso_dew33|23.0:|:
m eso_rh33|89:|:
m eso_dir33|NW:|:
m eso_wspeed33|0:|:
m eso_gust33|1:|:
m eso_barom33|30.21:|:
m eso_rrate33|0.00:|:
m eso_stat34|KILNAPER4:|:
m eso_loc34|Naperville:|:
m eso_neigh34|1.1 mi.West. of Rt 59 & 103rd St.:|:
m eso_url34|:|:
m eso_URLtext34|:|:
m eso_historyURL34|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER4:|:
m eso_lon34|-88.23:|:
m eso_lat34|41.70:|:
m eso_time34|02/08 10:49:14:|:
m eso_temp34|26.1:|:
m eso_dew34|22.0:|:
m eso_rh34|83:|:
m eso_dir34|NW:|:
m eso_wspeed34|5:|:
m eso_gust34|5:|:
m eso_barom34|30.27:|:
m eso_rrate34|0.00:|:
m eso_stat35|KILBOLIN8:|:
m eso_loc35|Bolingbrook:|:
m eso_neigh35|Bolingbrook Neighborhood Weather:|:
m eso_url35|:|:
m eso_URLtext35|:|:
m eso_historyURL35|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBOLIN8:|:
m eso_lon35|-88.09:|:
m eso_lat35|41.69:|:
m eso_time35|02/08 10:50:10:|:
m eso_temp35|27.5:|:
m eso_dew35|24.0:|:
m eso_rh35|88:|:
m eso_dir35|NW:|:
m eso_wspeed35|4:|:
m eso_gust35|5:|:
m eso_barom35|30.34:|:
m eso_rrate35|0.00:|:
m eso_stat36|KILWESTC8:|:
m eso_loc36|West Chicago:|:
m eso_neigh36|Ingalton Hills Estates:|:
m eso_url36|:|:
m eso_URLtext36|:|:
m eso_historyURL36|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWESTC8:|:
m eso_lon36|-88.20:|:
m eso_lat36|41.91:|:
m eso_time36|02/08 10:50:08:|:
m eso_temp36|25.0:|:
m eso_dew36|24.0:|:
m eso_rh36|94:|:
m eso_dir36|WNW:|:
m eso_wspeed36|0:|:
m eso_gust36|0:|:
m eso_barom36|30.45:|:
m eso_rrate36|0.00:|:
m eso_stat37|KILDARIE3:|:
m eso_loc37|Darien:|:
m eso_neigh37|Darien, Near 75th St and Lemont Rd (60561):|:
m eso_url37|:|:
m eso_URLtext37|:|:
m eso_historyURL37|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDARIE3:|:
m eso_lon37|-88.01:|:
m eso_lat37|41.74:|:
m eso_time37|02/08 10:49:37:|:
m eso_temp37|28.9:|:
m eso_dew37|25.0:|:
m eso_rh37|86:|:
m eso_dir37|North:|:
m eso_wspeed37|4:|:
m eso_gust37|8:|:
m eso_barom37|30.15:|:
m eso_rrate37|0.00:|:
m eso_stat38|KILWESTC11:|:
m eso_loc38|West Chicago:|:
m eso_neigh38|Wayne Eastgates:|:
m eso_url38|http://www.lightsofillinois.com:|:
m eso_URLtext38|Lights of Illinois Homepage:|:
m eso_historyURL38|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWESTC11:|:
m eso_lon38|-88.16:|:
m eso_lat38|41.92:|:
m eso_time38|02/08 10:09:12:|:
m eso_temp38|27.2:|:
m eso_dew38|23.0:|:
m eso_rh38|84:|:
m eso_dir38|West:|:
m eso_wspeed38|2:|:
m eso_gust38|2:|:
m eso_barom38|29.85:|:
m eso_rrate38|0.00:|:
m eso_stat39|KILAUROR12:|:
m eso_loc39|Aurora:|:
m eso_neigh39|Aurora Neighborhood Weather:|:
m eso_url39|:|:
m eso_URLtext39|:|:
m eso_historyURL39|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILAUROR12:|:
m eso_lon39|-88.32:|:
m eso_lat39|41.76:|:
m eso_time39|02/08 10:38:04:|:
m eso_temp39|26.4:|:
m eso_dew39|23.0:|:
m eso_rh39|88:|:
m eso_dir39|South:|:
m eso_wspeed39|8:|:
m eso_gust39|0:|:
m eso_barom39|30.34:|:
m eso_rrate39|0.00:|:
m eso_stat40|KILBATAV1:|:
m eso_loc40|Batavia:|:
m eso_neigh40|Tri-Cities on the Fox River:|:
m eso_url40|http://www.mdweather.com:|:
m eso_URLtext40|mdweather.com Weather Page:|:
m eso_historyURL40|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBATAV1:|:
m eso_lon40|-88.31:|:
m eso_lat40|41.85:|:
m eso_time40|02/08 10:49:40:|:
m eso_temp40|27.0:|:
m eso_dew40|24.0:|:
m eso_rh40|88:|:
m eso_dir40|NNW:|:
m eso_wspeed40|3:|:
m eso_gust40|6:|:
m eso_barom40|30.32:|:
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
