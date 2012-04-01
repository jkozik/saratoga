<?php
/*
fc-LogTemps.php
by Jim McMurry - jcweather.us - jmcmurry@mwt.net
4-4-09 - Changed the tag names and recommended times

Captures daily hi and nightly low temps for forecast comparisons.  Captures $mintempovernight at approx 9:00 am for use until the next reset.  Same for $maxtemp some time prior to midnight.

Run this script twice each day to capture the past night's low temperature and the days's high temperature:
- Once in the morning as close to 0900 as you can.  Make sure you run forecast-compare-include.php in logging mode after fc-LogTemps.php has run.
- Once prior to midnight but after your final running of forecast-compare-include.php in logging mode for that calendar day.

Requires that the following two lines be present in your testtags.txt file, or that the variables be populated another way
$maxtemp = %maxtemp%;                  
$mintempovernight = %mintempovernight%;              

Also requires that an empty text file named fc-temps.txt be placed in the same folder with this script and given write read/permissions
*/
date_default_timezone_set("America/Chicago");        
$log = "fc-temps.txt";
include("testtags.php"); 
//include("../testtags.php");                        // Maybe something like this if you have the fc files in a special folder
$now = date("G",time());
$lines = file($log);                                 // Reads into an array
$entry = explode(",",$lines[0]);                     // to get the contents of the 1st (and only ) record

/*
if ($now < 12) {                                     // It's morning, so capture the nightly low
	$entry[1] = $mintempovernight;
} else {                                             // It's evening, so capture the daily high
	$entry[0] = $maxtemp;
}
*/

$entry[0] = $maxtempyest;
$entry[1] = $mintempovernight;

/*
$entry[0] = $maxtempyest;
$entry[1] = $mintempyest;		
*/
$fp = fopen($log, "w") or die("Error: You must manually upload an empty text file named " . $log . " and give it write permissions (CHMOD 666)."); 
fwrite($fp, $entry[0] . "," . $entry[1] . "," . date("m/d/Y",time()) . " " . date("g:i a",time()));            // Write the most recent entry
fclose($fp);	

?>
