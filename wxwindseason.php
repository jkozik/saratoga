<?php
############################################################################
#
#   Module:     wxwindseason.php
#   Purpose:    Display a table of wind data.
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
############################################################################
// Obtains Server's Self and protect it against XSS injection
$SITE['self'] = htmlentities( substr($_SERVER['PHP_SELF'], 0,  
    strcspn( $_SERVER['PHP_SELF'] , "\n\r") ), ENT_QUOTES );
include_once("wxreport-settings.php");
$TITLE = $SITE['organ'] . " - ".langtransstr("Wind Season Summary");      
############################################################################
# Settings Unique to this script
############################################################################
$SITE['viewscr'] = 'sce';  // Password for View Source Function 
$start_year = "2010"; // Set to first year of wind data you have
$wind_unit = "mph";      # Set to mph, kmh, kts, or m/s
$css_file = "wxreports.css" ;  # name of css file 
$round = false;                # Set to true if you want windspeeds rounded to nearest integer  
############################################################################  
# End of user settings
############################################################################
$loc = $path_dailynoaa;            # Location of dailynoaareports
$first_year_of_data = $first_year_of_noaadata;
$first_year_of_data = max($first_year_of_data,$start_year);
$start_month = $start_day = 1;
$imagesDir = $SITE['imagesDir'];
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

$season_start = 12;
if ($season_start == 1){
    $range = "year";
} else {
    $range = "season";
}
 
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
<center><h1><?php echo langtransstr('Wind Seasonal Summary').' ('. trim($uomWind).')'; ?></h1></center>
<table><tr><td align="center">
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
echo $incomplete;
?>    
<br /><br />   
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
</div>
<?php echo $SITE['copy']; ?> 

</div><!-- end main-copy -->

<?php
############################################################################
 @include("footer.php");
############################################################################
# Functions
############################################################################

function get_detail ($first_year_of_data,$year,$years,$loc,$round,$range,$season_start) {
    global $SITE, $beau_scale, $wind_unit, $show_today, $maxgst, $avgspeedsincereset, $start_year, $start_month, $start_day, $mnthname, $seasonnames, $hemi;

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
                $gustdata[$y][1][$mx][1][0] = strip_units($maxgst);
                $winddata[$y][1][$mx][1][0] = strip_units($avgspeedsincereset);                                   
            } else {
                $rawdata = getnoaafile($loc . $filename,$yx,$m);
                 for ($i = 0 ; $i < 31 ; $i++ )  { 
                 $gustdata[$y][1][$mx][1][$i] = $rawdata[$i][10];
                 $winddata[$y][1][$mx][1][$i] = $rawdata[$i][9];                                  
                 }                
            }             
            

            if ($current_month AND $show_today){                                
                $gustdata[$y][1][$mx][1][date("j")-1] = strip_units($maxgst);
                $winddata[$y][1][$mx][1][date("j")-1] = strip_units($avgspeedsincereset);                                
                    
                }
        }        
        }                            
        }
                                                                                 
        
    // Output Table with information
    
    echo '<table>';  
    $seasondata_max = $seasondata_mean = array();
    $year_data = array();     
    $ytd = 0;
    $season_maxes = array(-100,-100,-100,-100,);             // Maxes of all like seasons
    $s_max = array();     
    $yearly_max = -100;
 

    
        for ($yx = 0 ; $yx < $years ; $yx++ )  {       
    
        // Display each years values for that month
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) {                    
        $season = (ceil(($mnt+1)/3))-1;                    
         for ($day = 0 ; $day < 31 ; $day++ ){                            
                                             

        $temp = $gustdata[$yx][1][$mnt][1][$day]; 
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
            

        $temp = $winddata[$yx][1][$mnt][1][$day]; 
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
        $year_data_mean[$yx][0] = $year_data_mean[$yx][0] + $monthlydata_mean[$yx][$mnt][0];
        $year_data_mean[$yx][1] = $year_data_mean[$yx][1] + $monthlydata_mean[$yx][$mnt][1];             
        if (($mnt+1) % 3 == 0) {
            if ($seasondata_max[$yx][$season][1] > 0){         
                if ($s_max[$yx][$season]>$season_maxes[$season]){
                    $season_maxes[$season] = $s_max[$yx][$season];
        }        
        
            }
    }          
        
          
        }  // end month loop 

   
        if ($year_data_max[$yx][0] > $yearly_max){
            $yearly_max = $year_data_max[$yx][0];
        }
 }   // end year loop
    
// We have all the info, now display the max wind speeds
    // 
   $s_names = ($hemi == 1) ? $seasonnames : array($seasonnames[2],$seasonnames[3],$seasonnames[0],$seasonnames[1]); 

    echo '<tr><th class="tableheading" colspan="6">'.langtransstr('Maximum Wind Speed').' ('.$wind_unit .')</th></tr>'  ;
    echo '<tr><th class="labels" width="15%">'.langtransstr('Date').'</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 4 ; $i++ ) 
        {    
            echo '<th class="labels" width="15%">' . $s_names[$i] . '</th>';     
        }
    echo '<th class="labels" width="15%">'.langtransstr('Year').'</th>';         
    echo '</tr>';        
      
  for ($y = 0; $y < $years; $y++)  {
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
                $cur_value = round($s_max[$yi][$i],1);                             
     echo '<td class=" ' . ValueColor($cur_value,$beau_scale).'">' . sprintf($places,$cur_value) . $ast.'</td>';                       
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
   echo '<td class=" ' . ValueColor($year_max,$beau_scale).'">' . sprintf($places,($year_max)) . $ast.' </td>';                             
    echo '</tr>';

    }

    echo '<tr><td class="reportttl">'.langtransstr('Max').'</td>';
    for ( $s = 0 ; $s < 4 ; $s++ ) { 
    if ($season_maxes[$s]==-100) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $max = $season_maxes[$s];
   echo '<td class=" ' . ValueColor($max,$beau_scale).'">' . sprintf($places,($max)) .' </td>';  
    }
    }
    $max = max($season_maxes);    
   echo '<td class=" ' . ValueColor($max,$beau_scale).'">' . sprintf($places,($max)) .' </td>';    
    echo '</tr>';     
    echo '<tr><td class="separator" colspan="6" >&nbsp;</td></tr>';     
    
// Now calculate & display the average high wind speeds 
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
    $all_years_avg = round(($average_data_max[0] / $average_data_max[1]),1);          

   echo '<tr><th class="tableheading" colspan="6">'.langtransstr('Average Maximum Wind Speed').' ('.$wind_unit .')</th></tr>'  ;
    echo '<tr><th class="labels" width="15%">'.langtransstr('Date').'</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 4 ; $i++ ) 
        {    
            echo '<th class="labels" width="15%">' . $s_names[$i] . '</th>';     
    }      
    echo '<th class="labels" width="15%">'.langtransstr('Year').'</th>';         
    echo '</tr>';        
      
  for ($y = 0; $y < $years; $y++)  {
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
                $avg_all = round(($like_seasons_max[$i][0] / $like_seasons_max[$i][1]),1);  
                $cur_value = round($seasondata_max[$yi][$i][0] / $seasondata_max[$yi][$i][1],1);
                $trend = trends_gen_difference($cur_value, $avg_all, $i, $s_names);                               
     echo '<td class=" ' . ValueColor($cur_value,$beau_scale).'"' . '>' . sprintf($places,$cur_value) . $ast.' '.$trend.'</td>';                       
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
                $yearly_avg = round($year_data_max[$y][0]/$days_of_data,1);
                $trend = trends_gen_difference($yearly_avg, $all_years_avg, 4, $s_names);                 
   echo '<td class=" ' . ValueColor($yearly_avg,$beau_scale).'">' . sprintf($places,($yearly_avg)) . $ast . $trend . ' </td>';                             
    echo '</tr>';

  }

    echo '<tr><td class="reportttl">'.langtransstr('Avg').'</td>';
    for ( $s = 0 ; $s < 4 ; $s++ ) { 
    if ($season_maxes[$s]==-100) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
      $avg = round(($like_seasons_max[$s][0] / $like_seasons_max[$s][1]),1);
   echo '<td class=" ' . ValueColor($avg,$beau_scale).'">' . sprintf($places,($avg)) .' </td>';  
    }
    } 
         
   echo '<td class=" ' . ValueColor($all_years_avg,$beau_scale).'">' . sprintf($places,($all_years_avg)) .' </td>';    
    echo '</tr>';  
    echo '<tr><td class="separator" colspan="6" >&nbsp;</td></tr>';   
    
// Now calculate & display the average wind speed  
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
    $all_years_avg = round(($average_data_mean[0] / $average_data_mean[1]),1);          

   echo '<tr><th class="tableheading" colspan="6">'.langtransstr('Average Wind Speed').' ('.$wind_unit .')</th></tr>'  ;
    echo '<tr><th class="labels" width="15%">'.langtransstr('Date').'</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 4 ; $i++ ) 
        {    
            echo '<th class="labels" width="15%">' . $s_names[$i] . '</th>';     
    }      
    echo '<th class="labels" width="15%">'.langtransstr('Year').'</th>';         
    echo '</tr>';        
      
  for ($y = 0; $y < $years; $y++)  {
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
                $avg_all = round(($like_seasons_mean[$i][0] / $like_seasons_mean[$i][1]),1);  
                $cur_value = round($seasondata_mean[$yi][$i][0] / $seasondata_mean[$yi][$i][1],1);
                $trend = trends_gen_difference($cur_value, $avg_all, $i, $s_names);                               
     echo '<td class=" ' . ValueColor($cur_value,$beau_scale).'"' . '>' . sprintf($places,$cur_value) . $ast.' '.$trend.'</td>';                       
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
                $yearly_avg = round($year_data_mean[$y][0]/$days_of_data,1);
                $trend = trends_gen_difference($yearly_avg, $all_years_avg, 4, $s_names);                 
   echo '<td class=" ' . ValueColor($yearly_avg,$beau_scale).'">' . sprintf($places,($yearly_avg)) . $ast . $trend . ' </td>';                             
    echo '</tr>';

  }

    echo '<tr><td class="reportttl">',langtransstr('Avg').'</td>';
    for ( $s = 0 ; $s < 4 ; $s++ ) { 
    if ($season_maxes[$s]==-100) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
      $avg = round(($like_seasons_mean[$s][0] / $like_seasons_mean[$s][1]),1);
   echo '<td class=" ' . ValueColor($avg,$beau_scale).'">' . sprintf($places,($avg)) .' </td>';  
    }
    } 
         
   echo '<td class=" ' . ValueColor($all_years_avg,$beau_scale).'">' . sprintf($places,($all_years_avg)) .' </td>';    
    echo '</tr>';  

     
   echo '</table>';
}

##################################

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
