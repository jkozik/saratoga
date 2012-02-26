<?php
$AGversion = 'ajax-gizmo.php V1.13 - 22-Jan-2012 - Multilingual';
// Version 1.11 - 29-Nov-2011 - improved translations for conditions
// Version 1.12 - 13-Dec-2011 - fix am/pm-24hr time display for WeatherLink stations
// Version 1.13 - 22-Jan-2012 - fix for dd.mm.yyyy date handling
// Note: for Base-Canada/Base-USA/Base-World template users you need not customize anything here
//   all the settings come from Settings.php instead.
// --- settings --------------------------
$uomTemp = ' &deg;C';
$uomBaro = ' hPa';
$uomWind = ' kmh';
$uomRain = ' mm';
$uomPerHour = '/hr';
$imagesDir = './ajax-images/';  // directory for ajax-images with trailing slash
//  $timeFormat = 'D, d-M-Y g:ia T';  // Fri, 31-Mar-2006 6:35pm TZone
$timeFormat = 'd-M-Y g:ia';  // Fri, 31-Mar-2006 6:35pm TZone
$timeOnlyFormat = 'g:ia';    // h:mm[am|pm];
//$timeOnlyFormat = 'H:i';     // hh:mm
$dateOnlyFormat = 'd-M-Y';   // d-Mon-YYYY
$WDdateMDY = true;     // true=dates by WD are 'month/day/year'
//                     // false=dates by WD are 'day/month/year'
  $ourTZ = "America/Los_Angeles";  //NOTE: this *MUST* be set correctly to
// translate UTC times to your LOCAL time for the displays.
$WXtags  =  'testtags.php';  // source of our weather variables
$Lang = 'en'; // default language is English
// overrides from Settings.php if available
$commaDecimal = false;
global $SITE,$forwardTrans,$reverseTrans;
if (isset($SITE['lang'])) 	{$Lang = $SITE['lang'];}
if (isset($SITE['uomTemp']))         {$uomTemp = $SITE['uomTemp'];}
if (isset($SITE['uomBaro']))         {$uomBaro = $SITE['uomBaro'];}
if (isset($SITE['uomWind']))         {$uomWind = $SITE['uomWind'];}
if (isset($SITE['uomRain']))         {$uomRain = $SITE['uomRain'];}
if (isset($SITE['uomPerHour'])) {$uomPerHour = $SITE['uomPerHour'];}
if (isset($SITE['imagesDir']))         {$imagesDir = $SITE['imagesDir'];}
if (isset($SITE['timeFormat'])) {$timeFormat = $SITE['timeFormat'];}
if (isset($SITE['timeOnlyFormat'])) {$timeOnlyFormat = $SITE['timeOnlyFormat'];}
if (isset($SITE['dateOnlyFormat'])) {$dateOnlyFormat = $SITE['dateOnlyFormat'];}
if (isset($SITE['WDdateMDY']))  {$WDdateMDY = $SITE['WDdateMDY'];}
if (isset($SITE['tz']))                 {$ourTZ = $SITE['tz'];}
if (isset($SITE['WXtags']))         {$WXtags = $SITE['WXtags'];}
if (isset($SITE['commaDecimal'])) 	{$commaDecimal = $SITE['commaDecimal'];}
// --- end of settings -------------------

if (isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
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

if(isset($WXtags) and $WXtags <> '') {include_once($WXtags); }  // for the bulk of our data
 else {
	echo "<!-- ajax-gizmo not loaded .. no wxtags specified -->\n";
	echo "&nbsp;<br/>&nbsp;<br/>&nbsp;\n"; // needed as placeholder if no gizmo
	return;
 }
include_once("common.php"); // for language translation functions

# Set timezone in PHP5/PHP4 manner
  if (!function_exists('date_default_timezone_set')) {
	putenv("TZ=" . $ourTZ);
#	$Status .= "<!-- using putenv(\"TZ=$ourTZ\") -->\n";
    } else {
	date_default_timezone_set("$ourTZ");
#	$Status .= "<!-- using date_default_timezone_set(\"$ourTZ\") -->\n";
   }

//  $UpdateDate = date($timeFormat,strtotime("$date_year-$date_month-$date_day  $time_hour:$time_minute:00"));
//  $UpdateDate = "$date_year-$date_month-$date_day  $time_hour:$time_minute:00";
    $UpdateDate = gizmo_fixup_date($date) . ' ' . gizmo_fixup_time($time);
print "<!-- Lang='$Lang' -->\n";
if ($Lang <> 'en' and ! isset($SITE['langTransloaded']) ) { // do language settings for ajaxWDwx.js
	print '<script type="text/javascript">';
	if(count($forwardTrans)>0) {
	   print "// Language translation for conditions by ajaxWDwx.js for lang='$Lang'\n";
	   print "//\n";
	}
	foreach ($forwardTrans as $key => $val) {
	   print "  langTransLookup['$key'] = '$val';\n";
    }
	print "</script>\n";
	$SITE['langTransloaded'] = true;
}
$decimalComma = (strpos($temperature,',') !==false)?true:false; // using comma for decimal point?
$DebugMode = false;
if(isset($_REQUEST['debug']) and preg_match('|y|i',$_REQUEST['debug'])) {$DebugMode = true;}
// --- end of initialization code ---------
?>
<!-- <?php echo $AGversion; ?> -->
<div class="ajaxgizmo">
   <div class="doNotPrint">
	  <!-- ##### start of AJAX gizmo ##### -->
	    <noscript>[<?php langtrans('Enable JavaScript for live updates'); ?>]&nbsp;</noscript>
	    <span class="ajax" id="gizmoindicator"><?php langtrans('Updated'); ?></span>:&nbsp;
		<span class="ajax" id="gizmodate"><?php echo $UpdateDate;?></span>&nbsp; 
		<span class="ajax" id="gizmotime"></span>
		
	  <br/>&nbsp;<img src="<?php echo $imagesDir; ?>spacer.gif" height="14" width="1" alt=" " />
		<span class="ajaxcontent0" style="display: none">
		  <span class="ajax" id="gizmocurrentcond"><?php echo gizmo_fixupCondition($Currentsolardescription); ?></span>
		</span>
		<span class="ajaxcontent1" style="display: none"><?php langtrans('Temperature'); ?>: 
			<span class="ajax" id="gizmotemp"><?php echo gizmo_strip_units($temperature) . $uomTemp; ?></span>
            
	        <span class="ajax" id="gizmotemparrow"><?php
			if(isset($tempchangehour)) {
				   if($commaDecimal) {
					  $Tnow = preg_replace('|,|','.',gizmo_strip_units($temperature));
					  $TLast = preg_replace('|,|','.',gizmo_strip_units($tempchangehour)); 
				   } else {
					  $Tnow = gizmo_strip_units($temperature);
					  $TLast = gizmo_strip_units($tempchangehour); 
				   }
				   echo gizmo_gen_difference($Tnow, $Tnow - $TLast, '',
			       langtransstr('Warmer').' %s'. $uomTemp .' ' . langtransstr('than last hour').'.',
			       langtransstr('Colder').' %s'. $uomTemp .' ' . langtransstr('than last hour').'.');
			} ?>
			</span>&nbsp;
			<span class="ajax" id="gizmotemprate"><?php 
			if(isset($tempchangehour)) {
				echo $tempchangehour; 
			} ?></span> 
			<?php 
			   if(isset($tempchangehour)) { 
			     echo langtransstr($uomPerHour); 
               } // $tempchangehour ?>
		</span>
		<span class="ajaxcontent2" style="display: none"><?php langtrans('Humidity'); ?>: 
		  <span class="ajax" id="gizmohumidity"><?php 
			 echo $humidity; ?></span>%<?php
			 if(isset($humchangelasthour)) {
			   $t1 = preg_replace('|\s|s','',$humchangelasthour); 
			   if($commaDecimal) {
				  $Tnow = preg_replace('|,|','.',gizmo_strip_units($humidity));
				  $TLast = preg_replace('|,|','.',gizmo_strip_units($t1)); 
			   } else {
				  $Tnow = gizmo_strip_units($humidity);
				  $TLast = gizmo_strip_units($t1); 
			   }
					 
			   echo gizmo_gen_difference($Tnow, $Tnow-$TLast, '',
			   langtransstr('Increased').' %s%% ' . langtransstr('since last hour').'.',
			   langtransstr('Decreased').' %s%% ' . langtransstr('since last hour').'.');
			 } ?>
		</span>
		<span class="ajaxcontent3" style="display: none"><?php langtrans('Dew Point'); ?>: 
		  <span class="ajax" id="gizmodew"><?php 
			 $t1 = gizmo_strip_units($dewpt); 
			 echo $t1 . $uomTemp;  ?></span><?php 
			 if(isset($dewchangelasthour)) {
			  if($commaDecimal) {
				  $Tnow = preg_replace('|,|','.',gizmo_strip_units($dewpt));
				  $TLast = preg_replace('|,|','.',gizmo_strip_units($dewchangelasthour)); 
			   } else {
				  $Tnow = gizmo_strip_units($dewpt);
				  $TLast = gizmo_strip_units($dewchangelasthour); 
			   }
			   echo gizmo_gen_difference($Tnow, $Tnow-$TLast, '',
			   langtransstr('Increased').' %s'. $uomTemp .' ' . langtransstr('since last hour').'.',
			   langtransstr('Decreased').' %s'. $uomTemp .' ' . langtransstr('since last hour').'.'); 
			 } ?>
		</span>
		<span class="ajaxcontent4" style="display: none"><?php langtrans('Wind'); ?>: 
	    	<span class="ajax" id="gizmowindicon"></span> 
			<span class="ajax" id="gizmowinddir"><?php langtrans($dirlabel); ?></span>&nbsp; 
			<span class="ajax" id="gizmowind"><?php echo gizmo_strip_units($avgspd) . $uomWind; ?></span>
		</span>
		<span class="ajaxcontent5" style="display: none"><?php langtrans('Gust'); ?>: 
  			<span class="ajax" id="gizmogust"><?php echo gizmo_strip_units($gstspd) . $uomWind; ?></span>
		</span>
		<span class="ajaxcontent6" style="display: none"><?php langtrans('Barometer'); ?>: 
    		<span class="ajax" id="gizmobaro"><?php 
			 $t1 = gizmo_strip_units($baro); 
			 echo $t1 . " $uomBaro"; ?></span><?php
			 if(isset($trend)) {
			  if($commaDecimal) {
				  $Tnow = preg_replace('|,|','.',gizmo_strip_units($baro));
				  $TLast = preg_replace('|,|','.',gizmo_strip_units($trend)); 
			   } else {
				  $Tnow = gizmo_strip_units($baro);
				  $TLast = gizmo_strip_units($trend); 
			   }
			   $decPts = 1;
			   if(preg_match('|in|i',$uomBaro)) {$decPts = 2; }
			   echo gizmo_gen_difference($Tnow, $Tnow-$TLast, '',
			   langtransstr('Rising') . ' %s '. $uomBaro . langtransstr($uomPerHour),
			   langtransstr('Falling') . ' %s ' . $uomBaro . langtransstr($uomPerHour),$decPts ); 
			 } ?>&nbsp;
             <span class="ajax" id="gizmobarotrendtext"><?php langtrans($pressuretrendname); ?></span>			
		</span> 
		<span class="ajaxcontent7" style="display: none"><?php langtrans('Rain Today'); ?>: 
    		<span class="ajax" id="gizmorain"><?php 
			 echo gizmo_strip_units($dayrn).$uomRain; ?></span>
		</span>
		<span class="ajaxcontent8" style="display: none"><?php langtrans('UV Index'); ?>: 
           <span class="ajax" id="gizmouv"><?php echo $VPuv; ?></span>&nbsp;
		   <span style="color: #ffffff">
	         <span class="ajax" id="gizmouvword"><?php echo gizmo_get_UVrange($VPuv); ?></span>
		   </span>
		</span>
	  </div>
	  <!-- ##### end of AJAX gizmo  ##### -->

</div>
<!-- end of ajax-gizmo.php -->
<?php


//=========================================================================
//
// Functions
//
//=========================================================================
//  generate an up/down arrow to show differences

function gizmo_gen_difference( $nowTemp, $yesterTemp, $Legend, $textUP, $textDN, $DP=1) {
// version 1.01 - handle ',' as decimal point on input
  global $imagesDir,$commaDecimal,$DebugMode;
  if($commaDecimal) {
    $tnowTemp = preg_replace('|,|','.',gizmo_strip_units($nowTemp));
    $tyesterTemp = preg_replace('|,|','.',gizmo_strip_units($yesterTemp));
  } else {
	$tnowTemp = gizmo_strip_units($nowTemp);
	$tyesterTemp = gizmo_strip_units($yesterTemp);
  }
  $diff = round(($tnowTemp - $tyesterTemp),3);
  $absDiff = abs($diff);
  $diffStr = sprintf("%01.".$DP."F",$diff);
  $absDiffStr = sprintf("%01.".$DP."F",$absDiff);
  if($commaDecimal) {
	 $absDiffStr = preg_replace('|\.|',',',$absDiffStr);
  }
  if($DebugMode) {
	  echo "<!-- gen_difference DP=$DP now='$nowTemp':'$tnowTemp' yest='$yesterTemp':'$tyesterTemp' dif='$diff':'$diffStr' absDiff='$absDiff':'$absDiffStr' -->\n";
	  echo "<!-- txtUP='$textUP' txtDN='$textDN' Legend='$Legend' -->\n";
  }
  if ($diffStr == '0.0') {
 // no change

$image = '&nbsp;'; 
  
  } elseif ($diffStr > '0.0') {
// today is greater 
$msg = sprintf($textUP,$absDiffStr); 
$image = "<img src=\"${imagesDir}rising.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 1px 3px;\" />";

  
  } else {
// today is lesser
$msg = sprintf($textDN,$absDiffStr); 
$image = "<img src=\"${imagesDir}falling.gif\" alt=\"$msg\" title=\"$msg\" width=\"7\" height=\"8\" style=\"border: 0; margin: 1px 3px;\" />";
   
  }
   if ($Legend) {
       return ($diff . $Legend . $image);
	} else {
	   return $image;
	}
}

//=========================================================================
//  decode UV to word+color for display

function gizmo_get_UVrange ( $inUV ) {
// figure out a text value and color for UV exposure text
//  0 to 2  Low
//  3 to 5 	Moderate
//  6 to 7 	High
//  8 to 10 Very High
//  11+ 	Extreme
   if(strpos($inUV,',') !== false ) {
	   $uv = preg_replace('|,|','.',$inUV);
   } else {
	   $uv = $inUV;
   }
   switch (TRUE) {
     case ($uv == 0):
       $uv = langtransstr('None');
     break;
     case (($uv > 0) and ($uv < 3)):
       $uv = '<span style="border: solid 1px; color: black; background-color: #A4CE6a;">&nbsp;' . langtransstr('Low') . '&nbsp;</span>';
     break;
     case (($uv >= 3) and ($uv < 6)):
       $uv = '<span style="border: solid 1px; color: black; background-color: #FBEE09;">&nbsp;' . langtransstr('Medium') . '&nbsp;</span>';
     break;
     case (($uv >=6 ) and ($uv < 8)):
       $uv = '<span style="border: solid 1px; color: black; background-color: #FD9125;">&nbsp;' . langtransstr('High') . '&nbsp;</span>';
     break;
     case (($uv >=8 ) and ($uv < 11)):
       $uv = '<span style="border: solid 1px; color: #FFFFFF; background-color: #F63F37;">&nbsp;' . langtransstr('Very&nbsp;High') . '&nbsp;</span>';
     break;
     case (($uv >= 11) ):
       $uv = '<span style="border: solid 1px; color: #FFFF00; background-color: #807780;">&nbsp;' . langtransstr('Extreme') . '&nbsp;</span>';
     break;
   } // end switch
   return $uv;
} // end get_UVrange


//=========================================================================
// strip trailing units from a measurement
// i.e. '30.01 in. Hg' becomes '30.01'
function gizmo_strip_units ($data) {
  preg_match('/([\d\.\,\+\-]+)/',$data,$t);
  return $t[1];
}

// end of functions
//=========================================================================
// Function to process %Currentsolarcondition% string and 
// remove duplicate stuff, then fix capitalization, and translate from English if needed
//  
  function gizmo_fixupCondition( $inCond ) {
    global $DebugMode;
  
    $Cond = str_replace('_',' ',$inCond);
	$Cond = strtolower($Cond);
	$Cond = preg_replace('| -|','',$Cond);
	$Cond = trim($Cond);
	$dt = '';
	
	$vals = array();
	if(strpos($Cond,'/') !==false) {
		$dt .= "<!-- vals split on slash -->\n";
		$vals = explode("/",$Cond);
	}
	if(strpos($Cond,',') !==false) {
		$dt .= "<!-- vals split on comma -->\n";
		$vals = explode(",",$Cond);
	}
	$ocnt = count($vals);
	if($ocnt < 1) { return(langtransstr(trim($inCond))); }
	foreach ($vals as $k => $v) { 
	  if($DebugMode) { $dt .= "<!-- v='$v' -->\n"; }
	  $v = ucfirst(strtolower(trim($v)));
	  $vals[$k] = langtransstr($v); 
	  if($DebugMode) { $dt .= "<!-- vals[$k]='".$vals[$k]."' -->\n"; }
	}
	
	if($vals[0] == '') {$junk = array_shift($vals);}
	if(isset($vals[2]) and $vals[0] == $vals[2]) {$junk = array_pop($vals);}
	reset($vals);
	$t = join(', ',$vals);
	
//	return($Cond . "' orig=$ocnt n=" . count($vals) ." t='$t'");
    if($DebugMode) {
      $t = "<!-- gizmo_fixupCondition in='$inCond' out='$t' ocnt='$ocnt' -->" . $dt . $t;
	}
    return($t);
  
  
  
  }
//=========================================================================
// change the hh:mm AM/PM to h:mmam/pm format
function gizmo_fixup_time ( $WDtime ) {
  global $timeOnlyFormat,$DebugMode;
  if ($WDtime == "00:00: AM") { return ''; }
  $t = explode(':',$WDtime);
  if (preg_match('/p/i',$WDtime)) { $t[0] = $t[0] + 12; }
  if ($t[0] > 23) {$t[0] = 12; }
  if (preg_match('/^12.*a/i',$WDtime)) { $t[0] = 0; }
  $t2 = join(':',$t); // put time back to gether;
  $t2 = preg_replace('/[^\d\:]/is','',$t2); // strip out the am/pm if any
  $r = date($timeOnlyFormat , strtotime($t2));
  if ($DebugMode) {
    $r = "<!-- fixup_time WDtime='$WDtime' t2='$t2' -->" . $r;
    $r = '<span style="color: green;">' . $r . '</span>'; 
  }
  return ($r);
}

//=========================================================================
// adjust WD date to desired format
//
function gizmo_fixup_date ($WDdate) {
  global $timeFormat,$timeOnlyFormat,$dateOnlyFormat,$WDdateMDY,$DebugMode;
  $d = explode('/',$WDdate);      // expect ##/##/## form
  if(!isset($d[2])) {$d = explode('-',$WDdate); } // try ##-##-#### form instead
  if(!isset($d[2])) {$d = explode('.',$WDdate); } // try ##.##.#### form instead
  if ($d[2] > 70 and $d[2] <= 99) {$d[2] += 1900;} // 2 digit dates 70-99 are 1970-1999
  if ($d[2] < 99) {$d[2] += 2000; } // 2 digit dates (left) are assumed 20xx dates.
  if ($WDdateMDY) {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[0],$d[1]); //  M/D/YYYY -> YYYY-MM-DD
  } else {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[1],$d[0]); // D/M/YYYY -> YYYY-MM-DD
  }
  
  $r = date($dateOnlyFormat,strtotime($new));
  if ($DebugMode) {
    $r = "<!-- fixup_date WDdate='$WDdate', WDdateUSA='$WDdateMDY' new='$new' -->" . $r;
    $r = '<span style="color: green;">' . $r . '</span>'; 
  }
  return ($r);
}

?>