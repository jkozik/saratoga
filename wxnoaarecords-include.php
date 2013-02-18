<?php
ini_set('display_errors', 0); 
error_reporting(E_ALL & ~E_NOTICE);
if(isset($SITE['langMonths'])) { 
    $months = $SITE['langMonths'];
} elseif (isset($SITE['monthNames']))  {
    $months = $SITE['monthNames'];
} else {
    $months = array('January','February','March','April','May','June','July','August','September','October','November','December');
}
$wxsoftware = strtoupper($SITE['WXsoftware']) ; // Set in settings-weather.php in V3
if ($wxsoftware == ''){
    $wxsoftware = 'WD';    
}
$loc = $path_dailynoaa; 
$loc1 = $path_climatedata;
if ($wxsoftware != 'WD'){
    $show_snow_records = $show_baro_records = false;  // These options only valid for WD
}
$uomTemp = $SITE['uomTemp'];
$uomRain = $SITE['uomRain']; 
$uomWind = $SITE['uomWind'];
$uomSnow = $SITE['uomSnow'];
$uomBaro = $SITE['uomBaro'];
if ($uomSnow == ''){
    if ('IN' == strtoupper(trim($uomRain))) {
        $uomSnow = $uomRain;
    } else {
        $uomSnow = ' cm';
    }
}


// Collect the data  
 
$year = date("Y");
if ($show_today != true){
    if (( date("n") == 1) AND date("j") == 1) {
    $year = $year -1;
    }
}    
 
$years = 1 + ($year - $first_year_of_data);
$days_in_month = array(31,29,31,30,31,30,31,31,30,31,30,31) ;
if ((' IN' == strtoupper($uomSnow))  OR ('IN' == strtoupper($uomSnow))){
    $snow_today = strip_units($snowtodayin);
} else {
    $snow_today = strip_units($snowtodaycm);
}

    $baro_today = baro_mb_to_inches($baro);

$rain_to_date = $snow_to_date = array();
// Get data for seasonal rain and snow year to date
        for ( $y = 0; $y < $years ; $y ++ ) {
            $yx = $first_year_of_data + $y;         
            for ( $m = 0; $m < 12 ; $m ++ ) { 
                
              $current_month = ($yx == date("Y") && $m == ( date("n") - 1) &&((date("j") != 1 ) OR $show_today)); 
              $filename = get_noaa_filename($yx,$m,$wxsoftware,$current_month) ;                    

              if ($wxsoftware == 'WD'){                                            
                  if ($current_month){             
                      $filename1 = "climatedataout.html";                                               
                  } else { 
                      $filename1 = "climatedataout" . ( $m + 1 ) . $yx . ".html";            
                      }
              }
            $override_filename = "wxreports" . str_pad(($m + 1), 2, "0", STR_PAD_LEFT) . $yx . ".csv";
            if ((file_exists($loc . $filename)) OR (file_exists($loc.$override_filename))) {               
                      $temp = getnoaafile($loc . $filename,$yx,$m);
                      for ($i = 0; $i <$days_in_month[$m]; $i ++){ 
                      $rain = $temp[$i][8]; 
                      $rain_to_date[$y][$m] = $rain_to_date[$y][$m] + $rain ;
                      }
                      $rain_to_date[$y][$m] = $rain_to_date[$y][$m] + $rain_to_date[$y][$m-1];
                  }
                  if ((file_exists($loc1 . $filename1)) OR (file_exists($loc.$override_filename))) {
                      $temp = getclimatefile($loc1 . $filename1,'Snow Fall',$yx,$m);
                      if (is_array($temp)){   
                        $snow_to_date[$y][$m] = array_sum($temp) ;
                        $snow_to_date[$y][$m] = $snow_to_date[$y][$m] + $snow_to_date[$y][$m-1];
                        }
                      }                      
                      
                    }   
        } // End seasonal year to date
   
    // Collect the data
 
        for ( $m = 0; $m < 12 ; $m ++ ) { 
             $mtddata = $mtdsnowdata = array();
             $csvdata = array();
             $data = $snowdata = array();
               
        for ( $y = 0; $y < $years ; $y ++ ) {
             $yx = $first_year_of_data + $y;             
             $leap_year = (29 == get_days_in_month(2, $yx));          
            // Check for current year and current month 
              $current_month = ($yx == date("Y") && $m == ( date("n") - 1) &&((date("j") != 1 ) OR $show_today)); 
              $filename = get_noaa_filename($yx,$m,$wxsoftware,$current_month) ;                     
             
              if ($wxsoftware == "WD"){           
                if ($current_month){             
                    $filename1 = "climatedataout.html";                                               
                } else { 
                    $filename1 = "climatedataout" . ( $m + 1 ) . $yx . ".html";                             
                }
              }
            if ($current_month AND $show_today AND date("j")==1){
                if ($m == $rain_season_start-1){
                    $ytd_yesterday = 0;
                } else {
                    if (($m==2) AND (!$leap_year)){
                        $ytd_yesterday = $ytddata[$y][$m-1][($days_in_month[$m-1]-2)];
                        $ytdsnow_yesterday = $ytdsnowdata[$y][$m-1][($days_in_month[$m-1]-2)];                                        
                    } else {
                   $ytd_yesterday = $ytddata[$y][$m-1][($days_in_month[$m-1])-1]; 
                   $ytdsnow_yesterday = $ytdsnowdata[$y][$m-1][$days_in_month[$m-1]-1];                                          
                    }
                }
                    
                if ($m == $snow_season_start-1){
                    $ytdsnow_yesterday = 0;
                }               
                
                $data[$y][0][0] = date("j");                
                $data[$y][0][1] = strip_units($avtempsincemidnight);                
                $data[$y][0][2] = strip_units($maxtemp);
                $data[$y][0][4] = strip_units($mintemp); 
                $data[$y][0][8] = strip_units($dayrn);                
                $data[$y][0][9] = strip_units($avgspeedsincereset);
                $data[$y][0][10] = strip_units($maxgst); 
                $mtddata[$y][$m][0] = $data[$y][0][8] ;
                $ytddata[$y][$m][0] = $data[$y][0][8] + $ytd_yesterday;                
                $snowdata[$y][1][$m][1][0] = $snow_today;
                $barodata[$y][1][$m][1][0] = strip_units($baro_today);                
                $mtdsnowdata[$y][$m][0] = $snow_today;
                $ytdsnowdata[$y][$m][0] = $snow_today + $ytdsnow_yesterday;
                              
                                                                                
            } else { //}if ((file_exists($loc . $filename)) OR (file_exists($loc1 . $filename1)))  {
//                if (file_exists($loc . $filename)){
                $data[$y] = getnoaafile($loc . $filename,$yx,$m);
//                }
                if ($show_snow_records){
//                    if (file_exists($loc1 . $filename1)){
                    $snowdata[$y][1][$m][1] = getclimatefile($loc1 . $filename1,'Snow Fall',$yx,$m);
//                    }
                }
                if ($show_baro_records){
//                    if (file_exists($loc1 . $filename1)){
                    $barodata[$y][1][$m][1] = getclimatefile($loc1 . $filename1,'Avg Sea Level',$yx,$m);         
//                    }
                }                
            
            if ($current_month AND $show_today){ 
                $data[$y][date("j")-1][0] = date("j");                
                $data[$y][date("j")-1][1] = strip_units($avtempsincemidnight);                                 
                $data[$y][date("j")-1][2] = strip_units($maxtemp);
                $data[$y][date("j")-1][4] = strip_units($mintemp);
                $data[$y][date("j")-1][8] = strip_units($dayrn);                                 
                $data[$y][date("j")-1][9] = strip_units($avgspeedsincereset);
                $data[$y][date("j")-1][10] = strip_units($maxgst);
                $snowdata[$y][1][$m][1][date("j")-1] = $snow_today;   
                $barodata[$y][1][$m][1][date("j")-1] = strip_units($baro_today);                            
            }
            if ($current_month AND (date("j")==2) AND ('WD' == $wxsoftware)){  // Fix for WD rain on first day of month not listed in NOAA report until the 3rd day of the month.
                if ((strip_units($yesterdayrain))>0){
                    $data[$y][date("j")-2][8] = strip_units($yesterdayrain);    
                }              
            }
               
                
            // Check if leap year
            $leap_year = (29 == get_days_in_month(2, $yx)); 
               
                
                // calculate mtd & ytd values
            for ($i = 0; $i <$days_in_month[$m]; $i ++){
                $rain = $data[$y][$i][8];
                $snow1 = $snowdata[$y][1][$m][1][$i];                     
                $mtddata[$y][$m][$i] = $rain + $mtddata[$y][$m][$i-1];
                $mtdsnowdata[$y][$m][$i] = $snow1 + $mtdsnowdata[$y][$m][$i-1];       
                if ($i == 0){
                    // if first day of month add to last day of previous month           
                    if (($m==2) AND (!$leap_year)){
                        $ytd_yesterday = $ytddata[$y][$m-1][($days_in_month[$m-1]-2)];
                        $ytdsnow_yesterday = $ytdsnowdata[$y][$m-1][($days_in_month[$m-1]-2)];                  
                    } else {                                               
                        $ytd_yesterday = $ytddata[$y][$m-1][$days_in_month[$m-1]-1];
                        $ytdsnow_yesterday = $ytdsnowdata[$y][$m-1][$days_in_month[$m-1]-1];                     
                    }
                    if ($m == 0){
                        $ytd_yesterday = $rain_to_date[$y-1][11] - $rain_to_date[$y-1][$rain_season_start-2];                                    
                        $ytdsnow_yesterday = $snow_to_date[$y-1][11] - $snow_to_date[$y-1][$snow_season_start-2] ;                                                    }                                                                 
                                                          
                    if ($m == $rain_season_start-1){
                        $ytd_yesterday = 0;
                    }
                    
                    if ($m == $snow_season_start-1){
                        $ytdsnow_yesterday = 0;
                    }                    

                }                                          
                else {        // if not first day of month
                    $ytd_yesterday = $ytddata[$y][$m][$i-1]; 
                    $ytdsnow_yesterday = $ytdsnowdata[$y][$m][$i-1];
               
                    }
                    
                $ytddata[$y][$m][$i] = $rain + $ytd_yesterday;
                $ytdsnowdata[$y][$m][$i] = $snow1 + $ytdsnow_yesterday;  
                                                       
            }                    
        }             
        }
        // All data for this month for all years has been collected. Now find the records
          $recordhighs = $recordlows = $recordrain = $recordmtd = $recordytd = $record_low_highs = $record_high_lows = array();
          $recordgusts = $recordwind = $recordgusts_years = $recordwind_years = array();
          $recordhighs_years = $recordlows_years = $recordrain_years = $recordmtd_years = $recordytd_years = array();
          $record_lowest_highs_years = $record_highest_lows_years = array();
          $recordsnow = $recordsnowmtd = $recordsnowytd = $recordsnow_years = $recordsnowmtd_years = $recordsnowytd_years = array();
          $recordhighbaro = $recordlowbaro = $recordhighbaro_years = $recordlowbaro_years = array();
        
        for ($i = 0; $i <$days_in_month[$m]; $i ++){
                      $mtd_rain = 0;
                      $mtd_snow = 0;
 
        for ( $y = 0; $y < $years ; $y ++ ) {

           $day = $data[$y][$i][0];
           $snowday = $i +1;
           $high = $data[$y][$i][2];
           $low = $data[$y][$i][4];
           $rain = $data[$y][$i][8]; 
           $wind = $data[$y][$i][9];
           $gust = $data[$y][$i][10];
           $snow1 = $snowdata[$y][1][$m][1][$i];            
           $baro1 = $barodata[$y][1][$m][1][$i];
           $baro1 = baro_mb_to_inches($baro1);
           if ($baro1 == "-----"){
               $baro1 = "";
           }
           
           if ($snow1 == "-----"){
               $snow1 = "";
               $snowday = "";
           }           
            if (($i == 28) AND ($m==1) AND (29 != get_days_in_month(($m+1),$first_year_of_data + $y) )){
             // Check for leap day
            } else {                     
           
           if ((($high > $recordhighs[$day-1]) OR ($recordhighs[$day-1] == "")) AND ($high != "")){
            $recordhighs[$day-1] = $high;
            $recordhighs_years[$day-1] = $first_year_of_data + $y;
           } 
           
           if (($high < $record_low_highs[$day-1]) OR ($record_low_highs[$day-1] == "")){
               if (date("j")== $day AND date("Y")== ($first_year_of_data + $y) AND ($record_lowest_highs_years[$day-1] != '')){
           } else {  
            if ($high != ''){  
            $record_low_highs[$day-1] = $high;
            $record_lowest_highs_years[$day-1] = $first_year_of_data + $y;
            }
               }
               }           
           
           if ((($low < $recordlows[$day-1]) OR ($recordlows[$day-1] == "")) AND ($low != "")){
            $recordlows[$day-1] = $low;
            $recordlows_years[$day-1] = $first_year_of_data + $y;
           } 
                    
           if (($low > $record_high_lows[$day-1]) OR ($record_high_lows[$day-1] == "")){
               if ((date("j")== $day) AND (date("Y")== ($first_year_of_data + $y)) AND ($record_highest_lows_years[$day-1] != '')){
           } else {                
            $record_high_lows[$day-1] = $low;
            $record_highest_lows_years[$day-1] = $first_year_of_data + $y;
           }
           }                      
           
           if (($rain > $recordrain[$day-1]) OR ($recordrain[$day-1] == "")) {
            $recordrain[$day-1] = $rain;
            $recordrain_years[$day-1] = $first_year_of_data + $y;
           }
           
           $mtd_rain = $mtddata[$y][$m][$i];         
            if (($mtd_rain > $recordmtd[$day-1]) OR ($recordmtd[$day-1] == "")){
            $recordmtd[$day-1] = (string)$mtd_rain;
            $recordmtd_years[$day-1] = $first_year_of_data + $y;
           }  
                      
           $ytd_rain = $ytddata[$y][$m][$i];         
            if (($ytd_rain > $recordytd[$day-1]) OR ($recordytd[$day-1] == "")){
            $recordytd[$day-1] = (string)$ytd_rain;
            $recordytd_years[$day-1] = $first_year_of_data + $y;
           }

           if (($gust > $recordgusts[$day-1]) OR ($recordgusts[$day-1] == "")){
            $recordgusts[$day-1] = $gust;
            $recordgusts_years[$day-1] = $first_year_of_data + $y;
           } 
           
           if (($wind > $recordwind[$day-1]) OR ($recordwind[$day-1] == "")){
               if (date("j")== $day AND date("Y")== ($first_year_of_data + $y) AND ($recordwind_years[$day-1]!='')){
           } else {                
            $recordwind[$day-1] = $wind;
            $recordwind_years[$day-1] = $first_year_of_data + $y;
           }
           }
           
           if (($snow1 > $recordsnow[$snowday-1]) OR ($recordsnow[$snowday-1] == "")) {
            $recordsnow[$snowday-1] = $snow1; 
            $recordsnow_years[$snowday-1] = $first_year_of_data + $y;
           }
           
           $mtd_snow = $mtdsnowdata[$y][$m][$i];         
            if (($mtd_snow > $recordsnowmtd[$snowday-1]) OR ($recordsnowmtd[$snowday-1] == "")){
            $recordsnowmtd[$snowday-1] = (string)$mtd_snow;
            $recordsnowmtd_years[$snowday-1] = $first_year_of_data + $y;
           }  
                      
           $ytd_snow = $ytdsnowdata[$y][$m][$i];         
            if (($ytd_snow > $recordsnowytd[$snowday-1]) OR ($recordsnowytd[$snowday-1] == "")){
            $recordsnowytd[$snowday-1] = (string)$ytd_snow;
            $recordsnowytd_years[$snowday-1] = $first_year_of_data + $y;
           } 
           
           if (($wind > $recordwind[$day-1]) OR ($recordwind[$day-1] == "")){
               if (date("j")== $day AND date("Y")== ($first_year_of_data + $y) AND ($recordwind_years[$day-1]!='')){
           } else {                
            $recordwind[$day-1] = $wind;
            $recordwind_years[$day-1] = $first_year_of_data + $y;
           }
           }           
           
           if (($baro1 > $recordhighbaro[$snowday-1]) OR ($recordhighbaro[$snowday-1] == "")){
               if (date("j")== $snowday AND date("Y")== ($first_year_of_data + $y) AND ($recordhighbaro[$snowday-1]!='')){
           } else {               
            $recordhighbaro[$snowday-1] = $baro1;
            $recordhighbaro_years[$snowday-1] = $first_year_of_data + $y;
           }
           } 
           
           if ((($baro1 < $recordlowbaro[$snowday-1]) OR ($recordlowbaro[$snowday-1] == "")) AND (($baro1 != ""))){
               if (date("j")== $snowday AND date("Y")== ($first_year_of_data + $y) AND ($recordlowbaro[$snowday-1]!='')){
           } else {               
            $recordlowbaro[$snowday-1] = $baro1;
            $recordlowbaro_years[$snowday-1] = $first_year_of_data + $y;
           }
           }                                                  
           
                                 
            }
        }
        }
        
                
                for ($i = 0; $i <$days_in_month[$m]; $i ++){
        $csvdata[$i]= array(($i+1),$recordhighs[$i],$recordhighs_years[$i],$recordlows[$i],$recordlows_years[$i],$recordrain[$i],$recordrain_years[$i],$recordmtd[$i],$recordmtd_years[$i],$recordytd[$i],$recordytd_years[$i],$record_low_highs[$i],$record_lowest_highs_years[$i],$record_high_lows[$i],$record_highest_lows_years[$i],$recordgusts[$i],$recordgusts_years[$i],
$recordwind[$i],$recordwind_years[$i],$recordsnow[$i],$recordsnow_years[$i],$recordsnowmtd[$i],$recordsnowmtd_years[$i],$recordsnowytd[$i],$recordsnowytd_years[$i],$recordhighbaro[$i],$recordhighbaro_years[$i],$recordlowbaro[$i],$recordlowbaro_years[$i]);                          
        }
         
         $monthlydata[$m] = $csvdata;       
        }   // end of daily data for month m for all years
        

// use current month as default
$month_number = date(n);
$month = langtransstr($months[$month_number-1]);
$year = date(Y);


if (isset($_GET['month'])) {
    $month = ($_GET['month']);
}
?>

<div id="main-copy"> 
	<div id="rain"> 
		<div class="getraindtbxfloat">             
			<div class="getraindtbx" ><?php langtrans('Other Months'); ?>:<br/>
				<form method="get" action="<?php echo $SITE['self']; ?>" >
					<select name="month">
<?php  foreach ($months as $value) {
	echo "\t\t\t\t\t\t" .
	'<option value="' . $value . '">' . $value .
		' </option>' . "\n";   
}

 ?>
					</select>
                    <input type="submit" value="<?php langtrans('Go')?>" />                    
				</form>
            </div>                   
        </div>
      
    <center><h2><?php echo $heading_name . ' ' . langtransstr('Daily Records'); ?></h2></center>  
	<br /><?php echo langtransstr('Records are from data collected at') .' '. $heading_name .' '. langtransstr('since'). ' ' . $recording_start_date ?>.
      <table>
	  <?php $columns = 8 + (3 * $show_snow_records) + $show_baro_records;
       echo '<tr><th class="labels1" colspan="'.$columns.'">'.$month."</th></tr>" ?>
        <tr>
            <th class="labels" ><?php langtrans('Day'); ?></th>
            <th class="labels" ><?php langtrans('High'); ?><br /><?php langtrans('Temperature'); ?><sup>1</sup><br />(<?php echo trim($uomTemp).')';?></th>
            <th class="labels" ><?php langtrans('Low'); ?><br /><?php langtrans('Temperature'); ?><sup>2</sup><br />(<?php echo trim($uomTemp).')';?></th>
            <th class="labels" ><?php langtrans('Daily Rain'); ?><br />(<?php echo trim($uomRain).')';?></th>
            <th class="labels" ><?php langtrans('Month To Date Rain'); ?><br />(<?php echo trim($uomRain).')';?></th>
            <th class="labels" ><?php langtrans('Year To Date Rain'); ?><?php
            if ($rain_season_start != 1){
            echo '<sup>3</sup>';
            $rain_year_note = '<sup>3</sup> - '. langtransstr('Rain year is').' '.$months[$rain_season_start-1].' '. langtransstr('to').' '.$months[$rain_season_start-2].'.<br />';
            } else $rain_year_note = ''; 
             ?>
            <br />(<?php echo trim($uomRain).')';?></th>
            <th class="labels" ><?php langtrans('High Wind Gust'); ?><br />(<?php echo trim($uomWind).')';?></th>
            <th class="labels" ><?php langtrans('Daily Average Wind'); ?><br />(<?php echo trim($uomWind).')';?></th> 
            <?php
            $snow_year_note = '';
            if ($show_snow_records){
                if ($snow_season_start == 1){
                    $sup = '';
                } else {
                    $sup = '<sup>'.(3 + ($rain_season_start != 1)).'</sup>';
                    $snow_year_note = $sup.' - '. langtransstr('Snow year is').' '.$months[$snow_season_start-1].' '.langtransstr('to').' '.$months[$snow_season_start-2].'.<br />';
                }    
            echo '<th class="labels">'. langtransstr('Daily Snow').'<br />(' . trim($uomSnow).')</th>';
            echo '<th class="labels">'. langtransstr('Month To Date Snow').'<br />('. trim($uomSnow).')</th>';
            echo '<th class="labels">'. langtransstr('Year To Date Snow').$sup.'<br />('. trim($uomSnow).')</th> ';
            }
            if ($show_baro_records){
            echo '<th class="labels">'. langtransstr('Average Barometric Pressure').'<br />('. trim($uomBaro).')</th> ';
            }
            ?>                     
                                   
        </tr>  

<?php

 switch ($month) {
    case $months[0]:
        $m = "1";
        break;
    case $months[1]:
        $m = "2";
        break; 
    case $months[2]:
        $m = "3";
        break; 
    case $months[3]:
        $m = "4";
        break; 
    case $months[4]:
        $m = "5";
        break; 
    case $months[5]:
        $m = "6";
        break; 
    case $months[6]:
        $m = "7";
        break; 
    case $months[7]:
        $m = "8";
        break; 
    case $months[8]:
        $m = "9";
        break; 
    case $months[9]:
        $m = "10";
        break; 
    case $months[10]:
        $m = "11";
        break; 
    case $months[11]:
        $m = "12";
        break; 
}

getrecordsdata($monthlydata[$m-1],$m,$month,$days_in_month[$m-1]);                                                        

echo '</table><div class="dev"><sup>1</sup> - '. langtransstr('Highest Temperature / Lowest High Temperature').'<br /><sup>2</sup> - '.langtransstr('Lowest Temperature / Highest Low Temperature').'<br />';
echo $rain_year_note;
echo $snow_year_note;
echo langtransstr('Highlighted values indicate records set in the last 365/366 days.').'<br />';         
echo $SITE['copy'] . '</div>';

?> 
</div><!--end rain --> 
</div> <!--end main-copy --> 

 <?php 
############################################################################
@include("footer.php");
###########################################################################

function getrecordsdata ($data,$m, $month,$max_days) {
global $uomTemp, $uomRain, $uomWind, $uomSnow, $uomBaro;
global $show_today, $show_snow_records, $show_baro_records, $rain_season_start, $snow_season_start;
if ((' IN' == strtoupper($uomRain))  OR ('IN' == strtoupper($uomRain))){
    $rainrounding = "%01.2f";
}else{
    $rainrounding = "%01.1f";    
}  
if ((' INHG' == strtoupper($uomBaro))  OR ('INHG' == strtoupper($uomBaro))){
    $barorounding = "%01.2f";
}else{
    $barorounding = "%01.1f";    
}
    $line_end = '<br />';    
    $line_end1 = '</td></tr>';
    $line_start = '<td class="temps"><table><tr>';
    $noclass = '"nodata1"';
    $lowclass = '"lowtemp1"';
    $highclass = '"hightemp1"';
    $reclowclass = '"lowtempnewrecord1"';
    $rechighclass = '"hightempnewrecord1"';    
    $rawdata = array();
    $year = date(Y);
    $day = date(j);
    $selectedmonth = intval($m);
    $currentmonth = intval(date(m)); 
	if ($currentmonth < $selectedmonth){  // If selected month is greater than current month, use previous year for hightlights
		$year = $year - 1;
	}
    $hilorecords = $hirecords = $hibarorecords = array(-100,0)  ; // Initilize for high temps
    $lohirecords = $lowrecords = array(200,0)  ; // Initilize for low temps
    $rainrecords = $mtdrecords = $ytdrecords = $gustrecords = $windrecords = array(-10,0)  ; // Initilize for rain & wind  
    $snowrecords = $mtdsnowrecords = $ytdsnowrecords = array(-10,0)  ; // Initilize for snow
    $lowbarorecords = array(2000,0);
       
        for ( $i = 0; $i < $max_days ; $i ++ ) {  
            $line = $data[$i];

            echo "<tr>";
            if (($currentmonth == $selectedmonth) AND ($day == $line[0])){
                echo '<th class="today">'.$line[0].'</th>'; 
            } else {
                echo '<th class="labels">'.$line[0].'</th>';   // Day
            }
            
###############   Record High   ###################            
            if ($line[1] == ''){
                echo $line_start . '<td class='.$noclass.'> --- '.$line_end1;   // No Data
            }
            else {
                $record_year = $year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)));
                if ($record_year == intval($line[2])) {  // If record was set this year, highlight cell
                $class = $rechighclass; //'"hightempnewrecord"';
            }   else {
                $class = $highclass; //'"hightemp"';
            }
                echo $line_start .'<td class='.$class.'>'. sprintf("%01.1f", $line[1]) . ' ('.$line[2].')'.$line_end1;  // High Temp </td>
            }
            
            if ((floatval($line[1])>floatval($hirecords[0])) AND ($line[1]!="")) {
                $hirecords = array($line[1] , $line[2]);
            }
            
###############   Lowest High   ################### 
          
            if ($line[11] == ''){
                echo '<tr><td class="nodata1"> --- </td></tr></table></td>';   // No Data
            }
            else {
                $record_year = $year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)));
                if ($record_year == intval($line[12])){  // If record was set this year, highlight cell
                $class = $reclowclass; //'"hightempnewrecord"';
            }   else {
                $class = $lowclass ; //'"hightemp"';
            }
                echo ' <tr><td class='.$class.'>'. sprintf("%01.1f", $line[11]) . ' ('.$line[12].')</td></tr></table></td>';  // Lowest High Temp
            }
            
            if ((floatval($line[11])<floatval($lohirecords[0])) AND ($line[11]!="")) {
                $lohirecords = array($line[11] , $line[12]);
            }  
                  

###############   Record Low   ################### 
               
            if ($line[3] == ''){
                echo $line_start . '<td class='.$noclass.'> --- '.$line_end1; // No Data
            }
            else { 
                $record_year = $year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)));
                if ($record_year == intval($line[4])) {  // If record was set this year, highlight cell
                    $class = $reclowclass; //'"lowtempnewrecord"';
                } else {
                    $class = $lowclass; //'"lowtemp"';
                }             
                echo $line_start .'<td class='.$class.'>'. sprintf("%01.1f",$line[3]).' ('.$line[4].')'.$line_end1;   // Low Temp
            }
             
            if ((floatval($line[3])<floatval($lowrecords[0])) AND ($line[3]!="")) {
                $lowrecords = array($line[3] , $line[4]);
            }
            
###############   Highest Low   ################### 
         
            if ($line[13] == ''){
                echo '<tr><td class="nodata1"> --- </td></tr></table></td>';   // No Data
            }
            else {
                $record_year = $year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)));
                if ($record_year == intval($line[14])) {  // If record was set this year, highlight cell
                $class = $rechighclass; //'"lowtempnewrecord"';
            }   else {
                $class = $highclass; //'"lowtemp"';
            }
                echo '<tr><td class='.$class.'>'. sprintf("%01.1f", $line[13]) . ' ('.$line[14].')</td></tr></table></td>';  // Highest Low Temp
            }
            
            if ((floatval($line[13])>floatval($hilorecords[0])) AND ($line[13]!="")) {
                $hilorecords = array($line[13] , $line[14]);
            }  
                    
            
###############   Record Daily Rain   ###################                          
             
            if ($line[5] == ''){
                echo '<td class="nodata"> --- </td>';   // No Data
            }
            else {
                if (($year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)))) == intval($line[6])) {  // If record was set this year, highlight cell
                    $class = '"raindaynewrecord"';
                }   else {
                $class = '"rainday"';
                }                
                echo '<td class='.$class.'>'.sprintf($rainrounding,$line[5]).'<br /> ('.$line[6].')</td>';   // Rain 
            }
            if ((floatval($line[5])>floatval($rainrecords[0])) AND ($line[5]!="")) {
                $rainrecords = array($line[5] , $line[6]);
            }              

###############   Record MTD Rain   ###################  
            
            if ($line[7] == ''){                                          
                echo '<td class="nodata"> --- </td>';   // No Data
            }
            else {            
                if (($year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)))) == intval($line[8])) {  // If record was set this year, highlight cell
                    $class = '"mtddaynewrecord"';
                }   else {
                    $class = '"mtdday"';
                }             
                echo '<td class='.$class.'>'.sprintf($rainrounding,$line[7]).'<br /> ('.$line[8].')</td>';   // MTD Rain
            } 
            if ((floatval($line[7])>floatval($mtdrecords[0])) AND ($line[7]!="")) {
                $mtdrecords = array($line[7] , $line[8]);
            }             

###############   Record YTD Rain   ###################  
            
            if ($line[9] == ''){
                echo '<td class="nodata"> --- </td>';   // No Data
            }
            else {            
                if (($year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)))) == intval($line[10])) {  // If record was set this year, highlight cell
                    $class = '"ytddaynewrecord"';
                }   else {
                    $class = '"ytdday"';
                }
                if ($rain_season_start == 1){
                    $record_year = $line[10];
                } else {
                    if ($m > $rain_season_start-1){
                        $record_year = ($line[10]).'/'.($line[10]+1);
                    } else {
                        $record_year = ($line[10]-1).'/'.($line[10]);                        
                    }
                }                                
                echo '<td class='.$class.'>'.sprintf($rainrounding,$line[9]).'<br /> ('.$record_year.')</td>';   // YTD Rain 
            }
             if ((floatval($line[9])>floatval($ytdrecords[0])) AND ($line[9]!="")) {
                $ytdrecords = array($line[9] , $record_year);
            } 
            
###############   Record Gust   ###################            
            if ($line[15] == ''){
                echo '<td class="nodata"> --- </td>';   // No Data
            }
            else {
                $record_year = $year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)));
                if ($record_year == intval($line[16])) {  // If record was set this year, highlight cell
                $class = '"gustnewrecord"';
            }   else {
                $class = '"gustday"';
            }
                echo '<td class='.$class.'>'. sprintf("%01.1f", $line[15]) . '<br /> ('.$line[16].')</td>';  // High Gust </td>
            }
            
            if ((floatval($line[15])>floatval($gustrecords[0])) AND ($line[15]!="")) {
                $gustrecords = array($line[15] , $line[16]);
            }   
            
###############   Record Daily Windrun   ###################            
            if ($line[17] == ''){
                echo '<td class="nodata"> --- </td>';   // No Data
            }
            else {
                $record_year = $year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)));
                if ($record_year == intval($line[18])) {  // If record was set this year, highlight cell
                $class = '"windnewrecord"';
            }   else {
                $class = '"windday"';
            }
                echo '<td class='.$class.'>'. sprintf("%01.1f", $line[17]) . '<br /> ('.$line[18].')</td>';  // High Windrun </td>
            }
            
            if ((floatval($line[17])>floatval($windrecords[0])) AND ($line[17]!="")) {
                $windrecords = array($line[17] , $line[18]);
            }
###############   Record Daily Snow   ###################                          
            if ($show_snow_records){
            if ($line[19] == ''){
                echo '<td class="nodata"> --- </td>';   // No Data
            }
            else {
                if (($year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)))) == intval($line[20])) {  // If record was set this year, highlight cell
                    $class = '"snowdaynewrecord"';
                }   else {
                $class = '"snowday"';
                }                
                echo '<td class='.$class.'>'.sprintf("%01.1f",$line[19]).'<br /> ('.$line[20].')</td>';   // Snow
            }
            if ((floatval($line[19])>floatval($snowrecords[0])) AND ($line[19]!="")) {
                $snowrecords = array($line[19] , $line[20]);
            }              

###############   Record MTD Snow   ###################  
            
            if ($line[19] == ''){      //21                                    
                echo '<td class="nodata"> --- </td>';   // No Data
            }
            else {            
                if (($year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)))) == intval($line[22])) {  // If record was set this year, highlight cell
                    $class = '"snowdaynewrecord"';
                }   else {
                    $class = '"snowday"';
                }             
                echo '<td class='.$class.'>'.sprintf("%01.1f",$line[21]).'<br /> ('.$line[22].')</td>';   // MTD Snow
            } 
            if ((floatval($line[21])>floatval($mtdsnowrecords[0])) AND ($line[21]!="")) {
                $mtdsnowrecords = array($line[21] , $line[22]);
            }             

###############   Record YTD Snow   ###################  
            
            if ($line[19] == ''){   //23
                echo '<td class="nodata"> --- </td>';   // No Data
            }
            else {            
                if (($year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)))) == intval($line[24])) {  // If record was set this year, highlight cell
                    $class = '"snowdaynewrecord"';
                }   else {
                    $class = '"snowday"';
                }
                if ($snow_season_start == 1){
                    $record_year = $line[24];
                } else {
                    if ($m > $snow_season_start-1){
                        $record_year = ($line[24]).'/'.($line[24]+1);
                    } else {
                        $record_year = ($line[24]-1).'/'.($line[24]);                        
                    }
                }
                           
                echo '<td class='.$class.'>'.sprintf("%01.1f",$line[23]).'<br /> ('.$record_year.')</td>';   // YTD Snow
            }
             if ((floatval($line[23])>floatval($ytdsnowrecords[0])) AND ($line[23]!="")) {
                $ytdsnowrecords = array($line[23] , $record_year);
            } 
            }
###############   Record High Barometer   ###################            
            if ($show_baro_records){
            if ($line[25] == ''){
                echo $line_start . '<td class='.$noclass.'> --- '.$line_end1;   // No Data
            }
            else {
                $record_year = $year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)));
                if ($record_year == intval($line[26])) {  // If record was set this year, highlight cell
                $class = '"baronewrecord"'; //'"hightempnewrecord"';
            }   else {
                $class = '"hibaroday"'; //'"hightemp"';
            }
                echo $line_start .'<td class='.$class.'>'. sprintf($barorounding, $line[25]) . ' ('.$line[26].')'.$line_end1;  // High Baro </td>
            }
            
            if ((floatval($line[25])>floatval($hibarorecords[0])) AND ($line[25]!="")) {
                $hibarorecords = array($line[25] , $line[26]);
            }
            
###############   Record Low Barometer   ################### 
         
            if ($line[27] == ''){
                echo '<tr><td class="nodata1"> --- </td></tr></table></td>';   // No Data
            }
            else {
                $record_year = $year - (($currentmonth == $selectedmonth) AND ($day <= ($line[0]-$show_today)));
                if ($record_year == intval($line[28])){  // If record was set this year, highlight cell
                $class = '"baronewrecord"'; //'"hightempnewrecord"';
            }   else {
                $class = '"lowbaroday"' ; //'"hightemp"';
            }
                echo ' <tr><td class='.$class.'>'. sprintf($barorounding, $line[27]) . ' ('.$line[28].')</td></tr></table></td>';  // Lowest Baro
            }
            
            if ((floatval($line[27])<floatval($lowbarorecords[0])) AND ($line[27]!="")) {
                $lowbarorecords = array($line[27] , $line[28]);
            }  
            }                      
                        
###########                                              
            
            echo "</tr>"; 
        }
        

 
    echo '<tr><td class="rainttl">' . $month . ' '.langtransstr('Record').'</td>';
    
    if ($hirecords[0]==-100){
        echo '<td class="nodata"> --- </td>';   // No Data 
    } else {   
        echo '<td class="rainttl">' . sprintf("%01.1f", $hirecords[0]) .' (' . $hirecords[1] . ')<br />';
        echo sprintf("%01.1f", $lohirecords[0]) . ' (' . $lohirecords[1] . ')</td>' ;
    }
    
    if ($lowrecords[0]==200){
        echo '<td class="nodata"> --- </td>';   // No Data 
    } else {      
    echo '<td class="rainttl">' . sprintf("%01.1f", $lowrecords[0]) . ' (' . $lowrecords[1] . ')<br />';
    echo sprintf("%01.1f", $hilorecords[0]) . ' (' . $hilorecords[1] . ')</td>' ;
    }
    
    if ($rainrecords[0]==-10){
        echo '<td class="nodata"> --- </td>';   // No Data 
    } else {                 
    echo '<td class="rainttl">' . sprintf($rainrounding, $rainrecords[0]). '<br />(' . $rainrecords[1] . ')</td>';
    }
    
    if ($mtdrecords[0]==-10){
        echo '<td class="nodata"> --- </td>';   // No Data 
    } else {                
    echo '<td class="rainttl">' . sprintf($rainrounding, $mtdrecords[0]) . '<br />(' . $mtdrecords[1] . ')</td>';
    }
    
    if ($ytdrecords[0]==-10){
        echo '<td class="nodata"> --- </td>';   // No Data 
    } else {     
    echo '<td class="rainttl">' . sprintf($rainrounding, $ytdrecords[0]) . '<br />(' . $ytdrecords[1] . ')</td>';
    } 
    
    if ($gustrecords[0]==-10){
        echo '<td class="nodata"> --- </td>';   // No Data 
    } else {     
    echo '<td class="rainttl">' . sprintf("%01.1f", $gustrecords[0]) . '<br />(' . $gustrecords[1] . ')</td>';
    }  
    
    if ($windrecords[0]==-10){
        echo '<td class="nodata"> --- </td>';   // No Data 
    } else {     
    echo '<td class="rainttl">' . sprintf("%01.1f", $windrecords[0]) . '<br />(' . $windrecords[1] . ')</td>';
    } 
 
    if ($show_snow_records) {  
    if ($snowrecords[0]==-10){
        echo '<td class="nodata"> --- </td>';   // No Data 
    } else {                 
    echo '<td class="rainttl">' . sprintf("%01.1f", $snowrecords[0]). '<br />(' . $snowrecords[1] . ')</td>';
    }
    
    if ($snowrecords[0]==-10){
        echo '<td class="nodata"> --- </td>';   // No Data 
    } else {                
    echo '<td class="rainttl">' . sprintf("%01.1f", $mtdsnowrecords[0]) . '<br />(' . $mtdsnowrecords[1] . ')</td>';
    }
    
    if ($snowrecords[0]==-10){
        echo '<td class="nodata"> --- </td>';   // No Data 
    } else {     
    echo '<td class="rainttl">' . sprintf("%01.1f", $ytdsnowrecords[0]) . '<br />(' . $ytdsnowrecords[1] . ')</td>';
    }
    }
    
    if ($show_baro_records){
    if ($hibarorecords[0]==-100){
        echo '<td class="nodata"> --- </td>';   // No Data 
    } else {     
    echo '<td class="rainttl">' . sprintf($barorounding, $hibarorecords[0]) . ' (' . $hibarorecords[1] . ')<br />';
    echo sprintf($barorounding, $lowbarorecords[0]) . ' (' . $lowbarorecords[1] . ')</td>';    
    }                
    }
          
    echo "</tr>";
      
}

#################################################################
function getnoaafile ($filename,$year,$m) {
    global $SITE;                               
    $rawdata = array();
    if (file_exists($filename)) {    
    $fd = @fopen($filename,'r');
    $i = 0;
    $startdt = 0;
    if ( $fd ) {
    
        while ( !feof($fd) ) { 
        
            // Get one line of data
            $gotdat = trim ( fgets($fd,8192) );
            $i++ ;
            if ($startdt == 1 ) {
                if ( strpos ($gotdat, "--------------" ) !== FALSE ){
                    if ($i != ($first_dash_line+1)){
                    $startdt = 2;
                    } 
                } else {
                    $gotdat = str_replace(",",".",$gotdat); 
                    $foundline = preg_split("/[\n\r\t ]+/", $gotdat );                    
                    $rawdata[intval ($foundline[0]) -1 ] = $foundline;
                }
            }
        
            if ($startdt == 0 ) {
                if ( strpos ($gotdat, "--------------" ) !== FALSE ){
                    $startdt = 1;
                    $first_dash_line = $i;
                } 
            }
        }
        // Close the file we are done getting data
        fclose($fd);
    }
    }
    $rawdata = check_for_overrides($year,$m,$rawdata,'noaa','');
    return($rawdata);
}      
          
 function check_for_overrides($year,$m,$rawdata,$type,$target){

        $override_filename = "wxreports" . str_pad(($m + 1), 2, "0", STR_PAD_LEFT) . $year . ".csv";
        if (file_exists($override_filename)){
        $override_data = getoverrides($override_filename);
        if ($type == 'noaa'){
            $rawdata = fixnoaareport ($rawdata,$override_data);
        } else {
            $rawdata = fixclimatereport ($rawdata,$override_data,$target);
        }
        }
                        
    return($rawdata);
}

function getoverrides ($filename) {
    global $SITE;                 
    $cooked = array();
    
    if (($fp = fopen($filename, "r")) !== FALSE) {
        # Set the parent multidimensional array key to 0.
        $nn = 0;
        while (($data = fgetcsv($fp, 2500, ",")) !== FALSE) {
            # Count the total keys in the row.
            $c = count($data);
            # Populate the multidimensional array.
            for ($x=0;$x<$c;$x++)
            {
               if ($nn>0) {
                    $data[$x]  = preg_replace('/\s+/', '',$data[$x]);   // remove any whitespace  
                }
                
                $cooked[$nn][$x] = $data[$x];
            }
            $nn++;
        }

        fclose($fp);
    }
    return($cooked);
} 

function fixnoaareport ($rawdata,$override_data) {
    $header = $override_data[0];
    $mean_temp_ix = array_search('Mean Temp',$header);
    $max_temp_ix = array_search('Max Temp',$header);
    $min_temp_ix = array_search('Min Temp',$header);
    $rain_ix = array_search('Rain',$header);
    $avg_wind_ix = array_search('Avg Wind',$header);
    $max_wind_ix = array_search('Max Wind',$header);
    $hdd_ix = array_search('HDD',$header);
    $cdd_ix = array_search('CDD',$header);
    $noaa_mean_temp_ix = 1;
    $noaa_max_temp_ix = 2;
    $noaa_min_temp_ix = 4;
    $noaa_rain_ix = 8;
    $noaa_avg_wind_ix = 9;
    $noaa_max_wind_ix = 10;
    $noaa_hdd_ix = 6;
    $noaa_cdd_ix = 7;
    
   $rows = count($rawdata,0);
   $days_raw = array();
    
   for ($i=0 ; $i < $rows ; $i++){
       $days_raw[$i]= $rawdata[$i][0];
       $days_raw_ix[$i]= $i;
   }
 
   $rows = count($override_data,0);
      
   for ($i=1 ; $i < $rows ; $i++){
       $days_over[$i-1]= ($override_data[$i][0]);
   }   
   
   for ($i=1 ; $i < 32; $i++){
       if (in_array($i,$days_raw)){
           $fixed_data[$i-1] = $rawdata[$i-1];
       }
       if (in_array($i,$days_over)){
           $override_ix = 1 + array_search($i,$days_over);
           if ($override_data[$override_ix][$max_temp_ix] != ''){
               if (strtolower($override_data[$override_ix][$max_temp_ix]) == 'd'){
                   $fixed_data[$i-1][$noaa_max_temp_ix] = '';
               } else {
                   $fixed_data[$i-1][$noaa_max_temp_ix] = $override_data[$override_ix][$max_temp_ix];                       }
           }
           if ($override_data[$override_ix][$mean_temp_ix] != ''){
                if (strtolower($override_data[$override_ix][$mean_temp_ix]) == 'd'){
                   $fixed_data[$i-1][$noaa_mean_temp_ix] = '';
                   } else {
               $fixed_data[$i-1][$noaa_mean_temp_ix] = $override_data[$override_ix][$mean_temp_ix];  
                   }             
           }
           if ($override_data[$override_ix][$min_temp_ix] != ''){
               if (strtolower($override_data[$override_ix][$min_temp_ix]) == 'd'){
                   $fixed_data[$i-1][$noaa_min_temp_ix] = '';
                   } else {
                $fixed_data[$i-1][$noaa_min_temp_ix] = $override_data[$override_ix][$min_temp_ix];
                   }               
           }
           if ($override_data[$override_ix][$rain_ix] != ''){
               if (strtolower($override_data[$override_ix][$rain_ix]) == 'd'){
                   $fixed_data[$i-1][$noaa_rain_ix] = '';
                   } else {
               $fixed_data[$i-1][$noaa_rain_ix] = $override_data[$override_ix][$rain_ix];
                   }               
           }
           if ($override_data[$override_ix][$avg_wind_ix] != ''){
                if (strtolower($override_data[$override_ix][$avg_wind_ix]) == 'd'){
                   $fixed_data[$i-1][$noaa_avg_wind_ix] = '';
                   } else {
               $fixed_data[$i-1][$noaa_avg_wind_ix] = $override_data[$override_ix][$avg_wind_ix];  
                   }             
           }
           if ($override_data[$override_ix][$max_wind_ix] != ''){
               if (strtolower($override_data[$override_ix][$max_wind_ix]) == 'd'){
                   $fixed_data[$i-1][$noaa_max_wind_ix] = '';
                   } else {
               $fixed_data[$i-1][$noaa_max_wind_ix] = $override_data[$override_ix][$max_wind_ix];
                   }               
           }
           if ($override_data[$override_ix][$hdd_ix] != ''){
               if (strtolower($override_data[$override_ix][$hdd_ix]) == 'd'){
                   $fixed_data[$i-1][$noaa_hdd_ix] = '';
                   } else {
               $fixed_data[$i-1][$noaa_hdd_ix] = $override_data[$override_ix][$hdd_ix]; 
                   }              
           }
           if ($override_data[$override_ix][$cdd_ix] != ''){
               if (strtolower($override_data[$override_ix][$cdd_ix]) == 'd'){
                   $fixed_data[$i-1][$noaa_cdd_ix] = '';
                   } else {
               $fixed_data[$i-1][$noaa_cdd_ix] = $override_data[$override_ix][$cdd_ix]; 
                   }              
           }
           
           
       } 
   }        
    
    
    return($fixed_data);
}

####################################################################
  function getclimatefile ($filename,$target,$year,$m) {
    global $SITE;
    if ($target == 'Avg Sea Level'){
    $target1 = '<!-- '.$target.'-->';     
    } else {
    $target1 = '<!-- '.$target.' -->';              
    }
    $rawdata = array();
    if (file_exists($filename)) {    
    $fd = @fopen($filename,'r');
    
    $startdt = 0;
    if ( $fd ) {
    
        while ( !feof($fd) ) { 
        
            // Get one line of data
            $gotdat = trim ( fgets($fd,8192) );
            
            if ($startdt == 1 ) {
                if ( strpos ($gotdat, "</td>" ) !== FALSE ){  // End of Snow data
                    $gotdatx = str_replace("<br />","",$gotdatx);
                    $foundline = preg_split("/[\n\r\t ]+/", $gotdatx );                                   
                    fclose($fd);
                    $foundline = check_for_overrides($year,$m,$foundline,'climate',$target);
                    return($foundline);
                } else {
                    $gotdatx = $gotdatx . $gotdat;
                    $foundline = preg_split("/[\n\r\t ]+/", $gotdatx );                    

                }
            }
        
            if ($startdt == 0 ) {
                if ( strpos ($gotdat, $target1) !== FALSE ){  // Found data line
                    $startdt = 1;
                } 
            }
        }
        // Close the file we are done getting data
        fclose($fd);
    }
    }
    $rawdata = check_for_overrides($year,$m,$rawdata,'climate',$target);    
    return($rawdata);
}

function fixclimatereport ($rawdata,$override_data,$target) {
    $header = $override_data[0];
    switch ($target) {
    case 'Snow Fall':
        $target = 'Snow';
        break;
    case 'Avg Sea Level':
        $target = 'Avg Pressure';
        break;
    }
  
   
    $override_column = array_search($target,$header);
   
   $rows = count($override_data,0);
      
   for ($i=1 ; $i < $rows ; $i++){
       $days_over[$i-1]= ($override_data[$i][0]);
   }   
   
   for ($i=1 ; $i < 32; $i++){
       if (in_array($i,$days_over)){
           $override_ix = 1 + array_search($i,$days_over);
           if ($override_data[$override_ix][$override_column] != ''){
               if (strtolower($override_data[$override_ix][$override_column]) == 'd'){
                   $rawdata[$i-1] = '';
                   } else {
                   $rawdata[$i-1] = $override_data[$override_ix][$override_column];               
           }
           }
           
           
       } 
   }           
}

####################################################################
function getbarometerdata ($filename) {
    global $SITE;               
    
    $rawdata = array();
    
    $fd = @fopen($filename,'r');
    
    $startdt = 0;
    if ( $fd ) {
    
        while ( !feof($fd) ) { 
        
            // Get one line of data
            $gotdat = trim ( fgets($fd,8192) );
            
            if ($startdt == 1 ) {
                if ( strpos ($gotdat, "</td>" ) !== FALSE ){  // End of Barometer data
                    $gotdatx = str_replace("<br />","",$gotdatx);
                    $foundline = preg_split("/[\n\r\t ]+/", $gotdatx );                                   
                    fclose($fd);
                    return($foundline);
                } else {
                    $gotdatx = $gotdatx . $gotdat;
                    $foundline = preg_split("/[\n\r\t ]+/", $gotdatx );                    

                }
            }
        
            if ($startdt == 0 ) {
                if ( strpos ($gotdat, "<!-- Avg Sea Level-->" ) !== FALSE ){  // Found Barometer line
                    $startdt = 1;
                } 
            }
        }
        // Close the file we are done getting data
        fclose($fd);
    }   
    return($rawdata);
}
###########################################

function get_days_in_month($month, $year)
{
   return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year %400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}

###########################################

function baro_mb_to_inches ($data){
    global $uomBaro;
    if ('INHG' == strtoupper(trim($uomBaro))){
        if ($data > 200){
            $data = $data * 0.02953;
            $data = round($data,2);
            $data = sprintf("%01.2f",$data);
        }
    }
    return $data;
}
#########################################

function get_noaa_filename ($year, $m, $wxsoftware, $current_month){
             if($wxsoftware == 'CU') {
                $filename = "NOAAMO" . str_pad(($m + 1), 2, "0", STR_PAD_LEFT) . substr($year,2,2) . ".txt";
              } 
              if($wxsoftware == 'WL') {
                  $now = getdate();
$now_month = sprintf("%02d",$now['mon']);
$now_year = $now['year'];
$prior_month = $now['mon'] - 1;
$prior_year = $now['year'];
$last_year = $prior_year -1;
if ($prior_month < 1) {$prior_month = 12; $prior_year--;}
$prior_month = sprintf("%02d",$prior_month);
global $path_dailynoaa;
$NOAAdir = $path_dailynoaa;
$LastMonthFile = $path_dailynoaa.'NOAAPRMO.TXT';
$now_hour = $now['hours'];

  if(! file_exists("$path_dailynoaa/NOAA$prior_year-$prior_month.TXT") and
       file_exists($LastMonthFile) and 
       $now_hour >= 6) {
       print "<!-- copying $LastMonthFile to $NOAAdir/NOAA$prior_year-$prior_month.TXT -->\n";
       if (copy($LastMonthFile,"$NOAAdir/NOAA$prior_year-$prior_month.TXT")) {
         print "<!-- copy successful -->\n";
       } else {
         print "<!-- unable to copy -->\n";
       }
       }
                 if ($current_month){ 
                     $filename = "NOAAMO.TXT";
                 } else {                  
                     $filename = "NOAA" . $year . "-" . str_pad(($m + 1), 2, "0", STR_PAD_LEFT) . ".TXT";
                 }
              }
              if($wxsoftware == 'VWS') {
                $filename = $year . "_" . str_pad(($m + 1), 2, "0", STR_PAD_LEFT) . ".txt";
              } 
              if ($wxsoftware == "WD"){           
                if ($current_month){             
                    $filename = "dailynoaareport.htm";                                            
                } else { 
                    $filename = "dailynoaareport" . ( $m + 1 ) . $year . ".htm";
                }                                                       
              }
              return ($filename);
}
############################################################################
# End of Page
############################################################################

?> 
