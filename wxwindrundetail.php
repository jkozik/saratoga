<?php
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
# 2011-05-17 3.16 Initial Release 
# 2011-12-27 3.6 Added support for Multilingual and Cumulus, Weatherlink, VWS
# 2012-08-26 3.8 Added check for manually provided NOAA data in csv file format
############################################################################
require_once("Settings.php");
@include_once("common.php");
############################################################################
// Obtains Server's Self and protect it against XSS injection
$SITE['self'] = htmlentities( substr($_SERVER['PHP_SELF'], 0,  
    strcspn( $_SERVER['PHP_SELF'] , "\n\r") ), ENT_QUOTES );
@include_once("wxreport-settings.php");  
$TITLE = $SITE['organ'] . " - ".langtransstr("Wind Run");  
############################################################################
# Settings Unique to this script
############################################################################
$start_year = "2010"; // Set to first year of wind data you have
$SITE['viewscr'] = 'sce';  // Password for View Source Function
$avg_wind_unit = 1; # 1 = mph, 2 = kmh, 3 = knots, 4 = m/s  Set to the units used in your NOAA monthly file 
$wind_run_unit = 1; # 1 = miles, 2 = kilometers  Set to the units you want displayed in the report
$increment_size = 30; #  Increments between colorbands
$increments = 11;  # if set higher than 11, you will need to edit the css file to add the additional color levels.
                   # 28 is the max value allowed. Values greater than 13 must be even numbers.
                   # The number of possible colors is two more than the $increment values - one below the 1st increment
                   # and one after the last increment  
$set_incrementvalues_manually = false; # Set to true if you want to set your own non-linear values
$manual_values = array(.25, .5, 1, 2, 3,6,12,18,24,36,60,75); # Only used if $set_incrementvalues_manually is true.
$round = true;    # Set to true to round to nearest integer
$css_file = "wxreports.css" ;  # name of css file 
############################################################################
# End of user settings
############################################################################
$loc = $path_dailynoaa;            # Location of NOAA monthly reports
$first_year_of_data = $first_year_of_noaadata;
$first_year_of_data = max($first_year_of_data,$start_year);
$increment = $increment_size;
$incrementvalues = array($increment);

for ( $i = 0; $i < $increments ; $i ++ ) {                                              
    $incrementvalues[$i+1] = $incrementvalues[$i] + $increment;
}
if ($set_incrementvalues_manually == true){
    $incrementvalues = $manual_values;
    $increments = count($manual_values);
}

$increments = min($increments,28); // Max of 22 increments allowed

if ($increments > 13){             // If more than 13 increments, must be an even number 
    $increments = (floor($increments * 0.5) / 0.5);
} 
################
$colors = $increments + 1;    
$season_start = 1;      # Start Month of Season        
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

if ($wind_run_unit == 1){
    $windrun_text = langtransstr('miles');
    $wind_conversions = array(24,14.91,27.62,53.69);
    $wind_conversion = $wind_conversions[$avg_wind_unit-1];
} else {
    $windrun_text = langtransstr('kilometers');
    $wind_conversions = array(38.62,24,44.45,86.4);
    $wind_conversion = $wind_conversions[$avg_wind_unit-1];    
}

?>

<div id="main-copy">
    <div id="report">
        <center><h1><?php echo langtransstr('Wind Run Report').' ('.$windrun_text.')' ?></h1>
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
        echo '</h3>'.langtransstr('Data last updated').' '.$date.' '.$time.'.<br /><br />';
    } else {        
    echo '</h3>'.langtransstr('Note: Data is updated after midnight each day.').'<br /><br /> ';
    }                
?>   </center> 
 <!--       <div class="getreportdtbxfloat">  -->

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
                    <input type="submit" value="Go" />
                </form>
<?php                
 
@include("wxreportinclude.php");
?>
           </div>
   
    <?php get_detail($year,$loc, $range, $season_start); ?>
<br /><table><tr><td class="infotext"><?php echo langtransstr('Wind run is a measurement of how much wind has passed a given point in a period of time. A wind blowing at five').' '. $windrun_text .' '.langtransstr('per hour for an entire day (24 hours) would give a wind run of 120').' '.$windrun_text.' '.langtransstr('for the day').'.' ?></td></tr></table>
   <div class="dev"><?php echo $SITE['copy'] ?></div>     
    </div>
</div><!-- end main-copy -->

<?php
############################################################################
 @include("footer.php");
############################################################################
# Functions
############################################################################

function get_detail ($year, $loc, $range, $season_start) {
    global $SITE, $incrementvalues, $colors, $windruntoday, $round, $wind_conversion;
    global $show_today, $date, $time, $mnthname,  $first_year_of_data;
    if ($round == true) {
    $datarounding = "%01.0f";  
    } else {
    $datarounding = "%01.1f";
    }
    // First Collect the data for the year
    
    if ($range == "year" ) {
        for ( $m = 0; $m < 12 ; $m ++ ) {

            // Save Month in the raw array for use later
            $raw[$m][1] = $m; 
            if (($year == $first_year_of_data) OR ($year > $first_year_of_data)) {            
            // Check for current year and current month
            
           if ($year == date("Y") && $m == ( date("n") - 1) && ((date("j") != 1 ) OR $show_today)) {
                $current_month = 1;
            } else {
                $current_month = 0;
            }
$filename = get_noaa_filename($year,$m,$SITE['WXsoftware'],$current_month);             
            if ($current_month AND $show_today AND date("j")==1){
                $raw[$m][0][0][9] = $windruntoday;                                 
            } else {
                $raw[$m][0] = getnoaafile($loc . $filename,$year,$m);                
                // convert avg wind to windrun
                for ($wr = 0; $wr < 31 ; $wr ++){
                    if (!(($raw[$m][0][$wr][9] == '---') OR ($raw[$m][0][$wr][9] == ''))){
                    $raw[$m][0][$wr][9] = strval($raw[$m][0][$wr][9] * $wind_conversion);
                    }
                }            
            }
                if ($current_month AND $show_today){ 
                    $raw[$m][0][date("j")-1][9] = $windruntoday;                                                        
                
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
$filename = get_noaa_filename(($year - 1),$m,$SITE['WXsoftware'],$current_month); 
            if ($current_month AND $show_today AND date("j")==1){
                $raw[$cnt][0][0][9] = $windruntoday;                  
            }else {
                $raw[$cnt][0] = getnoaafile($loc . $filename,($year-1),$m);
             if ($current_month AND $show_today){ 
                    $raw[$cnt][0][date("j")-1][9] = $windruntoday;                        
                    
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
                $raw[$cnt][0][0][9] = $windruntoday;                  
            }else {
                $raw[$cnt][0] = getnoaafile($loc . $filename,$year,$m);
             if ($current_month AND $show_today){ 
                    $raw[$cnt][0][date("j")-1][9] = $windruntoday;                        
                    
                }                
            }
            $cnt ++;
        }
    }   
    
    // Start display of info we got
    
    // Output Table with information
    
    echo '<table><tr><th class="labels">Day</th>';
    
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
    
    // Setup month and year totals
    
    $datamonth = array();
    $ytd = 0;
    
    
    // Cycle through the possible days 
        
    for ( $day = 0 ; $day < 31 ; $day++ ) {
     
        echo '<tr><td class="reportdt">' . ($day + 1) . '</td>';
        
        // Display each months values for that day
        
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) {
                   
           if ($maxdays[$mnt] < $day + 1 )  { 
           echo '<td class="noday">&nbsp;</td>'; 
           } else {          
            if (( $raw[$mnt][0][$day][9] == "" ) OR  ($raw[$mnt][0][$day][9] == "-----" ) ) {
                $put = "---";
                $raw[$mnt][0][$day][9] = "";
            } else {
                $put = sprintf($datarounding,($raw[$mnt][0][$day][9]));  
                $datamonth[$mnt][0] = $datamonth[$mnt][0] + $put;

                if ($put >= 0) {
                    $datamonth[$mnt][1] = $datamonth[$mnt][1] + 1;
                }
             }
           

            	if (($put >= 0) AND ($put != "---")){
                echo '<td class=" ' . ValueColor($put).'"' . '>' . $put .' </td>';
            } else {
               echo '<td class="reportday">' . $put . '</td>';
            }
                }

       }
        
        echo "</tr>\n"; 
    }
    
    // We are done with the daily numbers now lets show the totals
        $daysofdata = $temptotal = $monthmax = array(0,0,0,0,0,0,0,0,0,0,0,0);         
        $monthmin = array(2000,2000,2000,2000,2000,2000,2000,2000,2000,2000,2000,2000);             
    for ($i = 0; $i < 12; $i ++){
        $temp = $raw[$i][0];
        for ($j = 0; $j < 32; $j ++){
            if (($temp[$j][9] != "") AND ($temp[$j][9] != "-----")){
            $daysofdata[$i] = $daysofdata[$i]+1;
            $temptotal[$i] = $temptotal[$i] + $temp[$j][9];
                if ($temp[$j][9] < $monthmin[$i]){
                    $monthmin[$i] = $temp[$j][9];
                    }
                if ($temp[$j][9] > $monthmax[$i]){
                    $monthmax[$i] = $temp[$j][9];
                    }                    
            } 
        }   
        if ($daysofdata[$i] != 0){        
//        $monthmax[$i] = max($raw[$i][0][9]);
        $monthavg[$i] = round(($temptotal[$i]/$daysofdata[$i]),2);
        }
    }  
    
    // We are done with the daily numbers now lets show the monthly highs, averages, lows
   echo '<tr><td colspan="13" class="separator">&nbsp;</td></tr>'; 
   // Put month headings 
   echo '<tr><th class="labels">&nbsp;</th>'  ;
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th  class="labels">' . substr( $mnthname[ $raw[$i][1] ], 0, 3 ) . '</th>';     
        }
#############  Month Highs  ######################
   echo '</tr><tr><td class="reportttl">'.langtransstr('High').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {
        if ($daysofdata[$i] > 0){            
            echo '<td class="'. ValueColor($monthmax[$i]) .'">'. sprintf($datarounding, $monthmax[$i]) . '</td>';                     
        } else {
            echo '<td class="reportday">---</td>';
        }
    }
    
########### Month Average ############################
   echo '</tr><tr><td class="reportttl">'.langtransstr('Average').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {
        if ($daysofdata[$i] > 0){            
            echo '<td class="'. ValueColor($monthavg[$i]) .'">'. sprintf($datarounding, $monthavg[$i]) . '</td>';                     
        } else {
            echo '<td class="reportday">---</td>';;
        }
    }
  
########### Month Lows ############################
   echo '</tr><tr><td class="reportttl">'.langtransstr('Low').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {
        if ($daysofdata[$i] > 0){            
            echo '<td class="'. ValueColor($monthmin[$i]) .'">'. sprintf($datarounding, $monthmin[$i]) . '</td>';                     
        } else {
            echo '<td class="reportday">---</td>';;
        }
    }

#################################################    
$colorband_rows = ceil($colors/15);
$colorband_cols = ceil(($colors+1)/$colorband_rows);   
      echo '</tr></table>';
      echo '<table><tr><td class="separator" colspan="'.($colorband_cols).'" >&nbsp;</td></tr>';    
    echo '<tr><td class="colorband" colspan="'.($colorband_cols).'">'.langtransstr('Color Key').'</td></tr>';
    $i = 0;
    for ($r = 0; $r < $colorband_rows; $r ++){  
        for ( $j = 0; (($j < $colorband_cols) AND ($i < $colors)) ; $j ++ ){
        $band = $i;

        if ($i == 0){     
            echo '<tr><td class="levelb_1" >&lt;&nbsp;' . sprintf($datarounding,$incrementvalues[$i]) . '</td>';
        } else {
            if (($j == 0) AND ($r > 0)){
            }
                echo '<td class="levelb_'.($band+1).'"'.$color_text.' > ' . sprintf($datarounding,$incrementvalues[$i-1]) . " - " .sprintf($datarounding,$incrementvalues[$i]) . '</td>';
                if (($j == $colorband_cols-1) AND ($r != $colorband_rows)){
                    echo '</tr><tr>';
                    
                } 
        }
        $i = $i+1;
        
        }
    }

    echo '<td class="levelb_'.($band+2).'"'.$color_text.' >'. sprintf($datarounding,$incrementvalues[$i-1]) . '&gt;</td>';
    echo '</tr>';
  
   echo '</table>';

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

function ValueColor($value) {
    global $incrementvalues;
    $limit = count($incrementvalues);
  //  if ($value == 0){
  //      return 'reportday';
  //  }    
    if ($value < $incrementvalues[0]) {
      return 'levelb_1';
      } 
    for ($i = 1; $i < ($limit); $i++){
        if ($value <= $incrementvalues[$i]) {
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
