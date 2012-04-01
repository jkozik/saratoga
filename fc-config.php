<?php
// By Jim McMurry - jmcmurry@mwt.net - jcweather.us
// Version 3.4  12-21-11
// See fc-readme.txt for implementation details
//
// Important!  All file paths are in relation to the web root folder.  See Below.
//
// Setup
$debug       = false;                           // True allows multiple forecasts on the same day for testing purposes
$MSserver    = false;                           // Will provide a backslash instead of a forward one for those who need it.

// Date & Time
date_default_timezone_set("America/Chicago");   // Zones available at http://us2.php.net/manual/en/timezones.php
$datestr     = "m-d-y";                         // See http://us.php.net/date for config options
$timestr     = "g:i a";

// Files                                        No leading slash on any of the files or folders!!
//$fileLoc     = "fcreal/";                              // Location of these forecast files.  "" if in the web root folder, otherwise something like "forecast/"
$fileLoc     = "";                              // Location of these forecast files.  "" if in the web root folder, otherwise something like "forecast/"
 
// Log file(s)
$logfile = array (                              // The last two letters of your log files must must be unique because they're used to name the selectors 
  'am' => 'forecastAM.log',
  'pm' => 'forecastPM.log',
  'ev' => 'forecastEV.log',
  'ni' => 'forecastNI.log'
);

// Official forecast setup                      advforecast
$TypeScript  = "NWS";                           // advforecast="NWS", Weather Underground = "WU", Environment Canada = "EC"
$NWSscript   = "advforecast2.php";              // Use only one of these scripts
/*
$TypeScript  = "WU";                           
$NWSscript   = "WU-forecast.php";

$TypeScript  = "EC";                           
$NWSscript   = "ec-forecast.php";

$TypeScript  = "YR";                           
$NWSscript   = "yrfcst.php";
*/

// WxSim setup
$WXSIMscript = "plaintext-parser.php";   // This shouldn't change as far as location because it has to be in the same folder with our script

// High and low temp variables.  Must be made available through testtags.php or some other means
$tagsfile = "testtags.php";              // The script that generates the two tags that follow
//$TheMax   = $maxtempyest;                // These here as reminders
//$TheMin   = $mintempovernight;           // If you use something different, you'll have to change at approx line 38 in forecast-compare-include.php

// Configure the storage/display            NWS will produce up to 4 1/2 forecast days, Wunderground 5 days and Environment Canada 4 1/2 - 5 days
$analdays     = "7";                      // How many forecast days to log (D+1, D+2 etc).  Both forecast systems must produce at least this many days!!  
$logdays      = "365";		  			  // Days to maintain in the datafile
$Danaldays    = "4";                      // Default number of days to display horizontally
$Dlogdays     = "14";                     // Default number of days to display vertically
$showdeltas   = false;                    // Default whether to show the differences in each row.  This costs a lot of screen real estate
$showComp     = true;                     // Default whether to show the percent and standard deviation rows
$goal         = 2.0;					  // If 0, will display "Win/Lose" comparison.  > 0 will show success of each forecast as compared to the goal
$useWxSimTime = true;                     // If true, will store WxSim forecast time, otherwise it will store the time the log is written
$disptimes    = false;                    // Whether to show the times with the dates
$dispNWS      = true;                     // Whether to show the NWS/EC/WU/Yr.no forecast data columns

// Selectors
$showSels    = true;                     // Will turn all of the selectors on/off regardless of the settings that follow
$SelsOn = array (                        // Here you can chose to just show selectors with certain tables
  'am' => true,                          // Must match $logfile array above
  'pm' => true,
  'ev' => true,
  'ni' => true
);
$showDates   = true;                     // The following will control the individual selectors if $showSels above is true
$showRows    = true;                     
$showCols    = true; 
$showGoals   = true;                                                            
$showStats   = true;                     
$showDiffs   = true;                     
$showHnums   = true;
$showTime    = true;
$showNWS     = true;
$multiMenus  = true;                     // Change to false if you want all the tables to react to the selectors at the same time
 
// Display/Colors
$TableCenter = true;                     // If you want the table centered on your page or not
$lineColor   = "gray";                   // Color of the table lines
$BWSwinColor = "#0000d8";                // These 3 for just highlighting the number
$BNWwinColor = "#ff0000";
$BtieColor   = "#000000";
$WSwinColor  = "#99ff33";                // These 3 for background color highlights
$NWwinColor  = "#64d9ff";
$tieColor    = "#ffff80";

// Language
$lang        = "en";                     // Customize and add languages in fc-language.php. Defaults to English

$rndactuals = false;                      // Round the actual temps to a whole number for display

// Filter Words for WU Script
$WUword1 = "Standaarddruk";              // These are language words for "High" and "Low" that need to be filtered out
$WUword2 = "Hoog";                       // Change for your language if you have problems
$WUword3 = "of";
$WUword4 = "";
$WUword5 = "";
$WUword6 = "";

// Filter Words for WxSim Script
$WSword1 = "";                          // These are language words for "High" and "Low" that need to be filtered from WxSim output
$WSword2 = "";                          // Change for your language if you have problems

if ( isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
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
