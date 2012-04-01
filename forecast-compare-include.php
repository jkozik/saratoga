<?php
// By Jim McMurry - jmcmurry@mwt.net - jcweather.us
$version = 3.4;  // 12-21-11
// See fc-readme.txt for implementation and change details
//
if ( isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
//--self downloader --
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

if (isset($_REQUEST['log'])) {
	$fcDir = "";                          // Config file is always seen as in our folder in this case                
} else {                                  // Might have been called from elsewhere
	$fcDir = "";                        
//	$fcDir = "fcreal/";                // Something like this if you've installed your forecast-compare files to a special folder
}

$configfile = $fcDir . "fc-config.php";

if (file_exists($configfile)) {   
	include($configfile);
} else {
	echo "Configuration File Not Found";
	return;
}

// Day/Night
$nightstart = "16:50";                   // The times that WxSim switches day/night for first temperature
$nightend   = "04:50";                   // These moved from the config because they were confusing things for some folks

// Obtain Basic Environment - Thanks to Stuart (Broadstairs) for providing this code
// in APACHE servers Document_root is always defined but in IIS it is not
if ( ! isset($_SERVER['DOCUMENT_ROOT'] ) ) {
   $path_trans = str_replace( '\\\\', '/', $_SERVER['PATH_TRANSLATED']);
   $WEBROOT = substr($path_trans, 0, 0-strlen($_SERVER['PHP_SELF']) );
}
else {
   $WEBROOT = $_SERVER['DOCUMENT_ROOT'];
}

if ($MSserver) {
   $webRoot     = $WEBROOT . "\\";
} else {
	$webRoot     = $WEBROOT . "/";
}

$fcstLog     = $webRoot . $fileLoc . $logfile[$_REQUEST['config']];
$NWSscript   = $webRoot . $NWSscript;
$WXSIMscript = $webRoot . $fileLoc . $WXSIMscript;
include_once($webRoot . $fileLoc . 'fc-language.php');
include_once($webRoot . $fileLoc . 'fc-functions.php');
include_once($webRoot . $tagsfile);  

if (isset($_REQUEST['log'])) {  // In the logging mode
	$TheMax = $maxtempyest;                               
	$TheMin = $mintempovernight;             // Backups in case of fc-LogTemps.php problems or if it hasn't been implemented.

	if (file_exists("fc-temps.txt")) {   
		$lines = file("fc-temps.txt");       // Reads into an array
		$entry = explode(",",$lines[0]);     // to get date/time from 1st (and only ) record
		if ($entry[0] != "") {
			$TheMax = $entry[0];		
		}
		if ($entry[1] != "") {
			$TheMin = $entry[1];		
		}
	}
	
	if (!isset($TheMax) || !isset($TheMin)) {
		echo "The temperature variables must be initialized by running testtags.php or some other mechanism";	
		return;
	}
	
	$TheMax = preg_replace('/°F/',"",$TheMax);   // Could use a regex expert here
	$TheMin = preg_replace('/°F/',"",$TheMin);   // but this works
	$TheMax = preg_replace('/°C/',"",$TheMax);
	$TheMin = preg_replace('/°C/',"",$TheMin);

	
	$doPrintNWS = false;           // For advforecast2.php
	$doPrintWU = false;            // For WU-forecast.php
	$doInclude = true;             // For ec-forecast.php		
	$_REQUEST['force']='1';
	require($NWSscript);           // Initialize variables
	
	if ($TypeScript == "NWS") {
		$TempVar = $forecasttemp;               // advforecast2
		$lastUpdate = time($forecastupdated);
	} else if ($TypeScript == "EC") {	
		$TempVar = $forecasttemp;               // ec-forecast
		$lastUpdate = time($updated);
	} else if ($TypeScript == "WU") {
		$TempVar = $WUforecasttemp;             // WU-forecast 
		$lastUpdate = time($WUforecastupdated);
	} else if ($TypeScript == "YR") {
		$TempVar = $yrforecast;                 // yr.no-forecast
		$lastUpdate = time($lupd);
	} else return;
	
//	if (!isset($TempVar[1]) || $TempVar[1] == "" || $TempVar[1] == " ") {                  // Trying $TempVar[1] because we had one slip through
	if (!isset($TempVar[1]) || trim($TempVar[1]) == "") {	
		echo "Unable to obtain the temperature variables from " . $NWSscript . ", check the installation of that script.";	
		return;
	} else {
		if ($TypeScript == "EC") {	// Need to process the EC data to put it in the proper format
			$index = 0;
			for ($i=0;$i<=6;$i++) {    
				$TempVar[$i] = preg_replace('|<br/>|',"",$TempVar[$i]);
				$TempVar[$i] = preg_replace('|<b>|',"",$TempVar[$i]);
				$TempVar[$i] = preg_replace('|</b>|',"",$TempVar[$i]);
				$TempVar[$i] = preg_replace('|&deg;C|',"",$TempVar[$i]);
				$TempVar[$i] = preg_replace('|&deg;F|',"",$TempVar[$i]);				
				$TempVar[$i] = preg_replace('|&nbsp;|',$LangNA,$TempVar[$i]);
				if ($TempVar[$i] == "") { $TempVar[$i] = $LangNA; }          // Just in case
				preg_match_all('#">(.+?)</#is', $TempVar[$i], $contents);     //Extract just the temp 
				$temp[] = '">' . $contents[1][0] . "&deg;";          // These for Jean-Robert
				$temp[] = '">' . $contents[1][1] . "&deg;";
//				$temp[$index] = $contents[1][0];                     // These worked for Phil
//				$temp[$index] = '">' . $contents[1][1] . "&deg;";
				$index++;
			}
			$index = 0;		
			// This shifts the lows so there are no n/a entries - Philippe's recommendation
			if ($temp[0] == '">' . $LangNA ."&deg;") {   // It's a night forecast
				$temp[0] = $temp[1];			
				$temp[1] = $temp[2];			
				$temp[2] = $temp[5];			
				$temp[3] = $temp[4];			
				$temp[4] = $temp[7];			
				$temp[5] = $temp[6];			
				$temp[6] = $temp[9];			
				$temp[7] = $temp[8];			
				$temp[8] = $temp[11];			
				$temp[9] = $temp[10];			
				$temp[10] = $temp[13];			
				$temp[11] = $temp[12];			
				$temp[12] = $temp[15];			
				$temp[13] = $temp[14];			
			} else {                                // It's day		
				for ($i=0;$i<=14;$i++) {                          // This seems to work for the days
					if ($temp[$i] == '">' . $LangNA ."&deg;") {
						$save = $temp[$i];		
						$temp[$i] = $temp[$i+2];
						$temp[$i+2] = $save;
					}
				}						
			}
			// End of the shift code			
			$TempVar = $temp;
		} else if ($TypeScript == "WU") {	// Remove High: and Low: from WU data
			for ($i=0;$i<=14;$i++) {    
				$TempVar[$i] = str_replace('High',"",$TempVar[$i]);      
				$TempVar[$i] = str_replace('Low',"",$TempVar[$i]);
				$TempVar[$i] = str_replace($WUword1,"",$TempVar[$i]);      
				$TempVar[$i] = str_replace($WUword2,"",$TempVar[$i]);
				$TempVar[$i] = str_replace($WUword3,"",$TempVar[$i]);      
				$TempVar[$i] = str_replace($WUword4,"",$TempVar[$i]);
				$TempVar[$i] = str_replace($WUword5,"",$TempVar[$i]);      
				$TempVar[$i] = str_replace($WUword6,"",$TempVar[$i]);		
				$TempVar[$i] = str_replace(':',"",$TempVar[$i]);
				$TempVar[$i] = str_replace(' ',"",$TempVar[$i]);	
			}
		}
	}		
	
	$doPrint = false;
	include($WXSIMscript);                     // Initialize WxSim variables
	
	if (!isset($WXSIMtemp[0])) {
		echo "Unable to obtain the WxSim temperature variables, check your plaintext-parser.php installation.";	
		return;
	}
	
	$WXSIMupdated = substr($WXSIMupdated,strpos($WXSIMupdated,",")+1,strlen($WXSIMupdated));	
	$WXSIMupdated = str_replace("/","-",$WXSIMupdated);            // Added for Allesandro's problem			
	$now = time($WXSIMupdated);
	
	$fday = date("m/d/Y",$now);          // See if the forecast was at night
	$nstarttime = $fday . " " . $nightstart;
	$nendtime = $fday . " " . $nightend;	
	
	$nstarttime = strtotime($nightstart);
	$nendtime = strtotime($nightend);

	$isNight = $now > $nstarttime || $now < $nendtime;
	
	if ($isNight) {            // Need to swap the variables
		$save = $TheMax;
		$TheMax = $TheMin;
		$TheMin = $save;
		if ($TypeScript == "WU") {      // WU holds the high temp both day and night
			$TempVar = array_slice($TempVar,1);		
		}
	}	
	
	$lines = file($fcstLog);                   // Reads data into an array of lines for use in a moment
	$entry = explode(",",$lines[0]);           // to get date/time from 1st record
	
	$mask = "m-d-y";

	$hadRedo = false;	
	if (! $debug) {	  
		$WSUpdate = date($mask,strtotime($WXSIMupdated));  
		$lastUpdate = date($mask,$lastUpdate);		
		if ($lastUpdate == date($mask,(int)$entry[0]) || $WSUpdate == date($mask,(int)$entry[0])) {
			if ($entry[1] == "" || $entry[1] == " ") {  				// Had a bad advforecast2 last time around.
				$lines = array_slice($lines, 1);	    // So we'll slice that record off & try again
				$hadRedo = true;	
			} else {
				echo "This forecast has already been logged";
				return;			
			}				
		}			
	}

	if (! $hadRedo) {	                           // Otherwise they were already logged with the bad advforecast2 session
		for ($i=0;$i<=($analdays)-1;$i++) {        // Update the records with yesterday's temps
			$lines[$i] = preg_replace('/~/', $TheMax, $lines[$i], 1);  // First the high
			$lines[$i] = preg_replace('/~/', $TheMin, $lines[$i], 1);  // Then the low
		}
	}
	
	if ($useWxSimTime) {
		$entry = strtotime($WXSIMupdated) . ","; 
	} else {
		$entry = time() . ",";
	}

	for ($i=0;$i<=($analdays*2)-1;$i++) {      // Build today's record
		if ($TypeScript == "YR") {             // Output from Henkka's yr.no parser
			$cont= $TempVar[$i];
			$entry .= $cont . ",~,";
		} else {
//			preg_match_all('#">(.+?)&deg;#is', $TempVar[$i], $contents);     // Extract just the temp from advforecast2 vars
			preg_match_all('#">(.+?)<#is', $TempVar[$i], $contents);     // Extract just the temp from advforecast2 vars			
			$contents[1][0] = preg_replace('/&asymp;/',"",$contents[1][0]);	 // In case there's a "~" in there	
			$contents[1][0] = str_replace('&deg;',"",$contents[1][0]);	     	
			$contents[1][0] = str_replace('°',"",$contents[1][0]);	         			
			$contents[1][0] = str_replace('C',"",$contents[1][0]);           	
			$contents[1][0] = str_replace('F',"",$contents[1][0]);   			
			$entry .= $contents[1][0] . ",";
			$entry .= "~,";
		}		
		preg_match_all('#">(.+?)<#is', $WXSIMtemp[$i], $contents);       //Same for the WxSim vars
		$contents[1][0] = str_replace('&deg;',"",$contents[1][0]);	     // Have to do it this way because they can elect to not have a degree symbol	
		$contents[1][0] = str_replace('°',"",$contents[1][0]);	         // Sometimes comes out this way?			
		$contents[1][0] = str_replace('C',"",$contents[1][0]);           // There's an option now where they can have the measure there	
		$contents[1][0] = str_replace('F',"",$contents[1][0]);   
		$contents[1][0] = str_replace('Maximum',"",$contents[1][0]);           	
		$contents[1][0] = str_replace('Minimum',"",$contents[1][0]);   	
		$contents[1][0] = str_replace($WSword1,"",$contents[1][0]);      
		$contents[1][0] = str_replace($WSword2,"",$contents[1][0]);
		$entry .= substr($contents[1][0],strpos($contents[1][0]," ")+1,strlen($contents[1][0])) . ",";
	}
	$entry .= "\n"; 
	
	$fp = fopen($fcstLog, "w") or die("Error: You must manually upload an empty text file named " . $fcstLog . " and give it write permissions (CHMOD 666)."); 
	fwrite($fp, $entry);                  // Write the most recent entry
	
	for ($i=0;$i<=$logdays-1;$i++) {
		fwrite($fp, $lines[$i]);          // Write the rest of them
	}	
	fclose($fp); 
// End of the logging option                                
} else {  // display the current stats
	echo "\n";
	echo '<!-- Begin forecast-compare-include Version ' . $version . ' - by Jim McMurry jcweather.us -->' . "\n";
	echo '<!-- Time Zone is ' . date("e (T)") . ' Path ' . $fcDir;
	if (file_exists("fc-temps.txt")) {   
		echo ' - Using fc-temps.txt file';
	} else {
		echo ' - Using ' . $tagsfile;	
	}	
	echo ' -->' . "\n";

	if ($multiMenus) {
		$myID = substr($logfile[$_REQUEST['config']], -6, 2);
		$lb = "#";	
	} else {
		$myID = "";	
		$lb = "";
	}
	
	$lines = file($fcstLog);          // Reads into an array of lines
	$entry = explode(",",$lines[0]);  // to get date/time from 1st record
	
	$fday = date("m/d/Y",$entry[0]);          // See if the forecast was at night
	
	$nstarttime = $fday . " " . $nightstart;
	$nendtime = $fday . " " . $nightend;
	
	$nstarttime = strtotime($nstarttime);
	$nendtime = strtotime($nendtime);
	
	$isNight = ($entry[0] > $nstarttime) || ($entry[0] < $nendtime);
	if ($isNight) {            // Need to swap the headings
		$save = $Langhigh;
		$Langhigh = $Langlow;
		$Langlow = $save;
	}		

	if (isset($_POST['start' . $myID])) {
  		$startDay = floor($_POST['start' . $myID]);
	} else {
		$startDay = 0;
	}	

	if (isset($_POST['cols' . $myID])) {
  		$Danaldays = floor($_POST['cols' . $myID]);
	}
	
	if (isset($_POST['goal' . $myID])) {
  		$goal = $_POST['goal' . $myID];
	} else {
		$goal = sprintf("%01.1f", $goal);
	}
	
	if (isset($_POST['rows' . $myID])) {
  		$Dlogdays = floor($_POST['rows' . $myID]);
		
		if($_POST['stats' . $myID] == "on") {      // Read the checkboxes here because they don't post an "off" state
			$showComp = true;
		} else {
			$showComp = false;
		}
		if($_POST['diffs' . $myID] == "on") {
			$showdeltas = true;
		} else {
			$showdeltas = false;
		}
		if($_POST['hnums' . $myID] == "on") {
			$boldnumbers = true;
		} else {
			$boldnumbers = false;
		}
		if($_POST['times' . $myID] == "on") {
			$disptimes = true;
		} else {
			$disptimes = false;
		}
		if($_POST['NWSon' . $myID] == "on") {
			$dispNWS = true;
		} else {
			$dispNWS = false;
		}
	}

	if ($showComp) { $stats[$myID] = "on"; }        // So the checkboxes will show the correct state
	if ($showdeltas) { $diffs[$myID] ="on"; } 
	if ($boldnumbers) { $hnums[$myID] = "on"; }
	if ($disptimes) { $times[$myID] = "on"; } 
	if ($dispNWS) { $NWSon[$myID] = "on"; } 

	if ($Dlogdays > count($lines)) {
		if ($startDay+1 + $Dlogdays > count($lines)) {
			$Dlogdays = count($lines) - $startDay;
		} else {
			$Dlogdays = count($lines);
		}
	}

	if ($logdays > count($lines)) $logdays = count($lines);

if ($TableCenter) {
	echo '<table style="border:0;margin-left:auto;margin-right:auto;">' . "\n";
} else {
	echo '<table style="border:0;margin-left:0;margin-right:auto;">' . "\n";
}	
?>
  <tr style="text-align:center;">
    <td><?php echo $Langupdated . date($datestr,(int)$entry[0]) . $Langat . date($timestr,(int)$entry[0]); ?> </td>
  </tr>
  <tr>
    <td>
    <table width="100%" style="border-collapse:collapse;border-spacing:0px;border-style:outset;border:solid 1px;border-color:<?php echo $lineColor ?>;background-color:white;">	
<?php	
	echo '    <tr class="column-dark" style="text-align:center;border-top:solid 1px ' . $lineColor . ';">' . "\n";       // Day headings grouped
	if ($goal == 0) {
		echo '      <td style="min-width:50px;border-right:solid 1px;border-left:solid 1px;border-color:' . $lineColor . ';">' . $LangTop1 . '</td>' . "\n";
	} else {
		echo '      <td style="min-width:50px;border-right:solid 1px;border-left:solid 1px;border-color:' . $lineColor . ';">' . $LangTop3 . '</td>' . "\n";
	}
	if (! $showdeltas) {
		$span = 6;
		if (! $dispNWS) { $span = 4; }
	} else {
		$span = 10;	
		if (! $dispNWS) { $span = 6; }
	}
	echo '      <td colspan="' . $span . '" style="border-right:solid 1px;border-bottom: solid 1px;border-color:' . $lineColor . ';">' . $LangPday . $Langday . '</td>' . "\n";
	for ($L=1;$L<=$Danaldays-1;$L++) {
		echo '      <td colspan="' . $span . '" style="border-right:solid 1px;border-bottom: solid 1px;border-color:' . $lineColor . ';">' . $Langday . "+" . $L . '</td>' . "\n";
	}
	echo '    </tr>' . "\n";	
	echo '    <tr class="column-dark" style="text-align:center;">' . "\n";      // High/Low headings grouped
	if ($goal == 0) {
	echo '      <td style="border-left:solid 1px;border-right:solid 1px;border-bottom: solid 1px;border-left:solid 1px;border-color:' . $lineColor . ';">' . $LangTop2 . '</td>' . "\n";
	} else {
	echo '      <td style="border-left:solid 1px;border-right:solid 1px;border-bottom: solid 1px;border-left:solid 1px;border-color:' . $lineColor . ';">' . "&#177; " . $goal . $LangTop2 . '</td>' . "\n";
	}
	if (! $showdeltas) {
		$span = 3;
		if (! $dispNWS) { $span = 2; }
	} else {
		$span = 5;	
		if (! $dispNWS) { $span = 3; }
	}	
	for ($L=1;$L<=$Danaldays*2;$L++) {
		if ($L % 2 != 0) {
			$hword = $Langhigh;
		} else {
			$hword = $Langlow;
		}
		echo '      <td colspan="' . $span . '" style="border-right:solid 1px;border-bottom: solid 1px;border-color:' . $lineColor . ';">' . $hword . '</td>' . "\n";
	}
	echo '    </tr>' . "\n";	

	echo '    <tr class="table-top" style="text-align:center;">' . "\n";        // Display each day and it's temperatures
	echo '      <td style="border-left:solid 1px;border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $Langdate . '</td>' . "\n";
	$end = $Danaldays*2;
	$lastTriplet = $end - 3;
	for ($L=1;$L<=$end;$L++) {
		if ($dispNWS) {
			echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">'. $LangNW . '</td>' . "\n";
				if ($showdeltas) {
					echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';"> ' . $LangDiff . '</td>' . "\n";
				}
		}
		echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">'. $LangAct . '</td>' . "\n";
			if ($showdeltas) {
				echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';"> ' . $LangDiff . '</td>' . "\n";
			}
		echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">'. $LangWS . '</td>' . "\n";	
	}
	echo '    </tr>' . "\n";
	$logger = array();  // holds the "wins" so total and percent can be displayed.
	$deltas = array();  // holds the absolute diff from actual for each forecast for use later.
	$gaps = array();  // holds the diff from actual for each forecast for use later.
	for ($L=$startDay;$L<=$startDay+$Dlogdays-1;$L++) {  
		$entry = explode(",",$lines[$L]);
		if ($entry[0] != "") {
			if ($L % 2 != 0) {  // This changes the row color
				echo '    <tr class="column-dark" style="text-align:center;background-image:none;">' . "\n";	
			} else {
				echo '    <tr class="column-light" style="text-align:center;background-image:none;">' . "\n";	
			}
			echo '      <td style="border-left:solid 1px;border-right:solid 1px;border-bottom: solid 1px;border-color:' . $lineColor . ';">';
				if ($entry[0] != "") {
					echo date($datestr,(int)$entry[0]);
					if ($disptimes) {
						echo "<br />" . date($timestr,(int)$entry[0]);
					}
				} else {
					echo "&nbsp;";		
				}
			echo '      </td>' . "\n";
			$end = $Danaldays*2*3;
			$lastTriplet = $end - 3;
			for ($i=1;$i<=$end;$i++) {
				$color1 = $color2 = $color3 = "";
				if ($entry[$i] != "" && ($entry[$i+1] != "~" && $entry[$i+1] != "")) { 
					if ($rndactuals) {
						$entry[$i+1] = round($entry[$i+1],0);
					}
					if ($entry[$i] != $LangNA) {
					
						$nwdelta = $entry[$i] - $entry[$i+1];
						$deltas[$i][$L] = abs($nwdelta);
						$gaps[$i][$L] = $nwdelta;
						$nwdelta = round($nwdelta,1);

					} else {
						$nwdelta = 99;
					}
					
					$wsdelta = $entry[$i+2] - $entry[$i+1];
					$deltas[$i+2][$L] = abs($wsdelta);
					$gaps[$i+2][$L] = $wsdelta;											
					$wsdelta = round($wsdelta,1);
					
					$logger[$i][$L] = $logger[$i+1][$L] = $logger[$i+2][$L] = 0;
					
					if ($goal > 0) {            // Goal oriented approach
						if ($boldnumbers) {
							if (abs($nwdelta) <= $goal) {
								$color1 = "font-weight:600;color:" . $BNWwinColor . ";";
								$logger[$i][$L] = 1;			
							}
							if (abs($wsdelta) <= $goal) {
								$color3 = "font-weight:600;color:" . $BWSwinColor . ";";
								$logger[$i+2][$L] = 1;			
							}
						} else {      // Highlight the background
							if (abs($nwdelta) <= $goal) {
								$color1 = "background-color:" . $NWwinColor . ";";
								$logger[$i][$L] = 1;			
							}
							if (abs($wsdelta) <= $goal) {
								$color3 = "background-color:" . $WSwinColor . ";";
								$logger[$i+2][$L] = 1;			
							}	
						}
					
					} else {					
						if ($boldnumbers) {
							if (abs($nwdelta) < abs($wsdelta)) {		   // NWS wins
								$color1 = "font-weight:600;color:" . $BNWwinColor . ";";
								$logger[$i][$L] = 1;			
							} else if (abs($wsdelta) < abs($nwdelta)) {  // WxSim wins
								$color3 = "font-weight:600;color:" . $BWSwinColor . ";";
								$logger[$i+2][$L] = 1;			
							} else {				          // They tie
								$color2 = "font-weight:600;color:" . $BtieColor . ";";
								$logger[$i+1][$L] = 1;			
							}
						} else {      // Highlight the background
							if (abs($nwdelta) < abs($wsdelta)) {		  
								$color1 = "background-color:" . $NWwinColor . ";";
								$logger[$i][$L] = 1;		
							} else if (abs($wsdelta) < abs($nwdelta)) {		
								$color3 = "background-color:" . $WSwinColor . ";";
								$logger[$i+2][$L] = 1;				
							} else {				
								$color2 = "background-color:" . $tieColor . ";";
								$logger[$i+1][$L] = 1;				
							}
						}
					}
				}

				if ($nwdelta == 99) { 
					$nwdelta = "&nbsp;";
				}
				if ($entry[$i+1]=="~"  || $entry[$i+1]==""|| $entry[$i]=="") {  // Take care of problem when expanding "days watched"
					$entry[$i+1]="&nbsp;";
					$nwdelta = "&nbsp;";
					$wsdelta = "&nbsp;";					
				}
				
				if ($dispNWS) {
					echo '      <td  style="border-right:solid 1px;border-bottom: solid 1px;border-color:' . $lineColor . ';' . $color1 . '"> ' . $entry[$i] . '</td>' . "\n";
					if ($showdeltas) {
						if ($nwdelta != "&nbsp;") { 
							$deltacolor = "#000000";
							if ($nwdelta > 0) {
								$deltacolor = "#ff0000";    // color text red
							} else if ($nwdelta < 0) {
								$deltacolor = "#0000d8";    // color text blue
							}
							$nwdelta = number_format($nwdelta,1);    // Change here for differences column rounding
						}
						echo '      <td class="table-top" style="background-image:none;text-align:right;border-right:solid 1px;border-bottom: solid 1px;border-color:' . $lineColor . ';color:' . $deltacolor . ';"> ' . $nwdelta . '</td>' . "\n";
					}
				}
				echo '      <td  style="border-right:solid 1px;border-bottom: solid 1px;border-color:' . $lineColor . ';' . $color2 . '"> ' . $entry[$i+1] . '</td>' . "\n";
				if ($showdeltas) {
					if ($wsdelta != "&nbsp;") { 
						$deltacolor = "#000000";
						if ($wsdelta > 0) {
							$deltacolor = "#ff0000";      // color text red
						} else if ($wsdelta < 0) {
							$deltacolor = "#0000d8";      // color text blue
						}
						$wsdelta = number_format($wsdelta,1);       // Change here for differences column rounding
					}
					echo '      <td class="table-top" style="background-image:none;text-align:right;border-right:solid 1px;border-bottom: solid 1px;border-color:' . $lineColor . ';color:' . $deltacolor . ';"> ' . $wsdelta . '</td>' . "\n";
				}	
				if ($i <= $lastTriplet) {	
					echo '      <td  style="border-right: 3px double;border-bottom: solid 1px;border-color:' . $lineColor . ';' . $color3 . '"> ' . $entry[$i+2] . '</td>' . "\n";
				} else {
					echo '      <td  style="border-right: 1px solid;border-bottom: solid 1px;border-color:' . $lineColor . ';' . $color3 . '"> ' . $entry[$i+2] . '</td>' . "\n";
				}
				$i = $i + 2;	// shift to next 3 (next period)
			}
			echo '    </tr>' . "\n";	
		}		
	}

	echo '    <tr class="table-top" style="text-align:center;">' . "\n";        // Reiterate the sources
	echo '      <td style="min-width:50px;border-left:solid 1px;border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $Langresults . '</td>' . "\n";
	$end = $Danaldays*2;
	$lastTriplet = $end-1;
	for ($i=1;$i<=$end;$i++) {
		if ($dispNWS) {
			echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">'. $LangNW . '</td>' . "\n";
				if ($showdeltas) {
					echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $LangDiff . '</td>' . "\n";
				}
		}	
		if ($goal > 0) {
			echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">'. $LangAct . '</td>' . "\n";
		} else {	
			echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">'. $Langtie . '</td>' . "\n";
		}
			if ($showdeltas) {
				echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $LangDiff . '</td>' . "\n";
			}
		if ($i <= $lastTriplet) {	
			echo '      <td style="border-right: 3px double;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $LangWS . '</td>' . "\n";
		} else {
			echo '      <td style="border-right: 1px solid;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $LangWS . '</td>' . "\n";
		}
	}
	echo '    </tr>' . "\n";	
	
	echo '    <tr class="column-dark" style="text-align:center;">' . "\n";        // Show number of "wins"
	
	if ($goal > 0) {
		echo '      <td style="min-width:50px;border-left:solid 1px;border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $Langnsucc . '</td>' . "\n";
	} else {
		echo '      <td style="min-width:50px;border-left:solid 1px;border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $Langwins . '</td>' . "\n";
	}
	$end = $Danaldays*2*3;
	$lastTriplet = $end - 3;
	for ($i=1;$i<=$end;$i++) {
		if ($dispNWS) {
			if ($logger[$i]) { $total = array_sum($logger[$i]); } else { $total = ""; }
			echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $total . '</td>' . "\n";
			if ($showdeltas) {
				echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
			}
		}
		if ($logger[$i+1]) { $total = array_sum($logger[$i+1]); } else { $total = ""; }
		if ($goal > 0) { $total = ""; }
		echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $total . '</td>' . "\n";
		if ($showdeltas) {
			echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
		}
		if ($logger[$i+2]) { $total = array_sum($logger[$i+2]); } else { $total = ""; }
		if ($i <= $lastTriplet) {	
		
			echo '      <td style="border-right: 3px double;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $total . '</td>' . "\n";	
		} else {
			echo '      <td style="border-right: 1px solid;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $total . '</td>' . "\n";	
		}
		$i = $i + 2;
	}
	echo '    </tr>' . "\n";	
	echo '    <tr class="column-dark" style="text-align:center;">' . "\n";        // Show percentage of "wins"
	
	if ($goal > 0) {
		echo '      <td style="min-width:50px;border-left:solid 1px;border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $Langpsucc . '</td>' . "\n";
	} else {
		echo '      <td style="min-width:50px;border-left:solid 1px;border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $Langpwins . '</td>' . "\n";
	}
	
	$end = $Danaldays*2*3;
	$lastTriplet = $end - 3;
	for ($i=1;$i<=$end;$i++) {
		if ($dispNWS) {
			if ($logger[$i] && array_sum($logger[$i]) != 0) {		
				echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_percent($logger[$i]),1) . '</td>' . "\n";
			} else {
				echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
			}
			if ($showdeltas) {
				echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n"; 
			}
		}		
		if ($logger[$i+1] && array_sum($logger[$i+1]) != 0) {		
			echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_percent($logger[$i+1]),1) . '</td>' . "\n";
		} else {
			echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
		}
		if ($showdeltas) {
			echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
		}
		if ($deltas[$i+2] != 0) {	
			if ($i <= $lastTriplet) {	
				echo '      <td style="border-right: 3px double;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_percent($logger[$i+2]),1) . '</td>' . "\n";	
			} else {      // At the last cell
				echo '      <td style="border-right: 1px solid;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_percent($logger[$i+2]),1) . '</td>' . "\n";	
			}
		} else {
			if ($i <= $lastTriplet) {	
				echo '      <td style="border-right: 3px double;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
			} else {       // At the last cell
				echo '      <td style="border-right: 1px solid;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
			}
		}
		$i = $i + 2;
	}
	echo '    </tr>' . "\n";	
	
	if ($showComp) {

	
		echo '    <tr class="column-dark" style="text-align:center;">' . "\n";        // Show average difference - mean
		echo '      <td style="min-width:50px;border-left:solid 1px;border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $LangMean . '</td>' . "\n";
		$end = $Danaldays*2*3;
		$lastTriplet = $end - 3;
		for ($i=1;$i<=$end;$i++) {
			if ($showdeltas) {
				if ($dispNWS) {
					echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					if ($deltas[$i] != 0) {		
						echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_mean($deltas[$i]),1) . '</td>' . "\n";
					} else {
						echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					}
				}
				echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				if ($deltas[$i+2] != 0) {		
					echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_mean($deltas[$i+2]),1) . '</td>' . "\n";
				} else {
					echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				}
				if ($i <= $lastTriplet) {	
					echo '      <td  style="border-right: 3px double;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				} else {       // At the last cell
					echo '      <td  style="border-right: 1px solid;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				}
			} else {
				if ($dispNWS) {
					if (count($deltas[$i]) != 0) {		
						echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_mean($deltas[$i]),1) . '</td>' . "\n";
					} else {
						echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					}
				}
				echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n"; 
				if ($deltas[$i+2] != 0) {	
					if ($i <= $lastTriplet) {	
						echo '      <td style="border-right: 3px double;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_mean($deltas[$i+2]),1) . '</td>' . "\n";	
					} else {      // At the last cell
						echo '      <td style="border-right: 1px solid;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_mean($deltas[$i+2]),1) . '</td>' . "\n";	
					}
				} else {
					if ($i <= $lastTriplet) {	
						echo '      <td style="border-right: 3px double;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					} else {       // At the last cell
						echo '      <td style="border-right: 1px solid;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					}
				}
			}	
			$i = $i + 2;
		}
		echo '   </tr>';	

		echo '    <tr class="column-dark" style="text-align:center;">' . "\n";        // Show net error
		echo '      <td style="min-width:50px;border-left:solid 1px;border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . $LangNet . '</td>' . "\n";
		$end = $Danaldays*2*3;
		$lastTriplet = $end - 3;
		for ($i=1;$i<=$end;$i++) {
			if ($showdeltas) {
				if ($dispNWS) {
					echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					if ($deltas[$i] != 0) {		
						echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_mean($gaps[$i]),1) . '</td>' . "\n";
					} else {
						echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					}
				}
				echo '      <td  style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				if ($deltas[$i+2] != 0) {		
					echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_mean($gaps[$i+2]),1) . '</td>' . "\n";
				} else {
					echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				}
				if ($i <= $lastTriplet) {	
					echo '      <td  style="border-right: 3px double;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				} else {       // At the last cell
					echo '      <td  style="border-right: 1px solid;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				}
			} else {
				if ($dispNWS) {
					if (count($deltas[$i]) != 0) {		
						echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_mean($gaps[$i]),1) . '</td>' . "\n";
					} else {
						echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					}
				}
				echo '      <td style="border-right:solid 1px;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n"; 
				if ($deltas[$i+2] != 0) {	
					if ($i <= $lastTriplet) {	
						echo '      <td style="border-right: 3px double;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_mean($gaps[$i+2]),1) . '</td>' . "\n";	
					} else {      // At the last cell
						echo '      <td style="border-right: 1px solid;border-bottom: 3px double;border-color:' . $lineColor . ';">' . number_format(get_mean($gaps[$i+2]),1) . '</td>' . "\n";	
					}
				} else {
					if ($i <= $lastTriplet) {	
						echo '      <td style="border-right: 3px double;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					} else {       // At the last cell
						echo '      <td style="border-right: 1px solid;border-bottom: 3px double;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					}
				}
			}	
			$i = $i + 2;
		}
		echo '   </tr>';	

		
		echo '    <tr class="column-dark" style="text-align:center;">' . "\n";        // Show Standard Deviation
		echo '      <td style="min-width:50px;border-left:solid 1px;border-right:solid 1px;border-bottom: 1px solid;border-color:' . $lineColor . ';">' . $LangSD . '</td>' . "\n";
		$end = $Danaldays*2*3;
		$lastTriplet = $end - 3;
		for ($i=1;$i<=$end;$i++) {
			if ($showdeltas) {
				if ($dispNWS) {
					echo '      <td  style="border-right:solid 1px;border-bottom: 1px solid;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					if ($deltas[$i] != 0) {		
						echo '      <td style="border-right:solid 1px;border-bottom: 1px solid;border-color:' . $lineColor . ';">' . number_format(get_standard_deviation($deltas[$i]),1) . '</td>' . "\n";
					} else {
						echo '      <td style="border-right:solid 1px;border-bottom: 1px solid;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					}
				}
				echo '      <td  style="border-right:solid 1px;border-bottom: 1px solid;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				if ($deltas[$i+2] != 0) {		
					echo '      <td style="border-right:solid 1px;border-bottom: 1px solid;border-color:' . $lineColor . ';">' . number_format(get_standard_deviation($deltas[$i+2]),1) . '</td>' . "\n";
				} else {
					echo '      <td style="border-right:solid 1px;border-bottom: 1px solid;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				}
				if ($i <= $lastTriplet) {	
					echo '      <td  style="border-right: 3px double;border-bottom: 1px solid;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				} else {       // At the last cell
					echo '      <td  style="border-right: 1px solid;border-bottom: 1px solid;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				}
			} else {
				if ($dispNWS) {
					if (count($deltas[$i]) != 0) {		
						echo '      <td style="border-right:solid 1px;border-bottom: 1px solid;border-color:' . $lineColor . ';">' . number_format(get_standard_deviation($deltas[$i]),1) . '</td>' . "\n";
					} else {
						echo '      <td style="border-right:solid 1px;border-bottom: 1px solid;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					}
				}
				echo '      <td style="border-right:solid 1px;border-bottom: 1px solid;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
				if ($deltas[$i+2] != 0) {	
					if ($i <= $lastTriplet) {	
						echo '      <td style="border-right: 3px double;border-bottom: 1px solid;border-color:' . $lineColor . ';">' . number_format(get_standard_deviation($deltas[$i+2]),1) . '</td>' . "\n";	
					} else {      // At the last cell
						echo '      <td style="border-right: 1px solid;border-bottom: 1px solid;border-color:' . $lineColor . ';">' . number_format(get_standard_deviation($deltas[$i+2]),1) . '</td>' . "\n";	
					}
				} else {
					if ($i <= $lastTriplet) {	
						echo '      <td style="border-right: 3px double;border-bottom: 1px solid;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					} else {       // At the last cell
						echo '      <td style="border-right: 1px solid;border-bottom: 1px solid;border-color:' . $lineColor . ';">&nbsp;</td>' . "\n";
					}
				}
			}
			$i = $i + 2;
		}			
		echo '   </tr>';	
		
		
	}
	if ($isNight) {            // Need to put them back as they were
		$save = $Langhigh;
		$Langhigh = $Langlow;
		$Langlow = $save;
	}		
	
?>	
   </table>	   
  </td>
  </tr> 
<?php 
if ($showSels && $SelsOn[$_REQUEST['config']]) {
?> 
  <tr> 
    <td style="text-align:center;">
		<form id="col_sel<?php echo $myID ?>" method="post" action="<?php echo $lb . $myID ?>">  
		<fieldset style="border:0; margin:0; padding:0;">
<?php 
if ($showDates) {
?>	
			<label for="start<?php echo $myID ?>"><?php echo $LangStart ?>
			<select id="start<?php echo $myID ?>" name="start<?php echo $myID ?>" style="background-color:white !important;" >
			<?php			
				for ( $i = 0 ; $i <= $logdays - 1; $i ++ ) {
					$entry = explode(",",$lines[$i]);
					echo '			<option value="' . $i . '"'; 
					if ($i==$startDay) echo ' selected="selected"';  
					echo '>' . date($datestr,(int)$entry[0]) . '</option>' . "\n";
				}
			?>			
			</select></label>
<?php 
}
if ($showRows) {
?>	
			<label for="rows<?php echo $myID ?>"><?php echo '&nbsp;' . $LangRows ?>
			<select id="rows<?php echo $myID ?>" name="rows<?php echo $myID ?>" style="background-color:white !important;" >
			<?php			
				for ( $i = 1 ; $i <= $logdays ; $i ++ ) {
					echo '			<option value="' . $i . '"'; 
					if ($i==$Dlogdays) echo ' selected="selected"';  
					echo '>' . $i . '</option>' . "\n";
				}
			?>			
			</select></label>
<?php 
}
if ($showCols) {
?>	
			<label for="cols<?php echo $myID ?>"><?php echo '&nbsp;' . $LangCols ?>
			<select id="cols<?php echo $myID ?>" name="cols<?php echo $myID ?>" style="background-color:white !important;" >
			<?php			
				for ( $i = 1 ; $i <= $analdays ; $i ++ ) {
					echo '			<option value="' . $i . '"'; 
					if ($i==$Danaldays) echo ' selected="selected"';  
					echo '>' . $i . '</option>' . "\n";
				}
			?>			
			</select></label>
<?php 
}
if ($showGoals) {
?>	
			<label for="goal<?php echo $myID ?>"><?php echo '&nbsp;&nbsp;' . $LangGoals ?>
			<input  style="width:2em;height:1.1em;border:inset 2px #eeeeee;text-align:center;" type="text" id="goal<?php echo $myID ?>" name="goal<?php echo $myID ?>" value="<?php echo $goal ?>" />
			</label>
<?php 
}
if ($Danaldays < 4) { echo "<br />"; }
if ($showStats) {
?>	
			<label for="stats<?php echo $myID ?>"><?php echo '&nbsp;&nbsp;' . $LangStats ?>
			<input type="checkbox" id="stats<?php echo $myID ?>" name="stats<?php echo $myID ?>" <?php if($stats[$myID] == "on"){echo' checked="checked"';} ?> />
			</label>
<?php 
}
if ($showDiffs) {
?>	
			<label for="diffs<?php echo $myID ?>"><?php echo '&nbsp;&nbsp;' . $LangDsel ?>
			<input type="checkbox" id="diffs<?php echo $myID ?>" name="diffs<?php echo $myID ?>" <?php if($diffs[$myID] == "on"){echo' checked="checked"';} ?> />
			</label>
<?php 
}
if ($showHnums) {
?>	
			<label for="hnums<?php echo $myID ?>"><?php echo '&nbsp;&nbsp;' . $LangHlite ?>
			<input type="checkbox" id="hnums<?php echo $myID ?>" name="hnums<?php echo $myID ?>" <?php if($hnums[$myID] == "on"){echo' checked="checked"';} ?> />
			</label>
<?php 
}
if ($showTime) {
?>	
			<label for="times<?php echo $myID ?>"><?php echo '&nbsp;&nbsp;' . $LangTime ?>
			<input type="checkbox" id="times<?php echo $myID ?>" name="times<?php echo $myID ?>" <?php if($times[$myID] == "on"){echo' checked="checked"';} ?> />
			</label>
<?php 
}
if ($showNWS) {
?>	
			<label for="NWSon<?php echo $myID ?>"><?php echo '&nbsp;&nbsp;' . $LangSNWS . " " . $TypeScript ?>
			<input type="checkbox" id="NWSon<?php echo $myID ?>" name="NWSon<?php echo $myID ?>" <?php if($NWSon[$myID] == "on"){echo' checked="checked"';} ?> />
			</label>
<?php 
}
if ($Danaldays < 4) echo "<br />";
?>	
			<input type="submit" name="submit<?php echo $myID ?>" value="change" />
		</fieldset>	
		</form>
    </td>
  </tr> 
<?php
}   
?>   
</table>
<?php
}
echo '<!-- End forecast-compare-include Version' . $version . ' - by Jim McMurry jcweather.us -->' . "\n";
// End of script
?>