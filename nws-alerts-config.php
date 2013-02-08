<?php 
/*################################

 NWS Public Weather Alerts

*/################################

// ZONE CODES & COUNTY CODES CAN BE FOUND AT http://alerts.weather.gov/
// LOCATION|CODE ARRAY
// Note: this will be overridden by the Settings.php $SITE['NWSalertsCodes'] entry 
//   when using the Saratoga Base-USA template set

$myZC = array(
  //"Santa Clara Valley|CAZ513|CAC085",
  "Naperville|ILZ013|ILC043"
//  "Santa Cruz Mtns|CAZ512|CAC081|CAC085|CAC087",
  //"Santa Cruz|CAZ529|CAC087",
//  "Monterey|CAZ530|CAC053",
//  "South/East Bay|CAZ508|CAC081",
//  "San Mateo Coast|CAZ509|CAC081",
//  "San Francisco|CAZ006|CAC075"
);

// MAIN SETTINGS
// folders
$cacheFileDir  = './cache/';                // default cache file directory
$icons_folder  = './alert-images';          // folder that contains the icons. No slash on end
// file names
$cacheFileName = 'nws-alertsMainData.php';  // main data cache file name
$aboxFileName  = 'nws-alertsBoxData.php';   // alert box cache file name
$iconFileName  = 'nws-alertsIconData.php';  // big icons cache file
$alertURL      = 'wxnws-details.php';       // web page file name for complete details
//$alertURL      = 'nws-details.php';       // web page file name for complete details
$summaryURL    = 'wxadvisory.php';          // web page for the alert summary
//$summaryURL    = 'nws-summary.php';       // web page for the alert summary

// general settings
$ourTZ         = 'America/Chicago';        // Time Zone     http://www.php.net/manual/en/timezones.america.php
$noCron        = true;                     // true=not using cron, update data when cache file expires   false=use cron to update data
$updateTime    = 600;                       // IF $noCron=true - time span in seconds to retain cache file before updating
$logAlerts     = true;

// ALERT BOX SETTINGS
$useAlertBox   = true;                      // true=use alert box & write data file   false= not using alert box & don't write file
$titleNewline  = true;                      // true=new line for each title   false=string titles with other titles
$aBox_Width    = '99%';                     // width of box  examples - $aBox_Width = '80%';  $aBox_Width = '850px';  or smallest box  $aBox_Width = '';
$centerText    = false;                     // true=center text in alert box    false=left align text
$showNone      = false;                     // true=show 'NONE' if no alerts in alert box   false=don't show alert box if no alerts
$locSort       = 1;                         // location name sort - use number listed below
//                                             0 = sort location as listed in $myZC array
//                                             1 = sort location alphabetically

$sortbyEvent   = 0;                         // sort titles by severity in alert box & then by number listed below
//                                             0 = location - duplicate events will be displayed
//                                             1 = location - duplicate events removed
//                                             2 = event - duplicate events will be displayed
//                                             3 = event - duplicate events removed


// BIG ICONS
$iconLimit     = 0;                         // the number of icons to display  0=show all
$addNone       = false;                     // true=add NONE foreach location with no alerts        false= don't show any NONE
$shoNone       = true;                      // true=show one 'NONE' if no alerts for all location   false=don't show one 'NONE' if no alerts for all location
$useIcons      = 3;                         // select number below
//                                             0 = don't use icons - the cache file will not be written
//                                             1 = sort by alert - duplicate events will be displayed
//                                             2 = sort by alert - duplicate events removed
//                                             3 = single top alert icon for each location
//                                             4 = sort by location - duplicate removed
//                                             5 = sort by location - duplicate events will be displayed


// XML PAGE
$useXML   = false;                           // true=create XML RSS feed   false=not using RSS feed
$rssTitle = 'Naperville Area Weather Alerts'; // title for the RSS/XML page 


// GOOGLE MAP
$zoomLevel = '7';                           // default zoom level
$showClouds = true;                         // true=show clouds in the google map at zoom level6 or less   false=don't show the cloud overlay
$mapStyle= 3;                               // google map style
                                            //  1 = ROADMAP displays the normal, default 2D tiles of Google Maps.
                                            //  2 = SATELLITE displays photographic tiles.
                                            //  3 = HYBRID displays a mix of photographic tiles and a tile layer for prominent features (roads, city names).
                                            //  4 = TERRAIN displays physical relief tiles for displaying elevation and water features (mountains, rivers, etc.).

###  END OF MAIN SETTINGS   ###






// self downloader code
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
?>
