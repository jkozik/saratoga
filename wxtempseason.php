<?php
############################################################################
#
#   Module:     wxtempseason.php
#   Purpose:    Display a table of temperature data.
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
############################################################################
# Settings Unique to this script
############################################################################
$TITLE = $SITE['organ'] . " - Temperature Summary";
// Password for View Source Function    
$SITE['viewscr'] = 'sce';
$range_start = array(0, -15); # (Farenheit, Celcius) Starting point
$increment = array(10, 5); # (Farenheit, Celcius) Increments between colorbands
$increments = 11;  # if set higher than 11, you will need to edit the css file to add the additional color levels.
                   # 28 is the max value allowed. Values greater than 13 must be even numbers.
                   # The number of possible colors is two more than the $increment values - one below the 1st increment
                   # and one after the last increment  
$set_values_manually = false; # Set to true if you want to set your own non-linear values
$manual_values = array(.25, .5, 1, 2, 3,6,12,18,24,36,60,75); # Only used if $set_values_manually is true.
$round = false;    # Set to true to round to the nearest degree 
$css_file = "wxreports.css" ;  # name of css file 
############################################################################  
# End of user settings
############################################################################
$imagesDir = $SITE['imagesDir'];
$loc = $path_dailynoaa;            # Location of dailynoaareport*.html 
$start_year = $first_year_of_data = $first_year_of_noaadata;
$start_month = $start_day = 1;
$season_start = 12; 
$uomTemp = $SITE['uomTemp'];        
$increment = $increment[$uomTemp != "&deg;F"]; 
$increment_values = array($range_start[$uomTemp != "&deg;F"]);
for ( $i = 0; $i < $increments ; $i ++ ) {                                              
    $increment_values[$i+1] = $increment_values[$i] + $increment;
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
if ($season_start == 1)
    $range = "year";
else { 
    $range = "season"; 
}
           
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
?>

<div id="main-copy">
    <div id="report">
<center><h1>
<?php 
langtrans('Temperature Seasonal Summary');
echo ' ('.$uomTemp.')'; 
?>
</h1>

</center><table><tr><td align="center">
<?php
    if ($show_today){
        langtrans('Data last updated');
        echo ' '.$date.' '.$time.'.';
    } else { 
        langtrans('Note: Data is updated after midnight each day.');           
    }  

@include("wxreportinclude.php");  // Creates the various buttons linking to other reports   
?>
</td></tr></table>        
<?php 
get_detail($first_year_of_data,$year,$years,$loc,$round,$range,$season_start);
?>    
    </div>
<span style="font-size: 12px;">
<?php 
echo $incomplete;
echo $SITE['copy'];
echo '</div>';

############################################################################
 @include("footer.php");
############################################################################
# Functions
############################################################################

function get_detail ($first_year_of_data,$year,$years,$loc,$round,$range,$season_start) {
    global $SITE, $increment_values, $show_today, $maxtemp, $mintemp, $avtempsincemidnight, $colors, $year, $start_month, $start_day, $mnthname, $seasonnames, $hemi;

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
            
            if ((($yx == $first_year_of_data) AND ($m >= $start_month-1)) OR ($yx > $first_year_of_data)) {                          
            // Check for current year and current month 
            
           if ($yx == date("Y") && $m == ( date("n") - 1) &&((date("j") != 1 ) OR $show_today)){
                $filename = ".htm";                               
                $current_month = 1; 
            } else { 
                $filename = "dailynoaareport" . ( $m + 1 ) . $yx . ".htm";
                $current_month = 0;                                              
            }
           $filename = get_noaa_filename($yx,$m,$SITE['WXsoftware'],$current_month);            
            if ($current_month AND $show_today AND date("j")==1){
                $maxdata[$y][1][$mx][1][0] = strip_units($maxtemp);
                $mindata[$y][1][$mx][1][0] = strip_units($mintemp);
                $meandata[$y][1][$mx][1][0] = strip_units($avtempsincemidnight);                                   
            } else {
                $rawdata = getnoaafile($loc . $filename, $yx,$m);
                 for ($i = 0 ; $i < 31 ; $i++ )  { 
                 $maxdata[$y][1][$mx][1][$i] = $rawdata[$i][2];
                 $mindata[$y][1][$mx][1][$i] = $rawdata[$i][4];
                 $meandata[$y][1][$mx][1][$i] = $rawdata[$i][1];                                  
                 }                
            }             
            

            if ($current_month AND $show_today){                                
                $maxdata[$y][1][$mx][1][date("j")-1] = strip_units($maxtemp);
                $mindata[$y][1][$mx][1][date("j")-1] = strip_units($mintemp);
                $meandata[$y][1][$mx][1][date("j")-1] = strip_units($avtempsincemidnight);                                
                    
                }
        }        
        }                            
        }
                                                                                 
        
    // Output Table with information
    
    echo '<table>';  
    $seasondata_max = $seasondata_min = $seasondata_mean = array();
    $year_data = array();     
    $ytd = 0;
    $season_maxes = array(-100,-100,-100,-100,);             // Maxes of all like seasons
    $season_mins = array(10000,10000,10000,10000);   // Mins of all like seasons
    $s_max = array();
    $s_min = array();      
    $yearly_max = -100;
    $yearly_min = 100000;    

    
        for ($yx = 0 ; $yx < $years ; $yx++ )  {       
    
        // Display each years values for that month
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) {                    
        $season = (ceil(($mnt+1)/3))-1;                    
         for ($day = 0 ; $day < 31 ; $day++ ){                            
                                             

        $temp = $maxdata[$yx][1][$mnt][1][$day]; 
        $b4_start = ($yx == $years-1) && ($mnt == $start_month) && ($day < $start_day-1) ;        
                if (( $temp != "") AND ( $temp!= "-----") AND ( $temp!= "X") AND ($b4_start == false)) {  
  					$monthlydata_max[$yx][$mnt][0] = $monthlydata_max[$yx][$mnt][0] + $temp;
					$monthlydata_max[$yx][$mnt][1] = $monthlydata_max[$yx][$mnt][1] + 1;
                    $seasondata_max[$yx][$season][0] = $seasondata_max[$yx][$season][0] + $temp;
                    $seasondata_max[$yx][$season][1] = $seasondata_max[$yx][$season][1] + 1;
                    if ($seasondata_max[$yx][$season][1] > 0){         
                        if ($temp>$s_max[$yx][$season]){
                            $s_max[$yx][$season] = $temp;
                        }
        
        }        
        }
        

        $temp = $mindata[$yx][1][$mnt][1][$day]; 
        $b4_start = ($yx == $years-1) && ($mnt == $start_month) && ($day < $start_day-1) ;        
                if (( $temp != "") AND ( $temp!= "-----") AND ( $temp!= "X") AND ($b4_start == false)) {  
                      $monthlydata_min[$yx][$mnt][0] = $monthlydata_min[$yx][$mnt][0] + $temp;
                    $monthlydata_min[$yx][$mnt][1] = $monthlydata_min[$yx][$mnt][1] + 1;
                    $seasondata_min[$yx][$season][0] = $seasondata_min[$yx][$season][0] + $temp;
                    $seasondata_min[$yx][$season][1] = $seasondata_min[$yx][$season][1] + 1;
                    if ($seasondata_min[$yx][$season][1] > 0){         
                        if (($s_min[$yx][$season]>$temp) OR ($s_min[$yx][$season] == '')){
                            $s_min[$yx][$season] = $temp;
        }
        
        }        
        }
        

        $temp = $meandata[$yx][1][$mnt][1][$day]; 
        $b4_start = ($yx == $years-1) && ($mnt == $start_month) && ($day < $start_day-1) ;        
                if (( $temp != "") AND ( $temp!= "-----") AND ( $temp!= "X")AND ($b4_start == false)) {  
                      $monthlydata_mean[$yx][$mnt][0] = $monthlydata_mean[$yx][$mnt][0] + $temp;
                    $monthlydata_mean[$yx][$mnt][1] = $monthlydata_mean[$yx][$mnt][1] + 1;
                    $seasondata_mean[$yx][$season][0] = $seasondata_mean[$yx][$season][0] + $temp;
                    $seasondata_mean[$yx][$season][1] = $seasondata_mean[$yx][$season][1] + 1;     
        }                

        }    // end day loop
  
        $year_data_max[$yx][0] = $year_data_max[$yx][0] + $monthlydata_max[$yx][$mnt][0];
        $year_data_max[$yx][1] = $year_data_max[$yx][1] + $monthlydata_max[$yx][$mnt][1]; 
        $year_data_min[$yx][0] = $year_data_min[$yx][0] + $monthlydata_min[$yx][$mnt][0];
        $year_data_min[$yx][1] = $year_data_min[$yx][1] + $monthlydata_min[$yx][$mnt][1];          
        $year_data_mean[$yx][0] = $year_data_mean[$yx][0] + $monthlydata_mean[$yx][$mnt][0];
        $year_data_mean[$yx][1] = $year_data_mean[$yx][1] + $monthlydata_mean[$yx][$mnt][1];             
        if (($mnt+1) % 3 == 0) {
            if ($seasondata_max[$yx][$season][1] > 0){         
                if ($s_max[$yx][$season]>$season_maxes[$season]){
                    $season_maxes[$season] = $s_max[$yx][$season];
        }        
                if ($s_min[$yx][$season]<$season_mins[$season]){
                    $season_mins[$season] = $s_min[$yx][$season];         
        }
        
            }
    }          
        
          
        }  // end month loop 

   
        if ($year_data_max[$yx][0] > $yearly_max){
            $yearly_max = $year_data_max[$yx][0];
        }
        if ($year_data_min[$yx][0] < $yearly_min){
            $yearly_min = $year_data_min[$yx][0];
        }
 }   // end year loop
    
// We have all the info, now display the max temperatures
    // 
   $s_names = ($hemi == 1) ? $seasonnames : array($seasonnames[2],$seasonnames[3],$seasonnames[0],$seasonnames[1]); 

    echo '<tr><th class="tableheading" colspan="6">';
    langtrans('Highest Temperatures');
    echo '</th></tr>'  ;
    echo '<tr><th class="labels" width="15%">';
    langtrans('Date');
    echo '</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 4 ; $i++ ) 
        {    
            echo '<th class="labels" width="15%">' . $s_names[$i] . '</th>';     
        }
    echo '<th class="labels" width="15%">';
    langtrans('Year');
    echo '</th>';         
    echo '</tr>';        
      
  for ($y = 0; $y < $years; $y++)  {
    if ($year_data_max[$y][1]>0) {      
    echo '<tr><td class="reportttl">';
    echo substr($mnthname[11],0,3).' '.($year-($y))." / ".substr($mnthname[10],0,3).' '.($year-($y-1));          
    echo '</td>';
   
    for ( $i = 0 ; $i < 4 ; $i++ )
        {
        $mth = $season_start +$i;
        $yearx = ($year-$y);
        $yi = $y;
 
        if ($seasondata_max[$yi][$i][1] > 0)
            {                    
                $days_of_data = $seasondata_max[$yi][$i][1];
                $days_in_season = get_days_in_season(($i), ($yearx+1));
                $ast = $asterik[($days_of_data == $days_in_season)];     
                $cur_value = round($s_max[$yi][$i],2);                             
     echo '<td class=" ' . ValueColor($cur_value,$increment_values).'">' . sprintf($places,$cur_value) . $ast.'</td>';                       
            } else {
                echo '<td class="reportttl"  >' . "---"  . '</td>';
    } 
        }
                $days_of_data = $year_data_max[$y][1];
                if (($range == "season") AND ($season_start >2)){
                    $days_in_year = get_days_in_year(($year-$y)+1); 
                }
                else {                  
                    $days_in_year = get_days_in_year($year-$y);
    } 
                $ast = $asterik[($days_of_data == $days_in_year)];
               $year_max = max($s_max[$y]);                 
   echo '<td class=" ' . ValueColor($year_max,$increment_values).'">' . sprintf($places,($year_max)) . $ast.' </td>';                             
    echo '</tr>';
    }
    }

    echo '<tr><td class="reportttl">';
    langtrans('Max');
    echo '</td>';
    for ( $s = 0 ; $s < 4 ; $s++ ) { 
    if ($season_maxes[$s]==-100) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $max = $season_maxes[$s];
   echo '<td class=" ' . ValueColor($max,$increment_values).'">' . sprintf($places,($max)) .' </td>';  
    }
    }
    $max = max($season_maxes);    
   echo '<td class=" ' . ValueColor($max,$increment_values).'">' . sprintf($places,($max)) .' </td>';    
    echo '</tr>';     
    echo '<tr><td class="separator" colspan="6" >&nbsp;</td></tr>';     
    
// Now calculate & display the average high temps  
  for ($y = 0; $y < $years; $y++)  {   
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
                if ($seasondata_max[$y][$i][1] > 0) {   
                $like_seasons_max[$i][0] = $like_seasons_max[$i][0] + $seasondata_max[$y][$i][0]; // Add all like season amounts
                $like_seasons_max[$i][1] = $like_seasons_max[$i][1] + $seasondata_max[$y][$i][1]; 
                } 
    }       
  }

    
     for ( $i = 0 ; $i < 4 ; $i++ ) { 
        $average_data_max[0]=$average_data_max[0]+$like_seasons_max[$i][0];   
        $average_data_max[1]=$average_data_max[1]+$like_seasons_max[$i][1];
    }
    $all_years_avg = round(($average_data_max[0] / $average_data_max[1]),2);          

   echo '<tr><th class="tableheading" colspan="6">';
   langtrans('Average High Temperatures');
   echo '</th></tr>'  ;
    echo '<tr><th class="labels" width="15%">';
    langtrans('Date');
    echo '</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 4 ; $i++ ) 
        {    
            echo '<th class="labels" width="15%">' . $s_names[$i] . '</th>';     
    }      
    echo '<th class="labels" width="15%">';
    langtrans('Year');
    echo '</th>';         
    echo '</tr>';        
      
  for ($y = 0; $y < $years; $y++)  {
    if ($year_data_max[$y][1]>0) {       
    echo '<tr><td class="reportttl">';
    echo substr($mnthname[11],0,3).' '.($year-($y))." / ".substr($mnthname[10],0,3).' '.($year-($y-1));          
    echo '</td>';
   
    for ( $i = 0 ; $i < 4 ; $i++ )
        {
        $mth = $season_start +$i;
        $yearx = ($year-$y);
        $yi = $y;
 
        if ($seasondata_max[$yi][$i][1] > 0)
            { 
                $days_of_data = $seasondata_max[$yi][$i][1];
                $days_in_season = get_days_in_season(($i), ($yearx+1));
                $ast = $asterik[($days_of_data == $days_in_season)];   
                $avg_all = round(($like_seasons_max[$i][0] / $like_seasons_max[$i][1]),2);  
                $cur_value = round($seasondata_max[$yi][$i][0] / $seasondata_max[$yi][$i][1],2);
                $trend = trends_gen_difference($cur_value, $avg_all, $i, $s_names);                               
     echo '<td class=" ' . ValueColor($cur_value,$increment_values).'"' . '>' . sprintf($places,$cur_value) . $ast.' '.$trend.'</td>';                       
            } else {
                echo '<td class="reportttl"  >' . "---"  . '</td>';
            }
        }
                $days_of_data = $year_data_max[$y][1];
                if (($range == "season") AND ($season_start >2)){
                    $days_in_year = get_days_in_year(($year-$y)+1); 
                }
                else {                  
                    $days_in_year = get_days_in_year($year-$y);
                }
                $ast = $asterik[($days_of_data == $days_in_year)];
                $yearly_avg = round($year_data_max[$y][0]/$days_of_data,2);
                $trend = trends_gen_difference($yearly_avg, $all_years_avg, 4, $s_names);                 
   echo '<td class=" ' . ValueColor($yearly_avg,$increment_values).'">' . sprintf($places,($yearly_avg)) . $ast . $trend . ' </td>';                             
    echo '</tr>';
    }
  }

    echo '<tr><td class="reportttl">';
    langtrans('Avg');
    echo '</td>';
    for ( $s = 0 ; $s < 4 ; $s++ ) { 
    if ($season_maxes[$s]==-100) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
      $avg = round(($like_seasons_max[$s][0] / $like_seasons_max[$s][1]),2);
   echo '<td class=" ' . ValueColor($avg,$increment_values).'">' . sprintf($places,($avg)) .' </td>';  
    }
    } 
         
   echo '<td class=" ' . ValueColor($all_years_avg,$increment_values).'">' . sprintf($places,($all_years_avg)) .' </td>';    
    echo '</tr>';  
    echo '<tr><td class="separator" colspan="6" >&nbsp;</td></tr>';   
    
// Now calculate & display the mean temps  
  for ($y = 0; $y < $years; $y++)  {   
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
                if ($seasondata_mean[$y][$i][1] > 0) {   
                $like_seasons_mean[$i][0] = $like_seasons_mean[$i][0] + $seasondata_mean[$y][$i][0]; // Add all like season amounts
                $like_seasons_mean[$i][1] = $like_seasons_mean[$i][1] + $seasondata_mean[$y][$i][1]; 
                } 
    }       
  }

    
     for ( $i = 0 ; $i < 4 ; $i++ ) { 
        $average_data_mean[0]=$average_data_mean[0]+$like_seasons_mean[$i][0];   
        $average_data_mean[1]=$average_data_mean[1]+$like_seasons_mean[$i][1];
    }
    $all_years_avg = round(($average_data_mean[0] / $average_data_mean[1]),2);          

   echo '<tr><th class="tableheading" colspan="6">';
    langtrans('Mean Temperatures');
    echo '</th></tr>'  ;
    echo '<tr><th class="labels" width="15%">';
    langtrans('Date');
    echo '</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 4 ; $i++ ) 
        {    
            echo '<th class="labels" width="15%">' . $s_names[$i] . '</th>';     
    }      
    echo '<th class="labels" width="15%">Year</th>';         
    echo '</tr>';        
      
  for ($y = 0; $y < $years; $y++)  {
    if ($year_data_max[$y][1]>0) {       
    echo '<tr><td class="reportttl">';
    echo substr($mnthname[11],0,3).' '.($year-($y))." / ".substr($mnthname[10],0,3).' '.($year-($y-1));          
    echo '</td>';
   
    for ( $i = 0 ; $i < 4 ; $i++ )
        {
        $mth = $season_start +$i;
        $yearx = ($year-$y);
        $yi = $y;
 
        if ($seasondata_mean[$yi][$i][1] > 0)
            { 
                $days_of_data = $seasondata_mean[$yi][$i][1];
                $days_in_season = get_days_in_season(($i), ($yearx+1));
                $ast = $asterik[($days_of_data == $days_in_season)];   
                $avg_all = round(($like_seasons_mean[$i][0] / $like_seasons_mean[$i][1]),2);  
                $cur_value = round($seasondata_mean[$yi][$i][0] / $seasondata_mean[$yi][$i][1],2);
                $trend = trends_gen_difference($cur_value, $avg_all, $i, $s_names);                               
     echo '<td class=" ' . ValueColor($cur_value,$increment_values).'"' . '>' . sprintf($places,$cur_value) . $ast.' '.$trend.'</td>';                       
            } else {
                echo '<td class="reportttl"  >' . "---"  . '</td>';
            }
        }
                $days_of_data = $year_data_mean[$y][1];
                if (($range == "season") AND ($season_start >2)){
                    $days_in_year = get_days_in_year(($year-$y)+1); 
                }
                else {                  
                    $days_in_year = get_days_in_year($year-$y);
                }
                $ast = $asterik[($days_of_data == $days_in_year)];
                $yearly_avg = round($year_data_mean[$y][0]/$days_of_data,2);
                $trend = trends_gen_difference($yearly_avg, $all_years_avg, 4, $s_names);                 
   echo '<td class=" ' . ValueColor($yearly_avg,$increment_values).'">' . sprintf($places,($yearly_avg)) . $ast . $trend . ' </td>';                             
    echo '</tr>';
    }
  }

    echo '<tr><td class="reportttl">';
    langtrans('Avg');
    echo '</td>';
    for ( $s = 0 ; $s < 4 ; $s++ ) { 
    if ($season_maxes[$s]==-100) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
      $avg = round(($like_seasons_mean[$s][0] / $like_seasons_mean[$s][1]),2);
   echo '<td class=" ' . ValueColor($avg,$increment_values).'">' . sprintf($places,($avg)) .' </td>';  
    }
    } 
         
   echo '<td class=" ' . ValueColor($all_years_avg,$increment_values).'">' . sprintf($places,($all_years_avg)) .' </td>';    
    echo '</tr>';  
    echo '<tr><td class="separator" colspan="6" >&nbsp;</td></tr>';  
    
// Now calculate & display the average low temps  
  for ($y = 0; $y < $years; $y++)  {   
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
                if ($seasondata_min[$y][$i][1] > 0) {   
                $like_seasons_min[$i][0] = $like_seasons_min[$i][0] + $seasondata_min[$y][$i][0]; // Add all like season amounts
                $like_seasons_min[$i][1] = $like_seasons_min[$i][1] + $seasondata_min[$y][$i][1]; 
                } 
    }       
  }

    
     for ( $i = 0 ; $i < 4 ; $i++ ) { 
        $average_data_min[0]=$average_data_min[0]+$like_seasons_min[$i][0];   
        $average_data_min[1]=$average_data_min[1]+$like_seasons_min[$i][1];
    }
    $all_years_avg = round(($average_data_min[0] / $average_data_min[1]),2);          

   echo '<tr><th class="tableheading" colspan="6">';
   langtrans('Average Low Temperatures');
   echo '</th></tr>'  ;
    echo '<tr><th class="labels" width="15%">';
    langtrans('Date');
    echo '</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 4 ; $i++ ) 
        {    
            echo '<th class="labels" width="15%">' . $s_names[$i] . '</th>';     
    }      
    echo '<th class="labels" width="15%">';
    langtrans('Year');
    echo '</th>';         
    echo '</tr>';        
      
  for ($y = 0; $y < $years; $y++)  {
    if ($year_data_max[$y][1]>0) {       
    echo '<tr><td class="reportttl">';
    echo substr($mnthname[11],0,3).' '.($year-($y))." / ".substr($mnthname[10],0,3).' '.($year-($y-1));          
    echo '</td>';
   
    for ( $i = 0 ; $i < 4 ; $i++ )
        {
        $mth = $season_start +$i;
        $yearx = ($year-$y);
        $yi = $y;
 
        if ($seasondata_min[$yi][$i][1] > 0)
            { 
                $days_of_data = $seasondata_min[$yi][$i][1];
                $days_in_season = get_days_in_season(($i), ($yearx+1));
                $ast = $asterik[($days_of_data == $days_in_season)];   
                $avg_all = round(($like_seasons_min[$i][0] / $like_seasons_min[$i][1]),2);  
                $cur_value = round($seasondata_min[$yi][$i][0] / $seasondata_min[$yi][$i][1],2);
                $trend = trends_gen_difference($cur_value, $avg_all, $i, $s_names);                               
     echo '<td class=" ' . ValueColor($cur_value,$increment_values).'"' . '>' . sprintf($places,$cur_value) . $ast.' '.$trend.'</td>';                       
            } else {
                echo '<td class="reportttl"  >' . "---"  . '</td>';
            }
        }
                $days_of_data = $year_data_min[$y][1];
                if (($range == "season") AND ($season_start >2)){
                    $days_in_year = get_days_in_year(($year-$y)+1); 
                }
                else {                  
                    $days_in_year = get_days_in_year($year-$y);
                }
                $ast = $asterik[($days_of_data == $days_in_year)];
                $yearly_avg = round($year_data_min[$y][0]/$days_of_data,2);
                $trend = trends_gen_difference($yearly_avg, $all_years_avg, 4, $s_names);                 
   echo '<td class=" ' . ValueColor($yearly_avg,$increment_values).'">' . sprintf($places,($yearly_avg)) . $ast . $trend . ' </td>';                             
    echo '</tr>';
    }
  }

    echo '<tr><td class="reportttl">';
    langtrans('Avg');
    echo '</td>';
    for ( $s = 0 ; $s < 4 ; $s++ ) { 
    if ($season_maxes[$s]==-100) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
      $avg = round(($like_seasons_min[$s][0] / $like_seasons_min[$s][1]),2);
   echo '<td class=" ' . ValueColor($avg,$increment_values).'">' . sprintf($places,($avg)) .' </td>';  
    }
    } 
         
   echo '<td class=" ' . ValueColor($all_years_avg,$increment_values).'">' . sprintf($places,($all_years_avg)) .' </td>';    
    echo '</tr>';  
    echo '<tr><td class="separator" colspan="6" >&nbsp;</td></tr>';                   
       
// Now calculate & display the minimum seasons
     echo '<tr><th class="tableheading" colspan="6">';
     langtrans('Lowest Temperatures');
     echo '</th></tr>'  ;
    echo '<tr><th class="labels" width="15%">';
    langtrans('Date');
    echo '</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 4 ; $i++ ) 
        {    
            echo '<th class="labels" width="15%">' . $s_names[$i] . '</th>';     
        }
    echo '<th class="labels" width="15%">';
    langtrans('Year');
    echo '</th>';         
    echo '</tr>';        
  for ($y = 0; $y < $years; $y++)  {
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
                if ($seasondata_min[$y][$i][1] > 0) {   
                $like_seasons_min[$i][0] = $like_seasons_min[$i][0] + $seasondata_min[$y][$i][0]; // Add all like season amounts
                $like_seasons_min[$i][1] = $like_seasons_min[$i][1] + 1; 
                } 
    }
    }
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
    if ($like_seasons_min[$i][1] == 0){
        $avg = 0;
    } else {  
        $avg = round(($like_seasons_min[$i][0] / $like_seasons_min[$i][1]),2);
    }

    }      
  for ($y = 0; $y < $years; $y++)  {
    if ($year_data_max[$y][1]>0) {    
    echo '<tr><td class="reportttl">';
    echo substr($mnthname[11],0,3).' '.($year-($y))." / ".substr($mnthname[10],0,3).' '.($year-($y-1));          
    echo '</td>';
    
   
    for ( $i = 0 ; $i < 4 ; $i++ )
        {
        $mth = $season_start +$i;
        $yearx = ($year-$y);
        $yi = $y;
 
        if ($seasondata_min[$yi][$i][1] > 0)
            {                    
                $days_of_data = $seasondata_min[$yi][$i][1];
                $days_in_season = get_days_in_season(($i), ($yearx+1));
                $ast = $asterik[($days_of_data == $days_in_season)];    
                $cur_value = round($s_min[$yi][$i],2);                            
     echo '<td class=" ' . ValueColor($cur_value,$increment_values).'">' . sprintf($places,$cur_value) .'</td>';                     

            } else {
                echo '<td class="reportttl"  >' . "---"  . '</td>';
            }
                } 
                $days_of_data = $year_data_min[$y][1];
                if (($range == "season") AND ($season_start >2)){
                    $days_in_year = get_days_in_year(($year-$y)+1); 
        }
                else {                  
                    $days_in_year = get_days_in_year($year-$y);
                }
                $ast = $asterik[($days_of_data == $days_in_year)];
               $min = min($s_min[$y]);                 
   echo '<td class=" ' . ValueColor($min,$increment_values).'">' . sprintf($places,($min)) .' </td>';                             
    echo '</tr>';
    }       
    }
    
    echo '<tr><td class="reportttl">';
    langtrans('Min');
    echo '</td>';
    for ( $s = 0 ; $s < 4 ; $s++ ) { 
    if ($season_mins[$s]==10000) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $min = $season_mins[$s];
   echo '<td class=" ' . ValueColor($min,$increment_values).'">' . sprintf($places,($min)) .' </td>';  
    }
    }
    $min = min($season_mins);    
   echo '<td class=" ' . ValueColor($min,$increment_values).'">' . sprintf($places,($min)) .' </td>';    
    echo '</tr>';
  
$colorband_rows = ceil($colors/15);
$colorband_cols = ceil(($colors+1)/$colorband_rows);   

      echo '</table><table><tr><td class="separator" colspan="'.($colorband_cols).'" >&nbsp;</td></tr>';    
    echo '<tr><td class="colorband" colspan="'.($colorband_cols).'">';
    langtrans('Color Key');
    echo '</td></tr>';
    $i = 0;
    for ($r = 0; $r < $colorband_rows; $r ++){  
        for ( $j = 0; (($j < $colorband_cols) AND ($i < $colors)) ; $j ++ ){
        $band = $i;

        if ($i == 0){     
            echo '<tr><td class="level_1" >&lt;&nbsp;' . sprintf($places,$increment_values[$i]) . '</td>';
        } else {
            if (($j == 0) AND ($r > 0)){
             //   echo '<tr>';
            }
                echo '<td class="level_'.($band+1).'" > ' . sprintf($places,$increment_values[$i-1]) . " - " .sprintf($places,$increment_values[$i]) . '</td>';
                if (($j == $colorband_cols-1) AND ($r != $colorband_rows)){
                    echo '</tr><tr>';
                    
                } 
        }
        $i = $i+1;
        
        }
    }

    echo '<td class="level_'.($band+2).'" >'. sprintf($places,$increment_values[$i-1]) . '&gt;</td>';
    echo '</tr>';
  
   echo '</table>';
}

##################################

//Calculate colors depending on value

function ValueColor($value) {
    global $increment_values;
    $limit = count($increment_values);
    if ($value < $increment_values[0]) {
      return 'level_1';
      } 
    for ($i = 1; $i < $limit; $i++){
        if ($value <= $increment_values[$i]) {
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
  global $round, $mnthname, $places;
  $s_names[4] = 'yearly';  
  $hottext = '%s '. langtransstr('more than the').' '. $s_names[$i].' '.langtransstr('average').'.' ;
  $coldtext = '%s '.langtransstr('less than the').' '. $s_names[$i].' '.langtransstr('average').'.' ;
  if ($round == true)
    $places = 0;
  else
    $places = max(1,$places);    
  $diff = round(($nowTemp - $yesterTemp),$places) ; 
  $diff = number_format($diff,$places);
  $absDiff = abs($diff);
 
  if ($diff == 0 OR $show_trends !== true) {
     // no change
     $image = '&nbsp;'; 
     }
  elseif ($diff > 0) {
    //  is greater 
    $msg = sprintf($hottext,$absDiff); 
    $image = "<img src=\"${imagesDir}rising.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 0px 0px;\" />";
    }
  else {
    //  is lesser
    $msg = sprintf($coldtext,$absDiff); 
    $image = "<img src=\"${imagesDir}falling.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 0px 0px;\" />";
    }

       return $image;    
}

############################################################################
# End of Page
############################################################################
?>
