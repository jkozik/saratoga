<?php
############################################################################
#
#   Module:     winddetail.php
#   Purpose:    Display a table of winddata.
#   Author:     Murry Conarroe <murry@murry.com>
#              
# This script is based on the raindetail.php script developed by 
# Kevin Reed of TNET Services with additional changes made by myself to
# display the wind information.
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
# 2010-05-11 1.1 Various cosmetic changes 
# 2010-05-12 1.2 Update for Safari browser
# 2010-08-31 1.3 Fix for seasonal month
# 2010-09-07 2.0 Added option to include today's data from tags in testtags file
# 2010-11-27 3.0 Update to use common files between all summary and detail files
# 2011-05-09 3.15 Added option to round windspeed to nearest integer
# 2011-12-27 3.6 Added support for Multilingual and Cumulus, Weatherlink, VWS
# 2012-08-26 3.8 Added check for manually provided NOAA data in csv file format
############################################################################
require_once("Settings.php");
@include_once("common.php");
############################################################################
// Obtains Server's Self and protect it against XSS injection
$SITE['self'] = htmlentities( substr($_SERVER['PHP_SELF'], 0,  
    strcspn( $_SERVER['PHP_SELF'] , "\n\r") ), ENT_QUOTES );
include_once("wxreport-settings.php");
$TITLE = $SITE['organ'] . " - ".langtransstr("Wind Reports");      
############################################################################
# Settings Unique to this script
############################################################################
$start_year = "2010"; // Set to first year of wind data you have
$SITE['viewscr'] = 'sce';  // Password for View Source Function 
$wind_unit = "mph";      # Set to mph, kmh, kts, or m/s
$css_file = "wxreports.css" ;  # name of css file  
$round = false;                # Set to true if you want windspeeds rounded to nearest integer 
############################################################################
# End of user settings
############################################################################
$loc = $path_dailynoaa;            # Location of dailynoaareports
$first_year_of_data = $first_year_of_noaadata;
$first_year_of_data = max($first_year_of_data,$start_year);
if ("mph" == strtolower($wind_unit)) {
$beau_scale = array(1, 3.4, 7.4, 12.4, 17.4, 24.4, 30.4, 38.4, 46.4, 54.4, 63.4, 72.4) ;  // mph
} else {
    if ("kmh" == strtolower($wind_unit)) {
    $beau_scale = array(1, 5.5, 11.4, 19.4, 28.4, 38.4, 49.4, 61.4, 74.4, 88.4, 102.4, 117.4) ;  // kmh
    } else {
        if ("kts" == strtolower($wind_unit)) {
        $beau_scale = array(1, 2.4, 6.4, 10.4, 15.4, 20.4, 26.4, 33.4, 40.4, 47.4, 55.4, 63.4) ;  // kts    
        } else {
            if ("m/s" == strtolower($wind_unit)) {
            $beau_scale = array(0.3, 1.5, 3.4, 5.4, 7.9, 10.7, 13.8, 17.1, 20.7, 24.4, 28.4, 32.6) ;  // m/s    
            }
        }
    }
}

$season_start = 1;      
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
        <center><h1><?php echo langtransstr('Wind Reports').' ('.$wind_unit.')' ?></h1>
 <?php          echo '<h3>Report for ';
    
    if ($range == "year") 
        {
        echo langtransstr("Year").' ' . $year;
        }
    else
        {
        echo langtransstr("Seasonal Year")." (" . $mnthname[$season_start -1 ] . " " . ($year -1) . " - " . $mnthname[$season_start -2 ] . " " . $year . ") ";
    }
    
    if ($show_today){
        echo '</h3>'.langtransstr('Data last updated').' '.$date.' '.$time.'.<br /><br />';
    } else {        
    echo '</h3>'.langtransstr('Note: Data is updated after midnight each day.').'<br /><br /> ';
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
    
    <?php get_wind_detail($year,$loc, $range, $season_start, $hot, $cold, $round); ?>
 </table>

 
<?php echo '<div class="dev">' . $SITE['copy'] . '</div>'; ?>

<table>
<tr><td style="font-weight: bold; font-size:large;"><?php langtrans('WIND COLOR CODING CHART');?></td></tr></table>
<table style="font-family: verdana; border-width: 0">
<tr>
<th rowspan="2" style="border-style: none; border-width: medium" width="127" height="38"><?php langtrans('Beaufort number');?></th>
<th rowspan="2" style="border-style: none; border-width: medium" width="232" height="38"><?php langtrans('Description');?></th>
<th colspan="4" width="285" height="15"><?php langtrans('Wind speed');?></th></tr>
<tr>
<th width="66" style="border-style: none; border-width: medium" height="19">mph</th>
<th width="61" style="border-style: none; border-width: medium" height="19">km/h</th>
<th width="61" style="border-style: none; border-width: medium" height="19">kts</th>
<th width="85" style="border-style: none; border-width: medium" height="19">m/s</th>
</tr>
<tr class="beaufort0"><th>0</th><td><?php langtrans('Calm');?></td><td>&lt; 1</td><td>&lt; 1</td><td>&lt; 1</td><td>&lt; 0.3</td></tr>
<tr class="beaufort1"><th>1</th><td><?php langtrans('Light air');?></td><td>1 – 3</td><td>1.1 – 5.5</td><td>1 – 2</td><td>0.3 – 1.5</td></tr>
<tr class="beaufort2"><th>2</th><td><?php langtrans('Light breeze');?></td><td>4 – 7</td> <td>5.6 – 11</td><td>3 – 6</td><td>1.6 – 3.4</td></tr>
<tr class="beaufort3"><th>3</th><td><?php langtrans('Gentle breeze');?></td><td>8 – 12</td><td>12 – 19</td><td>7 – 10</td><td>3.4 – 5.4</td></tr>
<tr class="beaufort4"><th>4</th><td><?php langtrans('Moderate breeze');?></td> <td>13 – 17</td><td>20 – 28</td><td>11 – 15</td><td>5.5 – 7.9</td></tr>
<tr class="beaufort5"><th>5</th><td><?php langtrans('Fresh breeze');?></td> <td>18 – 24</td><td>29 – 38</td><td>16 – 20</td><td>8.0 – 10.7</td></tr>
<tr class="beaufort6"><th>6</th><td><?php langtrans('Strong breeze');?></td><td>25 – 30</td><td>39 – 49</td><td>21 – 26</td><td>10.8 – 13.8</td></tr>
<tr class="beaufort7"><th>7</th><td><?php langtrans('High wind, Moderate gale, Near gale');?></td><td>31 – 38</td><td>50 – 61</td><td>27 – 33</td><td>13.9 – 17.1</td></tr>
<tr class="beaufort8"><th>8</th><td><?php langtrans('Gale, Fresh gale');?></td><td>39 – 46</td><td>62 – 74</td><td>34 – 40</td><td>17.2 – 20.7</td></tr>
<tr class="beaufort9"><th>9</th><td><?php langtrans('Strong gale');?></td><td>47 – 54</td><td>75 – 88</td><td>41 – 47</td><td>20.8 – 24.4</td></tr>
<tr class="beaufort10"><th>10</th><td><?php langtrans('Storm, Whole gale');?></td><td>55 – 63</td><td>89 – 102</td><td>48 – 55</td><td>24.5 – 28.4</td></tr>
<tr class="beaufort11"><th>11</th><td><?php langtrans('Violent storm');?></td><td>64 – 72</td><td>103 - 107</td><td>56 – 63</td><td>28.5 – 32.6</td></tr>
<tr class="beaufort12"><th>12</th><td><?php langtrans('Hurricane force');?></td> <td>&gt; 73</td><td>&gt; 118</td><td>&gt; 64</td><td>&gt; 32.7</td></tr>
</table>
</div>   <!-- end wind --> 
</div> <!-- end main copy -->

<?php

############################################################################
 @include("footer.php");
############################################################################
# Functions
############################################################################
 function get_wind_detail ($year, $loc, $range, $season_start, $hot, $cold, $round) {
    global $SITE, $beau_scale;
    global $wind_unit, $show_today, $maxgst, $avgspeedsincereset, $date, $time, $timeofnextupdate, $mnthname;
    
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
                $raw[$m][0][0][9] = strip_units($avgspeedsincereset);                
                $raw[$m][0][0][10] = strip_units($maxgst);                 
            } else {
                $raw[$m][0] = getnoaafile($loc . $filename,$year,$m);
            }
                if ($current_month AND $show_today){ 
                    $raw[$m][0][date("j")-1][9] = strip_units($avgspeedsincereset);                                 
                    $raw[$m][0][date("j")-1][10] = strip_units($maxgst);
                                         
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
                $raw[$cnt][0][0][9] = strip_units($avgspeedsincereset);                
                $raw[$cnt][0][0][10] = strip_units($maxgst);                  
            }else {
                $raw[$cnt][0] = getnoaafile($loc . $filename,($year-1),$m);
             if ($current_month AND $show_today){ 
                    $raw[$cnt][0][date("j")-1][9] = strip_units($avgspeedsincereset);                                 
                    $raw[$cnt][0][date("j")-1][10] = strip_units($maxgst);                      
                    
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
                $raw[$cnt][0][0][9] = strip_units($avgspeedsincereset);                
                $raw[$cnt][0][0][10] = strip_units($maxtst);                 
            }else {
                $raw[$cnt][0] = getnoaafile($loc . $filename,$year,$m);
             if ($current_month AND $show_today){ 
                    $raw[$cnt][0][date("j")-1][9] = strip_units($avgspeedsincereset);                                 
                    $raw[$cnt][0][date("j")-1][10] = strip_units($maxgst);                      
                    
                }                
            }
            $cnt ++;
        }
    }
    
    // Start display of info we got
    
    // Output Table with information
    
    echo '<table><tr><th rowspan="2" class="labels"  width="8%">'.langtransstr('Day').'</th>';
    
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
        echo '<th class="labels" width="3%">'.langtransstr('Avg').'</th>';
        echo '<th class="labels" width="4%">'.langtransstr('Hi').'</th>';
    }
echo "</tr>\n";  
    
    // Setup month and year totals
    
    $windmonth = array();
    $monthmax = array (-100, -100, -100, -100, -100, -100, -100, -100, -100, -100, -100, -100) ; // Initial value for high wind for month
    $monthmaxgust = $monthmax ;  // Initial value for high gust for month    
    $monthdir = array (360, 360, 360, 360, 360, 360, 360, 360, 360, 360, 360, 360) ;  // Initial value for wind direction for month    
    $ytd = 0;
  
    // Cycle through the possible days 
        
    for ( $day = 0 ; $day < 31 ; $day++ ) 
        {
        echo '<tr><td class="reportdt" rowspan="1">' . ($day + 1) . '</td>';
        
        // Display each months values for that day
        
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) 
            {
            if ($maxdays[$mnt] < $day + 1 ) 
                {
                echo '<td class="noday" colspan="2" >&nbsp;</td>';
                }
            else 
                {
 // Average Wind speed
                if ( $raw[$mnt][0][$day][$rawlb['aws']] == "" || $raw[$mnt][0][$day][$rawlb['aws']] == "---" ) 
                    {
                    $put = "---";
                echo '<td class="reportday">' . $put . '</td>';
                    }
                else 
                    {
                    $put = $raw[$mnt][0][$day][$rawlb['aws']];
                    $windmonth[$mnt][0] = $windmonth[$mnt][0] + $put;
                    $windmonth[$mnt][1] = $windmonth[$mnt][1] + 1;
                    if ($put > $monthmax[$mnt])
                        {
                        $monthmax[$mnt] = $put; 
                        }
                    echo '<td class="'. ValueColor($put,$beau_scale) .'">'. sprintf($places, $put) . '</td>';    
                    }

  //  High Wind
            if ( $raw[$mnt][0][$day][$rawlb['hwind']] == "" || $raw[$mnt][0][$day][$rawlb['hwind']] == "---"  )
                {
                $put = "---";  
                echo '<td class="reportday">' . $put . '</td>';                   
                }
            else 
                {
                $put = $raw[$mnt][0][$day][$rawlb['hwind']];
                $windmonth[$mnt][2] = $windmonth[$mnt][2] + $put;
                $windmonth[$mnt][3] = $windmonth[$mnt][3] + 1;
                if ($put > $monthmaxgust[$mnt])
                    {
                    $monthmaxgust[$mnt] = $put; 
                    }
                    echo '<td class="'. ValueColor($put,$beau_scale) .'">'. sprintf($places, $put) . '</td>'; 
                }
                
                }
               
            }
               echo "</tr>\n";  
                                 
        }    
    
    // We are done with the daily numbers now lets show the totals 
   echo '<tr><td colspan="25" class="separator">&nbsp;</td></tr>'; 
   // Put month headings 
   echo '<tr><th class="labels">&nbsp;</th>'  ;
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th  colspan="2" class="labels">' . substr( $mnthname[ $raw[$i][1] ], 0, 3 ) . '</th>';     
        }  
           
   echo '</tr><tr><td class="reportttl">'.langtransstr('Month Avg').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($windmonth[$i][1] > 0)
            {
            $windspeed =  ($windmonth[$i][0] / $windmonth[$i][1] );
            echo '<td class="'. ValueColor($windspeed,$beau_scale) .'">'. sprintf($places, $windspeed) . '</td>'; 
            }
        else
            {
        echo '<td colspan="1" class="reportttl" >' . "---"  . '</td>';
            }
        
        if ($windmonth[$i][3] > 0)
            {
            $windspeed =  ($windmonth[$i][2] / $windmonth[$i][3] );
            echo '<td class="'. ValueColor($windspeed,$beau_scale) .'">'. sprintf($places, $windspeed) . '</td>';              
            }
        else
            {
        echo '<td colspan="1" class="reportttl" >' . "---"  . '</td>';
            }

        }
          echo '</tr>';
           
   echo '<tr><td class="reportttl">'.langtransstr('Month Highs').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($windmonth[$i][1] > 0)
            {
            $windspeed =  $monthmax[$i];
            echo '<td class="'. ValueColor($windspeed,$beau_scale) .'">'. sprintf($places, $windspeed) . '</td>';                              
            }
        else
            {
              echo '<td class="reportttl">' . "---"  . '</td>';
            }

        if ($windmonth[$i][3] > 0)
            {
            $windspeed = $monthmaxgust[$i];
            echo '<td class="'. ValueColor($windspeed,$beau_scale) .'">'. sprintf($places, $windspeed) . '</td>';                                             
            }
        else
            {
              echo '<td class="reportttl">---</td>';
            }            
        }
  echo '</tr>';   
}

//Calculate colors depending on value

function ValueColor($value,$values) {
    $limit = count($values);
    if ($value < $values[0]) {
      return 'levelb_0';
      } 
    for ($i = 1; $i < $limit ; $i++){
        if ($value <= $values[$i]) {
          return 'levelb_'.($i);
        }
    }
    return 'levelb_'.($limit);

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

function wind_degrees_to_label ($winddegree)
// Given the wind direction in degrees, return the text label
// for that value.  16 point compass
{
$windlabels = array ("N","NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S",
   "SSW","SW", "WSW", "W", "WNW", "NW", "NNW","N");
$windlabel = $windlabels[ (int) ($winddegree / 22.5) ];
return "$windlabel";
}

function wind_label_to_degrees ($windlabel)
// Given the wind direction label, return the degrees for that value.  16 point compass
{
// CHECK THAT IT NOT ALREADY IN DEGREES
$xyz = (strpos($windlabel,"~"));
if ($xyz === 0) {
    $abc = trim($windlabel,"~");
    return $abc ;
    }

$winddegrees = array (0, 22.5, 45, 67.5, 90, 112.5, 135, 157.5, 180, 202.5, 225, 247.5, 270, 292.5, 315, 337.7, 360);
$windlabels = array ("N","NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW","SW", "WSW", "W", "WNW", "NW", "NNW");
for ( $i = 0 ; $i < 16 ; $i++ )
    {
        if ($windlabel == $windlabels[$i]){
        $winddegree = $winddegrees[$i]; 
        return "$winddegree";
        }
    }   
}

function get_days_in_month($month, $year)
{
   return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year %400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}




############################################################################
# End of Page
############################################################################
?>
