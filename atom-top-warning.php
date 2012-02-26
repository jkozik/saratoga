<?php
/*
ATOM/CAP Top Warning PHP script modified by Ken True, webmaster@saratoga-weather.org
 script available at http://saratoga-weather.org/scripts.php
 
 rss-top-warning.php V1.00 by Ken True
 atom-top-warning.php V2.00 by Mike Challis

This script modifies Tom Chaplin's advisory script to use/share Ken True's atom-advisory cache file.
Place the css info at the end of this file with the css for the page where you're using it (or external css file).
Code based on 'warnscroll.php' by Jim McMurry - jmcmurry@mwt.net - jcweather.us October 2007

You are free to use and modify the code

This php code provided "as is", and Ken True, Mike Challis
disclaims any and all warranties, whether express or implied, including
(without limitation) any implied warranties of merchantability or
fitness for a particular purpose.

Version 1.00 - 07-Nov-2007 - Initial release (with thanks to Jim of jcweather.us )
Version 1.01 - 04-Jan-2008 - added support for Carterlake/AJAX/PHP template set
Version 1.02 - 17-Mar-2008 - modified cache error handling for more infomation
Version 1.03 - 25-Mar-2008 - corrected HTML comment error codes
Version 1.04 - 06-Apr-2008 - corrected HTML comment error code (MChallis)
Version 2.00 - 07-Jun-2009 - beta version specific to new beta NWS feeds
                             http://www.weather.gov/alerts-beta/
                             modded by Mike Challis - http://www.642weather.com/weather/scripts.php
                             (not backwards compatible with old feeds, requires PHP5)
                             see: http://www.weather-watch.com/smf/index.php/topic,40256.0.html
Version 2.00 - 31-Aug-2009   No longer BETA
Version 2.01 - 01-Sep-2009   Fixed blank output when Air Quality Alert, improved top alert selection
Version 2.02 - 21-Sep-2009   Renamed at atom-top-warning.php to avoid confusion and allow coexistance with
                             rss-top-warning.php which used the RSS feeds instead of the ATOM/CAP feeds.
Version 2.03 - 22-Sep-2009   Changed to HTTP/1.1 requests
If it cannot figure out which alert is the top most alert, it will select the 1st one.
Version 2.04 - 29-Oct-2009   Added support for County zone (M Challis)
version 2.05 - 31-Oct-2009   Corrected HTML generated for error condition.
Version 2.06 - 26-Jan-2011   Added support for $cacheFileDir global cache directory
Version 2.07 - 15-Mar-2011   Changed URL for NWS ATOM/CAP feeds
Version 2.08 - 05-Nov-2011   fixes for "* Storm Warning" icon selection

*/
$Version = 'atom-top-warning.php - V2.08 05-Nov-2011';
/*

This script gets the current ATOM/CAP Zone Watch/Warning/Advisory/Statement
from www.weather.gov/beta-alerts/ for the selected Zone and provides
(titles only, with links to details).

Output: creates XHTML 1.0-Strict HTML code (default)

      zone=ssZnnn     -- select the Zone to use.  Pick the Zone from the
                          RSS feed list at http://www.weather.gov/alerts/
                         and use the RSS County/Zone for your area.
     cache=refresh   -- reload the cache

Usage:
 This script is designed to work with the rss-advisory.php XML RSS
  parser script to display the details of the advisory/warning
  include this script in an existing page using:
  no parms:    include("atom-top-warning.php");
  parms:    include("http://your.website/atom-top-warning.php?zone=CAZ513");


//------------------------------------------------------------------
// begin settings
//------------------------------------------------------------------
*/

//  change $myDefaultZone default(below) to your station's county zone
//  other settings are outlined below, and are optional
//$myDefaultZone = 'CAZ513'; // change this to your zone
$myDefaultZone = 'ILZ013'; // change this to your zone

$hurlURL = 'wxadvisory.php'; // change this default to your webpage to open for details

$doLongTitles = true; // change to true to add the zone name to the Alert Title (new in ver. 2.00)
// Heat Advisory
// Heat Advisory - South Washington Coast (Washington)

// change to true to suppress "There are no active watches, warnings or advisories ..." (new in ver. 2.00)
$doSilentNoActive = false;

// cacheFileDir and cacheName is name of file used to store cached current conditions
$cacheFileDir = './';   // default cache file directory

$cacheName = 'atom-advisory.txt'; // used to store the file so we don't have to fetch it each time
// note: the real cache name will automatically be the above name with the Zone included
// ie:  rss-advisory.txt => rss-advisory-CAZ513.txt

// cache interval time
$refetchSeconds = 600; // refetch every nnnn seconds (600=10 minutes)

// Alert icons (new in version V2.00 20-Jul-2009 BETA .009)
// Each alert will display along with an icon image to indicate the type and severity of the alert.
// To use this feature, you must download the icons from here:
// www.642weather.com/weather/scripts/noaa-advisory-images.zip
$enable_alert_icons = true; // this feature is optional

// Alert icons directory. Only needed if Alert icons are enabled.
// This setting is so you can name the imnmages folder to what ever you like.
$icons_folder = './alert-images'; // No slash on end

//------------------------------------------------------------------
// end settings. Do not alter any code below this point in the script or it may not run properly.
//------------------------------------------------------------------

if (phpversion() < 5) {
  echo 'Failure: This ATOM/CAP Top Warning Script requires PHP version 5 or greater. You only have PHP version: ' . phpversion();
  exit;
}
// overrides from Settings.php if available
global $SITE;
if (isset($SITE['noaazone'])) 	{$DefaultZone = $SITE['noaazone'];}
if (isset($SITE['hurlURL'])) 	{$hurlURL = $SITE['hurlURL'];}
if (isset($SITE['timeFormat'])) {$timeFormat = $SITE['timeFormat'];}
if (isset($SITE['tz'])) 		{$ourTZ = $SITE['tz'];}
if(isset($SITE['cacheFileDir']))     {$cacheFileDir = $SITE['cacheFileDir']; }
// end of overrides from Settings.php if available

// initialize vars
global $Status;
$Summary = '';
$string = '';
$Status = '';
$curl_debug = '';
$xml = '';
$WLink = 0;
$get_file_failed = 0;

if (!isset ($PHP_SELF)) {
  $PHP_SELF = $_SERVER['PHP_SELF'];
}// needed for some PHP installations

if (!isset ($DefaultZone)) {
  $DefaultZone = $myDefaultZone;
}
if (!isset ($hurlURL)) {
  $hurlURL = $PHP_SELF;
}
if (isset ($SITE['hurlURL'])) {
  $hurlURL = $SITE['hurlURL'];
}
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

if (isset ($_GET['detailpage']) ) {
  $detailpage = $_GET['detailpage'];
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

$Status = "<!-- $Version -->\n";

$html = '';

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
     $xml = GrabURLWithoutHangingTW($RSS_URL);
   }
   else {
     $xml = curl_fetch_file($RSS_URL,0);
   }
   $Status .= $curl_debug;
   $curl_debug = '';

   // mchallis added to format cap code tags
   $xml = str_replace("cap:", 'cap_', $xml);

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

// all output from program goes into $advisory_html
if ($xml == 'invalid zone') {
      $advisory_html = "${string}${Status}<div class=\"advisoryBoxnoactive\">Top Warning Information Unavailable, invalid advisory zone selected.</div>\n";
}
else if ($get_file_failed == 1) {
      $advisory_html = "${string}${Status}" . graceful_error();
}
else if ($get_file_failed == 2) {
      $advisory_html = "${string}${Status}<div class=\"advisoryBoxnoactive\">Top Warning Information Unavailable, cache file conflict, view source for details.</div>";
}
else {
  $advisory_html = advisory_main_code();
}

$advisory_html .= "<!-- zone=$Zone -->\n";

echo "$advisory_html";

// ---------------------------- functions -----------------------------------

function advisory_main_code() {
  // mchallis added, find the top warning
  global $Summary, $HD, $hurlURL, $RSS_URL, $doLongTitles;
  global $Zone, $WLink, $cacheName, $string, $Status, $enable_alert_icons, $icons_folder;

  $string .= $Status;

  if (!$xml = @ simplexml_load_file($cacheName)) {
    return graceful_error();
  }

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

  // evacuation immediate
  title_search($xml, 'evacuation');

  if ($WLink == 0) {
    // tornado
    title_search($xml, 'tornado');
  }
  if ($WLink == 0) {
    // warning
    title_search($xml, 'warning');
  }
  if ($WLink == 0) {
    // Watch
    title_search($xml, 'watch');
  }
  if ($WLink == 0) {
    // Advisory
    title_search($xml, 'advisory');
  }
  if ($WLink == 0) {
    // Extreme Fire Danger
    title_search($xml, 'fire');
  }
  if ($WLink == 0) {
    // Statement
     title_search($xml, 'statement');
  }
  if ($WLink == 0) {
    //
     title_search($xml, 'emergency');
  }
   if ($WLink == 0) {
    //
     title_search($xml, 'hazard');
  }
  if ($WLink == 0) {
    //
     title_search($xml, 'alert');
  }
  if ($WLink == 0) {
    //
     title_search($xml, 'outage');
  }
  if ($WLink == 0) {
    // Hazardous Weather Outlook
    title_search($xml, 'outlook');
  }
  if ($WLink == 0) {
    // Air Quality Alert
     title_search($xml, 'air');
  }
  if ($WLink == 0) {
    // Forecast
     title_search($xml, 'forecast');
  }

  // Error: could not find the top active warning, picking 1st one...
  if ($WLink == 0 && !preg_match("|There are no active|i", $xml->entry[0]->title)) {
      $title = htmlspecialchars(strip_tags(trim($xml->entry[0]->title)));
      if (preg_match("| issued|", $title)) {
          //echo '|'.$title.'|(issued)'; // debugging
          $title = explode(" issued", $title);
        }
        else {
          //echo '|'.$title.'|(date)'; // debugging
          $title = explode(date("Y"), $title);
        }
        $title = $title[0];
       // "There are no active watches, warnings or advisories for $Zone"

          if ($enable_alert_icons && !$icons_warning) {
              $alert_icon_arr = find_alert_icon(trim($title));
              $this_icon = '<img src="'.$icons_folder.'/'.$alert_icon_arr['icon'].'" alt="' . $title .'" title="' . $title .'" /> ';
          } else {
              $this_icon = '';
          }
          if ($doLongTitles) {
            $zone_name = preg_match("/Advisories for (.*)\(.*\)(.*) Issued by/", trim($xml->title), $matches);
            $title .= ' - '.$matches[1].'('.trim($matches[2]).')';
          }
          $title = trim($title);
          $Summary = "<div class=\"warningBox\">Error: could not find the top active warning for zone $Zone, picking 1st one...<br />
           ".$icons_warning.$this_icon." <a href=\"" . $hurlURL."?zone=$Zone#WL1" . '">' . $title .' In Effect  ... [Click here for more]</a></div>'."\n";
       $WLink++;
  }
  $string .= $Summary;

  return $string;
} // end function advisory_main_code
// ------------------------------------------------------------------

function title_search($xml, $keyword) {
    // mchallis added, find the top warning
  global $Summary, $HD, $hurlURL, $RSS_URL, $doLongTitles, $doSilentNoActive;
  global $Zone, $WLink, $cacheName, $string, $Status, $enable_alert_icons, $icons_folder;

    if ($enable_alert_icons && !is_file($icons_folder.'/iconkey.png')) {
              $icons_warning = '(Script settings error: Alert icons are enabled but cannot be found by the script. Check the settings for $icons_folder.)<br />';
    } else {
              $icons_warning = '';
    }


    foreach ($xml->entry as $entry) {
    $title = htmlspecialchars(strip_tags(trim($entry->title)));
    if (preg_match("|There are no active|i", $title)) {
       // "There are no active watches, warnings or advisories for $Zone"
       if (!$doSilentNoActive) { // setting to supress the no active message.
          $Summary = '<div class="advisoryBoxnoactive">'.$icons_warning.$title . " for zone $Zone.</div>\n";
       } else {
          $string .= '<!-- '.$title . " for zone $Zone. -->\n";
       }
       $WLink++;
       return;
    } else {
        if (preg_match("| issued|", $title)) {
          //echo '|'.$title.'|(issued)'; // debugging
          $title = explode(" issued", $title);
        }
        else {
          //echo '|'.$title.'|(date)'; // debugging
          $title = explode(date("Y"), $title);
        }
        $title = $title[0];

        if ($enable_alert_icons && !$icons_warning) {
              $alert_icon_arr = find_alert_icon(trim($title));
              $this_icon = '<img src="'.$icons_folder.'/'.$alert_icon_arr['icon'].'" alt="' . $title .'" title="' . $title .'" /> ';
        } else {
              $this_icon = '';
        }

        // find the zone name: South Washington Coast (Washington)
        // in this string: 'Current Watches, Warnings and Advisories for South Washington Coast (WAZ021) Washington Issued by the National Weather Service';
        if ($doLongTitles) {
          $zone_name = preg_match("/Advisories for (.*)\(.*\)(.*) Issued by/", trim($xml->title), $matches);
          $title .= ' - '.$matches[1].'('.trim($matches[2]).')';
        }
        $title = trim($title);
        if ($keyword == 'evacuation') { //0,1,2
          if (preg_match("/Evacuation Immediate/i", $title) and !preg_match("/National/i", $title)){
		    $Summary = '<div class="tornadowarningBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '"><strong><span style="text-transform: uppercase;">' . $title .'</span></strong> In Effect  ... [Click here for more]</a></div>';
		    $WLink++;
		    break;
		  }
        } else
        if ($keyword == 'tornado') {  //0
          if (preg_match("/Tornado Warning/i", $title) and !preg_match("/National/i", $title)){
		    $Summary = '<div class="tornadowarningBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '"><strong><span style="text-transform: uppercase;">' . $title .'</span></strong> In Effect  ... [Click here for more]</a></div>';
		    $WLink++;
		    break;
		  }
        } else
        if ($keyword == 'warning') { //0,1,2
          if (preg_match("/Warning/i", $title) and !preg_match("/National/i", $title) and !preg_match("/There are no/i", trim($title)) ){
		    $Summary = '<div class="warningBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		}
        } else
        if ($keyword == 'watch') {  //4,5
          if (preg_match("/Watch/i", $title) and !preg_match("/National/i", $title) ){
		    $Summary = '<div class="watchBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		}
        } else
        if ($keyword == 'advisory') { //6
          if (preg_match("/Advisory/i", $title) and !preg_match("/National/i", $title) ){
		    $Summary = '<div class="advisoryBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		}
        } else
        if ($keyword == 'fire') {  //2
          if (preg_match("/Fire Danger/i", $title) and !preg_match("/National/i", $title) ){
		    $Summary = '<div class="advisoryBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		}
        } else
        if ($keyword == 'statement') { //7
         if (preg_match("/Statement/i", $title) and !preg_match("/National/i", $title) ){
		    $Summary = '<div class="advisoryBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		 }
        } else
        if ($keyword == 'emergency') { //4
         if (preg_match("/Emergency/i", $title) and !preg_match("/National/i", $title) ){
		    $Summary = '<div class="advisoryBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		 }
        } else
        if ($keyword == 'hazard') { //4
         if (preg_match("/Hazard/i", $title) and !preg_match("/National/i", $title) ){
		    $Summary = '<div class="advisoryBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		 }
        } else
        if ($keyword == 'alert') { //4
         if (preg_match("/Alert/i", $title) and !preg_match("/National/i", $title) ){
		    $Summary = '<div class="advisoryBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		 }
        } else
        if ($keyword == 'outage') { //11
         if (preg_match("/Outage/i", $title) and !preg_match("/National/i", $title) ){
		    $Summary = '<div class="advisoryBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		 }
        } else
        if ($keyword == 'outlook') {  //9
          if (preg_match("/Outlook/i", $title) and !preg_match("/National/i", $title) ){
		    $Summary = '<div class="advisoryBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		  }
       } else
       if ($keyword == 'air') {  //9
          if (preg_match("/Air/i", $title) and !preg_match("/National/i", $title) ){
		    $Summary = '<div class="advisoryBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		  }
       } else
        if ($keyword == 'forecast') { //9
          if (preg_match("/Forecast/i", $title) and !preg_match("/National/i", $title) ){
		    $Summary = '<div class="advisoryBox">'.$icons_warning.$this_icon.'<a href="' . $hurlURL."?zone=$Zone#WL$WLink" . '">' . $title .' In Effect  ... [Click here for more]</a></div>';
			$WLink++;
			break;
		  }
        }
     }
     $title = '';
   } // end foreach

   return;
} // end function title_search

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
array('N'=>'Heavy Freezing Spray Warning', 'C'=>'#F60', 'S'=>'6', 'I'=>'SWA.gif'),
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

function GrabURLWithoutHangingTW($url) {
  // get contents from one URL and return as string
  // thanks to Tom at Carterlake.org for this script fragment
  global $Status;
  $UA = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)';
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

} // end function GrabURLWithoutHangingTW
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
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)');
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
  $returnstring = "<div class=\"advisoryBoxnoactive\">Top Warning Information Unavailable, error fetching or reading data from the <a href=\"$RSS_URL\">NOAA advisories server.</a></div>\n";

  return $returnstring;
} // end function graceful_error

// ------------------------------------------------------------------


// The following to be placed in your css file.  Change colors etc to suit.
/*  ---------- start if CSS (one line below this)-----------------
.advisoryBoxnoactive {
  color: black;
  font-size: 12px;
  text-align: center;
  background-color: white;
  margin: 0 0 0 0;
  padding: .5em 0em .5em 0em;
  border: 1px dashed rgb(34,70,79);
}
.advisoryBox {
  color: black;
  font-size: 12px;
  text-align: center;
  background-color: #FFFF85;
  margin: 0 0 0 0;
  padding: .5em 0em .5em 0em;
  border: 0px dashed rgb(34,70,79);
}
.watchBox {
  color: black;
  font-size: 12px;
  text-align: center;
  background-color: #B3F7FF;
  margin: 0 0 0 0;
  padding: .5em 0em .5em 0em;
  border: 0px dashed rgb(34,70,79);
}
.warningBox {
  color: white;
  font-size: 13px;
  text-align: center;
  background-color: #E28080;
  margin: 0 0 0 0;
  padding: .5em 0em .5em 0em;
  border: 0px dashed rgb(255,255,255);
}
.warningBox a {
  color: white;
}
.tornadowarningBox {
  color: white;
  font-size: 13px;
  text-align: center;
  background-color: #CC0000;
  margin: 0 0 0 0;
  padding: .5em 0em .5em 0em;
  border: 1px dashed rgb(255,255,255);
}
.tornadowarningBox a {
  color: white;
}
-----end of CSS (one line above this)----------------------------- */
?>
