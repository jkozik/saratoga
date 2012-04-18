<?php
//  Generate the [X]HTML for the Midwestern Weather Network links page
//  Based on MWWN-mesomap.php for Midwestern Weather Network 
//  Version 1.00 - 29-Apr-2008  Author: Ken True, webmaster@saratoga-weather.org
//  Version 1.01 - 10-May-2008 -- added Metric/English units handling
//  Version 1.02 - 23-Aug-2008 -- added sortable table columns and temperatures to 1 decimal place
//  Version 1.03 - 26-Aug-2008 -- added dewpt, wind gust and units conversion for stickertags stations
//  Version 2.00 - 04-Aug-2009 -- added STCU, units support + display city names option
//  Version 2.01 - 05-Aug-2009 -- added support for 'offline' sensors in stations-cc.txt
//  Version 2.02 - 11-Aug-2009 -- added $mapOnly and minor fixes for numeric hostnames
//
//  Note: distribution of this program is limited to members of the
//  Midwestern Weather Network (http://midwesternweather.net/ )
//  Distribution outside of the membership is prohibited.
//  Copyright 2006-2008, Ken True - Saratoga-weather.org
//
$Version = "V2.02 (ENG) 11-Aug-2009";
//  Inputs:
//    a '|' -delimited data file with the information on the links to generate
//  PHP page parameters:
//   mode=[HTML] | [XHTML]  default=HTML 4.0 format
// ------------- Settings ----------------------------------------
//
$LinksFile = "./MWWN-mesomap/MWWN-stations-cc.txt"; // master control file
//
$Graphic = "./MWWN-mesomap/MWWN_meso.jpg";  // used for display on this page and in code gen.
//
$ThisStation = "NapervilleWeather.net"; // change to your station name like
//$ThisStation = "Saratoga-weather.org"; // or
//$ThisStation = "Stillweather.com";

//$timeFormat = 'D, Y-m-d H:i:s T';  // Fri, 2006-03-31 14:03:22 TZone
  $timeFormat = 'D, d-M-Y H:i:s T';  // Fri, 31-Mar-2006 14:03:22 TZone
  $ourTZ = "America/Chicago";  //NOTE: this *MUST* be set correctly to
// translate UTC times to your LOCAL time for the displays.
// cacheName is name of file used to store cached NDBC webpage
// 
  $windArrowDir = './MWWN-mesomap/MWWN-images/'; // set to directory with wind arrows, or
//                        set to '' if wind arrows are not wanted
//                        the program will test to see if images are 
//                        available, and set it to '' if no images are
//                        found in the directory specified.
//
//                        // used for rotating legend display :
  $windArrowSize = 'S';   // ='S' for Small 9x9 arrows   (*-sm.gif)
//                           ='L' for Large 14x14 arrows (*.gif)
//
  $showTownName = true;      // set to false to suppress town name in rotating conditions
  $showNoData = true;   // show table rows with no conditions data available
//
  $maxAge = 62*60;     // max age in seconds for conditions
//                       (62*60 = 62 minutes)
  $maxAgeMetar = 90*60; // max age for Metar is 90 minutes V2.10
//
//
// cacheName is name of file used to store cached current conditions
// 
  $cacheName = "MWWN-conditions.txt";  // used to store the file so we 
//                                        don't have to fetch it each time
//                                        cache is normally filed from master site
//
  $refetchSeconds = 600;     // refetch every nnnn seconds (600=10 minutes)
//
// V2.10 added baro, trend, conditions
  $showBaroTrendArrow = true; // set to false to suppress trend arrows
  $showMapCondIcons = true;   // set to false to supress tiny icons on map
  $showTownName = true;       // set to false to suppress town names on map
  $mapOnly = false;           // set to true to have map only in $MWWN_MAP
  $includeConfig = true;     // set to true to allow MWWN-mesomap-config.txt
  	                          // to override these settings
  $condIconsDir = './MWWN-mesomap/MWWN-images/'; // for condition icons (same as Anole's)
//
// V2.09 - URL to fetch master cached conditions from
  $masterCacheURL = 'http://midwesternweather.net/MWWN-conditions.txt';
//  $masterCacheURL = '';
//   set to '' to disable master cache url fetch.
//
// V2.10 optional -- record raw fetch stats.. set to '' to disable
  $statsDir = ''; // directory to store raw fetch stats.
// 
// V2.14 optional - display this text on map if no station report 
  $NAtext = 'Offline';  // text to display on rotating conditions if no station report
// 
// NOTE: the UOM must be the same for all mesomaps in the regional network.
// DONT CHANGE THESE SETTINGS:
  $myUOM = 'E'; // ='M' metric units, ='E' English(USA) units
// pick ONE of the following to override wind units of kmh for Metric or mph for English units  
  $useKnots = false;  // =true - show wind in Knots only
  $useMPH   = false;  // =true - show wind in Miles-per-hour only
  $useMPS   = false;  // =true - show wind in Meters-per-second only
//
  $useMB    = false;   // for $myUOM = 'M', =true to display 'mb', =false to display 'hPa' as baro units 

//
// Special handling for websites that require a cookie to be emitted to work correctly
//$needCookie = array('home.comcast.net' => 'Cookie: pwp_mig_status=0');
$needCookie = array('kflhuds05.tripod.com' => 
					'Cookie: CookieStatus=COOKIE_OK,MEMBER_PAGE=kflhuds05/VWS_stickertags.htm,REFERRER='
					);

// special handling for websites with Last-modified header not correct
//#needTimeAdjust = array('briantet.tripod.com' => 7100);   (time adjust in seconds)
$needTimeAdjust = array('briantet.tripod.com' => 7100
						);

// -------------------------------------------------------------
// ------- end of settings ------

/// ------  begin code ----------
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

// Map specific settings .. DO NOT CHANGE unless graphic is changed to match these

$MapWidth = 557;   // map image width in px
$MapHeight = 591;  // map image height in px

$LegendX = 280;    // left: for rotating legend display in px
$LegendY = 40;     // top: for rotating legend display in px

$ControlsX = 5;  // left: for control display in px
$ControlsY = 565;  // top: for control display in px

//------------ end of map-specific settings ---------------


// include the settings override file if it exists
if ($includeConfig and file_exists("MWWN-mesomap-config.txt")) {
  require_once("MWWN-mesomap-config.txt");
}

// Check parameters and force defaults/ranges
  $IncludeMode = false;
  $PrintMode = true;
  $showNoData = false;   // exclude table rows with no conditions data available.. screws up sort if enabled
  
  if (isset($doPrintMWWN) && ! $doPrintMWWN ) {
      $PrintMode = false;
  }
  if (isset($_REQUEST['inc']) && 
      strtolower($_REQUEST['inc']) == 'noprint' ) {
	  $PrintMode = false;
  }

if (isset($_REQUEST['inc']) && strtolower($_REQUEST['inc']) == 'y') {
  $IncludeMode = true;
}
if (isset($doIncludeMWWN)) {
  $IncludeMode = $doIncludeMWWN;
}


if ( ! isset($_REQUEST['inc']) )
        $_REQUEST['inc']="";
$includeOnly = strtolower($_REQUEST['inc']); // any nonblank is ok
if ($includeOnly) {$includeOnly = "Y";}
if (isset($_REQUEST['inc']) and strtoupper($_REQUEST['inc']) == 'CSS') {$CSSonly = true;} else {$CSSonly = false;}

// show map with hotspots outlined
if (isset($_REQUEST['show']) and strtolower($_REQUEST['show']) == 'map' ) {
 $ShowMap = '&amp;show=map';
 $genJPEG = true;
} else {
 $ShowMap = '';  // no outlines for map
 $genJPEG = false;
}


if($windArrowDir && ! file_exists("$windArrowDir" . 'NNE.gif') ) {
   $windArrowDir = '';  // bad spec.. no arrows found
}

if($condIconsDir && ! file_exists("$condIconsDir" . 'day_clear.gif') ) {
   $condIconsDir = '';  // bad spec.. no icons found
}
 
if (isset($_REQUEST['cfg']) and strtolower($_REQUEST['cfg']) == 'debug') {
  $doDebug = true; 
  } else {
  $doDebug = false;
}
$doDebug = true;
$Debug = "<!-- debug mode -->\n";
if (isset($_REQUEST['cache']) and strtolower($_REQUEST['cache']) == 'no') {
  $refetchSeconds = 10;  // set short period for refresh of cache
}

$maxAge = $maxAge + $refetchSeconds + 10; // no age penalty from caching
$maxAgeMetar = $maxAgeMetar + $refetchSeconds + 10; // no cache penalty

// HTML specific overrides if desired
$otherParms = '';
if (isset($_REQUEST['gen']) and strtolower($_REQUEST['gen']) == 'xhtml' ) {
 $TARGET = '';  // no target for XHTML 1.0-Strict
 $otherParms = "&amp;gen=xhtml";
} else {
 $TARGET = "target=\"_blank\"";
 $otherParms = "&amp;gen=html";
 
}
if(!isset($PHP_SELF)) {$PHP_SELF = $_SERVER['PHP_SELF']; }

$t = pathinfo($PHP_SELF);
$Program = $t['basename'];
$ourHost = $_SERVER['HTTP_HOST'];
$mc = parse_url($masterCacheURL);
$masterHost = $mc['host'];
$onMasterHost = false;
if ($ourHost == $masterHost) {$onMasterHost = true; }
 $Debug .= "<!-- ourHost='$ourHost' mH='$masterHost' onM='$onMasterHost' -->\n";

// Establish timezone offset for time display
# Set timezone in PHP5/PHP4 manner
  if (!function_exists('date_default_timezone_set')) {
	  if (! ini_get('safe_mode') ) {
		 putenv("TZ=$ourTZ");  // set our timezone for 'as of' date on file
	  }  
#	$Status .= "<!-- using putenv(\"TZ=$ourTZ\") -->\n";
    } else {
	date_default_timezone_set("$ourTZ");
#	$Status .= "<!-- using date_default_timezone_set(\"$ourTZ\") -->\n";
   }
// ---------------------main program -----------------------------------
// open and read links file
global $Debug,$doDebug,$cacheName,$refetchSeconds,$Icons,$windArrowDir,$wxInfo, $metarPtr, $group;
global $showTownName;

$updatedOn = filemtime($LinksFile);  // for our 'as of' date
$updatedOnG = filemtime($Graphic);  // for graphic file

$Stations = array();  // storage area for the station info
$StationData = array(); // storage area for current conditions
$StationStats = array(); // storage for fetch stats for station
$wxInfo = array();
$timeStamp = time();
$currentTime = date($timeFormat,$timeStamp);
$METARcache = array();  // so we only have to fetch once a metar for this execution
$METARcacheHits = array(); // count of hits on $METARcache

load_config($LinksFile);  // load up the $Stations from the config file

 // show jpg image of map with hotspots outlined
if (isset($_REQUEST['show']) and strtolower($_REQUEST['show']) == 'map' ) {
 $ShowMap = '&amp;show=map';
 $genJPEG = 1;
} else {
 $ShowMap = '';  // no outlines for map
 $genJPEG = 0;
}

  
// show image of map with hotspots outlined
  if (isset($_REQUEST['show']) and strtolower($_REQUEST['show']) == 'hotspots' ) {
    $ourGraphic = $SCRIPT_URI . "?show=map";
    $toggleState = "Now showing Hotspots -- <a href=\"$PHP_SELF?show=normal$otherParms\">click to show normal graphic</a>.\n";
   } else {
    $ourGraphic = $Graphic;  // no outlines for map
    $toggleState = "Now showing normal graphic -- <a href=\"$PHP_SELF?show=hotspots$otherParms\">click to show hotspots outlined</a>.\n";
   }

  if ($genJPEG) { // just produce the map with hotspots outlined
    outline_hotspots($ourGraphic);
	exit;
  } 
   	
 
	load_strings();  // Load up the CSS and boilerplates
	
	$Units = load_units($myUOM);
	
	gen_CSS();       // generate the CSS first

    if ($CSSonly) {  // just print the CSS and exit
      print $CSS;
      return;
    }	
	
    load_iconDefs();  // create definitions for condition icons

  $TotalTime = load_weather_data();  // this is the time consumer :-)
	

  reset($Stations);  // and reset the list from the config file
  
  $lastState = '';   // to handle the sublists

if (!$mapOnly) {
// generate the links list sorted by State, Station name
  while (list($key, $val) = each($Stations)) { 
    list($State,$Name) = explode("\t",$key);
	list($URL,$Coords,$Features,$DataPage) = explode("\t",$Stations["$key"]);
	
	if ($lastState <> $State) {
	   if ($lastState) {  // not first state
	     $html .= "    </ol>\n";
		 $html .= "  </li>\n";
	   }
       $html .= "  <li>$State\n";
	   $html .= '    <ol style="list-style: circle">' . "\n";
	   $lastState = $State;
	}  // different state
	$t = '';
	if ($TARGET) {$t = " $TARGET";}
	$html .= "	      <li><a href=\"$URL\"$t>$Name</a> 
		         [ " . $Features . " ]</li>\n";
  }  // end while
  $html .= "    </ol>\n";
  $html .= "  </li>\n";
  $html .= "</ol>\n";
} // $mapOnly
// generate the MAP list sorted by State, Station name
  $html .= '<map name="MWWN" id="MWWN">' . "\n";
  
  prt_tablehead(); // initialize the $table area with col heads

  reset($Stations);  // and reset the list so we can start at begining
  
  $lastState = '';   // to handle the sublists
  while (list($key, $val) = each($Stations)) { 
    list($State,$Name) = explode("\t",$key);
	list($URL,$Coords,$Features,$DataPage) = explode("\t",$Stations["$key"]);

	prt_tabledata($key);
	if ($lastState <> $State) {
       $html .= "<!-- $State -->\n";
	   $lastState = $State;
	} 
	$t = '';
	if ($TARGET) {$t = "\n                $TARGET";} 
	$html .= "	  <area shape=\"rect\" coords=\"$Coords\" href=\"$URL\" $t
		title=\"" . gen_tag($key) . "\" 
		  alt=\"" . gen_tag($key) . "\" />\n";
  }  // end while

// --------------- customize HTML if you like -----------------------
	     $table .= "</tbody>\n</table>\n<p>&nbsp;</p>\n\n";
// finish up the CSS/HTML assembly strings

  $html .= "</map>
</div> <!-- end html -->
<!-- end of included MWWN text -->\n";

$oldestData = 9999999999999999;
$newestData = 0;
foreach ($StationData as $key => $vals) {
  list($TEMP,$HUMID,$WDIR,$WSPD,$RAIN,$BARO,$BTRND,$COND,$CTXT,$DEWPT,$GUST,$UDATE,$FTIME) = preg_split("/\,/",$vals);
  if($UDATE > 1000) {
    $oldestData = min($UDATE,$oldestData);
    $newestData = max($UDATE,$newestData);
  }
}
$scroller .= "</div> <!-- end scroller -->\n";
$scroller .= "<p>Conditions data shown was collected <br/>from " . date($timeFormat,$oldestData) . " to " . date($timeFormat,$newestData) . " </p>\n"; 

$table .= "<!-- end of included MWWN table text -->\n";


// text is done now in $html
if ($PrintMode and ! $includeOnly and ! $genJPEG) {  
   // print headers only if inc parm omitted
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Generate MWWN mesomap code - <?php echo $Version ?></title>
<style type="text/css">
h1, h2, h3, h4, h5, h6 {
	font-family: Arial,sans-serif;
	margin: 0px;
	padding: 0px;
}

h1{
	font-family: Verdana,Arial,sans-serif;
	font-size: 120%;
	color: #334d55;
	font-weight: bold;
}

h2{
 font-size: 114%;
 color: #006699;
}
.codebox {
	border: 1px solid #080;
	color: #000000;
	padding: 5px;
	background-color: #FFFF99;
	margin: 5%;
	width: 85%;
}
</style>
<?php 
if ($ShowMap) {
    print preg_replace("|$Graphic|",$ourGraphic,$CSS); 
	} else {
	echo $CSS; 
}
?>
</head>
<body style="background-color:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px">
<h1><a name="top" id="top"></a>Midwestern Weather Network mesomap - <?php echo $Version; ?></h1>
<?php
} // end if not includeOnly

if ($PrintMode and ! $includeOnly) {  // print headers only if inc parm omitted
$scriptName = substr($PHP_SELF,1);
print "
<p>Detailed instructions on how to implement/maintain on your website are <a href=\"http://midwesternweather.net/MWWN-code.php\">available here</a>.<br /><br />
<b>New PHP users:</b> You can <a href=\"http://midwesternweather.net/MWWN-mesomap.zip\">download a starter .zip file</a>, unpack it and upload to your website.  It includes all files required by the script and the proper subdirectory structure (for ./MWWN-images/).  Then you can use the links below to refresh the components.<br /><br />
<b>PHP users:</b> <a href=\"$Graphic\">Download <b>graphic file</b></a> updated on <b>" . date($timeFormat,$updatedOnG) . "</b><br />
&nbsp;&nbsp;&nbsp;  (Right click on link above and save to <b>$Graphic</b> on your offline copy of your website,<br />
&nbsp;&nbsp;&nbsp;&nbsp;   then upload it to your website).<br />
<b>PHP users:</b> <a href=\"$LinksFile\">Download <b>data file</b></a> updated on <b>"  . date($timeFormat,$updatedOn) . "</b><br />
&nbsp;&nbsp;&nbsp;   (Right click on link above and save to <b>$LinksFile</b> on your website)<br />
<b>PHP users:</b>  <b>If needed</b>, <a href=\"$PHP_SELF?sce=view\">download <b>PHP source</b></a> for $scriptName <b>$Version</b>.<br />
&nbsp;&nbsp;&nbsp;   (Note: only needed if your copy of the script is older than this one)<br />
&nbsp;&nbsp;&nbsp;   (Right click on link above, then save page as <b>MWWN-mesomap.php</b> on your website).<br /><br />
<b>HTML users:</b> Please use the <a href=\"http://midwesternweather.net/MWWN-code.php#HTMLprocess\"><b>IFRAME method</b></a> to include the mesomap in your HTML-only website.
</p>
<p>
<a href=\"#top\">Top of page</a> |
<a href=\"#preview\">Preview of code</a> </p>
<div class=\"codebox\">
";
print "<h1><a name=\"preview\"></a>Preview of MWWN meso-map</h1>\n<p>$toggleState</p>\n"; 
    print $htmlstart . $ourGraphic . $htmlnext . $scroller . $html;
	print prt_jscript();
	print $table;
	if ($doDebug) { print $Debug; }

print '
</div> 
<p><a name="body" id="body"></a>Generating  ';
if ($TARGET <> '') {
  print "<b>HTML 4.01</b>";
  $toggleGenState = "<a href=\"$PHP_SELF?show=" . $_REQUEST['show'] . "&amp;gen=xhtml\"><b>XHTML 1.0-Strict</b></a>";
  } else {
  print "<b>XHTML 1.0-Strict</b>";
  $toggleGenState = "<a href=\"$PHP_SELF?show=" . $_REQUEST['show'] . "&amp;gen=html\"><b>HTML 4.01</b></a>";
  }
print ' code (change to '. $toggleGenState . ')</p>
';


$scriptName = substr($PHP_SELF,1);
print "
<p><a href=\"#top\">Top of page</a> |
<a href=\"#preview\">Preview of code</a> </p>
<p>Generated by $scriptName - $Version <br />
Author: Ken True - <a href=\"mailto:webmaster@saratoga-weather.org\">webmaster@saratoga-weather.org</a></p>
</body>
</html>
";
}  else if ($PrintMode) { // if including .. just print it

    print $htmlstart . $ourGraphic . $htmlnext . $scroller . $html;
	print prt_jscript();
	print $table;
	if ($doDebug) {print $Debug; }
  
} else { // end of if not includeOnly 
global $MWWN_MAP, $MWWN_TABLE, $MWWN_CSS;

$MWWN_MAP = $htmlstart . $ourGraphic . $htmlnext . $scroller . $html . prt_jscript();
$MWWN_TABLE = $table;
$MWWN_CSS = $CSS;
if ($doDebug) {$MWWN_TABLE .= $Debug; }

}
return;

//-------------------end of main program -------------------


//----------------------------functions ----------------------------------- 

// Generate CSS for positioning rollover conditions display

function gen_CSS () {
  global $CSS, $Stations, $LegendX, $LegendY;
  
  $Top = 35;  // default location for values legend on map
  $Left = 350;
  if ($LegendX) {$Left = $LegendX;}
  if ($LegendY) {$Top = $LegendY;}
 //------------customize this CSS entry for font/color/background for legend 
	$CSS .= "#mesolegend {
      top:  ${Top}px;
      left: ${Left}px;
	  font-family: Verdana, Arial, Helvetica, sans-serif;
	  font-size: 14pt;
	  font-weight: bold;
	  color: #FFFFFF;
	  padding: 6px 6px;
	  border: 1px solid white;
}
";  
  
  while (list($key, $val) = each($Stations)) { 
    list($State,$Name,$Seqno) = explode("\t",$key);
	list($URL,$Coords,$Features,$DataPage,$RawDataType,$RawDataURL,$Offsets) = explode("\t",$Stations["$key"]);
	$URLparts = parse_url($URL);
	$host = $URLparts['host'];
	$hostalias = preg_replace('/www\.|\.\w{3}|\W/s','',$host);
	$hostalias = preg_replace('/weather/','wx',$hostalias);
	$hostalias = "C" . $hostalias . "S$Seqno";
	
// generate the CSS to place the rotating display over the map
    $Coords = preg_replace("|\s|is","",$Coords);
	list($Left,$Top,$Right,$Bottom) = explode(",",$Coords . ',,,,');
	list($OLeft,$OTop) = explode(",",$Offsets . ',,');
 
    if (! $Offsets) {  // use default positioning
      $Top = $Top - 2;
      $Left = $Right + 2;
	} else {  // use relative positioning from bottom/left
	  $Top = $Bottom + $OTop;
	  $Left = $Right + $OLeft;
	}

	$CSS .= "#$hostalias {
      top:  ${Top}px;
      left: ${Left}px;
}
";
	

  } // end while $Stations
  $CSS .= "</style>\n<!-- end MWWN mesomap CSS -->\n";

  reset($Stations);  // and reset the list
  
  return;
} // end gen_CSS
// ------------------------------------------------------------------

//  produce the table heading row 
function prt_tablehead ( ){

global $Units, $table, $scroller, $CSS, $LegendX, $LegendY,$showTownName;
// --------------- customize HTML if you like -----------------------
	    $table .= "
<p class=\"MWWNtable\"><small>Note: Click on table column heading below to sort table by that column's vaues.</small></p>
<table border=\"0\" class=\"sortable MWWNtable\" cellspacing=\"2\">
 <thead>
 <tr>
  <th style=\"text-align: left;cursor: n-resize;\"><br />State</th>
  <th style=\"text-align: left;cursor: n-resize;\"><br />Station</th>
  <th style=\"text-align: center;\" class=\"sorttable_nosort\">Curr<br />Cond</th>
  <th style=\"text-align: center;cursor: n-resize;\">Temp<br />" . $Units['temp'] . "</th>
  <th style=\"text-align: center;cursor: n-resize;\">Dew Pt.<br />" . $Units['temp'] . "</th>
  <th style=\"text-align: center;cursor: n-resize;\">Hum<br />" . $Units['humid'] . "</th>
  <th style=\"text-align: center;cursor: n-resize;\" class=\"sorttable_numeric\">Wind Avg<br/>" . $Units['wind'] . "</th>
  <th style=\"text-align: center;cursor: n-resize;\" class=\"sorttable_numeric\">Gust<br/>" . $Units['wind'] . "</th>
  <th style=\"text-align: center;cursor: n-resize;\">Rain<br />" . $Units['rain'] . "</th>
  <th style=\"text-align: center;cursor: n-resize;\">Baro<br />" . $Units['baro'] . "</th>
  <th style=\"text-align: center;\" class=\"sorttable_nosort\">Baro<br />Trend</th>
  <th style=\"text-align: center;cursor: n-resize;\">Data<br/>Updated</th>
</tr>
</thead>
<tbody>
";

    $scroller .= "<p id=\"mesolegend\">
  <span class=\"MWWNcontent0\"><strong>&nbsp;Temperature&nbsp;</strong></span>
  <span class=\"MWWNcontent1\"><strong>&nbsp;Dew Point&nbsp;</strong></span>
  <span class=\"MWWNcontent2\"><strong>&nbsp;Humidity&nbsp;</strong></span>
  <span class=\"MWWNcontent3\"><strong>&nbsp;Wind from direction @ Speed/Gust&nbsp;</strong></span>
  <span class=\"MWWNcontent4\"><strong>&nbsp;Rain today&nbsp;</strong></span>
  <span class=\"MWWNcontent5\"><strong>&nbsp;Barometer &amp; Trend&nbsp;</strong></span>
  <span class=\"MWWNcontent6\"><strong>&nbsp;Current Conditions&nbsp;</strong></span>
    <span class=\"MWWNcontent7\"><strong>&nbsp;Fire Danger based on&nbsp;<br/>&nbsp;Chandler Burn Index&nbsp;</strong></span>
";
  if($showTownName) {
	  $scroller .= "  <span class=\"MWWNcontent8\"><strong>&nbsp;City/Town&nbsp;</strong></span>";
  }
  $scroller .= "</p>\n";

   $scroller .= '<form action=""> 
<p id="MWWNcontrols">
<input type="button" value="Run" name="run" onclick="MWWN_set_run(1);" />
<input type="button" value="Pause" name="pause" onclick="MWWN_set_run(0);" />
<input type="button" value="Step" name="step" onclick="MWWN_step_content();" />
</p>
</form>
';



return;
}  // end function prt_tablehead
// ------------------------------------------------------------------

// produce one row of current conditions data
function prt_tabledata($key) {

 global $StationData,$Stations,$Units,$table,$scroller,$CSS,$StationStats;
 global $skipNoData,$windArrowDir,$showNoData,$TARGET, $windArrowSize,$Icons,$condIconsDir;
 global $IconsText, $showBaroTrendArrow, $showMapCondIcons, $NAtext, $showTownName;

  if ($skipNoData && ! isset($StationData["$key"])) { return; }
  
    list($State,$Name,$Seqno) = explode("\t",$key);
	list($URL,$Coords,$Features,$DataPage,$RawDataType,$RawDataURL) = explode("\t",$Stations["$key"]);
	$URLparts = parse_url($URL);
	$host = $URLparts['host'];
	$hostalias = preg_replace('/www\.|\.\w{3}|\W/s','',$host);
	$hostalias = preg_replace('/weather/','wx',$hostalias);
	$hostalias = "C" . $hostalias . "S$Seqno";
	
	if ($windArrowSize == 'S') {
	  $windGIF = '-sm.gif';
	  $windSIZE = 'height="9" width="9"';
	} else {
	  $windGIF = '.gif';
	  $windSIZE = 'height="14" width="14"';
	}
 
  if (! isset($StationData["$key"])) {
  
    if ($showNoData) {
      $table .= "
<tr>
  <td>$State</td>
  <td><a href=\"$URL\">$Name</a></td>
  <td colspan=\"10\" align=\"left\">No current conditions report.</td>
</tr>
 ";
     } // end showNoData
//----------------------------
    $scroller .= "<p id=\"$hostalias\">
  <span class=\"MWWNcontent0\">$NAtext</span>
  <span class=\"MWWNcontent1\">$NAtext</span>
  <span class=\"MWWNcontent2\">$NAtext</span>
  <span class=\"MWWNcontent3\">$NAtext</span>
  <span class=\"MWWNcontent4\">$NAtext</span>
  <span class=\"MWWNcontent5\">$NAtext</span>
  <span class=\"MWWNcontent6\">$NAtext</span>
  <span class=\"MWWNcontent7\">$NAtext</span>
 ";
  if($showTownName) {
	  $scroller .= "  <span class=\"MWWNcontent8\">$Name</span>";
  }
  $scroller .= "</p>\n";

//----------------------------	 
	 
   return;
   }
// got data for one of our stations.. format the table entry

 	list($TEMP,$HUMID,$WDIR,$WSPD,$RAIN,$BARO,$BTRND,$COND,$CTXT,$DEWPT,$GUST,$UDATE,$FTIME) = preg_split("/\,/",$StationData["$key"]);

// --------------- customize HTML if you like -----------------------
	$t = '';
	if ($TARGET) {$t = " $TARGET";}
	$table .= "
<tr>
  <td>$State</td>
  <td><a href=\"$URL\"$t>$Name</a></td>
  <td align=\"center\">";
  if (preg_match('|.gif$|',$COND) && $condIconsDir) {
    $table .= "<img src=\"$condIconsDir" . $COND . "\" height=\"25\" width=\"25\"
	alt=\"$CTXT\" title=\"$CTXT\" />";
  } else {
    $table .= "$COND";
  }
  $table .= "</td>
  <td align=\"center\">$TEMP</td>
  <td align=\"center\">$DEWPT</td>
  <td align=\"center\">$HUMID</td>
  <td align=\"right\" style=\"padding-right: 10px;\">"; 
  if ($WDIR == 'n/a') {
    $table .= $WDIR; 
  } else {
    $wda = $WDIR;
	$table .= $wda;
	if ($windArrowDir and $wda <> '-') {
       $table .= "&nbsp;<img src=\"$windArrowDir${wda}.gif\" height=\"14\" width=\"14\" 
	    alt=\"Wind from $wda\" title=\"Wind from $wda\" />";
	}
    $table .=  "&nbsp;" . $WSPD;
  }
  $table .= "</td>
  <td align=\"center\">$GUST</td>
  <td align=\"center\">$RAIN</td>
  <td align=\"center\"><table class=\"MWWNtable\"><tr><td>$BARO</td><td>";
  if($showBaroTrendArrow) {
    $table .= getBaroTrendArrow($BTRND);
  } 
  $table .="</td></tr></table></td>
  <td align=\"center\">$BTRND</td>
  <td align=\"center\">" . date('H:i:s',$UDATE) . "</td>
  <!-- $RawDataType load time: $FTIME sec -->
</tr>\n";

// generate the data for the changing conditions display 
// NOTE: changes here may break the rotating conditions display..
    $scroller .= "<p id=\"$hostalias\">
  <span class=\"MWWNcontent0\">$TEMP " . $Units['temp'] . "</span>
  <span class=\"MWWNcontent1\">DP $DEWPT " . $Units['temp'] . "</span>
  <span class=\"MWWNcontent2\">$HUMID " . $Units['humid'] . "</span>
  <span class=\"MWWNcontent3\">";
  $wda = '';
  if ($WDIR <> 'n/a') {
    $wda = $WDIR;
	if ($windArrowDir) {
    	$scroller .= "<img src=\"$windArrowDir${wda}${windGIF}\" $windSIZE  
	    alt=\"Wind from $wda\" title=\"Wind from $wda\" />";
	}
	$gust = '';
	if ($GUST > 0) {$gust = "G $GUST"; }
    $scroller .= $WDIR . "&nbsp; $WSPD $gust " . $Units['wind'] ;
  } else {
    $scroller .= $WDIR;
  }
  $scroller .= "</span>
  <span class=\"MWWNcontent4\">$RAIN ". $Units['rain'] . "
  </span>
  <span class=\"MWWNcontent5\">";
  if ($BARO <> '') {
    $scroller .= "$BARO ". $Units['baro'];
	if ($showBaroTrendArrow) {
	  $scroller .= getBaroTrendArrow($BTRND);
	} else {
	  list($t,$q) = explode(" ",$BTRND);
	  $scroller .= " $BTRND";
	}
  } 
  $scroller .= "</span>
  <span class=\"MWWNcontent6\">";
  if ($showMapCondIcons and preg_match('|.gif$|',$COND) && $condIconsDir) {
      $scroller .= "<img src=\"$condIconsDir" . $COND . "\" height=\"12\" width=\"12\" alt=\"$CTXT\" title=\"$CTXT\" /> ";
  }
  list($mt,$t) = explode(":",$CTXT . '::');
  if ($t <> '') { 
    $scroller .= trim($t); 
	} else {
	$scroller .= trim($mt);
  }
  $scroller .= "</span>\n";
  $scroller .= "<span class=\"MWWNcontent7\">" . getFireDanger($TEMP,$HUMID) . "</span>\n";

//
  if($showTownName) {
	  $tname = explode(',',$Name.','); // take the short name if commas are found
	  $tname = $tname[0];
	  $scroller .= "  <span class=\"MWWNcontent8\">$tname</span>";
  }
  $scroller .= "</p>\n";


return;
} // end prt_tabledata
// ------------------------------------------------------------------

// load JPG image for hotspot work
function loadJPEG ($imgname) { 
   $im = @imagecreatefromjpeg ($imgname); /* Attempt to open */ 
   if (!$im) { /* See if it failed */ 
       $im  = imagecreate (150, 30); /* Create a blank image */ 
       $bgc = imagecolorallocate ($im, 255, 255, 255); 
       $tc  = imagecolorallocate ($im, 0, 0, 0); 
       imagefilledrectangle ($im, 0, 0, 150, 30, $bgc); 
       /* Output an errmsg */ 
       imagestring ($im, 1, 5, 5, "Error loading $imgname", $tc); 
   } 
   return $im; 
} 
// ------------------------------------------------------------------

// load the configuration file and set up $Stations
function load_config($LinksFile) {
   global $Stations;
      $rawlinks = file($LinksFile); // read file into array

	  // strip comment records, build $Stations indexed array
	  $nrec = 0;
	  $Seqno = 0;
      foreach ($rawlinks as $rec) {
	    $Seqno++;
	    $rec = preg_replace("|[\n\r]*|","",$rec);
	    $len = strlen($rec);
	    if($rec and substr($rec,0,1) <> "#") {  //only take non-comments
//	 	   echo "Rec $nrec ($len): $rec\n";
		   list($State,$URL,$Name,$Coords,$Features,$DataPage,$RawDataType,$RawDataURL,$Offsets,$METAR,$LatLong) = explode("|",trim($rec) . '||||||||||||');
		   $Stations["$State\t$Name\t$Seqno"] = "$URL\t$Coords\t$Features\t$DataPage\t$RawDataType\t$RawDataURL\t$Offsets\t$METAR\t$LatLong";  
		   // prepare for sort
//		   echo "<a href=\"$URL\">$Name</a> $State, coord=\"$Coords\"\n";
		   
		} elseif (strlen($rec) > 0) {
//		   echo "comment $nrec ($len): $rec\n";
		} else {
//		   echo "blank record ignored\n";
		}
	    $nrec++;
	  }

  ksort($Stations);  // now sort the keys (state, station name)

}
// ------------------------------------------------------------------

// fetch the weather data from stations and place in $StationData
function load_weather_data() {
  global $Stations,$StationData,$StationRawData,$Debug,$maxAge,
         $cacheName,$refetchSeconds,$masterCacheURL,$onMasterHost,$StationStats,$timeFormat,$needTimeAdjust;
		 
// use cache if current and available
if ($onMasterHost or ($cacheName<>'' and file_exists($cacheName) and filemtime($cacheName) + $refetchSeconds > time())) {
      $rawcache = file($cacheName);
	  if (preg_match('|\t\d+\||',$rawcache[0])) {
		  $Debug .= "<!-- using Cached version from $cacheName -->\n";
		  if($onMasterHost) {$Debug .= "<!-- master host -->\n"; }
		  foreach ($rawcache as $rec) {
			list($key,$val) = explode('|',trim($rec).'|');
			if ($key <> '') {
			  $StationData["$key"] = $val;
			}
		  }
		  return;
	  } else {
		  $Debug .= "<!-- invalid local cache in $cacheName -->\n";
	  }      
	  
    }
	
// Fetch master cache data if available	
if ($masterCacheURL <> '' and ! $onMasterHost) {
    $Debug .= "<!-- loading from master cache $masterCacheURL -->\n";

    $rawcache = fetchUrlWithoutHangingMWWN($masterCacheURL,false);
	list($headers,$content) = explode("\r\n\r\n",$rawcache);
	$cache = explode("\n",$content);
	$html = '';
    if ($cache and preg_match('| 200 OK|i',$headers) and preg_match('|\t\d+\||',$cache[0])) {
	  foreach ($cache as $rec) {
	    $html .= trim($rec) . "\n";
	    list($key,$val) = explode('|',trim($rec) . '|');
		if ($key <> '') {
		  $StationData["$key"] = $val;
		}
	  }
      $fp = fopen($cacheName, "w");
	  if (! $fp ) { 
	    $Debug .= "<!-- WARNING: cache $cacheName not writable -->\n";
	  } else {
        $write = fputs($fp, $html);
        fclose($fp);  
        $Debug .= "<!-- wrote local cache to $cacheName. -->\n";
	  }
      return;  
    } else { // failed to get master cache so have to load the hard way
	  $Debug .= "<!-- failed to get master cache -->\n";
	  $Debug .= "<!-- headers: \n " . $headers . " -->\n";
	  $cache = '';
	}
}  

  
  $total_time = 0;
  $Debug .= "<!-- fetching data direct from stations -->\n";
  
  while (list($key, $val) = each($Stations)) { 
    list($State,$Name) = explode("\t",$key);
	list($URL,$Coords,$Features,$DataPage,$RawDataType,$RawDataURL,$Offsets,$METAR,$LatLong) = 
	  explode("\t",$Stations["$key"]);
	  
	  
	if (! $RawDataURL && $DataPage) { // fetch datapage instead
	   $RawDataType = 'MWWN';
	   $RawDataURL = "$DataPage";
	}
	if(preg_match('|^http|i',$RawDataURL)) {
	  // do nothing.. url fully formed
	 } else {
	  $URLparts = parse_url($URL);
	  $RawDataURL = 'http://' . $URLparts['host'] . '/' . $RawDataURL;
	}
    if($RawDataURL && $RawDataType ) {
	  $Debug .= "\n<!-- type='$RawDataType' RawDataURL = '$RawDataURL' -->\n";
	  $useFopen = false;
	  if ($RawDataType == 'STF' || 
		  $RawDataType == 'CRF') {
	    $useFopen = true;
	  }
	  
      $time_start = microtime_float();

      $rawhtml = fetchUrlWithoutHangingMWWN($RawDataURL,$useFopen);

	  $time_stop = microtime_float();
	  $total_time += ($time_stop - $time_start);
	  $time_fetch = sprintf("%01.3f",round($time_stop - $time_start,3));
      $RC = '';
	  if (preg_match("|^HTTP\/\S+ (.*)\r\n|",$rawhtml,$matches)) {
	    $RC = trim($matches[1]);
	  }
	  $Debug .= "<!-- $RawDataType time to fetch: $time_fetch sec ($RC) -->\n";

//  	  list($headers,$content) = explode("\r\n\r\n",$rawhtml);  // split headers from html
      $i = strpos($rawhtml,"\r\n\r\n");
	  $headers = substr($rawhtml,0,$i-1);
	  $content = substr($rawhtml,$i+2);
	  $html = explode("\n",$content);  // put HTML area as separate lines
	  $age = 0;

	  if(preg_match('|\nLast-Modified: (.*)\n|Ui',$headers,$match)) {
			$udate = trim($match[1]);
			$utimestamp = strtotime($udate);
			$age = abs(time() - $utimestamp); // age in seconds
			// do time fixup if need be for hosts with funky Last-modified: dates
			foreach ($needTimeAdjust as $th => $tadj) {
			  if(preg_match("|".$th."|Ui",$RawDataURL) and 
				$age > 1 * $tadj ) {
				$age = $age - 1 * $tadj;
				$Debug .= "<!-- fixed age for $th (by $tadj seconds) -->\n";
			  }
			}
		    $Debug .= "<!-- age=$age sec '$udate' -->\n";
	  }

	  
      $metric = implode('',$html);     // now put as one long string
	  $metric = preg_replace('|<script.*</script>|is','',$metric); // clean out any javascript
	  $metric = preg_replace('|<[^>]+>|is','',$metric); // clean out any HTML markup
	  $metric = preg_replace('|\s+,|is',',',$metric);     // clean out unwanted spaces
	  $metric = preg_replace('|\s{2,}0\s*$|is',' ',$metric);     // clean out unwanted junk at end of record
	  $metric = preg_replace('|\s+|is',' ',$metric); // prune multi-spaces to single spaces
	  $metric = preg_replace('|^\S{3}\s+|is','',$metric); // Tehachapi/tripod.net tweak for bad html
  	  $metric = trim($metric);  // stickertag metric should be ok
	  
	  $Debug .= "<!-- metric='$metric' -->\n";
	  if (!preg_match('|200 |',$RC) or (! preg_match('|^\d|',$metric) && $RawDataType <> 'MWWN') ) {
	    $Debug .= "<!-- headers returned follows \n";
		$Debug .= $headers;
		$Debug .= " -->\n";
	    $Debug .= "<!-- non-digital data -- html follows \n";
		$tstr = print_r($html,true);
		$tstr = preg_replace('|<|Uis','&lt;',$tstr);
		$tstr = preg_replace('|>|Uis','&gr;',$tstr);
		$Debug .= $tstr;
		$Debug .= "-->\n";
	  } else {
	    if ($age <= $maxAge) {
	      $StationData["$key"] = parse_data($metric,$RawDataType,$METAR,$DataPage) .
		      ",$utimestamp,$time_fetch";  // save away the raw values
	  	  $Debug .= "<!-- $key data='" . $StationData["$key"] . "' -->\n\n";
		} else {
		  $Debug .= "<!-- data age $age more than $maxAge sec .. ignored -->\n\n";
		}
	  }

	} // if $RawDataURL
	$localUdate = date($timeFormat,strtotime($udate));
//    $StationStats["$key"] = "$RawDataType\t$time_fetch\t$age\t$localUdate";
  } // end while
  $Debug .= "<!-- total load time: " . round($total_time,3) . " sec -->\n";
  
  if ($cacheName <> '') {
// create cache of values 
    $html = ''; 
    while (list($key, $val) = each($StationData)) { 
      $html .= $key . "|" . $val . "\n";
    }
    $fp = fopen($cacheName, "w");
    if (! $fp ) { 
	    $Debug .=  "<!-- WARNING: cache $cacheName not writable</b> -- cache NOT saved. -->\n";
	  } else {
        $write = fputs($fp, $html);
        fclose($fp);  
	    $Debug .= "<!-- Cache $cacheName updated successfully with conditions data. -->\n";
    }
  }
}
// ------------------------------------------------------------------

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

// ------------------------------------------------------------------
function parse_data($metric,$RawDataType,$METAR,$DataPage) {
  global $Debug,$Icons,$IconsText,$dpTemp,$dpBaro,$dpRain,$dpWind,$myUOM,$Units;
// from the returned data, select the variables we'll use

  if ($RawDataType == 'ST' || $RawDataType == 'STF') { 
// a stickertag format for international:
//
// 0: <!--stationTime-->,
// 1: <!--stationDate-->,
// 2: <!--outsideTemp-->,
// 3: <!--outsideHeatIndex-->,
// 4: <!--windChill-->,
// 5: <!--outsideHumidity-->,
// 6: <!--outsideDewPt-->,
// 7: <!--barometer-->,
// 8: <!--BarTrend-->,
// 9: <!--windSpeed-->,
//10: <!--windDirection-->,
//11: <!--dailyRain-->  // old record ended here '12:44pm,7/3/06,99,101,97,31,63,29.90,Falling,2,SE,0.00,3'
//12: ^curr_cconds1^ in VWS, null for WeatherLink
//13: <!--sunriseTime-->
//14: <!--sunsetTime-->
//15: <!--windAvg10-->,
//16: <!--windHigh10-->,
//17: <!--tempUnit-->|<!--windUnit-->|<!--barUnit-->|<!--rainUnit-->
// '3:55,28/08/08,14.8,14.9,14.8,90,13.2,1016.9,Falling Slowly,1.6,WNW,0.0,, 6:31,20:25,1.8,8.0,°C|km/hr|mb|mm'
//

    $v = explode(",",$metric);
 
    $SuomTemp = $Units['temp'];  // make (possibly bad) assumption that station is reporting using the
    $SuomWind = $Units['wind'];  // same units of measure as the mesomap, so no conversion will be done
    $SuomBaro = $Units['baro'];
    $SuomRain = $Units['rain'];
	
  if(preg_match('/\|\S+\|/',$metric)) { 
	  $Debug .= "<!-- using International style stickertag process -->\n";
    // handle international style tags
    if(isset($v[17]) and preg_match('/\|/',$v[17])) {
	  $UNITS = explode('|',trim($v[17]).'||||');
	  if($UNITS[3] <> '' and strlen($UNITS[3]) > 2) { $UNITS[3] = substr($UNITS[3],0,2); }
	  if($UNITS[0] <> '') {$SuomTemp = $UNITS[0];}
	  if($UNITS[1] <> '') {$SuomWind = $UNITS[1];}
	  if($UNITS[2] <> '') {$SuomBaro = $UNITS[2];}
	  if($UNITS[3] <> '') {$SuomRain = $UNITS[3];}
	  
	  $Debug .= "<!-- station reporting units $SuomTemp,$SuomWind,$SuomBaro,$SuomRain -->\n";
	} else {
	  $SuomTemp = $Units['temp'];  // make (possibly bad) assumption that station is reporting using the
	  $SuomWind = $Units['wind'];  // same units of measure as the mesomap, so no conversion will be done
	  $SuomBaro = $Units['baro'];
	  $SuomRain = $Units['rain'];
	  $Debug .= "<!-- station using mesomap units $SuomTemp,$SuomWind,$SuomBaro,$SuomRain by default -->\n";
	}
	$TEMP = convertTemp(trim($v[2]),$SuomTemp);
	$HUMID= round(trim($v[5]),0);
	$DEWPT = convertTemp(trim($v[6]),$SuomTemp);
	$WDIR = split(" ",trim($v[10]));
	$WDIR = $WDIR[0]; // handle spaces in wind-dir from edhweather
	if (preg_match('|\d+|',$WDIR)) {
	   $Debug .= "<!-- fixed up WDIR='$WDIR' to '";
	   $WDIR = getWindDir($WDIR);
	   $Debug .= "$WDIR' --?\n";
	 }
	 // NOTE: need units conversion call here......
	$WSPD = convertWind(trim($v[9]),$SuomWind);
	$t = trim($v[15]);
	if (is_numeric($t)) { $WSPD = convertWind($t,$SuomWind); } // use the windAvg10 summary instead for wind speed
	$GUST = '';
	$t = trim($v[16]);
	if (is_numeric($t) and $t > 0) {$GUST = convertWind($t,$SuomWind); } // add  gust to display
	$tRAIN = explode(" ",trim($v[11])); // remove trailing junk from rain value 
	$RAIN = convertRain($tRAIN[0],$SuomRain);
	$BARO = convertBaro(trim($v[7]),$SuomBaro);
	$BTRND = trim($v[8]);
    if (isset($v[14]) and trim($v[12]) <> '') { // decode regular stickertag
	  // VWS Sticker Tag with conditions
	  $COND = decode_VWS_conds(trim($v[0]),trim(strtoupper($v[12])),trim($v[13]),trim($v[14]));
	  $CTXT = trim($v[12]);
    } else {
	  $COND = '';
	  $CTXT = '';
	}
	
    if ($COND == '' && $METAR <> '') {
     list($Sky,$Weather) = explode("|",getMetar($METAR));
	 $COND = decode_VWS_conds(trim($v[0]),strtoupper($Sky),trim($v[13]),trim($v[14]));
	 if ($COND <> '') {
 	 	$CTXT = "Metar $METAR: $Sky";
	 }
    }
  } else {
	  $Debug .= "<!-- using USA style stickertag process -->\n";
// handle USA style stickertags
// 0: <!--stationTime-->,
// 1: <!--stationDate-->,
// 2: <!--outsideTemp-->,
// 3: <!--outsideHeatIndex-->,
// 4: <!--windChill-->,
// 5: <!--outsideHumidity-->,
// 6: <!--outsideDewPt-->,
// 7: <!--barometer-->,
// 8: <!--BarTrend-->,
// 9: <!--windSpeed-->,
//10: <!--windDirection-->,
//11: <!--dailyRain-->
//'12:44pm,7/3/06,99,101,97,31,63,29.90,Falling,2,SE,0.00,3'
	  $SuomTemp = $Units['temp'];  // make (possibly bad) assumption that station is reporting using the
	  $SuomWind = $Units['wind'];  // same units of measure as the mesomap, so no conversion will be done
	  $SuomBaro = $Units['baro'];
	  $SuomRain = $Units['rain'];

    $v = explode(",",$metric);
    $TEMP = convertTemp($v[2],$SuomTemp);
    $HUMID= round(trim($v[5]),0);
	$DEWPT = convertTemp($v[6],$SuomTemp);
    $WDIR = split(" ",trim($v[10]));
    $WDIR = $WDIR[0]; // handle spaces in wind-dir from edhweather
    if (preg_match('|\d+|',$WDIR)) {
       $Debug .= "<!-- fixed up WDIR='$WDIR' to '";
       $WDIR = getWindDir($WDIR);
       $Debug .= "$WDIR' --?\n";
     }
    $WSPD = convertWind($v[9],$SuomWind);
	$tRAIN = explode(" ",trim($v[11])); // remove trailing junk from rain value 
    $RAIN = convertRain($tRAIN[0],$SuomRain);
    $BARO = trim($v[7]);
    if ($BARO > 35) {
      $BARO = $BARO  / 33.86388158; // convert from hPa
    }
    $BARO = convertBaro($BARO,$SuomBaro);
    $BTRND = trim($v[8]);
    if (isset($v[12]) && isset($v[24])) { // decode eldoradoweather tags
      $COND = decode_VWS_conds(trim($v[0]),trim(strtoupper($v[12])),trim($v[13]),trim($v[14]));
      $CTXT = trim($v[12]);
    
//    } elseif (isset($v[16]) && ! isset($v[23])) { // decode Bob's special tag
//      $COND = decode_VWS_conds(trim($v[0]),trim(strtoupper($v[16])),trim($v[20]),trim($v[21]));
//     $CTXT = trim($v[16]);
//  -- bob's changed his tags to have the first 16 fields the same as the rest
    
    } elseif (isset($v[15])) { // decode regular stickertag
      // VWS Sticker Tag with conditions
      $COND = decode_VWS_conds(trim($v[0]),trim(strtoupper($v[13])),trim($v[14]),trim($v[15]));
      $CTXT = trim($v[13]);
    } else {
      $COND = '';
      $CTXT = '';
    }
    if ($COND == '' && $METAR <> '') {
     list($Sky,$Weather) = explode("|",getMetar($METAR));
	 if (isset($v[15])) {
	   $COND = decode_VWS_conds(trim($v[0]),strtoupper($Sky),trim($v[14]),trim($v[15]));
	   } else {
	   $COND = decode_VWS_conds(trim($v[0]),strtoupper($Sky),'','');
	 }
     if ($COND <> '') {
        $CTXT = "Metar $METAR: $Sky";
     }
    }   

  } // end handle USA style stickertags
 } // end ST type

 if ($RawDataType == 'STCU') { // Cumulus

  $v = explode(" ",$metric);
  $SuomTemp = $v[14];
  $SuomWind = $v[13];
  $SuomBaro = $v[15];
  $SuomRain = $v[16];
  $Debug .= "<!-- station reporting units $SuomTemp,$SuomWind,$SuomBaro,$SuomRain -->\n";

   $TEMP = convertTemp($v[2],$SuomTemp);
   $MINITEMP = convertTemp($v[28],$SuomTemp);
   $DEWPT = convertTemp($v[4],$SuomTemp);

   $HUMID= round(trim($v[3]), 0);
   $WDIR = trim($v[11]);

   $WSPD = convertWind( trim($v[5]),$SuomWind);
   $WGST = convertWind( trim($v[40]),$SuomWind);
   $MAXWIND = convertWind($v[30],$SuomWind);
   $MAXGUST = convertWind($v[32],$SuomWind);



   $RAIN = convertRain(trim($v[9]),$SuomRain);
   $YMRAIN = convertRain(trim($v[21]),$SuomRain);
   $MRAIN = convertRain(trim($v[20]),$SuomRain);
   $RRATE = convertRain(trim($v[8]),$SuomRain);


   $BARO = convertBaro(trim($v[10]),$SuomBaro);

   $BTRND = set_barotrend(trim($v[18]));

   $TIME = $v[1];
   $COND = ''; // no conditions from Cumulus realtime.txt file
   if ($METAR <> '') {
     list($Sky,$Weather) = explode("|",getMetar($METAR));
	 $COND = decode_VWS_conds('',strtoupper($Sky),'','');
	 if ($COND <> '') {
	   $CTXT = "Metar $METAR: $Sky";
	 }
   }   

   $OLDRAIN = 0;
  } // end STCU type

  if ($RawDataType == 'CR' || $RawDataType == 'CRF') { 
  // WeatherDisplay clientraw.txt format
  // clientraw is in metric always
  $v = explode(" ",$metric); // clientraw is space-delimited
	  $SuomTemp = 'C';  // clientraw.txt is always in C,kts,hPa,mm
	  $SuomWind = 'kts';  
	  $SuomBaro = 'hPa';
	  $SuomRain = 'mm';
	  $Debug .= "<!-- station using CR units $SuomTemp,$SuomWind,$SuomBaro,$SuomRain by default -->\n";

//    $DATE = "$v[74]";
//    $TIME = sprintf ("%02d:%02d", $v[29], $v[30]);
    $WDIR = getWindDir($v[3]);
    $WSPD = convertWind($v[1],$SuomWind);
    $GUST = convertWind($v[2],$SuomWind); 
    $TEMP = convertTemp($v[4],$SuomTemp);
    $HUMID = round(trim($v[5]),0);
    $BARO = convertBaro($v[6],$SuomBaro);
    $BTRND = set_barotrend($v[50]);
    $RAIN = convertRain($v[7],$SuomRain);
	if(M_NETID == 'NZWN' and isset($v[162]) and !preg_match($v[162],'/^!!C/')) { // NZ Rain value
	  $Debug .= "<!-- using NZ rain cr[162]=". $v[162] . " mm instead of cr[7]=" . $v[7] . " mm -->\n";
	  $RAIN = convertRain($v[162],$SuomRain);
	}

//    $WDCH = round((1.8*$v[44]) + 32),1);
    $DEWPT = convertTemp($v[72],$SuomTemp);
//    $HEAT = round((1.8*$v[112]) + 32),1);
//    $SPCT = "$v[34]";
//    $SOLR = "$v[127]";
//    $UV = "$v[79]";
    $COND = $Icons[$v[48]]; // condition icon
	$CTXT = $IconsText[$v[48]]; // Text description
   } // end CR (clientraw) type

//V2.01 - bad sensor removal based on SwnData entries
   if(preg_match('/notemp|nohum|norain|nobaro|nowind/i',$DataPage)) {
	   $Debug .= "<!-- Note: station sensor(s) marked offline in MWWN-stations-cc.txt by '$DataPage' -->\n";
   }
   if(preg_match('/notemp/i',$DataPage)) {
	   $Debug .= "<!-- temp sensor (showing '$TEMP'} marked offline -->\n";
	   $TEMP = '-';
   }
   if(preg_match('/nohum/i',$DataPage)) {
	   $Debug .= "<!-- humidity sensor (showing '$HUMID') marked offline -->\n";
	   $HUMID = '-';
   }
   if(preg_match('/notemp|nohum/i',$DataPage)) {
	   $Debug .= "<!-- dewpoint (showing '$DEWPT') not avalible due to offline sensor(s) -->\n";
	   $DEWPT = '-';
   }
   if(preg_match('/norain/i',$DataPage)) {
	   $Debug .= "<!-- rain sensor (showing '$RAIN') marked offline -->\n";
	   $RAIN = '-';
   }
   if(preg_match('/nobaro/i',$DataPage)) {
	   $Debug .= "<!-- pressure(baro) sensor (showing '$BARO' with trend '$BTRND') marked offline -->\n";
	   $BARO = '-';
	   $BTRND = '-';
   }
    if(preg_match('/nowind/i',$DataPage)) {
	   $Debug .= "<!-- wind sensor (showing  speed '$WSPD' dir '$WDIR' gust '$GUST') marked offline -->\n";
	   $WSPD = '-';
	   $WDIR = '-';
	   $GUST = '-';
   }
   
 return("$TEMP,$HUMID,$WDIR,$WSPD,$RAIN,$BARO,$BTRND,$COND,$CTXT,$DEWPT,$GUST");
}

// ------------------------------------------------------------
// utility functions to handle conversions from clientraw data to desired units-of-measure
function convertTemp ($rawtemp,$usedunit) {
   global $myUOM,$dpTemp;
   if ($myUOM == 'E') {
	 if (preg_match('|C|i',$usedunit))  { // convert C to F
		return( sprintf("%01.${dpTemp}f",round((1.8 * $rawtemp) + 32.0,$dpTemp)));
	   } else {  // leave as F
		return (sprintf("%01.${dpTemp}f", round($rawtemp*1.0,$dpTemp)));
	 }
   }

   if ($myUOM == 'M') {
	 if (preg_match('|F|i',$usedunit))  { // convert F to C
		return( sprintf("%01.${dpTemp}f",round(($rawtemp-32.0) / 1.8,$dpTemp)));
	 } else {  // leave as C
		return (sprintf("%01.${dpTemp}f", round($rawtemp*1.0,$dpTemp)));
	 }
   }

}

function convertWind  ( $rawwind,$usedunit) {
   global $myUOM,$useKnots,$useMPH,$useMPS,$dpWind,$Debug;
  
   $using = '';
   $WIND = '';
   
// first convert all winds to knots

   $WINDkts = 0.0;
   if       (preg_match('/kts/i',$usedunit)) {
	   $WINDkts = $rawwind * 1.0;
   } elseif (preg_match('/mph/i',$usedunit)) {
	   $WINDkts = $rawwind * 0.8689762;
   } elseif (preg_match('/mps|m\/s/i',$usedunit)) {
	   $WINDkts = $rawwind * 1.94384449;
   } elseif  (preg_match('/kmh|km\/h/i',$usedunit)) {
	   $WINDkts = $rawwind * 0.539956803;
   } else {
	   $Debug .= "<!-- convertWind .. unknown input unit '$usedunit' -->\n";
	   $WINDkts = $rawwind * 1.0;
   }
   
 // now $WINDkts is wind speed in Knots  convert to desired form and decimals
 
   if ($myUOM == 'M' and ! ($useKnots || $useMPH || $useMPS)) { // output KMH
        $WIND = sprintf($dpWind?"%02.${dpWind}f":"%d",round($WINDkts * 1.85200,$dpWind));
        $using = 'KMH';
   }
   if (($myUOM == 'E' or $useMPH) and ! ($useKnots || $useMPS)) {
        $WIND = sprintf($dpWind?"%02.${dpWind}f":"%d",round($WINDkts * 1.15077945,$dpWind));
        $using = 'MPH';
   }

   if ($useMPS) {
        $WIND = sprintf($dpWind?"%02.${dpWind}f":"%d",round($WINDkts * 0.514444444,$dpWind));
        $using = 'M/S';
   }

   if ($useKnots or $WIND == '') {
        $WIND = sprintf($dpWind?"%02.${dpWind}f":"%d",round($WINDkts * 1.0,$dpWind));
        $using = 'KTS';
   }

 
   $Debug .= "<!-- convertWind($rawwind) $usedunit [$WINDkts kts] to '$WIND' $using -->\n";
   return($WIND);
}

function convertBaro ( $rawpress,$usedunit ) {
  global $myUOM,$dpBaro;
   if ($myUOM == 'E') { // convert hPa to inHg
	 if (preg_match('/hPa|mb/i',$usedunit)) {
		return (sprintf("%02.${dpBaro}f",round($rawpress  / 33.86388158,$dpBaro)));
	 } elseif (preg_match('/mm/i',$usedunit)) {
	   return (sprintf("%02.${dpBaro}f",round($rawpress * 0.0393700787,$dpBaro))); 
	 } else {
		return (sprintf("%02.${dpBaro}f",round($rawpress * 1.0,$dpBaro))); // leave in hPa
	 }
   }

   if ($myUOM == 'M') { // convert inHg to hPa
	 if (preg_match('/hPa|mb/i',$usedunit)) {
		return (sprintf("%02.${dpBaro}f",round($rawpress * 1.0,$dpBaro))); // leave in hPa
	 } elseif (preg_match('/mm/i',$usedunit)) {
	   return (sprintf("%02.${dpBaro}f",round($rawpress * 1.333224,$dpBaro))); 
	 } else {
		return (sprintf("%02.${dpBaro}f",round($rawpress  / 33.86388158,$dpBaro)));
	 }
   }
}

function convertRain ( $rawrain,$usedunit ) {
  global $myUOM,$dpRain;
   if ($myUOM == 'E') { // convert mm to inches
	 if ($usedunit == "mm")  {
		return (sprintf("%02.${dpRain}f",round($rawrain * .0393700787,$dpRain)));
	 } else {
		return (sprintf("%02.${dpRain}f",round($rawrain * 1.0,$dpRain))); // leave in mm
	 }
   }

   if ($myUOM == 'M') { // convert inches to mm
	 if ($usedunit == "mm")  {
		return (sprintf("%02.${dpRain}f",round($rawrain * 1.0,$dpRain))); // leave in mm
	 } else {
		return (sprintf("%02.${dpRain}f",round($rawrain * .0393700787,$dpRain)));
	 } 
   }
}

// ------------------------------------------------------------

function getMetar($icao) {
  global $Debug,$wxInfo,$maxAgeMetar;
  $Sky = '';
  $Weather = '';
  
  $Debug .= "<!-- METAR='$icao' -->\n";
  $host = 'weather.noaa.gov';
  $path = '/pub/data/observations/metar/stations/';
  $MetarURL = 'http://' . $host . $path . $icao . '.TXT';
 
  $time_start = microtime_float();

  $rawhtml = fetchUrlWithoutHangingMWWN($MetarURL,false);
  list($headers,$html) = explode("\r\n\r\n",$rawhtml);
  
  $time_stop = microtime_float();
  $total_time += ($time_stop - $time_start);
  $time_fetch = sprintf("%01.3f",round($time_stop - $time_start,3));
  $Debug .= "<!-- $METAR time to fetch: $time_fetch sec -->\n";
  
  $raw_metar = ereg_replace("[\n\r ]+", ' ', trim(implode(' ', (array)$html)));
  $Debug .= "<!-- '$raw_metar' -->\n";
  $metar = trim($raw_metar);
  $metarDate = preg_replace('|/|','-',substr($metar,0,16)) . ':00 GMT';
  $age = abs(time() - strtotime($metarDate)); // age in seconds
  
  $Debug .= "<!-- age=$age sec '$metarDate' -->\n";
  if ($age >= $maxAgeMetar) {
    $Debug .= "<!-- metar too old.. ignored -->\n";
	return('' . '|' . '');
  }
  error_reporting(E_ALL);
  process_metar($metar,$icao);
  error_reporting(0);
  $Debug .= "<!-- wxInfo \n" . print_r($wxInfo,true) . " -->\n";
  $Sky = trim($wxInfo['CLOUDS']);
  $Weather = trim($wxInfo['CONDITIONS']);

  if ($Sky && $Weather) {
    $Sky = $Weather;
  }
  if ($Sky && ! $Weather) {
    $Weather = $Sky;
  }

  return($Sky . '|' . $Weather);
}  
 // end getMetar functions
// ------------------------------------------------------------

// get contents from one URL and return as string 
 function fetchUrlWithoutHangingMWWN($url,$useFopen) {
// thanks to Tom at Carterlake.org for this script fragment
  global $Debug, $needCookie,$timeStamp,$TOTALtime;
  $overall_start = time();
  if (! $useFopen) {
   // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed
   $numberOfSeconds=4;   

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
   $T_start = microtime_float();
   $hostIP = gethostbyname($domain);
   $T_dns = microtime_float();
   $ms_dns  = sprintf("%01.3f",round($T_dns - $T_start,3));
   
   $Debug .= "<!-- GET $resourcePath HTTP/1.1 \n      Host: $domain  Port: $port IP=$hostIP-->\n";
//   print "GET $resourcePath HTTP/1.1 \n      Host: $domain  Port: $port IP=$hostIP\n";

   // Establish a connection
   $socketConnection = fsockopen($hostIP, $port, $errno, $errstr, $numberOfSeconds);
   $T_connect = microtime_float();
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
	   
	   $getString .= "\r\n";
//	   print "Sending:\n$getString\n\n";
       fputs($socketConnection, $getString);
       $T_puts = microtime_float();
	   
       // Loop until end of file
	   $TGETstats = array();
	   $TGETcount = 0;
       while (!feof($socketConnection))
           {
		   $T_getstart = microtime_float();
           $xml .= fgets($socketConnection, 16384);
		   $T_getend = microtime_float();
		   $TGETcount++;
		   $TGETstats[$TGETcount] = sprintf("%01.3f",round($T_getend - $T_getstart,3));
           }    // end while
       $T_gets = microtime_float();
       fclose ($socketConnection);
       $T_close = microtime_float();
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
   $T_start = microtime_float();

   $xml = implode('',file($url));
   $T_close = microtime_float();
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

   }    // end fetchUrlWithoutHangingMWWN
// ------------------------------------------------------------------

// load the image and draw hotspots on it
function outline_hotspots($Graphic) {

    global $Stations;
	
    $image = loadJPEG($Graphic);  // fetch our map image
    $color = imagecolorallocate($image, 0, 255, 0);
	$MaxX = imagesx($image);
	$MaxY = imagesy($image);

    while (list($key, $val) = each($Stations)) { //write each hotspot
      list($State,$Name) = explode("\t",$key);
	  list($URL,$Coords,$Features) = explode("\t",$Stations["$key"]);
      list($X1,$Y1,$X2,$Y2) = explode(",",$Coords);
  	  // make sure images in top-left, bottom-right order
	  if($X1 > $X2) { $tmp = $X2; $X2 = $X1; $X1 = $tmp; }
      if($Y1 > $Y2) { $tmp = $Y2; $Y2 = $Y1; $Y1 = $tmp; }
	  imagerectangle($image, $X1, $Y1, $X2, $Y2, $color);
  
    }
	$msg = "Green rectangles outline the hotspots on the image map.  DON'T SAVE THIS IMAGE.";
    imagestring($image, 3, 45, 2, $msg, $color); 

	header("Content-type: image/jpeg"); // now send to browser
    imagejpeg($image); 
    imagedestroy($image); 

}
// ------------------------------------------------------------------

function getFireDanger( $ctemp, $rh) {
global $myUOM,$Units;
// from SLOWeather.com = calculate fire danger based on temperature and relative humidity

if (preg_match('|C|i',$Units['temp'])) {
  // Convert F temp to C temp
  $ctemp = ($ftemp - 32) * 0.5556;
}

// Start Index Calcs

// Chandler Index
$cbi = (((110 - 1.373 * $rh) - 0.54 * (10.20 - $ctemp)) * (124 * pow(10,(-0.0142*"$rh"))))/60;
// CBI = (((110 - 1.373*RH) - 0.54 * (10.20 - T)) * (124 * 10**(-0.0142*RH)))/60

//Sort out the Chandler Index

$cbitextcolor = "white";

if ($cbi > "97.5") {
    $cbitxt = "Extreme";
	$cbicolor = "red";
    $cbiimg= "fdl_extreme.gif";

} elseif ($cbi >="90") {
    $cbitxt = "Very High";
	$cbicolor = "orange";
	$cbitextcolor = "black";
    $cbiimg= "fdl_vhigh.gif";

} elseif ($cbi >= "75") {
    $cbitxt = "High";
	$cbicolor = "yellow";
	$cbitextcolor = "black";
    $cbiimg= "fdl_high.gif";

} elseif ($cbi >= "50") {
    $cbitxt = "Moderate";
	$cbicolor = "blue";
    $cbiimg= "fdl_moderate.gif";

} else  {
    $cbitxt="Low";
	$cbicolor = "green";
    $cbiimg= "fdl_low.gif";

}


$val = '<span style="background-color:' . $cbicolor . 
     '; color:' . $cbitextcolor . ';">&nbsp;' .
     $cbitxt . '&nbsp;</span>';
return("$val"); // (CBI " . round($cbi,0) . ")");

}

// ------------------------------------------------------------------

//  convert degrees into wind direction abbreviation   
function getWindDir ($degrees) {
   // figure out a text value for compass direction
// Given the wind direction, return the text label
// for that value.  16 point compass
   $winddir = $degrees;
   if ($winddir == "n/a") { return($winddir); }

  if (!isset($winddir)) {
    return "---";
  }
  if (!is_numeric($winddir)) {
	return($winddir);
  }
  $windlabel = array ("N","NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S",
	 "SSW","SW", "WSW", "W", "WNW", "NW", "NNW");
  $dir = $windlabel[ fmod((($winddir + 11) / 22.5),16) ];
  return($dir);

} // end function getWindDir
// ------------------------------------------------------------------


function set_barotrend($baromtrend) {
   // routine from Anole's wxsticker PHP (adapted)
   
//   Barometric Trend(3 hour)

// Change Rates
// Rapidly: =.06" H; 1.5 mm Hg; 2 hPa; 2 mb
// Slowly: =.02" H; 0.5 mm Hg; 0.7 hPa; 0.7 mb

// 5 Arrow Positions:
// Rising Rapidly
// Rising Slowly
// Steady
// Falling Slowly
// Falling Rapidly

// Page 52 of the PDF Manual
// http://www.davisnet.com/product_documents/weather/manuals/07395.234-VP2_Manual.pdf


   // figure out a text value for barometric pressure trend
   settype($baromtrend, "float");
   switch (TRUE) {
      case (($baromtrend >= -.6) and ($baromtrend <= .6)):
        $baromtrendwords = "Steady";
      break;
      case (($baromtrend > .6) and ($baromtrend < 2)):
        $baromtrendwords = "Rising Slowly";
      break;
      case ($baromtrend >= 2):
        $baromtrendwords = "Rising Rapidly";
      break;
      case (($baromtrend < -.6) and ($baromtrend > -2)):
        $baromtrendwords = "Falling Slowly";
      break;
      case ($baromtrend <= -2):
        $baromtrendwords = "Falling Rapidly";
      break;
   } // end switch
  return($baromtrendwords);
}


// generate the alt=/title= text for area statement tooltip popups

function gen_tag($ID) {
   global $StationData,$Stations,$Units;
   list($State,$Name) = preg_split("/\t/","$ID");
   list($URL,$Coords,$Features,$DataPage,$RawDataType,$RawDataURL) = preg_split("/\t/",$Stations["$ID"]);

   if (! isset($StationData["$ID"]) ) {

   return "$Name - no current conditions report";
   }

	list($TEMP,$HUMID,$WDIR,$WSPD,$RAIN,$BARO,$BTRND,$COND,$CTXT,$DEWPT,$GUST,$UDATE) = preg_split("/\,/",$StationData["$ID"]);

// --------------- customize HTML if you like -----------------------
// note: only IE supports using new-line and tabs in tooltip displays
//   Firefox, Netscape and Opera all ignore these formatting characters,
//   or display a funky character instead.

$gust = '';
if ($GUST > 0) { $gust = "G $GUST "; }
$tag = "$Name: " .
  "$TEMP". $Units['temp'] . ", " .
  "DP $DEWPT".$Units['temp'] . ", " .
  "$HUMID" . $Units['humid'] . ", " .
  "$WDIR $WSPD $gust" . $Units['wind'] . ", " .
  "Rain: $RAIN". $Units['rain']  . ", " .
  "Baro: $BARO". $Units['baro'] . " " .
  $BTRND . ", $CTXT";

return $tag;
} // end gen_tag
// ------------------------------------------------------------------

function load_units($UOM) {

global $dpRain,$dpWind,$dpBaro,$dpTemp,$useKnots,$useMPH,$useMPS,$useMB;

$Units = array();

if ($UOM == 'M') {
    $Units =  array(  // metric with native wind units
    'wind' => 'kph',
	'temp' => '&deg;C',
	'baro' => 'hPa',
	'humid' => '%',
	'rain' => 'mm',
	'dist' => 'km');
	$dpRain = 1;
	$dpBaro = 1;
	$dpTemp = 1;
	$dpWind = 0;
  } else {
    $Units =  array(  // english with native wind units
    'wind' => 'mph',
	'temp' => '&deg;F',
	'baro' => 'in',
	'humid' => '%',
	'rain' => 'in',
	'dist' => 'nm');
	$dpRain = 2;
	$dpBaro = 2;
	$dpTemp = 0;
	$dpWind = 0;

}

 $Units['time'] = '';
 if ($useKnots) { $Units['wind'] = 'kts'; }
 if ($useMPH)   { $Units['wind'] = 'mph'; }
 if ($useMPS)   { $Units['wind'] = 'm/s'; $dpWind = 1; }
 if ($useMB and $UOM == 'M')    { $Units['baro'] = 'mb';}
 
 return $Units;
} // end load_units
// ------------------------------------------------------------------

// generate the rotation JavaScript to browser page
function prt_jscript () {
	global $showTownName;
// NOTE: the following is not PHP, it's JavaScript
//   no changes should be required here.
$t = '<script type="text/javascript">
// <![CDATA[
// ----------------------------------------------------------------------
// Rotate content display -- Ken True -- saratoga-weather.org
//
// --------- begom settomgs ---------------------------------------------------------------
var MWWNrotatedelay=3000; // Rotate display every 3 secs (= 3000 ms)
// --------- emd settomgs -----------------------------------------------------------------
//
// you shouldn\'t need to change things below this line
//
var ie4=document.all;
var ie8 = false;
if (ie4 && /MSIE (\d+\.\d+);/.test(navigator.userAgent)){ //test for MSIE x.x;
 var ieversion=new Number(RegExp.$1) // capture x.x portion and store as a number
 if (ieversion>=8) {
   ie4=false;
   ie8=true;
 }
}
var MWWNcurindex = 0;
var MWWNtotalcontent = 0;
var MWWNrunrotation = 1;
var browser = navigator.appName;

function get_content_tags ( tag ) {
// search all the span tags and return the list with class=tag 
//
  if (ie4 && browser != "Opera" && ! ie8) {
    var elem = document.getElementsByTagName("span");
	var lookfor = "className";
  } else {
    var elem = document.getElementsByTagName("span");
	var lookfor = "class";
  }
     var arr = new Array();
     for(var i = 0, iarr = 0; i < elem.length; i++) {
          var att = elem[i].getAttribute(lookfor);
          if(att == tag) {
               arr[iarr] = elem[i];
               iarr++;
          }
     }

     return arr;
}


function MWWN_get_total() {
';
if ($showTownName) {
	$t .= '  MWWNtotalcontent = 9; // content0 .. content8 ';
} else {
	$t .= '  MWWNtotalcontent = 8; // content0 .. content7 ';
}
 $t .= '
}

function MWWN_contract_all() {
  for (var y=0;y<MWWNtotalcontent;y++) {
      var elements = get_content_tags("MWWNcontent"+y);
	  var numelements = elements.length;
//	  alert("MWWN_contract_all: content"+y+" numelements="+numelements);
	  for (var index=0;index!=numelements;index++) {
         var element = elements[index];
		 element.style.display="none";
      }
  }
}

function MWWN_expand_one(which) {
  MWWN_contract_all();
  var elements = get_content_tags("MWWNcontent"+which);
  var numelements = elements.length;
  for (var index=0;index!=numelements;index++) {
     var element = elements[index];
	 element.style.display="inline";
  }
}
function MWWN_step_content() {
  MWWN_get_total();
  MWWN_contract_all();
  MWWNcurindex=(MWWNcurindex<MWWNtotalcontent-1)? MWWNcurindex+1: 0;
  MWWN_expand_one(MWWNcurindex);
}
function MWWN_set_run(val) {
  MWWNrunrotation = val;
  MWWN_rotate_content();
}
function MWWN_rotate_content() {
  if (MWWNrunrotation) {
    MWWN_get_total();
    MWWN_contract_all();
    MWWN_expand_one(MWWNcurindex);
    MWWNcurindex=(MWWNcurindex<MWWNtotalcontent-1)? MWWNcurindex+1: 0;
    setTimeout("MWWN_rotate_content()",MWWNrotatedelay);
  }
}

MWWN_rotate_content();

// ]]>
</script>
';
$t .= '<script type="text/javascript" src="sorttable.js"></script>
';
return($t);
          
}  // end prt_jscript
// ------------------------------------------------------------------

// initalize the assembly string for CSS
function load_strings () {

  global $CSS,$Graphic,$Version,$Program,$ControlsX, $ControlsY;
  
// top of CSS for mesomap display
  $CSS ='<!-- begin MWWN-mesomap CSS -->
<!-- generated by ' . $Program . ' ' . $Version . ' -->
<style type="text/css">
#MWWNmeso {
      background: url(' . $Graphic . ') no-repeat;
      font-family: Tahoma, Arial, Helvetica, sans-serif;
      color: #000088;
      position: relative;
}
#MWWNmeso p {
      position: absolute;
	  font-family: Tahoma, Arial, Helvetica, sans-serif; 
	  color: #FFFFFF;
	  font-size: 8pt;
	  font-weight: normal;
	  margin: 0 0 0 0;
	  padding: 0 0 0 0;
}
#MWWNmeso p img {
      border-style: none;
}
#MWWNmeso img {
      border-style: none;
}
#MWWNlist {
      color: black; 
	  font-family: Verdana, Arial, Helvetica, sans-serif; 
	  font-size: 10pt;
}
#MWWNcontrols {
	  top: ' . $ControlsY . 'px;
	  left: ' . $ControlsX . 'px;
	  font-family: Verdana, Arial, Helvetica, sans-serif; 
	  font-size: 8pt;
	  font-weight: normal;
	  position: relative;
	  display: inline;
	  padding: 0 0;
	  margin: 0 0;
	  border: none;
	  z-index: 20;
}
.MWWNtable {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10pt;
	color: #000000;

}
.MWWNcontent0 {
	display: inline;
}
.MWWNcontent1 {
	display: none;
}
.MWWNcontent2 {
	display: none;
}
.MWWNcontent3 {
	display: none;
}
.MWWNcontent4 {
	display: none;
}
.MWWNcontent5 {
	display: none;
}
.MWWNcontent6 {
	display: none;
}
.MWWNcontent7 {
	display: none;
}
.MWWNcontent8 {
	display: none;
}
'; 

global $htmlstart,$timeFormat,$updatedOn,$htmlnext,$html,$ThisStation,$MapWidth,$MapHeight,$mapOnly;

// top boilerplate for include text
  $htmlstart = '<!-- begin included MWWN text -->
<!-- as of ' . date($timeFormat,$updatedOn) . ' -->
<div id="MWWNmeso"><img src="';  // leave room for toggling the image here

  $htmlnext ='" usemap="#MWWN" width="'.$MapWidth.'" height="'.$MapHeight.'" 
    alt="Mesomap of Midwestern Weather Network Stations" />
';
if (!$mapOnly) {
  $html = '<p>The <a href="http://midwesternweather.net/">Midwestern Weather Network</a>
is an affiliation of personal weather websites located 
in the Midwestern USA (MN, WI, MI, IA, IL, IN, OH, MO, KY).  ' . $ThisStation .' is proud 
to be a member of this network.  
Please take a moment to visit other stations on the network by clicking on the map above 
or clicking on the links below.  Stations are displaying temperature, humidity, dew point,  
wind speed, today\'s rain, barometer/trend, current conditions and estimated Fire Danger (based on Chandler Burning Index) where available.</p>
';
}
$html .= '
<noscript><p><br /><br />
<b>Note: Enable JavaScript to see dewpoint, humidity, wind, rain, current conditions and barometer data along 
with current temperature.</b></p></noscript> 
';
if (!$mapOnly) {
	$html .= '
<h2>Current member stations of the Midwestern Weather Network</h2>
<p>as of ' . date($timeFormat,$updatedOn) . '
</p>
<div id="MWWNlist"> 
<ol style="list-style: square">
';
} else {
  $html .= '
<div id="MWWNlist"> 
';
}

} // end load_strings
// ------------------------------------------------------------------

// following code added in 2.10 for conditions and metar decoding


function load_iconDefs () {

global $Icons, $vws_icon, $IconsText;

   // CURRENT CONDITIONS ICONS FOR clientraw.txt
   // create array for icons. There are 35 possible values in clientraw.txt
   // It would be simpler to do this with array() but to make it easier to 
   // modify each element is defined individually. Each index [#] corresponds
   // to the value provided in clientraw.txt
   $Icons[0] = "day_clear.gif";            // imagesunny.visible
   $Icons[1] = "night_clear.gif";          // imageclearnight.visible
   $Icons[2] = "day_partly_cloudy.gif";    // imagecloudy.visible
   $Icons[3] = "day_partly_cloudy.gif";    // imagecloudy2.visible
   $Icons[4] = "night_partly_cloudy.gif";  // imagecloudynight.visible
   $Icons[5] = "day_clear.gif";            // imagedry.visible
   $Icons[6] = "fog.gif";                  // imagefog.visible
   $Icons[7] = "haze.gif";                 // imagehaze.visible
   $Icons[8] = "day_heavy_rain.gif";       // imageheavyrain.visible
   $Icons[9] = "day_mostly_sunny.gif";     // imagemainlyfine.visible
   $Icons[10] = "mist.gif";                // imagemist.visible
   $Icons[11] = "fog.gif";                 // imagenightfog.visible
   $Icons[12] = "night_heavy_rain.gif";    // imagenightheavyrain.visible
   $Icons[13] = "night_cloudy.gif";        // imagenightovercast.visible
   $Icons[14] = "night_rain.gif";          // imagenightrain.visible
   $Icons[15] = "night_light_rain.gif";    // imagenightshowers.visible
   $Icons[16] = "night_snow.gif";          // imagenightsnow.visible
   $Icons[17] = "night_tstorm.gif";        // imagenightthunder.visible
   $Icons[18] = "day_cloudy.gif";          // imageovercast.visible
   $Icons[19] = "day_partly_cloudy.gif";   // imagepartlycloudy.visible
   $Icons[20] = "day_rain.gif";            // imagerain.visible
   $Icons[21] = "day_rain.gif";            // imagerain2.visible
   $Icons[22] = "day_light_rain.gif";      // imageshowers2.visible
   $Icons[23] = "sleet.gif";               // imagesleet.visible
   $Icons[24] = "sleet.gif";               // imagesleetshowers.visible
   $Icons[25] = "day_snow.gif";            // imagesnow.visible
   $Icons[26] = "day_snow.gif";            // imagesnowmelt.visible
   $Icons[27] = "day_snow.gif";            // imagesnowshowers2.visible
   $Icons[28] = "day_clear.gif";           // imagesunny.visible
   $Icons[29] = "day_tstorm.gif";          // imagethundershowers.visible
   $Icons[30] = "day_tstorm.gif";          // imagethundershowers2.visible
   $Icons[31] = "day_tstorm.gif";          // imagethunderstorms.visible
   $Icons[32] = "tornado.gif";             // imagetornado.visible
   $Icons[33] = "windy.gif";               // imagewindy.visible
   $Icons[34] = "day_partly_cloudy.gif";   // stopped rainning
   $Icons[35] = "windyrain.gif";   // wind + rain

   $IconsText[0] =  'Sunny';
   $IconsText[1] =  'Clear';
   $IconsText[2] =  'Cloudy';
   $IconsText[3] =  'Cloudy2';
   $IconsText[4] =  'Partly Cloudy';
   $IconsText[5] =  'Dry';
   $IconsText[6] =  'Fog';
   $IconsText[7] =  'Haze';
   $IconsText[8] =  'Heavy Rain';
   $IconsText[9] =  'Mainly Fine';
   $IconsText[10] = 'Mist';
   $IconsText[11] = 'Fog';
   $IconsText[12] = 'Heavy Rain';
   $IconsText[13] = 'Overcast';
   $IconsText[14] = 'Rain';
   $IconsText[15] = 'Showers';
   $IconsText[16] = 'Snow';
   $IconsText[17] = 'Thunder';
   $IconsText[18] = 'Overcast';
   $IconsText[19] = 'Partly Cloudy';
   $IconsText[20] = 'Rain';
   $IconsText[21] = 'Rain2';
   $IconsText[22] = 'Showers2';
   $IconsText[23] = 'Sleet';
   $IconsText[24] = 'Sleet Showers';
   $IconsText[25] = 'Snow';
   $IconsText[26] = 'Snow Melt';
   $IconsText[27] = 'Snow Showers2';
   $IconsText[28] = 'Sunny';
   $IconsText[29] = 'Thunder Showers';
   $IconsText[30] = 'Thunder Showers2';
   $IconsText[31] = 'Thunder Storms';
   $IconsText[32] = 'Tornado';
   $IconsText[33] = 'Windy';
   $IconsText[34] = 'Stopped Raining';
   $IconsText[35] = 'Wind/Rain';

     // CURRENT CONDITIONS ICONS FOR VWS
     // create array for icons.
     // It would be simpler to do this with array() but to make it easier to 
     // modify each element is defined individually. Each index ["SOME VALUE"] 
     // corresponds to a possible value of the ^climate_cconds1^ tag. Because
     // VWS does not provide a day/night tag, we figure that out immediately 
     // above and then use the $daynight value in the image definitions below
     // to select the right icon.
     $vws_icon['BLIZZARD CONDITION'] = '%s_snow';
     $vws_icon['BLIZZARD'] = '%s_snow';
     $vws_icon['INCREASING CLOUDS'] = '%s_cloudy';
     $vws_icon['BECOMING CLOUDY'] = '%s_cloudy';
     $vws_icon['HAZY'] = 'haze';
     $vws_icon['HAZE'] = 'haze';
     $vws_icon['HZ'] = 'haze';
     $vws_icon['SUN AND CLOUD'] = '%s_partly_cloudy';
     $vws_icon['FEW CLOUD'] = '%s_partly_cloudy';
     $vws_icon['FEW CLOUDS'] = '%s_partly_cloudy';
     $vws_icon['PARTIAL CLEARING'] = '%s_partly_cloudy';
     $vws_icon['CLEARING'] = '%s_partly_cloudy';
     $vws_icon['VARIABLE CLOUDINESS'] = '%s_partly_cloudy';
     $vws_icon['VARIABLE CLOUDS'] = '%s_partly_cloudy';
     $vws_icon['SCATTERED CLOUDS'] = '%s_partly_cloudy';
     $vws_icon['BLOWING SNOW'] = '%s_snow';
     $vws_icon['DRIFTING SNOW'] = '%s_snow';
     $vws_icon['RAIN+SNOW'] = '%s_snow';
     $vws_icon['SNOW+FREEZING RAIN'] = '%s_snow';
     $vws_icon['SNOW+RAIN'] = '%s_snow';
     $vws_icon['RAIN+SNOW+SHOWERS'] = '%s_snow';
     $vws_icon['FREEZING RAIN AND SNOW'] = '%s_snow';
     $vws_icon['RAIN AND SNOW'] = '%s_snow';
     $vws_icon['SNOW AND RAIN'] = '%s_snow';
     $vws_icon['LIGHT SNOW MIST'] = '%s_snow';
     $vws_icon['LIGHT SNOW +MIST'] = '%s_snow';
     $vws_icon['CHANCE OF RAIN OR SNOW'] = '%s_snow';
     $vws_icon['FREEZING RAIN OR SNOW'] = '%s_snow';
     $vws_icon['FREEZING FOG'] = '%s_snow';
     $vws_icon['RAIN OR SNOW'] = '%s_snow';
     $vws_icon['RAIN+MIXED+SNOW'] = '%s_snow';
     $vws_icon['SNOW+MIXED+RAIN'] = '%s_snow';
     $vws_icon['MIXPCPN'] = '%s_snow';
     $vws_icon['SLEET+AND+SNOW'] = '%s_snow';
     $vws_icon['SLEET'] = 'sleet';
     $vws_icon['WINTRY MIX'] = '%s_snow';
     $vws_icon['CHANCE OF SNOW OR RAIN'] = '%s_snow';
     $vws_icon['SNOW OR RAIN'] = '%s_snow';
     $vws_icon['PARTLY|MOSTLY+CLOUDY|SUNNY+THUNDERSTORM'] = '%s_tstorm';
     $vws_icon['CHANCE OF+THUNDERSTORM'] = '%s_tstorm';
     $vws_icon['CHANCE T-STORMS'] = '%s_tstorm';
     $vws_icon['NEARBY THUNDERSTORM'] = '%s_tstorm';
     $vws_icon['LIGHT THUNDERSTORM RAIN'] = '%s_tstorm';
     $vws_icon['HEAVY THUNDERSTORM RAIN'] = '%s_tstorm';
     $vws_icon['THUNDERSTORM'] = '%s_tstorm';
     $vws_icon['T-STORMS'] = '%s_tstorm';
     $vws_icon['TSRA'] = '%s_tstorm';
     $vws_icon['VCTS'] = '%s_tstorm';
     $vws_icon['ISOLATED SHOWER'] = '%s_partly_cloudy';
     $vws_icon['HEAVY SNOW'] = '%s_snow';
     $vws_icon['SNOW SHOWER'] = '%s_snow';
     $vws_icon['PARTLY CLOUDY+SHOWERS LIKELY'] = '%s_partly_cloudy';
     $vws_icon['PARTLY CLOUDY+SHOWER'] = '%s_partly_cloudy';
     $vws_icon['MOSTLY CLOUDY|PARTLY SUNNY+SHOWERS LIKELY'] = '%s_partly_cloudy';
     $vws_icon['MOSTLY CLOUDY|PARTLY SUNNY+SHOWER'] = '%s_partly_cloudy';
     $vws_icon['CHANCE OF SHOWER'] = '%s_rain';
     $vws_icon['SCATTERED SHOWER'] = '%s_rain';
     $vws_icon['RAIN SHOWER'] = '%s_rain';
     $vws_icon['SHOWER'] = '%s_rain';
     $vws_icon['MAINLY CLOUDY'] = '%s_cloudy';
     $vws_icon['CLOUDY PERIODS'] = '%s_partly_cloudy';
     $vws_icon['BROKEN CLOUDS'] = '%s_partly_cloudy';
     $vws_icon['FLURR'] = '%s_snow';
     $vws_icon['FAIR'] = '%s_partly_cloudy';
     $vws_icon['LIGHT SNOW'] = '%s_snow';
     $vws_icon['CHANCE OF SNOW'] = '%s_snow';
     $vws_icon['SNOW'] = '%s_snow';
     $vws_icon['SNOW FOG'] = '%s_snow';
     $vws_icon['SNOW +FOG'] = '%s_snow';
     $vws_icon['SNOW FREEZING FOG'] = '%s_snow';
     $vws_icon['SNOW +FREEZING FOG'] = '%s_snow';
     $vws_icon['HEAVY SNOW'] = '%s_snow';
     $vws_icon['HEAVY SNOW +FOG'] = '%s_snow';
     $vws_icon['HEAVY SNOW FREEZING FOG'] = '%s_snow';
     $vws_icon['FRZDRZL'] = '%s_light_rain';
     $vws_icon['LIGHT DRIZZLE'] = '%s_light_rain';
     $vws_icon['FREEZING DRIZZLE'] = '%s_light_rain';
     $vws_icon['FREEZING RAIN'] = '%s_light_rain';
     $vws_icon['HEAVY RAIN'] = '%s_rain';
     $vws_icon['LIGHT RAIN'] = '%s_light_rain';
     $vws_icon['CHANCE RAIN'] = '%s_light_rain';
     $vws_icon['SHOWERS'] = '%s_light_rain';
     $vws_icon['SHOWERS RAIN'] = '%s_light_rain';
     $vws_icon['LIGHT RAIN DRIZZLE +FOG'] = '%s_light_rain';
     $vws_icon['LIGHT RAIN+MIST'] = '%s_light_rain';
     $vws_icon['LIGHT RAIN +MIST'] = '%s_light_rain';
     $vws_icon['LIGHT RAIN MIST'] = '%s_light_rain';
     $vws_icon['LIGHT RAIN SHOWERS +MIST'] = '%s_light_rain';
     $vws_icon['LIGHT RAIN SHOWERS'] = '%s_light_rain';
     $vws_icon['RAIN MIST'] = '%s_light_rain';
     $vws_icon['DRIZZLE +MIST'] = '%s_light_rain';
     $vws_icon['LIGHT DRIZZLE MIST'] = '%s_light_rain';
     $vws_icon['PARTLY CLOUDY+CHANCE OF RAIN'] = '%s_partly_cloudy';
     $vws_icon['MOSTLY CLOUDY+CHANCE OF RAIN'] = '%s_partly_cloudy';
     $vws_icon['CHANCE OF RAIN'] = '%s_rain';
     $vws_icon['OCCASIONAL RAIN'] = '%s_rain';
     $vws_icon['HEAVY RAIN +MIST'] = '%s_rain';
     $vws_icon['RAIN'] = '%s_rain';
     $vws_icon['WINDY'] = 'windy';
     $vws_icon['SAND WHIRLS'] = 'windy';
     $vws_icon['BLUSTERY'] = 'windy';
     $vws_icon['NEARBY FOG'] = 'fog';
     $vws_icon['PATCHY FOG'] = 'fog';
     $vws_icon['PATCHES FOG'] = 'fog';
     $vws_icon['PATCHES OF FOG'] = 'fog';
     $vws_icon['PATCHES FOG MIST'] = 'fog';
     $vws_icon['WIDESPREAD FOG'] = 'fog';
     $vws_icon['FOG MIST'] = 'fog';
     $vws_icon['SHALLOW FOG'] = 'fog';
     $vws_icon['FOG+?DRIZZLE'] = '%s_light_rain';
     $vws_icon['PATCHY+DRIZZLE'] = '%s_light_rain';
     $vws_icon['DRIZZLE'] = '%s_light_rain';
	 $vws_icon['LIGHT DRIZZLE +MIST'] = '%s_light_rain';
     $vws_icon['HAZE +MIST'] = 'mist';
     $vws_icon['MIST'] = 'mist';
     $vws_icon['MIST +SHALLOW FOG'] = 'mist';
     $vws_icon['LIGHT RAIN MIST'] = '%s_light_rain';
     $vws_icon['SMOKE'] = 'haze';
     $vws_icon['FROZEN PRECIP'] = '%s_snow';
     $vws_icon['DRY'] = '%s_clear';
     $vws_icon['VARIABLE HIGH CLOUDINESS'] = '%s_partly_cloudy';
     $vws_icon['PARTLY CLOUDY'] = '%s_partly_cloudy';
     $vws_icon['MOSTLY CLOUDY'] = '%s_cloudy';
     $vws_icon['MOSTLY SUNNY'] = '%s_mostly_sunny';
     $vws_icon['PARTLY SUNNY'] = '%s_partly_cloudy';
     $vws_icon['SUNNY'] = '%s_clear';
     $vws_icon['INCREASING CLOUDINESS'] = '%s_cloudy';
     $vws_icon['FOG'] = 'fog';
     $vws_icon['CLOUDY'] = '%s_cloudy';
     $vws_icon['OCCASIONAL SUNSHINE'] = '%s_partly_cloudy';
     $vws_icon['PARTIAL SUNSHINE'] = '%s_partly_cloudy';
     $vws_icon['CLOUDS'] = '%s_cloudy';
     $vws_icon['OVERCAST'] = '%s_cloudy';
     $vws_icon['MOSTLY CLEAR'] = '%s_clear';
     $vws_icon['CLEAR'] = '%s_clear';
     $vws_icon['CLEAR SKIES'] = '%s_clear';
     $vws_icon['ICE CRYSTALS'] = '%s_light_rain';
     $vws_icon['NO PRECIPITATION'] = '%s_partly_cloudy';
     $vws_icon['LIGHTNING OBSERVED'] = '%s_tstorm';
     $vws_icon['THUNDER'] = '%s_tstorm';
     $vws_icon['MILD AND BREEZY'] = 'windy';
     $vws_icon['NEARBY BLOWING WIDESPREAD DUST'] = 'dust';
     $vws_icon['WIDESPREAD BLOWING DUST'] = 'dust';
     $vws_icon['BLOWING WIDESPREAD DUST'] = 'dust';
     $vws_icon['BLOWING WIDESPREAD DUST +HAZE +SQUALLS'] = 'dust';
     $vws_icon['DUST'] = 'dust';
     $vws_icon['HOT AND HUMID'] = '%s_clear';
     $vws_icon['CONTINUED HOT'] = '%s_clear';
     $vws_icon['FILTERED SUNSHINE'] = '%s_cloudy';



return;

}


function decode_VWS_conds ($time,$forecast,$sunrise,$sunset) {
     // Many thanks to Larry at Anole Computer for the basis of
	 // this routine.
	 // adapted by Ken True to be compatible with WD icon set
	 global $vws_icon,$Debug;
	 
	 $Debug .= "<!-- condition begin: '$time','$forecast','$sunrise','$sunset' -->\n";
	 if(!preg_match('/^\d{1,2}:\d{2}[:\d{2}]{0,1}\s*[am|pm]*$/i',$sunrise)) { $sunrise = '';  }
	 if(!preg_match('/^\d{1,2}:\d{2}[:\d{2}]{0,1}\s*[am|pm]*$/i',$sunset)) { $sunset = '';  }
 
     $sunrise2 = fixupTime(($sunrise<>'')?"$sunrise":"6:00a");
     $sunset2 = fixupTime(($sunset<>'')?"$sunset":"7:00p");
     $time2 =   fixupTime(($time<>'')?"$time":date("H:i",time()));
     if ($time2 >= $sunrise2 and $time2 <= $sunset2) {
         $daynight = 'day';
     } // end if
     else {
         $daynight = 'night';
     } // end else
	 $Debug .= "<!-- condition using: time2='$time2' as $daynight for sunrise2='$sunrise2',sunset2='$sunset2'  -->\n";
  $forecast = trim($forecast);
  if ($forecast <> '' and strtoupper($forecast) <> 'N/A') {
     $temp = sprintf($vws_icon[$forecast],$daynight) . '.gif';
	 if ($temp == '.gif') { $temp = ''; }
	 return ($temp);
  } else {
     return ('');
  }
}

function fixupTime ($intime) {
  global $Debug;
  $t = split(':',$intime);
  if (preg_match('/p/i',$intime)) { $t[0] = $t[0] + 12; }
  if ($t[0] > 23) {$t[0] = 12; }
  if (preg_match('/^12.*a/i',$intime)) { $t[0] = 0; }
  if ($t[0] < '10') {$t[0] = sprintf("%02d",$t[0]); } // leading zero on hour.
  $t2 = join(':',$t); // put time back to gether;
  $t2 = preg_replace('/[^\d\:]/is','',$t2); // strip out the am/pm if any
  $Debug .= "<!-- fixupTime in='$intime' out='$t2' -->\n";
  return($t2);	
	
}

function getBaroTrendArrow($BTRND) {
  global $windArrowDir,$windArrowSize,$showBaroTrendArrow;
  
  $arrows = array(
    'S' => 'W',
	'RS' => 'WSW',
	'R' => 'SW',
	'RR' => 'S',
	'FS' => 'WNW',
	'F'  =>  'NW',
	'FR' =>  'N'
  );
  
  	if ($windArrowSize == 'S') {
	  $windGIF = '-sm.gif';
	  $windSIZE = 'height="9" width="9"';
	} else {
	  $windGIF = '.gif';
	  $windSIZE = 'height="14" width="14"';
	}

  $trend = trim(strtoupper($BTRND));
  $words = explode(" ",$trend . '  ');
  $abbrev= substr($words[0],0,1) . substr($words[1],0,1);
//  $windArrowDir = './arrows/';
  if ($showBaroTrendArrow and $windArrowDir <> '' and isset($arrows[$abbrev])) {
   return("<img src=\"$windArrowDir" . $arrows[$abbrev] . "$windGIF\" $windSIZE 
   alt=\"$BTRND\" title=\"$BTRND\" />");
  } else {
   return("");
  }
}
// ---------------------------------------------------------

function process_metar($metar,$icao) {
  global $lang,$Debug, $wxInfo, $metarPtr, $group;
  $Debug .= "<!-- called process_metar -->\n";
	//   This function directs the examination of each group of the METAR. The problem
	// with a METAR is that not all the groups have to be there. Some groups could be
	// missing. Fortunately, the groups must be in a specific order. (This function
	// also assumes that a METAR is well-formed, that is, no typographical mistakes.)
	//   This function uses a function variable to organize the sequence in which to
	// decode each group. Each function checks to see if it can decode the current
	// METAR part. If not, then the group pointer is advanced for the next function
	// to try. If yes, the function decodes that part of the METAR and advances the
	// METAR pointer and group pointer. (If the function can be called again to
	// decode similar information, then the group pointer does not get advanced.)
	$lang = 'en';
  foreach ($wxInfo as $i => $value) { // clear out prior contents
   unset($wxInfo[$i]);
//    $wxInfo[$i] = '';
  }
//  $Debug .= "<!-- wxInfo cleared\n" . print_r($wxInfo,true) . " -->\n";
  $wxInfo['STATION'] = $icao;
	
	if ($metar != '') {
		$metarParts = explode(' ',$metar);
		$groupName = array('get_station',
                       'get_time',
                       'get_station_type',
                       'get_wind',
                       'get_var_wind',
                       'get_visibility',
                       'get_runway',
                       'get_conditions',
                       'get_cloud_cover',
                       'get_temperature',
                       'get_altimeter');
		$metarPtr = 3;  // get_station identity is ignored
		$group = 1; // start with Time
		
		while ($group < count($groupName)) {
			$part = $metarParts[$metarPtr];
			$Debug .= "<!-- calling '" . $groupName[$group] . "' part='$part' ptr=$metarPtr grp=$group -->\n";
			$groupName[$group]($part);  // $groupName is a function variable
			}
	} else {
	  $wxInfo['ERROR'] = 'Data not available';
	}
}

//----------------------------------------------------------------
// Ignore station code. Script assumes this matches requesting
// $station. This function is never called. It is here for
// completeness of documentation.
function get_station($part)
{ global $lang,$Debug, $wxInfo, $metarPtr, $group;
  if (strlen($part) == 4 and $group == 0)
  {
    $group++;
    $metarPtr++;
  }
}

function get_time($part)
{ global $lang,$Debug, $wxInfo, $metarPtr, $group;
  // Ignore observation time. This information is found in the
  // first line of the NWS file.
  // Format is ddhhmmZ where dd = day, hh = hours, mm = minutes
  // in UTC time.
  if (substr($part,-1) == 'Z') {
     $dd = substr($part,0,2);
	 $hh = substr($part,2,2);
	 $mm = substr($part,4,2);
	 
     $metarPtr++;
  }
  $group++;
}

function get_station_type($part)
{ global $lang,$Debug, $wxInfo, $metarPtr, $group;
  // Ignore station type if present.
  if ($part == 'AUTO' || $part == 'COR')
    $metarPtr++;
  $group++;
}

  function speed($part, $unit)
  {
    global $lang,$Debug, $wxInfo, $metarPtr, $group;
    // Convert wind speed into miles per hour.
    // Some other common conversion factors (to 6 significant digits):
    //   1 mi/hr = 1.15080 knots  = 0.621371 km/hr = 2.23694 m/s
    //   1 ft/s  = 1.68781 knots  = 0.911344 km/hr = 3.28084 m/s
    //   1 knot  = 0.539957 km/hr = 1.94384 m/s
    //   1 km/hr = 1.852 knots  = 3.6 m/s
    //   1 m/s   = 0.514444 knots = 0.277778 km/s
    if ($unit == 'KT') 
      $speed = 1.1508 * $part;    // from knots
    elseif ($unit == 'MPS')
      $speed = 2.23694 * $part;   // from meters per second
    else
      $speed = 0.621371 * $part;  // from km per hour
    $speedkph = $speed / 0.621371;
    if ($lang=="en")
      $speed ="" . round($speed) . " mph (". round($speedkph) . " km/h)";
    else
      $speed = "" . round($speedkph) . " km/h";
    return $speed;
  }

//-------------------------------------------------------------------------
// Decodes wind direction and speed information.
// Format is dddssKT where ddd = degrees from North, ss = speed,
// KT for knots  or dddssGggKT where G stands for gust and gg = gust
// speed. (ss or gg can be a 3-digit number.)
// KT can be replaced with MPH for meters per second or KMH for
//kilometers per hour.
function get_wind($part)
{ global $lang,$Debug, $wxInfo, $metarPtr, $group;
  
  
  if (ereg('^([0-9G]{5,10}|VRB[0-9]{2,3})(KT|MPS|KMH)$',$part,$pieces))
  {
    $part = $pieces[1];
    $unit = $pieces[2];
    if ($part == '00000')
    {
      $wxInfo['WIND'] = 'calm';  // no wind
    }
    else
    {
      ereg('([0-9]{3}|VRB)([0-9]{2,3})G?([0-9]{2,3})?',$part,$pieces);
      if ($pieces[1] == 'VRB')
        $direction = 'varies';
      else
      {
        $angle = (integer) $pieces[1];
        $compass = array('N','NNE','NE','ENE','E','ESE','SE','SSE',
                         'S','SSW','SW','WSW','W','WNW','NW','NNW');
        $direction = $compass[round($angle / 22.5) % 16];
      }
      if ($pieces[3] == 0)
        $gust = '';
      else
        $gust = ', gusting to ' . speed($pieces[3], $unit);
      $wxInfo['WIND'] = $direction . ' at ' . speed($pieces[2], $unit) . $gust;
    }
    $metarPtr++;
  }
  $group++;
}

function get_var_wind($part)
{ global $lang,$Debug, $wxInfo, $metarPtr, $group;
  // Ignore variable wind direction information if present.
  // Format is fffVttt where V stands for varies from fff
  // degrees to ttt degrees.
  if (ereg('([0-9]{3})V([0-9]{3})',$part,$pieces))
    $metarPtr++;
  $group++;
}


//------------------------------------------------------------------
// Decodes visibility information. This function will be called a
// second time if visibility is limited to an integer mile plus a
// fraction part.
// Format is mmSM for mm = statute miles, or m n/dSM for m = mile
// and n/d = fraction of a mile, or just a 4-digit number nnnn (with
// leading zeros) for nnnn = meters.

function get_visibility($part) {

  global $lang,$Debug, $wxInfo, $metarPtr, $group;
  static $integerMile = '';
  if (strlen($part) == 1)
  {
    // visibility is limited to a whole mile plus a fraction part
    $integerMile = $part . ' ';
    $metarPtr++;
  }
  elseif (substr($part,-2) == 'SM')
  {
    // visibility is in miles
    $part = substr($part,0,strlen($part)-2);
    if (substr($part,0,1) == 'M')
    {
      $prefix = 'less than ';
      $part = substr($part, 1);
    }
    else
      $prefix = '';

    if ($lang == "en")
    {
      if (($integerMile == '' && ereg('[/]',$part,$pieces)) || $part == '1')
        $unit = ' mile';
      else
        $unit = ' miles';
    }
    $kmVis = round( $part * 1.6 );
    if ($lang=="en")
      $wxInfo['VISIBILITY'] = $prefix . $integerMile . 
                              " $part $unit ($kmVis km)";
    else
      $wxInfo['VISIBILITY'] = "$kmVis km";
    $metarPtr++;
    $group++;
  }
  elseif (substr($part,-2) == 'KM')
  {
    // unknown (Reported by NFFN in Fiji)
    $metarPtr++;
    $group++;
  }
  elseif (ereg('^([0-9]{4})$',$part,$pieces))
  {
    // visibility is in meters
    $distance = round($part/ 621.4, 1);      // convert to miles
    if ($distance > 5)
      $distance = round($distance);
    if ($distance <= 1)
      $unit = ' mile';
    else
      $unit = ' miles';
    $wxInfo['VISIBILITY'] = $distance . $unit;
    $metarPtr++;
    $group++;
  }
  elseif ($part == 'CAVOK')
  {
    // good weather
    $wxInfo['VISIBILITY'] = 'greater than 7 miles';  // or 10 km
    $wxInfo['CONDITIONS'] = 'Clear';
    $wxInfo['CLOUDS'] = 'clear skies';
    $metarPtr++;
    $group += 4;  // can skip the next 3 groups
  }
  else
  {
    $group++;
  }
}

function get_runway($part)
{ global $lang,$Debug, $wxInfo, $metarPtr, $group;
  // Ignore runway information if present. Maybe called a second time.
  // Format is Rrrr/vvvvFT where rrr = runway number and
  // vvvv = visibility in feet.
  if (substr($part,0,1) == 'R')
    $metarPtr++;
  else
    $group++;
}



function get_conditions($part) {
global $lang,$Debug, $wxInfo, $metarPtr, $group;
	// Decodes current weather conditions. This function maybe called several times
	// to decode all conditions. To learn more about weather condition codes, visit section
	// 12.6.8 - Present Weather Group of the Federal Meteorological Handbook No. 1 at
	// www.nws.noaa.gov/oso/oso1/oso12/fmh1/fmh1ch12.htm
	static $conditions = '';
        $Debug .= "<!-- conditions='$conditions' on entry -->\n";
	static $wxCode = array(
		'VC' => 'Nearby',
		'MI' => 'Shallow',
		'PR' => 'Partial',
		'BC' => 'Patches of',
		'DR' => 'Low Drifting',
		'BL' => 'Blowing',
		'SH' => 'Showers',
		'TS' => 'Thunderstorm',
		'FZ' => 'Freezing',
		'DZ' => 'Drizzle',
		'RA' => 'Rain',
		'SN' => 'Snow',
		'SG' => 'Snow Grains',
		'IC' => 'Ice Crystals',
		'PE' => 'Ice Pellets',
		'GR' => 'Hail',
		'GS' => 'Small Hail',  // and/or snow pellets
		'UP' => 'Unknown',
		'BR' => 'Mist',
		'FG' => 'Fog',
		'FU' => 'Smoke',
		'VA' => 'Volcanic Ash',
		'DU' => 'Widespread Dust',
		'SA' => 'Sand',
		'HZ' => 'Haze',
		'PY' => 'Spray',
		'PO' => 'Well-developed Dust/Sand Whirls',
		'SQ' => 'Squalls',
		'FC' => 'Funnel Cloud, Tornado, or Waterspout',
		'SS' => 'Sandstorm/Duststorm');
	if (ereg('^(-|\+|VC)?(TS|SH|FZ|BL|DR|MI|BC|PR|RA|DZ|SN|SG|GR|GS|PE|IC|UP|BR|FG|FU|VA|DU|SA|HZ|PY|PO|SQ|FC|SS|DS)+$',$part,$pieces)) {
	    $Debug .= "<!-- get_conditions part='$part' -->\n";
		if (strlen($conditions) == 0) $join = '';
		else $join = '+';
		if (substr($part,0,1) == '-') {
			$prefix = 'light ';
			$part = substr($part,1);
			}
		elseif (substr($part,0,1) == '+') {
			$prefix = 'heavy ';
			$part = substr($part,1);
			}
		else $prefix = '';  // moderate conditions have no descriptor
		$conditions .= $join . $prefix;
		// The 'showers' code 'SH' is moved behind the next 2-letter code to make the English translation read better.
		if (substr($part,0,2) == 'SH') $part = substr($part,2,2) . substr($part,0,2). substr($part, 4);
		while ($code = substr($part,0,2)) {
			$conditions .= $wxCode[$code] . ' ';
			$part = substr($part,2);
			}
		$wxInfo['CONDITIONS'] = $conditions;
//        $Debug .= "<!-- conditions='$conditions' metarPtr incr -->\n";
		$metarPtr++;
		}
	else {
		$wxInfo['CONDITIONS'] = $conditions;
		$group++;
//        $Debug .= "<!-- conditions='$conditions' group incr -->\n";
		$conditions = '';
//        $Debug .= "<!-- conditions='$conditions' reset -->\n";
		}
}

function get_cloud_cover($part) {
global $lang,$Debug, $wxInfo, $metarPtr, $group;
	// Decodes cloud cover information. This function maybe called several times
	// to decode all cloud layer observations. Only the last layer is saved.
	// Format is SKC or CLR for clear skies, or cccnnn where ccc = 3-letter code and
	// nnn = altitude of cloud layer in hundreds of feet. 'VV' seems to be used for
	// very low cloud layers. (Other conversion factor: 1 m = 3.28084 ft)
	static $cloudCode = array(
		'SKC' => 'Clear',
		'CLR' => 'Clear',
		'FEW' => 'Partly Cloudy',
		'SCT' => 'Scattered Clouds',
		'BKN' => 'Mostly Cloudy',
		'OVC' => 'Overcast',
		'VV'  => 'vertical visibility');
	$Debug .= "<!-- get cloud cover '$part' -->\n";
	if ($part == 'SKC' || $part == 'CLR') {
		$wxInfo['CLOUDS'] = $cloudCode[$part];
		$metarPtr++;
		$group++;
		}
	else {
		if (ereg('^([A-Z]{2,3})([0-9]{3})',$part,$pieces)) {  // codes for CB and TCU are ignored
			$wxInfo['CLOUDS'] = $cloudCode[$pieces[1]];
			if ($pieces[1] == 'VV') {
				$altitude = (integer) 100 * $pieces[2];  // units are feet
//				$wxInfo['CLOUDS'] .= " to $altitude ft";
				$wxInfo['CLOUDS'] = "Overcast";
				}
			$metarPtr++;
			}
		else {
			$group++;
			}
		}
}

  function get_heat_index($tempF, $rh)
  { global $lang,$Debug, $wxInfo, $metarPtr, $group;
    // Calculate Heat Index based on temperature in F and relative
    //humidity (65 = 65%)
    if ($tempF > 79 && $rh > 39)
    {
      $hiF = -42.379 + 2.04901523 * $tempF + 10.14333127 * 
             $rh - 0.22475541 * $tempF * $rh;
      $hiF += -0.00683783 * pow($tempF, 2) - 0.05481717 * pow($rh, 2);
      $hiF += 0.00122874 * pow($tempF, 2) * $rh + 0.00085282 * $tempF 
             * pow($rh, 2);
      $hiF += -0.00000199 * pow($tempF, 2) * pow($rh, 2);
      $hiF = round($hiF);
      $hiC = round(($hiF - 32) / 1.8);
      $wxInfo['HEAT INDEX'] = "$hiF&deg;F ($hiC&deg;C)";
    }
  }

  function get_wind_chill($tempF)
  {
    global $lang,$Debug, $wxInfo, $metarPtr, $group;

    // Calculate Wind Chill Temperature based on temperature in F and
    // wind speed in miles per hour
    if ($tempF < 51 && $wxInfo['WIND'] != 'calm')
    {
      $pieces = explode(' ', $wxInfo['WIND']);
      $windspeed = (integer) $pieces[2]; // wind speed must be in mph
      if ($windspeed > 3)
      {
        $chillF = 35.74 + 0.6215 * $tempF - 35.75 * pow($windspeed, 0.16) + 
                  0.4275 * $tempF * pow($windspeed, 0.16);
        $chillF = round($chillF);
        $chillC = round(($chillF - 32) / 1.8);
        $wxInfo['WIND CHILL'] = "$chillF&deg;F ($chillC&deg;C)";
      }
    }
  }

//-------------------------------------------------------------------------
// Decodes temperature and dew point information. Relative humidity is
// calculated. Also, depending on the temperature, Heat Index or Wind
// Chill Temperature is calculated.
// Format is tt/dd where tt = temperature and dd = dew point temperature.
// All units are in Celsius. A 'M' preceeding the tt or dd indicates a
// negative temperature. Some stations do not report dew point, so the
// format is tt/ or tt/XX.
function get_temperature($part)
{
  global $lang, $Debug, $wxInfo, $metarPtr, $group;

  if (ereg('^(M?[0-9]{2})/(M?[0-9]{2}|[X]{2})?$',$part,$pieces))
  {
    $tempC = (integer) strtr($pieces[1], 'M', '-');
    $tempF = round(1.8 * $tempC + 32);
    if ($lang=="en")
        $wxInfo['TEMP'] = $tempF . "F (" . $tempC . "C)";
    else
        $wxInfo['TEMP'] = $tempC . "C";
    get_wind_chill($tempF);
    if (strlen($pieces[2]) != 0 && $pieces[2] != 'XX')
    {
      $dewC = (integer) strtr($pieces[2], 'M', '-');
      $dewF = round(1.8 * $dewC + 32);
      if ($lang == "en")
          $wxInfo['DEWPT'] = $dewF . "F (" . $dewC . "C)";
      else
          $wxInfo['DEWPT'] = $dewC . "C";
      $rh = round(100 * pow((112 - (0.1 * $tempC) + $dewC) / 
                                                (112 + (0.9 * $tempC)), 8));
      $wxInfo['HUMIDITY'] = $rh . '%';
      get_heat_index($tempF, $rh);
    }
    $metarPtr++;
    $group++;
  }
  else
  {
    $group++;
  }
}


//-----------------------------------------------------------------------
// Decodes altimeter or barometer information.
// Format is Annnn where nnnn represents a real number as nn.nn in
// inches of Hg,
// or Qpppp where pppp = hectoPascals.
// Some other common conversion factors:
//   1 millibar = 1 hPa
//   1 in Hg = 0.02953 hPa
//   1 mm Hg = 25.4 in Hg = 0.750062 hPa
//   1 lb/sq in = 0.491154 in Hg = 0.014504 hPa
//   1 atm = 0.33421 in Hg = 0.0009869 hPa

function get_altimeter($part)
{  global $Debug, $wxInfo, $metarPtr, $group;
  if (ereg('^(A|Q)([0-9]{4})',$part,$pieces))
  {
    if ($pieces[1] == 'A')
    {
      $pressureIN = substr($pieces[2],0,2) . '.' . substr($pieces[2],2);
      // units are inches Hg, converts to hectoPascals
      $pressureHPA = round($pressureIN / 0.02953);
    }
    else
    {
      $pressureHPA = (integer) $pieces[2];        // units are hectoPascals
      $pressureIN = round(0.02953 * $pressureHPA,2);  // convert to inches Hg
    }
    $wxInfo['BAROMETER'] = "$pressureHPA hPa ($pressureIN in Hg)";
    $metarPtr++;
    $group++;
  }
  else
  {
    $group++;
  }
}


// end of the process_metar function set
//----------------------------------------------


?>
