<?php
############################################################################
#
#   Module:     wxnoaarecords.php
#   Purpose:    Display a table of temperature, rain, wind, snow, pressure records.
#   Author:     Murry Conarroe <murry@murry.com>
#              
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
#   This document uses Tab 4 Settings
############################################################################
#   History
# 2010-08-23 1.0 Inital Release
# 2010-09-07 2.0 Added option to include today's data from tags in testtags file
# 2010-11-01 2.1 Fix for first day of month when $show_today is true.
# 2010-11-07 2.5 Added options for snow and pressure records
# 2010-11-10 2.6 Fix for missing days from dailynoaareport files
# 2010-11-13 2.61 Update for those without $uomSnow set, and other cosmetic changes
# 2011-01-01 2.7 Fix for rain year-to-date for first day of rain year
# 2011-01-04 2.71 Update for those with less than a year of data to not show 
#                 snow mtd or ytd for days greater than the current day of the current month 
# 2011-03-01 2.72 Fix for year-to-date on first day of month is $show_today is true.
# 2011-12-27 3.0 Added support for multilingual and Cumulus, Weatherlink, & VWS
# 2012-03-06 3.1 Leap year fix for rain/snow year-to-date totals
# 2012-06-06 3.3 A day 1 workaround for rain data missing from NOAA report
# 2012-08-26 3.4 Added check for manually provided NOAA data in csv file format
############################################################################ 
require_once("Settings.php");
@include_once("common.php");
if(!function_exists('langtransstr')) {    
    add_language_functions();
}
############################################################################
############################################################################
# Settings Unique to this script
############################################################################
$TITLE = $SITE['organ'] . " - ". langtransstr("Daily Records");
$showGizmo = true;  // set to false to exclude the gizmo 
$path_dailynoaa = "./noaa/";             # path to NOAA reports 
$path_climatedata = "./";            # WD ONLY - Location of climatedataout*.html files
$first_year_of_data = "2010";  # First year of dailynoaareport & climatedataout data that is available
$heading_name = 'NapervilleWeather.net';            # Text to be displayed on page
$recording_start_date = 'May 2010'; # Text to be displayed on page
$show_today = true; # Set to true if you want today's info to be included. Info for today will come from custom tags in your testtags file.
$rain_season_start = 1; # Set to first month of rain season (1 for January, 7 for July, etc). Used for ytd calcuations
$snow_season_start = 7; # WD ONLY - Set to first month of snow season (1 for January, 7 for July, etc). Used for ytd calcuations
$show_snow_records = true; # WD ONLY - Set to false to not diplay snow, snow month to date, and snow year to date records
$show_baro_records = true; # WD ONLY - Set to false to not display barometer records
$SITE['viewscr'] = 'sce';  // Password for View Source Function 
############################################################################
# End of user settings
############################################################################
############################################################################
// Obtains Server's Self and protect it against XSS injection
$SITE['self'] = htmlentities( substr($_SERVER['PHP_SELF'], 0,  
    strcspn( $_SERVER['PHP_SELF'] , "\n\r") ), ENT_QUOTES );
############################################################################
############################################################################
check_sourceview();
 @include("top.php");
############################################################################
?>
<link rel="stylesheet" href="wxnoaarecords.css" type="text/css" />
</head>
<body>
<?php
############################################################################
@include("header.php");
############################################################################
@include("menubar.php");
############################################################################
if(!function_exists('strip_units')) {
    function strip_units ($data) {
        preg_match('/([\d\.\+\-]+)/',$data,$t);
        return $t[1];
}
}
$SITE['copy'] = langtransstr("Script Developed by")." Murry Conarroe of <a href='http://weather.wildwoodnaturist.com/'>Wildwood Weather</a>.";
error_reporting(E_ALL & ~E_NOTICE );
require_once("wxnoaarecords-include.php");

################### Functions #########################

function check_sourceview () {
    global $SITE;
    
    if ( isset($_GET['view']) && $_GET['view'] == $SITE['viewscr'] ) {
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
}

#####################################################################
///  Add language functions if using V2 USA template
function add_language_functions (){
if(!function_exists('langtransstr')) {    
function langtransstr ( $item ) {
  global $LANGLOOKUP,$missingTrans;
  
  if(isset($LANGLOOKUP[$item])) {
     return $LANGLOOKUP[$item];
  } else {
      if(isset($item) and $item <> '') {$missingTrans[$item] = true; }
     return $item;
  }
}
}
#####################################################################
if(!function_exists('langtrans')) {    
function langtrans ( $item ) {
  global $LANGLOOKUP,$missingTrans;

  if(isset($LANGLOOKUP[$item])) {
     echo $LANGLOOKUP[$item];
  } else {
     if(isset($item) and $item <> '') {$missingTrans[$item] = true; }
     echo $item;
    }
 
}
}
}
############################################################################
# End of Page
############################################################################

?> 
