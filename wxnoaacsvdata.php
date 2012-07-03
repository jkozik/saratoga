<?php
############################################################################
#
#   Module:     wxnoaacsvdata.php
#   Purpose:    Convert data in dailynoaareport files to csv format.
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
#   History
# 2010-08-21 1.0 Inital Release
# 2011-01-19 1.3 Fix for uses that have less than one year of data
# 2011-12-27 1.4 Added support for Cumulus, Weatherlink, VWS generated NOAA reports
############################################################################ 

error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors', 1); 

$noaa_file_name = "noaadata.csv";
// You can force the cache to update by adding ?force=1 to the end of the URL

if ( empty($_REQUEST['force']) )
        $_REQUEST['force']="0";

$Force = $_REQUEST['force'];

if (!file_exists($cache_location.$noaa_file_name)) {
    $file_time = 0;
    $do_cache = false;
}  else {
$file_time = filemtime($cache_location.$noaa_file_name);
}
$file_time_text = date('c',$file_time);
$now = time();
if (($Force == 1) OR($do_cache == false) OR ( $now > ($file_time +(3600 * 24)))){    // if more than 24 hours old or Force = 1, recalculate
// Calculate the highs & lows for each day of the year

// Get data for month m of each year

$year = date("Y");
    if ((( date("n") == 1) && date("j") == 1)){
    $year = $year -1;
    }    
 
$years = 1 + ($year - $first_year_of_data);
$days_in_month = array(31,29,31,30,31,30,31,31,30,31,30,31) ;
$yeardata = $yearlydata = array(); 
$yd = 0;
$files_found = 0;
 
    // Collect the data 
        for ( $m = 0; $m < 12 ; $m ++ ) {  
             $mtddata = array();
             $csvdata = array();
             $data = array();
             $monthdata = array();

             
        for ( $y = 0; $y < $years ; $y ++ ) {
             $yx = $first_year_of_data + $y;        
           
            // Check for current year and current month         
            
           if ($yx == date("Y") && $m == ( date("n") - 1) && date("j") != 1 )  {
                $filename = "dailynoaareport.htm";
                $current_month = 1;                               

            } else { 
                $filename = "dailynoaareport" . ( $m + 1 ) . $yx . ".htm";
                $current_month = 0;                             
            }
            $filename = get_noaa_filename ($yx, $m, $current_month);            
            
            if (file_exists($loc . $filename) ) { 
                $data[$y] = getnoaafile($loc . $filename);
                $files_found ++;
                                                    
            }             
        }
        // All data for this month for all years has been collected. Now find the records
          $recordhighs = $recordlows = $recordrain = $recordwind = array();
          $highs_count = $lows_count = $means_count = $rain_count = $avgwind_count = $maxwind_count = array();
          $avg_high = $avg_low = $avg_mean = $avg_rain = $avg_avgwind = $avg_maxwind = array();
       
        for ($i = 0; $i <$days_in_month[$m]; $i ++){
    
 
        for ( $y = 0; $y < $years ; $y ++ ) {

           $day = $data[$y][$i][0];
           $high = $data[$y][$i][2];
           $low = $data[$y][$i][4];
           $mean = $data[$y][$i][1];
           $rain = $data[$y][$i][8];
           $avgwind = $data[$y][$i][9];
           $maxwind = $data[$y][$i][10];                           
                       
           if ($high != '' AND $high != 'X' AND $high != '-----'){
               $avg_high[$day-1] = $avg_high[$day-1] + $high;
               $highs_count[$day-1] =  $highs_count[$day-1] +1 ;            
           } 
           
           if ($low != '' AND $low != 'X' AND $low != '-----'){
               $avg_low[$day-1] = $avg_low[$day-1] + $low;
               $lows_count[$day-1] = $lows_count[$day-1] +1;           
           }
           
           if ($mean != '' AND $mean != 'X' AND $mean != '-----'){
               $avg_mean[$day-1] = $avg_mean[$day-1] + $mean;
               $means_count[$day-1] = $means_count[$day-1] + 1;           
           }  
           
            if ($rain != '' AND $rain !== 'X' AND $rain!= '-----'){
               $avg_rain[$day-1] = $avg_rain[$day-1] + $rain;
               $rain_count[$day-1] =  $rain_count[$day-1] +1 ;                          
           } 
           
           if ($avgwind != '' AND $avgwind != 'X' AND $avgwind != '-----'){
               $avg_avgwind[$day-1] = $avg_avgwind[$day-1] + $avgwind;
               $avgwind_count[$day-1] = $avgwind_count[$day-1] +1;           
           }
           
           if ($maxwind != '' AND $maxwind != 'X' AND $maxwind != '-----'){
               $avg_maxwind[$day-1] = $avg_maxwind[$day-1] + $maxwind;
               $maxwind_count[$day-1] = $maxwind_count[$day-1] + 1;           
           }                                                  
           
           if (($high!='') AND(($high > $recordhighs[$day-1]) OR ($recordhighs[$day-1] == ""))){
            $recordhighs[$day-1] = $high;
            $recordhighs_years[$day-1] = $first_year_of_data + $y;
           } 
           
           if ((($low < $recordlows[$day-1]) OR ($recordlows[$day-1] == "")) AND ($low != "")){
            $recordlows[$day-1] = $low;
            $recordlows_years[$day-1] = $first_year_of_data + $y;
           }  
           
           if (($rain!='') AND (($rain > $recordrain[$day-1]) OR ($recordrain[$day-1] == ""))){
            $recordrain[$day-1] = $rain;
            $recordrain_years[$day-1] = $first_year_of_data + $y;
           } 
           
           if (($maxwind!='')AND(($maxwind > $recordwind[$day-1]) OR ($recordwind[$day-1] == ""))) {
            $recordwind[$day-1] = $maxwind;
            $recordwind_years[$day-1] = $first_year_of_data + $y;
           }                     
                              
        } 
        
        }
                      
                for ($i = 0; $i <$days_in_month[$m]; $i ++){     
                $yeardata[0][$yd]=$recordhighs[$i];
                $yeardata[1][$yd]=$recordlows[$i];
                $yeardata[5][$yd]=$recordrain[$i];
                $yeardata[7][$yd]=$recordwind[$i];                                

                if ($highs_count[$i] == '') {
                    $yeardata[2][$yd]='';
                } else {      
                    $yeardata[2][$yd]=round(($avg_high[$i]/$highs_count[$i]),1);
                    $monthdata[2][$m]=$monthdata[2][$m]+$avg_high[$i]; 
                    $monthcount[2][$m]=$monthcount[2][$m]+$highs_count[$i];
                }                   
                if ($lows_count[$i] == '') {
                    $yeardata[3][$yd]='';
                } else {                    
                    $yeardata[3][$yd]=round(($avg_low[$i]/$lows_count[$i]),1);
                    $monthdata[3][$m]=$monthdata[3][$m]+$avg_low[$i]; 
                    $monthcount[3][$m]=$monthcount[3][$m]+$lows_count[$i];
                }
                if ($means_count[$i] == '') {
                    $yeardata[4][$yd]='';
                } else {                                   
                    $yeardata[4][$yd]=round(($avg_mean[$i]/$means_count[$i]),1);
                    $monthdata[4][$m]=$monthdata[4][$m]+$avg_mean[$i]; 
                    $monthcount[4][$m]=$monthcount[4][$m]+$means_count[$i];
                }                    
                if ($rain_count[$i] == '') { 
                    $yeardata[6][$yd]='';
                } else {                     
                    $yeardata[6][$yd]=round(($avg_rain[$i]/$rain_count[$i]),2);
                    $monthdata[6][$m]=$monthdata[6][$m]+$avg_rain[$i]; 
                    $monthcount[6][$m]=$monthcount[6][$m]+$rain_count[$i];
                }                                        
                if ($avgwind_count[$i] == '') {
                    $yeardata[8][$yd]='';
                } else {                                   
                    $yeardata[8][$yd]=round(($avg_avgwind[$i]/$avgwind_count[$i]),1);
                    $monthdata[8][$m]=$monthdata[8][$m]+$avg_avgwind[$i]; 
                    $monthcount[8][$m]=$monthcount[8][$m]+$avgwind_count[$i];
                }                    
                if ($maxwind_count[$i] == '') { 
                    $yeardata[9][$yd]='';
                } else {                                  
                    $yeardata[9][$yd]=round(($avg_maxwind[$i]/$maxwind_count[$i]),1);
                    $monthdata[9][$m]=$monthdata[9][$m]+$avg_maxwind[$i]; 
                    $monthcount[9][$m]=$monthcount[9][$m]+$maxwind_count[$i];
                }                                        
                 
                                              
                $yd ++;  
                         
                }
 #########################
if (empty($recordhighs)){
    $recordhigh = '';
} else {
    $recordhigh = max($recordhighs);    
}   

if (empty($recordlows)){
    $recordlow = '';
} else {
    $recordlow = min($recordlows);    
} 

if (empty($recordwind)){
    $recordhighwind = '';
} else {
    $recordhighwind = max($recordwind);    
}              

if ($recordhigh != ''){
  $yeardata[10][$m] = round($recordhigh,1);
} else {
  $yeardata[10][$m] = '';       
}
  
if ($recordlow != '') { 
  $yeardata[11][$m] = round($recordlow,1);
} else {
  $yeardata[11][$m] = '';       
}   
 
if ($monthcount[2][$m] != ''){   
  $yeardata[12][$m] = round(($monthdata[2][$m]/$monthcount[2][$m]),1);  // avg high
} else {
  $yeardata[12][$m] = '';       
}  
  
if ($monthcount[3][$m] != ''){   
  $yeardata[13][$m] = round(($monthdata[3][$m]/$monthcount[3][$m]),1);  // avg low
} else {
  $yeardata[13][$m] = '';       
}  
  
if ($monthcount[4][$m] != ''){   
  $yeardata[14][$m] = round(($monthdata[4][$m]/$monthcount[4][$m]),1); // mean temp
} else {
  $yeardata[14][$m] = '';       
}  
  
if ($monthcount[6][$m] != '') { 
  $yeardata[15][$m] = round(($monthdata[6][$m]/ceil(($monthcount[6][$m]/$days_in_month[$m]))),2);//$monthcount[5][$m]),1);  // rain
} else {
  $yeardata[15][$m] = '';       
}  
  
if ($recordhigh != ''){
  $yeardata[16][$m] = round($recordhighwind,1);
} else {
  $yeardata[16][$m] = '';       
}  
  
if ($monthcount[8][$m] != ''){   
  $yeardata[17][$m] = round(($monthdata[8][$m]/$monthcount[8][$m]),1); // avg wind
} else {
  $yeardata[17][$m] = '';       
}  
  
if ($monthcount[9][$m] != ''){   
  $yeardata[18][$m] = round(($monthdata[9][$m]/$monthcount[9][$m]),1); // maxwind 
} else {
  $yeardata[18][$m] = '';       
}    
    
##########################################
               
        }   // end of daily data for month m for all years
if ($files_found == 0) {
  $string = "NOAA Report files not found";
  create_image1($string,' ');
  exit;
}                
// Save data as a csv file for cache
if ($do_cache == true) {

$fp = fopen($cache_location.$noaa_file_name, 'w');

for ($i = 0; $i <19; $i++){
    $line=implode(',',$yeardata[$i]);  
    fputcsv($fp, explode(',', $line));
}

fclose($fp);
}

} else {
     # Open the cache file.
    if (($fp = fopen($cache_location.$noaa_file_name, "r")) !== FALSE) {
        # Set the parent multidimensional array key to 0.
        $nn = 0;
        while (($data = fgetcsv($fp, 2500, ",")) !== FALSE) {
            # Count the total keys in the row.
            $c = count($data);
            # Populate the multidimensional array.
            for ($x=0;$x<$c;$x++)
            {
                $yeardata[$nn][$x] = $data[$x];
            }
            $nn++;
        }

        fclose($fp);
    }   
    
}

#################################################################
function get_noaa_filename ($year, $m, $current_month){
    global $SITE;
    //$wxsoftware = $SITE['WXsoftware'];
    $wxsoftware = 'VWS';
    if ($wxsoftware == ''){     // if using V2 Weather Display template
        $wxsoftware = 'WD';
    }
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
#################################################################
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


############################################################################
# End of Page
############################################################################
?>
