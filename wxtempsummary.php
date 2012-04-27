<?php
############################################################################
#
#   Module:     wxtempsummary.php
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
# 2010-01-29 1.0 Inital Release
# 2010-02-02 1.01 Added option to show links to other summary pages & css changes
# 2010-03-23 1.1 Added option to link to snow summary page
# 2010-09-07 2.0 Added option to include today's data from tags in testtags file
# 2010-11-01 2.1 Fix for first day of month when $show_today is true.
# 2010-11-27 3.0 Added additional options for increment quantity and size
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
$TITLE = $SITE['organ'] . " - Temperature Summary";
// Password for View Source Function    
$SITE['viewscr'] = 'sce';
$temprange_start = array(0, -15); # (Farenheit, Celcius) Starting point of the lowest temp colorband 
$temprange_increment = array(10, 5); # (Farenheit, Celcius) Increments between colorbands
$increments = 11;  # if set higher than 11, you will need to edit the css file to add the additional color levels.
                   # 28 is the max value allowed. Values greater than 13 must be even numbers.
                   # The number of possible colors is two more than the $increment values - one below the 1st increment
                   # and one after the last increment  
$set_values_manually = false; # Set to true if you want to set your own non-linear values
$manual_values = array(.25, .5, 1, 2, 3,6,12,18,24,36,60,75); # Only used if $set_values_manually is true. 
$round = true;                # Set to true if you want temperatures rounded to nearest integer  
$css_file = "wxreports.css" ;  # name of css file 
############################################################################################################# 
#############################################################################################################  
# End of user settings
############################################################################
$loc = $path_dailynoaa;            # Location of dailynoaareports
$first_year_of_data = $first_year_of_noaadata;
$table_order = array("High", "Avg High", "Mean", "Avg Low", "Low"); 
$uomTemp = $SITE['uomTemp'];
$imagesDir = $SITE['imagesDir']; 
$temptype = array("C","F") ;
$temptype = $temptype[$uomTemp == "&deg;F"];
$temprange_start = $temprange_start[((strtoupper($temptype)) != "F")] ;
$temprange_increment = $temprange_increment[((strtoupper($temptype)) != "F")] ;
$tempvalues = array($temprange_start);
for ( $i = 0; $i < $increments ; $i ++ ) 
    {
$tempvalues[$i+1] = $tempvalues[$i] + $temprange_increment;
    }
    

if ($round){
$temprounding = "%01.0f"; 
} else {
$temprounding = "%01.1f"; 
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

// If first day of first month of the year, use previous year  

$year = date("Y");
if ($show_today != true){
    if (( date("n") == 1) AND date("j") == 1) {
    $year = $year -1;
    }
} 
   
$setcolor = "background-color:";
 
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
<center><h1><?php echo langtransstr('Temperature Summary') . '(' . $uomTemp; ?>)</h1></center>
<table><tr><td align="center">
<?php
    if ($show_today){
        echo langtransstr('Data last updated') . ' '.$date.' '.$time.'.';
    } else {        
        echo langtransstr('Note: Data is updated after midnight each day.');
    }    

@include("wxreportinclude.php");  // Creates the various buttons linking to other reports
?>
</td></tr></table>     
<?php 
get_temp_detail($first_year_of_data,$year,$years,$loc, $setcolor, $round); 
?>
  </div> 
<?php 
echo $incomplete;
echo $SITE['copy'] 
?>
</div><!-- end main-copy -->

<?php
############################################################################
 @include("footer.php");
############################################################################
# Functions
############################################################################

function get_temp_detail ($first_year_of_data,$year, $years, $loc, $setcolor, $round) {
    global $SITE, $tempvalues, $temptype, $table_order, $uomTemp, $colors, $temprounding;
    global $show_today, $maxtemp, $mintemp, $avtempsincemidnight, $mnthname;      
if ($round == true) {
    $places = "%01.0f";
    $places_diff = 0;
}else{
    $places = "%01.1f";
    $places_diff = 1;     
}             
    
    // Collect the data 
                            
        for ( $y = 0; $y < $years ; $y ++ ) {
             $yx = $year - $y;
             
        for ( $m = 0; $m < 12 ; $m ++ ) {            
            
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
                $raw[$y][1][$m][1][0][1] = strip_units($avtempsincemidnight);                
                $raw[$y][1][$m][1][0][2] = strip_units($maxtemp);
                $raw[$y][1][$m][1][0][4] = strip_units($mintemp);                  
            } elseif (file_exists($loc . $filename) ) {
                $raw[$y][1][$m][1] = getnoaafile($loc . $filename);
            }
            if ($current_month AND $show_today){ 
                $raw[$y][1][$m][1][date("j")-1][1] = strip_units($avtempsincemidnight);                                 
                $raw[$y][1][$m][1][date("j")-1][2] = strip_units($maxtemp);
                $raw[$y][1][$m][1][date("j")-1][4] = strip_units($mintemp);                        
                    
                }                 
                                       
                        
        }                            
        }
     
        
    // Output Table with information
    
    echo '<table>';  
    $tempmonth = array();
    $tempyear = array();
    $monthmin = array(200,200,200,200,200,200,200,200,200,200,200,200);
    $monthmax = array(-200,-200,-200,-200,-200,-200,-200,-200,-200,-200,-200,-200);      
       
    $ytd = 0;
    
        for ($yx = 0 ; $yx < $years ; $yx++ )  {       
        // Display each years values for that month
            $tempyear[$yx][6] = -100;      // Initial High temp setting
            $tempyear[$yx][7] = 100;       // Initial Low temp setting 
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) { 
            $tempmonth[$yx][$mnt][6] = -100;      // Initial High temp setting
            $tempmonth[$yx][$mnt][7] = 100;       // Initial Low temp setting                 
         for ($day = 0 ; $day < 31 ; $day++ ){                            
                                             
        // High temps
        $temp = $raw[$yx][1][$mnt][1][$day][2];
                if ( $temp != "" AND $temp != "-----"  AND $temp != "---" AND $temp != "X") {  
  					$tempmonth[$yx][$mnt][0] = $tempmonth[$yx][$mnt][0] + $temp;
					$tempmonth[$yx][$mnt][1] = $tempmonth[$yx][$mnt][1] + 1;
                        if ($tempmonth[$yx][$mnt][6] < $temp)
                            {
                            $tempmonth[$yx][$mnt][6] =  $temp;
                            }
                        if ($temp > $monthmax[$mnt]) // Save max of like months
                        $monthmax[$mnt] = $temp;    
                }
	    // Low temps 
        $temp = $raw[$yx][1][$mnt][1][$day][4];                   
                if ( $temp != "" AND $temp != "-----"  AND $temp != "---" AND $temp != "X") {   
                    $tempmonth[$yx][$mnt][2] = $tempmonth[$yx][$mnt][2] + $temp;
                    $tempmonth[$yx][$mnt][3] = $tempmonth[$yx][$mnt][3] + 1;
                        if ($tempmonth[$yx][$mnt][7] > $temp)
                            {
                            $tempmonth[$yx][$mnt][7] =  $temp;
                            }
                         if ($temp < $monthmin[$mnt]) // Save min of like months
                        $monthmin[$mnt] = $temp;  				
                }
                
         // Get mean temperature data 
        $temp = $raw[$yx][1][$mnt][1][$day][1];    
                if ( $temp != "" AND $temp != "-----"  AND $temp != "---" AND $temp != "X") {                    
                $tempmonth[$yx][$mnt][4] = $tempmonth[$yx][$mnt][4] + $temp;
                $tempmonth[$yx][$mnt][5] = $tempmonth[$yx][$mnt][5] + 1;                 
                }
        }  // End of day loop
         for ($i = 0 ; $i < 6 ; $i++ ){        
        $tempyear[$yx][$i] = $tempyear[$yx][$i] + $tempmonth[$yx][$mnt][$i];
         }
         if ($tempyear[$yx][6] < $tempmonth[$yx][$mnt][6]) {
            $tempyear[$yx][6] = $tempmonth[$yx][$mnt][6]; 
         }
         if ($tempyear[$yx][7] > $tempmonth[$yx][$mnt][7]) {
            $tempyear[$yx][7] = $tempmonth[$yx][$mnt][7];                 
        }
        $temp_month[$mnt][0] = $temp_month[$mnt][0] + $tempmonth[$yx][$mnt][0];  // High Temp 
        $temp_month[$mnt][1] = $temp_month[$mnt][1] + $tempmonth[$yx][$mnt][1];  // Hight Temp days
        $temp_month[$mnt][2] = $temp_month[$mnt][2] + $tempmonth[$yx][$mnt][2];  // Low Temp
        $temp_month[$mnt][3] = $temp_month[$mnt][3] + $tempmonth[$yx][$mnt][3];  // Low Temp days   
        $temp_month[$mnt][4] = $temp_month[$mnt][4] + $tempmonth[$yx][$mnt][4];  // Mean Temp
        $temp_month[$mnt][5] = $temp_month[$mnt][5] + $tempmonth[$yx][$mnt][5];  // Mean Temp days                
        }   // End of month loop  
 }   // End of year loop
    
// We have all the info, now display it 
for ($table = 0; $table < 5; $table++){
    if ($table_order[$table] == "High") {
    // High Temps  
    echo '<tr><th class="tableheading" colspan="14">'.langtransstr('High Temperatures').'</th></tr>'  ;
    echo '<tr><th class="labels" width="7%">'.langtransstr('Date').'</th>'  ;
    $asterik = array("*","");
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th class="labels" width="7%">' . substr( $mnthname[$i], 0, 3 ) . '</th>';     
        }
    echo '<th class="labels" width="7%">'.langtransstr('Year').'</th>';         
    echo '</tr>';        
  for ($y = 0; $y < $years; $y++)  {
    if ($tempyear[$y][1]>0) {    
    echo '<tr><td class="reportttl">';
    echo $year-$y;       
    echo '</td>';
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($tempmonth[$y][$i][1] > 0)
            {                    
                $max = $tempmonth[$y][$i][6];
                $days_of_data = $tempmonth[$y][$i][1];
                $days_in_month = get_days_in_month(($i+1), ($year-$y));
                $ast = $asterik[($days_of_data == $days_in_month)];                 
                echo '<td class=" ' . ValueColor($max).'"' . '>' . sprintf($places,($max)) . $ast.' </td>';
            }
        else
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }
                 $days_of_data = $tempyear[$y][1];
                $days_in_year = get_days_in_year($year-$y);
                $ast = $asterik[($days_of_data == $days_in_year)];        
                echo '<td class=" ' . ValueColor(($tempyear[$y][6])).'"' . '>' . sprintf($places,($tempyear[$y][6])) . $ast.'</td>';            
    echo '</tr>';
  }    
  }
  // Now display the max of like months 
    echo '<tr class="reportttl2" ><td class="reportttl">'.langtransstr('Max').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {        
    if ($monthmax[$i]== "" OR $monthmax[$i] == -200) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $max = $monthmax[$i];
    echo '<td class=" ' . ValueColor($max).'"' . '>' . sprintf($places,($max)) .' </td>';  
     }
    }
    $max = max($monthmax);
    echo '<td class=" ' . ValueColor($max).'"' . '>' . sprintf($places,($max)) . ' </td>';    
    echo '</tr>';
      
    } elseif ($table_order[$table] == "Avg High") {  

    // Average High Temps      
    echo '<tr><td class="separator" colspan="14" >&nbsp;</td></tr>';     
    echo '<tr><th class="tableheading" colspan="14">'.langtransstr('Average High Temperatures').'</th></tr>'  ;
    echo '<tr><th class="labels">Date</th>'  ;
   
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th   class="labels">' . substr( $mnthname[$i], 0, 3 ) . '</th>';     
        }
    echo '<th  class="labels"> '.langtransstr('Year').' </th>';        
    echo '</tr>';       
  for ($y = 0; $y < $years; $y++)  {
            $days_of_data = $tempyear[$y][1];
            if ($days_of_data > 0){      // if no data for year, skip to next year
    echo '<tr><td class="reportttl">';
    echo $year-$y;       
    echo '</td>';
    $year_days = $year_temp = 0;    
    for ( $i = 0 ; $i < 12 ; $i++ ) {    
    $year_temp = $year_temp + $temp_month[$i][0];
    $year_days = $year_days + $temp_month[$i][1];
    }
    $year_avg = $year_temp / $year_days;      
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($tempmonth[$y][$i][1] > 0 AND $tempmonth[$y][$i][0] !="" )
            {
                $days_of_data = $tempmonth[$y][$i][1];
                $days_in_month = get_days_in_month(($i+1), ($year-$y));
                $ast = $asterik[($days_of_data == $days_in_month)];
                $avg = round(($tempmonth[$y][$i][0] / $tempmonth[$y][$i][1]),1);
                $avg_all = round(($temp_month[$i][0] / $temp_month[$i][1]),1);  
                $trend = trends_gen_difference($avg, $avg_all, $uomTemp, $places_diff, $i, 'High');                            
                echo '<td class=" ' . ValueColor($avg).'"' . '>' . sprintf($places,($avg))  . $ast.' '.$trend.'</td>';
            }
            
        else
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }
        if ($tempyear[$y][1] > 0 AND $tempmonth[$y][1] !="" ) { 
                $days_of_data = $tempyear[$y][1];
                $days_in_year = get_days_in_year($year-$y);
                $ast = $asterik[($days_of_data == $days_in_year)];
                $avg = round(($tempyear[$y][0] / $tempyear[$y][1]),1);
                $avg_all = round($year_avg,1) ;
                $trend = trends_gen_difference($avg, $avg_all, $uomTemp, $places_diff, 12, 'High');                              
     echo '<td class=" ' . ValueColor($avg).'"' . '>' . sprintf($places,$avg) . $ast.' '.$trend.'</td>';           
        }
        else {
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }        
    echo '</tr>';  
  }
  }
// Now calculate & display the average high temp  
    echo '<tr class="reportttl2"><td class="reportttl">'.langtransstr('Avg').'</td>';
    $year_days = $year_temp = 0;    
    for ( $i = 0 ; $i < 12 ; $i++ ) {       
    if ($temp_month[$i][1]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $avg = $temp_month[$i][0] / $temp_month[$i][1];
    echo '<td class=" ' . ValueColor($avg).'"' . '>' . sprintf($places,($avg)) .' </td>';  

    }
    }

    echo '<td class=" ' . ValueColor($year_avg).'"' . '>' . sprintf($places,($year_avg)) . ' </td>';    
    echo '</tr>';     
  
    
    } elseif ($table_order[$table] == "Mean") {    
 
  // Mean Temps
    echo '<tr><td class="separator" colspan="14" >&nbsp;</td></tr>';     
    echo '<tr><th class="tableheading" colspan="14">'.langtransstr('Mean Temperatures').'</th></tr>'  ;
    echo '<tr><th class="labels">Date</th>'  ;
   
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th   class="labels">' . substr( $mnthname[$i], 0, 3 ) . '</th>';     
        }
    echo '<th  class="labels"> '.langtransstr('Year').' </th>';        
    echo '</tr>';       
  for ($y = 0; $y < $years; $y++)  {
            $days_of_data = $tempyear[$y][5];
            if ($days_of_data > 0){      // if no data for year, skip to next year
    echo '<tr><td class="reportttl">';
    echo $year-$y;       
    echo '</td>';
    $year_days = $year_temp = 0;    
    for ( $i = 0 ; $i < 12 ; $i++ ) {    
    $year_temp = $year_temp + $temp_month[$i][4];
    $year_days = $year_days + $temp_month[$i][5];
    }
    $year_avg = $year_temp / $year_days;      
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($tempmonth[$y][$i][5] > 0 AND $tempmonth[$y][$i][5] !="" )
            {
                $days_of_data = $tempmonth[$y][$i][5];
                $days_in_month = get_days_in_month(($i+1), ($year-$y));
                $ast = $asterik[($days_of_data == $days_in_month)];
                $avg = round(($tempmonth[$y][$i][4] / $tempmonth[$y][$i][5]),1);
                $avg_all = round(($temp_month[$i][4] / $temp_month[$i][5]),1);  
                $trend = trends_gen_difference($avg, $avg_all, $uomTemp, $places_diff, $i, 'Mean');                            
                echo '<td class=" ' . ValueColor($avg).'"' . '>' . sprintf($places,($avg))  . $ast.' '.$trend.'</td>';
            }
            
        else
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }
        if ($tempyear[$y][5] > 0 AND $tempmonth[$y][5] !="" ) { 
                $days_of_data = $tempyear[$y][5];
                $days_in_year = get_days_in_year($year-$y);
                $ast = $asterik[($days_of_data == $days_in_year)];
                $avg = round(($tempyear[$y][4] / $tempyear[$y][5]),1);
                $avg_all = round($year_avg,1) ;
                $trend = trends_gen_difference($avg, $avg_all, $uomTemp, $places_diff, 12, 'Mean');                              
     echo '<td class=" ' . ValueColor($avg).'"' . '>' . sprintf($places,$avg) . $ast.' '.$trend.'</td>';           
        }
        else {
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }        
    echo '</tr>';  
  }
  }
// Now calculate & display the average mean temp  
    echo '<tr class="reportttl2"  ><td class="reportttl">'.langtransstr('Avg').'</td>';
    $year_days = $year_temp = 0;    
    for ( $i = 0 ; $i < 12 ; $i++ ) {       
    if ($temp_month[$i][5]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $avg = $temp_month[$i][4] / $temp_month[$i][5];
    echo '<td class=" ' . ValueColor($avg).'"' . '>' . sprintf($places,($avg)) .' </td>';  

    }
    }

    echo '<td class=" ' . ValueColor($year_avg).'"' . '>' . sprintf($places,($year_avg)) . ' </td>';    
    echo '</tr>';     
 
  
    } elseif ($table_order[$table] == "Avg Low") {    
  // Average Low Temps
   for ($y = 0; $y < $years; $y++)  {
  echo '<tr><td class="separator" colspan="14" >&nbsp;</td></tr>';     
    echo '<tr><th class="tableheading" colspan="14">'.langtransstr('Average Low Temperatures').'</th></tr>'  ;
    echo '<tr><th class="labels">Date</th>'  ; 
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th   class="labels">' . substr( $mnthname[$i], 0, 3 ) . '</th>';     
        }
    echo '<th  class="labels"> '.langtransstr('Year').' </th>';        
    echo '</tr>';       
  for ($y = 0; $y < $years; $y++)  {
            $days_of_data = $tempyear[$y][3];
            if ($days_of_data > 0){      // if no data for year, skip to next year
    echo '<tr><td class="reportttl">';
    echo $year-$y;       
    echo '</td>';
    $year_days = $year_temp = 0;    
    for ( $i = 0 ; $i < 12 ; $i++ ) {    
    $year_temp = $year_temp + $temp_month[$i][2];
    $year_days = $year_days + $temp_month[$i][3];
    }
    $year_avg = $year_temp / $year_days;      
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($tempmonth[$y][$i][3] > 0 AND $tempmonth[$y][$i][3] !="" )
            {
                $days_of_data = $tempmonth[$y][$i][3];
                $days_in_month = get_days_in_month(($i+1), ($year-$y));
                $ast = $asterik[($days_of_data == $days_in_month)];
                $avg = round(($tempmonth[$y][$i][2] / $tempmonth[$y][$i][3]),1);
                $avg_all = round(($temp_month[$i][2] / $temp_month[$i][3]),1);  
                $trend = trends_gen_difference($avg, $avg_all, $uomTemp, $places_diff, $i, 'Low');                            
                echo '<td class=" ' . ValueColor($avg).'"' . '>' . sprintf($places,($avg))  . $ast.' '.$trend.'</td>';
            }
            
        else
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }
        if ($tempyear[$y][3] > 0 AND $tempmonth[$y][3] !="" ) { 
                $days_of_data = $tempyear[$y][3];
                $days_in_year = get_days_in_year($year-$y);
                $ast = $asterik[($days_of_data == $days_in_year)];
                $avg = round(($tempyear[$y][2] / $tempyear[$y][3]),1);
                $avg_all = round($year_avg,1) ;
                $trend = trends_gen_difference($avg, $avg_all, $uomTemp, $places_diff, 12,'Low');                              
     echo '<td class=" ' . ValueColor($avg).'"' . '>' . sprintf($places,$avg) . $ast.' '.$trend.'</td>';           
        }
        else {
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }        
    echo '</tr>';  
  }
  }
   }
// Now calculate & display the average low temp  
    echo '<tr class="reportttl2" ><td class="reportttl">'.langtransstr('Avg').'</td>';
    $year_days = $year_temp = 0;    
    for ( $i = 0 ; $i < 12 ; $i++ ) {       
    if ($temp_month[$i][3]==0) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $avg = round(($temp_month[$i][2] / $temp_month[$i][3]),1);
    echo '<td class=" ' . ValueColor($avg).'"' . '>' . sprintf($places,($avg)) .' </td>';  

    }
    }

    echo '<td class=" ' . ValueColor($year_avg).'"' . '>' . sprintf($places,($year_avg)) . ' </td>';    
    echo '</tr>';     
    
   
} else {     
    // Low Temps
    echo '<tr><td class="separator" colspan="14" >&nbsp;</td></tr>';     
    echo '<tr><th class="tableheading" colspan="14">'.langtransstr('Low Temperatures').'</th></tr>'  ;
    echo '<tr><th class="labels">Date</th>'  ;
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th   class="labels">' . substr( $mnthname[$i], 0, 3 ) . '</th>';     
        }
    echo '<th  class="labels"> '.langtransstr('Year').' </th>';        
    echo '</tr>';        
  for ($y = 0; $y < $years; $y++)  {
    if ($tempyear[$y][3]>0) {    
    echo '<tr><td class="reportttl">';
    echo $year-$y;       
    echo '</td>';
    for ( $i = 0 ; $i < 12 ; $i++ )
        {
        if ($tempmonth[$y][$i][1] > 0)
            {                    
                $days_of_data = $tempmonth[$y][$i][1];
                $days_in_month = get_days_in_month(($i+1), ($year-$y));
                $ast = $asterik[($days_of_data == $days_in_month)]; 
                $min = $tempmonth[$y][$i][7];                 
                echo '<td class=" ' . ValueColor($min).'"' . '>' . sprintf($places,($min)) . $ast.' </td>';
            }
        else
                echo '<td class="reportttl"  >' . "---"  . '</td>';
        }
                 $days_of_data = $tempyear[$y][3];
                $days_in_year = get_days_in_year($year-$y);
                $ast = $asterik[($days_of_data == $days_in_year)];        
     echo '<td class=" ' . ValueColor(($tempyear[$y][7])).'"' . '>' . sprintf($places,($tempyear[$y][7])) . $ast .'</td>';         
    echo '</tr>';
  }    
  }
  // Now display the min of like months 
    echo '<tr class="reportttl2" ><td class="reportttl">'.langtransstr('Min').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {        
    if ($monthmin[$i]== "" OR $monthmin[$i] == 200) {
        echo '<td class="reportttl"  >' . "---"  . '</td>';
    } else {  
    $min = $monthmin[$i];
    echo '<td class=" ' . ValueColor($min).'"' . '>' . sprintf($places,($min)) .' </td>';  
     }
    }
    $min = min($monthmin);
    echo '<td class=" ' . ValueColor($min).'"' . '>' . sprintf($places,($min)) . ' </td>';    
    echo '</tr>';     
   }    
} 
  
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
    echo '</tr>';       

   echo '</table>';
}

##################################

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

function trends_gen_difference( $nowTemp, $yesterTemp, $uomTemp, $places, $i, $temptext) {
  global $imagesDir;
  global $mnthname;
  global $show_trends;

  $hottext = '%s '. langtransstr('more than the').' '. $mnthname[$i].' '.langtransstr('average').'.' ;
  $coldtext = '%s '.langtransstr('less than the').' '. $mnthname[$i].' '.langtransstr('average').'.' ;
  $diff = round(($nowTemp - $yesterTemp),2) ; 
  $diff = number_format($diff,$places);
  $absDiff = abs($diff);
 
  if ($diff == 0 OR $show_trends !== true) {
     // no change
     $image = '&nbsp;'; 
     }
  elseif ($diff > 0) {
    // today is greater 
    $msg = sprintf($hottext,$absDiff); 
    $image = "<img src=\"${imagesDir}rising.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 0px 0px;\" />";
    }
  else {
    // today is lesser
    $msg = sprintf($coldtext,$absDiff); 
    $image = "<img src=\"${imagesDir}falling.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 0px 0px;\" />";
    }

       return $image;    
}

############################################################################
# End of Page
############################################################################
?>