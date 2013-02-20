<?php
// PHP script by Ken True, webmaster@saratoga-weather.org
// quake-json.php  
// Version 1.00 - 08-Sep-2012 - initial release as quake-json.php
// Version 1.01 - 09-Sep-2012 - fixed XHTML 1.0-Strict and removed ' GMT' from local time display
// Version 1.02 - 12-Sep-2012 - added diagnostics, map control translations and optional target="_blank" for links
// Version 1.03 - 04-Jan-2013 - added fix for USGS unix timestamp with trailing zero added

  $Version = 'quake-json.php V1.03 - 04-Jan-2013';
//  error_reporting(E_ALL);  // uncomment to turn on full error reporting
//
// script available at http://saratoga-weather.org/scripts.php
//  
// you may copy/modify/use this script as you see fit,
// no warranty is expressed or implied.
//
// Customized for: all earthquakes from the new GeoJSON feeds
//   http://earthquake.usgs.gov/earthquakes/feed/geojson/1.0/week
//  which displays all earthquakes > 1.0 magnitude in the past 7 days
//
//
// output: creates XHTML 1.0-Strict HTML page (default)
// Options on URL:
//      tablesonly=Y    -- returns only the body code for inclusion
//                         in other webpages.  Omit to return full HTML.
//      magnitude=N.N   -- screens results looking for Richter magnitudes of
//                          N.N or greater.
//      distance=MMM    -- display quakes with epicenters only within 
//                         MMM km of your location
// example URL:
//  http://your.website/quake-json.php?tablesonly=Y&magnitude=2.1&distance=45
//  would return data without HTML header/footer for earthquakes of
//  magnitude 2.1 or larger within a 45 mile radius of your location.
//
// Usage:
//  you can use this webpage standalone (customize the HTML portion below)
//  or you can include it in an existing page:
/*
<?php
  $doIncludeQuake = true;

# uncomment ONE of the $setDistanceDisplay lines to use as template for distance displays  
#  $setDistanceDisplay = 'mi (km)';
  $setDistanceDisplay = 'mi';
#  $setDistanceDisplay = 'km (mi)';
#  $setDistanceDisplay = 'km';

  $setDistanceRadius  = 2500;  // same units as first unit in $setDistanceDisplay
# NOTE: quakes of magnitude 1.0+ are available for USA locations only.
#  non-USA location earthquakes of magnitude 4.0+ are the only ones available from the USGS
  $setMinMagnitude = '2.0';  // minimum Richter Magnitude to display
  $setHighMagnitude = '4.0';  // highlight this Magnitude and greater
  
  $setMapZoomDefault = 7;    // default zoom for Google Map 1=world to 13=street
# script will use your $SITE[] values for latitude, longitude, timezone and time display format

  $setDoLinkTarget = true;   // =true to have links open in new page, =false for XHTML 1.0-Strict
  include("quake-json.php");
?> 
*/
//  no parms:    include("quake-json.php"); 
//
//
// settings: --------------------------------------------------------------------  
//  change myLat, myLong to your station latitude/longitude, 
//  set $ourTZ to your time zone
//    other settings are optional
//
// minRichter= smallest quake to display (world is 4.0+, USA is 1.0+ on USGS
// cacheName is name of file used to store cached USGS webpage
// 
//  set to station latitude/longitude (decimal degrees)
  $myLat = 37.27153397;    //North=positive, South=negative decimal degrees
  $myLong = -122.02274323;   //East=positive, West=negative decimal degrees
// The above settings are for saratoga-weather.org location
//
  $ourTZ = "America/Chicago";  //NOTE: this *MUST* be set correctly to
// translate UTC times to your LOCAL time for the displays.
// Use http://www.php.net/manual/en/timezones.php to find the timezone suitable for
//  your location.
//
//  pick a format for the time to display ..uncomment one (or make your own)
//$timeFormat = 'D, Y-m-d H:i:s T';  // Fri, 2006-03-31 14:03:22 TZone
  $timeFormat = 'D, d-M-Y H:i:s T';  // Fri, 31-Mar-2006 14:03:22 TZone
  
// setting for how to display distances .. uncomment one below
// note: will be overridden by $SITE['distanceDisplay']  or $setDistanceDisplay if it exists
//
  $distanceDisplay = 'mi (km)';   // display for distances in 'N mi (K km)'
//  $distanceDisplay = 'mi';   // display for distances in 'N mi'
//  $distanceDisplay = 'km (mi)';   // display for distances in 'K km (N mi)'
//  $distanceDisplay = 'km';   // display for distances in 'K km'

  $minRichter = '2.0';   // minimum Richter scale earthquake to display
  $maxDistance = 200;    // quake must be within this number of miles/kilometers to location
                         // specified in $myLat, $myLong latitude/longitude
						 // and miles/kilometers chosen by first entry in $distanceDisplay above
  
  $highRichter = "5.0"; //change color for quakes >= this magnitude
  $mapZoomDefault = 7;  // default Google Map zoom entry for display (1=world, 13=street)


  $cacheFileDir = './';   // default cache file directory
  $cacheName = "quakesjson.txt";  // used to store the file so we don't have to
  //                          fetch it each time
  $refetchSeconds = 1800;     // refetch every nnnn seconds

  $imagesDir = './ajax-images/';
  $doLinkTarget = true; // =true to add target="_blank" to links in popups

// end of settings -------------------------------------------------------------

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
// Constants
// don't change $baseURL or $fileName or script may break ;-)
  $mapMainURL = "http://earthquake.usgs.gov/earthquakes/map/";  //USGS website main link
  $fileName = "http://earthquake.usgs.gov/earthquakes/feed/geojson/1.0/week";
// end of constants

// overrides from Settings.php if available
if(file_exists("Settings.php")) {include_once("Settings.php");}
//if(file_exists("common.php"))   {include_once("common.php");}
global $SITE,$missingTrans;
if (isset($SITE['latitude'])) 	     {$myLat = $SITE['latitude'];}
if (isset($SITE['longitude'])) 	     {$myLong = $SITE['longitude'];}
if (isset($SITE['tz']))              {$ourTZ = $SITE['tz']; }
if (isset($SITE['timeFormat']))      {$timeFormat = $SITE['timeFormat'];}
if (isset($SITE['cacheFileDir']))    {$cacheFileDir = $SITE['cacheFileDir']; }
if (isset($SITE['distanceDisplay'])) {$distanceDisplay = $SITE['distanceDisplay']; }
// end of overrides from Settings.php

# Shim function if run outside of AJAX/PHP template set
# these must be before the missing function is called in the source
if(!function_exists('langtransstr')) {
	function langtransstr($item) {
		return($item);
	}
}
if(!function_exists('langtrans')) {
	function langtrans($item) {
		print $item;
		return;
	}
}

// overrides from including page if any
if (isset($setDistanceDisplay)) { $distanceDisplay = $setDistanceDisplay; }
if (isset($setDistanceRadius))  { $maxDistance = $setDistanceRadius; }
if (isset($setMinMagnitude))    { $minRichter = $setMinMagnitude; }
if (isset($setHighMagnitude))   { $highRichter = $setHighMagnitude; }
if (isset($setMapZoomDefault))  { $mapZoomDefault = $setMapZoomDefault; }
if (isset($setDoLinkTarget))    { $doLinkTarget = $setDoLinkTarget; }

// ------ start of code -------

// Check parameters and force defaults/ranges
if ( ! isset($_REQUEST['tablesonly']) ) {
        $_REQUEST['tablesonly']="";
}
if (isset($doIncludeQuake) and $doIncludeQuake ) {
  $tablesOnly = "Y";
} else {
  $tablesOnly = $_REQUEST['tablesonly']; // any nonblank is ok
}

if ($tablesOnly) {$tablesOnly = "Y";}

if ( isset($_REQUEST['magnitude']) ) {
   $minRichter = preg_replace("/^[^\d\.]+$/",'',$_REQUEST['magnitude']);
}
if ($minRichter <= "1.0") {$minRichter = "1.0";}
if ($minRichter >= "9.0") {$minRichter = "9.0";}

if ( isset($_REQUEST['highmagnitude']) ) {
   $highRichter = preg_replace("/^[^\d\.]+$/",'',$_REQUEST['highmagnitude']);
}
if ($highRichter <= "1.0") {$highRichter = "1.0";}
if ($highRichter >= "9.0") {$highRichter = "9.0";}

if (isset($_REQUEST['distance']) ) {
    $maxDistance = preg_replace("/^[^\d]+$/",'',$_REQUEST['distance']);
}
if ($maxDistance <= "10") {$maxDistance = "10";}
if ($maxDistance >= "15000") {$maxDistance = "15000";}		

// for testing only 
if ( isset($_REQUEST['lat']) )     { $myLat = $_REQUEST['lat']; }
if ( isset($_REQUEST['lon']) )     { $myLong = $_REQUEST['lon']; }
if ( isset($_REQUEST['testloc']) ) { setTestLoc($_REQUEST['testloc']); } // allows for test override

if ( isset($_REQUEST['cache'])) {$refetchSeconds = 1; }

$Lang = 'en'; // default language
if ( isset($_REQUEST['lang']))  {$Lang = strtolower($_REQUEST['lang']); }

$Lang = QJ_ISO_Lang($Lang);  // use official abbreviation or 'en' as default

// omit HTML <HEAD>...</HEAD><BODY> if only tables wanted	
// --------------- customize HTML if you like -----------------------
if (! $tablesOnly) {
?>
<?php if($doLinkTarget) { // generate XHTML 1.0-Transitional ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php } else { // generate XHTML 1.0-Strict header ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php } // end DOCTYPE selector ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Refresh" content="300" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php langtrans('Earthquakes of magnitude'); ?> <?php print $minRichter; ?> <?php langtrans('within'); ?> <?php print $maxDistance; ?> <?php langtrans('km'); ?></title>
    <style type="text/css">
      body {
        margin: 0 auto;
        padding: 10px 20px 20px;
		width: 640px;
        font-family: Arial;
        font-size: 12pt;
		background-color: white;
		color: black;
      }

      #map-container {
        padding: 5px;
        border-width: 1px;
        border-style: solid;
        border-color: #ccc #ccc #999 #ccc;
        -webkit-box-shadow: rgba(64, 64, 64, 0.5) 0 2px 5px;
        -moz-box-shadow: rgba(64, 64, 64, 0.5) 0 2px 5px;
        box-shadow: rgba(64, 64, 64, 0.1) 0 2px 5px;
        width: 620px;
		display: none;
      }

      #map {
        width: 620px;
        height: 480px;
      }

      #actions {
        list-style: none;
        padding: 0;
      }

      #inline-actions {
        padding-top: 10px;
      }

      .item {
        margin-left: 20px;
      }
    </style>
    <script src="http://maps.google.com/maps/api/js?sensor=false&amp;language=<?php print $Lang; ?>" type="text/javascript"></script>
    <script src="quake-json.js" type="text/javascript"></script>
</head>
<body style="background-color:#FFFFFF;font-family:Arial, Helvetica, sans-serif;font-size:12px">
<?php
}

# Set timezone in PHP5/PHP4 manner
if (!function_exists('date_default_timezone_set')) {
  putenv("TZ=" . $ourTZ);
  } else {
  date_default_timezone_set("$ourTZ");
 }
 print "<!-- $Version -->\n";
 print "<!-- lat=$myLat long=$myLong dist=$maxDistance mag=$minRichter distanceDisplay ='$distanceDisplay' -->\n";

// refresh cached copy of page if needed
// fetch/cache code by Tom at carterlake.org
$cacheName = $cacheFileDir.$cacheName;
global $Debug;
$Debug = '';

if (file_exists($cacheName) and filemtime($cacheName) + $refetchSeconds > time()) {
      print "<!-- using Cached version of $cacheName -->\n";
      $rawhtml = implode('', file($cacheName));
    } else {
	  if($refetchSeconds == 1) { print "<!-- force cache reload -->\n"; }
      print "<!-- loading $cacheName from $fileName -->\n";
      $rawhtml = QJ_fetchUrlWithoutHanging($fileName);
	  print $Debug;
	  $i = strpos($rawhtml,"\r\n\r\n");
	  $headers = substr($rawhtml,0,$i-1);
	  $content = substr($rawhtml,$i+2);
      $RC = '';
	  if (preg_match("|^HTTP\/\S+ (.*)\r\n|",$rawhtml,$matches)) {
	    $RC = trim($matches[1]);
	  }
	  if(!preg_match('|200 |',$RC)) {
         print "<!-- fetch returns RC='".$RC."' for $fileName -->\n";
	  } else {
		$fp = fopen($cacheName, "w");
		if ($fp) {
		  $write = fputs($fp, $rawhtml);
		  fclose($fp);
		} else {
		  print "<!-- unable to write cache file $cacheName -->\n";
		}
	  }
      print "<!-- loading finished. -->\n";
	}


//get the earthquake data JSON entries
  if(strpos($rawhtml,"\r\n\r\n") !== false) {
    list($headers,$content) = explode("\r\n\r\n",$rawhtml); // extract the 'meat' from the response 
  } else {
	$headers = '';
	$content = '';
  }
  $utctimestamp = '';
  if(preg_match('|\nLast-Modified: (.*)\n|Ui',$headers,$match)) {
	$udate = trim($match[1]);
	$utimestamp = strtotime($udate);
	print "<!-- data last modified $udate -->\n";
  } elseif (file_exists($cacheName)) {
	$utimestamp = filemtime($cacheName);
	print "<!-- cache data saved ".gmdate($timeFormat,$utimestamp)." UTC -->\n";
  } else {
	$utimestamp = time();  // get unix time for date
	print "<!-- using now as last modified date ".gmdate($timeFormat,$utimestamp)." UTC -->\n";
  }

  $updatedUTC = langtransstr('Update time') . " = " . gmdate($timeFormat,$utimestamp);
  $updated = langtransstr('Update time') . " = " . date($timeFormat,$utimestamp);
  print "<!-- $updatedUTC UTC-->\n"; 
  print "<!-- $updated  Local-->\n";
  print "<!-- content length=".strlen($content)." -->\n";
  $quakeJSON = array();
  if(strlen($content) > 10) {
	$quakeJSON = json_decode($content,true);
  } else {
	print "<!-- no content to parse -->\n";
	print "<!-- USGS feed for earthquakes was not available at this time. See error messages above -->\n";
  }
  if(strlen($content > 10) and function_exists('json_last_error')) { // report status, php >= 5.3.0 only
	switch (json_last_error()) {
	  case JSON_ERROR_NONE:           $error = '- No errors';                                                break;
	  case JSON_ERROR_DEPTH:          $error = '- Maximum stack depth exceeded';                             break;
	  case JSON_ERROR_STATE_MISMATCH: $error = '- Underflow or the modes mismatch';                          break;
	  case JSON_ERROR_CTRL_CHAR:      $error = '- Unexpected control character found';                       break;
	  case JSON_ERROR_SYNTAX:         $error = '- Syntax error, malformed JSON';                             break;
	  case JSON_ERROR_UTF8:           $error = '- Malformed UTF-8 characters, possibly incorrectly encoded'; break;
	  default:                        $error = '- Unknown error';                                            break;
	}
  $Status .= "<!-- JSON decode $error -->\n";
 }
 $JQUAKES = array(); 
 if(isset($quakeJSON['features'])) {$JQUAKES = $quakeJSON['features']; }
 print "<!-- found ".count($JQUAKES)." earthquake records -->\n";

 /*  JSON returned format as associative array:
 Array
(
    [type] => FeatureCollection
    [features] => Array
        (
            [0] => Array
                (
                    [type] => Feature
                    [properties] => Array
                        (
                            [mag] => 2.2
                            [place] => 4km NNW of Brawley, California
                            [time] => 1346625108  (UTC timestamp)
                            [tz] => -420   (minutes offset from UTC for local time)
                            [url] => http://earthquake.usgs.gov/earthquakes/eventpage/ci15209889
                            [felt] => 1
                            [cdi] => 2
                            [mmi] => 
                            [alert] => 
                            [status] => AUTOMATIC
                            [tsunami] => 
                            [sig] => 75
                            [net] => ci
                            [code] => 15209889
                            [ids] => ,ci15209889,
                            [sources] => ,ci,
                            [types] => ,dyfi,general-link,geoserve,nearby-cities,origin,scitech-link,
                        )

                    [geometry] => Array
                        (
                            [type] => Point
                            [coordinates] => Array
                                (
                                    [0] => -115.5453 (longitude)
                                    [1] => 33.013   (latitude)
                                    [2] => 12.6  (depth in km)
                                )

                        )

                    [id] => ci15209889
                )

            [1] => Array
                (
                    [type] => Feature
                    [properties] => Array
                        (
                            [mag] => 4.8
                            [place] => 80km SSW of Adak, Alaska
                            [time] => 1346621542
                            [tz] => -720
                            [url] => http://earthquake.usgs.gov/earthquakes/eventpage/usc000cdrv
                            [felt] => 
                            [cdi] => 
                            [mmi] => 
                            [alert] => 
                            [status] => REVIEWED
                            [tsunami] => 
                            [sig] => 354
                            [net] => us
                            [code] => c000cdrv
                            [ids] => ,usc000cdrv,
                            [sources] => ,us,
                            [types] => ,eq-location-map,general-link,geoserve,historical-moment-tensor-map,historical-seismicity-map,nearby-cities,origin,p-wave-travel-times,phase-data,scitech-link,tectonic-summary,
                        )

                    [geometry] => Array
                        (
                            [type] => Point
                            [coordinates] => Array
                                (
                                    [0] => -176.947
                                    [1] => 51.1786
                                    [2] => 46.34
                                )

                        )

                    [id] => usc000cdrv
                )
*/ 
 // examine, process and format each line -- omit quakes not
 //   meeting the $minRichter and $maxDistance criteria
  $quakesFound = 0;
  $doneHeader = false;
  $comma = '';
  $dmaxDist = $distanceDisplay; // load template
  if(preg_match('|^km|',$dmaxDist)) {
    $maxDistanceMi = round($maxDistance/1.609344,0);
	$maxDistanceKm = $maxDistance;
  } else {
    $maxDistanceMi = $maxDistance;
	$maxDistanceKm = round($maxDistance*1.609344,0);
  }
  $dmaxDist = preg_replace('|mi|',"$maxDistanceMi mi",$dmaxDist);
  $dmaxDist = preg_replace('|km|',"$maxDistanceKm km",$dmaxDist);
  $JSONout = "var data = {\"markers\": [\n"; 
  $tgt = '';
  if($doLinkTarget) {$tgt = ' target="_blank"';}

  foreach ($JQUAKES as $key => $onequake) {
	 
      $magnitude = $onequake['properties']['mag'];
      $magnitude = sprintf("%1.1F",$magnitude);  // ensure one decimal point displayed
	  
      if ($magnitude >= "$minRichter")  {  // lets process it

      // load local variables
 	  $mapURL = $onequake['properties']['url'];
      // format quake date/time as local time at epicenter
	  $Qtimestamp = $onequake['properties']['time'];
	  if(strlen($Qtimestamp) > 10) {$Qtimestamp = substr($Qtimestamp,0,10); }
	  $QDateTime = gmdate($timeFormat,$Qtimestamp+$onequake['properties']['tz']*60);
	  $QDateTime = preg_replace('| GMT$|i','',$QDateTime); // Remove GMT string from local time text
	  // extract lat/long/depth
	  list($longitude,$latitude,$depth) = $onequake['geometry']['coordinates'];
	  $kmDepth = round($depth,1);
      $miDepth = round($kmDepth/1.609344,1);
	  $depth = $distanceDisplay;
	  $depth = preg_replace('|mi|',"$miDepth mi",$depth);
	  $depth = preg_replace('|km|',"$kmDepth km",$depth);
	  

	  preg_match('!^(\d+)km (\S+) of (.*)$!',$onequake['properties']['place'],$matches);
	  if(isset($matches[2])) {
		  $kmLoc = $matches[1];
		  $locDir = langtransstr($matches[2]);
		  $locText = $matches[3];
		  $miLoc = round($kmLoc/1.609344,0);
		  $location = $distanceDisplay; // load template
		  $location = preg_replace('|mi|',"$miLoc mi",$location);
		  $location = preg_replace('|km|',"$kmLoc km",$location);
		  $location .= " $locDir ".langtransstr('of')." ".$locText;
	  } else {
		  $location = $onequake['properties']['place'];
	  }
	  // provide highlighting for quakes >= $highRichter
	  if ($magnitude >= $highRichter) {
	     $magnitude = "<span style=\"color: red\">$magnitude</span>";
	     $location = "<span style=\"color: red;\">$location</span>";
	  }
	  
	  $distanceM = round(distance($myLat,$myLong,$latitude,$longitude,"M"));
	  $distanceK = round(distance($myLat,$myLong,$latitude,$longitude,"K"));
	  $distKsort = sprintf("%06d",$distanceK); // make an alpha sort key
	  
	  $dText = $distanceDisplay; // load template
	  $dText = preg_replace('|mi|',"$distanceM mi",$dText);
	  $dText = preg_replace('|km|',"$distanceK km",$dText);
	  $compareDistance = preg_match('|^km|',$distanceDisplay)?$distanceK:$distanceM;

      if ($compareDistance <= $maxDistance) { // only print 'close' ones
	  $quakesFound++;    // keep a tally of quakes for summary
	  if (! $doneHeader) {  // print the header if needed
// --------------- customize HTML if you like -----------------------
?>
    <div id="map-container">
      <div id="map"></div>
    </div>
<?php if(count($JQUAKES) > 0) { // only do the legend if there is a map to produce ?>
    <script type="text/javascript">
// <![CDATA[
     document.getElementById("map-container").style.display="block"; // got JavaScript enabled.. display map
	// only write the map legend if JavaScript is enabled
    var legend = '<p style="text-align: center"><img src="<?php print $imagesDir; ?>mma_20_yellow.png" height="20" width="12" alt="Quake" style="vertical-align:middle"/> M<?php print $minRichter;?> - &lt; M<?php print $highRichter; ?> | '+"\n"+ 
'<img src="<?php print $imagesDir; ?>mma_20_red.png" height="20" width="12" alt="Quake" style="vertical-align:middle"/> M<?php print $highRichter; ?>+ | '+
'<img src="<?php print $imagesDir; ?>m1.png" height="25" width="25" alt="Quake Cluster" style="vertical-align:middle"/> <?php langtrans("Cluster - click to expand details"); ?>'+"</p>\n";
    document.write(legend);
// ]]>
	</script>
<?php } // end of produce legend if a map is produced  ?>
    <noscript><p>
<b><?php langtrans('Enable JavaScript to view the Google Map.'); ?></b>
</p>
    </noscript>
<?php
	    print "
<p class=\"quake\" style=\"text-align: center;\"><strong>
".langtransstr('Earthquakes in the past 7 days of magnitude')." $minRichter ".langtransstr('or greater within')." $dmaxDist <br/>$updated</strong></p>
<table class=\"sortable quake\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\">
<thead>
  <tr>
	<th class=\"sorttable_nosort\">".langtransstr('Epicenter Near')."</th>
	<th style=\"cursor: n-resize;\"><script type=\"text/javascript\">document.write('&#8593;&#8595;');</script>".langtransstr('Magnitude')."</th>
	<th style=\"cursor: n-resize;\"><script type=\"text/javascript\">document.write('&#8593;&#8595;');</script>".langtransstr('Distance to Epicenter')."</th>
	<th style=\"cursor: n-resize;\"><script type=\"text/javascript\">document.write('&#8593;&#8595;');</script>".langtransstr('Local Time')."</th>
	<th class=\"sorttable_nosort\">".langtransstr('Link')."</th>
  </tr>
</thead>
<tbody>
";
	    $doneHeader = 1;
	  } // end doneHeader
// --------------- customize HTML if you like -----------------------
	    print "
<tr>
  <td><a href=\"$mapURL\" style=\"white-space:nowrap\"$tgt>$location</a></td>
  <td align=\"center\"><b>$magnitude</b></td>
  <td align=\"center\" style=\"white-space:nowrap\"><span style=\"display:none\">$distKsort</span><b>$dText</b></td>
  <td align=\"left\" style=\"white-space:nowrap\"><span style=\"display: none\">$Qtimestamp</span>$QDateTime</td>
  <td align=\"center\"><a href=\"$mapURL\"$tgt>".langtransstr('map')."</a></td>
</tr>\n";
      $JSONout .= "$comma";
	  $Jloc = strip_tags($location);
	  $Jmag = strip_tags($magnitude);
	   $JSONout .= " {\"loc\":\"$Jloc\",\"lat\":\"$latitude\",\"long\":\"$longitude\",\"mag\":\"$Jmag\",\"url\":\"$mapURL\",\"time\":\"$QDateTime\",\"dist\":\"$dText\",\"depth\":\"$depth\"}";
       $comma = ",\n";

	  } /* else {print "<!-- lat='$latitude' long='$longitude' reject distance $distanceK > $maxDistance for $location -->\n"; } */// end maxdistance
	
	 } // end minRichter
		 
		 
  } // end foreach loop

// finish up.  Write trailer info
 
	  if ($doneHeader) {
// --------------- customize HTML if you like -----------------------
	     print "</tbody>\n</table>\n";
?>
		     <script type="text/javascript">
// <![CDATA[
	// only write the map legend if JavaScript is enabled
    var footnote = '<p style="text-align: center"><small>'+
	'<?php langtrans("Note: Click on column heading marked with"); ?> &#8593;&#8595; '+
	'<?php langtrans("to sort column contents."); ?>'+
	"</small></p>\n";
    document.write(footnote);
// ]]>
	</script>
<?php
		 print "<p>$quakesFound ".
		 langtransstr("earthquakes found. Click on location or map links for more details from the <a href=\"$mapMainURL\">USGS</a>")."</p>\n";
		 
	  
	  } else {
// --------------- customize HTML if you like -----------------------
        if(strlen($content) > 10) {
  	      print "<p>".langtransstr("No earthquakes of magnitude")." $minRichter ".langtransstr("or greater within")." $dmaxDist ".langtransstr("reported in last 7 days").".</p>\n";
		} else {
		  print "<h3>".langtransstr('The USGS feed for earthquakes was not available at this time.')."</h3>\n";
		}
	  
	  }	 
	  
	$JSONout .= "\n]}\n";
	
    print "<script type=\"text/javascript\">\n// <![CDATA[\n";
	print $JSONout;
	print '// Generated Google Map code
var markers = [];
var imagesDir = \''.$imagesDir.'\';  // our marker/cluster images locations
var highMag = '.$highRichter.';      // highlight quakes >= this value
var doLinkTarget = '.$doLinkTarget.';    // generate target="_blank" option
MarkerClusterer.IMAGE_PATH = imagesDir+"m";  // set to use our images for clusters

function initialize() {  // Google map will load this function at page-load 
	
	var center = new google.maps.LatLng('.$myLat.','.$myLong.');
	var options = { // options for Google map
	  \'zoom\': '.$mapZoomDefault.',
	  \'center\': center,
	  \'scaleControl\': true,
	  \'mapTypeId\': google.maps.MapTypeId.TERRAIN
	};
	var cOptions = { // options for markerCluster
	  \'gridSize\': 22,
	  \'minimumClusterSize\': 4,
	  \'averageCenter\': true
	};

	var map = new google.maps.Map(document.getElementById("map"), options);

	// Make the info window close when clicking anywhere on the map.
	google.maps.event.addListener(map, \'click\', closeInfoWindow);

	// Create a single instance of the InfoWindow object which will be shared
	// by all Map objects to display information to the user.
	var ourInfoWindow = new google.maps.InfoWindow();

	var markerImageRed    = new google.maps.MarkerImage(imagesDir+"mma_20_red.png");
	var markerImageBlue   = new google.maps.MarkerImage(imagesDir+"mma_20_blue.png");
	var markerImageGreen  = new google.maps.MarkerImage(imagesDir+"mma_20_green.png");
	var markerImageYellow = new google.maps.MarkerImage(imagesDir+"mma_20_yellow.png");
    var markerImageShadow = new google.maps.MarkerImage(imagesDir+"mma_20_shadow.png", 
														new google.maps.Size(22, 20),
														new google.maps.Point(0,0),
														new google.maps.Point(0,20)
														);
	
	for (var i = 0; i < data.markers.length; i++) {
	  var latLng = new google.maps.LatLng(data.markers[i].lat,
		  data.markers[i].long);
      var loc = data.markers[i].loc;
	  var mag = data.markers[i].mag;
	  var url = data.markers[i].url;
	  var qtime = data.markers[i].time;
	  var dist = data.markers[i].dist;
	  var depth = data.markers[i].depth;
	  var useMarkerIcon = markerImageYellow;  // default to WX marker
	  if (mag >= highMag) { useMarkerIcon = markerImageRed;     }
	  
	  var title = "M"+mag+" - "+qtime;
	  
	  var tgt = \'\';
	  if(doLinkTarget > 0) {tgt = \' target="_blank"\'; }
	  var popupHtml = "<small><a href=\""+url+"\""+tgt+"><strong>M"+mag+"</strong></a> - "+
	  qtime+" - '.langtransstr("Depth").': "+depth+"<br/>"+
	  loc+" <br/>"+
	  "'.langtransstr("Distance to epicenter").': "+dist+
      "<br clear=\"left\"/></small>";
		
	  createMarker(map,latLng,useMarkerIcon,markerImageShadow,title,popupHtml);  
	  
	} // end of loop to create markers

	var markerCluster = new MarkerClusterer(map, markers, cOptions);

	function  createMarker (map,latLng, useMarkerIcon, markerImageShadow, title, popupHtml) {
		
	  var marker = new google.maps.Marker({
		map: map,
		position: latLng,
		clickable: true,
		draggable: false,
		icon: useMarkerIcon,
		shadow: markerImageShadow,
		title: title
	  });

	  marker.popupHtml = popupHtml;
	  google.maps.event.addListener(marker, \'click\', function() {
		openInfoWindow(marker);
	  });
	  markers.push(marker);
	}



	function clearClusters(e) {
	  e.preventDefault();
	  e.stopPropagation();
	  markerClusterer.clearMarkers();
	}
	/**
	 * Called when clicking anywhere on the map and closes the info window.
	 */
	function closeInfoWindow () {
	  ourInfoWindow.close();
	};
	
	/**
	 * Opens the shared info window, anchors it to the specified marker, and
	 * displays the marker\'s position as its content.
	 */
	function openInfoWindow (marker) {
	  ourInfoWindow.setContent(marker.popupHtml);
	  ourInfoWindow.open(map, marker);
	}
} // end of initialize function

// now load it all up and display the map

google.maps.event.addDomListener(window, \'load\', initialize);
// ]]>
';
	print "</script>\n";

// print footer of page if needed    
// --------------- customize HTML if you like -----------------------
if (! $tablesOnly ) {   
?>

</body>
</html>

<?php
}


// ----------------------------functions ----------------------------------- 
 
// get contents from one URL and return as string 
 function QJ_fetchUrlWithoutHanging($url,$useFopen=false) {
// thanks to Tom at Carterlake.org for this script fragment
  global $Debug, $needCookie,$timeStamp,$TOTALtime;
  $overall_start = time();
  if (! $useFopen) {
   // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed
   $numberOfSeconds=5;   

   // Suppress error reporting so Web site visitors are unaware if the feed fails
   error_reporting(0);

   // Extract resource path and domain from URL ready for fsockopen
   $FullUrl = $url;
   $urlParts = parse_url($url);
   
   $domain = $urlParts['host'];
   if(isset($urlParts['port'])) {
      $port   = $urlParts['port'];
   } else { 
      $port   = 80;
   }
   $resourcePath = $urlParts['path'];
   $resourcePath = preg_replace('|nocache|','?'.$timeStamp,$resourcePath);
   if(isset($urlParts['query']))    {$resourcePath .= "?" . $urlParts['query']; }
   if(isset($urlParts['fragment'])) {$resourcePath .= "#" . $urlParts['fragment']; }
   $T_start = QJ_microtime_float();
   $hostIP = gethostbyname($domain);
   $T_dns = QJ_microtime_float();
   $ms_dns  = sprintf("%01.3f",round($T_dns - $T_start,3));
   
   $Debug .= "<!-- GET $resourcePath HTTP/1.1 \n      Host: $domain  Port: $port IP=$hostIP-->\n";
//   print "GET $resourcePath HTTP/1.1 \n      Host: $domain  Port: $port IP=$hostIP\n";

   // Establish a connection
   $socketConnection = fsockopen($hostIP, $port, $errno, $errstr, $numberOfSeconds);
   $T_connect = QJ_microtime_float();
   $T_puts = 0;
   $T_gets = 0;
   $T_close = 0;
   
   if (!$socketConnection)
       {
       // You may wish to remove the following debugging line on a live Web site
       $Debug .= "<!-- Network error: $errstr ($errno) -->\n";
//       print "Network error: $errstr ($errno)\n";
       }    // end if
   else    {
       $xml = '';
	   $getString = "GET $resourcePath HTTP/1.1\r\nHost: $domain\r\nConnection: Close\r\n";
	   if (isset($needCookie[$domain])) {
	     $getString .= $needCookie[$domain] . "\r\n";
		 $Debug .=  "<!-- cookie used '" . $needCookie[$domain] . "' for GET to $domain -->\n";
	   }
	   $getString .= "Accept: text/plain,text/html\r\n";
	   $getString .= "\r\n";
//	   print "Sending:\n$getString\n\n";
       fputs($socketConnection, $getString);
       $T_puts = QJ_microtime_float();
	   
       // Loop until end of file
	   $TGETstats = array();
	   $TGETcount = 0;
       while (!feof($socketConnection))
           {
		   $T_getstart = QJ_microtime_float();
           $xml .= fgets($socketConnection, 16384);
		   $T_getend = QJ_microtime_float();
		   $TGETcount++;
		   $TGETstats[$TGETcount] = sprintf("%01.3f",round($T_getend - $T_getstart,3));
           }    // end while
       $T_gets = QJ_microtime_float();
       fclose ($socketConnection);
       $T_close = QJ_microtime_float();
       }    // end else
   $ms_connect = sprintf("%01.3f",round($T_connect - $T_dns,3));

   if($T_close > 0) {
      $ms_puts = sprintf("%01.3f",round($T_puts - $T_connect,3));
	  $ms_gets = sprintf("%01.3f",round($T_gets - $T_puts,3));
	  $ms_close = sprintf("%01.3f",round($T_close - $T_gets,3));
	  $ms_total = sprintf("%01.3f",round($T_close - $T_start,3)); 
    } else {
       $ms_puts = 'n/a';
	  $ms_gets = 'n/a';
	  $ms_close = 'n/a';
	  $ms_total = sprintf("%01.3f",round($T_connect - $T_start,3)); 
   }

   $Debug .= "<!-- HTTP stats:  dns=$ms_dns conn=$ms_connect put=$ms_puts get($TGETcount blocks)=$ms_gets close=$ms_close total=$ms_total secs -->\n";
//   print  "HTTP stats: dns=$ms_dns conn=$ms_connect put=$ms_puts get($TGETcount blocks)=$ms_gets close=$ms_close total=$ms_total secs \n";
//   foreach ($TGETstats as $block => $mstimes) {
//     print "HTTP Block $block took $mstimes\n";
//   }
   $TOTALtime+= ($T_close - $T_start);
   $overall_end = time();
   $overall_elapsed =   $overall_end - $overall_start;
   $Debug .= "<!-- fetch function elapsed= $overall_elapsed secs. -->\n"; 
//   print "fetch function elapsed= $overall_elapsed secs.\n"; 
   return($xml);
 } else {
//   print "<!-- using file function -->\n";
   $T_start = QJ_microtime_float();

   $xml = implode('',file($url));
   $T_close = QJ_microtime_float();
   $ms_total = sprintf("%01.3f",round($T_close - $T_start,3)); 
   $Debug .= "<!-- file() stats: total=$ms_total secs -->\n";
//   print " file() stats: total=$ms_total secs.\n";
   $TOTALtime+= ($T_close - $T_start);
   $overall_end = time();
   $overall_elapsed =   $overall_end - $overall_start;
   $Debug .= "<!-- fetch function elapsed= $overall_elapsed secs. -->\n"; 
//   print "fetch function elapsed= $overall_elapsed secs.\n"; 
   return($xml);
 }

   }    // end QJ_fetchUrlWithoutHanging
// ------------------------------------------------------------------

function QJ_microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
   
// ------------ distance calculation function ---------------------
   
    //**************************************
    //     
    // Name: Calculate Distance and Radius u
    //     sing Latitude and Longitude in PHP
    // Description:This function calculates 
    //     the distance between two locations by us
    //     ing latitude and longitude from ZIP code
    //     , postal code or postcode. The result is
    //     available in miles, kilometers or nautic
    //     al miles based on great circle distance 
    //     calculation. 
    // By: ZipCodeWorld
    //
    //This code is copyrighted and has
	// limited warranties.Please see http://
    //     www.Planet-Source-Code.com/vb/scripts/Sh
    //     owCode.asp?txtCodeId=1848&lngWId=8    //for details.    //**************************************
    //     
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    /*:: :*/
    /*:: This routine calculates the distance between two points (given the :*/
    /*:: latitude/longitude of those points). It is being used to calculate :*/
    /*:: the distance between two ZIP Codes or Postal Codes using our:*/
    /*:: ZIPCodeWorld(TM) and PostalCodeWorld(TM) products. :*/
    /*:: :*/
    /*:: Definitions::*/
    /*::South latitudes are negative, east longitudes are positive:*/
    /*:: :*/
    /*:: Passed to function::*/
    /*::lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees) :*/
    /*::lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees) :*/
    /*::unit = the unit you desire for results:*/
    /*::where: 'M' is statute miles:*/
    /*:: 'K' is kilometers (default):*/
    /*:: 'N' is nautical miles :*/
    /*:: United States ZIP Code/ Canadian Postal Code databases with latitude & :*/
    /*:: longitude are available at http://www.zipcodeworld.com :*/
    /*:: :*/
    /*:: For enquiries, please contact sales@zipcodeworld.com:*/
    /*:: :*/
    /*:: Official Web site: http://www.zipcodeworld.com :*/
    /*:: :*/
    /*:: Hexa Software Development Center © All Rights Reserved 2004:*/
    /*:: :*/
    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    function distance($lat1, $lon1, $lat2, $lon2, $unit) { 
    $theta = $lon1 - $lon2; 
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
    $dist = acos($dist); 
    $dist = rad2deg($dist); 
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);
    if ($unit == "K") {
    return ($miles * 1.609344); 
    } else if ($unit == "N") {
    return ($miles * 0.8684);
    } else {
    return $miles;
    }
  }
  
// ------------------------------------------------------------------
//  testing function to safely set location/distance/zone using testloc= parm 
function setTestLoc ( $LOC )
{
  global $myLat,$myLong,$ourTZ,$maxDistance;
  
  if ($LOC == 'NZ') {
    $myLat = -37.07;   
    $myLong = 174.35; 
    $ourTZ = "Pacific/Auckland";  
	$maxDistance = 1000;
// Yes, the above settings are for Brian Hamilton's Grahams Beach, NZ station
// in honor of his outstanding work as author of Weather-Display software
  } elseif ($LOC == 'JP') {
    $myLat = 35.8499;   
    $myLong = 139.97; 
    $ourTZ = "Asia/Tokyo";  
	$maxDistance = 1000;
  } elseif ($LOC == 'MX') {
     $myLat = 19.3999;   
    $myLong = -99.1999; 
    $ourTZ = "America/Mexico_City";  
	$maxDistance = 1000;
  } elseif ($LOC == 'PR') {
    $myLat = 18.467248;   
    $myLong = -66.108963; 
    $ourTZ = "America/Puerto_Rico";  
	$maxDistance = 2000;
  } elseif ($LOC == 'AK') {
     $myLat = 61.21574783;   
    $myLong = -149.86894226; 
    $ourTZ = "America/Anchorage";  
	$maxDistance = 2000;
  } elseif ($LOC == 'IR') {
     $myLat = 35.68;   
    $myLong = 51.3499; 
    $ourTZ = "Asia/Tehran";  
	$maxDistance = 1000;
  } elseif ($LOC == 'GR') {
     $myLat = 37.983056;   
    $myLong = 23.733056; 
    $ourTZ = "Europe/Athens";  
	$maxDistance = 1000;
  } elseif ($LOC == 'SU') {
     $myLat = 3.0;   
    $myLong = 100.0; 
    $ourTZ = "Asia/Jakarta";  
	$maxDistance = 1000;
  }

} 
// ------------------------------------------------------------------

function QJ_ISO_Lang ( $inLang) {
  global $SITE;
  if(isset($SITE['ISOLang'])) { 
    $ISOlang = $SITE['ISOLang']; 
  } else {
    $ISOlang =  array ( // ISO 639-1 2-character language abbreviations from country domain 
	'af' => 'af',
	'bg' => 'bg',
	'ct' => 'ca',
	'dk' => 'da',
	'nl' => 'nl',
	'en' => 'en',
	'fi' => 'fi',
	'fr' => 'fr',
	'de' => 'de',
	'el' => 'el',
	'ga' => 'ga',
	'it' => 'it',
	'he' => 'he',
	'hu' => 'hu',
	'no' => 'no',
	'pl' => 'pl',
	'pt' => 'pt',
	'ro' => 'ro',
	'es' => 'es',
	'se' => 'sv',
	'si' => 'sl',
  );
	  
  }
  if(isset($ISOlang[$inLang])) {
	  return($ISOlang[$inLang]);
  } else {
	  return('en');
  }
}
  
// --------------end of functions ---------------------------------------

?>
