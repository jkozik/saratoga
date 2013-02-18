<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org 
# (WD-USA template set)
############################################################################
#
#   Project:    TNET Development Scripts
#   Module:     raindetail.php
#   Purpose:    RAW script to display a table of rain data.
#   Authors:    Kevin W. Reed <kreed@tnet.com>
#               TNET Services, Inc.
#
#   Copyright:  (c) 1992-2009 Copyright TNET Services, Inc.
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
#
# History
#
# 2009-03-15 1.0 Inital Release
# 2009-03-15 1.1 Fixed data parsing in getnoaafile function
# 2009-03-16 1.2 Corrected form to use PHP Self for action
# 2010-05-11 1.3 Various cosmetic changes 
# 2010-05-12 1.4 Update for Safari browser
# 2010-08-31 1.5 Fix for seasonal month
# 2010-09-07 2.0 Added option to include today's data from tags in testtags file
# 2010-11-27 3.0 Added additional options for increment quantity and size
# 2011-12-27 3.6 Added support for Multilingual and Cumulus, Weatherlink, VWS
# 2012-06-06 3.7 A day 1 workaround for rain data missing from NOAA report 
# 2012-08-26 3.8 Added check for manually provided NOAA data in csv file format
############################################################################
require_once("Settings.php");
@include_once("common.php");
############################################################################
// Obtains Server's Self and protect it against XSS injection
$SITE['self'] = htmlentities( substr($_SERVER['PHP_SELF'], 0,  
    strcspn( $_SERVER['PHP_SELF'] , "\n\r") ), ENT_QUOTES );
@include_once("wxreport-settings.php");   
############################################################################
# Settings Unique to this script
############################################################################
$TITLE = $SITE['organ'] . " - ". langtransstr('Rain Detail');  
$SITE['viewscr'] = 'sce';  // Password for View Source Function 
$leading_zeros = 1; # max number of leading zeros
$increments = 11;  # if set higher than 11, you will need to edit the css file to add the additional color levels.
                   # 28 is the max value allowed. Values greater than 13 must be even numbers.
                   # The number of possible colors is two more than the $increment values - one below the 1st increment
                   # and one after the last increment  
$season_start = 1;      # Start Month of Season 
$rain_increment = array(.25, 1); # (inches, mm) Increments between colorbands
$set_values_manually = false; # Set to true if you want to set your own non-linear values
$manual_values = array(.05, .1, .25, .5, .75,1,1.5,2,2.5,3,5); # must have 11 values. Only used if $set_rainvalues_manually is true.
$css_file = "wxreports.css" ;  # name of css file
$round = false;                # Set to true if you want rainfall rounded to nearest integer  
############################################################################
# End of user settings
############################################################################
$loc = $path_dailynoaa;            # Location of dailynoaareports
$first_year_of_data = $first_year_of_noaadata;
$rain_units = trim($SITE['uomRain']);  
$rain_units = strtolower($rain_units);
$rainformat = '%0' . ($leading_zeros+2+($rain_units == "in")) . '.' . (1+($rain_units == "in")) . 'f' ;
$rain_increment = $rain_increment[($rain_units != "in")] ; 
$rain_units = " " . $rain_units ; // insert a blank space for report legibility
$rainvalues = array($rain_increment);

for ( $i = 0; $i < $increments ; $i ++ )                                                    
    {
$rainvalues[$i+1] = $rainvalues[$i] + $rain_increment;
    }
    
if ($set_values_manually == true){
    $rainvalues = $manual_values;
    $increments = (count($manual_values))-1;
}

$increments = min($increments,28); // Max of 22 increments allowed

if ($increments > 13){             // If more than 13 increments, must be an even number 
    $increments = (floor($increments * 0.5) / 0.5);
} 

$colors = $increments + 1;         
       
if ($season_start == 1)
    $range = "year";
else
    $range = "season";
    
// If first day of year/season, default to the previous year
    if (( date("n") == $season_start) AND (date("j") == 1) AND ($show_today != true))
	{
		$year = date("Y")-1;
	}
	else
	{
		$year = date("Y");
	}
       
if (( $range == "season") AND ($season_start <= date("m") )) 
        $year = $year + 1;     

// Build an array of years available assumming data is available from the first year thru the current year.

$years_available[0] = $year ;
$max = $year - $first_year_of_data;

for ( $i = 0; $i < $max ; $i ++ ) 
  
    {
$years_available[$i+1] = (string)($year - $i-1);
    }

############################################################################
# Adjust with passed variables
############################################################################

if (isset($_GET['year'])) {
    $year = intval($_GET['year']);
}

if (isset($_GET['range'])) {
    if ($_GET['range'] == 'season') {
        $range = "season";
    } else {
        $range = "year";
    }
}

############################################################################
// Check for Source Code View
check_sourceview();
@include("top.php");
############################################################################

echo ' <link rel="stylesheet" href="'.$css_file.'" type="text/css" />'; 
echo '</head>';
echo '<body>';

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
?>

<div id="main-copy">
    <div id="report">
        <center><h1><?php echo langtransstr('Rainfall Reports') . ' (' . trim($rain_units) ?>)</h1>
 <?php          echo '<h3>'.langtransstr('Report for').' ';
    
    if ($range == "year") 
        {
        echo langtransstr("Year")." " . $year;
        }
    else
        {
        echo langtransstr("Seasonal Year")." (" . $mnthname[$season_start -1 ] . " " . ($year -1) . " - " . $mnthname[$season_start -2 ] . " " . $year . ") ";
    }
    
    if ($show_today){
        echo '</h3>'. langtransstr('Data last updated').' '.$date.' '.$time.'.<br /><br />';
    } else {        
    echo '</h3>'. langtransstr('Note: Data is updated after midnight each day.').'<br /><br /> ';
    }                
?>   </center> 

            <div class="getreportdtbx doNotPrint"><?php langtrans('Other Years')?>:<br/> 
                <form  method="get" action="<?php echo $SITE['self']; ?>" >
                    <select name="year">
<?php 
// Display options for each available year from array
foreach($years_available as $value) {
    if ($range == "year")
    {
    echo "\t\t\t\t\t\t" . 
        '<option value="' . $value . '">' . $value . 
        ' </option>' . "\n"; 
    }
    else
    {  
    echo "\t\t\t\t\t\t" . 
        '<option value="' . $value . '">' .($value-1) . " / ". $value . 
        ' </option>' . "\n"; 
    }                          
} ?>
                    </select>
                    <input type="submit" value="<?php langtrans('Go')?>" />
                </form>
<?php                
 
@include("wxreportinclude.php");
$SITE['copy'] = "Script Developed by www.TNETWeather.com. Modified by Murry Conarroe of <a href='http://weather.wildwoodnaturist.com/'>Wildwood Weather</a>";
?>
           </div>
 
    
    <?php get_rain_detail($year,$loc, $range, $season_start, $rain_units); ?>
    
    </div>
</div><!-- end main-copy -->

<?php
############################################################################
 @include("footer.php");
############################################################################
# Functions
############################################################################

function get_rain_detail ($year, $loc, $range, $season_start, $rain_units) {
    global $SITE, $rainvalues, $rainformat, $colors;
    global $show_today, $dayrn, $date, $time, $timeofnextupdate, $mnthname, $yesterdayrain; 
        
    // Raw Data Definitions
    $rawlb = array("day" => 0, "mean" => 1, "high" => 2 , "htime" => 3,
        "low" => 4, "ltime" => 5, "hdg" => 6, "cdg" => 7, "rain" => 8,
        "aws" => 9, "hwind" => 10 , "wtime" => 11, "dir" => 12);

    // First Collect the data for the year
    
    if ($range == "year" ) {
        for ( $m = 0; $m < 12 ; $m ++ ) {
      
            // Save Month in the raw array for use later
            $raw[$m][1] = $m; 
            
            // Check for current year and current month
            
           if ($year == date("Y") && $m == ( date("n") - 1) && ((date("j") != 1 ) OR $show_today)) {
                $current_month = 1;
            } else {
                $current_month = 0;
            }
            
            $filename = get_noaa_filename($year,$m,$SITE['WXsoftware'],$current_month);            
            
            if ($current_month AND $show_today AND date("j")==1){
                $raw[$m][0][0][8] = strip_units($dayrn);                                 
            } else {
                $raw[$m][0] = getnoaafile($loc . $filename,$year,$m);            
            }
                if ($current_month AND $show_today){ 
                    $raw[$m][0][date("j")-1][8] = strip_units($dayrn);              
            }
            if ($current_month AND (date("j")==2) AND ('WD' == $SITE['WXsoftware'])){  // Fix for WD rain on first day of month not listed in NOAA report until the 3rd day of the month.
                if ((strip_units($yesterdayrain))>0){
                    $raw[$m][0][date("j")-2][8] = strip_units($yesterdayrain);    
                }
                
            }  
        }
    } else {
        
        $cnt = 0;
    
        // Pickup Season Start to end of that year
        for ( $m = ( $season_start - 1 ); $m < 12 ; $m ++ ) {
            
            // Save Month in the raw array for use later
            $raw[$cnt][1] = $m;
            
            // Check for current year and current month
            $yearx = $year - (($m +1) == date("n"));            
            if ($yearx == date("Y") && $m == ( date("n") - 1) && ((date("j") != 1 ) OR $show_today)) {
                $current_month = 1;
            } else {
                $current_month = 0;                    
            }
            $filename = get_noaa_filename(($year-1),$m,$SITE['WXsoftware'],$current_month);
            if ($current_month AND $show_today AND date("j")==1){
                $raw[$cnt][0][0][8] = strip_units($dayrn);                  
            }else {
                $raw[$cnt][0] = getnoaafile($loc . $filename,($year-1),$m);
             if ($current_month AND $show_today){ 
                    $raw[$cnt][0][date("j")-1][8] = strip_units($dayrn);                        
                }
             if ($current_month AND (date("j")==2) AND ('WD' == $SITE['WXsoftware'])){  // Fix for WD rain on first day of month not listed in NOAA report until the 3rd day of the month.
                if ((strip_units($yesterdayrain))>0){
                    $raw[$cnt][0][date("j")-2][8] = strip_units($yesterdayrain);    
                }
                
            }                 
            }
            $cnt ++;
        }
        
        // Pickup Beginning of year to month before season start of current year
        
        for ( $m = 0; $m < $season_start ; $m ++ ) {
            
            // Save Month in the raw array for use later
            $raw[$cnt][1] = $m;
            
            // Check for current year and current month
            
            if ($year == date("Y") && $m == ( date("n") - 1) && ((date("j") != 1 ) OR $show_today)) {           
                $current_month = 1;                
            } else {
                $current_month = 0;                
            }
           $filename = get_noaa_filename($year,$m,$SITE['WXsoftware'],$current_month);
            if ($current_month AND $show_today AND date("j")==1){
                $raw[$cnt][0][0][8] = strip_units($dayrn);                  
            }else {
                $raw[$cnt][0] = getnoaafile($loc . $filename,$year,$m);
             if ($current_month AND $show_today){ 
                    $raw[$cnt][0][date("j")-1][8] = strip_units($dayrn);                        
                    
                }                
            }
            $cnt ++;
        }
    }
    
    // Start display of info we got
    
    
    // Output Table with information
    
    echo '<table><tr><th class="labels">'.langtransstr('Day').'</th>';
    
        // Cycle through the months for a label
    
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        $mx = $m + $i;
        if ($range == "season")
            {
                if ($mx > 12)
                    {
                    $mx = $mx - 12;
                    $yearx = $year ;
                    }
                else 
                    {
                    $yearx = $year - 1;
                    }
            }
        else
            {
            $mx = $i + 1;
            $yearx = $year;
            }
        
        $maxdays[$i] = get_days_in_month($mx, $yearx);  // sets number of days per month for selected year
        echo '<th  class="labels">' . substr( $mnthname[ $raw[$i][1] ], 0, 3 ) . '</th>';
        }
    echo "</tr>\n";    
    
    // Setup Rainmonth and year totals
    
    $rainmonth = array();
    $ytd = 0;
    
    
    // Cycle through the possible days 
        
    for ( $day = 0 ; $day < 31 ; $day++ ) {
     
        echo '<tr><td class="reportdt">' . ($day + 1) . '</td>';
        
        // Display each months values for that day
        
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) {
            
            $mark = 0;
            
            if ($maxdays[$mnt] < $day + 1 ) { 
                echo '<td class="noday">&nbsp;</td>'; 
            } else {          
            if ( $raw[$mnt][0][$day][$rawlb['rain']] == "" ) {
                $put = "---";
            } else {
                $put = $raw[$mnt][0][$day][$rawlb['rain']];
                $rainmonth[$mnt][0] = $rainmonth[$mnt][0] + $put;
                if ($put > 0.0) {
                    $rainmonth[$mnt][1] = $rainmonth[$mnt][1] + 1;
                    $mark = 1;
                }
             }
           

            	if ($put > 0){
                echo '<td class=" ' . ValueColor($put,$rainvalues).'"' . '>' . sprintf($rainformat,$put) .' </td>';
                } else {
                    if ($put == "---"){
                        echo '<td class="reportday">' . $put . '</td>';
                    } else {
                         echo '<td class="reportday">' . sprintf($rainformat,$put) . '</td>';                        
                    }
            }
                }

       }
        
        echo "</tr>\n"; 
    }
    
    // We are done with the daily numbers now lets show the totals
    
    echo '<tr><td class="reportttl">'.langtransstr('Rain Days').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {
        echo '<td class="reportttl">' . sprintf("%d", $rainmonth[$i][1]) . 
            '</td>';
    }
    echo '</tr>';
    
    echo '<tr><td class="reportttl">'.langtransstr('Month Total').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {
            echo '<td class="reportttl">' . sprintf("%01.".(1+($rain_units==" in"))."f", $rainmonth[$i][0]) . '&nbsp;' . $rain_units . '</td>';         
    }
    echo '</tr>';
    
    $ytd=0;
    echo '<tr><td class="reportttl">'.langtransstr('YTD Total').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {
        $ytd = $ytd + $rainmonth[$i][0];      
        echo '<td class="reportttl">' . sprintf("%01.".(1+($rain_units==" in"))."f", $ytd) . '&nbsp;' . $rain_units .'</td>';
    }
      
      echo '</tr></table>';
      
$colorband_rows = ceil($colors/15);
$colorband_cols = ceil(($colors+1)/$colorband_rows);   

      echo '<table><tr><td class="separator" colspan="'.($colorband_cols).'" >&nbsp;</td></tr>';    
    echo '<tr><td class="colorband" colspan="'.($colorband_cols).'">'.langtransstr('Color Key').'</td></tr>';
    $i = 0;
    for ($r = 0; $r < $colorband_rows; $r ++){  
        for ( $j = 0; (($j < $colorband_cols) AND ($i < $colors)) ; $j ++ ){
        $band = $i;

        if ($i == 0){     
            echo '<tr><td class="levelb_1" >&lt;&nbsp;' . sprintf($rainformat,$rainvalues[$i]) . '</td>';
        } else {
            if (($j == 0) AND ($r > 0)){
            }
                echo '<td class="levelb_'.($band+1).'"'.$color_text.' > ' . sprintf($rainformat,$rainvalues[$i-1]) . " - " .sprintf($rainformat,$rainvalues[$i]) . '</td>';
                if (($j == $colorband_cols-1) AND ($r != $colorband_rows)){
                    echo '</tr><tr>';
                    
                } 
        }
        $i = $i+1;
        
        }
    }

    echo '<td class="levelb_'.($band+2).'"'.$color_text.' >'. sprintf($rainformat,$rainvalues[$i-1]) . '&gt;</td>';
    echo '</tr>';
    echo '</table><div class="dev">' . $SITE['copy'] . '</div>';       
}

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

//Calculate colors depending on value

function ValueColor($value,$values) {
    $limit = count($values);
    if ($value == 0){
        return 'reportday';
    }
    if ($value < $values[0]) {
      return 'levelb_1';
      } 
    for ($i = 1; $i < $limit ; $i++){
        if ($value <= $values[$i]) {
          return 'levelb_'.($i+1);
        }
    }
    return 'levelb_'.($limit+1);

}
       
function get_days_in_month($month, $year)
{
   return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year %400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}


############################################################################
# End of Page
############################################################################
?>