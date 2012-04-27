<?php
############################################################################
#
#   Module:     wxdegreesummary.php
#   Purpose:    Display a table of degree days data.
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
############################################################################
require_once("Settings.php");
@include_once("common.php");
@include_once("wxreport-settings.php"); 
############################################################################
// Obtains Server's Self and protect it against XSS injection
$SITE['self'] = htmlentities( substr($_SERVER['PHP_SELF'], 0,  
    strcspn( $_SERVER['PHP_SELF'] , "\n\r") ), ENT_QUOTES ); 
$TITLE = $SITE['organ'] . " - ".langtransstr("Degree Days Summary");    
############################################################################
# Settings Unique to this script
############################################################################
 
$SITE['viewscr'] = 'sce';  // Password for View Source Function

$base_temp_heating = array(65,18);      # (Farenheit, Celcius) Base temperature used for calculating degree days. 
$base_temp_cooling = array(65,18);                                     
$increment_size = 300; #  Increments between colorbands
$increments = 11;  # if set higher than 11, you will need to edit the css file to add the additional color levels.
                   # 28 is the max value allowed. Values greater than 13 must be even numbers.
                   # The number of possible colors is two more than the $increment values - one below the 1st increment
                   # and one after the last increment  
$set_values_manually = false; # Set to true if you want to set your own non-linear values
$manual_values = array(.25, .5, 1, 2, 3,6,12,18,24,36,60,75); # Only used if $set_incrementvalues_manually is true.
$css_file = "wxreports.css" ;  # name of css file 
$round = false;    # Set to true to round to the nearest degree day
############################################################################  
# End of user settings
############################################################################
$loc = $path_dailynoaa;            # Location of NOAA reports
$first_year_of_data = $first_year_of_noaadata;
$start_year = $first_year_of_data; 
$start_month = "1";                   
$start_day = "1";                     
$imagesDir = $SITE['imagesDir'];
$temptype = array("C","F") ;
$temptype = $temptype[$SITE['uomTemp'] == "&deg;F"];
$base_temp_heating = $base_temp_heating[$temptype == "C"];
$base_temp_cooling = $base_temp_cooling[$temptype == "C"];
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
$first_year_of_data = max($first_year_of_data,$start_year);           
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
 <?php
 if ($base_temp_cooling == $base_temp_heating) {
  echo '<center><h1>'.langtransstr('Degree Days Seasonal Summary').' ('.$base_temp_cooling.$uomTemp.' '.langtransstr('Base').')</h1>';
 } else {
  echo '<center><h1>'.langtransstr('Degree Days Seasonal Summary').'</h1>';     
 }
    
?>
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
$info_text = langtransstr('A Degree Day is a unit of measurement equal to a difference of one degree between the mean outdoor temperature and a reference temperature').' ('; 
if ($base_temp_cooling == $base_temp_heating){
    $info_text = $info_text . $base_temp_cooling . $uomTemp;
} else {
    $info_text = $info_text . $base_temp_cooling . $uomTemp . ' '.langtransstr('for cooling degrees and').' '.$base_temp_heating . $uomTemp . ' '.langtransstr('for heating degrees');     
}
$info_text = $info_text . '). '.langtransstr('Degree Days are used in estimating the energy needs for heating or cooling a building.');
?>    
    
<span style="font-size: 12px;"><?php echo $incomplete ?></span><br /><br />
<table><tr><td class="infotext"><?php echo $info_text ?></td></tr></table>
</div>
<?php echo '<div class="dev">' . $SITE['copy'] . '</div>'; ?> 
</div><!-- end main-copy -->

<?php
############################################################################
 @include("footer.php");
############################################################################
# Functions
############################################################################

function get_detail ($first_year_of_data,$year,$years,$loc,$round,$range,$season_start) {
    global $SITE, $increment_values, $show_today, $hddday, $cddday, $colors,$start_year, $start_month, $start_day, $mnthname, $seasonnames, $hemi;
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
                $current_month = 1; 
            } else {
                $current_month = 0;                                             
            }
$filename = get_noaa_filename($yx,$m,$SITE['WXsoftware'],$current_month);            
         
            if ($current_month AND $show_today AND date("j")==1){              
                $hraw[$y][1][$mx][1][0][6] = $hddday; 
                $craw[$y][1][$mx][1][0][7] = $cddday;                
            } elseif (file_exists($loc . $filename) ) {
                $hraw[$y][1][$mx][1] = getnoaafile($loc . $filename);
                $craw[$y][1][$mx][1] = getnoaafile($loc . $filename);                
            }
            if ($current_month AND $show_today){                                
                $hraw[$y][1][$mx][1][date("j")-1][6] = $hddday; 
                $craw[$y][1][$mx][1][date("j")-1][7] = $cddday;                     
                    
                }
        }        
        }                            
        }
                                                                                 
 
    $hmonthlydata = $hdatayear = $cmonthlydata = $cdatayear = array();
    $hseason_maxes = $cseason_maxes = array(-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1);
    $hseason_mins = $cseason_mins = array(10000,10000,10000,10000,10000,10000,10000,10000,10000,10000,10000,10000);
    $max_cdd_year = $max_hdd_year = -1;
    $min_cdd_year = $min_hdd_year = 1000000;        
    
        for ($yx = 0 ; $yx < $years ; $yx++ )  {       
        // Display each years values for that month
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) {             
         for ($day = 0 ; $day < 31 ; $day++ ){                                                                         

        $b4_start = ($yx == $years-1) && ($mnt == $start_month-1) && ($day < $start_day-1) ;
         $temp = $hraw[$yx][1][$mnt][1][$day][6]; 
                if (( $temp != "") AND ( $temp != "-----") AND ($temp != "X") AND ($b4_start == false)) {  
  					$hmonthlydata[$yx][$mnt][0] = $hmonthlydata[$yx][$mnt][0] + $temp;
					$hmonthlydata[$yx][$mnt][1] = $hmonthlydata[$yx][$mnt][1] + 1;
                }
           
          $temp = $craw[$yx][1][$mnt][1][$day][7];     
                if (( $temp != "") AND ( $temp != "-----") AND ($temp != "X") AND ($b4_start == false)) {  
                    $cmonthlydata[$yx][$mnt][0] = $cmonthlydata[$yx][$mnt][0] + $temp;
                    $cmonthlydata[$yx][$mnt][1] = $cmonthlydata[$yx][$mnt][1] + 1;
                }                

        }    // end day loop
        
        if ($hmonthlydata[$yx][$mnt][1] > 0){
            $hmonthlydata[$yx][$mnt][2] = round(($hmonthlydata[$yx][$mnt][0]/$hmonthlydata[$yx][$mnt][1]),1) ;
            $hlikemonths[$mnt][0] = $hlikemonths[$mnt][0] + $hmonthlydata[$yx][$mnt][0];
            $hlikemonths[$mnt][1] = $hlikemonths[$mnt][1] + 1;
            if ($hmonthlydata[$yx][$mnt][0]>$hmonthly_maxes[$mnt]){
                $hmonthly_maxes[$mnt] = $hmonthlydata[$yx][$mnt][0];
            }
            if ($hmonthlydata[$yx][$mnt][0]<$hmonthly_mins[$mnt]){
                $hmonthly_mins[$mnt] = $hmonthlydata[$yx][$mnt][0];
            }
        } 
        if ($cmonthlydata[$yx][$mnt][1] > 0){
            $cmonthlydata[$yx][$mnt][2] = round(($cmonthlydata[$yx][$mnt][0]/$cmonthlydata[$yx][$mnt][1]),1) ;
            $clikemonths[$mnt][0] = $clikemonths[$mnt][0] + $cmonthlydata[$yx][$mnt][0];
            $clikemonths[$mnt][1] = $clikemonths[$mnt][1] + 1;
            if ($cmonthlydata[$yx][$mnt][0]>$cmonthly_maxes[$mnt]){
                $cmonthly_maxes[$mnt] = $cmonthlydata[$yx][$mnt][0];
            }
            if ($cmonthlydata[$yx][$mnt][0]<$cmonthly_mins[$mnt]){
                $cmonthly_mins[$mnt] = $cmonthlydata[$yx][$mnt][0];
            }            
        }            
        $hdatayear[$yx][0] = $hdatayear[$yx][0] + $hmonthlydata[$yx][$mnt][0];    // Total hdd per year
        $hdatayear[$yx][1] = $hdatayear[$yx][1] + $hmonthlydata[$yx][$mnt][1];    // Days of data
        $cdatayear[$yx][0] = $cdatayear[$yx][0] + $cmonthlydata[$yx][$mnt][0];    // Total cdd per year
        $cdatayear[$yx][1] = $cdatayear[$yx][1] + $cmonthlydata[$yx][$mnt][1];    
        
        $season = (ceil(($mnt+1)/3))-1;
        if ($hmonthlydata[$yx][$mnt][1] > 0){        
        $hseasondata[$yx][$season][0] = $hseasondata[$yx][$season][0] + $hmonthlydata[$yx][$mnt][0];
        $hseasondata[$yx][$season][1] = $hseasondata[$yx][$season][1] + 1;
        $hseasondata[$yx][$season][3] = $hseasondata[$yx][$season][3] + $hmonthlydata[$yx][$mnt][1];        
        $hlikeseasons[$season][0] = $hlikeseasons[$season][0] + $hmonthlydata[$yx][$mnt][0];
        }
        if ($cmonthlydata[$yx][$mnt][1] > 0){                 
        $cseasondata[$yx][$season][0] = $cseasondata[$yx][$season][0] + $cmonthlydata[$yx][$mnt][0];
        $cseasondata[$yx][$season][1] = $cseasondata[$yx][$season][1] + 1;
        $cseasondata[$yx][$season][3] = $cseasondata[$yx][$season][3] + $cmonthlydata[$yx][$mnt][1];        
        $clikeseasons[$season][0] = $clikeseasons[$season][0] + $cmonthlydata[$yx][$mnt][0];
        }
               
       
        if (($mnt+1) % 3 == 0) {
            if ($hseasondata[$yx][$season][1] > 0){         
                if ($hseasondata[$yx][$season][0]>$hseason_maxes[$season]){
                    $hseason_maxes[$season] = $hseasondata[$yx][$season][0];
                    }
                    if ($hseasondata[$yx][$season][0]<$hseason_mins[$season]){
                    $hseason_mins[$season] = $hseasondata[$yx][$season][0];
                    }
                    $hseasondata[$yx]{$season}[2] = $hseasondata[$yx]{$season}[0] / $hseasondata[$yx]{$season}[1] ;
                    $hlikeseasons[$season][1] = $hlikeseasons[$season][1] + 1; 
        
            }
            if ($cseasondata[$yx][$season][1] > 0){         
                if ($cseasondata[$yx][$season][0]>$cseason_maxes[$season]){
                    $cseason_maxes[$season] = $cseasondata[$yx][$season][0];
                    }
                    if ($cseasondata[$yx][$season][0]<$cseason_mins[$season]){
                    $cseason_mins[$season] = $cseasondata[$yx][$season][0];
                    }
                    $cseasondata[$yx]{$season}[2] = $cseasondata[$yx]{$season}[0] / $cseasondata[$yx]{$season}[1] ;
                    $clikeseasons[$season][1] = $clikeseasons[$season][1] + 1; 
        
            }            
        }                        

        }  // end month loop 
if ($hdatayear[$yx][1]>0){   
        if ($hdatayear[$yx][0] > $max_hdd_year ){
        $max_hdd_year = $hdatayear[$yx][0];
        }
        if ($hdatayear[$yx][0] < $min_hdd_year ){
        $min_hdd_year = $hdatayear[$yx][0];
        } 
} 
if ($cdatayear[$yx][1]>0){       
        if ($cdatayear[$yx][0] > $max_cdd_year ){
        $max_cdd_year = $cdatayear[$yx][0];
        }          
        if ($cdatayear[$yx][0] < $min_cdd_year ){
        $min_cdd_year = $cdatayear[$yx][0];
        }
}   
             
 }   // end year loop
 
// for ($y = 0; $y < $years; $y++)  {
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
                if ($hlikeseasons[$i][1] > 0) {   
                $hlikeseasons[$i][2] = round(($hlikeseasons[$i][0]/$hlikeseasons[$i][1]),1); // Get hdd averages of each season
                } 
                if ($clikeseasons[$i][1] > 0) {   
                $clikeseasons[$i][2] = round(($clikeseasons[$i][0]/$clikeseasons[$i][1]),1); // Get cdd averages of each season
                } 
    }       
//  } 
    
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
    $hyear_avg = $hyear_avg + $hlikeseasons[$i][2];   // Add the season averages to get the yearly average
    $cyear_avg = $cyear_avg + $clikeseasons[$i][2];    

    }
   
// We have all the info, now display it 
        
    // Output Table with information
   $s_names = ($hemi == 1) ? $seasonnames : array($seasonnames[2],$seasonnames[3],$seasonnames[0],$seasonnames[1]);     
    echo '<table>'; 

    echo '<tr><th rowspan="2" class="labels"  width="15%">'.langtransstr('Date').'</th>';
    echo '<th colspan="5" class="labelshdd" >'.langtransstr('Heating Degree Days').'</th></tr><tr>';
    $asterik = array("*","");
        for ( $i = 0 ; $i < 4 ; $i++ ) 
        {    
            echo '<th class="labels" width="15%">' . $s_names[$i] . '</th>';     
        }
    echo '<th class="labels" width="15%">'.langtransstr('Year').'</th>';         
    echo '</tr>';        
  
    
///////////////////////////////////     
          
  for ($y = 0; $y < $years; $y++)  { 
    if ($hdatayear[$y][1]>0) {    
    echo '<tr><td class="reportttl">';
    echo substr($mnthname[11],0,3).' '.($year-($y))." / ".substr($mnthname[10],0,3).' '.($year-($y-1));          
    echo '</td>';
   
    for ( $i = 0 ; $i < 4 ; $i++ )
        {
        $mth = $season_start +$i;
        $yearx = ($year-$y);          
        $yi = $y;
        
        if ($hseasondata[$yi][$i][1] > 0)
            {                    
                $hdays_of_data = $hseasondata[$yi][$i][3]; 
                $days_in_season = get_days_in_season(($i), ($yearx+1));
                $ast = $asterik[($hdays_of_data == $days_in_season)];
                $hseason_avg = $hlikeseasons[$i][2];     
                $hcurrent = round($hseasondata[$yi][$i][0],2);
                $trend = trends_gen_difference($hcurrent, $hseason_avg, $i,'hdd',$s_names);                             
     echo '<td class=" ' . ValueColor($hcurrent,$increment_values).'"' . '>' . sprintf($places,$hcurrent) . $ast.' '.$trend.'</td>';                 
                               

          
            }
        else
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }
                $hdays_of_data = $hdatayear[$y][1];
                if (($range == "season") AND ($season_start >2)){
                    $days_in_year = get_days_in_year(($year-$y)+1); 
                }
                else {                  
                    $days_in_year = get_days_in_year($year-$y);
                }
                $ast = $asterik[($hdays_of_data == $days_in_year)];
                $trend = trends_gen_difference($hdatayear[$y][0], $hyear_avg, 4,'hdd', $s_names);                 
   echo '<td class="yeartotals">' . sprintf($places,($hdatayear[$y][0])) . $ast.' '.$trend.'</td>';                             
   echo '</tr>';
  }
  }

   echo '<tr><td class="separator" colspan="6" >&nbsp;</td></tr>';
      
     
// Now calculate & display the Seasonal HDD Maximums 
    echo '<tr><td class="reportttl">'.langtransstr('Max').'</td>';
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
    if ($hlikeseasons[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $max = $hseason_maxes[$i];
    echo '<td class=" ' . ValueColor($max,$increment_values).'"' . '>' . sprintf($places,($max)) .' </td>';  
    }
    } 
    echo '<td class="yeartotals">' . sprintf($places,($max_hdd_year)) . ' </td>';       
    echo '</tr>';  
    
// Now calculate & display the hdd averages 
   echo '<tr><td class="reportttl">'.langtransstr('Avg').'</td>';
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
    if ($hlikeseasons[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $havg = round(($hlikeseasons[$i][2]),1); 
    echo '<td class=" ' . ValueColor($havg,$increment_values).'"' . '>' . sprintf($places,($havg)) .' </td>';
    $average_seasonal_average[0]=$average_season_average[0] + $havg; 
    $average_seasonal_average[1]=$average_seasonal_average[1] + 1;   
    }
    }
    echo '<td class="yeartotals">' . sprintf($places,$hyear_avg) . ' </td>';        
    echo '</tr>'; 
    
// Now calculate & display the Monthly Minimums
   echo '<tr><td class="reportttl">'.langtransstr('Min').'</td>';
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
    if ($hlikeseasons[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $min = $hseason_mins[$i];
    echo '<td class=" ' . ValueColor($min,$increment_values).'"' . '>' . sprintf($places,($min)) .' </td>';  
    }
    }
//    echo '<td class="yeartotals">' . sprintf($places,(min($hmonthly_mins))) . ' </td>'; 
    echo '<td class="yeartotals">' . sprintf($places,($min_hdd_year)) . ' </td>';        
    echo '</tr>';     
    
    
///////////////////////////////////////////////////////////////////////////
    echo '</table><table><tr><td class="separator" colspan="6">&nbsp;</td></tr>';  
    echo '</table><table><tr><td class="separator" colspan="6">&nbsp;</td></tr>';
    echo '<tr><th rowspan="2" class="labels"  width="15%">'.langtransstr('Date').'</th>';
    echo '<th colspan="5" class="labelscdd" >'.langtransstr('Cooling Degree Days').'</th></tr><tr>'; 
    
        for ( $i = 0 ; $i < 4 ; $i++ ) 
        {    
            echo '<th class="labels" width="15%">' . $s_names[$i] . '</th>';     
        }
    echo '<th class="labels" width="15%">'.langtransstr('Year').'</th>';         
    echo '</tr>'; 
           
  for ($y = 0; $y < $years; $y++)  {
      if ($cdatayear[$y][1]>0) {   
    echo '<tr><td class="reportttl">';
    echo substr($mnthname[11],0,3).' '.($year-($y))." / ".substr($mnthname[10],0,3).' '.($year-($y-1)); 
    echo '</td>';
   
    for ( $i = 0 ; $i < 4 ; $i++ )
        {
        $mth = $season_start +$i;
        $yearx = ($year-$y);
            
        $yi = $y;
        if ($cseasondata[$yi][$i][1] > 0)
            {                    
                $cdays_of_data = $cseasondata[$yi][$i][3];
                $days_in_season = get_days_in_season(($i), ($yearx+1));
                $ast = $asterik[($cdays_of_data == $days_in_season)];   
                $season_avg = $clikeseasons[$i][2];  
                $ccurrent = round($cseasondata[$yi][$i][0],2);
                $trend = trends_gen_difference($ccurrent, $season_avg, $i,'cdd', $s_names);                             
     echo '<td class=" ' . ValueColor($ccurrent,$increment_values).'"' . '>' . sprintf($places,$ccurrent) . $ast.' '.$trend.'</td>';   
            }
        else
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }
                $cdays_of_data = $cdatayear[$y][1];
                if (($range == "season") AND ($season_start >2)){
                    $days_in_year = get_days_in_year(($year-$y)+1); 
                }
                else {                  
                    $days_in_year = get_days_in_year($year-$y);
                }
                $ast = $asterik[($cdays_of_data == $days_in_year)];
                $trend = trends_gen_difference($cdatayear[$y][0], $cyear_avg, 4,'cdd',$s_names);                 
   echo '<td class="yeartotals">' . sprintf($places,($cdatayear[$y][0])) . $ast.' '.$trend.'</td>';                             
    echo '</tr>';
  }
  }     
    
 // Now calculate & display the Season CDD Maximums 
    echo '<tr><td class="separator" colspan="6">&nbsp;</td></tr>';
   echo '<tr><td class="reportttl">'.langtransstr('Max').'</td>';
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
    if ($clikeseasons[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $max = $cseason_maxes[$i];
    echo '<td class=" ' . ValueColor($max,$increment_values).'"' . '>' . sprintf($places,($max)) .' </td>';  
    }
    }
    echo '<td class="yeartotals">' . sprintf($places,($max_cdd_year)) . ' </td>';    
    echo '</tr>';  
    
// Now calculate & display the cdd averages 
   echo '<tr><td class="reportttl">'.langtransstr('Avg').'</td>';
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
    if ($clikeseasons[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $cavg = round(($clikeseasons[$i][2]),1);
    echo '<td class=" ' . ValueColor($cavg,$increment_values).'"' . '>' . sprintf($places,($cavg)) .' </td>';
    }
    }
    echo '<td class="yeartotals">' . sprintf($places,($cyear_avg)) . ' </td>';   
    echo '</tr>'; 
    
// Now calculate & display the Season CDD Minimums
   echo '<tr><td class="reportttl">'.langtransstr('Min').'</td>';
    for ( $i = 0 ; $i < 4 ; $i++ ) { 
    if ($clikeseasons[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $min = $cseason_mins[$i];
    echo '<td class=" ' . ValueColor($min,$increment_values).'"' . '>' . sprintf($places,($min)) .' </td>';  
    }
    }
    echo '<td class="yeartotals">' . sprintf($places,($min_cdd_year)) . ' </td>';    
    echo '</tr>';     
    

//////////////////////////////////////////////////////////////////////////    
    
$colorband_rows = ceil($colors/15);
$colorband_cols = ceil(($colors+1)/$colorband_rows);   

      echo '</table><table><tr><td class="separator" colspan="'.($colorband_cols).'" >&nbsp;</td></tr>';    
    echo '<tr><td class="colorband" colspan="'.($colorband_cols).'">'.langtransstr('Color Key').'</td></tr>';
    $i = 0;
    for ($r = 0; $r < $colorband_rows; $r ++){  
        for ( $j = 0; (($j < $colorband_cols) AND ($i < $colors)) ; $j ++ ){
        $band = $i;

        if ($i == 0){     
            echo '<tr><td class="levelb_1" >&lt;&nbsp;' . sprintf("%01.0f",$increment_values[$i]) . '</td>';
        } else {
            if (($j == 0) AND ($r > 0)){
            }
                echo '<td class="levelb_'.($band+1).'" > ' . sprintf("%01.0f",$increment_values[$i-1]) . " - " .sprintf("%01.0f",$increment_values[$i]) . '</td>';
                if (($j == $colorband_cols-1) AND ($r != $colorband_rows)){
                    echo '</tr><tr>';
                    
                } 
        }
        $i = $i+1;
        
        }
    }        

    echo '<td class="levelb_'.($band+2).'" >'. sprintf("%01.0f",$increment_values[$i-1]) . '&gt;</td>';   
    echo '</tr>';       
       
   echo '</table>';

}
##################################

//Calculate colors depending on value

function ValueColor($value,$values) {
    $limit = count($values);
//        if ($value == 0){
//           return 'reportday';
//    }
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

 function trends_gen_difference( $nowTemp, $yesterTemp, $i,$hotcold,$s_names) {
  global $imagesDir;
  global $show_trends;
  global $round;
  $s_names[4] = 'yearly';   
  if ($hotcold == 'hdd'){
      $degree_type = 'heating degree days';
  } else {
      $degree_type = 'cooling degree days';
  }  
  $wettext = '%s '. langtransstr('more than the').' '. $s_names[$i].' '.langtransstr('average').'.' ;
  $drytext = '%s '.langtransstr('less than the').' '. $s_names[$i].' '.langtransstr('average').'.' ; 
  if ($round == true)
    $places = 0;
  else
    $places = 1;    
  $diff = round(($nowTemp - $yesterTemp),$places) ; 
 // $diff = number_format($diff,$places);
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