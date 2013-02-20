<?php
// PHP script by Ken True, webmaster@saratoga-weather.org
// quake-USA-test.php  
// Version 1.01 - 31-Mar-2006 -- initial release
// Version 1.02 - 09-Jul-2006 -- fix updated date display
// Version 1.03 - 18-Nov-2006 -- fix for updated USGS website
// Version 1.04 - 18-Jan-2007 -- fix for updated USGS website
// Version 1.05 - 17-Nov-2007 - modified view function, added $doIncludeQuake for non-URL include usage
// Version 1.06 - 08-Mar-2008 - modified for PHP5 and setup with Carterlake/WD/AJAX/PHP template set
// Version 1.07 - 12-Mar-2008 - renamed from quake-USA-test.php to quake-USA.php + cosmetic changes
// Version 1.08 - 26-Apr-2008 - fix for UTC-to-timezone delta seconds for some servers
// Version 1.09 - 03-Jul-2009 - PHP5 support for timezone setting
// Version 1.10 - 04-Nov-2008 - fix for USGS website change
// Version 1.11 - 26-Jan-2011 - added support for $cacheFileDir global cache files
//
    $Version = "quake-USA.php V1.11 26-Jan-2011";
//  error_reporting(E_ALL);  // uncomment to turn on full error reporting
// script available at http://saratoga-weather.org/scripts.php
//  
// you may copy/modify/use this script as you see fit,
// no warranty is expressed or implied.
//
// Customized for: USA earthquakes from
//   http://earthquake.usgs.gov/earthquakes/recenteqsus/Quakes/quakes_all.php
//  displays 1.0+ USA (lower 48, Alaska, Hawaii, Puerto Rico)
//
// (note: there are two other scripts available: 
//  a CANV script:  California/Nevada quakes of 1.0+ magnitude
//  a WORLD script:   USA 2.5+ and World 4.0+ quakes
//  see http://saratoga-weather.org/scripts.php for more information)
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
//  http://your.website/quake-USA-test.php?tablesonly=Y&magnitude=2.1&distance=45
//  would return data without HTML header/footer for earthquakes of
//  magnitude 2.1 or larger within a 45 mile radius of your location.
//
// Usage:
//  you can use this webpage standalone (customize the HTML portion below)
//  or you can include it in an existing page:
/*
//            <?php $doIncludeQuake = true;
//                  include("quake-USA.php");
//            ?> 
*/
//  no parms:  $doIncludeQuake = true; include("quake-USA.php"); 
//  parms:    include("http://your.website/quake-USA.php?tableonly=Y&magnitude=2.0&distance=50");
//
//
// settings:  
//  change myLat, myLong to your station latitude/longitude, 
//  set $ourTZ to your time zone
//    other settings are optional
//
// minRichter= smallest quake to display (world is 4.0+, USA is 2.5+ on USGS
// cacheName is name of file used to store cached USGS webpage
// 
//  set to station latitude/longitude (decimal degrees)
  $myLat = 37.27153397;    //North=positive, South=negative decimal degrees
  $myLong = -122.02274323;   //East=positive, West=negative decimal degrees
// The above settings are for saratoga-weather.org location
//
  $ourTZ = "America/Chicago";  //NOTE: this *MUST* be set correctly to
// translate UTC times to your LOCAL time for the displays.
//  http://saratoga-weather.org/timezone.txt  has the list of timezone names
//  pick the one that is closest to your location and put in $ourTZ
// also available is the list of country codes (helpful to pick your zone
//  from the timezone.txt table
//  http://saratoga-weather.org/country-codes.txt : list of country codes

//  pick a format for the time to display ..uncomment one (or make your own)
//$timeFormat = 'D, Y-m-d H:i:s T';  // Fri, 2006-03-31 14:03:22 TZone
  $timeFormat = 'D, d-M-Y H:i:s T';  // Fri, 31-Mar-2006 14:03:22 TZone
  
  $highRichter = "4.0"; //change color for quakes >= this magnitude
  $cacheFileDir = './';   // default cache file directory
  $cacheName = "quakesUSA.txt";  // used to store the file so we don't have to
  //                          fetch it each time
  $refetchSeconds = 1800;     // refetch every nnnn seconds

// end of settings

// Constants
// don't change $baseURL or $fileName or script may break ;-)
  $baseURL = "http://earthquake.usgs.gov";  //USGS website (omit trailing slash)
  $fileName = "http://earthquake.usgs.gov/earthquakes/recenteqsus/Quakes/quakes_all.php";
// end of constants

// ------ start of code -------
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

// overrides from Settings.php if available
global $SITE;
if (isset($SITE['latitude'])) 	{$myLat = $SITE['latitude'];}
if (isset($SITE['longitude'])) 	{$myLong = $SITE['longitude'];}
if (isset($SITE['tz'])) {$ourTZ = $SITE['tz']; }
if (isset($SITE['timeFormat'])) {$timeFormat = $SITE['timeFormat'];}
if(isset($SITE['cacheFileDir']))     {$cacheFileDir = $SITE['cacheFileDir']; }
// end of overrides from Settings.php

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

if ( ! isset($_REQUEST['magnitude']) )
        $_REQUEST['magnitude']="2.0";
$minRichter = $_REQUEST['magnitude'];
if (! preg_match("/^[\d\.]+$/",$minRichter) ) {
   $minRichter = "2.0";  // default for bad data input
}	
if ($minRichter <= "1.0") {$minRichter = "1.0";}
if ($minRichter >= "9.0") {$minRichter = "9.0";}

if ( ! isset($_REQUEST['distance']) )
        $_REQUEST['distance']="150";
$maxDistance = $_REQUEST['distance'];
if (! preg_match("/^\d+$/",$maxDistance) ) {
   $maxDistance = "150"; // default for bad data input
}
if ($maxDistance <= "10") {$maxDistance = "10";}
if ($maxDistance >= "8000") {$maxDistance = "8000";}		
// for testing only 
if ( isset($_REQUEST['lat']) ) { $myLat = $_REQUEST['lat']; }
if ( isset($_REQUEST['lon']) ) { $myLong = $_REQUEST['lon']; }
if ( isset($_REQUEST['testloc']) ) { setTestLoc($_REQUEST['testloc']); } // allows for test override


// omit HTML <HEAD>...</HEAD><BODY> if only tables wanted	
// --------------- customize HTML if you like -----------------------
if (! $tablesOnly) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Refresh" content="300" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Earthquakes of magnitude <?php echo $minRichter; ?> within <?php echo $maxDistance; ?> miles</title>
</head>
<body style="background-color:#FFFFFF;">
<?php
}

// ------------- code starts here -------------------
echo "<!-- $Version -->\n";
// Establish timezone offset for time display
 # Set timezone in PHP5/PHP4 manner
  if (!function_exists('date_default_timezone_set')) {
	putenv("TZ=" . $ourTZ);
#	$Status .= "<!-- using putenv(\"TZ=$ourTZ\") -->\n";
    } else {
	date_default_timezone_set("$ourTZ");
#	$Status .= "<!-- using date_default_timezone_set(\"$ourTZ\") -->\n";
   }
 print("<!-- server lcl time is: " . date($timeFormat) . " -->\n");
 print("<!-- server GMT time is: " . gmdate($timeFormat) . " -->\n");
 print("<!-- server timezone for this script is: " . getenv('TZ')." -->\n");
 $timediff = date("Z");
 print "<!-- TZ Delta = $timediff seconds (" . $timediff/3600 . " hours) -->\n";


// refresh cached copy of page if needed
// fetch/cache code by Tom at carterlake.org
$cacheName = $cacheFileDir . $cacheName;

if (file_exists($cacheName) and filemtime($cacheName) + $refetchSeconds > time()) {
      echo "<!-- using Cached version of $cacheName -->\n";
      $html = implode('', file($cacheName));
    } else {
      echo "<!-- loading $cacheName from $fileName -->\n";
      $html = fetchUrlWithoutHangingQUSA($fileName);
      $fp = fopen($cacheName, "w");
      if ($fp) {
        $write = fputs($fp, $html);
        fclose($fp);
      } else {
            print "<!-- unable to write cache file $cacheName -->\n";
      }
      echo "<!-- loading finished. -->\n";
	}

// find the Updated date
 preg_match('|<FONT COLOR="#CC0000">(.*)<BR/>|Usi',$html,$updated);
 $updated = $updated[1]; // Update time = Fri Mar 31 17:00:03 UTC 2006
 print "<!-- '$updated' -->\n";
 $updated = substr($updated,15);  // Fri Mar 31 17:00:03 UTC 2006
 $updated = preg_replace("|\S+ (\S+) (\S+) (\S+) (\S+) (\S+)|",
                         "\\2-\\1-\\5 \\3 \\4",$updated);
 $UTCdate = strtotime($updated);  // get unix time for date
 $updatedUTC = "Update time = " . gmdate($timeFormat,$UTCdate);
 $updated = "Update time = " . date($timeFormat,$UTCdate);
 print "<!-- $updatedUTC -->\n"; 
 print "<!-- $updated -->\n";

//get the earthquake datr lines
 preg_match_all('|<A NAME="listtop"></A>(.*)</table>|si',$html,$betweenspan);
 $quakerawdata = $betweenspan[1][0];
 $quakedata = explode("\n",$quakerawdata);

 $doneHeader = 0;  // flag to determine when to do the header
 // things to clean out of the quake table row
 $removestr = array (
      "'<strong>|</strong>|<tr>|</tr>|<font[^>]+>|</font>'si"
	  );

 // examine, process and format each line -- omit quakes not
 //   meeting the $minRichter and $maxDistance criteria
 $quakesFound = 0; 
 foreach ($quakedata as $key => $quake) {

   if (preg_match("|^<tr><td align=|i",$quake))  // keep only the data ) 
	{
    // clear out USGS formatting <strong> and <font>
    $quake = preg_replace($removestr,"",$quake);
    preg_match_all('|<td[^>]*>(.*)</td>|Uis',$quake,$values);
	$values = $values[1];

// Now we have the array values as:
//Array
//(
//    [0] => <A HREF="/earthquakes/recenteqsus/Maps/US2/32.34.-118.-116.php">MAP</A>
//    [1] => 1.2 
//    [2] => <a href="/earthquakes/recenteqsus/Quakes/ci14219652.php">2006/03/30 17:17:48</a>

//    [3] => <a href="/earthquakes/recenteqsus/Quakes/ci14219652.php"> 33.413</a>
//    [4] => <a href="/earthquakes/recenteqsus/Quakes/ci14219652.php">-116.666</a>
//    [5] => <a href="/earthquakes/recenteqsus/Quakes/ci14219652.php"> 16.2</a>
//    [6] => &nbsp; 16 km ( 10 mi) S   of Anza, CA
//)
//  print "<!-- values\n" . print_r($values,true) . " -->\n";

      if ($values[1] >= "$minRichter")  {  // lets process it

      // load local variables

      // slice up map url
	  $temp = preg_split("|href=\"([^\"]+)\">([^<]+)</a>|Usi",$values[0],-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
 	  $mapURL = $baseURL . $temp[1];
	  $magnitude = $values[1];

      // slice up time and details url
	  $temp = preg_split("|href=\"([^\"]+)\">([^<]+)</a>|Usi",$values[2],-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

	  $url = $baseURL . $temp[1];
	  $QDateTime = date($timeFormat,strtotime($temp[2])+$timediff);

      // slice up latitude and details url
	  $temp = preg_split("|href=\"([^\"]+)\">([^<]+)</a>|Usi",$values[3],-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	  $latitude = $temp[2];

      // slice up longitude and details url
	  $temp = preg_split("|href=\"([^\"]+)\">([^<]+)</a>|Usi",$values[4],-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
      $longitude = $temp[2];

      // slice up depth and details url
	  $temp = preg_split("|href=\"([^\"]+)\">([^<]+)</a>|Usi",$values[5],-1,PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	  $depth = $temp[2];
	  $depth = $depth . " km (" . round($depth*0.621371192) . " mi)";
	  
	  $location = substr($values[6],7);  // get rid of '&nbsp; '
	  $location = preg_replace("|(\d+) km\s+\(\s+(\d+) mi\)|","\$2 mi ( \$1 km)",$location);
	  $location = preg_replace("|\(Probable|i","<br />(Probable",$location);
	  
	  // provide highlighting for quakes >= $highRichter
	  if ($magnitude >= $highRichter) {
	     $magnitude = "<span style=\"color: red\">$magnitude</span>";
	     $location = "<span style=\"color: red;\">$location</span>";
	  }
	  
	  $distanceM = round(distance($myLat,$myLong,$latitude,$longitude,"M"));
	  $distanceK = round(distance($myLat,$myLong,$latitude,$longitude,"K"));
	  
      if ($distanceM <= $maxDistance) { // only print 'close' ones
	  $quakesFound++;    // keep a tally of quakes for summary
	  if (! $doneHeader) {  // print the header if needed
// --------------- customize HTML if you like -----------------------
	    print "
<table class=\"quake\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\">
<tr><th colspan=\"5\" align=\"center\">Earthquakes of magnitude $minRichter or greater within $maxDistance miles</th></tr>
<tr><th colspan=\"5\" align=\"center\">$updated</th></tr>
<tr><th>Epicenter Near</th><th>Magnitude</th><th>Distance to <br />Epicenter</th><th>Local Time</th><th>Link to<br />Map</th></tr>
";
	    $doneHeader = 1;
	  } // end doneHeader
// --------------- customize HTML if you like -----------------------
	    print "
<tr>
  <td><a href=\"$url\">$location</a></td>
  <td align=\"center\"><b>$magnitude</b></td>
  <td align=\"left\" nowrap=\"nowrap\"><b>$distanceM</b> mi (<b>$distanceK</b> km)</td>
  <td align=\"left\" nowrap=\"nowrap\">$QDateTime</td>
  <td align=\"center\"><a href=\"$mapURL\">map</a></td>
</tr>\n";
	  } // end maxdistance
	
	  } // end minRichter
		 
		 
	 } // end skip non-data
  } // end foreach loop

// finish up.  Write trailer info
 
	  if ($doneHeader) {
// --------------- customize HTML if you like -----------------------
	     print "</table><p>$quakesFound earthquakes found. Click on location or map links for more details from the <a href=\"http://earthquake.usgs.gov/earthquakes/recenteqsus/\">USGS</a>.</p>\n";
	  
	  } else {
// --------------- customize HTML if you like -----------------------
	    print "<p>No earthquakes of magnitude $minRichter or greater within $maxDistance miles reported in last 7 days.</p>\n";
	  
	  }	 

// print footer of page if needed    
// --------------- customize HTML if you like -----------------------
if (! $tablesOnly ) {   
?>

</body>
</html>

<?php
}

// ----------------------------functions ----------------------------------- 
 
 function fetchUrlWithoutHangingQUSA($url) // thanks to Tom at Carterlake.org for this script fragment
   {
   // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed
   $numberOfSeconds=4;   

   // Suppress error reporting so Web site visitors are unaware if the feed fails
   error_reporting(0);

   // Extract resource path and domain from URL ready for fsockopen

   $url = str_replace("http://","",$url);
   $urlComponents = explode("/",$url);
   $domain = $urlComponents[0];
   $resourcePath = str_replace($domain,"",$url);

   // Establish a connection
   $socketConnection = fsockopen($domain, 80, $errno, $errstr, $numberOfSeconds);

   if (!$socketConnection)
       {
       // You may wish to remove the following debugging line on a live Web site
          print("<!-- Network error: $errstr ($errno) -->");
       }    // end if
   else    {
       $xml = '';
       fputs($socketConnection, "GET $resourcePath HTTP/1.1\r\nConnection: close\r\nHost: $domain\r\n\r\n");
   
       // Loop until end of file
       while (!feof($socketConnection))
           {
           $xml .= fgets($socketConnection, 4096);
           }    // end while

       fclose ($socketConnection);

       }    // end else

   return($xml);

   }    // end function
   
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
  
//To calculate the delta between the local time and UTC:
function tzdelta ( $iTime = 0 )
{
   if ( 0 == $iTime ) { $iTime = time(); }
   $ar = localtime ( $iTime );
   $ar[5] += 1900; $ar[4]++;
   $iTztime = gmmktime ( $ar[2], $ar[1], $ar[0],
       $ar[4], $ar[3], $ar[5], $ar[8] );
   return ( $iTztime - $iTime );
}
//  testing function to safely set location/distance/zone using testloc= parm 
function setTestLoc ( $LOC )
{
  global $myLat,$myLong,$ourTZ,$maxDistance,$minRichter;
  
  if ($LOC == 'MX') {
     $myLat = 19.3999;   
    $myLong = -99.1999; 
    $ourTZ = "America/Mexico_City";  
	$maxDistance = 250;
  } elseif ($LOC == 'PR') {
    $myLat = 18.467248;   
    $myLong = -66.108963; 
    $ourTZ = "America/Puerto_Rico";  
	$maxDistance = 250;
  } elseif ($LOC == 'AK') {
     $myLat = 61.21574783;   
    $myLong = -149.86894226; 
    $ourTZ = "America/Anchorage";  
	$maxDistance = 150;
  } elseif ($LOC == 'HI') {
     $myLat = 19.7032;   
    $myLong = -155.09377; 
    $ourTZ = "Pacific/Honolulu";  
	$maxDistance = 250;
   } elseif ($LOC == 'WA') {
     $myLat = 47.6117;   
    $myLong = -122.3333; 
    $ourTZ = "America/Los_Angeles";  
	$maxDistance = 250;
  } elseif ($LOC == 'MO') {
    $myLat = 38.63132;   
    $myLong = -90.19215393; 
    $ourTZ = "America/Chicago"; 
	$minRichter = 1.5; 
	$maxDistance = 250;
  } elseif ($LOC == 'NV') {
    $myLat = 39.51833;   
    $myLong = -119.98778; 
    $ourTZ = "PST8PDT"; 
	$minRichter = 2.0; 
	$maxDistance = 150;
 }

} 
  
// --------------end of functions ---------------------------------------


?>
