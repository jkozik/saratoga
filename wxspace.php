<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-USA template set)
############################################################################
#
#   Project:    Sample Included Website Design
#   Module:     sample.php
#   Purpose:    Sample Page
#   Authors:    Kevin W. Reed <kreed@tnet.com>
#               TNET Services, Inc.
#
# 	Copyright:	(c) 1992-2007 Copyright TNET Services, Inc.
############################################################################
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA
############################################################################
#	This document uses Tab 4 Settings
############################################################################
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE= $SITE['organ'] . " - Space Weather";
$showGizmo = true;  // set to false to exclude the gizmo
include("top.php");
############################################################################
?>
</head>
<body>
<?php
############################################################################
include("header.php");
############################################################################
include("menubar.php");
############################################################################
?>

<?php

// get spaceweather current update , parse and display

// settings --------------------------------------------------------

$Version        = 'V 1.2 24-Oct-2009';
$cacheName      = 'spaceweather-current.txt';  // used to cache the file
$refetchSeconds = 1800; // # 1800 = 1/2 hour - refetch every nnnn seconds (600=5 minutes)
$SPACE_URL      = 'http://www.swpc.noaa.gov/today.html';

// end of settings -------------------------------------------------

$Status = "<!-- space weather - $Version -->\n";

// refresh cached copy of page if needed
// fetch/cache code by Tom at carterlake.org

if (file_exists($cacheName) and filemtime($cacheName) + $refetchSeconds > time()) {
      $Status .= "<!-- using Cached version from $cacheName -->\n";
      $age = time() - filectime($cacheName);
      $nextFetch = $refetchSeconds - $age;
      $Status .= "<!-- cache is $age seconds old. Refetch in $nextFetch secs. -->\n";
} else {
      $Status .= "<!-- loading $cacheName from URL\n $SPACE_URL\n -->\n";
      $html = GrabURLWithoutHangingSW($SPACE_URL,false);
      $fp = fopen($cacheName, "w");
      if ($fp) {
        $write = fputs($fp, $html);
        fclose($fp);
      } else {
            $Status .= "<!-- unable to write cache file $cacheName -->\n";
      }
      $Status .= "<!-- loading finished. -->\n";
}

?>

<div id="main-copy">

          <h3>Space Weather Observations, Alerts, and Forecast</h3>


<br />

<h3>3-day Solar-Geophysical Forecast <?php echo main_code();

echo $Status;
?>

<div style="text-align:center">
<a href="http://www.swpc.noaa.gov/alerts/archive/current_month.html">Space Weather Alerts - Current Month</a>
</div>

<br />
<h3>Real Time Images of the Sun</h3>
<br />


<table width="99%" cellpadding="0" border="0" cellspacing="0">
<tr>

<td align="center">
<a href="http://sohowww.nascom.nasa.gov/data/realtime-images.html">SOHO EIT 304</a><br />
<a href="http://sohowww.nascom.nasa.gov/data/LATEST/current_eit_304.mpg">
 <img src="image-space-eit-304.php?dontcache=<?php echo time(); ?>"
      style="border:none;" width="170" height="170"
      alt="Click for time-lapse image of the sun"
      title="Click for time-lapse image of the sun"
      /></a>
</td>

<td align="center">
<a href="http://sohowww.nascom.nasa.gov/data/realtime-images.html">SOHO EIT 284</a><br />
<a href="http://sohowww.nascom.nasa.gov/data/realtime-images.html">
<img src="image-space-eit-284.php?dontcache=<?php echo time(); ?>"
     style="border:none;" width="170" height="170"
     alt="SOHO EIT 284 image of the sun"
     title="SOHO EIT 284 image of the sun" /></a>
</td>

<td align="center">
<a href="http://mlso.hao.ucar.edu/cgi-bin/mlso_homepage.cgi">Mauna Loa Solar Image</a><br />
<a href="http://mlso.hao.ucar.edu/cgi-bin/mlso_homepage.cgi">
 <img src="image-space-solar-disk.php?dontcache=<?php echo time(); ?>"
         style="border:none;" width="170" height="170"
         alt="Latest Mauna Loa image of the Sun"
         title="Latest Mauna Loa image of the Sun" /></a>
</td>

</tr>
</table>

<p>The sun is constantly monitored for <a href="http://en.wikipedia.org/wiki/Sunspot">sun spots</a> and <a href="http://en.wikipedia.org/wiki/Coronal_mass_ejections">coronal mass ejections</a>.
EIT (Extreme ultraviolet Imaging Telescope) images the solar atmosphere at several wavelengths,
and therefore, shows solar material at different temperatures.
In the images taken at 304 Angstrom the bright material is at 60,000 to 80,000 degrees Kelvin.
In those taken at 171 Angstrom, at 1 million degrees.
195 Angstrom images correspond to about 1.5 million Kelvin, 284 Angstrom to 2 million degrees.
The hotter the temperature, the higher you look in the solar atmosphere.</p>

<h3>Real Time Solar X-ray and Solar Wind</h3>
<br />

<table width="99%" cellpadding="0" border="0" cellspacing="0">
<tr>

<td align="center">
 <a href="http://www.swpc.noaa.gov/SolarCycle/">Solar Cycle Progression</a><br />
<a href="http://www.swpc.noaa.gov/SolarCycle/">
<img src="image-space-solar-cycle.php?dontcache=<?php echo time(); ?>"
     style="border:none;" width="239" height="183"
     alt="Graph showing current solar cycle progression"
     title="Graph showing current solar cycle progression" /></a>
     <br />
    Solar Cycle chart updated using the latest ISES predictions.
</td>

<td align="center" valign="top">
 <a href="http://www.swpc.noaa.gov/ace/">Real-Time Solar Wind</a><br />
<a href="http://www.swpc.noaa.gov/ace/">
<img src="image-space-solar-wind.php?dontcache=<?php echo time(); ?>"
     style="border:none;" width="310" height="170"
     alt="Graph showing Real-Time Solar Wind"
     title="Graph showing Real-Time Solar Wind" /></a>
     <br />
     Real-Time Solar Wind data broadcast from NASA's ACE satellite.
</td>

</tr>
</table>

<p>
The <a href="http://en.wikipedia.org/wiki/Solar_cycle">Solar Cycle</a> is observed by counting the frequency and placement of sunspots visible on the Sun.
Solar minimum occurred in December, 2008. Solar maximum is expected to occur in May, 2013.
</p>

<table width="99%" cellpadding="0" border="0" cellspacing="0">
<tr>
<td align="center">
<a href="http://www.swpc.noaa.gov/today.html#xray">Solar X-ray Flux</a><br />
 <a href="http://www.swpc.noaa.gov/today.html#xray">
 <img src="image-space-xray.php?dontcache=<?php echo time(); ?>"
 style="border:none;" width="320" height="240"
 alt="Graph showing Real-Time Solar X-ray Flux"
 title="Graph showing Real-Time Solar X-ray Flux" /></a><br />
This plot shows 3-days of 5-minute solar x-ray flux values measured on the SWPC primary and secondary GOES satellites.
</td>

<td align="center">
<a href="http://www.swpc.noaa.gov/today.html#satenv">Satellite Environment Plot</a><br />
 <a href="http://www.swpc.noaa.gov/today.html#satenv">
 <img src="image-space-sat-env.php?dontcache=<?php echo time(); ?>"
 style="border:none;" width="320" height="240"
 alt="Graph showing Real-Time Satellite Environment Plot"
 title="Graph showing Real-Time Satellite Environment Plot" /></a><br />
The Satellite Environment Plot combines satellite and ground-based data to provide an overview of the current geosynchronous satellite environment.
</td>

</tr>
</table>

<br />
<h3>Auroral Activity Extrapolated from NOAA POES</h3>
<br />

<table width="99%" cellpadding="0" border="0" cellspacing="0">
<tr>

<td align="center">
<a href="http://www.swpc.noaa.gov/pmap/index.html">Northern Hemi Auroral Map</a><br />
  <a href="http://www.swpc.noaa.gov/pmap/index.html">
   <img src="image-space-aurora.php?dontcache=<?php echo time(); ?>"
        style="border:none;" width="315" height="280"
        alt="Current Northern hemispheric power input map"
        title="Current Northern hemispheric power input map" /></a>
</td>

<td align="center">
<a href="http://www.swpc.noaa.gov/pmap/index.html">Southern Hemi Auroral Map</a><br />
  <a href="http://www.swpc.noaa.gov/pmap/index.html">
   <img src="image-space-aurora-s.php?dontcache=<?php echo time(); ?>"
        style="border:none;" width="315" height="280"
        alt="Current Southern hemispheric power input map"
        title="Current Southern hemispheric power input map" /></a>
</td>

</tr>
</table>

<p>
Instruments on board the NOAA Polar-orbiting Operational Environmental Satellite (POES) continually monitor the power flux carried by the protons and electrons that produce aurora in the atmosphere. SWPC has developed a technique that uses the power flux observations obtained during a single pass of the satellite over a polar region (which takes about 25 minutes) to estimate the total power deposited in an entire polar region by these auroral particles.
The power input estimate is converted to an auroral activity index that ranges from 1 to 10.
</p>

<h3>Credits: </h3>
Space Weather Images and Information (excluded from copyright) courtesy of:
<a href="http://www.swpc.noaa.gov/">NOAA / NWS Space Weather Prediction Center</a>,
<a href="http://mlso.hao.ucar.edu/cgi-bin/mlso_homepage.cgi">Mauna Loa Solar Observatory (HAO/NCAR)</a>,
and <a href="http://sohowww.nascom.nasa.gov/home.html">SOHO (ESA &amp; NASA)</a>.
<br />
<br />

<b>Space Weather links: </b><br />
<a href="http://www.swpc.noaa.gov/forecast.html">3-Day Forecast of Solar and Geophysical Activity</a><br />
<a href="http://www.swpc.noaa.gov/SWN/index.html">Space Weather Now</a><br />
<a href="http://www.swpc.noaa.gov/today.html">Today's Space Weather</a><br />
<a href="http://www.swpc.noaa.gov/ace/">Real-Time Solar Wind</a><br />
<a href="http://www.swpc.noaa.gov/advisories/outlooks.html">Space Weather Outlooks</a><br />
<a href="http://www.swpc.noaa.gov/advisories/bulletins.html">Space Weather Bulletins</a><br />
<a href="http://www.swpc.noaa.gov/alerts/index.html">Space Weather Alerts and Warnings</a><br />
<a href="http://www.swpc.noaa.gov/alerts/archive/current_month.html">Space Weather Alerts - Current Month</a><br />
<a href="http://sohowww.nascom.nasa.gov/home.html">Solar and Heliospheric Observatory (SOHO)</a><br />
<a href="http://sohowww.nascom.nasa.gov/data/realtime-images.html">The Very Latest SOHO Images</a><br />

<p>Powered by <a href="http://www.642weather.com/weather/scripts-space-weather.php">Space Weather PHP script</a> by Mike Challis</p>

</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################

// ----------------------------functions -----------------------------------

function main_code() {

  global $cacheName;

   if (!$data = file_get_contents("$cacheName") ) {
      $string = gracefulerror('Error reading spaceweather current update data.');
      return $string;
   }

   preg_match('|3-day Solar-Geophysical Forecast</a>(.*)(</blockquote>{1})|Uis', $data, $betweenspan);
   $string = $betweenspan[1];

   $string = preg_replace('|</b>|is','</b><br />',$string);

   if ($string == '' || preg_match("|You don't have permission to access|i",$data)) {
     $string = '</h3><br />forecast not available';
   }

   // security feature
   $string = strip_tags($string, '<p><h3><b><br>');

   return $string;

}

// get contents from one URL and return as string
function GrabURLWithoutHangingSW($url,$useFopen) {
// thanks to Tom at Carterlake.org for this script fragment
  $Debug = '';
  $UA = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)';
  if (! $useFopen) {
   // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed
   $numberOfSeconds = 4;

   // Suppress error reporting so Web site visitors are unaware if the feed fails
   error_reporting(0);

   // Extract resource path and domain from URL ready for fsockopen
   $FullUrl = $url;
   $url = str_replace("http://","",$url);
   $urlComponents = explode("/",$url);
   $domain = $urlComponents[0];
   $resourcePath = str_replace($domain,"",$url);
   $Debug .= "<!-- GET $resourcePath HTTP/1.1 \n Host: $domain -->\n";
   $time_start = microtime_float();

   // Establish a connection
   $socketConnection = fsockopen($domain, 80, $errno, $errstr, $numberOfSeconds);

   if (!$socketConnection){
       // You may wish to remove the following debugging line on a live Web site
       // print("<!-- Network error: $errstr ($errno) -->");
   }else {
       $xml = '';
       fputs($socketConnection, "GET $resourcePath HTTP/1.1\r\nHost: $domain\r\nUser-agent: $UA\r\nConnection: close\r\n\r\n");

       // Loop until end of file
       while (!feof($socketConnection)) {
           $xml .= fgets($socketConnection, 4096);
       }
       fclose ($socketConnection);
   }
   $time_stop = microtime_float();
   $total_time += ($time_stop - $time_start);
   $time_fetch = sprintf("%01.3f",round($time_stop - $time_start,3));
   $Debug .= "<!-- Time to fetch: $time_fetch sec -->\n";

   list($headers,$html) = explode("\r\n\r\n",$xml,2);
   echo $Debug;
   return($html);
 } else {
   $Debug .= "<!-- using file function -->\n";
   $time_start = microtime_float();
   $xml = implode('',file($url));
   $time_stop = microtime_float();
   $total_time += ($time_stop - $time_start);
   $time_fetch = sprintf("%01.3f",round($time_stop - $time_start,3));
   $Debug .= "<!-- Time to fetch: $time_fetch sec -->\n";
   echo $Debug;
   return($xml);
 }
}    // end fetchUrlWithoutHanging

function microtime_float() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function gracefulerror($reason) {
  return "Space Weather Forecast Unavailable<br />$reason";
}

?>