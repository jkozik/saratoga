<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-USA template set)
############################################################################
#
#   Project:    Local River/Lake Heights
#   Module:     river-fetch.php
#   Purpose:    Gets data from AHPS
#   Authors:    Dennis Clapperton <webmaster@eastmasonvilleweather.com>
#               East Masonville Weather
#   Version:	3.00F
############################################################################
#
#
# Gauge Array 
############################################################################
$RiverGauge = array(
    "AFBI2" => "Fox River at Algonquin Tailwater",
    "MNGI2" => "Fox River at Mongomery",
    "NDRI2" => "Du Page River near Naperville",
    "BOLI2" => "East Branch Du Page River at Bollingbrook",
    "DSPI2" => "Des Plaines River near Des Plaines",
    "RVRI2" => "Des Plaines River at Riverside"
);

###  FILENAMES and OTHER SETTINGS #### #####################################################################################
$currentstage = "http://water.weather.gov/ahps2/all_layer_merge.php?wfo=lot&amp;layers=10,7,8,2,9,15,6"; // Set to your region

$recordstoshow = 20; // Number of observation records to show on detail page.

$displayscale = 0; // If set to 1 display Scale to Flood Categories 
				   // If set to 0 display Default Hydrograph 

$ourTZ = 'America/Chicago';        // NOTE: this *MUST* be set correctly to show the correct times	
// find your zone    http://us3.php.net/manual/en/timezones.php

$riverpage= "wxriverpage.php"; // name of river page for go back link on detailed page
$detailspage = "wxriverdetail.php";  // river details page added here for use in river-fetch.php.
$target = 'target="_blank"';        // make "" if not wanting a new page/tab for links to external pages
$hydrographtop = false; //set to true if you want the hydrograph on top of the details page
$rivermaptop = false; //set to true if you want the river map on top of the river summary page
$dropdown = true; // Set to true if you want the dropdown menu on the details page
$forecasttrend = true; // Set to true if you want the forecast trend (arrow) column on the summary page
$forecastcolor = true; // Set to true if you want the max forecast color (colored dot) column on the summary page
$trend = true; // Set to true if you want the trend column on the summary page

###  END OF SETTINGS  ###########################################################################################

// set time zone
if (isset ($SITE['tz'])) {
  $ourTZ = $SITE['tz'];
}
if (isset ($SITE['timeFormat'])) {
  $timeFormat = $SITE['timeFormat'];
}

if (phpversion() < 5) {
  echo 'This script requires PHP version 5 or greater. You only have PHP version: ' . phpversion();
  exit;
}

// Establish timezone offset for time display
date_default_timezone_set("$ourTZ");

// Time zone offset for xml date
$TZ_offset = date("P");
?>
