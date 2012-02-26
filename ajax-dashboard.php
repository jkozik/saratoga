<?php
// ajax-dashboard.php  - Ken True - webmaster@saratoga-weather.org
//  Version 1.00 - 22-Dec-2007 - Initial release
//  Version 1.01 - 04-Jan-2008 - refit for WD/AJAX/PHP package
//  Version 1.02 - 18-Jan-2008 - fixed icon=34 to ra1.jpg (stopped raining)
//  Version 1.03 - 21-Feb-2008 - added icon=35 for wind/rain
//  Version 1.04 - 25-Feb-2008 - added options for UV and/or Solar display
//  Version 1.05 - 11-Mar-2008 - changed to .png for wind-rose defaults (Carterlake/AJAX/PHP templates)
//  Version 1.06 - 11-Mar-2008 - fixed fixup_time routine
//  Version 1.07 - 29-Mar-2008 - added debug features, honor $SITE['WXtags'] for testtags.php include
//  Version 1.08 - 24-Apr-2008 - added langtrans functions for languages other than English + language specific
//                               windrose.  Also support for changing ajaxWDwx.js to different language.
//  Version 1.09 - 29-Jun-2008 - added showSnow, snowShowTemp to display Snow values instead of Rain
//  Version 1.10 - 11-Jul-2008 - added moonphase fix for WD-Mac users
//  Version 1.11 - 25-Sep-2008 - minor translation fixes
//  Version 1.12 - 04-Oct-2008 - added translation for monthname/dayname from WD
//  Version 1.13 - 04-Nov-2008 - improved Snow handling+ added optional last-year, WU-Almanac info
//  Version 1.14 - 03-Jul-2009 - thermometer.php?t=, PHP5 support for TZ changes, remove Notice: alerts
//  Version 1.15 - 13-Jan-2011 - mods for universal template sets
//  Version 1.16 - 04-Feb-2011 - update to remove Notify: and Deprecated: errata with PHP5+
//  Version 1.17 - 11-Feb-2011 - fix display of dayswithnorain/dayswithrain in Rain section
//  Version 1.18 - 17-Feb-2011 - support comma for decimal point from weather station inputs
//  Version 1.19 - 23-Mar-2011 - fix display of baro trend arrow
//  Version 1.20 - 30-May-2011 - fix translation for Baro Trend
//  Version 1.21 - 01-Oct-2011 - added support for alternative animated icon set from http://www.meteotreviglio.com/
//  Version 1.22 - 13-Nov-2011 - some fixes for UTF-8 languages
//  Version 1.23 - 29-Nov-2011 - improved language translations for conditions+ DavisVP handling for forecast
//  Version 1.24 - 14-Jan-2012 - added support for WeatherSnoop display
//  Version 1.25 - 22-Jan-2012 - added fix for dd.mm.yyyy format dates
//
$ADBversion = 'ajax-dashboard.php - Version 1.25 - 22-Jan-2012 - Multilingual';
//error_reporting(E_ALL);
// --- settings for standalone use --------------------------
$Lang    = 'en';
$uomTemp = '&deg;F';
$uomBaro = ' inHg';
$uomWind = ' mph';
$uomRain = ' in';
$uomSnow = ' in';
$uomPerHour = '/hr';
$imagesDir = './ajax-images/';  // directory for ajax-images with trailing slash
$condIconDir = './ajax-images/'; // directory for condition icons
$condIconType = '.jpg'; // default type='.jpg' -- use '.gif' for animated icons from http://www.meteotreviglio.com/
//  $timeFormat = 'D, d-M-Y g:ia T';  // Fri, 31-Mar-2006 6:35pm TZone
$timeFormat = 'd-M-Y g:ia';  // Fri, 31-Mar-2006 6:35pm TZone
$timeOnlyFormat = 'g:ia';    // h:mm[am|pm];
//$timeOnlyFormat = 'H:i';     // hh:mm
$dateOnlyFormat = 'd-M-Y';   // d-Mon-YYYY
$WDdateMDY = true;     // true=dates by WD are 'month/day/year'
//                     // false=dates by WD are 'day/month/year'

$ourTZ = "America/Los_Angeles";  //NOTE: this *MUST* be set correctly to
// translate UTC times to your LOCAL time for the displays.
//
// optional settings for the Wind Rose graphic in ajaxwindiconwr as wrName . winddir . wrType
$wrName   = 'wr-';       // first part of the graphic filename (followed by winddir to complete it)
$wrType   = '.png';      // extension of the graphic filename
$wrHeight = '58';        // windrose graphic height=
$wrWidth  = '58';        // windrose graphic width=
$wrCalm   = 'wr-calm.png';  // set to full name of graphic for calm display ('wr-calm.gif')
$haveUV   = true;        // set to false if no UV sensor
$haveSolar = true;       // set to false if no Solar sensor
$WXtags  =  'testtags.php';  // source of our weather variables
$fcstorg =   'NWS'; // default forecast organization ('NWS','EC','WU','WXSIM')
$fcstscript = 'advforecast2.php'; // default forecast script
//            NWS - 'advforecast2.php', EC - 'ec-forecast.php', 
//            WU - 'WU-forecast.php', WXSIM - 'plaintext-parser.php',
$UVscript		= 'get-UV-forecast-inc.php'; // worldwide forecast script for UV Index
//	comment out above line to exclude UV forecast
$DavisVP	= true; 	// set to false if weather station is not a Davis VP
$showSnow	= true;		// set to false if Snow values not recorded manually in WD
$showSnowTemp = 4;		// display Snow instead of Rain when temp (C) is <= this temperature.
$decimalComma = false;  // set to true to process numbers with a comma for a decimal point
// --- end of settings for standalone use
//
include_once("common.php");  // for language translation
include_once("Settings.php"); 
// overrides from Settings.php if available
global $SITE,$forwardTrans,$reverseTrans;
$commaDecimal = false;
if (isset($SITE['lang'])) 	{$Lang = $SITE['lang'];}
if (isset($SITE['uomTemp'])) 	{$uomTemp = $SITE['uomTemp'];}
if (isset($SITE['uomBaro'])) 	{$uomBaro = $SITE['uomBaro'];}
if (isset($SITE['uomWind'])) 	{$uomWind = $SITE['uomWind'];}
if (isset($SITE['uomRain'])) 	{$uomRain = $SITE['uomRain'];}
if (isset($SITE['uomSnow'])) 	{$uomSnow = $SITE['uomSnow'];}
if (isset($SITE['uomPerHour'])) {$uomPerHour = $SITE['uomPerHour'];}
if (isset($SITE['imagesDir'])) 	{$imagesDir = $SITE['imagesDir'];}
if (isset($SITE['timeFormat'])) {$timeFormat = $SITE['timeFormat'];}
if (isset($SITE['timeOnlyFormat'])) {$timeOnlyFormat = $SITE['timeOnlyFormat'];}
if (isset($SITE['dateOnlyFormat'])) {$dateOnlyFormat = $SITE['dateOnlyFormat'];}
if (isset($SITE['WDdateMDY']))  {$WDdateMDY = $SITE['WDdateMDY'];}
if (isset($SITE['tz'])) 		{$ourTZ = $SITE['tz'];}
if (isset($SITE['UV'])) 		{$haveUV = $SITE['UV'];}
if (isset($SITE['SOLAR'])) 		{$haveSolar = $SITE['SOLAR'];}
if (isset($SITE['WXtags'])) 	{$WXtags = $SITE['WXtags'];}
if (isset($SITE['fcstorg']))	{$fcstorg = $SITE['fcstorg'];}
if (isset($SITE['fcstscript']))	{$fcstscript = $SITE['fcstscript'];}
if (isset($SITE['UVscript'])) 	{$UVscript = $SITE['UVscript'];}
if (isset($SITE['DavisVP'])) 	{$DavisVP = $SITE['DavisVP'];}
if (isset($SITE['showSnow'])) 	{$showSnow = $SITE['showSnow'];}
if (isset($SITE['commaDecimal'])) 	{$commaDecimal = $SITE['commaDecimal'];}
if (isset($SITE['fcsticonsdir'])) 	{$fcstIconDir = $SITE['fcsticonsdir'];}
if (isset($SITE['fcsticonstype'])) 	{$condIconType = $SITE['fcsticonstype'];}

if (isset($_REQUEST['sce']) && ( strtolower($_REQUEST['sce']) == 'view' or
    strtolower($_REQUEST['sce']) == 'show') ) {
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
// testing parameters// testing parameters
print "<!--  $ADBversion -->\n";

$DebugMode = false;
if (isset($_REQUEST['debug'])) {$DebugMode = strtolower($_REQUEST['debug']) == 'y'; }
if (isset($_REQUEST['UV'])) {$haveUV = $_REQUEST['UV'] <> '0';}
if (isset($_REQUEST['solar'])) {$haveSolar = $_REQUEST['solar'] <> '0';}
$displaySnow = false;  // assume normal rain display
if ($showSnow and strip_units($temperature) <= $showSnowTemp and 
  (isset($snowseasoncm) and isset($snowseasonin)) and
  ($snowseasoncm > 0.0 or $snowseasonin > 0.0) ) {
  $displaySnow = true;
}

if (isset($_REQUEST['snow'])) {$displaySnow = $_REQUEST['snow'] <> '0';}

$fcstby = isset($_REQUEST['fcstby'])?strtoupper($_REQUEST['fcstby']):'';
if ($fcstby == 'NWS') {
$SITE['fcstscript']		= 'advforecast2.php';  // USA-only NWS Forecast script
$SITE['fcstorg']		= 'NWS';    // set to 'NWS' for NOAA NWS
$fcstorg = $fcstby;
$fcstscript = $SITE['fcstscript'];
}

if ($fcstby == 'EC') {

$SITE['fcstscript']   = 'ec-forecast.php';    // Canada forecasts from Environment Canada
$SITE['fcstorg']		= 'EC';    // set to 'EC' for Environment Canada
$fcstorg = $fcstby;
$fcstscript = $SITE['fcstscript'];
}
if ($fcstby == 'WU') {

$SITE['fcstscript']	= 'WU-forecast.php';    // Non-USA, Non-Canada Wunderground Forecast Script
$SITE['fcstorg']		= 'WU';    // set to 'WU' for WeatherUnderground
$fcstorg = $fcstby;
$fcstscript = $SITE['fcstscript'];
}
if ($fcstby == 'WXSIM') {

$SITE['fcstscript']	= 'plaintext-parser.php';    // WXSIM forecast
$SITE['fcstorg']		= 'WXSIM';    // set to 'WXSIM' for WXSIM forecast
$fcstorg = $fcstby;
$fcstscript = $SITE['fcstscript'];
}
// end of special testing parms
print "<!-- fcstby='$fcstby' fcstscript='" . $SITE['fcstscript'] . "' fcstorg='". $SITE['fcstorg'] . "' -->\n"; 
// end of overrides from Settings.php

  include_once($WXtags);   // for the bulk of our data
  $doPrintNWS = false; // suppress printing of forecast by advforecast2.php
  $doPrint    = false; // suppress printing of ec-forecast.php
  $doPrintWU  = false; // suppress printing of WU-forecast.php
  include_once($fcstscript); // for the forecast icon stuff
  
// copy forecast script variables to carterlake-style names if script used is not advforecast2.php
if ($fcstorg == 'WU') {
  $forecasticons = $WUforecasticons;
  $forecasttemp = $WUforecasttemp;
  $forecasttitles = $WUforecasttitles;
  $forecasttext = $WUforecasttext;

} else if ($fcstorg == 'EC') {
  foreach ($forecasticon as $i => $temp) {
  	$forecasticons[$i] = $forecasttitles[$i] . "<br />\n" .
	               $forecasticon[$i] . "\n" .
				   $forecasttext[$i] . "\n";
  }
  $forecasttext = $forecastdetail;
  foreach ($forecastdays as $i => $temp) {
     $t = explode('<br />',$forecastdays[$i]);
     $forecasttitles[$i] = $t[0];
  }

} else if ($fcstorg == 'WXSIM') {

  $forecasticons = $WXSIMicons;
  $forecasttemp  = $WXSIMtemp;
  $forecasttitles = $WXSIMday;
  $forecasttext = $WXSIMtext;

}
if (isset($SITE['WXSIM']) and $SITE['WXSIM'] == true and $fcstorg <> 'WXSIM') {
  $doPrint = false;
  include_once($SITE['WXSIMscript']);
}
print "<!-- Lang='$Lang' -->\n";
if ($Lang <> 'en') { // try changing windrose graphics test for the Calm graphic
  $tfile = preg_replace('|^'.$wrName.'|',$wrName.$Lang.'-',$wrCalm);
  print "<!-- checking for '" . $imagesDir.$tfile . "' -->\n";
  if (file_exists($imagesDir.$tfile)) {
    print "<!-- alternate windrose for '" . $Lang . "' loaded -->\n";
	print '<script type="text/javascript">';
	print "\n var wrCalm = \"$tfile\";\n";
	print " var wrName = \"$wrName$Lang-\";\n";
	if(!isset($SITE['langTransloaded'])) {
		if(count($forwardTrans)>0) {
		   print "// Language translation for conditions by ajaxWDwx.js\n";
		}
		foreach ($forwardTrans as $key => $val) {
		   print "  langTransLookup['$key'] = '$val';\n";
		}
		$SITE['langTransloaded'] = true;
	}
	print "</script>\n";
	$wrCalm = $tfile;  // change the PHP dashboard settings too
	$wrName = $wrName . $Lang . '-';
	print "<!-- new wrName='$wrName', wrCalm='$wrCalm' -->\n";
  } 
}

// --- end of settings -------------------

if($SITE['WXsoftware'] == 'WD') {
// Sample from WD: $moonage = "Moon age: 10 days,10 hours,41 minutes,80%";	// current age of the moon (days since new moon)
// Sample from WD: $moonphase = "80%";	// Moon phase %
  $moonagedays = preg_replace('|^Moon age:\s+(\d+)\s.*$|is',"\$1",$moonage);
  if($moonphase == '') { // MAC version of WD is missing this value
    preg_match_all('|(\d+)|is',$moonage,$matches);
    $moonphase  = $matches[1][3];
	if(isset($matches[1][4])) {
	  $moonphase .= '.' . $matches[1][4]; // pick up decimal;
	  $moonphase = round($moonphase,0);
	}
	$moonphase .= '%';
  }
} else { // perform non-WD moon stuff
	$mooninfo = cGetMoonInfo();  /* Note:  getMoonInfo() is located in common.php */
/* returns $mooninfo of:
    [age] => 9 days, 11 hours, 57 minutes
    [ill] => 65
    [pic] => 8
    [phase] => Waxing Gibbous
    [NM] => 1294131882
    [NMGMT] => Tue, 04-Jan-2011 09:04 GMT
    [NMWD] => 09:04 GMT 04 January 2011
    [Q1] => 1294831983
    [Q1GMT] => Wed, 12-Jan-2011 11:33 GMT
    [Q1WD] => 11:33 GMT 12 January 2011
    [FM] => 1295472210
    [FMGMT] => Wed, 19-Jan-2011 21:23 GMT
    [FMWD] => 21:23 GMT 19 January 2011
    [Q3] => 1298590089
    [Q3GMT] => Thu, 24-Feb-2011 23:28 GMT
    [Q3WD] => 23:28 GMT 24 February 2011
    [Q4] => 1296700310
    [Q4GMT] => Thu, 03-Feb-2011 02:31 GMT
    [Q4WD] => 02:31 GMT 03 February 2011
    [FM2] => 1298018189
    [FM2GMT] => Fri, 18-Feb-2011 08:36 GMT
    [FM2WD] => 08:36 GMT 18 February 2011
*/
	print "<!-- cGetMoonInfo returns\n".print_r($mooninfo,true)." -->\n";
	if(!isset($moonphase)) {$moonphase = $mooninfo->ill.'%'; }
	if(!isset($newmoon))   {$newmoon   = $mooninfo->NMWD;    }
	if(!isset($nextnewmoon)) {$nextnewmoon = $mooninfo->Q4WD; } /*check this! */
	if(!isset($firstquarter)) {$firstquarter = $mooninfo->Q1WD; }
	if(!isset($lastquarter)) {$lastquarter = $mooninfo->Q3WD; }
	if(!isset($fullmoon))    {$fullmoon = $mooninfo->FMWD; }
	if(!isset($moonphasename)) {$moonphasename = $mooninfo->phase;}
	if(!isset($moonagedays))  {$moonagedays = $mooninfo->pic; }
	if(!isset($moonage))      {$moonage = 'Moon age: '.$mooninfo->age.','.$mooninfo->ill.'%'; }
    if(file_exists("get-USNO-sunmoon.php")) { 
      include_once("get-USNO-sunmoon.php");
	  $USNOdata = getUSNOsunmoon();
   }
   if(isset($USNOdata['sunrise']))                 {$sunrise =     $USNOdata['sunrise']; }  
   if(isset($USNOdata['sunrisedate']))             {$sunrisedate = $USNOdata['sunrisedate']; }  
   if(isset($USNOdata['sunset']))                  {$sunset =      $USNOdata['sunset']; }  
   if(isset($USNOdata['sunsetdate']))              {$sunsetdate =  $USNOdata['sunsetdate']; }  
   if(isset($USNOdata['moonrise']))                {$moonrise =    $USNOdata['moonrise']; }  
   if(isset($USNOdata['moonrisedate']))            {$moonrisedate= $USNOdata['moonrisedate']; }  
   if(isset($USNOdata['moonset']))                 {$moonset =     $USNOdata['moonset']; }  
   if(isset($USNOdata['moonsetdate']))             {$moonsetdate = $USNOdata['moonsetdate']; }  
   if(isset($USNOdata['moonphase']))               {$moonphasename = $USNOdata['moonphase']; }  
   if(isset($USNOdata['illumination']))            {$moonphase = $USNOdata['illumination']; }  
   if(isset($USNOdata['hoursofpossibledaylight'])) {$hoursofpossibledaylight = $USNOdata['hoursofpossibledaylight'];} 
}
# Set timezone in PHP5/PHP4 manner
  if (!function_exists('date_default_timezone_set')) {
	putenv("TZ=" . $ourTZ);
#	$Status .= "<!-- using putenv(\"TZ=$ourTZ\") -->\n";
    } else {
	date_default_timezone_set("$ourTZ");
#	$Status .= "<!-- using date_default_timezone_set(\"$ourTZ\") -->\n";
   }
  $UpdateDate = date($timeFormat,strtotime("$date_year-$date_month-$date_day  $time_hour:$time_minute:00"));
//  $UpdateDate = "$date_year-$date_month-$date_day  $time_hour:$time_minute:00";
$monthname = langtransstr($monthname);
$dayname = langtransstr(substr($dayname,0,3));
//
// Snow setup
if (preg_match('|in|i',$uomSnow) and isset($snowseasonin)) { // use USA measurements
  $snowseason = $snowseasonin;	// Snow for season you have entered under input daily weather, inches
  $snowmonth = $snowmonthin;	// Snow for month you have entered under input daily weather, inches
  $snowtoday = $snowtodayin;	// Snow for today you have entered under input daily weather, inches
  $snowyesterday = $snowyesterday;	// Yesterdays' snow
  $snownow = $snownowin;	// Current snow depth, inches.
} elseif (isset($snowseasoncm)) { // use Metric measurements
  $snowseason = $snowseasoncm;	// Snow for season you have entered under input daily weather, cm
  $snowmonth = $snowmonthcm;	// Snow for month you have entered under input daily weather, cm
  $snowtoday = $snowtodaycm;	// Snow for today you have entered under input daily weather, cm
  $snowyesterday = $snowyesterday;	// Yesterdays' snow
  $snownow = $snownowcm;	// Current snow depth, cm.
}
$decimalComma = (strpos($temperature,',') !==false)?true:false; // using comma for decimal point?
// --- end of initialization code ---------
?>
<!-- start of ajax-dashboard.php -->
<div class="ajaxDashboard" style="width: 632px;">
      <table width="620" border="0" cellpadding="2" cellspacing="1" style="border:solid; border-color: #CCCCCC;">
        <tr align="center">
          <td class="data1" colspan="4" style="text-align: center">
		    <span class="ajax" id="ajaxindicator"><?php langtrans('Updated'); ?>:</span>&nbsp;@ 
		    <span class="ajax" id="ajaxdate">
		    <?php echo fixup_date($date) . ' ' . fixup_time($time);
			  if(isset($timeofnextupdate)) { echo " - " . langtransstr('next update at') . " " . fixup_time($timeofnextupdate);} ?>
		    </span>&nbsp;<span class="ajax" id="ajaxtime"></span>
            <?php if(isset($SITE['ajaxScript'])) { ?>
            <script type="text/javascript">
<!--
document.write('<b> - <?php langtrans('updated'); ?> <span id="ajaxcounter"></span>&nbsp;<?php langtrans('sec ago'); ?></b>');
//-->
            </script>
            <?php } // there is a ajaxScript ?>
          </td>
        </tr>
        <tr align="center">
          <td class="datahead"><?php langtrans('Summary'); ?> / <?php langtrans('Temperature'); ?></td>
          <td class="datahead"><?php langtrans('Wind'); ?></td>
	  <?php if($displaySnow) { ?>
          <td class="datahead"><?php langtrans('Snow'); ?></td>
	  <?php } else { ?>
          <td class="datahead"><?php langtrans('Rain');
		  if(isset($snownow) and $snownow > 0) { echo ' / '.langtransstr('Snow Depth'); } ?></td>
	  <?php } // end displaySnow ?>
         <td class="datahead"><?php langtrans('Outlook'); ?></td>
        </tr>
        <tr align="left">
          <td valign="top" rowspan="5">
		    <table width="180" border="0" cellpadding="2" cellspacing="0">
			  <tr>
				<td align="center" valign="top" class="data1" style="text-align: center;border: none">
				<span class="ajax" id="ajaxconditionicon2">
				  <img src="<?php echo $condIconDir . newIcon($iconnumber) ?>"  
					alt="<?php $t1 = fixupCondition($Currentsolardescription);
						  echo $t1; ?>" 
					title="<?php echo $t1; ?>" height="58" width="55" />
				</span> 
				</td>
				<td class="data1" style="text-align: center;border: none"><span class="ajax" id="ajaxcurrentcond">
				  <?php echo $t1; ?> </span> 
				</td>
			  </tr>
              <tr align="center">
                <td align="center" valign="top" class="data1" style="text-align: center;border: none">
				  <span class="ajax" id="ajaxthermometer">
                  <?php $tstr = langtransstr("Currently") . ": $temperature, "
				   . langtransstr('Max') . ": $maxtemp, ".langtransstr('Min').": $mintemp"; ?>
                  <img src="thermometer.php?t=<?php echo strip_units($temperature);
				  if(isset($_REQUEST['wx'])) {echo "&amp;wx=".$_REQUEST['wx']; } ?>" alt="<?php echo $tstr; ?>"
		           title="<?php echo $tstr; ?>" height="170" width="54" /> </span> 
				</td>
                <td class="data1" style="text-align: center;border: none" valign="middle">
				  <span class="ajax" id="ajaxtemp" style="font-size:20px">
				    <?php echo strip_units($temperature) . $uomTemp; ?> 
				  </span>
				  <br/>
				  <span class="ajax" id="ajaxtemparrow"><?php 
				     if(isset($tempchangehour)) {
						 if($commaDecimal) {
							$Tnow = preg_replace('|,|','.',strip_units($temperature));
							$TLast = preg_replace('|,|','.',strip_units($tempchangehour)); 
						 } else {
							$Tnow = strip_units($temperature);
							$TLast = strip_units($tempchangehour); 
						 }
					 echo gen_difference($Tnow, $Tnow - $TLast, '',
			         langtransstr('Warmer').' %s'.$uomTemp.' '. langtransstr('than last hour') .'.',
			         langtransstr('Colder').' %s'.$uomTemp.' '. langtransstr('than last hour') .'.');
					 } // isset tempchangehour ?></span>
				  <br/><br/>
                  <span class="ajax" id="ajaxheatcolorword">
                    <?php  langtrans($heatcolourword); ?></span>
                  <br/><br/>
                  <?php if(isset($feelslike)) { ?>
                  <?php langtrans('Feels like'); ?>: <span class="ajax" id="ajaxfeelslike">
				    <?php echo $feelslike . $uomTemp; ?> 
					</span>
				  <br/><br/>
                  <?php } // $feelslike ?>
                  <?php if(isset($temp24hoursago)) { ?>
                  <?php langtrans('24-hr difference'); ?>
				  <br />
                  <?php 
					if($commaDecimal) {
						$Tnow = preg_replace('|,|','.',strip_units($temperature));
						$TLast = preg_replace('|,|','.',strip_units($temp24hoursago)); 
					 } else {
						$Tnow = strip_units($temperature);
						$TLast = strip_units($temp24hoursago); 
					 }

					echo gen_difference($Tnow, $TLast, $uomTemp,
					langtransstr('Warmer'). ' %s'.$uomTemp.' '.langtransstr('than yesterday at this time').'.',
					langtransstr('Colder'). ' %s'.$uomTemp.' '.langtransstr('than yesterday at this time').'.'); ?>
                  <?php } // $temp24hoursago ?>
                </td>
              </tr>
              <?php if(isset($mintemp) and $mintemp <> '-') { ?>
              <tr>
                <td colspan="2" align="center">
				  <table width="100%"  class="data1" style="font-size: 9px;border: none">
                    <tr>
                      <th>&nbsp;</th>
                      <th style="text-align: center;"><?php langtrans('Today'); ?></th>
                      <?php if(isset($maxtempyest) or isset($mintempyest)) { ?>
                      <th style="text-align: center;"><?php langtrans('Yesterday'); ?></th>
                      <?php } ?>
                    </tr>
                    <tr>
                      <td style="text-align: right;"><strong><?php langtrans('High:'); ?></strong></td>
                      <td style="text-align: center;">
					    <span class="ajax" id="ajaxtempmax">
                        <?php echo strip_units($maxtemp) . $uomTemp; ?>
                        </span><br />
                        <?php echo fixup_time($maxtempt); ?>
					  </td>
                      <?php if(isset($maxtempyest)) { ?>
                      <td style="text-align: center;"><?php 
			            echo strip_units($maxtempyest) . $uomTemp; ?>
                        <br />
                        <?php echo fixup_time($maxtempyestt); ?>
					  </td>
                      <?php } ?>
                    </tr>
                    <tr>
                      <td style="text-align: right;"><strong><?php langtrans('Low:'); ?></strong></td>
                      <td style="text-align: center;">
					    <span class="ajax" id="ajaxtempmin">
                        <?php 
			               echo strip_units($mintemp) . $uomTemp; ?>
                        </span><br />
                        <?php echo fixup_time($mintempt); ?>
					  </td>
                      <?php if(isset($mintempyest)) { ?>
                      <td style="text-align: center;"><?php 
			               echo strip_units($mintempyest) . $uomTemp; ?>
                        <br />
                        <?php echo fixup_time($mintempyestt); ?>
					  </td>
                      <?php } ?>
                    </tr>
                  </table>
				</td>
              </tr>
              <?php } // end for display min/max table ?>
            </table>
		  </td>
          <td valign="middle">
		    <table width="180" border="0" cellpadding="2" cellspacing="0">
              <tr>
                <td class="data1" valign="middle" align="center" style="border: none">
				  <img src="<?php echo $imagesDir; ?>spacer.gif" width="1" 
				    height="<?php echo $wrHeight; ?>" alt=" " align="left"/>
				  <span class="ajax" id="ajaxwindiconwr">
				  <?php $wr = $imagesDir . $wrName . $dirlabel . $wrType; // normal wind rose
				        $wrtext = langtransstr('Wind from') ." " . langtransstr($dirlabel);
						if ( (strip_units($avgspd) + strip_units($gstspd) < 0.1 ) and
						     ($wrCalm <> '') ) { // use calm instead
						  $wr = $imagesDir . $wrCalm;
						  $wrtext = $bftspeedtext;
						 }
						
				  ?>
				    <img src="<?php echo $wr; ?>" 
					height="<?php echo $wrHeight; ?>" width="<?php echo $wrWidth; ?>" 
					title="<?php echo $wrtext; ?>" 
					alt="<?php echo $wrtext; ?>"  style="text-align:center" />
				   </span> 
				 </td>
                <td valign="middle" class="data1" style="text-align: center; border: none;" >
					<span class="ajax" id="ajaxwinddir"><?php langtrans($dirlabel); ?></span><br/> 
					<span class="ajax" id="ajaxwind">
					 <?php echo strip_units($avgspd); ?>
					</span><br/>
					<span class="meas"><?php langtrans('Gust'); ?>:<br/></span> 
					<span class="ajax" id="ajaxgust">
					  <?php echo strip_units($gstspd). " $uomWind"; ?>
					</span> 
				</td>
              </tr>
               <tr>
                <td colspan="2" class="data1" align="center" style="text-align:center">
				  <span class="ajax" id="ajaxbeaufortnum"><?php echo strip_units($beaufortnum); ?></span> Bft - <i>
				  <span class="ajax" id="ajaxbeaufort"><?php langtrans($bftspeedtext); ?></span></i>
				</td>
              </tr>
             <?php if(isset($maxgst) and isset($maxgstt)) { ?>
             <tr>
                <td colspan="2" class="data1" align="center">
				  <?php langtrans('Today'); ?>: 
				  <span class="ajax" id="ajaxwindmaxgust"><?php echo strip_units($maxgst). " $uomWind"; ?></span> 
				  <span class="ajax" id="ajaxwindmaxgusttime"><?php echo fixup_time($maxgstt); ?></span>
				</td>
              </tr>
              <?php } // end maxgust/maxgustt display ?>
              <?php if(isset($mrecordwindgust)) { ?>
              <tr>
                <td colspan="2" class="data1" align="center">
				  <?php langtrans('Gust Month'); ?>: <?php echo $mrecordwindgust. " $uomWind"; ?> 
                  <?php if(isset($mrecordhighgustday)) { ?>
				  <?php echo $monthname . " " . $mrecordhighgustday; ?> 
                  <?php } // $mrecordhighgustday ?>
				</td>
              </tr>
              <?php } // $mrecordwindgust ?>
            </table>
		  </td>
          <td valign="middle"><table width="166" border="0" cellpadding="2" cellspacing="0">
    <?php if ($displaySnow) { // display Snow instead of Rain ?>
              <tr>
                <td class="data1"><?php langtrans('Snow Today'); ?>:</td>
                <td style="text-align: right;" class="data1">
                    <?php echo strip_units($snowtoday) . $uomSnow; ?>
				</td>
              </tr>
              <tr>
                <td class="data1" nowrap="nowrap"><?php langtrans('Snow Yesterday') ?>: </td>
                <td style="text-align: right;" class="data1">
                  <?php echo strip_units($snowyesterday) . $uomSnow; ?>
				</td>
              </tr>
             <tr>
                <td class="data1"><?php langtrans('This Month'); ?>:</td>
                <td style="text-align: right;" class="data1">
                  <?php echo strip_units($snowmonth) . $uomSnow; ?>
				</td>
              </tr>
              <tr>
                <td class="data1" nowrap="nowrap"><?php langtrans('Winter Total'); ?>:</td>
                <td style="text-align: right;" class="data1">
                    <?php echo strip_units($snowseason) . $uomSnow; ?>
				</td>
              </tr>
              <tr>
                <td class="data1" nowrap="nowrap"><?php langtrans('Snow Depth'); ?>:</td>
                <td style="text-align: right;" class="data1">
                    <?php echo strip_units($snownow) . $uomSnow; ?>
				</td>
              </tr>
              <tr>
                <td class="data1" colspan="2">
				<?php 
				   print "$snowdaysthismonth " ;
				   print ($snowdaysthismonth <> 1)?
					langtransstr('snow days in'):
					langtransstr('snow day in');
				   print " " . $monthname . ".";
				 ?>
                </td>
              </tr>
	<?php } else { // display Rain ?>
              <tr>
                <td class="data1"><?php langtrans('Rain Today'); ?>:</td>
                <td style="text-align: right;" class="data1">
				  <span class="ajax" id="ajaxrain">
                    <?php echo strip_units($dayrn) . $uomRain; ?>
                  </span>
				</td>
              </tr>
              <tr>
                <td class="data1"><?php langtrans('Rain Rate'); ?> (<?php echo $uomPerHour; ?>):</td>
                <td style="text-align: right;" class="data1">
				  <span class="ajax" id="ajaxrainratehr">
                  <?php echo strip_units($currentrainratehr) . $uomRain; ?>
                  </span>
				</td>
              </tr>
              <?php if(isset($yesterdayrain)) { ?>
              <tr>
                <td class="data1" nowrap="nowrap"><?php langtrans('Rain Yesterday') ?>: </td>
                <td style="text-align: right;" class="data1">
				  <span class="ajax" id="ajaxrainydy">
                  <?php echo strip_units($yesterdayrain) . $uomRain; ?>
                  </span>
				</td>
              </tr>
              <?php } // end $yesterdayrain ?>
			  <?php if (isset($vpstormrain)) { // Storm Rain is a Davis VP thing ?>
               <tr>
                <td class="data1" nowrap="nowrap"><?php langtrans('Storm Rain'); ?>: </td>
                <td style="text-align: right;" class="data1">
                  <?php echo strip_units($vpstormrain).$uomRain; ?>
				</td>
              </tr>
			  <?php } // end of DavisVP specific variable ?>
             <tr>
                <td class="data1"><?php langtrans('This Month'); ?>:</td>
                <td style="text-align: right;" class="data1">
				  <span class="ajax" id="ajaxrainmo">
                  <?php echo strip_units($monthrn) . $uomRain; ?>
                  </span>
				</td>
              </tr>
              <tr>
                <td class="data1" nowrap="nowrap"><?php langtrans('Season Total'); ?>:</td>
                <td style="text-align: right;" class="data1">
				  <span class="ajax" id="ajaxrainyr">
                    <?php echo strip_units($yearrn) . $uomRain; ?>
                  </span>
				</td>
              </tr>
			  <?php if(isset($snownow) and $snownow > 0) { // show depth of snow if needed ?>
              <tr>
                <td class="data1" nowrap="nowrap"><?php langtrans('Snow Depth'); ?>:</td>
                <td style="text-align: right;" class="data1">
                    <?php echo strip_units($snownow) . $uomSnow; ?>
				</td>
              </tr>
			  <?php } // end show-depth-of-snow if needed ?>
              <?php if(isset($dayswithnorain) or isset($dayswithrain)) { // Unique WD value ?>
              <tr>
                <td class="data1" colspan="2">
				  <?php 
                    if (isset($dayswithrain) and $dayswithrain > 0) { 
					   print "$dayswithrain " ;
					   print ($dayswithrain > 1)?
					    langtransstr('rain days in'):
					    langtransstr('rain day in');
					   print " " . $monthname . ".";
					  } elseif (isset($dayswithnorain)) {
					   
					   if(strpos($temperature,',') !== false) {
						   $t = preg_replace('|,|','.',$dayrn);
					   } else {
						   $t = $dayrn;
					   }
					   if($t > 0.00) {$dayswithnorain = 0;} // raining today so 0 days since last rain
					   
					   print "$dayswithnorain "; 
					   print ($dayswithnorain > 1)?
					   langtransstr('days since last rain'):
					   langtransstr('day since last rain');
					   print  "."; 
					  } ?>
                </td>
              </tr>
              <?php } // end dayswithnorain/dayswithrain display for WD ?>
	 <?php } // end snow or rain display ?>
            </table>
		  </td>
          <td align="center" valign="middle">
		    <table border="0" cellpadding="2" cellspacing="0">
              <tr>
                <td class="data1" align="center" style="font-size: 8pt; border:none;text-align: center">
				  <?php print $forecasticons[1]; ?>
			   </td>
              </tr>
            </table>
		  </td>
        </tr>
        <tr align="center" valign="top">
          <td class="datahead"><?php langtrans('Humidity'); ?> &amp; <?php langtrans('Barometer'); ?></td>
          <td class="datahead"><?php langtrans('Almanac'); ?></td>
          <td class="datahead"><?php langtrans('Moon'); ?></td>
        </tr>
        <tr align="center" valign="middle">
          <td valign="middle"><table width="180" border="0" cellpadding="2" cellspacing="0">
              <tr>
                <td class="data1"><?php langtrans('Humidity'); ?>:</td>
                <td style="text-align: center;" class="data1">
				  <span class="ajax" id="ajaxhumidity">
                    <?php echo $humidity; ?>
                  </span>%
                  <?php
				   if(isset($humchangelasthour)) {
					 $t1 = preg_replace('|\s|s','',$humchangelasthour);
					 if($commaDecimal) {
						$Tnow = preg_replace('|,|','.',strip_units($humidity));
						$TLast = preg_replace('|,|','.',strip_units($t1)); 
					 } else {
						$Tnow = strip_units($humidity);
						$TLast = strip_units($t1); 
					 }
					 
					 echo gen_difference($Tnow, $Tnow-$TLast, '',
					 langtransstr('Increased').' %s%% '.langtransstr('since last hour').'.',
					 langtransstr('Decreased').' %s%% '.langtransstr('since last hour').'.');
				   } ?>
				</td>
              </tr>
              <tr>
                <td class="data1"><?php langtrans('Dew Point'); ?>: </td>
                <td style="text-align: center;" class="data1">
				  <span class="ajax" id="ajaxdew">
                  <?php $t1 = strip_units($dewpt); echo $t1 . $uomTemp;  ?>
                  </span>
                  <?php 
				  if(isset($dewchangelasthour)) {
					if($commaDecimal) {
						$Tnow = preg_replace('|,|','.',strip_units($dewpt));
						$TLast = preg_replace('|,|','.',strip_units($dewchangelasthour)); 
					 } else {
						$Tnow = strip_units($dewpt);
						$TLast = strip_units($dewchangelasthour); 
					 }
					 echo gen_difference($Tnow, $Tnow-$TLast, '',
					 langtransstr('Increased').' %s'.$uomTemp.' '.langtransstr('since last hour').'.',
					 langtransstr('Decreased').' %s'.$uomTemp.' '.langtransstr('since last hour').'.'); 
				  } ?>
                </td>
              </tr>
              <tr>
                <td class="data1"><?php langtrans('Barometer'); ?>:</td>
                <td style="text-align: center;" class="data1">
				  <span class="ajax" id="ajaxbaro">
                    <?php $t1 = strip_units($baro); echo $t1 . $uomBaro; ?>
                  </span>
				  <span class="ajax" id="ajaxbaroarrow">
                  <?php 
				  if(isset($trend)) {
					if($commaDecimal) {
						$Tnow = preg_replace('|,|','.',strip_units($baro));
						$TLast = preg_replace('|,|','.',strip_units($trend)); 
					 } else {
						$Tnow = strip_units($baro);
						$TLast = strip_units($trend); 
					 }
					 $decPts = 1;
					 if(preg_match('|in|i',$uomBaro)) {$decPts = 2; }
					 echo gen_difference($Tnow, $Tnow-$TLast, '',
					 langtransstr('Rising') . ' %s '.$uomBaro.$uomPerHour,
					 langtransstr('Falling') . ' %s '.$uomBaro.$uomPerHour, $decPts); 
				  } ?>
                   </span>
				 </td>
              </tr>
              <tr>
                <td class="data1"><?php langtrans('Baro Trend'); ?>: </td>
                <td style="text-align: center;" class="data1">
				  <span class="ajax" id="ajaxbarotrendtext">
                  <?php  langtrans($pressuretrendname); ?>
                  </span>
				</td>
              </tr>
            </table>
		  </td>
          <td align="center" valign="middle">
		    <table width="166" border="0" cellpadding="2" cellspacing="0">
              <tr>
                <td class="data1"><?php langtrans('Sunrise'); ?>:</td>
                <td style="text-align: right;" class="data1">
				  <?php echo fixup_time($sunrise); ?>
			    </td>
              </tr>
              <tr>
                <td class="data1"><?php langtrans('Sunset'); ?>:</td>
                <td style="text-align: right;" class="data1">
				  <?php echo fixup_time($sunset); ?>
				</td>
              </tr>
              <tr>
                <td class="data1"><?php langtrans('Moonrise'); ?>:</td>
                <td style="text-align: right;" class="data1">
				  <?php echo fixup_time($moonrise); ?>
				</td>
              </tr>
              <tr>
                <td class="data1"><?php langtrans('Moonset'); ?>:</td>
                <td style="text-align: right;" class="data1">
				  <?php echo fixup_time($moonset); ?>
				</td>
              </tr>
            </table>
		  </td>
          <td rowspan="3" valign="middle" align="center">
		    <table border="0" cellpadding="4" cellspacing="0">
              <tr>
                <td class="data1" style="text-align: center; border: none;">
				  <?php langtrans(moonphase($moonage)); ?>
				</td>
              </tr>
              <tr>
                <td style="text-align: center;"><img src="<?php 
				  echo $imagesDir . 'moon' . $moonagedays; ?>.gif" 
				  alt="<?php $t1 =  moonphase($moonage) . ", Moon at $moonagedays days in cycle";
				  echo $t1; ?>" 
				  title="<?php echo $t1; ?>" 
				  width="48" height="48" style="border: 0;" />
			    </td>
              </tr>
              <tr>
                <td class="data1" style="text-align: center; border: none;" >
				  <?php  echo $moonphase ?>
                    <br />
                  <?php langtrans('Illuminated'); ?>
				</td>
              </tr>
            </table>
		  </td>
        </tr>
<?php 
// construct 4 flavors of the UV/ Solar display based on status of $haveUV and $haveSolar
// $haveUV && $haveSolar :  UV on left, Solar on Right
// $haveUV && !$haveSolar:  UV on left, UV Forecast on right
// !$haveUV && $haveSolar:  UV forecast on left, Solar on right
// !$haveUV && !$haveSolar: UV Forecast on left, UV Forecast+1 on right

$leftHead = langtransstr('UV Index');
if (!$haveUV) { $leftHead = langtransstr('UV Index Forecast'); }

$rightHead = langtransstr('Solar Radiation');
if (!$haveSolar) {$rightHead = langtransstr('UV Index Forecast'); }

$UVfcstDate = array_fill(0,9,'');   // initialize the return arrays
$UVfcstUVI  = array_fill(0,9,'n/a');

if (isset($UVscript)) { // load up the UV forecast script
  @include_once($UVscript);
}
$UVptr = 0;  // index for which day to use
?>
        <tr>
          <td class="datahead" style="text-align:center"><?php echo $leftHead; ?></td>
          <td class="datahead" style="text-align:center"><?php echo $rightHead; ?></td>
        </tr>
        <tr>
          <td align="center">
		    <table width="180" border="0" cellpadding="2" cellspacing="0">
<?php
  if ($haveUV) {  //  Have a UV sensor .. show realtime data
?>
              <tr>
                <td class="data1" nowrap="nowrap" style="text-align: center; ">&nbsp;&nbsp;
				  <span class="ajax" id="ajaxuv">
				    <?php echo $VPuv; ?>
				  </span>&nbsp;&nbsp;
				  <span class="ajax" id="ajaxuvword">
				    <?php echo get_UVrange($VPuv); ?>
				  </span>
				</td>
              </tr>
              <?php if(isset($highuv) and isset($highuvtime)) { ?>
              <tr>
                <td class="data1" nowrap="nowrap" style="text-align:center; font-size: 8pt;">
				  <?php langtrans('High:'); ?> <?php echo $highuv; ?> @&nbsp;<?php echo fixup_time($highuvtime); ?> 
				</td>
              </tr>
              <?php } // have high UV and time ?>
<?php } else {  //  don't have UV sensor .. show UV forecast instead ?>
              <tr>
                <td class="data1" nowrap="nowrap" style="text-align: center; ">
				    <?php echo $UVfcstDate[$UVptr]; ?>
				</td>
              </tr>
              <tr>
                <td class="data1" nowrap="nowrap" style="text-align:center; font-size: 8pt;">
				    <b><a href="<?php echo htmlspecialchars($UV_URL); ?>" title="<?php echo strip_tags($requiredNote); ?>"><?php echo $UVfcstUVI[$UVptr]; ?></a></b>
				  &nbsp;&nbsp;
				    <?php echo get_UVrange($UVfcstUVI[$UVptr]); ?>
				</td>
              </tr>
			  <?php $UVptr++; // increment counter in case they have no solar either ?>
<?php } // end $haveUV  ---------------------------------------- ?>
            </table>
		  </td>
          <td align="center">
		    <table width="166" border="0" cellpadding="2" cellspacing="0">
<?php if ($haveSolar) {  // Have a Solar Sensor  show current values ?>
              <tr>
                <td class="data1" style="text-align: center;" nowrap="nowrap" >
				  <span class="ajax" id="ajaxsolar">
				    <?php echo $VPsolar; ?></span> W/m<sup>2</sup>
                    <?php if(isset($currentsolarpercent)) { // display only if data available ?> 
					 (<span class="ajax" id="ajaxsolarpct"><?php echo strip_units($currentsolarpercent); ?>
                     </span>%)
                     <?php } // end of $currentsolarpercent ?>
				</td>
              </tr>
              <?php if(isset($highsolar) and isset($highsolartime)) { ?>
              <tr>
                <td style="text-align: center; font-size: 8pt;" class="data1">
				  <?php langtrans('High:'); ?>
                  <?php echo $highsolar; ?>
                  @&nbsp;
                  <?php echo fixup_time($highsolartime); ?>
				</td>
              </tr>
              <?php } // have high solar amt+time ?>
<?php } else { // don't have solar  show UV forecast instead ?>
              <tr>
                <td class="data1" nowrap="nowrap" style="text-align: center; ">
				    <?php echo $UVfcstDate[$UVptr]; ?>
				</td>
              </tr>
              <tr>
                <td class="data1" nowrap="nowrap" style="text-align:center; font-size: 8pt;">
				    <b><a href="<?php echo htmlspecialchars($UV_URL); ?>" title="<?php echo strip_tags($requiredNote); ?>"><?php echo $UVfcstUVI[$UVptr]; ?></a></b>
				  &nbsp;&nbsp;
				    <?php echo get_UVrange($UVfcstUVI[$UVptr]); ?>
				</td>
              </tr>
<?php } // end $haveSolar  ?>
            </table>
		  </td>
        </tr>
     </table>
<table width="620" border="0" cellpadding="3" cellspacing="3">
   <tr>
     <td style="text-align: left" class="datahead">&nbsp;<?php print $fcstorg; ?> <?php langtrans('Weather Forecast'); ?>&nbsp; -  <?php langtrans('Outlook'); ?>:&nbsp;<?php echo $forecasttitles[0]; ?> &amp; <?php echo $forecasttitles[1]; ?></td>
     </tr>
     <tr>
       <td align="center">
	    <table width="620" border="0" cellpadding="3" cellspacing="3">
           <tr>
		   <td class="data1" style="width: 80px;font-size: 8pt;border: none;text-align: center" valign="middle" align="center"><strong><?php echo $forecasticons[0] . "</strong><br />" . $forecasttemp[0]; ?></td>
           <td style="width: 504px;" class="data1"><?php print "<b>$fcstorg " . langtransstr('forecast') . ":</b> " . $forecasttext[0] . "<br />\n";
		   if ($fcstorg <> 'WXSIM' and isset($WXSIMtext[0]) ) {
		      print '              <b>WXSIM ' . langtransstr('forecast') . ':</b> ' . $WXSIMtext[0] . "<br/>\n";
		   }
		   if (isset($SITE['DavisVP']) and $SITE['DavisVP'] and isset($vpforecasttext) and $vpforecasttext <> '') {
		      print '		   	   <b>Davis VP+ ' . langtransstr('forecast') . ':</b> <span style="color: green; font-size:9pt">' . ucfirst($vpforecasttext) . "</span>"; 
			} ?></td>
           </tr>
		   <tr><td colspan="2">&nbsp;</td></tr>
           <tr>
		   <td class="data1" style="width: 80px;font-size: 8pt; border:none; text-align: center" valign="middle" align="center"><strong><?php echo $forecasticons[1] . "</strong><br />" . $forecasttemp[1]; ?></td>
           <td style="width: 504px;" class="data1"><?php print "<b>$fcstorg " . langtransstr('forecast') . ":</b> " . $forecasttext[1];
		   if ($fcstorg <> 'WXSIM' and isset($WXSIMtext[1]) ) {
		      print '              <br/><b>WXSIM ' . langtransstr('forecast') . ':</b> ' . $WXSIMtext[1] . "\n";
		   }
		    ?>			</td>
           </tr>
       </table>
	 </td>
   </tr>
 </table>
<?php if($condIconType <> '.jpg') {
	print "<small>".langtransstr('Animated icons courtesy of')." <a href=\"http://www.meteotreviglio.com/\">www.meteotreviglio.com</a>.</small>";
} 
?>

 </div>
 <!-- end of ajax-dashboard.php -->

<?php

//=========================================================================
//
// Functions
//
//=========================================================================
//  generate an up/down arrow to show differences

function gen_difference( $nowTemp, $yesterTemp, $Legend, $textUP, $textDN, $DP=1) {
// version 1.01 - handle ',' as decimal point on input
  global $imagesDir,$commaDecimal,$DebugMode;
  if($commaDecimal) {
    $tnowTemp = preg_replace('|,|','.',strip_units($nowTemp));
    $tyesterTemp = preg_replace('|,|','.',strip_units($yesterTemp));
  } else {
	$tnowTemp = strip_units($nowTemp);
	$tyesterTemp = strip_units($yesterTemp);
  }
  $diff = round(($tnowTemp - $tyesterTemp),3);
  $absDiff = abs($diff);
  $diffStr = sprintf("%01.".$DP."F",$diff);
  $absDiffStr = sprintf("%01.".$DP."F",$absDiff);
  if($commaDecimal) {
	 $absDiffStr = preg_replace('|\.|',',',$absDiffStr);
  }
  if($DebugMode) {
	  echo "<!-- gen_difference DP=$DP now='$nowTemp':'$tnowTemp' yest='$yesterTemp':'$tyesterTemp' dif='$diff':'$diffStr' absDiff='$absDiff':'$absDiffStr' -->\n";
	  echo "<!-- txtUP='$textUP' txtDN='$textDN' Legend='$Legend' -->\n";
  }
  if ($diffStr == '0.0') {
 // no change

$image = '&nbsp;'; 
  
  } elseif ($diffStr > '0.0') {
// today is greater 
$msg = sprintf($textUP,$absDiffStr); 
$image = "<img src=\"${imagesDir}rising.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 1px 3px;\" />";

  
  } else {
// today is lesser
$msg = sprintf($textDN,$absDiffStr); 
$image = "<img src=\"${imagesDir}falling.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 1px 3px;\" />";
   
  }
   if ($Legend) {
       return ($diff . $Legend . $image);
	} else {
	   return $image;
	}
}

//=========================================================================
//  decode UV to word+color for display

function get_UVrange ( $inUV ) {
// figure out a text value and color for UV exposure text
//  0 to 2  Low
//  3 to 5 	Moderate
//  6 to 7 	High
//  8 to 10 Very High
//  11+ 	Extreme
   if(strpos($inUV,',') !== false ) {
	   $uv = preg_replace('|,|','.',$inUV);
   } else {
	   $uv = $inUV;
   }
   switch (TRUE) {
     case ($uv == 0):
       $uv = langtransstr('None');
     break;
     case (($uv > 0) and ($uv < 3)):
       $uv = '<span style="border: solid 1px; color: black; background-color: #A4CE6a;">&nbsp;' . langtransstr('Low') . '&nbsp;</span>';
     break;
     case (($uv >= 3) and ($uv < 6)):
       $uv = '<span style="border: solid 1px; color: black; background-color: #FBEE09;">&nbsp;' . langtransstr('Medium') . '&nbsp;</span>';
     break;
     case (($uv >=6 ) and ($uv < 8)):
       $uv = '<span style="border: solid 1px; color: black; background-color: #FD9125;">&nbsp;' . langtransstr('High') . '&nbsp;</span>';
     break;
     case (($uv >=8 ) and ($uv < 11)):
       $uv = '<span style="border: solid 1px; color: #FFFFFF; background-color: #F63F37;">&nbsp;' . langtransstr('Very&nbsp;High') . '&nbsp;</span>';
     break;
     case (($uv >= 11) ):
       $uv = '<span style="border: solid 1px; color: #FFFF00; background-color: #807780;">&nbsp;' . langtransstr('Extreme') . '&nbsp;</span>';
     break;
   } // end switch
   return $uv;
} // end get_UVrange

//=========================================================================
// change the hh:mm AM/PM to h:mmam/pm format
function fixup_time ( $WDtime ) {
  global $timeOnlyFormat,$DebugMode;
  if ($WDtime == "00:00: AM") { return ''; }
  $t = explode(':',$WDtime);
  if (preg_match('/p/i',$WDtime)) { $t[0] = $t[0] + 12; }
  if ($t[0] > 23) {$t[0] = 12; }
  if (preg_match('/^12.*a/i',$WDtime)) { $t[0] = 0; }
  $t2 = join(':',$t); // put time back to gether;
  $t2 = preg_replace('/[^\d\:]/is','',$t2); // strip out the am/pm if any
  $r = date($timeOnlyFormat , strtotime($t2));
  if ($DebugMode) {
    $r = "<!-- fixup_time WDtime='$WDtime' t2='$t2' -->" . $r;
    $r = '<span style="color: green;">' . $r . '</span>'; 
  }
  return ($r);
}

//=========================================================================
// adjust WD date to desired format
//
function fixup_date ($WDdate) {
  global $timeFormat,$timeOnlyFormat,$dateOnlyFormat,$WDdateMDY,$DebugMode;
  $d = explode('/',$WDdate);      // expect ##/##/## form
  if(!isset($d[2])) {$d = explode('-',$WDdate); } // try ##-##-#### form instead
  if(!isset($d[2])) {$d = explode('.',$WDdate); } // try ##.##.#### form instead
  if ($d[2] > 70 and $d[2] <= 99) {$d[2] += 1900;} // 2 digit dates 70-99 are 1970-1999
  if ($d[2] < 99) {$d[2] += 2000; } // 2 digit dates (left) are assumed 20xx dates.
  if ($WDdateMDY) {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[0],$d[1]); //  M/D/YYYY -> YYYY-MM-DD
  } else {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[1],$d[0]); // D/M/YYYY -> YYYY-MM-DD
  }
  
  $r = date($dateOnlyFormat,strtotime($new));
  if ($DebugMode) {
    $r = "<!-- fixup_date WDdate='$WDdate', WDdateUSA='$WDdateMDY' new='$new' -->" . $r;
    $r = '<span style="color: green;">' . $r . '</span>'; 
  }
  return ($r);
}

//=========================================================================
// strip trailing units from a measurement
// i.e. '30.01 in. Hg' becomes '30.01'
function strip_units ($data) {
  preg_match('/([\d\,\.\+\-]+)/',$data,$t);
  return $t[1];
}

//=========================================================================
// decode WD %moonage% tag and return a text description for the moon phase name
// "Moon age: 10 days,10 hours,41 minutes,80%"

function moonphase ($WDmoonage) {

  preg_match_all('|(\d+)|is',$WDmoonage,$matches);
//  print "<!-- matches=\n" . print_r($matches,true) . "-->\n";
  $mdays = $matches[1][0];
  $mhours = $matches[1][1];
  $mmins = $matches[1][2];
  $mpct  = $matches[1][3];
  
  $mdaysd = $mdays + ($mhours / 24) + ($mmins / 1440);
  // Definitions from http://www.answers.com/topic/lunar-phase
  //  * Dark Moon - Not visible
  //  * New Moon - Not visible, or traditionally, the first visible crescent of the Moon
  //  * Waxing Crescent Moon - Right 1-49% visible
  //  * First Quarter Moon - Right 50% visible
  //  * Waxing gibbous Moon - Right 51-99% visible
  //  * Full Moon - Fully visible
  //  * Waning gibbous Moon - Left 51-99% visible
  //  * Third Quarter Moon - Left 50% visible
  //  * Waning Crescent Moon - Left 1-49% visible
  //  * New Moon - Not visible

  if ($mdaysd <= 29.53/2) { // increasing illumination
    $ph = "Waxing";
	$qtr = "First";
  } else { // decreasing illumination
    $ph = "Waning";
	$qtr = "Last";
  }
  
  if ($mpct < 1 ) { return("New Moon"); }
  if ($mpct <= 49) { return("$ph Crescent"); }
  if ($mpct < 51) { return("$qtr Quarter"); }
  if ($mpct < 99) { return("$ph Gibbous"); }
	return("Full Moon");
 }


//=========================================================================
// pick the NOAA style condition icon based on iconnumber 
function newIcon($numb) {
  global $condIconDir,$condIconType;
  
  $iconList = array(
	"skc.jpg",          //  0 imagesunny.visible
	"nskc.jpg",         //  1 imageclearnight.visible
	"bkn.jpg",          //  2 imagecloudy.visible
	"sct.jpg",          //  3 imagecloudy2.visible
	"nbkn.jpg",         //  4 imagecloudynight.visible
	"sct.jpg",          //  5 imagedry.visible
	"fg.jpg",           //  6 imagefog.visible
	"hazy.jpg",         //  7 imagehaze.visible
	"ra.jpg",           //  8 imageheavyrain.visible
	"few.jpg",          //  9 imagemainlyfine.visible
	"mist.jpg",         // 10 imagemist.visible
	"nfg.jpg",          // 11 imagenightfog.visible
	"nra.jpg",          // 12 imagenightheavyrain.visible
	"novc.jpg",         // 13 imagenightovercast.visible
	"nra.jpg",          // 14 imagenightrain.visible
	"nshra.jpg",        // 15 imagenightshowers.visible
	"nsn.jpg",          // 16 imagenightsnow.visible
	"ntsra.jpg",        // 17 imagenightthunder.visible
	"ovc.jpg",          // 18 imageovercast.visible
	"sct.jpg",          // 19 imagepartlycloudy.visible
	"ra.jpg",           // 20 imagerain.visible
	"ra.jpg",           // 21 imagerain2.visible
	"shra.jpg",         // 22 imageshowers2.visible
	"ip.jpg",           // 23 imagesleet.visible
	"ip.jpg",           // 24 imagesleetshowers.visible
	"sn.jpg",           // 25 imagesnow.visible
	"sn.jpg",           // 26 imagesnowmelt.visible
	"sn.jpg",           // 27 imagesnowshowers2.visible
	"skc.jpg",          // 28 imagesunny.visible
	"scttsra.jpg",      // 29 imagethundershowers.visible
	"hi_tsra.jpg",      // 30 imagethundershowers2.visible
	"tsra.jpg",         // 31 imagethunderstorms.visible
	"nsvrtsra.jpg",     // 32 imagetornado.visible
	"wind.jpg",         // 33 imagewindy.visible
	"ra1.jpg",          // 34 stopped rainning
	"windyrain.jpg"     // 35 windy/rain 
	);	
	$tempicon = $iconList[$numb];
	if($condIconType <> '.jpg') {
	  $tempicon = preg_replace('|\.jpg|',$condIconType,$tempicon);
	}
	return($tempicon);
}

// Function to process %Currentsolarcondition% string and 
// remove duplicate stuff, then fix capitalization, and translate from English if needed
//  
  function fixupCondition( $inCond ) {
    global $DebugMode;
	
    $Cond = str_replace('_',' ',$inCond);
	$Cond = strtolower($Cond);
	$Cond = preg_replace('| -|','',$Cond);
	$Cond = trim($Cond);
	$dt = '';
	
	$vals = array();
	if(strpos($Cond,'/') !==false) {
		$dt .= "<!-- vals split on slash -->\n";
		$vals = explode("/",$Cond);
	}
	if(strpos($Cond,',') !==false) {
		$dt .= "<!-- vals split on comma -->\n";
		$vals = explode(",",$Cond);
	}
	$ocnt = count($vals);
	if($ocnt < 1) { return(langtransstr(trim($inCond))); }
	foreach ($vals as $k => $v) { 
	  if($DebugMode) { $dt .= "<!-- v='$v' -->\n"; }
	  $v = ucfirst(strtolower(trim($v)));
	  $vals[$k] = langtransstr($v); 
	  if($DebugMode) { $dt .= "<!-- vals[$k]='".$vals[$k]."' -->\n"; }
	}
	
	if($vals[0] == '') {$junk = array_shift($vals);}
	if(isset($vals[2]) and $vals[0] == $vals[2]) {$junk = array_pop($vals);}
	reset($vals);
	$t = join(', ',$vals);
	
//	return($Cond . "' orig=$ocnt n=" . count($vals) ." t='$t'");
    if($DebugMode) {
      $t = "<!-- fixupCondition in='$inCond' out='$t' ocnt='$ocnt' -->" . $dt . $t;
	}
    return($t);
  
  }

				
// end of functions
//=========================================================================
?>