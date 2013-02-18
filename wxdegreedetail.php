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
#
# 2010-11-27 3.0 Initial release
# 2010-11-39 3.01 Changed colspan setting for IE problem
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
$TITLE = $SITE['organ'] . " - ".langtransstr("Degree Days");     
############################################################################
# Settings Unique to this script
############################################################################
$SITE['viewscr'] = 'sce';  // Password for View Source Function
$base_temp_cooling = array(65,18);   #  (Farenheit, Celcius) Base temperature used for calculating degree days. 
$base_temp_heating = array(65,18);
$increment_size = 5; #  Increments between colorbands
$increments = 11;  # if set higher than 11, you will need to edit the css file to add the additional color levels.
                   # 28 is the max value allowed. Values greater than 13 must be even numbers.
                   # The number of possible colors is two more than the $increment values - one below the 1st increment
                   # and one after the last increment  
$set_incrementvalues_manually = false; # Set to true if you want to set your own non-linear values
$manual_values = array(.25, .5, 1, 2, 3,6,12,18,24,36,60,75); # Only used if $set_incrementvalues_manually is true.
$round = false;    # Set to true to round to the nearest degree day
$css_file = "wxreports.css" ;  # name of css file 
############################################################################
# End of user settings
############################################################################
$loc = $path_dailynoaa;            # Location of climatedataout*.html  
$first_year_of_data = $first_year_of_noaadata;
$uomTemp = $SITE['uomTemp'];
$temptype = array("C","F") ;
$temptype = $temptype[$SITE['uomTemp'] == "&deg;F"];
$base_temp_heating = $base_temp_heating[$temptype == "C"];
$base_temp_cooling = $base_temp_cooling[$temptype == "C"];
$incrementvalues = array($increment_size);
for ( $i = 0; $i < $increments ; $i ++ ) {                                              
    $incrementvalues[$i+1] = $incrementvalues[$i] + $increment_size;
}

if ($set_incrementvalues_manually == true){
    $incrementvalues = $manual_values;
    $increments = (count($manual_values))-1;
}


$increments = min($increments,28); // Max of 22 increments allowed

if ($increments > 13){             // If more than 13 increments, must be an even number 
    $increments = (floor($increments * 0.5) / 0.5);
} 
################
$colors = $increments +1;
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
?>

<div id="main-copy">
    <div id="report">
        
 <?php
  if ($base_temp_cooling == $base_temp_heating) {
  echo '<center><h1>'.langtransstr('Daily Degree Days').' ('.$base_temp_cooling.$uomTemp.' '.langtransstr('Base').')</h1>';
 } else {
  echo '<center><h1>'.langtransstr('Daily Degree Days').'</h1>';     
 } 

          echo '<h3>'.langtransstr('Report for').' ';
    
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


    
<?php
get_detail($year,$loc, $range, $season_start); 
$info_text = langtransstr('A Degree Day is a unit of measurement equal to a difference of one degree between the mean outdoor temperature and a reference temperature').' ('; 
if ($base_temp_cooling == $base_temp_heating){
    $info_text = $info_text . $base_temp_cooling . $uomTemp;
} else {
    $info_text = $info_text . $base_temp_cooling . $uomTemp . ' '.langtransstr('for cooling degrees and').' '.$base_temp_heating . $uomTemp . ' '.langtransstr('for heating degrees');     
}
$info_text = $info_text . '). '.langtransstr('Degree Days are used in estimating the energy needs for heating or cooling a building.');   
?>
<br /><table><tr><td class="infotext"><?php echo $info_text ?></td></tr></table>
<?php echo '<div class="dev">' . $SITE['copy'] . '</div>'; ?>    
    </div>
</div><!-- end main-copy -->

<?php
############################################################################
 @include("footer.php");
############################################################################
# Functions
############################################################################

function get_detail ($year, $loc, $range, $season_start) {
    global $SITE, $incrementvalues, $colors, $hddday, $cddday;
    global $show_today, $date, $time, $mnthname, $first_year_of_data;
    
if ($round == true) 
    $datarounding = "%01.0f";  
else 
    $datarounding = "%01.1f";
    

    // First Collect the data for the year
    
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
                $hraw[$m][0][0][6] = $hddday; 
                $craw[$m][0][0][7] = $cddday;                                 
            } else {
                $hraw[$m][0] = getnoaafile($loc . $filename,$year,$m);
                $craw[$m][0] = getnoaafile($loc . $filename,$year,$m);                            
            }
                if ($current_month AND $show_today){ 
                    $hraw[$m][0][date("j")-1][6] = $hddday;                                                        
                    $craw[$m][0][date("j")-1][7] = $cddday;                
            }
             
        }
      
    
    // Start display of info we got
    
    // Output Table with information
    
    echo '<center><table width="100%"><tr><th rowspan="2" class="labels"  width="8%">'.langtransstr('Day').'</th>';
    echo '<th colspan="12" class="labelshdd" width="46%">'.langtransstr('Heating Degree Days').'</th>';
    echo '<th colspan="12" class="labelscdd" width="46%">'.langtransstr('Cooling Degree Days').'</th></tr><tr>';    
    
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
        echo '<th  colspan="1" class="labels" width="3%"  >' . substr( $mnthname[ $raw[$i][1] ], 0, 3 ) . '</th>';
        }
        
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
        echo '<th  colspan="1" class="labels" width="3%"  >' . substr( $mnthname[ $raw[$i][1] ], 0, 3 ) . '</th>';
        }        
    echo "</tr>\n";

    
    // Setup month and year totals
    
    $ytd = 0;
    
    
    // Cycle through the possible days 
        
    for ( $day = 0 ; $day < 31 ; $day++ ) {
     
        echo '<tr><td class="reportdt">' . ($day + 1) . '</td>';
        
        // Display each months values for that day
        
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) {       
           if ($maxdays[$mnt] < $day + 1 )  { 
           echo '<td class="noday" colspan="1">&nbsp;</td>'; 
           } else {          
            if (( $hraw[$mnt][0][$day][6] == "" ) OR  ($hraw[$mnt][0][$day][6] == "-----" ) OR  ($hraw[$mnt][0][$day][6] == "X" )) {
                $put = "---";
                $hraw[$mnt][0][$day][6] = "";
            } else {
                $put = sprintf($datarounding,($hraw[$mnt][0][$day][6]));  
             }
           
            	if (($put >= 0) AND ($put != "---")){
                echo '<td class=" ' . ValueColor($put).'"' . '>' . $put .' </td>';
            } else {
               echo '<td class="reportday">' . $put . '</td>';
            }                      
                
            
                }

       }
       
        for ($mnt = 0 ; $mnt < 12 ; $mnt++ ) {       
           if ($maxdays[$mnt] < $day + 1 )  { 
           echo '<td class="noday" colspan="1">&nbsp;</td>'; 
           } else {          
            if (( $craw[$mnt][0][$day][7] == "" ) OR  ($craw[$mnt][0][$day][7] == "-----" ) OR  ($hraw[$mnt][0][$day][7] == "X" )) {
                $put = "---";
                $craw[$mnt][0][$day][7] = "";
            } else {
                $put = sprintf($datarounding,($craw[$mnt][0][$day][7]));  
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
        $cdaysofdata = $hdaysofdata = $temptotal = $hmonthmax = $cmonthmax = array(0,0,0,0,0,0,0,0,0,0,0,0); 
        $cmonthmin = $hmonthmin = array(2000,2000,2000,2000,2000,2000,2000,2000,2000,2000,2000,2000);             
    for ($i = 0; $i < 12; $i ++){
        $temp = $hraw[$i][0];
        for ($j = 0; $j < 32; $j ++){
            if (($temp[$j][6] != "") AND ($temp[$j][6] != "-----")){
            $hdaysofdata[$i] = $hdaysofdata[$i]+1;
            $htemptotal[$i] = $htemptotal[$i] + $temp[$j][6];
                if ($temp[$j][6] < $hmonthmin[$i]){
                    $hmonthmin[$i] = $temp[$j][6];
                    }
                if ($temp[$j][6] > $hmonthmax[$i]){
                    $hmonthmax[$i] = $temp[$j][6];
                    }                    
            } 
        }   
        if ($hdaysofdata[$i] != 0){        
//        $hmonthmax[$i] = max($hraw[$i][0]);
        $hmonthavg[$i] = round(($htemptotal[$i]/$hdaysofdata[$i]),2);
        }
        
      
    }  
    
    for ($i = 0; $i < 12; $i ++){
        $temp = $craw[$i][0];
        for ($j = 0; $j < 32; $j ++){
            if (($temp[$j][7] != "") AND ($temp[$j][7] != "-----")){
            $cdaysofdata[$i] = $cdaysofdata[$i]+1;
            $ctemptotal[$i] = $ctemptotal[$i] + $temp[$j][7];
                if ($temp[$j][7] < $cmonthmin[$i]){
                    $cmonthmin[$i] = $temp[$j][7];
                    }
                if ($temp[$j][7] > $cmonthmax[$i]){
                    $cmonthmax[$i] = $temp[$j][7];
                    }                    
            } 
        }   
        if ($cdaysofdata[$i] != 0){        
//        $cmonthmax[$i] = max($craw[$i][0]);
        $cmonthavg[$i] = round(($ctemptotal[$i]/$cdaysofdata[$i]),2);
        }
        
      
    }     
    
    // We are done with the daily numbers now lets show the monthly highs, averages, lows
   echo '<tr><td colspan="25" class="separator">&nbsp;</td></tr>'; 
    echo '<tr><th rowspan="1" class="labels"  width="8%">&nbsp;</th>';
    echo '<th colspan="12" class="labelshdd" width="46%">'.langtransstr('Heating Degree Days').'</th>';
    echo '<th colspan="12" class="labelscdd" width="46%">'.langtransstr('Cooling Degree Days').'</th></tr>';   
   // Put month headings 
   echo '<tr><th class="labels">&nbsp;</th>'  ;
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th  class="labels" colspan="1">' . substr( $mnthname[ $raw[$i][1] ], 0, 3 ) . '</th>';     
        }
        for ( $i = 0 ; $i < 12 ; $i++ ) 
        {    
            echo '<th  class="labels" colspan="1">' . substr( $mnthname[ $raw[$i][1] ], 0, 3 ) . '</th>';     
        }        
#############  Month Highs  ######################
   echo '</tr><tr><td class="reportttl">'.langtransstr('High').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {
        if ($hdaysofdata[$i] > 0){            
            echo '<td class="'. ValueColor($hmonthmax[$i]) .'">'. sprintf($datarounding, $hmonthmax[$i]) . '</td>';                     
        } else {
            echo '<td class="reportday">---</td>';
        }
        
    }
    
    for ( $i = 0 ; $i < 12 ; $i++ ) {
        if ($hdaysofdata[$i] > 0){            
            echo '<td class="'. ValueColor($cmonthmax[$i]) .'">'. sprintf($datarounding, $cmonthmax[$i]) . '</td>';                     
        } else {
            echo '<td class="reportday">---</td>';
        }
        
    }    
    
########### Month Average ############################
   echo '</tr><tr><td class="reportttl">'.langtransstr('Avg').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {
        if ($hdaysofdata[$i] > 0){            
            echo '<td class="'. ValueColor($hmonthavg[$i]) .'">'. sprintf($datarounding, $hmonthavg[$i]) . '</td>';                     
        } else {
            echo '<td class="reportday">---</td>';;
        }
               
    }
    
    for ( $i = 0 ; $i < 12 ; $i++ ) {
        if ($cdaysofdata[$i] > 0){            
            echo '<td class="'. ValueColor($cmonthavg[$i]) .'">'. sprintf($datarounding, $cmonthavg[$i]) . '</td>';                     
        } else {
            echo '<td class="reportday">---</td>';;
        }
               
    }    
  
########### Month Lows ############################
   echo '</tr><tr><td class="reportttl">'.langtransstr('Low').'</td>';
    for ( $i = 0 ; $i < 12 ; $i++ ) {
        if ($hdaysofdata[$i] > 0){            
            echo '<td class="'. ValueColor($hmonthmin[$i]) .'">'. sprintf($datarounding, $hmonthmin[$i]) . '</td>';                     
        } else {
            echo '<td class="reportday">---</td>';;
        }
              
    }
    
    for ( $i = 0 ; $i < 12 ; $i++ ) {
        if ($cdaysofdata[$i] > 0){            
            echo '<td class="'. ValueColor($cmonthmin[$i]) .'">'. sprintf($datarounding, $cmonthmin[$i]) . '</td>';                     
        } else {
            echo '<td class="reportday">---</td>';;
        }
              
    }    

#################################################    
$colorband_rows = ceil($colors/15);
$colorband_cols = ceil(($colors+1)/$colorband_rows);   
      echo '</tr></table></center>';
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
    for ($i = 1; $i < $limit; $i++){
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
