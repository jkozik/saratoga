<?php
// plaintext-parser.php script by Ken True - webmaster@saratoga-weather.org
//
// Version 0.90 - 24-Mar-2007 - Pre-release - testing and commments version
// Version 0.91 - 25-Mar-2007 - fixed day/night icon glitch for translated languages
// Version 0.92 - 27-Mar-2007 - added conditionals for selective printing, optional degrees, and
//                              capitalization for First letter of sentence in translation.
// Version 1.00 - 30-Mar-2007 - changed primary parsing to support IIS, and added minPoP to
//                              display sky condition (with pop) instead of rain icon.
// Version 1.01 - 02-Apr-2007 - support for UTF-8 display, translation of City names, Date format option
// Version 1.02 - 16-May-2007 - minor change to allow operation with plaintext-rss.php
// Version 1.03 - 03-Jun-2007 - Added Wind dir/speed/units forecast and changed table generation method
// Version 1.04 - 27-Jul-2007 - Added Beaufort options for wind speed display
// Version 1.05 - 04-Aug-2007 - Modified Beaufort to use 'mps' or 'm/s' for meters-per-second
// Version 1.06 - 06-Sep-2007 - Added support for charset in lang files + Greek translation
// Version 1.07 - 07-Nov-2007 - Added support for GMT/Zulu time from WXSIM plaintext.txt
// Version 1.08 - 28-Dec-2007 - Added Snow Accumulation stats to PoP line when available
// Version 1.09 - 02-Jan-2008 - Added settings for integration to Carterlake/AJAX/PHP site design and
//                              'not available' message capability
// Version 1.10 - 12-May-2008 - fixed &rArr; to &rarr; for right-arrow in wind display
// Version 1.11 - 29-Jul-2008 - added $maxIcons to control number of icons to display
// Version 1.12 - 09-Aug-2008 - added $showIconsMax parameter (optional) to override $maxIcons
// Version 1.13 - 10-Sep-2008 - added support for multi-char Wind direction (for -pl language)
// Version 1.14 - 11-Jan-2009 - fixed Beaufort and added Snow/Freezing level support 
// Version 1.15 - 14-Jan-2009 - added $tempDegrees symbols for wind chill and heat index text
// Version 1.16 - 05-Feb-2009 - fixed plaintext occasional parsing issue
// Version 1.17 - 18-Jun-2009 - added fix for missing leading space in plaintext.txt lines
// Version 1.18 - 09-Jul-2010 - added support for Humidex and frost forecast optional displays
// Version 1.19 - 12-Aug-2010 - added support for Heat-index and 'hot' icon for Heat-index/humidex above specified values
// Version 1.20 - 18-Oct-2010 - Corrected Frost display wording below icons
// Version 1.21 - 01-Jan-2011 - added support for Wind-chill and 'Cold!' for windchill below specified value
// Version 1.22 - 03-Jan-2011 - added formatting to windchill, heatidx, humidex and frost arrays
// Version 1.23 - 23-Jan-2011 - corrected display for frost when windchill also exists
// Version 1.24 - 15-Feb-2011 - corrected display for snow conditions not being selected
// Version 1.25 - 18-Feb-2011 - added lang=en for optional translation and fixed 'snow likely' not showing snow icon
// Version 1.26 - 22-Feb-2011 - added more Wind-Chill processing for WXSIM 12.8.5+
// Version 1.27 - 01-Oct-2011 - added support for alternative animated icon set from http://www.meteotreviglio.com/
// Version 1.28 - 27-Dec-2011 - added support for RTL languages (Hebrew)
// Version 1.29 - 08-Dec-2012 - added fixes for PHP 5.4
//
$Version = "plaintext-parser.php Version 1.29 - 08-Dec-2012";
//
// error_reporting(E_ALL);  // uncomment to turn on full error reporting
//
// script available at http://saratoga-weather.org/scripts.php
//  
// you may copy/modify/use this script as you see fit,
// no warranty is expressed or implied.
//
// This script parses the plaintext.txt forecast output from WXSIM 
//  (http://www.wxsim.com/) to create a HTML page that resembles
//  the NOAA NWS point-printable forecast page in the carterlake advforecast.php style.
//
// output: creates XHTML 1.0-Strict HTML page (or inclusion)
//
// This script requires the NOAA icons and the icons need to be placed in the path 
// where the original NOAA icons are located: \forecast\images\
// (so make a folder in your web HTML root called "forecast", then make a folder in it 
// called "images", and place the icons in this folder)
// The icon set is available at http://saratoga-weather.org/carterlake-icons.zip 
// If you have the icon set from:
//   http://members.cox.net/carterlakeweather/forecasticons.zip (380K)
// then please download http://saratoga-weather.org/carterlake-icons-addon.zip and
// upload to your /forecast/images directory.  It contains images to support the
// "show sky conditions when chance rain and PoP < $minPoP" logic.
//
//
// Options on URL:
//   lang=en          (default) - use English language
//   lang=ZZ          - use 'ZZ' language translation file for conversion from English
//                    note: there must be a plaintext-parser-lang-LL.txt file with the
//                    conversion rules.   A sample Dutch file is included (lang=nl).
//
//   inc=Y            - omit <HTML><HEAD></HEAD><BODY> and </BODY></HTML> from output
//   heading=n        - (default)='y' suppress printing of heading (forecast city/by/date)
//   icons=n          - (default)='y' suppress printing of the icons+conditions+temp+wind+UV
//   text=n           - (default)='y' suppress printing of the periods/forecast text
//
//
//  You can also invoke these options directly in the PHP like this
//
//    $doInclude = true;
//    include("plaintext-parser.php");  for just the text
//  or ------------
//    $doPrint = false;
//    include("plaintext-parser.php");  for setting up the $WXSIM... variables without printing
//
//  or ------------
//    $doInclude = true;
//    $doPrintHeading = true;
//    $doPrintIcons = true;
//    $doPrintText = false
//    include("plaintext-parser.php");  include mode, print only heading and icon set
//
// Variables returned (useful for printing an icon or forecast or two...)
//
// $WXSIMcity 		- Name of city from WXSIM Forecast header
// $WXSIMstation    - (in settings below) Name of forecaster or weather station
// $WXSIMupdated 	- Time of forecast from WXSIM Forecast header
//
// The following variables exist for $i=0 to $i= number of forecast periods minus 1
//  a loop of for ($i=0;$i<count($WXSIMday);$i++) { ... } will loop over the available 
//  values.
//
// $WXSIMday[$i]	- period of forecast (translated)
// $WXSIMtext[$i]	- text of forecast (translated)
// $WXSIMuv[$i]		- UV index value (number)
//      note: you can use 'PPset_UV_string($WXSIMuv[$i])' to get a UV N and meaning text
// $WXSIMtemp[$i]	- Temperature with translated text and formatting
// $WXSIMpop[$i]	- Number - Probabability of Precipitation ('',10,20, ... ,100)
// $WXSIMprecip[$i] - amount of precipitation (native units from plaintext forecast)
// $WXSIMicon[$i]   - base name of icon graphic to use
// $WXSIMcond[$i]   - Short legend for forecast icon (translated)
// $WXSIMicons[$i]  - Full icon with translated Period, <img> and Short legend.
//  the following were added in V1.03:
// $WXSIMwinddir[$i]- Wind direction ( may be something like 'SSE' or 'NW->N' ) (translated)
// $WXSIMwind[$i]   - Wind speed (may be something like '10' or '7->4' or '10-16->6' )
// $WXSIMgust[$i]   - Wind gust
// $WXSIMwindunits[$i] - Wind speed units (mph, kph, mps, kts) (translated)
// $WXSIMBeaufort[$i]  - Beaufort scale for max wind speed 'n Bft' or 'Light air' (translated)
// $WXSIMhumidex[$i] - humidex number (or '' if not present in forecast)
// $WXSIMfrost[$i]  - frost forecast (or '' if not present in forecast)
// $WXSIMheatidx[$i]  - heat-index forecast (or '' if not present in forecast)
// $WXSIMwindchill[$i] - wind-chill forecast (or '' if not present in forecast)
// Note:
//
// Settings ---------------------------------------------------------------
$iconDir ='./forecast/images/';           // directory for carterlake icons
$iconType = '.jpg';        // default type='.jpg' -- use '.gif' for animated icons from http://www.meteotreviglio.com/
$WXSIMstation = "NapervilleWeather.net";   // name of your weather station
$plaintextFile = './plaintext.txt';       // location of the WXSIM plaintext.txt
$lang = 'en';                             // default language is 'en' = English
$tempDegrees = '&deg;';                   // set to '' to omit degree sign
//                                        //   or set to '&deg;', '&deg;F' or '&deg;C'
$maxIcons = 10;                           // set to maximum number of icons to display
$maxWidth = '640px';                      // max width of tables (could be '100%')
$minPoP = '40';                           // PoP must be this or above to display
//                                        // rain icon, otherwise sky icon w/PoP displayed.
$showBeaufort = 'T';					  // set to false to not display Beaufort at all
//										  // ='V' for 'n Bft', ='T' for translated name
$showHumidex = true;                      // =true to display Humidex (Hdx), =false to suppress display
$showFrost = true;                        // =true to display frost, =false to suppress display
$showHeatIDX = true;                      // =true to display Heat-Index (HI), =false to suppress display
$showHotIconF = 100;                      // show Hot icon if Heat-Index >= value
$showHotIconC = 37.7;                     // show Hot icon if Humidex >= value
//
$showWindChill = true;                    // =true to display Wind-Chill, =false to suppress display
$showColdvalF = 26;                      // show Cold! if Wind-Chill <= value in F
$showColdvalC = -3;                      // show Cold! if Wind-Chill <= value in C
$uomTemp   = 'C';                        // ='C' if forecast temperature in Centigrade, ='F' if in Fahrenheit
$ourTZ     = 'America/Los_Angeles';      // your timezone
// ---- end of settings ---------------------------------------------------
//
// overrides from Settings.php if available
global $SITE;
if (isset($SITE['fcsticonsdir'])) 	{$iconDir = $SITE['fcsticonsdir'];}
if (isset($SITE['fcsticonstype'])) 	{$iconType = $SITE['fcsticonstype'];}
if (isset($SITE['defaultlang'])) 	{$lang = $SITE['defaultlang'];}
if (isset($SITE['uomTemp']))        {$uomTemp = $SITE['uomTemp'];}
if (isset($SITE['tz'])) 		{$ourTZ = $SITE['tz'];}
// end of overrides from Settings.php
//
// -------------------begin code ------------------------------------------
if (isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
   //--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header('Pragma: public');
   header('Cache-Control: private');
   header('Cache-Control: no-cache, must-revalidate');
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header('Connection: close');
   
   readfile($filenameReal);
   exit;
}
# Set timezone in PHP5/PHP4 manner
if (!function_exists('date_default_timezone_set')) {
  putenv("TZ=" . $ourTZ);
  } else {
  date_default_timezone_set("$ourTZ");
 }

$Status = "<!-- $Version -->\n";
$doTranslate = true;                      
if (isset($_REQUEST['lang']) ) {
  $lang=strtolower($_REQUEST['lang']);
//  if ($lang == 'en') { $doTranslate = false; };
}
global $doDebug;
$doDebug = false;
if (isset($_REQUEST['debug']) ) {
  $doDebug = substr(strtolower($_REQUEST['debug']),0,1) == 'y';
}

$printHeading = true;
$printIcons = true;
$printText = true;

if (isset($doPrintHeading)) {
  $printHeading = $doPrintHeading;
}
if (isset($_REQUEST['heading']) ) {
  $printHeading = substr(strtolower($_REQUEST['heading']),0,1) == 'y';
}

if (isset($doPrintIcons)) {
  $printIcons = $doPrintIcons;
}
if (isset($_REQUEST['icons']) ) {
  $printIcons = substr(strtolower($_REQUEST['icons']),0,1) == 'y';
}
if (isset($doPrintText)) {
  $printText = $doPrintText;
}
if (isset($_REQUEST['text']) ) {
  $printText = substr(strtolower($_REQUEST['text']),0,1) == 'y';
}

if (isset($showIconsMax)) {
  $maxIcons = $showIconsMax;
}
if (isset($_REQUEST['maxicons']) ) {
  $maxIcons = $_REQUEST['maxicons'];
  $maxIcons = preg_replace('|[^\d]+|is','',$maxIcons);
}

$useCharSet = 'iso-8859-1';
$UTFLang = ',gr,ru,cn,jp,he,';  // languages needing UTF-8 character set for display
$RTLlangs = ',he,cn,jp,';       // languages needing right-to-left display
$doRTL = (strpos($RTLlangs,$lang) !== false)?true:false;
$timeFormat = 'd-M-Y h:i a'; // default to USA format
// load the config file
$config = file("./plaintext-parser-data.txt");  // 
// load and merge the language file (if it exists)
if ($doTranslate and file_exists("./plaintext-parser-lang-$lang.txt") ) {
  $lfile = file("./plaintext-parser-lang-$lang.txt");
  foreach ($lfile as $val) {
    array_push($config,$val);
  }
  $Status .= "<!-- translation file for '$lang' loaded -->\n";
  if (strpos($UTFLang,$lang) > 0) {$useCharSet = 'UTF-8'; $Status .= "<!-- using UTF-8 -->\n";}
} else {
  $doTranslate = false;
//  if($lang <> 'en') {
    $Status .= "<!-- translation file for '$lang' not found -->\n";
    $lang = 'en';
//  }
}

$showColdVal = preg_match('|C|i',$uomTemp)?$showColdvalC:$showColdvalF;
$Status .= "<!-- using $showColdVal for wind-chill test -->\n";

// Initialize -- read in control file and place in $Conditions and $Precip
//  for later use

$LanguageLookup = array();

$WindLookup = array( // initialized by wind-direction abbreviations for icons
// NOTE: don't change these .. use LANGLOOKUP entries in the plaintext-parser-LL.txt
// translation control file instead.
'north' => 'N',
'north-northeast' => 'NNE',
'northeast' => 'NE',
'east-northeast' => 'ENE',
'east' => 'E',
'east-southeast' => 'ESE',
'southeast' => 'SE',
'south-southeast' => 'SSE',
'south' => 'S',
'south-southwest' => 'SSW',
'southwest' => 'SW',
'west-southwest' => 'WSW',
'west' => 'W',
'west-northwest' => 'WNW',
'northwest' => 'NW',
'north-northwest' => 'NNW'
);

$BeaufortText = array(
// NOTE: don't change these .. use LANGLOOKUP entries in the plaintext-parser-LL.txt
// translation file instead.
'Calm', 'Light air', 'Light breeze','Gentle breeze', 'Moderate breeze', 'Fresh breeze', 
'Strong breeze', 'Near gale', 'Gale', 'Strong gale', 'Storm', 'Violent storm', 
'Hurricane' 
);
// wind speed < values below correspond to force 0 .. 11 . >= last value = force 12
$BeaufortKTS = array(
1,4,7,11,17,22,28,34,41,48,56,64,64
);
$BeaufortMPH = array(
1,4,8,13,19,25,32,39,47,55,64,73,73
);
$BeaufortKPH = array(
1,6,12,20,30,40,51,63,76,88,103,118,118
);
$BeaufortMS  = array(
0.2,1.6,3.4,5.5,8.0,10.8,13.9,17.2,20.8,24.5,28.5,32.7,32.7
);


reset($config);
// $Status .= "<!-- config: \n" . print_r($config,true) . " -->\n";
foreach ($config as $key => $rec) { // load the parser condition strings
  $recin = trim($rec);
  if ($recin and substr($recin,0,1) <> '#') { // got a non comment record
    list($type,$keyword,$dayicon,$nighticon,$condition) = explode('|',$recin . '|||||');
	
	if (isset($type) and strtolower($type) == 'cond' and isset($condition)) {
	  $Conditions["$keyword"] = "$dayicon\t$nighticon\t$condition";
	}
	if (isset($type) and strtolower($type) == 'precip' and isset($nighticon)) {
	  $Precip["$keyword"] = "$dayicon\t$nighticon";
	}
	if (isset($type) and strtolower($type) == 'snow' and isset($nighticon)) {
	  $Snow["$keyword"] = "$dayicon\t$nighticon";
	}
	if (isset($type) and strtolower($type) == 'lang' and isset($dayicon)) {
	  $Language["$keyword"] = "$dayicon";
    } 
	if (isset($type) and strtolower($type) == 'langlookup' and isset($dayicon)) {
	  $LanguageLookup["$keyword"] = "$dayicon";
    } 
	if (isset($type) and strtolower($type) == 'dateformat' and isset($keyword)) {
	  $timeFormat = trim($keyword);
    } 
	if (isset($type) and strtolower($type) == 'charset' and isset($keyword)) {
	  $useCharSet = trim($keyword);
	  $Status .= "<!-- using charset '$useCharSet' -->\n";
    } 
	if (isset($type) and strtolower($type) == 'notavail' and isset($keyword)) {
	  $notAvail = trim($keyword);
    } 
  } // end if not comment or blank
} // end loading of $Conditions and $Precip

if (count($LanguageLookup) < 1) {$doTranslate = false; }

$testFiles = array(  // local testing for Ken .. these files aren't part of the distribution
 'a' => 'plaintext-bin.txt',
 'b' => 'plaintext-test1.txt',
 'fr' => 'plaintext-cy.txt',
 'nl' => 'plaintext-du.txt',
 'it' => 'plaintext-it.txt',
 'se' => 'plaintext-se.txt',
 'nz' => 'plaintext-nz.txt',
 'snow' => 'plaintext-snow.txt',
 'rest' => 'plaintext-rest.txt',
 'oregon' => 'plaintext-oregon.txt',
 'fograin' => 'plaintext-fograin.txt',
 'snow2' => 'plaintext-snow2.txt',
 'snow3' => 'plaintext-snow3.txt',
 'snow4' => 'plaintext-snow4.txt',
 'snow5' => 'plaintext-snow5.txt',
 'snow6' => 'plaintext-snow6.txt',
 'snow7' => 'plaintext-snow7.txt',
 'snow8' => 'plaintext-snow8.txt',
 'snow9' => 'plaintext-snow9.txt',
 'snow10' => 'plaintext-snow10.txt',
 'gr' => 'plaintext-gr.txt',
 'zulu' => 'plaintext-zulu.txt',
 'beaufort' => 'plaintext-beaufort.txt',
 'snowlevel' => 'plaintext-snowlevel.txt',
 'thunder' => 'plaintext-thunder.txt',
 'thunder2' => 'plaintext-thunder2.txt',
 'broadstairs' => 'plaintext-broadstairs.txt',
 'hdx' => 'plaintext-hdx.txt',
 'frost' => 'plaintext-frost.txt',
 'hot' => 'plaintext-hot.txt',
 'nl-frost' => 'plaintext-nl-frost.txt',
 'wc' => 'plaintext-wc.txt',
 'wc2' => 'plaintext-wc2.txt',
 'nl4' => 'plaintext-nl4.txt',
 'nl5' => 'plaintext-nl5.txt',
 'sn4' => 'plaintext-sn4.txt',
 'sn5' => 'plaintext-sn5.txt',
 'wc3' => 'plaintext-wc3.txt',
);
$linksList = '';
if (isset($_REQUEST['testlinks'])) {
	$thisTest = isset($_REQUEST['test'])?$_REQUEST['test']:'notspecified';
 	foreach ($testFiles as $name => $fname) {
		$linksList .= "<a href=\"?testlinks&amp;test=$name&amp;lang=$lang\">";
		if($thisTest == $name) {
			$linksList .= "<b><span style=\"font-size: 14pt;\">$name</span></b></a>&nbsp; ";
		} else {
			$linksList .= "$name</a>&nbsp; ";
		}
	}

}
 
if (isset($_REQUEST['test']) ) { // for testing only
  $t = strtolower($_REQUEST['test']);
  if (isset($testFiles[$t]) ) {
    $Status .= "<!-- using $testFiles[$t] for plaintext.txt -->\n";
	$plaintextFile = $testFiles[$t]; 
  }
}

if (isset($_REQUEST['minpop'])) { // for testing only
  $minPoP = $_REQUEST['minpop'];
}

// Read in the plaintext.txt forecast file for processing
if (! file_exists($plaintextFile) ) {
   $msg = "The WXSIM forecast is not currently available.";
   if (isset($notAvail)) {
     $msg = $notAvail;
   }
   print "<p>$msg</p>\n";
   return;
}
$pt = file($plaintextFile);
// fix missing space at start of line if need be
foreach ($pt as $i => $line) {
	if(substr($line,0,1) != ' ') {$pt[$i] = ' ' . $line;}
}
$plaintext = implode('',$pt);   // get the plaintext file.
// preprocess.. mark divisions in the forecast days for later parsing
$plaintext = preg_replace('![\n][\r|\n]+!s',"\n \n",$plaintext);
$plaintext = preg_replace('![\r|\n]+ [\r|\n]+!s',"\t\t",$plaintext); 
$plaintext .= "\t\t";  // make sure a delimiter is at the end too.
$plaintext = preg_replace('|_|is','',$plaintext); // remove dashed line in front

// Find city and update date
if (preg_match('|WXSIM text forecast for (.*), initialized at\s+(.*)|i',$plaintext,$matches)) {
  $WXSIMcity = PPget_lang(trim($matches[1]));
  $wdate = trim($matches[2]);
  $Status .= "<!-- wdate '$wdate' -->\n";
  // there are LOTS of formats of the date stamp in plaintext.txt
  // 012345678901234567890
  // 20:00   Feb 21, 2007
  // 15:00   02 Apr, 2007
  // 8:00 PM Mar 24, 2007
  // 12:00 AM 24 Mar, 2007
  // 17:00Z  05 Nov, 2007
  $wdateParts =  preg_split("/[\s,]+/", $wdate);
//  $Status .= "<!-- wdate split\n" . print_r($wdateParts,true) . " -->\n";
  $i=0;
  if (preg_match('!AM|PM!i',$wdateParts[1]) ) { // got US style date.
    list($wHrs,$wMins) = explode(':',$wdateParts[0]);
	if (strtolower($wdateParts[1]) == 'pm' and $wHrs <> 12 ) {$wHrs += 12; }
	if (strtolower($wdateParts[1]) == 'am' and $wHrs == 12 ) {$wHrs = '00'; }
    $wTime = "$wHrs:$wMins";
	$i=1;
  } else {
    $wTime = $wdateParts[0];
  }
  if (preg_match('!\d+!',$wdateParts[$i+1]) ) { // got day in 1, month in 2 (+offset)
    $wDay = $wdateParts[$i+1];
	$wMon = $wdateParts[$i+2];
  } else {
    $wDay = $wdateParts[$i+2];
	$wMon = $wdateParts[$i+1];
  }
  $wYear = $wdateParts[$i+3];
  

//  $wdate = preg_replace('|(\S+)\s+(\S+)\s+(\S+),\s+(\S+)|s',"$2-$3-$4 $1:00 ".date('O',time()),$wdate);
  if(preg_match('|Z|i',$wTime) ) {
    // Zulu(GMT) time format
	$wTime = preg_replace('|Z|i','',$wTime);
    $wdate = "$wDay-$wMon-$wYear $wTime UTC";
    $Status .= "<!-- updated '$wdate'-->\n";
    $d = strtotime($wdate);
    $WXSIMupdated = PPget_lang(date('l',$d)) . ', ' . gmdate($timeFormat,$d) . ' UTC';
  } else {
    $wdate = "$wDay-$wMon-$wYear $wTime " . date('O',time());
    $Status .= "<!-- updated '$wdate'-->\n";
    $d = strtotime($wdate);
    $WXSIMupdated = PPget_lang(date('l',$d)) . ', ' . date($timeFormat,$d);
  }
}

// split up the forecast days and texts
// good preg_match_all('![\r|\n]+\s+(.*):\s(.*)[\r|\n]+ [\r|\n]+!Us',$plaintext,$matches);
preg_match_all('!\t\s(.*):\s(.*)\t!Us',$plaintext,$matches); // split up the forecast

// Main loop over each forecast text paragraph.
for ($i=0;$i<count($matches[1]);$i++) { // loop over results by forecast period and peel out values

 $WXSIMday[$i] = trim($matches[1][$i]);
 $WXSIMtext[$i] =  preg_replace('![\r|\n]+!is','',trim($matches[2][$i])); // remove CR and LF chars.
 // initialize this day's variables
 $WXSIMuv[$i] = '';
 $WXSIMtemp[$i] = '';
 $WXSIMpop[$i] = '';
 $WXSIMprecip[$i] = '';
 $WXSIMicon[$i] = '';
 $WXSIMcond[$i] = '';
 $WXSIMtempdirect[$i] = '';
 $WXSIMwinddir[$i] = '';
 $WXSIMwinddiricon[$i] = '';
 $WXSIMwind[$i] = '';
 $WXSIMgust[$i] = '';
 $WXSIMwindunits[$i] = '';
 $WXSIMhumidex[$i] = '';
 $WXSIMfrost[$i] = '';
 $WXSIMheatidx[$i] = '';
 $WXSIMwindchill[$i] = '';
 
 // make the period 'pretty' with HTML breaks.
 $WXSIMtitles[$i] = preg_replace('! (\S+)$!',"<br />\\1",PPget_lang($WXSIMday[$i]));
 if (! preg_match('!<br />!',$WXSIMtitles[$i])) {
   $WXSIMtitles[$i] .= '<br />';  // add line break to 'short' day titles
 }
 
 // extract UV index value
 if (preg_match('|UV index up to (\d+)\.|i',$WXSIMtext[$i],$mtemp) ) {
   $WXSIMuv[$i] = $mtemp[1];
 }
 
 // extract Heat Index value
 if (preg_match('|Heat index up to (\d+)\.|i',$WXSIMtext[$i],$mtemp) ) {
   $WXSIMheatidx[$i] = $mtemp[1];
 }

// extract Humidex value
 if (preg_match('|Humidex up to (\d+)\.|i',$WXSIMtext[$i],$mtemp) ) {
   $WXSIMhumidex[$i] = $mtemp[1];
 }

// extract Wind-chill value
 if (preg_match('/Wind chill (down to|ranging from) (\S+)/i',$WXSIMtext[$i],$mtemp) ) {
   $WXSIMwindchill[$i] = $mtemp[2];
 }

// extract Wind direction, values
 $testwind = str_replace('Wind chill','Wind-chill',$WXSIMtext[$i]);
 if (preg_match('|Wind (.*)\.|Ui',$testwind,$mtemp) ) {
//   $Status .= "<!-- mtemp[1]='" . $mtemp[1] . "' -->\n";
   $wtemp = preg_replace(
        '! around| near| in the| morning| evening| afternoon| midnight| tonight| to| after!Uis',
        '',$mtemp[1]);
//   $Status .= "<!-- stripped='" . $wtemp . "' -->\n";
   $wtemp = explode(', ',$wtemp);
//   $Status .= "<!-- wind\n" . print_r($wtemp, true) . " -->\n";
//    [0] => northwest around 5 mph
// or
//    [0] => northwest near calm
//    [1] => gusting to 12 mph
//    [2] => in the morning
//    [3] => becoming 12 mph in the afternoon
// or
//    [3] => becoming west-northwest around 12 mph in the afternoon
// or
//    [3] => becoming north-northwest in the afternoon

  $wparts = explode(' ',$wtemp[0]); // break it by spaces.
  $maxWind = 0;
  for ($k =0;$k<count($wtemp);$k++) {
    $wparts = explode(' ',$wtemp[$k]);
	
	if(isset($WindLookup[$wparts[0]]) ) { // got <dir> [speed] [units] format
	  $WXSIMwinddir[$i] = $WindLookup[$wparts[0]];  // get abbreviation for direction
      $WXSIMwinddiricon[$i] = $WXSIMwinddir[$i];    // base name for wind icon
      $WXSIMwind[$i] = $wparts[1];  // get speed
	  if ($wparts[1] > $maxWind and $wparts[1] <> 'calm') { $maxWind = $wparts[1]; }
      if ( isset($wparts[2])) {
       $WXSIMwindunits[$i] = $wparts[2]; // get wind units of measure
      }
    }
	
	if ($wparts[0] == 'gusting') {
	  $WXSIMwind[$i] .= '-' . $wparts[1];
	  if ($wparts[1] > $maxWind) { $maxWind = $wparts[1]; }
	  $WXSIMgust[$i] = $wparts[1];
	}
	if ($wparts[0] == 'becoming') { // got 'becoming [dir] [speed] [units]
	  if (isset($WindLookup[$wparts[1]]) ) {
	    $WXSIMwinddir[$i] .= '&rarr;' . $WindLookup[$wparts[1]];
	  }
	  if (preg_match('!(\d+|calm)!',$wtemp[$k],$match)) {
	    $WXSIMwind[$i] .= '&rarr;' . $match[1];
  	    if ($match[1] > $maxWind and $match[1] <> 'calm') { $maxWind = $match[1]; }
	  }
	  if (! $WXSIMwindunits[$i] and isset($wparts[2]) ) {
        $WXSIMwindunits[$i] = $wparts[2]; // get wind units of measure
	  }
	}
  
  }
   $WXSIMBeaufort[$i] = PPdisplayBeaufort(PPgetBeaufort($maxWind,$WXSIMwindunits[$i])) ;
//   $Status .= "<!-- dir='" . $WXSIMwinddir[$i] . "' wind='" . $WXSIMwind[$i] . 
     "' gust='" . $WXSIMgust[$i] . "' units='" . $WXSIMwindunits[$i] . 
	 "' maxWind = '$maxWind' Beaufort='" . $WXSIMBeaufort[$i] . "'-->\n";
 }
 
 // extract temperature High/Low values
 if (preg_match('!(high|low) ([-|\d]+)[\.|,]!i',$WXSIMtext[$i],$mtemp)) {
   $WXSIMtemp[$i] = PPget_lang($mtemp[1] .':') . ' ' . $mtemp[2] . $tempDegrees;
   if ($tempDegrees) {  // fix up degrees in the text
      $WXSIMtext[$i] = preg_replace(
	  '|' . $mtemp[1] . ' ' . $mtemp[2] .'|',
	  $mtemp[1] . ' ' . $mtemp[2] . $tempDegrees,
	  $WXSIMtext[$i]);
	  $WXSIMtext[$i] = preg_replace('/Wind chill down to ([-|\d]+)/i',"Wind chill down to $1$tempDegrees",$WXSIMtext[$i]);
	  $WXSIMtext[$i] = preg_replace('/Heat index up to ([-|\d]+)/i',"Heat index up to $1$tempDegrees",$WXSIMtext[$i]);
//     $Status .= "<!-- WXSIMtext[$i]='".$WXSIMtext[$i]."' -->\n";
	  
   }
   if(substr($mtemp[1],0,1) == 'H') {
     $WXSIMtemp[$i] = '<span style="color: red">' . $WXSIMtemp[$i] . '</span>';
   } else {
     $WXSIMtemp[$i] = '<span style="color: blue">' . $WXSIMtemp[$i] . '</span>';
   }
 }  
 
 if (preg_match('!temperatures (rising|falling)!i',$WXSIMtext[$i],$mtemp)) {
   $WXSIMtempdirect[$i] = $mtemp[1];
 }
 
 // extract PoP
 if (preg_match('!Chance of precipitation (.*) percent!i',$WXSIMtext[$i],$mtemp)) {
//   print "<pre>" . print_r($mtemp,true) . "</pre>\n";
   $WXSIMpop[$i] = $mtemp[1];
   $WXSIMpop[$i] = preg_replace('|less than |i','<',$WXSIMpop[$i]);
   if ($WXSIMpop[$i] == '<20') {$WXSIMpop[$i] = '10';}
   if ($WXSIMpop[$i] == 'near 100') { $WXSIMpop[$i] = '100'; }
 }
 
 // extract frost
 if (preg_match('!with\s+(.*)\s+frost.!is',$WXSIMtext[$i],$mtemp)) {
//   print "<pre>" . print_r($mtemp,true) . "</pre>\n";
   $WXSIMfrost[$i] = ucfirst($mtemp[1]) . ' frost';
  if ($doTranslate) {
    // perform optional translation if $Language has entries
    reset ($Language); // process in order of the file
    $text = $WXSIMfrost[$i];
    foreach ($Language as $key => $replacement) {
      $text = str_replace($key,$replacement,$text);
    }
	$WXSIMfrost[$i] = $text;
  }
 }

 // now look for harshest conditions first.. (in order in -data file
 if ($doDebug) {
   $Status .= "<!-- WXSIMtext[$i]='" . $WXSIMtext[$i] . "' -->\n";
 }
 reset($Conditions);  // Do search in load order
 foreach ($Conditions as $cond => $condrec) { // look for matching condition
 if ($doDebug) {
   $Status .= "<!-- check '$cond' -->\n";
 }
   
   if(preg_match("!$cond!i",$WXSIMtext[$i],$mtemp)) {
     list($dayicon,$nighticon,$condition) = explode("\t",$condrec);
	 if (preg_match('!chance!i',$condition) and $WXSIMpop[$i] < $minPoP) {
	   if ($doDebug) {
		 $Status .= "<!-- skip-chance -->\n";
	   }
	   continue; // skip this one
	 }
	 if (preg_match("|$cond level|i",$WXSIMtext[$i]) and !preg_match("/(chance of $cond|$cond likely|$cond very likely)/i",$WXSIMtext[$i]) ) {
	   if ($doDebug) {
		 $Status .= "<!-- skip $cond level -->\n";
	   }
	   continue; // skip 'snow level' and 'freezing level' entries
	 }
	 if ($doDebug) {
	   $Status .= "<!-- $cond / $condition selected -->\n";
	 }
	 $WXSIMcond[$i] = PPget_lang($condition);
	 if (preg_match('|night|i',$WXSIMday[$i])) {
	   $WXSIMicon[$i] = $nighticon;
	 } else {
	   $WXSIMicon[$i] = $dayicon;
	 }
	 break;
   }
 } // end of conditions search
 
 // look for precipitation amounts
 reset($Precip);  // Do search in load order
 foreach ($Precip as $pamt => $prec) { // look for matching precipitation amounts
   
   if(preg_match("!$pamt!is",$WXSIMtext[$i],$mtemp)) {
     list($amount,$units) = explode("\t",$prec);
	 $WXSIMprecip[$i] = $amount . ' ' . $units;
	 break;
    }
 } // end of precipitation amount search

 // look for Snow Accumulation
 reset($Snow);  // Do search in load order
 foreach ($Snow as $pamt => $prec) { // look for matching precipitation amounts
   
   if(preg_match("!$pamt!is",$WXSIMtext[$i],$mtemp)) {
     list($amount,$units) = explode("\t",$prec);
	 $WXSIMprecip[$i] .= "<br/><span style=\"color: blue\"><b>" . $amount . ' ' . $units . "</b></span>\n";
	 break;
    }
 } // end of Snow Accumulation amount search
 
 
//  now fix up the full icon name and PoP if available
  $curicon = $WXSIMicon[$i]  . $iconType;
  if ($WXSIMpop[$i] > 0) {
	$testicon = preg_replace('|'.$iconType.'|',$WXSIMpop[$i].$iconType,$curicon);
//	print "<pre>testicon='$testicon'</pre>\n";
	if (file_exists($iconDir . $testicon)) {
      $WXSIMicon[$i] = $testicon;
	} else {
	  $WXSIMicon[$i] = $curicon;
	}
  } else {
    $WXSIMicon[$i] = $curicon;
  }
  
  if ($WXSIMtempdirect[$i] <> '') {
     $tempdirect = 'up.gif';
	 if (substr($WXSIMtempdirect[$i],0,1) == 'f') { $tempdirect='down.gif'; }
	 $WXSIMtemp[$i] .= "<img src=\"$iconDir$tempdirect\" height=\"10\" width=\"10\" alt=\" \" />";
  
  
  }
  
  if ($doTranslate) {
    // perform optional translation if $Language has entries
    reset ($Language); // process in order of the file
    $text = $WXSIMtext[$i];
    foreach ($Language as $key => $replacement) {
      $text = str_replace($key,$replacement,$text);
    }
	// put back translated text, fixing capitalization for sentence starts (except the first one).
    $WXSIMtext[$i] = preg_replace('!\.\s+([a-z])!es',"'.  ' . strtoupper('\\1')",$text);
	$WXSIMday[$i] = PPget_lang($WXSIMday[$i]);
	if($doRTL and isset($LanguageLookup['NESW-N'])) { // handle RTL language type
	  $Status .= "<!-- RTLtrans input WXSIMwinddir[$i] = '".$WXSIMwinddir[$i]."' -->\n";
	  $tstr = preg_replace('|&rarr;|',"\t",$WXSIMwinddir[$i]);
	  $twdirs = explode("\t",$tstr);
	  foreach ($twdirs as $n => $twdir) {
		  $twdirs[$n] = isset($LanguageLookup['NESW-'.$twdir])?$LanguageLookup['NESW-'.$twdir]:$twdir;
	  }
	  $WXSIMwinddir[$i] = join('&larr;',array_reverse($twdirs));
	  $Status .= "<!-- RTLtrans output WXSIMwinddir[$i] = '".$WXSIMwinddir[$i]."' -->\n";
	  if(preg_match('|&rarr;|',$WXSIMwind[$i])) { 
	    $Status .= "<!-- RTLtrans input WXSIMwind[$i] = '".$WXSIMwind[$i]."' -->\n";
	    $tstr = preg_replace('|&rarr;|',"\t",$WXSIMwind[$i]);
		$twspds = explode("\t",$tstr);
		$WXSIMwind[$i] = join('&larr;',array_reverse($twspds));
	    $Status .= "<!-- RTLtrans output WXSIMwind[$i] = '".$WXSIMwind[$i]."' -->\n";
	  }
	  
	} else { // handle non-RTL language type
	
	  $wdirs = PPget_lang('NESW');  // default directions
	  if(strlen($wdirs) == 4) {
		$WXSIMwinddir[$i] = strtr($WXSIMwinddir[$i],'NESW',$wdirs); // do translation
	  } elseif (preg_match('|,|',$wdirs)) { //multichar translation
		$wdirsmc = explode(',',$wdirs);
		$wdirs = array('N','E','S','W');
		$wdirlook = array();
		foreach ($wdirs as $n => $d) {
		  $wdirlook[$d] = $wdirsmc[$n];
		} 
		$tstr = ''; // get ready to pass once through the string
		for ($n=0;$n<strlen($WXSIMwinddir[$i]);$n++) {
		  $c = substr($WXSIMwinddir[$i],$n,1);
		  if(isset($wdirlook[$c])) {
			$tstr .= $wdirlook[$c]; // use translation
		  } else {
			$tstr .= $c; // use regular
		  }
		}
		$WXSIMwinddir[$i] = $tstr; // do translation
	  
	  }
	}
	// translate speed and wind units based on lang entries in one pass
	$text = ' ' . $WXSIMwind[$i] . " \t " . $WXSIMwindunits[$i] . ' '; // make test string.
	reset ($Language); // process in order of the file
	foreach ($Language as $key => $replacement) {
	  $text = str_replace($key,$replacement,$text);
	}
	$key = explode("\t",$text);
	$WXSIMwind[$i] = trim($key[0]);
	$WXSIMwindunits[$i] = trim($key[1]);	
  
  }
   // override icon with hot.jpg if high heat-index or humidex 
   if($WXSIMheatidx[$i] >= $showHotIconF or $WXSIMhumidex[$i] >= $showHotIconC) {
	   $WXSIMicon[$i] = 'hot.jpg';
	   $WXSIMcond[$i] .= "<br/>\n<span style=\"color:red;\"><strong>" . PPget_lang('Hot!') . "</strong></span>";
   }
   
   if($WXSIMheatidx[$i] <> '') {
	   $WXSIMheatidx[$i] = "<span style=\"color: red;\">".PPget_lang('Heat').": $WXSIMheatidx[$i]$tempDegrees</span>\n";
	   
   }
  
   if($WXSIMhumidex[$i] <> '') {
	   $WXSIMhumidex[$i] = "<span style=\"color: red;\">".PPget_lang('Hmdx').": $WXSIMhumidex[$i]</span>\n";
   }
  
   // process Wind-chill
   if ($WXSIMwindchill[$i] <= $showColdVal and $WXSIMwindchill[$i] <> '') {
	   $WXSIMcond[$i] .= "<br/>\n<span style=\"color:blue;\"><strong>" . PPget_lang('Cold!') . "</strong></span>";
  //	 $WXSIMicon[$i] = 'cold.jpg';
   }
   
  
   if($WXSIMwindchill[$i] <> '') {
	   $WXSIMwindchill[$i] = "<span style=\"color: blue;\">".PPget_lang('WCh').": $WXSIMwindchill[$i]$tempDegrees</span>\n";
   }
   
   if ($WXSIMfrost[$i] <> '') {
	  $WXSIMfrost[$i] = "<span style=\"color: blue;\">$WXSIMfrost[$i]</span>\n";
   }


   // make HTML for full icon with condition description
   $WXSIMicons[$i] = "$WXSIMtitles[$i]<br />
   <img src=\"$iconDir$WXSIMicon[$i]\" alt=\"".strip_tags($WXSIMcond[$i])."\"  title=\"".
   strip_tags($WXSIMcond[$i]) ."\" /><br />
   $WXSIMcond[$i]";
 
} // end of main loop -------------------------


  $IncludeMode = false;
  $PrintMode = true;

  if (isset($doPrint) && ! $doPrint ) {
   //   print $Status;
      return;
  }
  if (isset($_REQUEST['inc']) && 
      strtolower($_REQUEST['inc']) == 'noprint' ) {
	  print $Status;
	  return;
  }

if (isset($_REQUEST['inc']) && strtolower($_REQUEST['inc']) == 'y') {
  $IncludeMode = true;
}
if (isset($doInclude)) {
  $IncludeMode = $doInclude;
}
// V1.03 -- set width of <td> for icons based on overall width/num icons
preg_match('|(\d+)(.*)|',$maxWidth,$widthparts);
//$Status .= "<!-- widthparts\n" . print_r($widthparts,true) . " -->\n";
$nTD = count($WXSIMicon);
if ($nTD < 1) { $nTD = 1; }
$wTD = round($widthparts[1]/$nTD,0) . $widthparts[2];
$nUseIcons = min(count($WXSIMicons),$maxIcons);
$Status .= "<!-- maxWidth='$maxWidth' nTD=$nTD wTD='$wTD' Icons to display=$nUseIcons -->\n";

//begin printing of advforecast style printout

 if(! $IncludeMode) { 
 
 ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $useCharSet; ?>" />
<title><?php echo $Version; ?></title>
<?php if($doRTL) { ?>
<style type="text/css">
.WXSIMforecast {
	direction: rtl;
	unicode-bidi: embed;
}
</style>
<?php } // end doRTL ?>
</head>
<body style="background-color:#FFFFFF; font-family:Arial, Helvetica, sans-serif; font-size: 10pt; width: 640px;">
<?php if(strlen($linksList) > 1) {
	print "<p style=\"width: 620px; border: 1px solid red;background-color: #FF9;\">";
	print "Testing links for ";
	$thisLink = isset($_REQUEST['test'])?'&amp;test='.$_REQUEST['test']:'';
	print "<a href=\"?testlinks$thisLink&amp;lang=$lang\">lang=$lang</a> ";
	print "<a href=\"?testlinks$thisLink&amp;lang=en\">lang=en</a><br/>\n";
	print $linksList;
	print "\n</p>\n";
    }
 } // only print above if not include mode 

print $Status;

if ($printHeading or $printIcons) {
?>
  <div class="WXSIMforecast">
  <table width="<?php echo $maxWidth; ?>" style="border: none;">
<?php 
}
if ($printHeading) {
	$RTL = $doRTL?'style="unicode-bidi: embed;"':'';

?>
    <tr>
      <td align="center"<?php echo $RTL; ?>><b><?php echo PPget_lang("WXSIM Forecast for:");?> </b><span style="color: green;">
	   <?php echo $WXSIMcity; ?></span><br />
        <?php echo PPget_lang("Issued by:"); ?> <?php echo $WXSIMstation; ?>
      </td>
    </tr>
    <tr>
      <td align="center"<?php echo $RTL; ?>><?php echo PPget_lang("Updated:");?> <?php 
	    if($doRTL) { echo "<span style=\"unicode-bidi: bidi-override; direction: ltr;\">";}
		echo $WXSIMupdated;
		if($doRTL) { echo "</span>";}
      ?>
	  </td>
    </tr>
<?php
} // end printHeading

if ($printIcons) {
?>
    <tr>
      <td align="center">&nbsp;
	    <table width="100%" border="0" cellpadding="0" cellspacing="0">  
	      <tr valign ="top" align="center">
	<?php
	  for ($i=0;$i<min(count($WXSIMicons),$maxIcons);$i++) {
	    print "<td style=\"width: $wTD;\"><span style=\"font-size: 8pt;\">$WXSIMicons[$i]</span></td>\n";
	  }
	?>
          </tr>	
          <tr valign ="top" align="center">
	  <?php
	  for ($i=0;$i<min(count($WXSIMprecip),$maxIcons);$i++) {
	    print "<td style=\"width: $wTD; color: green;\">$WXSIMprecip[$i]</td>\n";
	  }
	  ?>
          </tr>
          <tr valign ="top" align="center">
	  <?php
	  for ($i=0;$i<min(count($WXSIMtemp),$maxIcons);$i++) {
	    print "<td style=\"width: $wTD;\">$WXSIMtemp[$i]</td>\n";
	  }
	  ?>
          </tr>
          
      <?php if ($showHumidex or $showHeatIDX or $showWindChill) { ?>
          <tr valign ="top" align="center">
	  <?php
	  for ($i=0;$i<min(count($WXSIMhumidex),$maxIcons);$i++) {
		$flag = 0;
		if ($showHumidex and $WXSIMhumidex[$i] <> '') {$flag++;
	      print "<td style=\"width: $wTD;\">$WXSIMhumidex[$i]</td>\n";
		  
		}
		if ($showHeatIDX and $WXSIMheatidx[$i] <> '') {$flag++;
	      print "<td style=\"width: $wTD;\">$WXSIMheatidx[$i]</td>\n";
		}
		if ($showWindChill and $WXSIMwindchill[$i] <> '') {$flag++;
		  print "<td style=\"width: $wTD;\">$WXSIMwindchill[$i]</td>\n";
			
		}
		if($flag < 1) { // print spacer row
		  print "<td style=\"width: $wTD;\">&nbsp;</td>\n";
		  
		}
	  }
	  ?>
          </tr>
      <?php } // end of showHumidex/showHeatidx/showWindchill ?>
      <?php if ($showFrost and strlen(implode('',$WXSIMfrost)) > 1) { ?>
          <tr valign ="top" align="center">
 	  <?php
	  for ($i=0;$i<min(count($WXSIMfrost),$maxIcons);$i++) {
		
		if ($WXSIMfrost[$i] <> '') {$flag++;
		  print "<td style=\"width: $wTD;\">$WXSIMfrost[$i]</td>\n";
		} else {
		  print "<td style=\"width: $wTD;\">&nbsp;</td>\n";
		}
	  }
	  ?>
         </tr>         
     <?php } // end of showFrost ?>
          <tr valign ="top" align="center">
	  <?php
	  for ($i=0;$i<min(count($WXSIMwinddir),$maxIcons);$i++) {
	    print "<td style=\"width: $wTD;\">" . $WXSIMwinddir[$i] . "<br/> " .
		$WXSIMwind[$i] . "<br/> " . $WXSIMwindunits[$i]; 
		if ($showBeaufort) {
		  print "<br/><i>" . $WXSIMBeaufort[$i] ."</i>";
		}
		print "</td>\n";
	  }
	  ?>
          </tr>
          <tr valign ="top" align="center">
	  <?php
	  for ($i=0;$i<min(count($WXSIMuv),$maxIcons);$i++) {
	    print "<td style=\"width: $wTD;\">" . PPset_UV_string($WXSIMuv[$i]) . "</td>\n";
	  }
	  ?>
          </tr>
        </table>
     </td>
   </tr>
<?php 
}  // end printIcons

if ($printHeading or $printIcons) {
?>
</table>
</div>
  <p>&nbsp;</p>
<?php
} // end heading and icons

if ($printText) {
?>
<div class="WXSIMforecast">

<table style="border: 0" width="<?php echo $maxWidth; ?>">
	<?php
	  $rAlign = $doRTL?' text-align: right;':'';
	  for ($i=0;$i<count($WXSIMday);$i++) {
        print "<tr valign =\"top\" align=\"left\">\n";
	    print "<td style=\"width: 20%;$rAlign\"><b>$WXSIMday[$i]</b><br />&nbsp;<br /></td>\n";
	    print "<td style=\"width: 80%;$rAlign\">$WXSIMtext[$i]</td>\n";
		print "</tr>\n";
	  }
	?>
</table>
<p><small><a href="http://www.wxsim.com/">WXSIM</a> forecast formatting script by <a href="http://saratoga-weather.org/scripts.php">Saratoga-Weather.org</a>.
<?php if($iconType <> '.jpg') {
	print "<br/>Animated forecast icons courtesy of <a href=\"http://www.meteotreviglio.com/\">www.meteotreviglio.com</a>.";
} 
?>
 
</small></p></div><?php 
} // end printText

if (! $IncludeMode) { 
?>
</body>
</html>
<?php
} // print above only if not include mode

// Functions -----------------------------------------------------------------------

//  replace text with language lookup text if available
function PPget_lang( $text ) {
  global $LanguageLookup, $doTranslate;
  
  if ($doTranslate && isset($LanguageLookup[$text])) {
    $newtext = $LanguageLookup[$text];
  } else {
    $newtext = $text;
  }
 return($newtext);
}
//  decode UV to word+color for display

function PPset_UV_string ( $uv ) {
// figure out a text value and color for UV exposure text
//  0 to 2  Low
//  3 to 5 	Moderate
//  6 to 7 	High
//  8 to 10 Very High
//  11+ 	Extreme
   switch (TRUE) {
     case ($uv == 0):
       $uv = '';
     break;
     case (($uv > 0) and ($uv < 3)):
       $uv = "UV: $uv<br/>" . '<span style="border: solid 1px; background-color: #A4CE6a;">&nbsp;' . PPget_lang("Low") .'&nbsp;</span>';
     break;
     case (($uv >= 3) and ($uv < 6)):
       $uv = "UV: $uv<br/>" . '<span style="border: solid 1px;background-color: #FBEE09;">&nbsp;' . PPget_lang("Medium") .'&nbsp;</span>';
     break;
     case (($uv >=6 ) and ($uv < 8)):
       $uv = "UV: $uv<br/>" . '<span style="border: solid 1px; background-color: #FD9125;">&nbsp;' . PPget_lang("High") .'&nbsp;</span>';
     break;
     case (($uv >=8 ) and ($uv < 11)):
       $uv = "UV: $uv<br/>" . '<span style="border: solid 1px; color: #FFFFFF; background-color: #F63F37;">&nbsp;' . PPget_lang('Very&nbsp;High') . '&nbsp;</span>';
     break;
     case (($uv >= 11) ):
       $uv = "UV: $uv<br/>" . '<span style="border: solid 1px; color: #FFFF00; background-color: #807780;">&nbsp;' . PPget_lang("Extreme") .'&nbsp;</span>';
     break;
   } // end switch
   return $uv;
} // end get_UVrange


// determine Beaufort number based on wind speed and units
function PPgetBeaufort ( $wind, $units) {

global $showBeaufort,$BeaufortMPH,$BeaufortKPH,$BeaufortKTS,$BeaufortMS,$doDebug,$Status;
   switch ($units) {
     case 'mph': $winds = $BeaufortMPH; break;
	 case 'kph': $winds = $BeaufortKPH; break;
	 case 'km/h': $winds = $BeaufortKPH; break;
	 case 'm/s': $winds = $BeaufortMS;  break;
	 case 'mps': $winds = $BeaufortMS;  break;
	 case 'kts': $winds = $BeaufortKTS; break;
	 default: $winds = $BeaufortMPH;
   } // end switch
   $Bft = 0;
   for ($i=0;$i<12;$i++) {
      if ($wind < $winds[$i]) {
	    $Bft = $i;
		break;
	  }
   }
   if ($i > 11 and ! $Bft) { $Bft = 12; };
   if($doDebug) {
	   $Status .= "<!-- '$wind' '$units' bft=$Bft i=$i -->\n";
   }
   return($Bft);

}// end PPgetBeaufort

// perform Beaufort display if desired by showBeaufort setting
function PPdisplayBeaufort ($Bft) {
  global $showBeaufort, $BeaufortText;
  if ($Bft < 0 or $Bft > 12 ) { return(""); }

  if ($showBeaufort == 'T') { return( PPget_lang($BeaufortText[$Bft]) ); }

  if ($showBeaufort == 'V') { return("$Bft Bft"); }

  return("");

}// end PPdisplayBeaufort
?>
