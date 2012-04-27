<?php
############################################################################
#
#   Module:     wxwindsummary.php
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
# 2010-01-29 1.0 Inital Release 
# 2010-02-02 1.01 Added option to show links to other summary pages & css changes
# 2010-03-23 1.1 Added option to link to snow summary page
# 2010-03-29 1.2 Update to use rounding to determine closest beaufort number 
# 2010-09-07 2.0 Added option to include today's data from tags in testtags file
# 2010-11-01 2.1 Fix for first day of month when $show_today is true.
# 2010-11-27 3.0 Update to use common files between all summary and detail files
# 2011-05-09 3.15 Added option to round windspeed to nearest integer
# 2011-12-27 3.6 Added support for Multilingual and Cumulus, Weatherlink, VWS
############################################################################
require_once("Settings.php");
@include_once("common.php");
############################################################################
// Obtains Server's Self and protect it against XSS injection
$SITE['self'] = htmlentities( substr($_SERVER['PHP_SELF'], 0,  
    strcspn( $_SERVER['PHP_SELF'] , "\n\r") ), ENT_QUOTES );
include_once("wxreport-settings.php");
$TITLE = $SITE['organ'] . " - ".langtransstr("Wind Summary");      
############################################################################
# Settings Unique to this script
############################################################################
$SITE['viewscr'] = 'sce';  // Password for View Source Function 
$start_year = "2002"; // Set to first year of wind data you have
$wind_unit = "mph";      # Set to mph, kmh, kts, or m/s
$css_file = "wxreports.css" ;  # name of css file 
$round = false;                # Set to true if you want windspeeds rounded to nearest integer  
############################################################################
# End of user settings
############################################################################
$loc = $path_dailynoaa;            # Location of dailynoaareports
$first_year_of_data = $first_year_of_noaadata;
$first_year_of_data = max($first_year_of_data,$start_year);
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

// If first day of first month of the year, use previous year  
$year = date("Y");
if ($show_today != true){
    if (( date("n") == 1) AND date("j") == 1) {
    $year = $year -1;
    }
}     
 
$years = 1 + ($year - $first_year_of_data);
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
<center><h1><?php echo langtransstr('Wind Summary').' ('. trim($uomWind).')'; ?></h1></center>
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
get_wind_detail($first_year_of_data,$year,$years,$loc, $round); 
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

function get_wind_detail ($first_year_of_data,$year, $years, $loc, $round) {
    global $SITE;
    global $beau_scale;
    global $wind_unit, $show_today, $maxgst, $avgspeedsincereset, $mnthname;
    
if ($round == true)
    $places = "%01.0f";
else
    $places = "%01.1f"; 

    
    // Collect the data 
                            
        for ( $y = 0; $y < $years ; $y ++ ) {
             $yx = $year - $y;
             
        for ( $m = 0; $m < 12 ; $m ++ ) {            
            
            // Check for current year and current month         
            
           if ($yx == date("Y") && $m == ( date("n") - 1) &&((date("j") != 1 ) OR $show_today)){
                $filename = "dailynoaareport.htm";                               
                $current_month = 1; 
            } else { 
                $filename = "dailynoaareport" . ( $m + 1 ) . $yx . ".htm";
                $current_month = 0;                                              
            }
           $filename = get_noaa_filename($yx,$m,$SITE['WXsoftware'],$current_month);                      
            if ($current_month AND $show_today AND date("j")==1){              
                $raw[$y][1][$m][1][0][9] = strip_units($avgspeedsincereset);
                $raw[$y][1][$m][1][0][10] = strip_units($maxgst);                  
            } elseif (file_exists($loc . $filename) ) {
                $raw[$y][1][$m][1] = getnoaafile($loc . $filename);
            }
            if ($current_month AND $show_today){                                
                $raw[$y][1][$m][1][date("j")-1][9] = strip_units($avgspeedsincereset);
                $raw[$y][1][$m][1][date("j")-1][10] = strip_units($maxgst);                        
                    
                }
                     
        }                            
        }    
        
    // Output Table with information
    
    echo '<table>';  
    $windmonth = array();
    $windyear = array();     
    $ytd = 0;
    
        for ($yx = 0 ; $yx < $years ; $yx++ )  {       
        // Display each years values for that month
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) {             
         for ($day = 0 ; $day < 31 ; $day++ ){                            

        $wind = $raw[$yx][1][$mnt][1][$day][9];   // Average Wind Speed
                if ( $wind != "" AND $wind != "-----") {  
                      $windmonth[$yx][$mnt][0] = $windmonth[$yx][$mnt][0] + $wind;
                    $windmonth[$yx][$mnt][1] = $windmonth[$yx][$mnt][1] + 1;
                    $like_months[$mnt][0] = $like_months[$mnt][0] + $wind;
                    $like_months[$mnt][1] = $like_months[$mnt][1] + 1; 
                }                                             
        $wind = $raw[$yx][1][$mnt][1][$day][10];   // Max Wind Speed
                if ( $wind != "" AND $wind != "-----") {  
  					$windmonth[$yx][$mnt][2] = $windmonth[$yx][$mnt][2] + $wind;
					$windmonth[$yx][$mnt][3] = $windmonth[$yx][$mnt][3] + 1;
                    if ($wind > $maxgust[$yx][$mnt])    
                        $maxgust[$yx][$mnt] = $wind;
                    if ($wind > $monthmax[$mnt]) // Save max of like months
                        $monthmax[$mnt] = $wind; 
                    $like_months[$mnt][2] = $like_months[$mnt][2] + $wind;
                    $like_months[$mnt][3] = $like_months[$mnt][3] + 1;                           
                }               

        }    // end day loop
    
        $windyear[$yx][0] = $windyear[$yx][0] + $windmonth[$yx][$mnt][0];  // Average Wind
        $windyear[$yx][1] = $windyear[$yx][1] + $windmonth[$yx][$mnt][1];  // Average Wind days
        $windyear[$yx][2] = $windyear[$yx][2] + $windmonth[$yx][$mnt][2];  // Max wind
        $windyear[$yx][3] = $windyear[$yx][3] + $windmonth[$yx][$mnt][3];  // Max wind days                
        $allyears[0] = $allyears[0] + $windmonth[$yx][$mnt][0];
        $allyears[1] = $allyears[1] + $windmonth[$yx][$mnt][1]; 
        $allyears[2] = $allyears[2] + $windmonth[$yx][$mnt][2]; 
        $allyears[3] = $allyears[3] + $windmonth[$yx][$mnt][3]; 
        }  // end month loop    
 }   // end year loop
    
// We have all the info, now display it 
    //  Maximum Wind 
    echo '<tr><th class="tableheading" colspan="14">'.langtransstr('Maximum Wind Speed').' ('.$wind_unit .')</th></tr>'  ;
    echo '<tr><th class="labels" width="7%">'.langtransstr('Date').'</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th class="labels" width="7%">' . substr( $mnthname[$i], 0, 3 ) . '</th>';     
        }
    echo '<th class="labels" width="7%">'.langtransstr('Year').'</th>';         
    echo '</tr>';        
  for ($y = 0; $y < $years; $y++)  {
    
    echo '<tr><td class="reportttl">';
    echo $year-$y;       
    echo '</td>';
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($windmonth[$y][$i][3] > 0)
            {                    
                $days_of_data = $windmonth[$y][$i][3];
                $days_in_month = get_days_in_month(($i+1), ($year-$y));
                $ast = $asterik[($days_of_data == $days_in_month)];                 
                echo '<td class=" ' . ValueColor($maxgust[$y][$i],$beau_scale).'"' . '>' . sprintf($places,($maxgust[$y][$i])) . $ast.' </td>'; 
                $wind_months[$i][2] = $wind_months[$i][2] + $windmonth[$y][$i][2]; // Add all like month amounts
                $wind_months[$i][3] = $wind_months[$i][3] + $days_of_data;            
            }
        else
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }
                $days_of_data = $windyear[$y][3];
                if ($days_of_data > 0){               
                    $days_in_year = get_days_in_year($year-$y);
                    $ast = $asterik[($days_of_data == $days_in_year)];
                    $max = max($maxgust[$y]);
                    echo '<td class=" ' . ValueColor($max,$beau_scale).'"' . '>' . sprintf($places,($max)) . $ast.'</td>';
                }
                else {
                    echo '<td class="reportttl"  >' . "---"  . '</td>'; 
                    }                                            
    echo '</tr>';    
  }
// Now display the max of like months 
    echo '<tr><td class="reportttl">'.langtransstr('Max').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {        
    if ($wind_months[$i][3]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $max = $monthmax[$i];
    echo '<td class=" ' . ValueColor($max,$beau_scale).'"' . '>' . sprintf($places,($max)) .' </td>';  
     }
    }
    $max = max($monthmax);
    echo '<td class=" ' . ValueColor($max,$beau_scale).'"' . '>' . sprintf($places,($max)) . ' </td>';    
    echo '</tr>';
    
    // Average Maximum Wind
   for ($y = 0; $y < $years; $y++)  { 
      for ( $i = 0 ; $i < 12 ; $i++ )  {       
    $year_wind[$y][2] = $year_wind[$y][2] + $wind_months[$i][2];
    $year_days[$y][3] = $year_days[$y][3] + $wind_months[$i][3];
 
  }
   }       
    echo '<tr><td class="separator" colspan="14" >&nbsp;</td></tr>';     
    echo '<tr><th class="tableheading" colspan="14">'.langtransstr('Average Maximum Wind Speed').' ('.$wind_unit .')</th></tr>'  ;
    echo '<tr><th class="labels" width="7%">'.langtransstr('Date').'</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th class="labels" width="7%">' . substr( $mnthname[$i], 0, 3 ) . '</th>';     
        }
    echo '<th class="labels" width="7%">'.langtransstr('Year').'</th>';         
    echo '</tr>';    
           
  for ($y = 0; $y < $years; $y++)  {
    
    echo '<tr><td class="reportttl">';
    echo $year-$y;       
    echo '</td>';

    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($windmonth[$y][$i][3] > 0)
            {                    
                $days_of_data = $windmonth[$y][$i][3];
                $days_in_month = get_days_in_month(($i+1), ($year-$y));
                $ast = $asterik[($days_of_data == $days_in_month)];
                $cur_wind = $windmonth[$y][$i][2] / $days_of_data ;
                $avg = round(($like_months[$i][2] / $like_months[$i][3]),1);
                $trend = trends_gen_difference($cur_wind, $avg, 'Maximum', $i);                                 
                echo '<td class=" ' . ValueColor($cur_wind,$beau_scale).'"' . '>' . sprintf($places,$cur_wind) . $ast.' '.$trend.' </td>'; 
             }
        else {
                echo '<td class="reportttl"  >' . "---"  . '</td>';
             }
        }
                $days_of_data = $windyear[$y][3];
                if ($days_of_data > 0)
                {               
                    $days_in_year = get_days_in_year($year-$y);
                    $ast = $asterik[($days_of_data == $days_in_year)];
                    $cur_wind = $windyear[$y][2] / $windyear[$y][3]; 
                    $all_year_avg = round($allyears[2] / $allyears[3],1); 
                    $trend = trends_gen_difference($cur_wind, $all_year_avg, 'maximum', $i);                                 
                    echo '<td class=" ' . ValueColor($cur_wind,$beau_scale).'"' . '>' . sprintf($places,$cur_wind) . $ast.' '.$trend.' </td>';                     
                }
                else {
                    echo '<td class="reportttl"  >' . "---"  . '</td>'; 
                    }                                            
    echo '</tr>';
          
        }
  
// Now calculate & display the max wind averages
 
    echo '<tr><td class="reportttl">'.langtransstr('Avg').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) { 
      
    if ($like_months[$i][3]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $avg = $like_months[$i][2] / $like_months[$i][3];
    echo '<td class=" ' . ValueColor($avg,$beau_scale).'"' . '>' . sprintf($places,($avg)) .' </td>';  

    }
    }
    $all_year_avg = round($allyears[2] / $allyears[3],1);
    echo '<td class=" ' . ValueColor($all_year_avg,$beau_scale).'"' . '>' . sprintf($places,($all_year_avg)) . ' </td>';    
    echo '</tr>';   

    // Average Wind
   for ($y = 0; $y < $years; $y++)  { 
      for ( $i = 0 ; $i < 12 ; $i++ )  {  
  }
   }    
    echo '<tr><td class="separator" colspan="14" >&nbsp;</td></tr>';     
    echo '<tr><th class="tableheading" colspan="14">'.langtransstr('Average Wind Speed').' ('.$wind_unit .')</th></tr>'  ;
    echo '<tr><th class="labels" width="7%">'.langtransstr('Date').'</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th class="labels" width="7%">' . substr( $mnthname[$i], 0, 3 ) . '</th>';     
        }
    echo '<th class="labels" width="7%">'.langtransstr('Year').'</th>';         
    echo '</tr>';     
       
  for ($y = 0; $y < $years; $y++)  {
    
    echo '<tr><td class="reportttl">';
    echo $year-$y;       
    echo '</td>';
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($windmonth[$y][$i][1] > 0)
            {                    
                $days_of_data = $windmonth[$y][$i][1];
                $days_in_month = get_days_in_month(($i+1), ($year-$y));
                $ast = $asterik[($days_of_data == $days_in_month)]; 
                $cur_wind = $windmonth[$y][$i][0] / $days_of_data ;
                $avg = round(($like_months[$i][0] / $like_months[$i][1]),1);   
                $trend = trends_gen_difference($cur_wind, $avg, '', $i);                                 
                echo '<td class=" ' . ValueColor($cur_wind,$beau_scale).'">' . sprintf($places,$cur_wind) . $ast.' '.$trend.' </td>';                                      
            }
        else
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }
                $days_of_data = $windyear[$y][1];
                if ($days_of_data > 0)
                {               
                    $days_in_year = get_days_in_year($year-$y);
                    $ast = $asterik[($days_of_data == $days_in_year)];
                    $cur_wind = $windyear[$y][0] / $windyear[$y][1]; 
                    $all_year_avg = round($allyears[0] / $allyears[1],1); 
                    $trend = trends_gen_difference($cur_wind, $all_year_avg, '', $i);                              
                    echo '<td class=" ' . ValueColor($cur_wind,$beau_scale).'">' . sprintf($places,$cur_wind) . $ast.' '.$trend.' </td>';                      
                }
                else {
                    echo '<td class="reportttl"  >' . "---"  . '</td>'; 
                    }                                            
    echo '</tr>';

  }
// Now calculate & display the monthly averages 
    echo '<tr class="windttl2"><td class="reportttl">'.langtransstr('Avg').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {   
    if ($like_months[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $avg = $like_months[$i][0] / $like_months[$i][1];
    echo '<td class=" ' . ValueColor($avg,$beau_scale).'"' . '>' . sprintf($places,($avg)) .' </td>';  
    }
    }
    $all_year_avg = round($allyears[0] / $allyears[1],1);     
    echo '<td class=" ' . ValueColor($all_year_avg,$beau_scale).'"' . '>' . sprintf($places,($all_year_avg)) . ' </td>';    
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

function get_days_in_month($month, $year)
{
   return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year %400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
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

   function trends_gen_difference( $nowTemp, $yesterTemp, $windtype, $i) {
  global $imagesDir;
  global $mnthname;
  global $show_trends;
  global $wind_unit;
 
  $moretext = '%s '. langtransstr('more than the').' '. $mnthname[$i].' '.langtransstr('average').'.' ;
  $lesstext = '%s '.langtransstr('less than the').' '. $mnthname[$i].' '.langtransstr('average').'.' ; 
  
  $diff = round(($nowTemp - $yesterTemp),1) ; 
  $diff = number_format($diff,1);
  $absDiff = abs($diff);
 
  if ($diff == 0 OR $show_trends !== true) {
     // no change
     $image = '&nbsp;'; 
     }
  elseif ($diff > 0) {
    //  is greater 
    $msg = sprintf($moretext,$absDiff); 
    $image = "<img src=\"${imagesDir}rising.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 0px 0px;\" />";
    }
  else {
    //  is lesser
    $msg = sprintf($lesstext,$absDiff); 
    $image = "<img src=\"${imagesDir}falling.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 0px 0px;\" />";
    }

    return $image;    

}

############################################################################
# End of Page
############################################################################
?>
