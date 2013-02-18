<?php
############################################################################
#
#   Module:     wxrainsummary.php
#   Purpose:    Display a table of rain data.
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
# 2010-09-07 2.0 Added option to include today's data from tags in testtags file
# 2010-11-01 2.1 Fix for first day of month when $show_today is true.
# 2010-11-27 3.0 Added additional options for increment quantity and size
# 2011-01-06 3.07 Added seasonal year option
# 2011-01-09 3.1 Added minmax table
# 2011-02-11 3.13 Fix for mouseover displaying wrong month if using seasonal year.
# 2011-12-27 3.6 Added support for Multilingual and Cumulus, Weatherlink, VWS
# 2012-06-06 3.7 A day 1 workaround for rain data missing from NOAA report
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
$TITLE = $SITE['organ'] . " - ".langtransstr("Rainfall Summary");
// Password for View Source Function    
$SITE['viewscr'] = 'sce';
$rain_increment = array(0.5, 10); # (inches, mm) Increments between colorbands
$season_start = 1;      # Start Month of Season
$increments = 11;  # if set higher than 11, you will need to edit the css file to add the additional color levels.
                   # 28 is the max value allowed. Values greater than 13 must be even numbers.
                   # The number of possible colors is two more than the $increment values - one below the 1st increment
                   # and one after the last increment  
$set_values_manually = true; # Set to true if you want to set your own non-linear values
$manual_values = array(.1,.25, 1, 2, 3,4,6,8,10,12,14); # Only used if $set_values_manually is true. 
$round = false;                # Set to true if you want rainfall rounded to nearest integer 
$css_file = "wxreports.css" ;  # name of css file  
############################################################################  
# End of user settings
############################################################################
$loc = $path_dailynoaa;            # Location of dailynoaareports
$first_year_of_data = $first_year_of_noaadata;
$raintype = trim($SITE['uomRain']);
$imagesDir = $SITE['imagesDir'];
$rain_increment = $rain_increment[((strtoupper($raintype)) != "IN")] ;
$raintype = " " . $raintype ; // insert a blank space for report legibility
$rainvalues = array($rain_increment);

if ($season_start == 1){
    $range = "year";
} else {
    $range = "season";
}

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
   
if (( $range == "season") AND ($season_start >= date("m") )){ 
        $year = $year - 1;
       // $years = 2 + ($year - $first_year_of_data); 
        $years = (2 - ($start_month >= $season_start)) + ($year - $first_year_of_data);        
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
<center><h1><?php echo langtransstr('Rainfall Summary').' ('.$raintype.')'; ?></h1>
<?php
if ($range == "season")
    {
    echo '<h2> (' . langtransstr($SITE['monthNames'][$season_start-1]) .' - ' . langtransstr($SITE['monthNames'][$season_start-2]).')</h2>';
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
get_rain_detail($first_year_of_data,$year,$years,$loc, $season_start, $range, $round); 
?>   
    </div>
<?php 
echo $incomplete;
echo $SITE['copy'];
?>
</div><!-- end main-copy -->

<?php
############################################################################
 @include("footer.php");
############################################################################
# Functions
############################################################################

function get_rain_detail ($first_year_of_data,$year,$years,$loc, $season_start, $range, $round) {
    global $SITE, $rainvalues, $raintype, $table_order, $show_today, $dayrn, $colors, $mnthname, $yesterdayrain;     
if ($round == true) 
    $places = "%01.0f";  
elseif($raintype == " in")
    $places = "%01.2f";
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
                $raw[$y][1][$mx][1][0][8] = strip_units($dayrn);                                  
            } else {
                $raw[$y][1][$mx][1] = getnoaafile($loc . $filename,$yx,$m);
            }
            if ($current_month AND $show_today){ 
                $raw[$y][1][$mx][1][date("j")-1][8] = strip_units($dayrn);                    
                }
            if ($current_month AND (date("j")==2) AND ('WD' == $SITE['WXsoftware'])){  // Fix for WD rain on first day of month not listed in NOAA report until the 3rd day of the month.
                if ((strip_units($yesterdayrain))>0){
                    $raw[$y][1][$mx][1][date("j")-2][8] = strip_units($yesterdayrain);    
                }
                
            }                                             
             }
                                
        }                            
        }
     
        
    // Output Table with information
    
    echo '<table>';  
    $rainmonth = array();
    $rainyear = array();     
    $ytd = 0;
    $monthly_maxes = array(-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1);
    $monthly_mins = array(10000,10000,10000,10000,10000,10000,10000,10000,10000,10000,10000,10000);
    $yearly_max = -1;
    $yearly_min = 100000;    
    
        for ($yx = 0 ; $yx < $years ; $yx++ )  {       
        // Display each years values for that month
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) {             
         for ($day = 0 ; $day < 31 ; $day++ ){                            
                                             
        // Rain amount
        $rain = $raw[$yx][1][$mnt][1][$day][8];
        $b4_start = ($yx == $years-1) && ($mnt == $start_month-1) && ($day < $start_day-1) ;        
                if (( $rain != "") AND ( $rain != "-----") AND ($b4_start == false)) {  
  					$rainmonth[$yx][$mnt][0] = $rainmonth[$yx][$mnt][0] + $rain;
					$rainmonth[$yx][$mnt][1] = $rainmonth[$yx][$mnt][1] + 1;
                }               

        }    // end day loop
     
        $rainyear[$yx][0] = $rainyear[$yx][0] + $rainmonth[$yx][$mnt][0];
        $rainyear[$yx][1] = $rainyear[$yx][1] + $rainmonth[$yx][$mnt][1];
        if ($rainmonth[$yx][$mnt][1] > 0){         
            if ($rainmonth[$yx][$mnt][0]>$monthly_maxes[$mnt]){
                $monthly_maxes[$mnt] = $rainmonth[$yx][$mnt][0];
                }
                if ($rainmonth[$yx][$mnt][0]<$monthly_mins[$mnt]){
                $monthly_mins[$mnt] = $rainmonth[$yx][$mnt][0];
                }
        }                  

        }  // end month loop
        if ($rainyear[$yx][0] > $yearly_max){
            $yearly_max = $rainyear[$yx][0];
        }
        if ($rainyear[$yx][0] < $yearly_min){
            $yearly_min = $rainyear[$yx][0];
        }            
 }   // end year loop
    
// We have all the info, now display it 
    // Rainfall
   
    echo '<tr><th class="tableheading" colspan="14">'.langtransstr('Rainfall').'</th></tr>'  ;
    echo '<tr><th class="labels" width="7%">'.langtransstr('Date').'</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            $mi = ($season_start + $i)-1;
            if ($mi > 11)
                $mi = $mi - 12;
            echo '<th class="labels" width="7%">' . substr( $mnthname[$mi], 0, 3 ) . '</th>';     
        }
    echo '<th class="labels" width="7%">'.langtransstr('Year').'</th>';         
    echo '</tr>';        
  for ($y = 0; $y < $years; $y++)  {
    for ( $i = 0 ; $i < 12 ; $i++ ) { 
                if ($rainmonth[$y][$i][1] > 0) {   
                $rain_months[$i][0] = $rain_months[$i][0] + $rainmonth[$y][$i][0]; // Add all like month amounts
                $rain_months[$i][1] = $rain_months[$i][1] + 1; 
                } 
    }       
  }
    for ( $i = 0 ; $i < 12 ; $i++ ) {
    if (0 == $rain_months[$i][1]) 
        $avg = 0;
    else      
        $avg = round(($rain_months[$i][0] / $rain_months[$i][1]),2);
    $year_avg = $year_avg + $avg;
    }      
  for ($y = 0; $y < $years; $y++)  {
    if ($rainyear[$y][1]>0) {     
    echo '<tr><td class="reportttl">';
    if ($range == "year")
    echo $year-$y;       
    else {
        echo ($year-($y))." / ".($year-($y-1)); 
    }         
    echo '</td>';
   
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        $mth = $season_start +$i;
        $yearx = ($year-$y);
        if ($mth > 12)
            {                    
        $mth = $mth - 12;
        $yearx = ($year-$y)+1;
        }            
        $yi = $y;
        if ($rainmonth[$yi][$i][1] > 0)
            {                    
                $days_of_data = $rainmonth[$yi][$i][1];
                $days_in_month = get_days_in_month(($mth), ($yearx));
                $ast = $asterik[($days_of_data == $days_in_month)];   
                $avg_all = round(($rain_months[$i][0] / $rain_months[$i][1]),2);  
                $cur_rain = round($rainmonth[$y][$i][0],2);
                $trend = trends_gen_difference($cur_rain, $avg_all, $raintype, ($mth-1));                              
     echo '<td class="' . ValueColor($cur_rain,$rainvalues).'"' . '>' . sprintf($places,$cur_rain) . $ast.' '.$trend.'</td>';                 
                               

          
            }
        else
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }
                $days_of_data = $rainyear[$y][1];
                if (($range == "season") AND ($season_start >2)){
                    $days_in_year = get_days_in_year(($year-$y)+1); 
                }
                else {                  
                $days_in_year = get_days_in_year($year-$y);
                }
                $ast = $asterik[($days_of_data == $days_in_year)];
                $trend = trends_gen_difference($rainyear[$y][0], $year_avg, $raintype, 12);                 
   echo '<td class="yeartotals">' . sprintf($places,($rainyear[$y][0])) . $ast.' '.$trend.'</td>';                             
    echo '</tr>';
  }
  }

    echo '<tr><td class="separator" colspan="14" >&nbsp;</td></tr>';  
// Now calculate & display the maximum months
    echo '<tr><td class="reportttl">'.langtransstr('Max').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) { 
    if ($rain_months[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $max = $monthly_maxes[$i];
    echo '<td class=" ' . ValueColor($max,$rainvalues).'"' . '>' . sprintf($places,($max)) .' </td>';  
    }
    }
    echo '<td class="yeartotals">' . sprintf($places,($yearly_max)) . ' </td>';    
    echo '</tr>';   
  
// Now calculate & display the averages 
    echo '<tr><td class="reportttl">'.langtransstr('Avg').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) { 
    if ($rain_months[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $avg = round(($rain_months[$i][0] / $rain_months[$i][1]),2);
    echo '<td class=" ' . ValueColor($avg,$rainvalues).'"' . '>' . sprintf($places,($avg)) .' </td>';  
    }
    }
    echo '<td class="yeartotals">' . sprintf($places,($year_avg)) . ' </td>';    
    echo '</tr>';  
    
// Now calculate & display the minimum months
    echo '<tr><td class="reportttl">'.langtransstr('Min').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) { 
    if ($rain_months[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $min = $monthly_mins[$i];
    echo '<td class=" ' . ValueColor($min,$rainvalues).'"' . '>' . sprintf($places,($min)) .' </td>';  
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
            echo '<tr><td class="levelb_1" >&lt;&nbsp;' . sprintf($places,$rainvalues[$i]) . '</td>';
        } else {
            if (($j == 0) AND ($r > 0)){
             //   echo '<tr>';
            }
                echo '<td class="levelb_'.($band+1).'"'.$color_text.' > ' . sprintf($places,$rainvalues[$i-1]) . " - " .sprintf($places,$rainvalues[$i]) . '</td>';
                if (($j == $colorband_cols-1) AND ($r != $colorband_rows)){
                    echo '</tr><tr>';
                    
                } 
        }
        $i = $i+1;
        
        }
    }        

    echo '<td class="levelb_'.($band+2).'"'.$color_text.' >'. sprintf($places,$rainvalues[$i-1]) . '&gt;</td>';   
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

 function trends_gen_difference( $nowTemp, $yesterTemp, $raintype, $i) {
  global $imagesDir;
  global $mnthname;
  global $show_trends;
  global $round;

  $wettext = '%s '. langtransstr('more than the').' '. $mnthname[$i].' '.langtransstr('average').'.' ;
  $drytext = '%s '.langtransstr('less than the').' '. $mnthname[$i].' '.langtransstr('average').'.' ; 
  if ($round == true)
    $places = 0;
  elseif ($raintype == ' in')
    $places = 2;
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