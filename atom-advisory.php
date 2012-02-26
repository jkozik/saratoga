<?php
/*
NOAA ATOM/CAP Advisory PHP Script
rss-advisory.php  version 1.00 - 17-Apr-2006 (Ken True)
atom-advisory.php version 2.00 - 31-May-2009 (Mike Challis)

http://saratoga-weather.org/scripts.php

You are free to use and modify the code

This php code provided "as is", and Ken True, Mike Challis
disclaims any and all warranties, whether express or implied, including
(without limitation) any implied warranties of merchantability or
fitness for a particular purpose.

Version 1.01 - 16-Aug-2006 - added cacheing for RSS feed
Version 1.02 - 08-Dec-2007 - added time formatting for lastBuildDate
Version 1.03 - 17-Dec-2007 - added safety features from Mike Challis - http://www.642weather.com/weather/scripts.php
                             fixed XHTML 1.0-Strict output, improved debug code,
                             added file-mode include functions
Version 1.04 - 22-Jan-2008 - added graceful failure code from Mike Challis
Version 1.05 - 27-Jan-2008 - added many features from Mike Challis, rename to rss-advisory.php
                             accommodates a different CSS style for when there is no advisory,
                             backwards compatible with old include methods if you are not going to use the
                             CSS style switch for no advisory / advisory
                             NOTE: to use the new feature (CSS style switch for no advisory / advisory)
                             use the new php include params and add the sample css (see settings notes),
                             added invalid zone error code
                             added option to lowercase the advisories text
Version 1.06 - 02-Feb-2008 - added wxflyer.php support from Mike Challis.
                             see: http://www.weather-watch.com/smf/index.php/topic,28523.0.html
                             when used for a printable flyer, advisory titles are shown without URL links
                             Live example: http://www.642weather.com/weather/local-flier.php
                             (the live example only shows advisories when they are currently active)
Version 1.07 - 04-Feb-2008 - added support for Carterlake/AJAX/PHP template set
Version 1.08 - 17-Mar-2008 - added improved cache file error handling
Version 2.00 - 31-May-2009 - beta version specific to new beta NWS feeds
                             http://www.weather.gov/alerts-beta/
                             modded by Mike Challis - http://www.642weather.com/weather/scripts.php
                             (not backwards compatible with old feeds, requires PHP5)
                             see: http://www.weather-watch.com/smf/index.php/topic,40119.0.html
Version 2.00 - 31-Aug-2009   No longer BETA
Version 2.01 - 21-Sep-2009   Renamed at atom-advisory.php to avoid confusion and allow coexistance with
                             rss-advisory.php which used the RSS feeds instead of the ATOM/CAP feeds.
Version 2.02 - 22-Sep-2009   Changed to use HTTP/1.1 requests
Version 2.03 - 29-Oct-2009   Added County zone code capability (M Challis)
Version 2.04 - 28-Jan-2010   Fixed unterminated entity reference error (M Challis)
Version 2.05 - 05-Jul-2010   Added new $doZoneTitles setting (M Challis)
Version 2.06 - 26-Jan-2011   Added support for $cacheFileDir global cache directory
Version 2.07 - 05-Mar-2011   Updated support for $cacheFileDir with template override
Version 2.08 - 15-Mar-2011   Changed URL for NWS ATOM/CAP feeds
Version 2.09 - 05-Nov-2011   fixes for "* Storm Warning" icon selection

*/
$Version = 'atom-advisory.php - V2.09 05-Nov-2011';
/*

This script gets the current ATOM/CAP Zone Watch/Warning/Advisory/Statement
from www.weather.gov/alerts-beta/ for the selected Zone and provides either
a summary (titles only, with links) or details.  It returns
'There are no active watches, warnings or advisories for zone CAZ513'
if there are no cited hazards in the Zone CAZ513.

output: creates XHTML 1.0-Strict HTML page (default)

Options on URL:
inc=Y           -- returns only the body code for inclusion in other webpages.  Omit to return full HTML.
summary=Y       -- returns only the titles of the cited hazards N.N or greater.
zone=ssZnnn     -- select the Zone to use.
   Pick the Zone from the RSS feed list at http://www.weather.gov/alerts/
   and use the RSS County/Zone for your area.
detailpage=xxx   -- set your page of full advisories ie: detailpage=advisories.php
cache=refresh    -- reload the cache

Example URL:
http://your.website/atom-advisory.php?zone=CAZ513
  would return data for hazards in Zone=CAZ513 which is Santa Clara County, California

#########
# PHP Include Usage:
#########
You can include this with summary output only
or you can include it with full advisory text output:

There are two ways of including the script in your pages depending on whether your hoster has
allow_url_include option set to on or off. The URL method only works if allow_url_fopen
is on in your PHP settings. The File method usually always works.

#########
# Include and display alert links only, not full alert details:
# Live example: http://www.642weather.com/weather/index.php
#########
to include this with Summary output only, put the following code in your php page inside php tags:

if ( isset($_GET['zone']) ) {
   $DefaultZone = $_GET['zone'];
} else {
   $DefaultZone = 'WAZ021'; // set to your NOAA zone
}
$detailpage  = 'advisories.php'; // overrides $hurlURL setting
$doSummary   = 1;  // display alert links only, not full alert details
$includeOnly = 1;  // include mode
$noprint     = 1;  // required for echo $advisory_html output
include 'atom-advisory.php';
if (preg_match("|There are no active|i",$advisory_html) ||
   preg_match("|Advisory Information Unavailable|i",$advisory_html)) {
   echo '<div class="advisoryBoxnoactive">' .$advisory_html .'</div>';
}else{
   echo '<div class="advisoryBox">' . $advisory_html .'</div>';
}

#########
# Include and display full alert details::
# Live example: http://www.642weather.com/weather/advisories.php
#########
to include this with Full advisory output, put the following code in your php page inside php tags:

if ( isset($_GET['zone']) ) {
   $DefaultZone = $_GET['zone'];
} else {
   $DefaultZone = 'WAZ021'; // set to your NOAA zone
}
$detailpage  = 'advisories.php'; // overrides $hurlURL setting
$includeOnly = 1;  // include mode
$noprint     = 1;  // required for echo $advisory_html output
include 'atom-advisory.php';
if (preg_match("|There are no active|i",$advisory_html) ||
   preg_match("|Advisory Information Unavailable|i",$advisory_html)) {
   echo '<div class="advisoryBoxnoactive">' .$advisory_html .'</div>';
}else{
   echo $advisory_html;
}

#########
# CSS sample:
#########
put this css style code in your css file (modify the colors if you like)
(no need to worry about this if you use the PHP carterlake templates)
see live sample at http://www.642weather.com/weather/index.php
forum discussion page:
http://www.weather-watch.com/smf/index.php/topic,29274.0.html

.advisoryBox {
color: black;
font-size: 12px;
text-align: center;
background-color: #FFE991;
margin: 0 0 0 0;
padding: .5em 0em .5em 0em;
border: 1px dashed rgb(34,70,79);
}
.advisoryBoxnoactive {
color: black;
font-size: 12px;
text-align: center;
background-color: white;
margin: 0 0 0 0;
padding: .5em 0em .5em 0em;
border: 1px dashed rgb(34,70,79);
}

//------------------------------------------------------------------
// begin settings
//------------------------------------------------------------------
*/

//  change $myDefaultZone default(below) to your stations county zone
//  other settings are outlined below, and are optional

$myDefaultZone = 'CAZ513'; // <== change this to your zone

$hurlURL = 'wxadvisory.php'; // <== change this default to your webpage to open for details

$HD = 'h2'; // <== type of heading for advisories <$HD>...</$HD>

$doLowerCase = true; // <== change to true to lowercase the advisories (new in ver. 1.05)

$doLongTitles = true; // <== change to true to add the zone name to the Alert Title (new in ver. 2.00)
// Heat Advisory
// Heat Advisory - South Washington Coast (Washington)

$doZoneTitles = true; // <== change to true to add the full zone name to the No Active Alert Title
// false = There are no active watches, warnings or advisories for zone WAZ021.
// true =  There are no active watches, warnings or advisories for South Washington Coast (Washington).

// cacheDir cacheName is name of file used to store cached current conditions
$cacheFileDir = './';   // default cache file directory
$cacheName = 'atom-advisory.txt'; // used to store the file so we don't have to fetch it each time
// note: the real cache name will automatically be the above name with the Zone included
// ie:  rss-advisory.txt => rss-advisory-CAZ513.txt

$fullMessagesMode = true; // note: enabling this uses more resources,
// but is desired to fetch the complete message for each alert.  (new in ver. 2.00)

// Set the timezone for your location, because some servers are in different timezone than your location
// http://saratoga-weather.org/timezone.txt  has the list of timezone names
// uncomment one ourTZ setting: NOTE: this *MUST* be set correctly or alert times will be incorrect
//$ourTZ = 'America/New_York';  // Eastern Time
//$ourTZ = 'America/Chicago';   // Central Time
//$ourTZ = 'America/Denver';    // Mountain Time
//$ourTZ = 'America/Phoenix';   // Mountain Standard Time - Arizona
$ourTZ = 'America/Los_Angeles'; // Pacific Time

// time format for alert's updated, effective and expired timestamps
$timeFormat = 'D d-M-y h:ia T'; //  Tue 02-Jun-09 06:14pm TZone

// cache interval time
$refetchSeconds = 600; // refetch every nnn seconds (600=10 minutes)

// Alert icons (new in version V2.00 20-Jul-2009 BETA .025)
// Each alert will display along with an icon image to indicate the type and severity of the alert.
// To use this feature, you must download the icons from here:
// www.642weather.com/weather/scripts/noaa-advisory-images.zip
$enable_alert_icons = true; // this feature is optional

// Alert icons directory. Only needed if Alert icons are enabled.
// This setting is so you can name the images folder to what ever you like.
$icons_folder = './alert-images'; // No slash on end

// $flyer (this setting moved to wxflyer.php)
// as of version 2.00, this setting moved to wxflyer.php

// $flyerpromo (this setting moved to wxflyer.php)
// as of version 2.00, this setting moved to wxflyer.php

//------------------------------------------------------------------
// end settings. Do not alter any code below this point in the script or it may not run properly.
//------------------------------------------------------------------

if (phpversion() < 5) {
  echo 'Failure: This ATOM/CAP Advisory Script requires PHP version 5 or greater. You only have PHP version: ' . phpversion();
  exit;
}

// overrides from Settings.php if available
global $SITE;
if (isset ($SITE['noaazone'])) {
  $myDefaultZone = $SITE['noaazone'];
}
if (isset ($SITE['hurlURL'])) {
  $hurlURL = $SITE['hurlURL'];
}
if (isset ($SITE['timeFormat'])) {
  $timeFormat = $SITE['timeFormat'];
}
if (isset ($SITE['tz'])) {
  $ourTZ = $SITE['tz'];
}
if (isset($SITE['cacheFileDir']))     {
  $cacheFileDir = $SITE['cacheFileDir']; 
}

// end of overrides from Settings.php if available

if (!isset ($PHP_SELF)) {
  $PHP_SELF = $_SERVER['PHP_SELF'];
}// needed for some PHP installations

if (!isset ($DefaultZone)) {
  $DefaultZone = $myDefaultZone;
}
if (!isset ($hurlURL)) {
  $hurlURL = $PHP_SELF;
}

// Set timezone in PHP5/PHP4 manner
if (!function_exists('date_default_timezone_set')) {
        putenv("TZ=" . $ourTZ);
} else {
        date_default_timezone_set("$ourTZ");
}

// initialize vars
global $Status, $advcount;
$advcount = 0;
$Summary = '';
$string = '';
$Status = '';
$curl_debug = '';
$xml = '';
$WLink = 0;
$get_file_failed = 0;

//  set desired zone
$Zone = $DefaultZone;
$invalid_zone = 0;
if (!preg_match("/^[a-z]{2}[C|Z][0-9]{3}$/i", $Zone)) {
  $invalid_zone = 1; // valid zone syntax from $DefaultZone setting
}
// mchallis added security update - protect zone value input. allowed zone = [2letters][C or Z][3numbers]
if (isset($_GET['zone']) && preg_match("/^[a-z]{2}[C|Z][0-9]{3}$/i", $_GET['zone'])) {
  $Zone = $_GET['zone'];  // valid zone syntax from input
} else if (isset ($_GET['zone']) && !preg_match("/^[a-z]{2}[C|Z][0-9]{3}$/i", $_GET['zone'])) {
  $invalid_zone = 1;   // invalid zone syntax from input
}

if (isset($_REQUEST['inc']) || isset($includeOnly)) {
  $includeOnly = true;   // full html output or just contents (inc=Y)?
} else {
  $includeOnly = false;
}
if (isset($_REQUEST['flyer']) || isset($flyer)) {
  $flyer = true;   // used for printable flyer
} else {
  $flyer = false;
}
if (isset($_REQUEST['summary']) || isset($doSummary)) {
  $doSummary = true;
} else {
  $doSummary = false;
}
if (isset ($_REQUEST['noprint']) || isset($noprint)) {
  $noprint = true;
} else {
  $noprint = false;
}
if (isset ($_REQUEST['detailpage']) ) {
  $detailpage = $_REQUEST['detailpage'];
}
if (isset($detailpage) ) {
  // new, safer way of setting detailpage
  $t = parse_url(urldecode($detailpage));
  if (isset ($t['path'])) {
    $t['path'] = htmlspecialchars(strip_tags($t['path']));
    $t['path'] = preg_replace('/[^A-Za-z0-9-._]/i','', $t['path']); // character filter
    $Status .= "<!-- detailpage\n" . $t['path'] . " -->\n";
    $hurlURL = $t['path'];
  }
}
if (isset ($_GET['sce']) and $_GET['sce'] == 'view' and !$includeOnly) {
  // -- self downloader --
  $filenameReal = __file__;
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
if (isset ($_GET['cache']) and strtolower($_GET['cache']) == 'refresh') {
  $refetchSeconds = 1;  // set short period for refresh of cache
}


 // fix up zone if need be.... only alpha-numeric and uppercase needed
$Zone = strtoupper(preg_replace('/[^A-Za-z0-9]/i', '', $Zone));

$RSS_URL = "http://alerts.weather.gov/cap/wwaatmget.php?x=$Zone";

$t = pathinfo($PHP_SELF);
$Program = $t['basename'];

$cacheName = $cacheFileDir . $cacheName;
$cacheName = preg_replace('|\.txt$|i', "-$Zone.txt", $cacheName);

$Status = "<!-- $Program - $Version -->\n";

if (!$invalid_zone) {
 if (file_exists($cacheName) and filemtime($cacheName) + $refetchSeconds > time()) {  //600=10 min
   $age = time() - filectime($cacheName);
   $nextFetch = $refetchSeconds - $age;
   $Status .= "<!-- using cached version from $cacheName - age=$age secs. Next fetch in $nextFetch secs. -->\n";
   $html = file_get_contents($cacheName);
   if (strlen($html) < 300) {
     $Status .= "<!-- Warning: cache file size = " . strlen($html) . " is too small.. XML data not found. -->\n";
     $Status .= "<!-- Cache file HTML contents: " . htmlspecialchars(strip_tags(str_replace('-->','',trim($html)))) . " -->\n";
     $get_file_failed = 1;
   }
  if (preg_match("/<channel>/",$html)) {
     $Status .= "<!-- Warning:
     The cache file is conflicting with another script using the soon to be outdated NOAA feeds.
     You possibly have another rss-advisory.php or rss-top-warning.php script that is pre version 2.00
     Fix: make sure all your NOAA feed scripts are version 2.00 or higher
     or
     Rename the \$cacheName = 'rss-advisory.txt'; setting in this script to something else like: \$cacheName = 'rss-advisorynew.txt';
     Then the conflict will be resolved.
     -->\n";
     $get_file_failed = 2;
   }

 }
 else {
   $Status .= "<!-- getting new file from $RSS_URL -->\n";

   if (!function_exists('curl_init')) {
     $xml = GrabURLWithoutHangingRA($RSS_URL);
   }
   else {
     $xml = curl_fetch_file($RSS_URL,0);
   }
   $Status .= $curl_debug;
   $curl_debug = '';

   // mchallis added to format cap code tags
   $xml = str_replace("cap:", 'cap_', $xml);
   $xml = str_replace('/&//&/', '', $xml);

   $junk = 'n/a';
   if (preg_match("/\r\n\r\n/", $xml)) {
     list($junk, $xml) = explode("\r\n\r\n", $xml, 2);
   }
   if ($xml == '') {
     $xml = $junk;    // curl method does not have the headers
     $junk = 'n/a';
   }
   if (preg_match("/invalid zone/i", $xml)) {
     $xml = 'invalid zone';
   }
   if ($xml != 'invalid zone') { // not going to be a valid data return, do not cache it
     $fp = fopen($cacheName, "w");
     if ($fp) {
       $write = fputs($fp, $xml);
       fclose($fp);
       $Status .= "<!-- cache saved to $cacheName -->\n";
     }
     else {
       $Status .= "<!-- unable to write $cacheName -->\n";
     }
   }
   if (strlen($xml) < 300 && $xml != 'invalid zone') {  // not going to be a valid data return, do cache it
     $Status .= "<!-- HTML characters length = " . strlen($xml) . " -->\n";
     $Status .= "<!-- HTML headers received: " . htmlspecialchars(strip_tags(trim($junk))) . " -->\n";
     $Status .= "<!-- HTML received: " . htmlspecialchars(strip_tags(str_replace('-->','',trim($xml)))) . " -->\n";
     $get_file_failed = 1;
   }
 }
} else {
   $xml = 'invalid zone';
}


  if (!$includeOnly) {    // omit HTML headers if doing inc=Y
    $string .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>RSS Advisory Script</title>
</head>

<body style="background-color:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px;">
';
    if (!$includeOnly and $doSummary) {
      $string .= "<p>\n";
    }    // for XHTML 1.0-Strict validation
  }  // end .. only printed if full html needed


// all output from program goes into $advisory_html
if ($xml == 'invalid zone') {
      $advisory_html = "${string}${Status}<p>Advisory Information Unavailable, invalid advisory zone selected.</p>";
}
else if ($get_file_failed == 1) {
      $advisory_html = "${string}${Status}" . graceful_error();
}
else if ($get_file_failed == 2) {
      $advisory_html = "${string}${Status}<p>Advisory Information Unavailable, cache file conflict, view source for details.</p>";
}
else {
  $advisory_html = advisory_main_code();
}

$advisory_html .= "<!-- zone=$Zone -->\n";

if (!$includeOnly) {
    if (!$includeOnly and $doSummary) {
      $advisory_html .= "</p>\n";
    }
    $advisory_html .= '</body>
</html>
';
}  // end !$includeOnly - only printed if full html wanted (no inc=Y)

if (!$includeOnly || !$noprint) {
  echo $advisory_html;
  $advisory_html = "<!-- Warning: Trying to include \$advisory_html without the noprint=1 parameter -->\n";
}

// ---------------------------- functions -----------------------------------

function advisory_main_code() {
  // mchallis added
  global $Summary, $HD, $hurlURL, $RSS_URL, $timeFormat, $fullMessagesMode, $doLongTitles, $doZoneTitles, $advcount, $enable_alert_icons, $icons_folder;
  global $doSummary, $Zone, $WLink, $cacheName, $includeOnly, $string, $Status, $doLowerCase, $flyer, $flyerpromo;

  $string .= $Status;

  if (!$xml = @ simplexml_load_file($cacheName)) {
    return graceful_error();
  }

  foreach ($xml->entry as $entry) {
    /*
    available 'entry' fields from the xml:
    $entry->id  (url)
    $entry->updated (time) 2009-05-30T15:50:00-08:00
    $entry->title  (alert name)
    $entry->summary  (details)
    $entry->cap_effective (time)  2009-05-30T15:50:00-08:00
    $entry->cap_expires    (time) 2009-05-31T16:00:00-08:00
    $entry->cap_status
    $entry->cap_msgType
    $entry->cap_category
    $entry->cap_urgency
    $entry->cap_severity
    $entry->cap_certanty
    $entry->cap_areaDesc  ( location(s) )
    */

    $title = htmlspecialchars(strip_tags(trim($entry->title)));
    $entry->summary = str_replace('/&//&/', '', $entry->summary);

    if ($enable_alert_icons && !is_file($icons_folder.'/iconkey.png')) {
              $icons_warning = '(Script settings error: Alert icons are enabled but cannot be found by the script. Check the settings for $icons_folder.)<br />';
    } else {
              $icons_warning = '';
    }

    if (preg_match("|There are no active|i", $title)) {
      if ($doZoneTitles) {
              $zone_name = preg_match("/Advisories for (.*)\(.*\)(.*) Issued by/", trim($xml->title), $matches);
              $zone_name = $matches[1].'('.trim($matches[2]).')';
             if (!$doSummary) {
               $string .= '<p>' . $icons_warning.$title . " for $zone_name.</p>";
             }
             // "There are no active watches, warnings or advisories for $Zone"
             $Summary = $icons_warning.$title . " for $zone_name.";
      } else {
             if (!$doSummary) {
               $string .= '<p>' . $icons_warning.$title . " for zone $Zone.</p>";
             }
             // "There are no active watches, warnings or advisories for $Zone"
             $Summary = $icons_warning.$title . " for zone $Zone.";
      }
    }
    else {
      // title with zone name in it.
      if (!$WLink && !$doSummary)
        $string .= "<$HD>" . htmlspecialchars(strip_tags(trim($xml->title))) . "</$HD>\n\n";

      if (preg_match("| issued|", $title)) {
        //echo '|'.$title.'|(issued)'; // debugging
        $title = explode(" issued", $title);
      }
      else {
        //echo '|'.$title.'|(date)'; // debugging
        $title = explode(date("Y"), $title);
      }
      $title = $title[0];

      $alert_icon_arr = find_alert_icon(trim($title));
      if ($enable_alert_icons && !$icons_warning) {
              $this_icon = '<img src="'.$icons_folder.'/'.$alert_icon_arr['icon'].'" alt="' . htmlspecialchars(strip_tags(trim($title))) .'" title="' . htmlspecialchars(strip_tags(trim($title))) .'" /> ';
      } else {
              $this_icon = '';
      }

      // find the zone name: South Washington Coast (Washington)
      // in this string: 'Current Watches, Warnings and Advisories for South Washington Coast (WAZ021) Washington Issued by the National Weather Service';
      if ($doLongTitles) {
        $zone_name = preg_match("/Advisories for (.*)\(.*\)(.*) Issued by/", trim($xml->title), $matches);
        $title .= ' - '.$matches[1].'('.trim($matches[2]).')';
      }
      $WLink++;
      if (!$doSummary) {
       $string .= '<div>&nbsp;</div>
       <div class="column-light" style="width: 630px; border: 1px solid '.$alert_icon_arr['color'].'; margin: 0px auto 6px auto;">
  <a name="WL'.$WLink.'" id="WL'.$WLink.'"></a>
       <table width="615" border="0" cellspacing="0" cellpadding="0" style="margin: 0 auto 10px auto;">
    <tr>
      <td colspan="3">
       <'.$HD.'>'.$icons_warning.$this_icon."<a href=\"" . trim($entry->id) . '" title="Click to view this statement at the NWS" style="color: '. $alert_icon_arr['color'] .'">' . $title . "</a></$HD>\n";
        $updated_date = date($timeFormat, strtotime($entry->updated));
        $string .= "<!-- '$entry->updated '='$updated_date' -->\n";
        $string .= '</td>
    </tr>
    <tr>
      <td colspan="3" style="text-align: center"><hr/></td>
    </tr>
    <tr>
      <td>
    ';
        $string .= "<table cellpadding=\"0\" cellspacing=\"2\">\n";
        $string .= "<tr>\n";
        $string .= "<td><strong>Updated:</strong></td><td>" . $updated_date . "</td>\n";
        $string .= "</tr><tr>\n";
        $string .= "<td><strong>Effective:</strong></td><td>" . date($timeFormat, strtotime($entry->cap_effective)) . "</td>\n";
        $string .= "</tr><tr>\n";
        $string .= "<td><strong>Expires:</strong></td><td>" . date($timeFormat, strtotime($entry->cap_expires)) . "</td>\n";
        $string .= "</tr>\n";
        $string .= "</table>\n";

        $string .= "</td><td>\n";

        $string .= "<table cellpadding=\"0\" cellspacing=\"2\">\n";
        $string .= "<tr>\n";
        $string .= '<td><strong>Severity:</strong></td><td><span style="color:'.$alert_icon_arr['color'].'">' . htmlspecialchars(strip_tags(trim($entry->cap_severity))) . "</span></td>\n";
        $string .= "</tr><tr>\n";
        $string .= "<td><strong>Urgency:</strong></td><td>";
        if (trim($entry->cap_urgency) == 'Immediate') {
          $string .= '<span style="border: solid 1px; color: white; background-color: red;">' . htmlspecialchars(strip_tags(trim($entry->cap_urgency))) . '</span>';
        }
        else {
          $string .= htmlspecialchars(strip_tags(trim($entry->cap_urgency)));
        }
        $string .= "</td>\n";
        $string .= "</tr><tr>\n";
        $string .= "<td><strong>Certainty:</strong></td><td>" . htmlspecialchars(strip_tags(trim($entry->cap_certainty))) . "</td>\n";
        $string .= "</tr>\n";
        $string .= "</table>\n";

        $string .= "</td><td>\n";

        $string .= "<table cellpadding=\"0\" cellspacing=\"2\">\n";
        $string .= "<tr>\n";
        $string .= "<td><strong>Status:</strong></td><td>" .  htmlspecialchars(strip_tags(trim($entry->cap_status))) . "</td>\n";
        $string .= "</tr><tr>\n";
        $string .= "<td><strong>Type:</strong></td><td>" . htmlspecialchars(strip_tags(trim($entry->cap_msgType))) . "</td>\n";
        $string .= "</tr><tr>\n";
        $string .= "<td><strong>Category:</strong></td><td>" . htmlspecialchars(strip_tags(trim($entry->cap_category))). "</td>\n";
        $string .= "</tr>\n";
        $string .= "</table>\n";

      $string .= '</td>
    </tr>
    <tr>
      <td colspan="3" style="text-align: center"><hr/></td>
    </tr>
    <tr>
      <td colspan="3">';
        $string .= "<strong>Areas affected:</strong> " . htmlspecialchars(strip_tags(trim($entry->cap_areaDesc))) . '</td>
    </tr>
    <tr>
      <td colspan="3" style="text-align: center">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" style="background-color: #FFFFDD; padding: 10px 10px 4px 10px; border: 1px solid '.$alert_icon_arr['color'].';">
        ';
        if ($fullMessagesMode) {
          $FMarray = get_full_message(trim($entry->id));
          if (is_array($FMarray)) { // if it came back a string, fall back to $fullMessagesMode off
             $entry->summary = $FMarray['description'];
             if ($FMarray['instruction'] != '') { // instructions are not included in all alerts
               if ($doLowerCase) {
                 // uppercase first word of every sentence
                 $FMinstruction = ucfirst(htmlspecialchars(strip_tags(strtolower($FMarray['instruction']))));
                 $FMinstruction = preg_replace('/([\.!\?]\s+|\A)(\w)/e', '"$1" . strtoupper("$2")', $FMinstruction);
                 $string .= '<strong>Instructions:</strong> ' . $FMinstruction;
               }
               else {
                 $string .= '<strong>Instructions:</strong> ' . trim(htmlspecialchars(strip_tags($FMarray['instruction'])));
               }
               $string .= "<br /><br />\n";
             }
          } // end if is_array
        } // end if $fullMessagesMode
        if ($doLowerCase) {
          // uppercase first word of every sentence
          $FMsummary = trim($entry->summary);
          $FMsummary = ucfirst(htmlspecialchars(strip_tags(strtolower($FMsummary))));
          $FMsummary = preg_replace('/([\.!\?]\s+|\A)(\w)/e', '"$1" . strtoupper("$2")', $FMsummary);
        }
        else {
          $FMsummary = trim($entry->summary);
          $FMsummary = htmlspecialchars(strip_tags($FMsummary));
        }
        // line break corrections
        $FMsummary = preg_replace('/\.\.\.\n/', '...<br />', $FMsummary);
        $FMsummary = preg_replace('/\n\.\.\./', '<br />...', $FMsummary);
        $FMsummary = preg_replace('/\n\*/', '<br />*', $FMsummary);

        $string .= '<strong>Message summary:</strong> ' . $FMsummary;
        if ($FMsummary != 'This alert has expired')
          $string .= "<br />...<a href=\"" . trim($entry->id) . "\">view the complete message</a>\n";
        $string .= "</td>
    </tr>
  </table>
  </div>\n";
      }
      // mchallis modified for flyer usage
      if ($flyer) {
        $Summary .= '<span style="color: red;"><strong>' . htmlspecialchars(strip_tags(trim($title))) . "</strong></span>$flyerpromo<br />\n";
      }
      else {
        $Summary .= "<span style=\"color: red;\">".$this_icon."<a href=\"$hurlURL?zone=$Zone#WL$WLink\"><strong>" . htmlspecialchars(strip_tags(trim($title))) . "</strong></a></span><br />\n";
      }
      $advcount++;
    }
    $title = '';
    $link = '';

  } // end foreach
  if ($doSummary) {
      $string .= $Summary;
      // mchallis modified for flyer usage
      if ($WLink && !$flyer) {
          $string .= "Click on link";
          if ($WLink > 1) {$string .= "s";}
          $string .= " above to see details on the $WLink NOAA advisor";
          if ($WLink > 1) {$string .= "ies"; } else { $string .= "y"; };
          $string .= " for zone $Zone.\n";
      }
  }
  if ($WLink && !$doSummary) {
    $string .= "<p>\n";
    if ($enable_alert_icons) {
      $string .= '<strong>Icon Legend:</strong> <a href="#" onclick="window.open(\''.$icons_folder.'/iconkey.png\', \'\',\'width=572,height=398\')">Icon key</a><br />';
    }
    $string .= "<strong>Source:</strong> <a href=\"$RSS_URL\">NWS Watches, Warnings or Advisories for zone $Zone</a>\n</p>";
  }
  return $string;
} // end function advisory_main_code
// ------------------------------------------------------------------

function get_full_message($url) {
  // mchallis added to get full contents of alert messages in real time
  global $string, $curl_debug;

  if (!function_exists('curl_init')) {  // some do not have CURL
    $string .= "<!-- fetching full contents of alert message from simplexml_load_file -->\n";
    if (!$xml = @ simplexml_load_file($url)) {
      $string .= "<!-- Error reading alert message url from file. -->\n";
      return 'failed';
    }
  }
  else {   // use CURL
    $xml = curl_fetch_file($url,1);
    $string .= "<!-- fetching full contents of alert message with CURL -->\n";
    $string .= $curl_debug;
    if (!$xml = @ simplexml_load_string($xml)) {
      $string .= "<!-- Error reading full contents of alert message with simplexml_load_string. -->\n";
      return 'failed';
    }
  }
  $FMarray = array();
  $FMarray['instruction'] = trim($xml->info->instruction);
  $FMarray['description'] = trim($xml->info->description);
  $FMarray['description'] = str_replace('/&//&/', '', $FMarray['description']);
  //print_r($xml); // for debugging
  return $FMarray;

} // end function get_full_message
// ------------------------------------------------------------------

function find_alert_icon($title){

   // This function searches all alert titles to assign color code, severity, and icon
   // function code by (mchallis) mike challis, inspired by curly's AtomFeed.php
   $a = array();

   $alert_types = array (
   // looking for warning
array('N'=>'Ashfall Warning',              'C'=>'#D00', 'S'=>'2', 'I'=>'EWW.gif'),
array('N'=>'Avalanche Warning',            'C'=>'#D00', 'S'=>'2', 'I'=>'WSW.gif'),
array('N'=>'Blizzard Warning',             'C'=>'#D00', 'S'=>'1', 'I'=>'WSW.gif'),
array('N'=>'Civil Danger Warning',         'C'=>'#D00', 'S'=>'2', 'I'=>'WSW.gif'),
array('N'=>'Coastal Flood Warning',        'C'=>'#D00', 'S'=>'2', 'I'=>'CFW.gif'),
array('N'=>'Dust Storm Warning',           'C'=>'#D00', 'S'=>'2', 'I'=>'EWW.gif'),
array('N'=>'Earthquake Warning',           'C'=>'#D00', 'S'=>'2', 'I'=>'WSW.gif'),
array('N'=>'Extreme Cold Warning',         'C'=>'#D00', 'S'=>'2', 'I'=>'HZW.gif'),
array('N'=>'Excessive Heat Warning',       'C'=>'#D00', 'S'=>'2', 'I'=>'EHW.gif'),
array('N'=>'Extreme Wind Warning',         'C'=>'#D00', 'S'=>'2', 'I'=>'EWW.gif'),
array('N'=>'Fire Warning',                 'C'=>'#D00', 'S'=>'2', 'I'=>'WSW.gif'),
array('N'=>'Flash Flood Warning',          'C'=>'#D00', 'S'=>'2', 'I'=>'FFW.gif'),
array('N'=>'Flood Warning',                'C'=>'#D00', 'S'=>'2', 'I'=>'FFW.gif'),
array('N'=>'Freeze Warning',               'C'=>'#D00', 'S'=>'2', 'I'=>'FZW.gif'),
array('N'=>'Gale Warning',                 'C'=>'#D00', 'S'=>'2', 'I'=>'HWW.gif'),
array('N'=>'Hard Freeze Warning',          'C'=>'#D00', 'S'=>'2', 'I'=>'HZW.gif'),
array('N'=>'Hazardous Materials Warning',  'C'=>'#D00', 'S'=>'2', 'I'=>'WSW.gif'),
array('N'=>'Hazardous Seas Warning',       'C'=>'#D00', 'S'=>'2', 'I'=>'SMW.gif'),
array('N'=>'Heavy Freezing Spray Warning', 'C'=>'#F60', 'S'=>'6', 'I'=>'SWA.gif'),
array('N'=>'High Surf Warning',            'C'=>'#D00', 'S'=>'2', 'I'=>'SMW.gif'),
array('N'=>'High Wind Warning',            'C'=>'#D00', 'S'=>'2', 'I'=>'HWW.gif'),
array('N'=>'Hurricane Force Wind Warning', 'C'=>'#D00', 'S'=>'1', 'I'=>'HUW.gif'),
array('N'=>'Heavy Snow Warning',           'C'=>'#D00', 'S'=>'1', 'I'=>'WSW.gif'),
array('N'=>'Hurricane Warning',            'C'=>'#D00', 'S'=>'1', 'I'=>'HUW.gif'),
array('N'=>'Hurricane Wind Warning',       'C'=>'#D00', 'S'=>'1', 'I'=>'HUW.gif'),
array('N'=>'Ice Storm Warning',            'C'=>'#D00', 'S'=>'2', 'I'=>'ISW.gif'),
array('N'=>'Lake Effect Snow Warning',     'C'=>'#D00', 'S'=>'2', 'I'=>'SMW.gif'),
array('N'=>'Lakeshore Flood Warning',      'C'=>'#D00', 'S'=>'2', 'I'=>'SMW.gif'),
array('N'=>'Law Enforcement Warning',      'C'=>'#D00', 'S'=>'2', 'I'=>'WSA.gif'),
array('N'=>'Nuclear Power Plant Warning',  'C'=>'#D00', 'S'=>'2', 'I'=>'WSW.gif'),
array('N'=>'Radiological Hazard Warning',  'C'=>'#D00', 'S'=>'2', 'I'=>'WSW.gif'),
array('N'=>'Red Flag Warning',             'C'=>'#D00', 'S'=>'2', 'I'=>'FWW.gif'),
array('N'=>'River Flood Warning',          'C'=>'#D00', 'S'=>'2', 'I'=>'FLW.gif'),
array('N'=>'Severe Thunderstorm Warning',  'C'=>'#B11', 'S'=>'1', 'I'=>'SVR.gif'),
array('N'=>'Shelter In Place Warning',     'C'=>'#D00', 'S'=>'2', 'I'=>'WSW.gif'),
array('N'=>'Sleet Warning',                'C'=>'#D00', 'S'=>'2', 'I'=>'IPW.gif'),
array('N'=>'Special Marine Warning',       'C'=>'#D00', 'S'=>'2', 'I'=>'SMW.gif'),
array('N'=>'Tornado Warning',              'C'=>'#A00', 'S'=>'0', 'I'=>'TOR.gif'),
array('N'=>'Tsunami Warning',              'C'=>'#D00', 'S'=>'1', 'I'=>'SMW.gif'),
array('N'=>'Tropical Storm Warning',       'C'=>'#D00', 'S'=>'1', 'I'=>'TRW.gif'),
array('N'=>'Typhoon Warning',              'C'=>'#D00', 'S'=>'2', 'I'=>'WSW.gif'),
array('N'=>'Volcano Warning',              'C'=>'#D00', 'S'=>'2', 'I'=>'WSW.gif'),
array('N'=>'Wind Chill Warning',           'C'=>'#D00', 'S'=>'2', 'I'=>'WCW.gif'),
array('N'=>'Winter Storm Warning',         'C'=>'#D00', 'S'=>'1', 'I'=>'WSW.gif'),
array('N'=>'Winter Weather Warning',       'C'=>'#D00', 'S'=>'1', 'I'=>'WSW.gif'),
array('N'=>'Storm Warning',                'C'=>'#D00', 'S'=>'2', 'I'=>'SVR.gif'),

      // looking for watch
array('N'=>'Avalanche Watch',              'C'=>'#F33', 'S'=>'5', 'I'=>'WSA.gif'),
array('N'=>'Blizzard Watch',               'C'=>'#F33', 'S'=>'5', 'I'=>'WSA.gif'),
array('N'=>'Coastal Flood Watch',          'C'=>'#F33', 'S'=>'5', 'I'=>'CFA.gif'),
array('N'=>'Excessive Heat Watch',         'C'=>'#F33', 'S'=>'5', 'I'=>'EHA.gif'),
array('N'=>'Extreme Cold Watch',           'C'=>'#F33', 'S'=>'5', 'I'=>'HZA.gif'),
array('N'=>'Flash Flood Watch',            'C'=>'#F33', 'S'=>'5', 'I'=>'FFA.gif'),
array('N'=>'Fire Weather Watch',           'C'=>'#F33', 'S'=>'5', 'I'=>'FWA.gif'),
array('N'=>'Flood Watch',                  'C'=>'#F33', 'S'=>'5', 'I'=>'FFA.gif'),
array('N'=>'Freeze Watch',                 'C'=>'#F33', 'S'=>'5', 'I'=>'FZA.gif'),
array('N'=>'Gale Watch',                   'C'=>'#F33', 'S'=>'5', 'I'=>'GLA.gif'),
array('N'=>'Hard Freeze Watch',            'C'=>'#F33', 'S'=>'5', 'I'=>'HZA.gif'),
array('N'=>'Hazardous Seas Watch',         'C'=>'#F33', 'S'=>'5', 'I'=>'SUY.gif'),
array('N'=>'Heavy Freezing Spray Watch',   'C'=>'#F33', 'S'=>'5', 'I'=>'SWA.gif'),
array('N'=>'High Wind Watch',              'C'=>'#F33', 'S'=>'4', 'I'=>'WIY.gif'),
array('N'=>'Hurricane Force Wind Watch',   'C'=>'#F33', 'S'=>'4', 'I'=>'HWW.gif'),
array('N'=>'Hurricane Watch',              'C'=>'#F33', 'S'=>'4', 'I'=>'HUA.gif'),
array('N'=>'Hurricane Wind Watch',         'C'=>'#F33', 'S'=>'4', 'I'=>'HWW.gif'),
array('N'=>'Lake Effect Snow Watch',       'C'=>'#F33', 'S'=>'5', 'I'=>'WSA.gif'),
array('N'=>'Lakeshore Flood Watch',        'C'=>'#F33', 'S'=>'5', 'I'=>'FFA.gif'),
array('N'=>'Severe Thunderstorm Watch',    'C'=>'#F31', 'S'=>'4', 'I'=>'SVA.gif'),
array('N'=>'Tornado Watch',                'C'=>'#F33', 'S'=>'3', 'I'=>'TOA.gif'),
array('N'=>'Tropical Storm Watch',         'C'=>'#F33', 'S'=>'5', 'I'=>'TRA.gif'),
array('N'=>'Tropical Storm Wind Watch',    'C'=>'#F33', 'S'=>'5', 'I'=>'WIY.gif'),
array('N'=>'Tsunami Watch',                'C'=>'#F33', 'S'=>'5', 'I'=>'WSA.gif'),
array('N'=>'Typhoon Watch',                'C'=>'#F33', 'S'=>'4', 'I'=>'HUA.gif'),
array('N'=>'Wind Chill Watch',             'C'=>'#F33', 'S'=>'5', 'I'=>'WCA.gif'),
array('N'=>'Winter Storm Watch',           'C'=>'#F33', 'S'=>'5', 'I'=>'SRA.gif'),
array('N'=>'Winter Weather Watch',         'C'=>'#F33', 'S'=>'5', 'I'=>'WSA.gif'),
array('N'=>'Storm Watch',                  'C'=>'#F33', 'S'=>'5', 'I'=>'SRA.gif'),

       // looking for advisory
array('N'=>'Air Stagnation Advisory',      'C'=>'#F60', 'S'=>'6', 'I'=>'SCY.gif'),
array('N'=>'Ashfall Advisory',             'C'=>'#F60', 'S'=>'6', 'I'=>'WSW.gif'),
array('N'=>'Blowing Dust Advisory',        'C'=>'#F60', 'S'=>'6', 'I'=>'HWW.gif'),
array('N'=>'Blowing Snow Advisory',        'C'=>'#F60', 'S'=>'6', 'I'=>'WSA.gif'),
array('N'=>'Coastal Flood Advisory',       'C'=>'#F60', 'S'=>'6', 'I'=>'FLS.gif'),
array('N'=>'Small Craft Advisory',         'C'=>'#F60', 'S'=>'6', 'I'=>'SCY.gif'),
array('N'=>'Dense Fog Advisory',           'C'=>'#F60', 'S'=>'6', 'I'=>'FGY.gif'),
array('N'=>'Dense Smoke Advisory',         'C'=>'#F60', 'S'=>'6', 'I'=>'SMY.gif'),
array('N'=>'Brisk Wind Advisory',          'C'=>'#F60', 'S'=>'6', 'I'=>'WIY.gif'),
array('N'=>'Flash Flood Advisory',         'C'=>'#F60', 'S'=>'6', 'I'=>'FLS.gif'),
array('N'=>'Flood Advisory',               'C'=>'#F60', 'S'=>'6', 'I'=>'FLS.gif'),
array('N'=>'Freezing Drizzle Advisory',    'C'=>'#F60', 'S'=>'6', 'I'=>'SWA.gif'),
array('N'=>'Freezing Fog Advisory',        'C'=>'#F60', 'S'=>'6', 'I'=>'FZW.gif'),
array('N'=>'Freezing Rain Advisory',       'C'=>'#F60', 'S'=>'6', 'I'=>'SWA.gif'),
array('N'=>'Freezing Spray Advisory',      'C'=>'#F60', 'S'=>'6', 'I'=>'SWA.gif'),
array('N'=>'Frost Advisory',               'C'=>'#F60', 'S'=>'6', 'I'=>'FRY.gif'),
array('N'=>'Heat Advisory',                'C'=>'#F60', 'S'=>'6', 'I'=>'HTY.gif'),
array('N'=>'High Surf Advisory',           'C'=>'#F60', 'S'=>'6', 'I'=>'SUY.gif'),
array('N'=>'Hydrologic Advisory',          'C'=>'#F60', 'S'=>'6', 'I'=>'FLS.gif'),
array('N'=>'Lake Effect Snow Advisory',    'C'=>'#F60', 'S'=>'6', 'I'=>'WSA.gif'),
array('N'=>'Lake Effect Snow and Blowing Snow Advisory', 'C'=>'#F60', 'S'=>'6', 'I'=>'WSA.gif'),
array('N'=>'Lake Wind Advisory',           'C'=>'#F60', 'S'=>'6', 'I'=>'LWY.gif'),
array('N'=>'Lakeshore Flood Advisory',     'C'=>'#F60', 'S'=>'6', 'I'=>'FLS.gif'),
array('N'=>'Low Water Advisory',           'C'=>'#F60', 'S'=>'6', 'I'=>'FFA.gif'),
array('N'=>'Sleet Advisory',               'C'=>'#F60', 'S'=>'6', 'I'=>'SWA.gif'),
array('N'=>'Snow Advisory',                'C'=>'#F60', 'S'=>'6', 'I'=>'WSA.gif'),
array('N'=>'Snow and Blowing Snow Advisory', 'C'=>'#F60', 'S'=>'6', 'I'=>'WSA.gif'),
array('N'=>'Tsunami Advisory',             'C'=>'#F60', 'S'=>'6', 'I'=>'SWA.gif'),
array('N'=>'Wind Advisory',                'C'=>'#F60', 'S'=>'6', 'I'=>'WIY.gif'),
array('N'=>'Wind Chill Advisory',          'C'=>'#F60', 'S'=>'6', 'I'=>'WCY.gif'),
array('N'=>'Winter Weather Advisory',      'C'=>'#F60', 'S'=>'6', 'I'=>'WWY.gif'),

     // looking for statement
array('N'=>'Coastal Flood Statement',      'C'=>'#C70', 'S'=>'7', 'I'=>'FFS.gif'),
array('N'=>'Flash Flood Statement',        'C'=>'#C70', 'S'=>'7', 'I'=>'FFS.gif'),
array('N'=>'Flood Statement',              'C'=>'#C70', 'S'=>'7', 'I'=>'FFS.gif'),
array('N'=>'Hurricane Statement',          'C'=>'#C70', 'S'=>'7', 'I'=>'HUA.gif'),
array('N'=>'Lakeshore Flood Statement',    'C'=>'#C70', 'S'=>'7', 'I'=>'FFS.gif'),
array('N'=>'Marine Weather Statement',     'C'=>'#C70', 'S'=>'7', 'I'=>'MWS.gif'),
array('N'=>'Public Information Statement', 'C'=>'#C70', 'S'=>'7', 'I'=>'PNS.gif'),
array('N'=>'River Flood Statement',        'C'=>'#C70', 'S'=>'7', 'I'=>'FLS.gif'),
array('N'=>'River Statement',              'C'=>'#C70', 'S'=>'7', 'I'=>'RVS.gif'),
array('N'=>'Severe Weather Statement',     'C'=>'#F33', 'S'=>'7', 'I'=>'SVS.gif'),
array('N'=>'Special Weather Statement',    'C'=>'#C70', 'S'=>'7', 'I'=>'SPS.gif'),
array('N'=>'Tropical Statement',           'C'=>'#C70', 'S'=>'7', 'I'=>'HLS.gif'),
array('N'=>'Typhoon Statement',            'C'=>'#C70', 'S'=>'7', 'I'=>'TRA.gif'),

       // looking for other misc. things
array('N'=>'Air Quality Alert',            'C'=>'#06C', 'S'=>'8',  'I'=>'SPS.gif'),
array('N'=>'Significant Weather Alert',    'C'=>'#F33', 'S'=>'4',  'I'=>'SWA.gif'),
array('N'=>'Child Abduction Emergency',    'C'=>'#093', 'S'=>'10', 'I'=>'SPS.gif'),
array('N'=>'Civil Emergency Message',      'C'=>'#093', 'S'=>'4',  'I'=>'SPS.gif'),
array('N'=>'Local Area Emergency',         'C'=>'#093', 'S'=>'4',  'I'=>'SPS.gif'),
array('N'=>'Extreme Fire Danger',          'C'=>'#D00', 'S'=>'2',  'I'=>'WSW.gif'),
array('N'=>'Coastal Hazard',               'C'=>'#C70', 'S'=>'7',  'I'=>'CFS.gif'),
array('N'=>'Short Term',                   'C'=>'#093', 'S'=>'9',  'I'=>'NOW.gif'),
array('N'=>'911 Telephone Outage',         'C'=>'#36C', 'S'=>'11', 'I'=>'SPS.gif'),
array('N'=>'Evacuation Immediate',         'C'=>'EA00', 'S'=>'2',  'I'=>'SVW.gif'),

     // looking for no active
array('N'=>'no active', 'C'=>'#333', 'S'=>'', 'I'=>''),
       );

   // loop through the $alert_types array now
   foreach ($alert_types as $a_type)  {
          if(strpos($title,$a_type['N']) !== false){
                 $a['color']    = $a_type['C'];
                 $a['severity'] = $a_type['S'];
                 $a['icon']     = $a_type['I'];
                 return $a; // found it so just return out of this function now
          }
   }

   // still did not find anything....

   // assign a default for each alert type
   if (strpos($title,"Warning") !== false) {
             $a['color']    = "#D11";
             $a['severity'] = 2;
             $a['icon']     = 'SVW.gif';
             return $a; // found it so just return now
   }
   if (strpos($title,"Watch") !== false) {
             $a['color']    = "#F30";
             $a['severity'] = 4;
             $a['icon']     = 'SWA.gif';
             return $a; // found it so just return now
   }
   if (strpos($title,"Advisory") !== false) {
             $a['color']    = "#F60";
             $a['severity'] = 6;
             $a['icon']     = 'SWA.gif';
             return $a; // found it so just return now
   }
   if (strpos($title,"Statement") !== false) {
             $a['color']    = "#C70";
             $a['severity'] = 7;
             $a['icon']     = 'SWA.gif';
             return $a; // found it so just return now
   }
   if (strpos($title,"Air") !== false) {
             $a['color']    = "#06C";
             $a['severity'] = 8;
             $a['icon']     = 'SPS.gif';
             return $a; // found it so just return now
   }
   if (strpos($title,"Short") !== false) {
             $a['color']    = "#093";
             $a['severity'] = 9;
             $a['icon']     = 'NOW.gif';
             return $a; // found it so just return now
   }
   if (strpos($title,"Emergency") !== false) {
             $a['color']    = "#093";
             $a['severity'] = 4;
             $a['icon']     = 'SPS.gif';
             return $a; // found it so just return now
   }
   if (strpos($title,"Outage") !== false) {
             $a['color']    = "#36C";
             $a['severity'] = 11;
             $a['icon']     = 'SPS.gif';
             return $a; // found it so just return now
   }
   if (strpos($title,"no active") !== false) {
             $a['color']    = "#333";
             $a['severity'] = 14;
             $a['icon']     = ''; // this might need a 'no active' icon?
   }

   // wow, all the way down here and did not match any alerts...
   // well then here is some kind of default for nothing found.
   $a['color'] = "#333";
   $a['severity'] = 14;
   $a['icon'] = 'SPS.gif';

   return $a;

} // end function find_alert_icon
// ------------------------------------------------------------------

function GrabURLWithoutHangingRA($url) {
  // get contents from one URL and return as string
  // thanks to Tom at Carterlake.org for this script fragment
  global $Status;
  $UA = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 (.NET CLR 3.5.30729)';
  // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed
  $numberOfSeconds = 4;

  // Suppress error reporting so Web site visitors are unaware if the feed fails
  error_reporting(0);

  // Extract resource path and domain from URL ready for fsockopen
  $FullUrl = $url;
  $url = str_replace("http://", '', $url);
  $urlComponents = explode("/", $url);
  $domain = $urlComponents[0];
  $resourcePath = str_replace($domain, '', $url);
  $Status .= "<!-- GET $resourcePath HTTP/1.1 \n  Host: $domain -->\n";
  $time_start = microtime_float();

  // Establish a connection
  $socketConnection = fsockopen($domain, 80, $errno, $errstr, $numberOfSeconds);

  if (!$socketConnection) {
    // You may wish to remove the following debugging line on a live Web site
    $Status .= "<!-- Network error: $errstr ($errno) -->\n";
  }
  else {
    $xml = '';
    fputs($socketConnection, "GET $resourcePath HTTP/1.1\r\nHost: $domain\r\nUser-agent: $UA\r\nConnection: close\r\n\r\n");

    // Loop until end of file
    while (!feof($socketConnection)) {
      $xml .= fgets($socketConnection, 4096);
    }
    fclose($socketConnection);

  }  // end else
  $time_stop = microtime_float();
  $total_time = '';
  $total_time += ($time_stop - $time_start);
  $time_fetch = sprintf("%01.3f", round($time_stop - $time_start, 3));
  $Status .= "<!-- Time to fetch: $time_fetch sec -->\n";

  return ($xml);

} // end function GrabURLWithoutHangingRA
// ------------------------------------------------------------------

function curl_fetch_file($url, $fullMessages = 0) {
  // mchallis added get contents from one URL using a CURL call and return as string
  global $curl_debug, $fullMessagesMode;

  $curlurl = str_replace("http://", '', $url);
  $urlComponents = explode("/", $curlurl);
  $domain = $urlComponents[0];
  $resourcePath = str_replace($domain, '', $curlurl);
  $curl_debug = "<!-- CURL GET $resourcePath HTTP/1.1 Host: $domain -->\n";
  $time_start = microtime_float();

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 (.NET CLR 3.5.30729)');
  curl_setopt($ch, CURLOPT_URL, $url);
  if ($fullMessages == 1) {   // use a different timeout while getting $fullMessages
         curl_setopt($ch, CURLOPT_TIMEOUT, 3);  // 3 sec timeout
  } else {
         curl_setopt($ch, CURLOPT_TIMEOUT, 15);  // 15 sec timeout
  }
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // write the response to a variable

  $data = curl_exec($ch);
  if ($data === false) {
      // could not connect
      $curl_debug .= '<!-- CURL Network error: ' . curl_error($ch) . " -->\n";
      $fullMessagesMode = false; // turn off $fullMessagesMode to shorten timeouts
  }
  curl_close($ch);
  $time_stop = microtime_float();
  $total_time = '';
  $total_time += ($time_stop - $time_start);
  $time_fetch = sprintf("%01.3f", round($time_stop - $time_start, 3));
  $curl_debug .= "<!-- Time to fetch: $time_fetch sec -->\n";

  //echo $data;
  return $data;

} // end function curl_fetch_file
// ------------------------------------------------------------------

function microtime_float() {
  // set a timer
  list($usec, $sec) = explode(" ", microtime());
  return ((float) $usec + (float) $sec);
} // end function microtime_float
// ------------------------------------------------------------------

function graceful_error() {
  // mchallis added graceful error function so your site stays with valid HTML after soft errors
  global $RSS_URL;
  $returnstring = "<p>Advisory Information Unavailable, error fetching or reading data from the <a href=\"$RSS_URL\">NOAA advisories server.</a></p>\n";

  return $returnstring;
} // end function graceful_error

// ------------------------------------------------------------------

?>
