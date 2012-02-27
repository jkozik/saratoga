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
v xv001|W:|:
v hi001|357:|:
v ht001|12:27pm:|:
v lo001|1:|:
v lt001|10:54am:|:
v va001|250:|:
v da001|0:|:
v ma001|0:|:
v ya001|0:|:
v vr001|19.1:|:
v rh001|128.9:|:
v rt001|9:10pm:|:
v rl001|-145.7:|:
v rs001|6:46pm:|:
v mh001|360:|:
v md001|2/1/12:|:
v ml001|1:|:
v me001|2/1/12:|:
v yh001|360:|:
v yd001|1/1/12:|:
v yl001|0:|:
v ye001|1/9/12:|:
v zh001|360:|:
v zt001|2:01pm:|:
v zl001|1:|:
v zs001|4:12am:|:
v hv001|258:|:
v dv001|46:|:
u ni001|&deg;:|:
v xv002|3:|:
v hi002|34:|:
v ht002|2:19pm:|:
v lo002|0:|:
v lt002|12:00am:|:
v va002|4:|:
v da002|5:|:
v ma002|4:|:
v ya002|4:|:
v vr002|-0.8:|:
v rh002|12.3:|:
v rt002|6:47pm:|:
v rl002|-5.6:|:
v rs002|7:09pm:|:
v mh002|34:|:
v md002|2/26/12:|:
v ml002|0:|:
v me002|2/1/12:|:
v yh002|34:|:
v yd002|2/26/12:|:
v yl002|0:|:
v ye002|1/1/12:|:
v zh002|19:|:
v zt002|4:08am:|:
v zl002|0:|:
v zs002|12:18am:|:
v hv002|0:|:
v dv002|3:|:
u ni002|mph:|:
v xv003|11:|:
v hi003|34:|:
v ht003|2:19pm:|:
v lo003|0:|:
v lt003|12:00am:|:
v va003|9:|:
v da003|13:|:
v ma003|9:|:
v ya003|9:|:
v vr003|2.5:|:
v rh003|10.9:|:
v rt003|5:55pm:|:
v rl003|-9.6:|:
v rs003|7:30pm:|:
v mh003|34:|:
v md003|2/26/12:|:
v ml003|0:|:
v me003|2/1/12:|:
v yh003|34:|:
v yd003|2/26/12:|:
v yl003|0:|:
v ye003|1/3/12:|:
v zh003|19:|:
v zt003|4:08am:|:
v zl003|0:|:
v zs003|7:20pm:|:
v hv003|0:|:
v dv003|11:|:
u ni003|mph:|:
v xv004|33:|:
v hi004|34:|:
v ht004|1:48pm:|:
v lo004|30:|:
v lt004|12:00am:|:
v va004|33:|:
v da004|32:|:
v ma004|32:|:
v ya004|32:|:
v vr004|-0.3:|:
v rh004|1.0:|:
v rt004|6:44pm:|:
v rl004|-0.9:|:
v rs004|9:09pm:|:
v mh004|42:|:
v md004|2/24/12:|:
v ml004|25:|:
v me004|2/10/12:|:
v yh004|42:|:
v yd004|2/24/12:|:
v yl004|24:|:
v ye004|1/19/12:|:
v zh004|32:|:
v zt004|5:53am:|:
v zl004|28:|:
v zs004|1:08am:|:
v hv004|30:|:
v dv004|3:|:
u ni004|%:|:
v xv005|64:|:
v hi005|89:|:
v ht005|2:06am:|:
v lo005|38:|:
v lt005|3:49pm:|:
v va005|60:|:
v da005|63:|:
v ma005|76:|:
v ya005|76:|:
v vr005|4.0:|:
v rh005|8.1:|:
v rt005|7:37pm:|:
v rl005|-0.4:|:
v rs005|6:54pm:|:
v mh005|99:|:
v md005|2/16/12:|:
v ml005|32:|:
v me005|2/18/12:|:
v yh005|100:|:
v yd005|1/23/12:|:
v yl005|32:|:
v ye005|2/18/12:|:
v zh005|86:|:
v zt005|11:53pm:|:
v zl005|55:|:
v zs005|2:27pm:|:
v hv005|83:|:
v dv005|-19:|:
u ni005|%:|:
v xv006|71.8:|:
v hi006|72.1:|:
v ht006|9:09pm:|:
v lo006|67.5:|:
v lt006|4:10am:|:
v va006|71.8:|:
v da006|70.0:|:
v ma006|71.4:|:
v ya006|71.0:|:
v vr006|-0.05:|:
v rh006|0.31:|:
v rt006|9:09pm:|:
v rl006|-0.06:|:
v rs006|10:15pm:|:
v mh006|75.0:|:
v md006|2/3/12:|:
v ml006|67.4:|:
v me006|2/13/12:|:
v yh006|75.4:|:
v yd006|1/27/12:|:
v yl006|65.6:|:
v ye006|1/20/12:|:
v zh006|71.4:|:
v zt006|12:00am:|:
v zl006|67.9:|:
v zs006|3:17am:|:
v hv006|70.4:|:
v dv006|1.4:|:
u ni006|&deg;F:|:
v xv007|41.8:|:
v hi007|52.9:|:
v ht007|3:40pm:|:
v lo007|15.7:|:
v lt007|12:41am:|:
v va007|43.1:|:
v da007|35.6:|:
v ma007|31.7:|:
v ya007|30.5:|:
v vr007|-1.27:|:
v rh007|0.00:|:
v rt007|5:55pm:|:
v rl007|-3.27:|:
v rs007|7:39pm:|:
v mh007|52.9:|:
v md007|2/26/12:|:
v ml007|8.3:|:
v me007|2/11/12:|:
v yh007|56.7:|:
v yd007|1/11/12:|:
v yl007|2.8:|:
v ye007|1/20/12:|:
v zh007|28.2:|:
v zt007|2:43pm:|:
v zl007|15.7:|:
v zs007|11:53pm:|:
v hv007|16.4:|:
v dv007|25.5:|:
u ni007|&deg;F:|:
v xv008|29.30:|:
v hi008|29.55:|:
v ht008|12:57am:|:
v lo008|29.21:|:
v lt008|4:18pm:|:
v va008|29.28:|:
v da008|29.37:|:
v ma008|29.33:|:
v ya008|29.27:|:
v vr008|0.023:|:
v rh008|0.033:|:
v rt008|9:16pm:|:
v rl008|-0.003:|:
v rs008|8:48pm:|:
v mh008|29.69:|:
v md008|2/3/12:|:
v ml008|28.62:|:
v me008|2/24/12:|:
v yh008|29.69:|:
v yd008|2/3/12:|:
v yl008|28.59:|:
v ye008|1/23/12:|:
v zh008|29.57:|:
v zt008|8:16pm:|:
v zl008|29.19:|:
v zs008|12:00am:|:
v hv008|29.55:|:
v dv008|-0.25:|:
u ni008|in:|:
v xv009|2.32:|:
v hi009|2.32:|:
v ht009|3:49pm:|:
v lo009|2.21:|:
v lt009|12:00am:|:
v va009|2.32:|:
v da009|2.26:|:
v ma009|1.62:|:
v ya009|1.00:|:
v vr009|0.000:|:
v rh009|0.000:|:
v rt009|:|:
v rl009|0.000:|:
v rs009|:|:
v mh009|2.32:|:
v md009|2/26/12:|:
v ml009|1.40:|:
v me009|2/1/12:|:
v yh009|2.32:|:
v yd009|2/26/12:|:
v yl009|0.01:|:
v ye009|1/1/12:|:
v zh009|2.21:|:
v zt009|4:13pm:|:
v zl009|2.15:|:
v zs009|12:00am:|:
v hv009|2.21:|:
v dv009|0.11:|:
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
v md010|2/1/12:|:
v ml010|0.0:|:
v me010|2/1/12:|:
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
v md011|2/1/12:|:
v ml011|0:|:
v me011|2/1/12:|:
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
v md012|2/1/12:|:
v ml012|0.0:|:
v me012|2/1/12:|:
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
v md013|2/1/12:|:
v ml013|0:|:
v me013|2/1/12:|:
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
v md014|2/1/12:|:
v ml014|0.0:|:
v me014|2/1/12:|:
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
v md015|2/1/12:|:
v ml015|0:|:
v me015|2/1/12:|:
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
v xv016|0.10:|:
v hi016|0.10:|:
v ht016|10:00pm:|:
v lo016|0.00:|:
v lt016|12:00am:|:
v va016|0.10:|:
v da016|0.04:|:
v ma016|0.02:|:
v ya016|0.01:|:
v vr016|0.002:|:
v rh016|0.004:|:
v rt016|7:00pm:|:
v rl016|0.000:|:
v rs016|:|:
v mh016|0.10:|:
v md016|2/26/12:|:
v ml016|0.00:|:
v me016|2/1/12:|:
v yh016|0.10:|:
v yd016|2/26/12:|:
v yl016|0.00:|:
v ye016|1/1/12:|:
v zh016|0.06:|:
v zt016|7:00pm:|:
v zl016|0.00:|:
v zs016|12:00am:|:
v hv016|0.06:|:
v dv016|0.04:|:
u ni016|in:|:
v xv017|0.0:|:
v hi017|2.5:|:
v ht017|11:54am:|:
v lo017|0.0:|:
v lt017|12:00am:|:
v va017|0.0:|:
v da017|0.5:|:
v ma017|0.2:|:
v ya017|0.1:|:
v vr017|0.00:|:
v rh017|0.00:|:
v rt017|:|:
v rl017|0.00:|:
v rs017|:|:
v mh017|2.5:|:
v md017|2/26/12:|:
v ml017|0.0:|:
v me017|2/1/12:|:
v yh017|2.5:|:
v yd017|2/26/12:|:
v yl017|0.0:|:
v ye017|1/1/12:|:
v zh017|2.2:|:
v zt017|12:11pm:|:
v zl017|0.0:|:
v zs017|12:00am:|:
v hv017|0.0:|:
v dv017|0.0:|:
u ni017|:|:
v xv018|0:|:
v hi018|705:|:
v ht018|11:53am:|:
v lo018|0:|:
v lt018|12:00am:|:
v va018|0:|:
v da018|167:|:
v ma018|82:|:
v ya018|66:|:
v vr018|0.0:|:
v rh018|0.0:|:
v rt018|:|:
v rl018|0.0:|:
v rs018|:|:
v mh018|835:|:
v md018|2/16/12:|:
v ml018|0:|:
v me018|2/1/12:|:
v yh018|835:|:
v yd018|2/16/12:|:
v yl018|0:|:
v ye018|1/1/12:|:
v zh018|733:|:
v zt018|12:00pm:|:
v zl018|0:|:
v zs018|12:00am:|:
v hv018|0:|:
v dv018|0:|:
u ni018|W/sqm:|:
v xv019|40.4:|:
v hi019|52.9:|:
v ht019|3:42pm:|:
v lo019|8.5:|:
v lt019|2:00am:|:
v va019|41.2:|:
v da019|32.8:|:
v ma019|28.6:|:
v ya019|27.2:|:
v vr019|-0.82:|:
v rh019|1.88:|:
v rt019|5:58pm:|:
v rl019|-6.73:|:
v rs019|9:16pm:|:
v mh019|52.9:|:
v md019|2/26/12:|:
v ml019|-10.3:|:
v me019|2/11/12:|:
v yh019|56.7:|:
v yd019|1/11/12:|:
v yl019|-10.7:|:
v ye019|1/19/12:|:
v zh019|28.2:|:
v zt019|3:41pm:|:
v zl019|3.6:|:
v zs019|6:51am:|:
v hv019|16.4:|:
v dv019|24.1:|:
u ni019|&deg;F:|:
v xv020|71.2:|:
v hi020|71.6:|:
v ht020|9:10pm:|:
v lo020|66.8:|:
v lt020|4:10am:|:
v va020|71.3:|:
v da020|69.4:|:
v ma020|70.7:|:
v ya020|70.3:|:
v vr020|-0.06:|:
v rh020|0.34:|:
v rt020|7:34pm:|:
v rl020|-0.09:|:
v rs020|10:15pm:|:
v mh020|74.5:|:
v md020|2/3/12:|:
v ml020|66.4:|:
v me020|2/13/12:|:
v yh020|74.9:|:
v yd020|1/27/12:|:
v yl020|64.5:|:
v ye020|1/20/12:|:
v zh020|70.6:|:
v zt020|12:00am:|:
v zl020|67.1:|:
v zs020|3:17am:|:
v hv020|69.6:|:
v dv020|1.6:|:
u ni020|&deg;F:|:
v xv021|44.1:|:
v hi021|52.8:|:
v ht021|3:40pm:|:
v lo021|20.9:|:
v lt021|12:41am:|:
v va021|44.9:|:
v da021|37.9:|:
v ma021|35.7:|:
v ya021|34.3:|:
v vr021|-0.84:|:
v rh021|0.00:|:
v rt021|5:55pm:|:
v rl021|-2.55:|:
v rs021|7:45pm:|:
v mh021|52.8:|:
v md021|2/26/12:|:
v ml021|9.8:|:
v me021|2/11/12:|:
v yh021|59.4:|:
v yd021|1/31/12:|:
v yl021|4.2:|:
v ye021|1/19/12:|:
v zh021|30.0:|:
v zt021|2:43pm:|:
v zl021|20.8:|:
v zs021|11:53pm:|:
v hv021|21.0:|:
v dv021|23.1:|:
u ni021|&deg;F:|:
v xv022|30.5:|:
v hi022|31.0:|:
v ht022|10:22pm:|:
v lo022|12.5:|:
v lt022|12:41am:|:
v va022|30.1:|:
v da022|23.1:|:
v ma022|24.5:|:
v ya022|23.2:|:
v vr022|0.43:|:
v rh022|1.08:|:
v rt022|10:22pm:|:
v rl022|-0.69:|:
v rs022|6:49pm:|:
v mh022|39.1:|:
v md022|2/21/12:|:
v ml022|-4.1:|:
v me022|2/11/12:|:
v yh022|48.2:|:
v yd022|1/31/12:|:
v yl022|-12.0:|:
v ye022|1/19/12:|:
v zh022|18.8:|:
v zt022|12:36am:|:
v zl022|10.9:|:
v zs022|6:29am:|:
v hv022|12.2:|:
v dv022|18.5:|:
u ni022|&deg;F:|:
v xv023|30.11:|:
v hi023|30.37:|:
v ht023|12:57am:|:
v lo023|30.02:|:
v lt023|4:18pm:|:
v va023|30.08:|:
v da023|30.18:|:
v ma023|30.14:|:
v ya023|30.07:|:
v vr023|0.024:|:
v rh023|0.034:|:
v rt023|9:16pm:|:
v rl023|-0.003:|:
v rs023|8:48pm:|:
v mh023|30.51:|:
v md023|2/3/12:|:
v ml023|29.41:|:
v me023|2/24/12:|:
v yh023|30.51:|:
v yd023|2/3/12:|:
v yl023|29.38:|:
v ye023|1/23/12:|:
v zh023|30.38:|:
v zt023|8:16pm:|:
v zl023|30.00:|:
v zs023|12:00am:|:
v hv023|30.37:|:
v dv023|-0.26:|:
u ni023|in:|:
v xv024|578:|:
u ni024|ft:|:
v xv025|2817:|:
u ni025|ft:|:
v xv026|-418:|:
u ni026|ft:|:
v xv027|42.9:|:
u ni027|&deg;F:|:
v xv028|0.17:|:
u ni028|in:|:
v xv121|0.11:|:
u ni121|in:|:
v xv122|0.00:|:
u ni122|in:|:
v xv123|0.11:|:
u ni123|in:|:
v xv124|0.000:|:
u ni124|in/hr:|:
v xv125|100:|:
u ni125|miles:|:
v xv126|24.1:|:
u ni126|&deg;F:|:
v xv127|0.0:|:
u ni127|&deg;F:|:
v xv128|5:|:
u ni128|:|:
v xv129|0.92:|:
u ni129|in:|:
v xv130|772.1:|:
u ni130|&deg;F:|:
v xv131|0.0:|:
u ni131|&deg;F:|:
v xv132|2069:|:
u ni132|miles:|:
v xv133|1781.7:|:
u ni133|&deg;F:|:
v xv134|0.0:|:
u ni134|&deg;F:|:
v xv135|4683:|:
u ni135|miles:|:
v st136|---:|:
u ni136|:|:
v st137|Cool:|:
u ni137|:|:
v st138|Mostly clear with little temperature change:|:
u ni138|:|:
v st139|Rising:|:
u ni139|:|:
v st140|Rising:|:
u ni140|:|:
v st141|Light Air:|:
u ni141|:|:
v st142|2/26/12:|:
u ni142|:|:
v st143|10:35pm:|:
u ni143|:|:
v st144|6:30am:|:
u ni144|:|:
v st145|5:36pm:|:
u ni145|:|:
v st146|8:23am:|:
u ni146|:|:
v st147|10:42pm:|:
u ni147|:|:
v st148|West:|:
u ni148|:|:
m oon_percent|23%:|:
m oon_day|5:|:
v ervws|V14.01:|:
w station|Davis Instruments Vantage Pro2:|:
w sdescription|NapervilleWeather.com:|:
w slocation|Naperville, IL:|:
w staturl|http://www.ambientweather.com/dawest.html:|:
w slong|-88.1200027:|:
w slat|41.7900009:|:
w orld_id|us:|:
t emp_color|53d3ff:|:
t emp_color_hi|53f753:|:
t emp_color_lo|ff53d3:|:
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
a lmdatehi1|1998:|:
a lmlo1|3:|:
a lmdatelo1|2003:|:
a lmnormhi1|40:|:
a lmnormlo1|23:|:
a lmytd1|1.88:|:
a lmmtd1|0.50:|:
t emp_rec_hi1|9fff53:|:
t emp_rec_lo1|ffffff:|:
t emp_norm_hi1|53d3ff:|:
t emp_norm_lo1|ac53f9:|:
c limate_cconds1|Clear
:|:
c limate_icon1|clear:|:
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
f orecast_hi1_1|36:|:
f orecast_lo1_1|12:|:
f orecast_conds1_1|Clear:|:
f orecast_icon1_1|clear:|:
t emp_for_hi1_1|539fff:|:
t emp_for_lo1_1|ff53d3:|:
f orecast_day1_2|Tue:|:
f orecast_hi1_2|39:|:
f orecast_lo1_2|36:|:
f orecast_conds1_2|Chance Rain:|:
f orecast_icon1_2|chancerain:|:
t emp_for_hi1_2|53c6ff:|:
t emp_for_lo1_2|539fff:|:
f orecast_day1_3|Wed:|:
f orecast_hi1_3|48:|:
f orecast_lo1_3|27:|:
f orecast_conds1_3|Chance Rain:|:
f orecast_icon1_3|chancerain:|:
t emp_for_hi1_3|53d386:|:
t emp_for_lo1_3|7953ff:|:
f orecast_day1_4|Thu:|:
f orecast_hi1_4|37:|:
f orecast_lo1_4|28:|:
f orecast_conds1_4|Partly Cloudy:|:
f orecast_icon1_4|partlycloudy:|:
t emp_for_hi1_4|53acff:|:
t emp_for_lo1_4|6c53ff:|:
f orecast_day1_5|Fri:|:
f orecast_hi1_5|45:|:
f orecast_lo1_5|27:|:
f orecast_conds1_5|Chance T-storms:|:
f orecast_icon1_5|chancetstorms:|:
t emp_for_hi1_5|53d3d2:|:
t emp_for_lo1_5|7953ff:|:
f orecast_nexrad2|LOT:|:
f orecast_country2|US:|:
f orecast_radregion2|a4:|:
f orecast_tzname2|America/Chicago:|:
f orecast_wmo2|:|:
f orecast_day2_1|Mon:|:
f orecast_hi2_1|36:|:
f orecast_lo2_1|12:|:
f orecast_conds2_1|Clear:|:
f orecast_icon2_1|clear:|:
t emp_for_hi2_1|ffffff:|:
t emp_for_lo2_1|ffffff:|:
f orecast_day2_2|Tue:|:
f orecast_hi2_2|39:|:
f orecast_lo2_2|36:|:
f orecast_conds2_2|Chance Rain:|:
f orecast_icon2_2|chancerain:|:
t emp_for_hi2_2|ffffff:|:
t emp_for_lo2_2|ffffff:|:
f orecast_day2_3|Wed:|:
f orecast_hi2_3|48:|:
f orecast_lo2_3|27:|:
f orecast_conds2_3|Chance Rain:|:
f orecast_icon2_3|chancerain:|:
t emp_for_hi2_3|ffffff:|:
t emp_for_lo2_3|ffffff:|:
f orecast_day2_4|Thu:|:
f orecast_hi2_4|37:|:
f orecast_lo2_4|28:|:
f orecast_conds2_4|Partly Cloudy:|:
f orecast_icon2_4|partlycloudy:|:
t emp_for_hi2_4|ffffff:|:
t emp_for_lo2_4|ffffff:|:
f orecast_day2_5|Fri:|:
f orecast_hi2_5|45:|:
f orecast_lo2_5|27:|:
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
f orecast_hi3_1|36:|:
f orecast_lo3_1|12:|:
f orecast_conds3_1|Clear:|:
f orecast_icon3_1|clear:|:
t emp_for_hi3_1|ff53d3:|:
t emp_for_lo3_1|ffffff:|:
f orecast_day3_2|Tue:|:
f orecast_hi3_2|39:|:
f orecast_lo3_2|36:|:
f orecast_conds3_2|Chance Rain:|:
f orecast_icon3_2|chancerain:|:
t emp_for_hi3_2|539fff:|:
t emp_for_lo3_2|ffffff:|:
f orecast_day3_3|Wed:|:
f orecast_hi3_3|48:|:
f orecast_lo3_3|27:|:
f orecast_conds3_3|Chance Rain:|:
f orecast_icon3_3|chancerain:|:
t emp_for_hi3_3|7953ff:|:
t emp_for_lo3_3|ffffff:|:
f orecast_day3_4|Thu:|:
f orecast_hi3_4|37:|:
f orecast_lo3_4|28:|:
f orecast_conds3_4|Partly Cloudy:|:
f orecast_icon3_4|partlycloudy:|:
t emp_for_hi3_4|6c53ff:|:
t emp_for_lo3_4|ffffff:|:
f orecast_day3_5|Fri:|:
f orecast_hi3_5|45:|:
f orecast_lo3_5|27:|:
f orecast_conds3_5|Chance T-storms:|:
f orecast_icon3_5|chancetstorms:|:
t emp_for_hi3_5|7953ff:|:
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
m eso_time1|02/26 22:20:07:|:
m eso_temp1|42.3:|:
m eso_dew1|31.0:|:
m eso_rh1|65:|:
m eso_dir1|WNW:|:
m eso_wspeed1|4:|:
m eso_gust1|8:|:
m eso_barom1|29.21:|:
m eso_rrate1|0.00:|:
m eso_stat2|KILNAPER21:|:
m eso_loc2|Naperville:|:
m eso_neigh2|Saybrook:|:
m eso_url2|:|:
m eso_URLtext2|:|:
m eso_historyURL2|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER21:|:
m eso_lon2|-88.14:|:
m eso_lat2|41.79:|:
m eso_time2|02/26 21:59:52:|:
m eso_temp2|42.9:|:
m eso_dew2|32.0:|:
m eso_rh2|66:|:
m eso_dir2|WNW:|:
m eso_wspeed2|2:|:
m eso_gust2|8:|:
m eso_barom2|30.09:|:
m eso_rrate2|0.00:|:
m eso_stat3|KILNAPER19:|:
m eso_loc3|Naperville:|:
m eso_neigh3|Brookdale Lakes:|:
m eso_url3|:|:
m eso_URLtext3|:|:
m eso_historyURL3|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER19:|:
m eso_lon3|-88.20:|:
m eso_lat3|41.78:|:
m eso_time3|02/26 22:20:05:|:
m eso_temp3|42.0:|:
m eso_dew3|33.0:|:
m eso_rh3|69:|:
m eso_dir3|NW:|:
m eso_wspeed3|1:|:
m eso_gust3|4:|:
m eso_barom3|30.02:|:
m eso_rrate3|0.00:|:
m eso_stat4|KILNAPER7:|:
m eso_loc4|Naperville:|:
m eso_neigh4|NapervilleWeather.com Near Ogden and Naper Blvd:|:
m eso_url4|http://NapervilleWeather.com:|:
m eso_URLtext4|NapervilleWeather.com Web Page:|:
m eso_historyURL4|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER7:|:
m eso_lon4|-88.12:|:
m eso_lat4|41.79:|:
m eso_time4|02/26 22:20:06:|:
m eso_temp4|42.5:|:
m eso_dew4|31.0:|:
m eso_rh4|63:|:
m eso_dir4|WSW:|:
m eso_wspeed4|2:|:
m eso_gust4|2:|:
m eso_barom4|30.10:|:
m eso_rrate4|0.00:|:
m eso_stat5|KILNAPER9:|:
m eso_loc5|Naperville:|:
m eso_neigh5|75th St. /  Modaff Area:|:
m eso_url5|http://mistybeagle.com/wx/:|:
m eso_URLtext5|Misty The Weather Beagle:|:
m eso_historyURL5|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILNAPER9:|:
m eso_lon5|-88.14:|:
m eso_lat5|41.74:|:
m eso_time5|02/26 22:20:02:|:
m eso_temp5|41.0:|:
m eso_dew5|33.0:|:
m eso_rh5|72:|:
m eso_dir5|WSW:|:
m eso_wspeed5|2:|:
m eso_gust5|4:|:
m eso_barom5|30.06:|:
m eso_rrate5|0.00:|:
m eso_stat6|KILAUROR18:|:
m eso_loc6|Aurora:|:
m eso_neigh6|Cambridge Chase:|:
m eso_url6|:|:
m eso_URLtext6|:|:
m eso_historyURL6|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILAUROR18:|:
m eso_lon6|-88.23:|:
m eso_lat6|41.79:|:
m eso_time6|02/26 22:20:07:|:
m eso_temp6|41.4:|:
m eso_dew6|27.0:|:
m eso_rh6|54:|:
m eso_dir6|WNW:|:
m eso_wspeed6|2:|:
m eso_gust6|4:|:
m eso_barom6|30.03:|:
m eso_rrate6|0.00:|:
m eso_stat7|KILWHEAT8:|:
m eso_loc7|Wheaton:|:
m eso_neigh7|Wheaton Sanitary District:|:
m eso_url7|http://www.wsd.dst.il.us/:|:
m eso_URLtext7|:|:
m eso_historyURL7|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT8:|:
m eso_lon7|-88.14:|:
m eso_lat7|41.85:|:
m eso_time7|02/26 22:08:04:|:
m eso_temp7|42.1:|:
m eso_dew7|34.0:|:
m eso_rh7|73:|:
m eso_dir7|NE:|:
m eso_wspeed7|0:|:
m eso_gust7|3:|:
m eso_barom7|30.08:|:
m eso_rrate7|0.00:|:
m eso_stat8|KILLISLE3:|:
m eso_loc8|Lisle:|:
m eso_neigh8|Main and Short Lisle:|:
m eso_url8|:|:
m eso_URLtext8|:|:
m eso_historyURL8|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLISLE3:|:
m eso_lon8|-88.07:|:
m eso_lat8|41.79:|:
m eso_time8|02/26 22:19:57:|:
m eso_temp8|41.9:|:
m eso_dew8|30.0:|:
m eso_rh8|63:|:
m eso_dir8|West:|:
m eso_wspeed8|0:|:
m eso_gust8|0:|:
m eso_barom8|29.88:|:
m eso_rrate8|0.00:|:
m eso_stat9|KILAUROR16:|:
m eso_loc9|Aurora:|:
m eso_neigh9|Oakhurst:|:
m eso_url9|:|:
m eso_URLtext9|:|:
m eso_historyURL9|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILAUROR16:|:
m eso_lon9|-88.24:|:
m eso_lat9|41.75:|:
m eso_time9|02/26 22:20:05:|:
m eso_temp9|43.1:|:
m eso_dew9|34.0:|:
m eso_rh9|70:|:
m eso_dir9|WSW:|:
m eso_wspeed9|1:|:
m eso_gust9|0:|:
m eso_barom9|29.32:|:
m eso_rrate9|0.00:|:
m eso_stat10|KILWHEAT5:|:
m eso_loc10|Wheaton:|:
m eso_neigh10|N9ABF - West Wheaton:|:
m eso_url10|http://n9abf.com:|:
m eso_URLtext10|N9ABF.com Amateur Radio:|:
m eso_historyURL10|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT5:|:
m eso_lon10|-88.13:|:
m eso_lat10|41.87:|:
m eso_time10|02/26 22:06:08:|:
m eso_temp10|40.2:|:
m eso_dew10|26.0:|:
m eso_rh10|57:|:
m eso_dir10|WNW:|:
m eso_wspeed10|0:|:
m eso_gust10|0:|:
m eso_barom10|30.05:|:
m eso_rrate10|0.00:|:
m eso_stat11|KILWHEAT7:|:
m eso_loc11|Wheaton:|:
m eso_neigh11|South Wheaton:|:
m eso_url11|:|:
m eso_URLtext11|:|:
m eso_historyURL11|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT7:|:
m eso_lon11|-88.08:|:
m eso_lat11|41.86:|:
m eso_time11|02/26 22:10:30:|:
m eso_temp11|39.6:|:
m eso_dew11|32.0:|:
m eso_rh11|74:|:
m eso_dir11|WSW:|:
m eso_wspeed11|2:|:
m eso_gust11|3:|:
m eso_barom11|30.03:|:
m eso_rrate11|0.00:|:
m eso_stat12|MUP431:|:
m eso_loc12|West Chicago:|:
m eso_neigh12|MesoWest West Chicago                     IL US UPR:|:
m eso_url12|http://madis.noaa.gov:|:
m eso_URLtext12|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL12|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MUP431:|:
m eso_lon12|-88.18:|:
m eso_lat12|41.88:|:
m eso_time12|02/26 20:45:00:|:
m eso_temp12|41.0:|:
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
m eso_time13|02/26 22:10:13:|:
m eso_temp13|41.8:|:
m eso_dew13|32.0:|:
m eso_rh13|69:|:
m eso_dir13|WSW:|:
m eso_wspeed13|3:|:
m eso_gust13|7:|:
m eso_barom13|30.06:|:
m eso_rrate13|0.00:|:
m eso_stat14|KILWOODR2:|:
m eso_loc14|Woodridge:|:
m eso_neigh14|Woodridge:|:
m eso_url14|http://www.tkhuman.com:|:
m eso_URLtext14|TKHuman.com:|:
m eso_historyURL14|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWOODR2:|:
m eso_lon14|-88.04:|:
m eso_lat14|41.77:|:
m eso_time14|02/26 22:20:07:|:
m eso_temp14|40.8:|:
m eso_dew14|30.0:|:
m eso_rh14|66:|:
m eso_dir14|West:|:
m eso_wspeed14|0:|:
m eso_gust14|0:|:
m eso_barom14|29.56:|:
m eso_rrate14|0.00:|:
m eso_stat15|KILGLENE1:|:
m eso_loc15|Glen Ellyn:|:
m eso_neigh15|Butterfield West:|:
m eso_url15|http://www.dupageweather.com:|:
m eso_URLtext15|:|:
m eso_historyURL15|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILGLENE1:|:
m eso_lon15|-88.06:|:
m eso_lat15|41.85:|:
m eso_time15|02/26 22:19:56:|:
m eso_temp15|41.6:|:
m eso_dew15|30.0:|:
m eso_rh15|63:|:
m eso_dir15|SW:|:
m eso_wspeed15|8:|:
m eso_gust15|12:|:
m eso_barom15|30.14:|:
m eso_rrate15|0.00:|:
m eso_stat16|KILWHEAT9:|:
m eso_loc16|Wheaton:|:
m eso_neigh16|NE Wheaton:|:
m eso_url16|:|:
m eso_URLtext16|:|:
m eso_historyURL16|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT9:|:
m eso_lon16|-88.09:|:
m eso_lat16|41.87:|:
m eso_time16|02/26 22:20:03:|:
m eso_temp16|42.8:|:
m eso_dew16|30.0:|:
m eso_rh16|59:|:
m eso_dir16|WNW:|:
m eso_wspeed16|8:|:
m eso_gust16|11:|:
m eso_barom16|29.22:|:
m eso_rrate16|0.00:|:
m eso_stat17|KILDOWNE2:|:
m eso_loc17|Downers Grove:|:
m eso_neigh17|Prentiss Creek:|:
m eso_url17|:|:
m eso_URLtext17|:|:
m eso_historyURL17|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDOWNE2:|:
m eso_lon17|-88.04:|:
m eso_lat17|41.77:|:
m eso_time17|02/26 22:19:50:|:
m eso_temp17|41.0:|:
m eso_dew17|24.0:|:
m eso_rh17|51:|:
m eso_dir17|West:|:
m eso_wspeed17|4:|:
m eso_gust17|4:|:
m eso_barom17|30.00:|:
m eso_rrate17|0.00:|:
m eso_stat18|KILBOLIN10:|:
m eso_loc18|Bolingbrook:|:
m eso_neigh18|Bolingbrook/Clow Airport:|:
m eso_url18|:|:
m eso_URLtext18|:|:
m eso_historyURL18|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBOLIN10:|:
m eso_lon18|-88.11:|:
m eso_lat18|41.70:|:
m eso_time18|02/26 22:19:59:|:
m eso_temp18|41.2:|:
m eso_dew18|30.0:|:
m eso_rh18|63:|:
m eso_dir18|WSW:|:
m eso_wspeed18|3:|:
m eso_gust18|4:|:
m eso_barom18|29.32:|:
m eso_rrate18|0.00:|:
m eso_stat19|KILDOWNE3:|:
m eso_loc19|Downers Grove:|:
m eso_neigh19|Maple Woods West:|:
m eso_url19|:|:
m eso_URLtext19|:|:
m eso_historyURL19|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDOWNE3:|:
m eso_lon19|-88.03:|:
m eso_lat19|41.79:|:
m eso_time19|02/26 22:20:03:|:
m eso_temp19|42.5:|:
m eso_dew19|35.0:|:
m eso_rh19|76:|:
m eso_dir19|NNW:|:
m eso_wspeed19|3:|:
m eso_gust19|3:|:
m eso_barom19|30.10:|:
m eso_rrate19|0.00:|:
m eso_stat20|MD0023:|:
m eso_loc20|Glen Ellyn:|:
m eso_neigh20|APRSWXNET Glen Ellyn                 IL US:|:
m eso_url20|http://madis.noaa.gov:|:
m eso_URLtext20|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL20|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MD0023:|:
m eso_lon20|-88.05:|:
m eso_lat20|41.84:|:
m eso_time20|02/26 21:47:00:|:
m eso_temp20|43.0:|:
m eso_dew20|30.0:|:
m eso_rh20|60:|:
m eso_dir20|South:|:
m eso_wspeed20|1:|:
m eso_gust20|5:|:
m eso_barom20|30.12:|:
m eso_rrate20|0.00:|:
m eso_stat21|MAU162:|:
m eso_loc21|Glen Ellyn:|:
m eso_neigh21|APRSWXNET Glen Ellyn                   IL US:|:
m eso_url21|http://madis.noaa.gov:|:
m eso_URLtext21|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL21|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MAU162:|:
m eso_lon21|-88.08:|:
m eso_lat21|41.87:|:
m eso_time21|02/26 21:55:00:|:
m eso_temp21|40.0:|:
m eso_dew21|30.0:|:
m eso_rh21|68:|:
m eso_dir21|WNW:|:
m eso_wspeed21|0:|:
m eso_gust21|0:|:
m eso_barom21|30.11:|:
m eso_rrate21|0.00:|:
m eso_stat22|MC0472:|:
m eso_loc22|Glen Ellyn:|:
m eso_neigh22|APRSWXNET Wheaton                    IL US:|:
m eso_url22|http://madis.noaa.gov:|:
m eso_URLtext22|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL22|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MC0472:|:
m eso_lon22|-88.09:|:
m eso_lat22|41.88:|:
m eso_time22|02/26 21:02:00:|:
m eso_temp22|44.0:|:
m eso_dew22|20.0:|:
m eso_rh22|38:|:
m eso_dir22|East:|:
m eso_wspeed22|0:|:
m eso_gust22|0:|:
m eso_barom22|29.16:|:
m eso_rrate22|0.00:|:
m eso_stat23|KILWHEAT3:|:
m eso_loc23|Wheaton:|:
m eso_neigh23|Main and Geneva:|:
m eso_url23|http://www.mychicagogarden.com:|:
m eso_URLtext23|My Chicago Garden:|:
m eso_historyURL23|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWHEAT3:|:
m eso_lon23|-88.10:|:
m eso_lat23|41.88:|:
m eso_time23|02/26 22:20:00:|:
m eso_temp23|41.4:|:
m eso_dew23|32.0:|:
m eso_rh23|51:|:
m eso_dir23|NW:|:
m eso_wspeed23|3:|:
m eso_gust23|5:|:
m eso_barom23|30.06:|:
m eso_rrate23|0.00:|:
m eso_stat24|MC5020:|:
m eso_loc24|Glen Ellyn:|:
m eso_neigh24|APRSWXNET Wheaton                    IL US:|:
m eso_url24|http://madis.noaa.gov:|:
m eso_URLtext24|Meteorological Assimilation Data Ingest System:|:
m eso_historyURL24|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=MC5020:|:
m eso_lon24|-88.09:|:
m eso_lat24|41.88:|:
m eso_time24|02/26 21:43:00:|:
m eso_temp24|42.0:|:
m eso_dew24|30.0:|:
m eso_rh24|61:|:
m eso_dir24|WSW:|:
m eso_wspeed24|0:|:
m eso_gust24|0:|:
m eso_barom24|29.99:|:
m eso_rrate24|0.00:|:
m eso_stat25|KILBOLIN8:|:
m eso_loc25|Bolingbrook:|:
m eso_neigh25|Bolingbrook Neighborhood Weather:|:
m eso_url25|:|:
m eso_URLtext25|:|:
m eso_historyURL25|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBOLIN8:|:
m eso_lon25|-88.09:|:
m eso_lat25|41.69:|:
m eso_time25|02/26 22:20:09:|:
m eso_temp25|40.2:|:
m eso_dew25|31.0:|:
m eso_rh25|70:|:
m eso_dir25|WSW:|:
m eso_wspeed25|2:|:
m eso_gust25|2:|:
m eso_barom25|30.10:|:
m eso_rrate25|0.00:|:
m eso_stat26|KILWESTC8:|:
m eso_loc26|West Chicago:|:
m eso_neigh26|Ingalton Hills Estates:|:
m eso_url26|:|:
m eso_URLtext26|:|:
m eso_historyURL26|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWESTC8:|:
m eso_lon26|-88.20:|:
m eso_lat26|41.91:|:
m eso_time26|02/26 22:20:06:|:
m eso_temp26|41.0:|:
m eso_dew26|32.0:|:
m eso_rh26|69:|:
m eso_dir26|WSW:|:
m eso_wspeed26|5:|:
m eso_gust26|5:|:
m eso_barom26|30.21:|:
m eso_rrate26|0.00:|:
m eso_stat27|KILDARIE3:|:
m eso_loc27|Darien:|:
m eso_neigh27|Darien, Near 75th St and Lemont Rd (60561):|:
m eso_url27|:|:
m eso_URLtext27|:|:
m eso_historyURL27|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILDARIE3:|:
m eso_lon27|-88.01:|:
m eso_lat27|41.74:|:
m eso_time27|02/26 22:19:38:|:
m eso_temp27|41.5:|:
m eso_dew27|25.0:|:
m eso_rh27|52:|:
m eso_dir27|NW:|:
m eso_wspeed27|0:|:
m eso_gust27|2:|:
m eso_barom27|29.91:|:
m eso_rrate27|0.00:|:
m eso_stat28|KILBATAV1:|:
m eso_loc28|Batavia:|:
m eso_neigh28|Tri-Cities on the Fox River:|:
m eso_url28|http://www.mdweather.com:|:
m eso_URLtext28|mdweather.com Weather Page:|:
m eso_historyURL28|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBATAV1:|:
m eso_lon28|-88.31:|:
m eso_lat28|41.85:|:
m eso_time28|02/26 22:18:13:|:
m eso_temp28|39.9:|:
m eso_dew28|31.0:|:
m eso_rh28|69:|:
m eso_dir28|WNW:|:
m eso_wspeed28|4:|:
m eso_gust28|14:|:
m eso_barom28|30.00:|:
m eso_rrate28|0.00:|:
m eso_stat29|KILWESTC5:|:
m eso_loc29|West Chicago:|:
m eso_neigh29|THEBROWNHOUSE.ORG:|:
m eso_url29|http://www.thebrownhouse.org:|:
m eso_URLtext29|http://www.thebrownhouse.org:|:
m eso_historyURL29|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILWESTC5:|:
m eso_lon29|-88.21:|:
m eso_lat29|41.92:|:
m eso_time29|02/26 22:20:05:|:
m eso_temp29|44.6:|:
m eso_dew29|31.0:|:
m eso_rh29|59:|:
m eso_dir29|WNW:|:
m eso_wspeed29|0:|:
m eso_gust29|8:|:
m eso_barom29|30.04:|:
m eso_rrate29|0.00:|:
m eso_stat30|KILLOMBA5:|:
m eso_loc30|Lombard:|:
m eso_neigh30|Lombard (East of Sunset Knoll):|:
m eso_url30|http://www.knottme.com/weather/lombard:|:
m eso_URLtext30|Dee and Audreys Lombard Weather:|:
m eso_historyURL30|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLOMBA5:|:
m eso_lon30|-88.02:|:
m eso_lat30|41.87:|:
m eso_time30|02/26 22:20:05:|:
m eso_temp30|42.4:|:
m eso_dew30|27.0:|:
m eso_rh30|55:|:
m eso_dir30|West:|:
m eso_wspeed30|5:|:
m eso_gust30|10:|:
m eso_barom30|30.16:|:
m eso_rrate30|0.00:|:
m eso_stat31|KILGLENE5:|:
m eso_loc31|Glen Ellyn:|:
m eso_neigh31|Near North Ave. and Main St. Glen Ellyn:|:
m eso_url31|http://glenellyn.weather.patmulcahy.com:|:
m eso_URLtext31|Father Pat's Glen Ellyn Weather:|:
m eso_historyURL31|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILGLENE5:|:
m eso_lon31|-88.06:|:
m eso_lat31|41.90:|:
m eso_time31|02/26 22:20:09:|:
m eso_temp31|41.2:|:
m eso_dew31|25.0:|:
m eso_rh31|52:|:
m eso_dir31|SW:|:
m eso_wspeed31|3:|:
m eso_gust31|3:|:
m eso_barom31|29.21:|:
m eso_rrate31|0.00:|:
m eso_stat32|KILCAROL5:|:
m eso_loc32|Carol Stream:|:
m eso_neigh32|Stonehenge Court:|:
m eso_url32|http://www.mccreations.us/weather/:|:
m eso_URLtext32|Carol Stream Live Weather:|:
m eso_historyURL32|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILCAROL5:|:
m eso_lon32|-88.17:|:
m eso_lat32|41.92:|:
m eso_time32|02/26 22:20:07:|:
m eso_temp32|41.0:|:
m eso_dew32|17.0:|:
m eso_rh32|38:|:
m eso_dir32|WNW:|:
m eso_wspeed32|5:|:
m eso_gust32|5:|:
m eso_barom32|30.02:|:
m eso_rrate32|0.00:|:
m eso_stat33|KILLOMBA6:|:
m eso_loc33|Lombard:|:
m eso_neigh33|Fairfield Townhome Assoc:|:
m eso_url33|:|:
m eso_URLtext33|:|:
m eso_historyURL33|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLOMBA6:|:
m eso_lon33|-88.01:|:
m eso_lat33|41.86:|:
m eso_time33|02/26 22:19:25:|:
m eso_temp33|43.1:|:
m eso_dew33|32.0:|:
m eso_rh33|65:|:
m eso_dir33|NW:|:
m eso_wspeed33|4:|:
m eso_gust33|6:|:
m eso_barom33|30.08:|:
m eso_rrate33|0.00:|:
m eso_stat34|KILOSWEG9:|:
m eso_loc34|Oswego:|:
m eso_neigh34|Farmington Lakes:|:
m eso_url34|:|:
m eso_URLtext34|:|:
m eso_historyURL34|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILOSWEG9:|:
m eso_lon34|-88.31:|:
m eso_lat34|41.71:|:
m eso_time34|02/26 22:15:40:|:
m eso_temp34|41.9:|:
m eso_dew34|32.0:|:
m eso_rh34|67:|:
m eso_dir34|NNW:|:
m eso_wspeed34|5:|:
m eso_gust34|6:|:
m eso_barom34|29.84:|:
m eso_rrate34|0.00:|:
m eso_stat35|KILSTCHA8:|:
m eso_loc35|St. Charles:|:
m eso_neigh35|Ronzheimer Ave. & S. Tyler Rd.:|:
m eso_url35|:|:
m eso_URLtext35|:|:
m eso_historyURL35|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILSTCHA8:|:
m eso_lon35|-88.29:|:
m eso_lat35|41.91:|:
m eso_time35|02/26 22:20:09:|:
m eso_temp35|39.5:|:
m eso_dew35|32.0:|:
m eso_rh35|75:|:
m eso_dir35|WSW:|:
m eso_wspeed35|3:|:
m eso_gust35|3:|:
m eso_barom35|30.07:|:
m eso_rrate35|0.00:|:
m eso_stat36|KILBATAV3:|:
m eso_loc36|Batavia:|:
m eso_neigh36|West Batavia:|:
m eso_url36|:|:
m eso_URLtext36|:|:
m eso_historyURL36|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILBATAV3:|:
m eso_lon36|-88.36:|:
m eso_lat36|41.83:|:
m eso_time36|02/26 22:06:16:|:
m eso_temp36|41.5:|:
m eso_dew36|33.0:|:
m eso_rh36|72:|:
m eso_dir36|West:|:
m eso_wspeed36|2:|:
m eso_gust36|9:|:
m eso_barom36|30.08:|:
m eso_rrate36|0.00:|:
m eso_stat37|KILROMEO4:|:
m eso_loc37|Romeoville:|:
m eso_neigh37|Lakewood Estates:|:
m eso_url37|:|:
m eso_URLtext37|:|:
m eso_historyURL37|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILROMEO4:|:
m eso_lon37|-88.10:|:
m eso_lat37|41.65:|:
m eso_time37|02/26 22:20:04:|:
m eso_temp37|40.4:|:
m eso_dew37|12.0:|:
m eso_rh37|31:|:
m eso_dir37|West:|:
m eso_wspeed37|5:|:
m eso_gust37|7:|:
m eso_barom37|24.02:|:
m eso_rrate37|0.00:|:
m eso_stat38|KILLOMBA4:|:
m eso_loc38|Lombard:|:
m eso_neigh38|Madison Meadow:|:
m eso_url38|:|:
m eso_URLtext38|:|:
m eso_historyURL38|www.wunderground.com/weatherstation/WXDailyHistory.asp?ID=KILLOMBA4:|:
m eso_lon38|-88.00:|:
m eso_lat38|41.88:|:
m eso_time38|02/26 22:19:33:|:
m eso_temp38|39.8:|:
m eso_dew38|19.0:|:
m eso_rh38|42:|:
m eso_dir38|WSW:|:
m eso_wspeed38|3:|:
m eso_gust38|6:|:
m eso_barom38|30.09:|:
m eso_rrate38|0.00:|:
m eso_stat39|KDPA:|:
m eso_loc39|Chicago DuPage:|:
m eso_neigh39|Official Station:|:
m eso_url39|:|:
m eso_URLtext39|:|:
m eso_historyURL39|www.wunderground.com/history/airport/KDPA/2012/2/26/DailyHistory.html?FULLALMANAC=KDPA:|:
m eso_lon39|-88.25:|:
m eso_lat39|41.91:|:
m eso_time39|02/26 21:52:00:|:
m eso_temp39|39.0:|:
m eso_dew39|29.0:|:
m eso_rh39|67:|:
m eso_dir39|West:|:
m eso_wspeed39|8:|:
m eso_gust39|---:|:
m eso_barom39|30.06:|:
m eso_rrate39|---:|:
m eso_stat40|KLOT:|:
m eso_loc40|Romeoville:|:
m eso_neigh40|Official Station:|:
m eso_url40|:|:
m eso_URLtext40|:|:
m eso_historyURL40|www.wunderground.com/history/airport/KLOT/2012/2/26/DailyHistory.html?FULLALMANAC=KLOT:|:
m eso_lon40|-88.09:|:
m eso_lat40|41.61:|:
m eso_time40|02/26 21:55:00:|:
m eso_temp40|39.0:|:
m eso_dew40|31.0:|:
m eso_rh40|73:|:
m eso_dir40|WSW:|:
m eso_wspeed40|8:|:
m eso_gust40|---:|:
m eso_barom40|30.04:|:
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