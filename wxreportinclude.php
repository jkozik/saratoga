<?php
ini_set('display_errors', 0); 
error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(E_ALL );
$self = $SITE['self'];
$self = '/'.$self;
$lastslash = 1+ strripos($self,"/");
$self = substr($self,$lastslash);
$hemi = ($SITE['latitude'] >= 0) ; // 0 = Southern Hemisphere, 1 = Northern Hemisphere

$summary_button = langtransstr("Monthly Summary");         # Text to show on Summary radio button
$detail_button = langtransstr("Daily Detail");         # Text to show on Detail radio button
$season_button = langtransstr("Seasonal Summary");          # Text to show on Season radio button  

$temp_button = langtransstr("Temperature");         # Text to show on temperature buttons
$rain_button = langtransstr("Rain");         # Text to show on rain buttons
$wind_button = langtransstr("Wind");         # Text to show on wind buttons
$windrun_button = langtransstr("Wind Run");         # Text to show on windrun buttons
$degree_button = langtransstr("Degree Days");         # Text to show on degree day buttons
$snow_button = langtransstr("Snow");         # Text to show on snow buttons
$snowdepth_button = langtransstr("Snow Depth");         # Text to show on snow depth buttons
$baro_button = langtransstr("Barometric Pressure");         # Text to show on barometric pressure buttons
$sunhours_button = langtransstr("Sunshine Hours");         # Text to show on solar buttons
$solar_button = langtransstr("Max Solar");        # Text to show on max solar buttons
$solarkwh_button = langtransstr("Solar kWh");   # Text to show on total solar buttons
$uv_button = langtransstr("UV");         # Text to show on uv buttons
$dewpoint_button = langtransstr("Dew Point");         # Text to show on dewpoint buttons
$wetbulb_button = langtransstr("Wet Bulb");         # Text to show on wetbulb buttons
$soiltemp_button = langtransstr("Soil Temp");         # Text to show on soil temp buttons

$mnthname = array((langtransstr('January')),(langtransstr('February')),(langtransstr('March')),(langtransstr('April')),(langtransstr('May')),(langtransstr('June')),(langtransstr('July')),(langtransstr('August')),(langtransstr('September')),(langtransstr('October')),(langtransstr('November')),(langtransstr('December')),(langtransstr('yearly')));
//$seasonnames = array('Winter','Spring','Summer','Fall');
$seasonnames = array((langtransstr('Winter')),(langtransstr('Spring')),(langtransstr('Summer')),(langtransstr('Fall')));
$SITE['copy'] = '<p style="font-size: 9px;" align="right">'.langtransstr("Script Developed by")." Murry Conarroe of <a href='http://weather.wildwoodnaturist.com/'>Wildwood Weather</a>.</p>";
$incomplete = '<span style="font-size: 12px;">'. langtransstr('* denotes incomplete data for the month/year.').'</span>';
$wxsoftware = strtoupper($SITE['WXsoftware']) ; // Set in settings-weather.php in V3
if ($wxsoftware == ''){
    $wxsoftware = 'WD';    
}
if ($wxsoftware != 'WD'){
    $show_snow_links = $show_snowdepth_links = $show_sunhours_links = $show_solar_links = $show_baro_links = false;
    $show_solarkwh_links = $show_uv_links = $show_dewpoint_links = $show_wetbulb_links = $show_soiltemp_links = false;
}
if ($show_detail_links) {
if ($show_temp_links) {$show_temp_detail_link = true;}  
if ($show_rain_links) {$show_rain_detail_link = true;}
if ($show_wind_links) {$show_wind_detail_link = true;} 
if ($show_windrun_links) {$show_windrun_detail_link = true;} 
if ($show_snow_links) {$show_snow_detail_link = true;} 
if ($show_snowdepth_links) {$show_snowdepth_detail_link = true;}
if ($show_baro_links) {$show_baro_detail_link = true;} 
if ($show_degree_links) {$show_degree_detail_link = true;} 
if ($show_sunhours_links) {$show_sunhours_detail_link = true;} 
if ($show_solar_links) {$show_solar_detail_link = true;} 
if ($show_solarkwh_links) {$show_solarkwh_detail_link = true;} 
if ($show_uv_links) {$show_uv_detail_link = true;} 
if ($show_dewpoint_links) {$show_dewpoint_detail_link = true;} 
if ($show_wetbulb_links) {$show_wetbulb_detail_link = true;} 
if ($show_soiltemp_links) {$show_soiltemp_detail_link = true;} 
}   

if ($show_summary_links) {
if ($show_temp_links) {$show_temp_summary_link = true;}  
if ($show_rain_links) {$show_rain_summary_link = true;}
if ($show_wind_links) {$show_wind_summary_link = true;} 
if ($show_windrun_links) {$show_windrun_summary_link = true;} 
if ($show_snow_links) {$show_snow_summary_link = true;} 
if ($show_snowdepth_links) {$show_snowdepth_summary_link = true;} 
if ($show_baro_links) {$show_baro_summary_link = true;}
if ($show_degree_links) {$show_degree_summary_link = true;} 
if ($show_sunhours_links) {$show_sunhours_summary_link = true;} 
if ($show_solar_links) {$show_solar_summary_link = true;} 
if ($show_solarkwh_links) {$show_solarkwh_summary_link = true;} 
if ($show_uv_links) {$show_uv_summary_link = true;} 
if ($show_dewpoint_links) {$show_dewpoint_summary_link = true;} 
if ($show_wetbulb_links) {$show_wetbulb_summary_link = true;} 
if ($show_soiltemp_links) {$show_soiltemp_summary_link = true;} 
}  

if ($show_season_links) {
if ($show_temp_links) {$show_temp_season_link = true;}  
if ($show_rain_links) {$show_rain_season_link = true;}
if ($show_wind_links) {$show_wind_season_link = true;} 
if ($show_windrun_links) {$show_windrun_season_link = true;} 
if ($show_snow_links) {$show_snow_season_link = true;} 
if ($show_snowdepth_links) {$show_snowdepth_season_link = true;} 
if ($show_baro_links) {$show_baro_season_link = true;}
if ($show_degree_links) {$show_degree_season_link = true;} 
if ($show_sunhours_links) {$show_sunhours_season_link = true;} 
if ($show_solar_links) {$show_solar_season_link = true;} 
if ($show_solarkwh_links) {$show_solarkwh_season_link = true;} 
if ($show_uv_links) {$show_uv_season_link = true;} 
if ($show_dewpoint_links) {$show_dewpoint_season_link = true;} 
if ($show_wetbulb_links) {$show_wetbulb_season_link = true;} 
if ($show_soiltemp_links) {$show_soiltemp_season_link = true;} 
}     
if ($show_no_links != true){
$detail_links = array($show_temp_detail_link,$show_rain_detail_link,$show_wind_detail_link,$show_windrun_detail_link,$show_snow_detail_link,$show_snowdepth_detail_link,$show_baro_detail_link,$show_degree_detail_link,$show_sunhours_detail_link,$show_solar_detail_link,$show_solarkwh_detail_link,$show_uv_detail_link,$show_dewpoint_detail_link,$show_wetbulb_detail_link,$show_soiltemp_detail_link);

$summary_links = array($show_temp_summary_link,$show_rain_summary_link,$show_wind_summary_link,$show_windrun_summary_link,$show_snow_summary_link,$show_snowdepth_summary_link,$show_baro_summary_link,$show_degree_summary_link,$show_sunhours_summary_link,$show_solar_summary_link,$show_solarkwh_summary_link,$show_uv_summary_link,$show_dewpoint_summary_link,$show_wetbulb_summary_link,$show_soiltemp_summary_link);

$season_links = array($show_temp_season_link,$show_rain_season_link,$show_wind_season_link,$show_windrun_season_link,$show_snow_season_link,$show_snowdepth_season_link,$show_baro_season_link,$show_degree_season_link,$show_sunhours_season_link,$show_solar_season_link,$show_solarkwh_season_link,$show_uv_season_link,$show_dewpoint_season_link,$show_wetbulb_season_link,$show_soiltemp_season_link);

$detail_files = array($tempdetailfile_name,$raindetailfile_name,$winddetailfile_name,$windrundetailfile_name,$snowdetailfile_name,$snowdepthdetailfile_name,$barodetailfile_name,$degreedetailfile_name,$sunhoursdetailfile_name,$solardetailfile_name,$solarkwhdetailfile_name,$uvdetailfile_name,$dewpointdetailfile_name,$wetbulbdetailfile_name,$soiltempdetailfile_name);

$summary_files = array($tempsummaryfile_name,$rainsummaryfile_name,$windsummaryfile_name,$windrunsummaryfile_name,$snowsummaryfile_name,$snowdepthsummaryfile_name,$barosummaryfile_name,$degreesummaryfile_name,$sunhourssummaryfile_name,$solarsummaryfile_name,$solarkwhsummaryfile_name,$uvsummaryfile_name,$dewpointsummaryfile_name,$wetbulbsummaryfile_name,$soiltempsummaryfile_name);

$season_files = array($tempseasonfile_name,$rainseasonfile_name,$windseasonfile_name,$windrunseasonfile_name,$snowseasonfile_name,$snowdepthseasonfile_name,$baroseasonfile_name,$degreeseasonfile_name,$sunhoursseasonfile_name,$solarseasonfile_name,$solarkwhseasonfile_name,$uvseasonfile_name,$dewpointseasonfile_name,$wetbulbseasonfile_name,$soiltempseasonfile_name);

$button_names = array($temp_button,$rain_button,$wind_button,$windrun_button,$snow_button,$snowdepth_button,$baro_button,$degree_button,$sunhours_button,$solar_button,$solarkwh_button,$uv_button,$dewpoint_button,$wetbulb_button,$soiltemp_button);

$detail = preg_match("#".$self."#i",implode(",", $detail_files));
$summary = preg_match("#".$self."#i",implode(",", $summary_files));
$four_seasons = preg_match("#".$self."#i",implode(",", $season_files));
$limit = count($detail_links);

$show_detail_links = (0 != array_sum($detail_links));
$show_summary_links = (0 != array_sum($summary_links));
$show_season_links = (0 != array_sum($season_links));

$disabled = ' disabled="disabled" ';
$detail_disabled = $summary_disabled = $season_disabled = '';
$checked = 'checked="checked"';
if ($detail){
    $detail_checked = $checked;
    $summary_checked = $season_checked = '';
} elseif ($summary) {
    $detail_checked = $season_checked = '';
    $summary_checked = $checked;
} else {
    $detail_checked = $summary_checked = '';
    $season_checked = $checked;    
}

echo '<br />';

if ($summary){
    echo '<div style="font-weight:bold">';
    echo '<br /><br /><br />';
   if (($self == $snowsummaryfile_name) OR ($self == $snowdepthsummaryfile_name) OR ($self == $rainsummaryfile_name)) {
       if ($season_start == 1){
    echo '<br /><br />';
    }
       } else {
           echo '<br /><br />';
       }
    echo '</div>';
    echo '<div class="getreportdtbx doNotPrint">';
}

if ($four_seasons){
        $hemi_text = ($hemi==1) ? langtransstr('Northern Hemisphere Meterological Seasons') : langtransstr('Southern Hemisphere Meterological Seasons'); 
        echo '<div style="font-weight:bold">',$hemi_text.'<br />';
        echo $seasonnames[2 - ($hemi*2)],': '.$mnthname[11].', '.$mnthname[0].', '.$mnthname[1];
        echo '<br />';
        echo $seasonnames[3 - ($hemi*2)],': '.$mnthname[2].', '.$mnthname[3].', '.$mnthname[4];
        echo '<br />';
        echo $seasonnames[0 + ($hemi*2)],': '.$mnthname[5].', '.$mnthname[6].', '.$mnthname[7];
        echo '<br />';
        echo $seasonnames[1 + ($hemi*2)],': '.$mnthname[8].', '.$mnthname[9].', '.$mnthname[10];            
        echo '<br /></div>';
        echo '<div class="getreportdtbx doNotPrint">';  
      
}
       
$current = get_current_file($limit,$self);
if ($detail){
    if ($summary_links[$current]==0){
        $summary_disabled = $disabled;
    }
    if ($season_links[$current]==0){
        $season_disabled = $disabled;
    }    
} elseif ($summary) {
    if ($detail_links[$current]==0){
        $detail_disabled = $disabled;
    }
    if ($season_links[$current]==0){
        $season_disabled = $disabled;
    }
} else {
    if ($detail_links[$current]==0){
        $detail_disabled = $disabled;
    } 
    if ($summary_links[$current]==0){
        $summary_disabled = $disabled;
    }
        
}
echo '<br />';
echo '<form name="myForm" action="'.$self.'" method="get">';
echo '<table><tr><td style="width:15%;">&nbsp;</td>';
if ($show_detail_links == true){
    if ($show_summary_links OR $show_season_links){ // Don't show radio button if only one choice
echo '<td align="right" style="width:20%; padding-right:20px;"><input type="radio" name="r" value="'.$detail_files[$current].'" '.$detail_checked.' '.$detail_disabled.' onclick="this.form.action=this.value;this.form.submit()" />'.$detail_button.'</td>';
    }
}
if ($show_summary_links == true) {
    if ($show_detail_links OR $show_season_links){ // Don't show radio button if only one choice       
    echo '<td align="center" style="width:20%; padding-left:10px;"><input type="radio" name="r" value="'.$summary_files[$current].'" '.$summary_checked.' '.$summary_disabled.' onclick="this.form.action=this.value;this.form.submit()" />'.$summary_button.'</td>';
    }
}
if ($show_season_links == true) {
    if ($show_detail_links OR $show_summary_links){ // Don't show radio button if only one choice  
    echo '<td align="left" style="width:25%; padding-left:10px;"><input type="radio" name="r" value="'.$season_files[$current].'" '.$season_checked.' '.$season_disabled.' onclick="this.form.action=this.value;this.form.submit()" />'.$season_button.'</td>';
    }
}
echo '<td style="width:15%;">&nbsp;</td></tr></table>';
echo '</form>';


    if (0 != $max_links = max(array_sum($detail_links),array_sum($summary_links),array_sum($season_links))){
         $buttons = 0; 
         $ds_files = $summary_files;
//         $max_links = max(array_sum($detail_links),array_sum($summary_links));
    for ($i = 0; $i < $limit; $i ++){
        if (preg_match("#".$detail_files[$i]."#i", $self)){
            $ds_files = $detail_files;
        }    
    }
    for ($i = 0; $i < $limit; $i ++){
        if (preg_match("#".$season_files[$i]."#i", $self)){
            $ds_files = $season_files;
        }    
    }    
            for ($i = 0; $i < $limit; $i ++){
            if (0 != $detail_links[$i] + $summary_links[$i] + $season_links[$i]){    
            if (!preg_match("#".$ds_files[$i]."#i", $self)) {               
            echo '<form method="get" action="'.$ds_files[$i].'">
             <input type="submit" value="'.$button_names[$i].'"';
             if ($detail){
                 if ($detail_links[$i]==0){
                     echo $disabled;
                 }
             } elseif ($summary) {
                 if ($summary_links[$i]==0){
                     echo $disabled;
                 }
             } else {
                 if ($season_links[$i]==0){
                     echo $disabled;
                 }
             } 
                echo ' /></form>';
            } else {
              echo '<form method="get" action="'.$ds_files[$i].'">
              <input disabled="disabled" type="submit" value="'.$button_names[$i].'" />
                </form>';
            }
                $buttons = $buttons +1; 
                    if ($max_links > 14) {
                        if ($buttons % round($max_links/3) == 0) {
                        echo '<br />';
                        }
                    } 
                    elseif ($max_links >7) {                            
                        if ($buttons % round($max_links/2)== 0){
                        echo '<br />';
                        } 
                    }
                    
            }
            
        }
    }
  
    
if (!$detail){    
   echo '</div>';
}     

 
}
 
###################### Functions #######################################   
 
 function get_current_file($limit,$self){
    global $detail_links, $detail_files, $summary_files, $season_files;
for ($i = 0; $i < $limit ; $i ++){
        if (preg_match("#".$detail_files[$i]."#i", $self)){
            return $i;
        } 
        if (preg_match("#".$summary_files[$i]."#i", $self)){
            return $i;
        } 
        if (preg_match("#".$season_files[$i]."#i", $self)){
            return $i;
        }            
}
}

function get_noaa_filename ($year, $m, $wxsoftware, $current_month){
             if($wxsoftware == 'CU') {
                $filename = "NOAAMO" . str_pad(($m + 1), 2, "0", STR_PAD_LEFT) . substr($year,2,2) . ".txt";
              } 
              if($wxsoftware == 'WL') {
                  $now = getdate();
// print "<!-- now \n" . print_r($now,true) . " -->\n";
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
$zzz = "$path_dailynoaa/NOAA$prior_year-$prior_month.TXT" ;
$xxx = file_exists("$path_dailynoaa/NOAA$prior_year-$prior_month.TXT");
$yyy = file_exists($LastMonthFile);

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
                     $filename = "NOAAMO.txt";
                 } else {                  
                     $filename = "NOAA" . $year . "-" . str_pad(($m + 1), 2, "0", STR_PAD_LEFT) . ".txt";
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
if(!function_exists('getnoaafile')) {
# GETNOAAFILE function
# Developed by TNETWeather.com
#
# Returns an array of the contents of the specified filename
# Array contains days from 1 - 31 (or less if the month has less) and
# the values:
# Day
# Mean Temp
# High Temp
# Time of High Temp
# Low Temp
# Time of Low Temp
# Hot Degree Day
# Cold Degree Day
# Rain
# Avg Wind Speed
# High Wind
# Time High Wind
# Dom Wind Direction
############################################################################

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
                    $gotdat = str_replace(",",".",$gotdat); 
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
}

?>
