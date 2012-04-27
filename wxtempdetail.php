<?php
############################################################################
#
#   Module:     tempdetail.php
#   Purpose:    Display a table of temperature data.
#   Author:     Murry Conarroe <murry@murry.com>
#   Modified:   Labbs  www.lokaltvader.se  
#              
# This script is based on the raindetail.php script developed by 
# Kevin Reed of TNET Services with additional changes made by myself to
# display the temperature information.
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
# 2009-12-28 1.0 Inital Release
# 2010-01-06 1.1 Added additional color options
# 2010-01-08 1.2 Fixed typo in color index, and number of days per month
#                not calculated properly on some systems.
# 2010-01-09 1.3 Fix for font colors for some temps using option 3 for highlight
#                Added option to round temperatures to nearest integer
# 2010-01-10 1.4 Added option to show the monthly hi/low in the same format as daily temps
# 2010-01-12 1.5 Added options to select starting point and increments of 
#                temperature colorbands.
# 2010-01-21 1.6 Added monthly mean temperature to summary table
# 2010-05-11 1.7 Various cosmetic changes 
# 2010-05-12 1.8 Update for Safari browser
# 2010-08-31 1.9 Fix for seasonal month
# 2010-09-07 2.0 Added option to include today's data from tags in testtags file
# 2010-11-27 3.0 Added additional options for increment quantity and size
# 2010-11-39 3.01 Changed colspan setting for IE problem
# 2011-03-09 3.14 Removed leading zero from single digit temps
# 2011-12-27 3.6 Added support for Multilingual and Cumulus, Weatherlink, VWS
############################################################################
require_once("Settings.php");
@include_once("common.php");
############################################################################
// Obtains Server's Self and protect it against XSS injection
$SITE['self'] = htmlentities( substr($_SERVER['PHP_SELF'], 0,  
    strcspn( $_SERVER['PHP_SELF'] , "\n\r") ), ENT_QUOTES );
############################################################################
@include_once("wxreport-settings.php");   
############################################################################
# Settings Unique to this script
############################################################################
$TITLE = $SITE['organ'] . " - Temperature Detail";   
$SITE['viewscr'] = 'sce';  // Password for View Source Function 
$temprange_start = array(0, -15); # (Farenheit, Celcius) Starting point of the lowest temp colorband 
$temprange_increment = array(10, 5); # (Farenheit, Celcius) Increments between colorbands
$increments = 11;  # if set higher than 11, you will need to edit the css file to add the additional color levels.
                   # 28 is the max value allowed. Values greater than 13 must be even numbers.
                   # The number of possible colors is two more than the $increment values - one below the 1st increment
                   # and one after the last increment  
$set_values_manually = false; # Set to true if you want to set your own non-linear values
$manual_values = array(.25, .5, 1, 2, 3,6,12,18,24,36,60,75); # Only used if $set_values_manually is true. 
$round = false;                # Set to true if you want temperatures rounded to nearest integer  
$css_file = "wxreports.css" ;  # name of css file 
############################################################################################################# 
#############################################################################################################  
# End of user settings
############################################################################
$loc = $path_dailynoaa;            # Location of dailynoaareports
$first_year_of_data = $first_year_of_noaadata;
$season_start = 1;             # Month Number (1 for January, 7 for July, etc)
$uomTemp = $SITE['uomTemp'];
$temptype = array("C","F") ;
$temptype = $temptype[$uomTemp == "&deg;F"];
$temprange_start = $temprange_start[((strtoupper($temptype)) != "F")] ;
$temprange_increment = $temprange_increment[((strtoupper($temptype)) != "F")] ;

$tempvalues = array($temprange_start);
for ( $i = 0; $i < $increments ; $i ++ ) 
    {
$tempvalues[$i+1] = $tempvalues[$i] + $temprange_increment;
    }
    

if ($set_values_manually == true){
    $tempvalues = $manual_values;
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

if (isset($_GET['year']))
	{
    $year = intval($_GET['year']);
	}	

if (isset($_GET['range']))
	{
    if ($_GET['range'] == 'season')
		{
        $range = "season";
		}	
	else
		{
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
        <center><h1>
<?php 
echo langtransstr('Temperature Reports') . ' (' .$uomTemp .')</h1>';
echo '<h3>' . langtransstr('Report for'). ' ';
    
    if ($range == "year") 
        {
        echo langtransstr('Year') . ' ' . $year;
        }
    else
        {
        echo langtransstr('Seasonal Year') .' (' . $mnthname[$season_start -1 ] . " " . ($year -1) . " - " . $mnthname[$season_start -2 ] . " " . $year . ") ";
    }
    
        if ($show_today){
            echo '</h3>' . langtransstr('Data last updated') . ' ' .$date.' '.$time.'.<br /><br />'; 
        } else { 
        echo '</h3>' . langtransstr('Note: Data is updated after midnight each day.').'.<br /><br />';            
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
?>
           </div>
    
<?php get_temp_detail($year,$loc, $range, $season_start, $round); ?>

    </div>
</div><!-- end main-copy -->



<?php  
############################################################################
 @include("footer.php");
############################################################################
# Functions
############################################################################

function get_temp_detail ($year, $loc, $range, $season_start, $round) {
    global $SITE, $tempvalues, $temptype, $colors;
    global $show_today, $maxtempyest, $mintempyest, $maxtemp, $mintemp, $date, $time, $timeofnextupdate, $mnthname; 
    global $avtempsincemidnight, $yesterdayavtemp ;    
if ($round == true)
    $places = "%01.0f";
else
    $places = "%01.1f";
        
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
                $raw[$m][0][0][1] = strip_units($avtempsincemidnight);                
                $raw[$m][0][0][2] = strip_units($maxtemp);
                $raw[$m][0][0][4] = strip_units($mintemp);                  
            } elseif (file_exists($loc . $filename) ) {
                $raw[$m][0] = getnoaafile($loc . $filename);
            }
                if ($current_month AND $show_today){ 
                    $raw[$m][0][date("j")-1][1] = strip_units($avtempsincemidnight);                                 
                    $raw[$m][0][date("j")-1][2] = strip_units($maxtemp);
                    $raw[$m][0][date("j")-1][4] = strip_units($mintemp);                        
                    
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
            $filename = get_noaa_filename(($year-1),$m+1,$SITE['WXsoftware'],$current_month);            
            if ($current_month AND $show_today AND date("j")==1){
                $raw[$cnt][0][0][1] = strip_units($avtempsincemidnight);                
                $raw[$cnt][0][0][2] = strip_units($maxtemp);
                $raw[$cnt][0][0][4] = strip_units($mintemp);                  
            }elseif (file_exists($loc . $filename) ) {
                $raw[$cnt][0] = getnoaafile($loc . $filename);
             if ($current_month AND $show_today){ 
                    $raw[$cnt][0][date("j")-1][1] = strip_units($avtempsincemidnight);                                 
                    $raw[$cnt][0][date("j")-1][2] = strip_units($maxtemp);
                    $raw[$cnt][0][date("j")-1][4] = strip_units($mintemp);                        
                    
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
           $filename = get_noaa_filename($year,($m),$SITE['WXsoftware'],$current_month);
            if ($current_month AND $show_today AND date("j")==1){
                $raw[$cnt][0][0][1] = strip_units($avtempsincemidnight);                
                $raw[$cnt][0][0][2] = strip_units($maxtemp);
                $raw[$cnt][0][0][4] = strip_units($mintemp);                  
            }elseif (file_exists($loc . $filename) ) {
                $raw[$cnt][0] = getnoaafile($loc . $filename);
             if ($current_month AND $show_today){ 
                    $raw[$cnt][0][date("j")-1][1] = strip_units($avtempsincemidnight);                                 
                    $raw[$cnt][0][date("j")-1][2] = strip_units($maxtemp);
                    $raw[$cnt][0][date("j")-1][4] = strip_units($mintemp);                        
                    
                }                
            }
            $cnt ++;
        }
    }
    
    // Start display of info we got
       
    // Output Table with information
    
    echo '<table><tr><th rowspan="2" class="labels"  width="8%">'. langtransstr('Day').'</th>';
    
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
        echo '<th  colspan="2" class="labels" width="7%" >' . substr( $mnthname[ $raw[$i][1] ], 0, 3 ) . '</th>';
		}
    echo "</tr>\n";


echo '<tr>';
for ($i = 0 ; $i < 12 ; $i++ ) 
	{
        echo '<th class="labels" width="3%">'. langtransstr('Hi').'</th>';
        echo '<th class="labels" width="3%" >'. langtransstr('Lo').'</th>';
	}
echo "</tr>\n";  
    
    // Setup month and year totals
    
    $tempmonth = array();
    $monthmax = array (-100, -100, -100, -100, -100, -100, -100, -100, -100, -100, -100, -100) ; // Initial value for high temp for month
    $monthmin = array (100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100) ;  // Initial value for low temp for month    
    $monthmean = array();
    $ytd = 0;
    
    // Cycle through the possible days 
        
    for ( $day = 0 ; $day < 31 ; $day++ ) {
        echo '<tr><td class="reportdt">' . ($day + 1) . '</td>';
        
        // Display each months values for that day
        
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) {

            if ($maxdays[$mnt] < $day + 1 ) {
				echo '<td class="noday" colspan="2">&nbsp;</td>';
			} else {
                
        // Get mean temperature data 
                $xyz =  $raw[$mnt][0][$day][$rawlb['mean']] ;      
                if ( $raw[$mnt][0][$day][$rawlb['mean']] != "" AND $raw[$mnt][0][$day][$rawlb['mean']] != "-----" AND $raw[$mnt][0][$day][$rawlb['mean']] != "X" ) {       
                $tempmonth[$mnt][4] = $tempmonth[$mnt][4] + $xyz;
                $tempmonth[$mnt][5] = $tempmonth[$mnt][5] + 1;
                 }                
                                             

				if ( $raw[$mnt][0][$day][$rawlb['high']] == "" ) {
					$put = "---";
				} else {
					$put = $raw[$mnt][0][$day][$rawlb['high']];
                    $put = roundoff($put, $round);
					$tempmonth[$mnt][0] = $tempmonth[$mnt][0] + $put;
					$tempmonth[$mnt][1] = $tempmonth[$mnt][1] + 1;
                    if ($put > $monthmax[$mnt]) {
                        $monthmax[$mnt] = $put; 
                        }

				}
               if ($put != "---"){
                    echo '<td class=" ' . ValueColor($put).'">' . sprintf($places,$put) .' </td>';
               } else {
                    echo '<td class="reportday" >' . "---"  . '</td>';
               }


	            if ( $raw[$mnt][0][$day][$rawlb['low']] == ""  ) {
                $put = "---";
				} else {
                $put = $raw[$mnt][0][$day][$rawlb['low']];
                $tempmonth[$mnt][2] = $tempmonth[$mnt][2] + $put;
                $tempmonth[$mnt][3] = $tempmonth[$mnt][3] + 1;
	                if ($put < $monthmin[$mnt]) {
                    $monthmin[$mnt] = $put; 
                    }

				}

               if ($put != "---"){
                    echo '<td class=" ' . ValueColor($put).'">' . sprintf($places,$put) .' </td>';
               } else {
                    echo '<td class="reportday" >' . "---"  . '</td>';
               }
		  }
       }
        echo "</tr>\n";
   
    }
    
    // We are done with the daily numbers now lets show the totals
    
  //  echo '<tr><td class="noday" colspan="26" >&nbsp;</td></tr>'; 
  echo '<tr><td class="separator" colspan="25" >&nbsp;</td></tr>';     

    echo '<tr><th class="labels">&nbsp;</th>'  ;
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th  colspan="2" class="labels">' . substr( $mnthname[ $raw[$i][1] ], 0, 3 ) . '</th>';     
        }
    echo '</tr>';  
    
    echo '<tr><td class="reportttl">';
    langtrans('High');
    echo '</td>';
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($tempmonth[$i][1] > 0)
            {                  
                    echo '<td class=" ' . ValueColor($monthmax[$i]).'" colspan="2">' . sprintf($places,$monthmax[$i]) .' </td>';     
            }
            
        else
            {
                echo '<td class="reportttl" colspan="2">' . "---"  . '</td>';
            }
        }
    echo '</tr>';    
    echo '<tr><td class="reportttl">';
    langtrans('Avg High');
	echo '</td>';
    for ( $i = 0 ; $i < 12 ; $i++ )
	    {
		if ($tempmonth[$i][1] > 0)
			{         
            echo '<td class=" ' . ValueColor(($tempmonth[$i][0] / $tempmonth[$i][1] )).'" colspan="2">' .  sprintf($places,($tempmonth[$i][0] / $tempmonth[$i][1] )) . '</td>';
            }
            
        else
				echo '<td class="reportttl" colspan="2" >' . "---"  . '</td>';
        }
 
    echo '</tr><tr><td class="reportttl">';
    langtrans('Mean');
    echo '</td>';
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($tempmonth[$i][5] > 0 AND $tempmonth[$i][5] !="" )
            {           
            echo '<td class=" ' . ValueColor(($tempmonth[$i][4] / $tempmonth[$i][5] )).'" colspan="2">' .  sprintf($places,($tempmonth[$i][4] / $tempmonth[$i][5] )) . '</td>';                
            }  else
                echo '<td class="reportttl" colspan="2" >' . "---"  . '</td>';
        } 
 
 
    echo '</tr><tr><td class="reportttl">';
    langtrans('Avg Low');
    echo '</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) 
    {       
		if ($tempmonth[$i][3] > 0)
			{              
            echo '<td class=" ' . ValueColor(($tempmonth[$i][2] / $tempmonth[$i][3] )).'" colspan="2">' .  sprintf($places,($tempmonth[$i][2] / $tempmonth[$i][3] )) . '</td>';                                
			}
		else
			{
				echo '<td class="reportttl" colspan="2">' . "---"  . '</td>';
			}
		}
    echo '</tr>';

    echo '<tr><td class="reportttl">';
	langtrans('Low');
	echo '</td>';
    for ( $i = 0 ; $i < 12 ; $i++ )
        {

        if ($tempmonth[$i][3] > 0)
            {
            echo '<td class=" ' . ValueColor($monthmin[$i]).'" colspan="2">' .  sprintf($places,$monthmin[$i]) . '</td>';                                     
            }
        else
            {
                echo '<td class="reportttl" colspan="2" >' . "---"  . '</td>';
            }
        }
    echo '</tr>'; 

       
  
$colorband_rows = ceil($colors/15);
$colorband_cols = ceil(($colors+1)/$colorband_rows);   

      echo '</table><table><tr><td class="separator" colspan="'.($colorband_cols).'" >&nbsp;</td></tr>';    
    echo '<tr><td class="colorband" colspan="'.($colorband_cols).'">'.langtransstr('Color Key').'</td></tr>';
    $i = 0;
    for ($r = 0; $r < $colorband_rows; $r ++){  
        for ( $j = 0; (($j < $colorband_cols) AND ($i < $colors)) ; $j ++ ){
        $band = $i;

        if ($i == 0){     
            echo '<tr><td class="level_1" >&lt;&nbsp;' . sprintf("%01.0f",$tempvalues[$i]) . '</td>';
        } else {
            if (($j == 0) AND ($r > 0)){
             //   echo '<tr>';
            }
                echo '<td class="level_'.($band+1).'" > ' . sprintf("%01.0f",$tempvalues[$i-1]) . " - " .sprintf("%01.0f",$tempvalues[$i]) . '</td>';
                if (($j == $colorband_cols-1) AND ($r != $colorband_rows)){
                    echo '</tr><tr>';
                    
                } 
        }
        $i = $i+1;
        
        }
    }

    echo '<td class="level_'.($band+2).'" >'. sprintf("%01.0f",$tempvalues[$i-1]) . '&gt;</td>';
    echo '</tr></table>';
    echo '<div class="dev">' . $SITE['copy'] . '</div>';   
     
}

# GETNOAAFILE function
# Developed by TNETWeather.com
#
# Returns an array of the contents of the specified filename
# Array contains days from 1 - 31 (or less if the month has less) and
# the values:
# Day
# Mean Temp
# High Temp
# Time of High Temp
# Low Temp
# Time of Low Temp
# Hot Degree Day
# Cold Degree Day
# Rain
# Avg Wind Speed
# High Wind
# Time High Wind
# Dom Wind Direction
############################################################################

function getnoaafile ($filename) {
    global $SITE;               
    
    $rawdata = array();
    
    $fd = @fopen($filename,'r');
    
    $startdt = 0;
    if ( $fd ) {
    
        while ( !feof($fd) ) { 
        
            // Get one line of data
            $gotdat = trim ( fgets($fd,8192) );
            
            if ($startdt == 1 ) {
                if ( strpos ($gotdat, "--------------" ) !== FALSE ){
                    $startdt = 2;
                } else {
                    $gotdat = str_replace(",",".",$gotdat); 
                    $foundline = preg_split("/[\n\r\t ]+/", $gotdat );                    
                    $rawdata[intval ($foundline[0]) -1 ] = $foundline;
                }
            }
        
            if ($startdt == 0 ) {
                if ( strpos ($gotdat, "--------------" ) !== FALSE ){
                    $startdt = 1;
                } 
            }
        }
        // Close the file we are done getting data
        fclose($fd);
    }   
    return($rawdata);
}

//Calculate colors depending on value

function ValueColor($value) {
    global $tempvalues;
    $limit = count($tempvalues);
    if ($value < $tempvalues[0]) {
      return 'level_1';
      } 
    for ($i = 1; $i < $limit; $i++){
        if ($value <= $tempvalues[$i]) {
          return 'level_'.($i+1);
        }
    }
    return 'level_'.($limit+1);

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

function get_days_in_month($month, $year)
{
   return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year %400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}

function roundoff($put, $round)
{
    if ($round == false)
        return $put;
    else     
        return  round($put,0);
}

############################################################################
# End of Page
############################################################################
?>