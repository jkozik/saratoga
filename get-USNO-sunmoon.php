<?php
function getUSNOsunmoon() {
/*
Function: get-USNO-sunmoon()
Purpose: fetch and cache the sun/moon data for one day from the US Naval Observatory, 
    using a POST request to http://aa.usno.navy.mil/cgi-bin/aa_pap.pl
    parse the returned HTML, and return data in the an array.

Calling sequence:

    $array = getUSNOsunmoon();

Returned array contents like:

    $Data['beginciviltwilight'] => 06:52
    $Data['beginciviltwilightdate'] => 01/18/2011
    $Data['sunrise'] => 07:20
    $Data['sunrisedate'] => 01/18/2011
    $Data['suntransit'] => 12:18
    $Data['suntransitdate'] => 01/18/2011
    $Data['sunset'] => 17:17
    $Data['sunsetdate'] => 01/18/2011
    $Data['endciviltwilight'] => 17:46
    $Data['endciviltwilightdate'] => 01/18/2011
    $Data['moonriseprior'] => 15:13
    $Data['moonrisepriordate'] => 01/17/2011
    $Data['moonset'] => 06:16
    $Data['moonsetdate'] => 01/18/2011
    $Data['moonrise'] => 16:21
    $Data['moonrisedate'] => 01/18/2011
    $Data['moontransit'] => 23:45
    $Data['moontransitdate'] => 01/18/2011
    $Data['moonsetnext'] => 07:02
    $Data['moonsetnextdate'] => 01/19/2011
    $Data['moonphase'] => Waxing Gibbous
    $Data['illumination'] => 98%
    $Data['hoursofpossibledaylight'] => 09:57
	

Author: Ken True - webmaster@saratoga-weather.org
*/
//  Version 1.00 - 18-Jan-2011 - initial release
//  Version 1.01 - 23-Mar-2011 - added code for missing moonrise/moonset due to prior/next day times
//  Version 1.02 - 03-Dec-2011 - fixed moonset date if for following day
$Version = 'get-USNO-sunmoon.php - Version 1.02 - 03-Dec-2011';
// -----------local settings-------------------
$ourTZ = "America/Los_Angeles";     //NOTE: this *MUST* be set correctly to
// translate UTC times to your LOCAL time for the displays.
//  set to station latitude/longitude (decimal degrees)
$myLat  = 37.27153397;             //North=positive, South=negative decimal degrees
$myLong = -122.02274323;           //East=positive, West=negative decimal degrees
// The above settings are for saratoga-weather.org location
$myCity = 'Saratoga';              // my city name
$useMDY = true;                    // true=use mm/dd/yyyy for dates, false=use dd/mm/yyyy for dates
$cacheFileDir = './';              // default cache file directory
$cacheName = "USNO-moondata.txt";  // used to store the file so we don't have to fetch from USNO website
$refetchSeconds = 3600;            // refetch every nnnn seconds 3600=1 hour
// -----------end local settings --------------

// overrides from Settings.php if available
global $SITE;
if (isset($SITE['latitude'])) 	{$myLat = $SITE['latitude'];}
if (isset($SITE['longitude'])) 	{$myLong = $SITE['longitude'];}
if (isset($SITE['tz'])) {$ourTZ = $SITE['tz']; }
if (isset($SITE['location'])) {$myCity = $SITE['location'];}
if (isset($SITE['WDdateMDY']))  {$useMDY = $SITE['WDdateMDY'];}
if(isset($SITE['cacheFileDir']))     {$cacheFileDir = $SITE['cacheFileDir']; }
// end of overrides from Settings.php

global $Debug;
$Debug = "<!-- $Version -->\n";
$Data = array();

# Set timezone in PHP5/PHP4 manner
  if (!function_exists('date_default_timezone_set')) {
	putenv("TZ=" . $ourTZ);
#	$Status .= "<!-- using putenv(\"TZ=$ourTZ\") -->\n";
    } else {
	date_default_timezone_set("$ourTZ");
#	$Status .= "<!-- using date_default_timezone_set(\"$ourTZ\") -->\n";
   }
   
if(isset($_REQUEST['force']) or isset($_REQUEST['cache']) ) {
	$refetchSeconds = 1;
}

$doDebug = false;
if(isset($_REQUEST['debug'])) {$doDebug = true;}

# fixup the POST parameters before the call to the USNO website so it looks like the form is used for the query

list($xx0,$xx1,$xx2) = toDM($myLong,+1,-1); // USNO expects longitude in degrees, minutes
list($yy0,$yy1,$yy2) = toDM($myLat,+1,-1);  // USNO expects latitude in degrees, minutes
$myCity = urlencode($myCity);               // make the location 'URL safe'
list($xxy,$xxm,$xxd,$tzo) = explode(" ",date("Y n j Z",time())); // USNO has separate fields for time and timezone to use
$zz1 = abs($tzo/3600);  // USNO needs timezone offset as positive number
$zz0 = ($tzo>=0)?1:-1;  // USNO wants +1 for east of GMT, -1 for west of gmt

$PostParms = "FFX=2&ID=AA&xxy=$xxy&xxm=$xxm&xxd=$xxd&place=$myCity&xx0=$xx0&xx1=$xx1&xx2=$xx2&yy0=$yy0&yy1=$yy1&yy2=$yy2&zz1=$zz1&zz0=$zz0&ZZZ=END";
$USNOUrl = 'http://aa.usno.navy.mil/cgi-bin/aa_pap.pl';

$cacheName = $cacheFileDir.$cacheName;

// either load the cached html page or fetch and cache a new html page
if (file_exists($cacheName) and filemtime($cacheName) + $refetchSeconds > time()) {
      $Debug .= "<!-- using Cached version of $cacheName -->\n";
      $html = implode('', file($cacheName));
    } else {
      $Debug .= "<!-- loading $cacheName from $USNOUrl -->\n";
      $html = PostURLWithoutHanging($USNOUrl,$PostParms);
      $fp = fopen($cacheName, "w");
      if ($fp) {
        $write = fputs($fp, $html);
        fclose($fp);
      } else {
            $Debug .= "<!-- unable to write cache file $cacheName -->\n";
      }
      $Debug .= "<!-- loading finished. -->\n";
	}

/*
USNO returns info like:

 <p>The following information is provided for Saratoga                        
                                                          
 (longitude W122.0, latitude N37.3): </p>
 <pre>
        Tuesday  
        18 January 2011       Universal Time - 8h            

                         <strong>SUN</strong>
        Begin civil twilight      06:52                 
        Sunrise                   07:20                 
        Sun transit               12:18                 
        Sunset                    17:17                 
        End civil twilight        17:46                 

                         <strong>MOON</strong>
        Moonrise                  15:13 on preceding day
        Moonset                   06:16                 
        Moonrise                  16:21                 
        Moon transit              23:45                 
        Moonset                   07:02 on following day

 </pre> 

 <p>Phase of the Moon on 18 January:     &nbsp; waxing gibbous 
 with  98% of the Moon's visible disk illuminated. </p>

 <p>Full          Moon on 19 January   2011 at 13:22     
 (Universal Time - 8h).              </p>
*/

// now slice the page for the main times for the sun and moon

preg_match('|(\n\s{8}Begin Civil Twilight.*)</pre>|is',$html,$matches);
if($doDebug) {$Debug .= "<!-- find pre slice\n".print_r($matches,true)." -->\n";}
$slice = $matches[1];
$slice = preg_replace('|\n\s{25}<strong>MOON</strong>\n|i','',$slice);
preg_match_all('|\n\s{8}(.*)\s+(\d\d:\d\d) ([\S\s]{16})|Uis',$slice,$matches);

if($doDebug) {$Debug .= "<!-- find main parts\n".print_r($matches,true)." -->\n";}

$Data = array();

$useDateFormat = $useMDY?"m/d/Y":"d/m/Y";
$dateprior = date($useDateFormat,strtotime("-1 day"));
$datenow   = date($useDateFormat);
$datenext  = date($useDateFormat,strtotime("+1 day"));
									
foreach ($matches[1] as $i => $name) {
	$event = strtolower(trim($name));
	$event = preg_replace('|\s+|is','',$event);
	$etime = trim($matches[2][$i]);
	$emod  = trim($matches[3][$i]);
	$usedate = $datenow;
	if ($emod <> '') {
		if (preg_match('|preceding|is',$emod)) {$emod = 'prior'; $usedate = $dateprior;}
		if (preg_match('|following|is',$emod)) {$emod = 'next';  $usedate = $datenext;}
	}
	$event .= $emod;
	$Data["$event"] = $etime;
	$Data["$event".'date'] = $usedate;
	
}

// now extract the current phase and illumination %

preg_match('|Phase of the Moon on .*:\s+&nbsp;\s+(.*)\n\s+with\s+(\d+)% of the Moon|Uis',$html,$matches); 
if($doDebug) {$Debug .= "<!-- find Phase/illum.\n".print_r($matches,true)." -->\n";}
if(isset($matches[1])) {
	$Data['moonphase'] = ucwords(trim($matches[1]));
}
if(isset($matches[2])) {
	$Data['illumination'] = trim($matches[2]).'%';
}
if(isset($Data['sunrise']) and isset($Data['sunset'])) {
	$diff =	strtotime($Data['sunset'])-strtotime($Data['sunrise']);
	$diffh = intval($diff/3600); // hours
	$diffm = intval(($diff / 60) % 60);
	$Data['hoursofpossibledaylight'] = sprintf("%02d:%02d",$diffh,$diffm);
}

if( !isset($Data['moonrise']) and isset($Data['moonriseprior']) ) {
	$Debug .= "<!-- moonrise missing.. using moonriseprior -->\n";
	$Data['moonrise'] = $Data['moonriseprior'];
	$Data['moonrisedate'] = $Data['moonrisepriordate'];
}

if( !isset($Data['moonset']) and isset($Data['moonsetnext']) ) {
	$Debug .= "<!-- moonset missing.. using moonsetnext -->\n";
	$Data['moonset'] = $Data['moonsetnext'];
	$Data['moonsetdate'] = $Data['moonsetnextdate'];
}

$Debug .= "<!-- USNOdata\n".print_r($Data,true) . " -->\n";

print $Debug;

return($Data);
} // end of getUSNOsunmoon function

# --------- end of mainline function --------------

function toDM($val,$dir1,$dir2) { // convert decimal degrees to sign, degrees, minutes
  $sign = ($val >=0)?$dir1:$dir2;
  $deg = intval($val);
  $min = sprintf("%02d",intval(60*abs($val-$deg)));
  $deg = abs($deg);
  return (array($sign,$deg,$min));

}

// get contents from one URL and return as string 
 function PostUrlWithoutHanging($url,$PostParms) {
// thanks to Tom at Carterlake.org for this script fragment
  global $Debug, $TOTALtime;
  $overall_start = time();
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
   if(isset($urlParts['query']))    {$resourcePath .= "?" . $urlParts['query']; }
   if(isset($urlParts['fragment'])) {$resourcePath .= "#" . $urlParts['fragment']; }
   $T_start = my_microtime();
   $hostIP = gethostbyname($domain);
   $T_dns = my_microtime();
   $ms_dns  = sprintf("%01.3f",round($T_dns - $T_start,3));
   
   $Debug .= "<!-- POST $resourcePath HTTP/1.1 \n      Host: $domain  Port: $port IP=$hostIP-->\n";
//   print "GET $resourcePath HTTP/1.1 \n      Host: $domain  Port: $port IP=$hostIP\n";

   // Establish a connection
   $socketConnection = fsockopen($hostIP, $port, $errno, $errstr, $numberOfSeconds);
   $T_connect = my_microtime();
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
	   $getString = "POST $resourcePath HTTP/1.1\r\nHost: $domain\r\nConnection: Close\r\n";
	   $getString .= "Accept: text/plain,text/html\r\nAccept-Encoding: gzip;q=0,compress;q=0\r\n";
	   $getString .= "User-agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13\r\n";
	   $getString .= "Referer: http://aa.usno.navy.mil/data/docs/RS_OneDay.php\r\n";
	   $getString .= "Content-Length: ".strlen($PostParms)."\r\n";
       $getString .= "Content-Type: application/x-www-form-urlencoded\r\n";
	   $getString .= "\r\n";
	   $getString .= "$PostParms\r\n";
	   $Debug .= "<!-- Sending:\n$getString\n-->\n";
       fputs($socketConnection, $getString);
       $T_puts = my_microtime();
	   
       // Loop until end of file
	   $TGETstats = array();
	   $TGETcount = 0;
       while (!feof($socketConnection))
           {
		   $T_getstart = my_microtime();
           $xml .= fgets($socketConnection, 16384);
		   $T_getend = my_microtime();
		   $TGETcount++;
		   $TGETstats[$TGETcount] = sprintf("%01.3f",round($T_getend - $T_getstart,3));
           }    // end while
       $T_gets = my_microtime();
       fclose ($socketConnection);
       $T_close = my_microtime();
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

   }    // end PostUrlWithoutHanging

// ------------------------------------------------------------------

function my_microtime()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

?>