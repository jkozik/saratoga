<?php
/*
 File: VWS-defs.php

 Purpose: provide a bridge to naming of weather variables from the native Virtual Weather Station software package to
          Weather-Display names used in common scripts like ajax-dashboard.php and ajax-gizmo.php

 
 Author: Ken True - webmaster@saratoga-weather.org

 (created by gen-defs.php - V1.05 - 09-Sep-2011)
 Generated on 2011-12-09 18:12:19 PST

//Version VWS-defs.php - V1.02 - 09-Dec-2011

*/
// --------------------------------------------------------------------------

// allow viewing of generated source

if (isset($_REQUEST["sce"]) and strtolower($_REQUEST["sce"]) == "view" ) {
//--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header("Pragma: public");
   header("Cache-Control: private");
   header("Cache-Control: no-cache, must-revalidate");
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header("Connection: close");
   
   readfile($filenameReal);
   exit;
}
$WXsoftware = 'VWS';  
// this has WD $varnames = $WX['VWS-varnames']; equivalents
 
$uomtemp = $WX['uni007'];
$uombaro = $WX['uni023'];
$uomwind = $WX['uni002'];
$uomrain = $WX['uni009'];
$time = $WX['vst143'];
$date = $WX['vst142'];
$sunrise = $WX['vst144'];
$sunset = $WX['vst145'];
$moonrise = $WX['vst146'];
$moonset = $WX['vst147'];
$moonphase = $WX['moon_percent'];
$weatherreport = $WX['climate_cconds1'];
$stationlatitude = $WX['wslat'];
$stationlongitude = $WX['wslong'];
$NOAAEvent = $WX['warning_desc1'];
$wdversion = $WX['vervws'];
$wdversiononly = $WX['vervws'];
$noaacityname = $WX['wslocation'];
$heatcolourword = $WX['vst137'];
$temperature = $WX['vxv007'];
$tempnodp  = round($WX['vxv007'],0); // calculated value
$humidity = $WX['vxv005'];
$dewpt = $WX['vxv022'];
$maxtemp = $WX['vhi007'];
$maxtempt = $WX['vht007'];
$mintemp = $WX['vlo007'];
$mintempt = $WX['vlt007'];
$feelslike = $WX['vxv027'];
$heati = $WX['vxv021'];
$windch = $WX['vxv019'];
$WUmaxtemp = $WX['almnormhi1'];
$WUmintemp = $WX['almnormlo1'];
$WUmaxtempr = $WX['almhi1'];
$WUmintempr = $WX['almlo1'];
$WUmaxtempryr = $WX['almdatehi1'];
$WUmintempryr = $WX['almdatelo1'];
$tempchangehour = $WX['vvr007'];
$maxtempyest = $WX['vzh007'];
$maxtempyestt = $WX['vzt007'];
$mintempyest = $WX['vzl007'];
$mintempyestt = $WX['vzs007'];
$temp24hoursago = $WX['vhv007'];
$humchangelasthour = $WX['vvr005'];
$dewchangelasthour = $WX['vvr022'];
$barochangelasthour = $WX['vvr023'];
$avgspd = $WX['vxv002'];
$gstspd = $WX['vxv003'];
$maxgst = $WX['vhi003'];
$maxgstt = $WX['vht003'];
$dirlabel = $WX['vxv001'];
$bftspeedtext = $WX['vst141'];
$baro = $WX['vxv023'];
$trend = $WX['vvr023'];
$pressuretrendname = $WX['vst139'];
$pressuretrendname3hour = $WX['vst139'];
$vpforecasttext = $WX['vst138'];
$dayrn = $WX['vxv121'];
$monthrn = $WX['vxv129'];
$yearrn = $WX['vxv009'];
$dayswithnorain  = intval((strtotime(date('Y-m-d'))-strtotime(VWSfixupDate($WX['vyd009'],$SITE['WDdateMDY'])))/86400); // from yrly rain high date // calculated value
$currentrainratehr = $WX['vvr009'];
$maxrainrate = $WX['vrh009'];
$maxrainratehr = $WX['vrh009'];
$maxrainratetime = $WX['vrt009'];
$yesterdayrain  = sprintf('%01.2f', round($WX['vzh009']-$WX['vzl009'],2)); // calculated value
$VPsolar = $WX['vxv018'];
$VPuv = $WX['vxv017'];
$highsolar = $WX['vhi018'];
$highuv = $WX['vhi017'];
$highsolartime = $WX['vht018'];
$highuvtime = $WX['vht017'];
$mrecordwindgust = $WX['vmh003'];
$mrecordhighgustday  = date('j',strtotime(VWSfixupDate($WX['vmd003'],$SITE['WDdateMDY']))); // calculated value
$VPet = $WX['vxv016'];
$highbaro = $WX['vhi023'];
$highbarot = $WX['vht023'];
$hourrn = $WX['vxv122'];
$mrecordhightemp = $WX['vmh007'];
$mrecordlowtemp = $WX['vml007'];
$yrecordhightemp = $WX['vyh007'];
$yrecordlowtemp = $WX['vyl007'];
$cloudheightfeet = $WX['vxv025'];
$maxdew = $WX['vhi022'];
$maxdewt = $WX['vht022'];
$mindew = $WX['vlo022'];
$mindewt = $WX['vlt022'];
$maxdewyest = $WX['vzh022'];
$maxdewyestt = $WX['vzt022'];
$mindewyest = $WX['vzl022'];
$mindewyestt = $WX['vzs022'];
$mrecordhighdew = $WX['vmh022'];
$mrecordlowdew = $WX['vml022'];
$yrecordhighdew = $WX['vyh022'];
$yrecordlowdew = $WX['vyl022'];
$maxheat = $WX['vhi021'];
$maxheatt = $WX['vht021'];
$mrecordhighheatindex = $WX['vmh021'];
$yrecordhighheatindex = $WX['vyh021'];
$lowbaro = $WX['vlo023'];
$lowbarot = $WX['vlt023'];
$monthtodatemaxbaro = $WX['vmh023'];
$monthtodateminbaro = $WX['vml023'];
$avtempsincemidnight = $WX['vda007'];
$highhum = $WX['vhi005'];
$highhumt = $WX['vht005'];
$lowhum = $WX['vlo005'];
$lowhumt = $WX['vlt005'];
$maxhumyest = $WX['vzh005'];
$maxhumyestt = $WX['vzt005'];
$minhumyest = $WX['vzl005'];
$minhumyestt = $WX['vzs005'];

// end of generation script
# VWS unique functions included from VWS-functions-inc.txt 
$temp24hourago = $WX['vhv007'];
$wind24hourago = $WX['vhv002'];
$gust24hourago = $WX['vhv003'];
$dir24hourago = $WX['vhv001'];
$hum24hourago = $WX['vhv005'];
$baro24hourago = $WX['vhv023'];
$rain24hourago = sprintf('%01.2f', round($WX['vzh009']-$WX['vzl009'],2)); // calculated value


#-------------------------------------------------------------------------------------
# function processed WD variables
#-------------------------------------------------------------------------------------

$Currentsolardescription = $WX['climate_cconds1'];  // Current Conditions
$iconnumber = VWS_icons($WX['climate_icon1'],$WX['vst143'],$WX['vst144'],$WX['vst145']);
if(isset($SITE['conditionsMETAR'])) { // override with METAR conditions for text and icon if requested.
	global $SITE;
	include_once("get-metar-conditions-inc.php");
	list($Currentsolardescription,$iconnumber) = mtr_conditions($SITE['conditionsMETAR'], $time, $sunrise, $sunset);
}

$beaufortnum =  VWS_beaufortNumber($avgspd,$uomwind);
$bftspeedtext = VWS_beaufortText($beaufortnum);

list($chandler,$chandlertxt,$chandlerimg) = VWS_CBI($temperature,$uomtemp,$humidity);

# generate the separate date/time variables by dissection of input date/time and format
list($date_year,$date_month,$date_day,$time_hour,$time_minute,$monthname,$dayname)
  = VWS_setDateTimes($WX['vst142'],$WX['vst143'],$SITE['WDdateMDY']);

#-------------------------------------------------------------------------------------
# VWS support function - VWS_icons
#-------------------------------------------------------------------------------------

function VWS_icons($icon,$time,$sunrise,$sunset) {

	 global $Debug;
	 $VWSlist = array( // maps day/night, VWS icon names to WD conditions icon numbers
		'day' => array(
			  'chancetstorms' => 29,   // Chance Thunder storms
			  'chanceflurries' => 27,  // Chance Flurries
			  'chancerain' => 22,      // Chance Rain
			  'chancesleat' => 24,     // Chance Sleat
			  'clear' => 0,            // Clear
			  'cloudy' => 18,          // Cloudy
			  'fog' => 6,              // Fog
			  'flurries' => 27,        // Flurries
			  'hazy' => 7,             // Hazy
			  'mostlycloudy' => 2,     // Mostly Cloudy
			  'mostlysunny' => 9,      // Mostly Sunny
			  'partlycloudy' => 19,    // Partly Cloudy
			  'partlysunny' => 19,     // Partly Sunny
			  'rain' => 8,             // Rain
			  'sleat' => 23,           // Sleat
			  'snow' => 25,            // Snow
			  'sunny' => 0,            // Sunny
			  'thunderstorms' => 31,   // Thunderstorms
			  'unknown' => 0,          // Unknown
				 ),
		'night' => array(
			  'chancetstorms' => 17,   // Chance Thunder storms
			  'chanceflurries' => 16,  // Chance Flurries
			  'chancerain' => 15,      // Chance Rain
			  'chancesleat' => 23,     // Chance Sleat
			  'clear' => 1,            // Clear
			  'cloudy' => 4,           // Cloudy
			  'fog' => 11,             // Fog
			  'flurries' => 16,        // Flurries
			  'hazy' => 7,             // Hazy
			  'mostlycloudy' => 4,     // Mostly Cloudy
			  'mostlysunny' => 4,      // Mostly Sunny
			  'partlycloudy' => 4,     // Partly Cloudy
			  'partlysunny' => 4,      // Partly Sunny
			  'rain' => 14,            // Rain
			  'sleat' => 23,           // Sleat
			  'snow' => 16,            // Snow
			  'sunny' => 1,            // Sunny
			  'thunderstorms' => 17,   // Thunderstorms
			  'unknown' => 1,          // Unknown
				)
		); // end $VWSlist definitions
	 
	 $Debug .= "<!-- VWS_icons begin: '$time','$icon','$sunrise','$sunset' -->\n";
	 if(!preg_match('/^\d{1,2}:\d{2}[:\d{2}]{0,1}\s*[am|pm]*$/i',$sunrise)) { $sunrise = '';  }
	 if(!preg_match('/^\d{1,2}:\d{2}[:\d{2}]{0,1}\s*[am|pm]*$/i',$sunset)) { $sunset = '';  }
 
     $sunrise2 = VWSfixupTime(($sunrise<>'')?"$sunrise":"6:00a");
     $sunset2 = VWSfixupTime(($sunset<>'')?"$sunset":"7:00p");
     $time2 =   VWSfixupTime(($time<>'')?"$time":date("H:i",time()));
     if ($time2 >= $sunrise2 and $time2 <= $sunset2) {
         $daynight = 'day';
     } // end if
     else {
         $daynight = 'night';
     } // end else
	 $Debug .= "<!-- VWS_icons using: time2='$time2' as $daynight for sunrise2='$sunrise2',sunset2='$sunset2'  -->\n";
     if(isset($VWSlist[$daynight][$icon])) {
	     $Debug .= "<!-- VWS_icons using: $daynight iconnumber='".$VWSlist[$daynight][$icon]."' for $icon  -->\n";
		 return $VWSlist[$daynight][$icon];
	 } else {
	     $Debug .= "<!-- VWS_icons using: $daynight iconnumber='".$VWSlist[$daynight]['partlycloudy']."' default for $icon  -->\n";
		 return $VWSlist[$daynight]['partlycloudy']; // default icon: partly cloudy
	 }
} // end VWS_icons

#-------------------------------------------------------------------------------------
# VWS support function - VWSfixupTime
#-------------------------------------------------------------------------------------

function VWSfixupTime ($intime) {
  global $Debug;
  $tfixed = preg_replace('/^(\S+)\s+(\S+)$/is',"$2",$intime);
  $t = explode(':',$tfixed);
  if (preg_match('/p/i',$tfixed)) { $t[0] = $t[0] + 12; }
  if ($t[0] > 23) {$t[0] = 12; }
  if (preg_match('/^12.*a/i',$tfixed)) { $t[0] = 0; }
  if ($t[0] < '10') {$t[0] = sprintf("%02d",$t[0]); } // leading zero on hour.
  $t2 = join(':',$t); // put time back to gether;
  $t2 = preg_replace('/[^\d\:]/is','',$t2); // strip out the am/pm if any
  $Debug .= "<!-- VWSfixupTime in='$intime' tfixed='$tfixed' out='$t2' -->\n";
  return($t2);
  	
} // end VWSfixupTime

#-------------------------------------------------------------------------------------
# VWS support function - VWSfixupDate
#-------------------------------------------------------------------------------------

function VWSfixupDate ($indate,$WDdateMDY=true) {
  // input: mm/dd/yyyy or dd/mm/yyyy format 
  global $Debug;
  $d = explode('/',$indate);      // expect ##/##/## form
  if(!isset($d[2])) {$d = explode('-',$indate); } // try ##-##-#### form instead
  if ($d[2] > 70 and $d[2] <= 99) {$d[2] += 1900;} // 2 digit dates 70-99 are 1970-1999
  if ($d[2] < 99) {$d[2] += 2000; } // 2 digit dates (left) are assumed 20xx dates.
  if ($WDdateMDY) {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[0],$d[1]); //  M/D/YYYY -> YYYY-MM-DD
  } else {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[1],$d[0]); // D/M/YYYY -> YYYY-MM-DD
  }
  
  return ($new);
  	
} // end VWSfixupDate

#-------------------------------------------------------------------------------------
# VWS support function - VWS_setDateTimes
#-------------------------------------------------------------------------------------

function VWS_setDateTimes ($indate,$intime,$MDYformat=true) {
// returns: $date_year,$date_month,$date_day,$time_hour,$time_minute,$date_month,$monthname,$dayname
  global $Debug;
  $Debug .= "<!-- VWS_setDateTimes date='$indate' time=$intime' MDY=$MDYformat -->\n";
  $d = explode('/',$indate);
  if($d[2]<2000) {$d[2]+=2000;}
  if($MDYformat) { // mm/dd/yyyy
    $YMD = "$d[2]-$d[0]-$d[1]";
  } else {         // dd/mm/yyyy
    $YMD = "$d[2]-$d[1]-$d[0]";
  }
  $t = VWSfixupTime($intime);
  
  $VWStime = strtotime("$YMD $t:00");
  $Debug .= "<!-- VWS_setDateTimes VWStime='$YMD $t:00' assembled -->\n";
   
  $VWStime = date('Y m d H i F l',$VWStime);
  $Debug .= "<!-- VWS_setDateTimes VWStime='$VWStime' values set -->\n";
  if(isset($_REQUEST['debug'])) {echo $Debug; } 
  return(explode(' ',$VWStime)); // results returned in array for list() assignment
  	
} // end VWS_setDateTimes

#-------------------------------------------------------------------------------------
# VWS support function - VWS_beaufortNumber
#-------------------------------------------------------------------------------------

function VWS_beaufortNumber ($rawwind,$usedunit) {
   global $Debug;
  
// first convert all winds to knots

   $WINDkts = 0.0;
   if       (preg_match('/kts|knot/i',$usedunit)) {
	   $WINDkts = $rawwind * 1.0;
   } elseif (preg_match('/mph/i',$usedunit)) {
	   $WINDkts = $rawwind * 0.8689762;
   } elseif (preg_match('/mps|m\/s/i',$usedunit)) {
	   $WINDkts = $rawwind * 1.94384449;
   } elseif  (preg_match('/kmh|km\/h/i',$usedunit)) {
	   $WINDkts = $rawwind * 0.539956803;
   } else {
	   $Debug .= "<!-- VWS_beaufortNumber .. unknown input unit '$usedunit' for wind=$rawwind -->\n";
	   $WINDkts = $rawwind * 1.0;
   }

// return a number for the beaufort scale based on wind in knots
  if ($WINDkts < 1 ) {return(0); }
  if ($WINDkts < 4 ) {return(1); }
  if ($WINDkts < 7 ) {return(2); }
  if ($WINDkts < 11 ) {return(3); }
  if ($WINDkts < 17 ) {return(4); }
  if ($WINDkts < 22 ) {return(5); }
  if ($WINDkts < 28 ) {return(6); }
  if ($WINDkts < 34 ) {return(7); }
  if ($WINDkts < 41 ) {return(8); }
  if ($WINDkts < 48 ) {return(9); }
  if ($WINDkts < 56 ) {return(10); }
  if ($WINDkts < 64 ) {return(11); }
  if ($WINDkts >= 64 ) {return(12); }
  return("0");
} // end VWS_beaufortNumber

#-------------------------------------------------------------------------------------
# VWS support function - VWS_beaufortText
#-------------------------------------------------------------------------------------

function VWS_beaufortText ($beaufortnumber) {

  $B = array( /* Beaufort 0 to 12 in English */
   "Calm", "Light air", "Light breeze", "Gentle breeze", "Moderate breeze", "Fresh breeze",
   "Strong breeze", "Near gale", "Gale", "Strong gale", "Storm",
   "Violent storm", "Hurricane"
  );

  if(isset($B[$beaufortnumber])) {
	return $B[$beaufortnumber];
  } else {
    return "Unknown $beaufortnumber Bft";
  }
	
	
} // end VWS_beaufortText

#-------------------------------------------------------------------------------------
# VWS support function - VWS_CBI - Chandler Burning Index
#-------------------------------------------------------------------------------------

function VWS_CBI($inTemp,$inTempUOM,$inHumidity) {
	// thanks to Chris from sloweather.com for the CBI calculation script
	// modified by Ken True for template usage
	
	preg_match('/([\d\.\+\-]+)/',$inTemp,$t); // strip non-numeric from inTemp if any
	$ctemp = $t[1];
	if(!preg_match('|C|i',$inTempUOM)) {
	  $ctemp = ($ctemp-32.0) / 1.8; // convert from Fahrenheit	
	}
	preg_match('/([\d\.\+\-]+)/',$inHumidity,$t); // strip non-numeric from inHumidity if any
	$rh = $t[1];

	// Start Index Calcs
	
	// Chandler Index
	$cbi = (((110 - 1.373 * $rh) - 0.54 * (10.20 - $ctemp)) * (124 * pow(10,-0.0142 * $rh) ))/60;
	// CBI = (((110 - 1.373*RH) - 0.54 * (10.20 - T)) * (124 * 10**(-0.0142*RH)))/60
	
	//Sort out the Chandler Index
	$cbi = round($cbi,1);
	if ($cbi > "97.5") {
		$cbitxt = "EXTREME";
		$cbiimg= "fdl_extreme.gif";
	
	} elseif ($cbi >="90") {
		$cbitxt = "VERY HIGH";
		$cbiimg= "fdl_vhigh.gif";
	
	} elseif ($cbi >= "75") {
		$cbitxt = "HIGH";
		$cbiimg= "fdl_high.gif";
	
	} elseif ($cbi >= "50") {
		$cbitxt = "MODERATE";
		$cbiimg= "fdl_moderate.gif";
	
	} else {
		$cbitxt="LOW";
		$cbiimg= "fdl_low.gif";
	}
	 $data = array($cbi,$cbitxt,$cbiimg);
	 return $data;
	 
} // end VWS_CBI

#-------------------------------------------------------------------------------------
# end of VWS support functions
#-------------------------------------------------------------------------------------

?>