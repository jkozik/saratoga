<?php
############################################################################
#
#   Module:     wxswindrunseason.php
#   Purpose:    Display a table of windrun data.
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
# 2011-11-9 3.5 Initial Release
# 2011-12-27 3.6 Added support for Multilingual and Cumulus, Weatherlink, VWS
# 2012-08-26 3.8 Added check for manually provided NOAA data in csv file format
############################################################################
require_once("Settings.php");
@include_once("common.php");
@include_once("wxreport-settings.php"); 
############################################################################
// Obtains Server's Self and protect it against XSS injection
$SITE['self'] = htmlentities( substr($_SERVER['PHP_SELF'], 0,  
    strcspn( $_SERVER['PHP_SELF'] , "\n\r") ), ENT_QUOTES ); 
$TITLE = $SITE['organ'] . " - Wind Run Summary";      
############################################################################
# Settings Unique to this script
############################################################################
$SITE['viewscr'] = 'sce';  // Password for View Source Function
$avg_wind_unit = 1; # 1 = mph, 2 = kmh, 3 = knots, 4 = m/s  Set to the units used in your NOAA reports 
$wind_run_unit = 1; # 1 = miles, 2 = kilometers  Set to the units you want displayed in the report
$increment_size = 1500; #  Increments between colorbands
$increments = 11;  # if set higher than 11, you will need to edit the css file to add the additional color levels.
                   # 28 is the max value allowed. Values greater than 13 must be even numbers.
                   # The number of possible colors is two more than the $increment values - one below the 1st increment
                   # and one after the last increment  
$set_values_manually = false; # Set to true if you want to set your own non-linear values
$manual_values = array(.25, .5, 1, 2, 3,6,12,18,24,36,60,75); # Only used if $set_values_manually is true.
$css_file = "wxreports.css" ;  # name of css file  
$round = true; # Set to true to round hours to nearest interger
// error_reporting(E_ALL);
//error_reporting(E_ALL ^ E_NOTICE);
############################################################################  
# End of user settings
############################################################################
$loc = $path_dailynoaa;            # Location of NOAA reports
$first_year_of_data = $first_year_of_noaadata;
$imagesDir = $SITE['imagesDir'];
$increment_values = array($increment_size);
$season_start = 12;
if ($season_start == 1){
    $range = "year";
} else {
    $range = "season";
}

for ( $i = 0; $i < $increments ; $i ++ )                                                    
    {
$increment_values[$i+1] = $increment_values[$i] + $increment_size;
    }
    
if ($set_values_manually == true){
    $increment_values = $manual_values;
    $increments = (count($manual_values))-1;
}

$increments = min($increments,28); // Max of 22 increments allowed

if ($increments > 13){             // If more than 13 increments, must be an even number 
    $increments = (floor($increments * 0.5) / 0.5);
} 

$colors = $increments + 1;   
          
// If first day of year/season, default to the previous year
    if (( date("n") == $season_start) AND (date("j") == 1) AND ($show_today != true))
    {
        $year = date("Y")-1;
    }
    else
    {
        $year = date("Y");
    }

$years = 1 + ($year - $first_year_of_data);    
   
if (( $range == "season") AND ($season_start > date("m") )){ 
        $year = $year - 1;
        $years = 2 + ($year - $first_year_of_data); 
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
if ($wind_run_unit == 1){
    $windrun_text = langtransstr('miles');
    $wind_conversions = array(24,14.91,27.62,53.69);  // mph, kmh, knots, m/s
    $wind_conversion = $wind_conversions[$avg_wind_unit-1];
} else {
    $windrun_text = langtransstr('kilometers');
    $wind_conversions = array(38.62,24,44.45,86.4);  // mph, kmh, knots, m/s
    $wind_conversion = $wind_conversions[$avg_wind_unit-1];    
} 
?>

<div id="main-copy">
    <div id="report">
<center><h1><?php echo langtransstr('Wind Run Seasonal Summary').' ('.$windrun_text.')' ?></h1>

</center><table><tr><td align="center">
<?php
    if ($show_today){
        echo langtransstr('Data last updated').' '.$date.' '.$time.'.';
    } else {        
        echo langtransstr('Note: Data is updated after midnight each day.');
    }  

@include("wxreportinclude.php");  // Creates the various buttons linking to other reports         
?>
</td></tr></table>        
<?php 
get_detail($first_year_of_data,$year,$years,$loc,$round,$range,$season_start); 
?>    

<span style="font-size: 12px;"><?php echo $incomplete ?></span><br /><br />
<table><tr><td class="infotext"><?php echo langtransstr('Wind run is a measurement of how much wind has passed a given point in a period of time. A wind blowing at five').' '. $windrun_text .' '.langtransstr('per hour for an entire day (24 hours) would give a wind run of 120').' '.$windrun_text.' '.langtransstr('for the day').'.' ?></td></tr></table>
    </div>
   <div class="dev"><?php echo $SITE['copy'] ?></div>
</div><!-- end main-copy -->

<?php
############################################################################
 @include("footer.php");
############################################################################
# Functions
############################################################################

function get_detail ($first_year_of_data,$year,$years,$loc,$round,$range,$season_start) {
    global $SITE, $increment_values, $show_today, $windruntoday, $colors, $mnthname, $wind_conversion, $seasonnames, $hemi;
if ($round == true) 
    $places = "%01.0f";  
else 
    $places = "%01.1f";
    
    // Collect the data 
                            
        for ( $y = 0; $y < $years ; $y ++ ) {
             $yx = $year - $y;         
              
        for ( $mx = 0; $mx < 12 ; $mx ++ ) {            
            $m = $season_start+$mx-1;
            if ($m > 11){
                $m = $m - 12;
                $yx = ($year-$y)+1; 
            } 
            
            if (($yx == $first_year_of_data) OR ($yx > $first_year_of_data)) {                          
            // Check for current year and current month         
             
           if ($yx == date("Y") && $m == ( date("n") - 1) &&((date("j") != 1 ) OR $show_today)){                              
                $current_month = 1; 
            } else {
                $current_month = 0;                                             
            }
            
$filename = get_noaa_filename($yx,$m,$SITE['WXsoftware'],$current_month);
         
            if ($current_month AND $show_today AND date("j")==1){              
                $raw[$y][1][$mx][1][0][9] = $windruntoday/$wind_conversion;                 
            } else {
                $raw[$y][1][$mx][1] = getnoaafile($loc . $filename,$yx,$m);
            }
            if ($current_month AND $show_today){                                
                $raw[$y][1][$mx][1][date("j")-1][9] = $windruntoday/$wind_conversion;                      
                    
                }
             }
        }                            
        }
                                                                                 
        
    // Output Table with information
    
    echo '<table>';  
    $seasondata = array();
    $year_data = array();     
    $ytd = 0;
    $monthly_maxes = $season_maxes = array(-1,-1,-1,-1,);
    $monthly_mins = $season_mins = array(10000,10000,10000,10000);
    $yearly_max = -1;
    $yearly_min = 100000;    
    $yearly_avg = 0;
    
        for ($yx = 0 ; $yx < $years ; $yx++ )  {       
        // Display each years values for that month
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) {             
         for ($day = 0 ; $day < 31 ; $day++ ){                            
                                             

        $temp = $wind_conversion * $raw[$yx][1][$mnt][1][$day][9]; //[8];
                if (( $temp != "") AND ( $temp != "-----")) {  
  					$monthlydata[$yx][$mnt][0] = $monthlydata[$yx][$mnt][0] + $temp;
					$monthlydata[$yx][$mnt][1] = $monthlydata[$yx][$mnt][1] + 1;
                }               

        }    // end day loop
/////////////////////////////
     
        $year_data[$yx][0] = $year_data[$yx][0] + $monthlydata[$yx][$mnt][0];
        $year_data[$yx][1] = $year_data[$yx][1] + $monthlydata[$yx][$mnt][1];   
        
        $season = (ceil(($mnt+1)/3))-1;
        $seasondata[$yx][$season][0] = $seasondata[$yx][$season][0] + $monthlydata[$yx][$mnt][0];
        $seasondata[$yx][$season][1] = $seasondata[$yx][$season][1] + $monthlydata[$yx][$mnt][1];
        if (($mnt+1) % 3 == 0) {
            if ($seasondata[$yx][$season][1] > 0){         
                if ($seasondata[$yx][$season][0]>$season_maxes[$season]){
                    $season_maxes[$season] = $seasondata[$yx][$season][0];
                    }
                    if ($seasondata[$yx][$season][0]<$season_mins[$season]){
                    $season_mins[$season] = $seasondata[$yx][$season][0];
                    }
        
            }
        }       
                          

        }  // end month loop

   
        if ($year_data[$yx][0] > $yearly_max){
            $yearly_max = $year_data[$yx][0];
        }
        if ($year_data[$yx][0] < $yearly_min){
            $yearly_min = $year_data[$yx][0];
        }            
 }   // end year loop
    
// We have all the info, now display it 
    // 
   $s_names = ($hemi == 1) ? $seasonnames : array($seasonnames[2],$seasonnames[3],$seasonnames[0],$seasonnames[1]);   
    echo '<tr><th class="tableheading" colspan="6">'.langtransstr('Wind Run').'</th></tr>'  ;
    echo '<tr><th class="labels" width="15%">'.langtransstr('Date').'</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 4 ; $i++ ) 
        {    
            echo '<th class="labels" width="15%">' . $s_names[$i] . '</th>';     
        }
    echo '<th class="labels" width="15%">',langtransstr('Year').'</th>';         
    echo '</tr>';        
  for ($y = 0; $y < $years; $y++)  {       
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
                if ($seasondata[$y][$i][1] > 0) {   
                $like_seasons[$i][0] = $like_seasons[$i][0] + $seasondata[$y][$i][0]; // Add all like season amounts
                $like_seasons[$i][1] = $like_seasons[$i][1] + 1; 
                } 
    }      
  }
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
    if ($like_seasons[$i][1] == 0){
        $avg = 0;
    } else {      
        $avg = round(($like_seasons[$i][0] / $like_seasons[$i][1]),2);
    }
    $year_avg = $year_avg + $avg;
    }      
  for ($y = 0; $y < $years; $y++)  { 
    if ($year_data[$y][1]>0) {    
    echo '<tr><td class="reportttl">';
    echo substr($mnthname[11],0,3).' '.($year-($y))." / ".substr($mnthname[10],0,3).' '.($year-($y-1));          
    echo '</td>';
    
    
   
    for ( $i = 0 ; $i < 4 ; $i++ )
        {
        $mth = $season_start +$i;
        $yearx = ($year-$y);
        $yi = $y;
 
        if ($seasondata[$yi][$i][1] > 0)
            {                    
                $days_of_data = $seasondata[$yi][$i][1];
                $days_in_season = get_days_in_season(($i), ($yearx+1));
                $ast = $asterik[($days_of_data == $days_in_season)];   
                $avg_all = round(($like_seasons[$i][0] / $like_seasons[$i][1]),2);  
                $cur_value = round($seasondata[$yi][$i][0],2);
                $trend = trends_gen_difference($cur_value, $avg_all, $i, $s_names);                              
     echo '<td class=" ' . ValueColor($cur_value,$increment_values).'"' . '>' . sprintf($places,$cur_value) . $ast.' '.$trend.'</td>';                 
                               

          
            } else {
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }
        }
                $days_of_data = $year_data[$y][1];
                if (($range == "season") AND ($season_start >2)){
                    $days_in_year = get_days_in_year(($year-$y)+1); 
                }
                else {                  
                    $days_in_year = get_days_in_year($year-$y);
                }
                $ast = $asterik[($days_of_data == $days_in_year)];
                $trend = trends_gen_difference($year_data[$y][0], $year_avg, 4, $s_names);                 
   echo '<td class="yeartotals">' . sprintf($places,($year_data[$y][0])) . $ast.' '.$trend.'</td>';                             
    echo '</tr>';
  }
  }

    echo '<tr><td class="separator" colspan="6" >&nbsp;</td></tr>';  


    echo '<tr><td class="reportttl">'.langtransstr('Max').'</td>';
    for ( $s = 0 ; $s < 4 ; $s++ ) { 
    if ($season_maxes[$s]==-1) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $max = $season_maxes[$s];
    echo '<td class=" ' . ValueColor($max,$increment_values).'"' . '>' . sprintf($places,($max)) .' </td>';  
    }
    }
    echo '<td class="yeartotals">' . sprintf($places,($yearly_max)) . ' </td>';    
    echo '</tr>';   
  
// Now calculate & display the averages 
    echo '<tr><td class="reportttl">'.langtransstr('Avg').'</td>';
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
    if ($like_seasons[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $avg = round(($like_seasons[$i][0] / $like_seasons[$i][1]),2);
    echo '<td class=" ' . ValueColor($avg,$increment_values).'"' . '>' . sprintf($places,($avg)) .' </td>';  
    }
    }
    echo '<td class="yeartotals">' . sprintf($places,($year_avg)) . ' </td>';    
    echo '</tr>';  
    
// Now calculate & display the minimum seasons
    echo '<tr><td class="reportttl">'.langtransstr('Min').'</td>';
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
    if ($like_seasons[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $min = $season_mins[$i];
    echo '<td class=" ' . ValueColor($min,$increment_values).'"' . '>' . sprintf($places,($min)) .' </td>';  
    }
    }
    echo '<td class="yeartotals">' . sprintf($places,($yearly_min)) . ' </td>';    
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
            echo '<tr><td class="levelb_1" >&lt;&nbsp;' . sprintf($places,$increment_values[$i]) . '</td>';
        } else {
            if (($j == 0) AND ($r > 0)){
            }
                echo '<td class="levelb_'.($band+1).'" > ' . sprintf($places,$increment_values[$i-1]) . " - " .sprintf($places,$increment_values[$i]) . '</td>';
                if (($j == $colorband_cols-1) AND ($r != $colorband_rows)){
                    echo '</tr><tr>';
                    
                } 
        }
        $i = $i+1;
        
        }
    }        

    echo '<td class="levelb_'.($band+2).'" >'. sprintf($places,$increment_values[$i-1]) . '&gt;</td>';   
    echo '</tr>';       
       
   echo '</table>';
}

##################################

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

function get_days_in_season($season, $year)
{
    if ($season > 0){
        $days = 92 - ($season == 3);
    } else {
        $days = date("z", mktime(0,0,0,12,31,$year)) + 1;
        $leap_year = ($days == 366);
        $days = 90 + $leap_year;
    }

    return $days;
}

function get_days_in_year($year)
{
return date("z", mktime(0,0,0,12,31,$year)) + 1;
}

function roundoff($put, $round)
{
    if ($round == false)
        return $put;
    else     
        return  round($put,0);
}

//  generate an up/down arrow to show differences

 function trends_gen_difference( $nowTemp, $yesterTemp, $i, $s_names) {
  global $imagesDir;
  global $show_trends;
  global $round, $mnthname;
  $s_names[4] = 'yearly';  
  $wettext = '%s '. langtransstr('more than the').' '. $s_names[$i].' '.langtransstr('average').'.' ;
  $drytext = '%s '.langtransstr('less than the').' '. $s_names[$i].' '.langtransstr('average').'.' ; 
  if ($round == true)
    $places = 0;
  else
    $places = 1;    
  $diff = round(($nowTemp - $yesterTemp),$places) ; 
  $diff = number_format($diff,$places);
  $absDiff = abs($diff);
 
  if ($diff == 0 OR $show_trends !== true) {
     // no change
     $image = '&nbsp;'; 
     }
  elseif ($diff > 0) {
    //  is greater 
    $msg = sprintf($wettext,$absDiff); 
    $image = "<img src=\"${imagesDir}rising.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 0px 0px;\" />";
    }
  else {
    //  is lesser
    $msg = sprintf($drytext,$absDiff); 
    $image = "<img src=\"${imagesDir}falling.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 0px 0px;\" />";
    }

       return $image;    
}

############################################################################
# End of Page
############################################################################
?>