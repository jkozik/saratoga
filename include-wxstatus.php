<?php
############################################################################
#
# include-wxstatus.php
#
# Purpose: provide the stock checks for all supported weather software
#
#   Please DO NOT CHANGE this file .. add your custom checks to wxstatus.php instead
#   That will make it easy to just replace this file as new weather software 
#   is supported.
#
# Version - 1.00 - 20-Nov-2011 - initial release - bulk of wx software checks moved from wxstatus.php to here
# Version - 1.01 - 21-Nov-2011 - add check for Cumulus NOAA report (per Beau n9mfk9 ) and WD NOAA+month reports + some ML fixes
# Version - 1.02 - 14-Jan-2012 - add support for WeatherSnoop
# Version - 1.03 - 22-Jan-2012 - fix support for dd.mm.yy format dates in tags
#
############################################################################
$WXSIVer = 'include-wxstatus.php - V1.03 - 21-Jan-2012';
//--self downloader --
if(isset($_REQUEST['sce']) and strtolower($_REQUEST['sce']) == 'view') {
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
print "<!-- $WXSIVer -->\n";
// Standard checks for weather software updates
//
// check the appropriate realtime file first
  if($SITE['WXsoftware'] == 'WD' and isset($SITE['clientrawfile'])) {
	do_check($SITE['WXsoftwareLongName']." ".langtransstr("realtime"),$SITE['clientrawfile'],15,'file');
  }
  if($SITE['WXsoftware'] == 'MH' and isset($SITE['clientrawFile'])) {
	do_check($SITE['WXsoftwareLongName']." ".langtransstr("realtime"),$SITE['clientrawFile'],60+30,'file');
  }
  if($SITE['WXsoftware'] == 'VWS' and isset($SITE['wflashdir'])) {
	do_check($SITE['WXsoftwareLongName']." ".langtransstr("realtime"),$SITE['wflashdir'].'wflash.txt',15,'file');
  }
  if($SITE['WXsoftware'] == 'VWS' and isset($SITE['wflashdir'])) {
	do_check($SITE['WXsoftwareLongName']." ".langtransstr("realtime").' 2',$SITE['wflashdir'].'wflash2.txt',60+15,'file');
  }
  if($SITE['WXsoftware'] == 'CU' and isset($SITE['realtimefile'])) {
	do_check($SITE['WXsoftwareLongName']." ".langtransstr("realtime"),$SITE['realtimefile'],15,'file');
  }
  if($SITE['WXsoftware'] == 'WL' and isset($SITE['WLrealtime'])) {
	do_check($SITE['WXsoftwareLongName']." ".langtransstr("realtime"),$SITE['WLrealtime'],90,'file');  // weatherlink updates at 60 second intervals
  }
  
  // check the weather tags themselves
  if($SITE['WXsoftware'] <> 'WSN') { // Only WeatherSnoop does not upload the WXtags file 
    do_check($SITE['WXsoftwareLongName']." ".langtransstr("FTP"),$SITE['WXtags'],60*5+15,'file');
  }
  // check the internal date of the tags file
  $chkdate = fixup_date($date) . ' ' . fixup_time($time); // handle all formats to make standard
  print "<!-- ".$SITE['WXtags']." internal update date time='$date $time' fixed='$chkdate' -->\n";
  do_check($SITE['WXsoftwareLongName']." ".langtransstr("weather data"),strtotime($chkdate),60*5+15,'application');
  
  
  // checks for optional NOAA-style report updates
  if($SITE['WXsoftware'] == 'CU' and isset($WX['LatestNOAAMonthlyReport']) and 
		file_exists($SITE['NOAAdir'].$WX['LatestNOAAMonthlyReport'])) {
	do_check($SITE['WXsoftwareLongName']." ".langtransstr("NOAA report"),
	  $SITE['NOAAdir'].$WX['LatestNOAAMonthlyReport'],24*60*60+10*60,'file');
  }

  if($SITE['WXsoftware'] == 'VWS' and file_exists($SITE['NOAAdir'].'noaamo.txt')) {
	do_check($SITE['WXsoftwareLongName']." ".langtransstr("NOAA report"),
	  $SITE['NOAAdir'].'noaamo.txt',60*65+15,'file');
  }

  if($SITE['WXsoftware'] == 'WD' and 
		file_exists($SITE['HistoryFilesDir'].'dailynoaareport.htm')) {
	do_check($SITE['WXsoftwareLongName']." ".langtransstr("NOAA report"),
	  $SITE['HistoryFilesDir'].'dailynoaareport.htm',24*60*60+15*60,'file');
  }
  
  $MYYYYfile = date('FY') . '.htm'; // generates the <Monthname><YYYY>.htm filename used by WD
  if($SITE['WXsoftware'] == 'WD' and file_exists($SITE['HistoryFilesDir'].$MYYYYfile)) {
	do_check($SITE['WXsoftwareLongName']." ".langtransstr("Month report"),
	  $SITE['HistoryFilesDir'].$MYYYYfile,24*60*60+15*60,'file');
  }

  if($SITE['WXsoftware'] == 'WL' and file_exists($SITE['NOAACurDir'].'NOAAMO.TXT')) {
	do_check($SITE['WXsoftwareLongName']." ".langtransstr("NOAA report"),
	  $SITE['NOAACurDir'].'NOAAMO.TXT',60*5+15,'file');
  }

  // check for optional GR3 radar images (USA)
  if(isset($SITE['GR3DIR'])) {
	if (! isset($_SERVER['DOCUMENT_ROOT'] ) ) {
	   $path_trans = str_replace( '\\\\', '/', $_SERVER['PATH_TRANSLATED']);
	   $SELF = $_SERVER['PHP_SELF'];
	   $WEBROOT = substr($path_trans, 0, strlen($path_trans)-strlen($SELF) );
	} else {
	   $WEBROOT        = $_SERVER['DOCUMENT_ROOT'];
	}
	$path = realpath($WEBROOT . $SITE['GR3DIR'] . '/' ) . '/';
	$GR3file = $path . $SITE['GR3radar'] . "_" . $SITE['GR3type'] . "_0." . $SITE['GR3img'];
	print "<!-- GR3='$GR3file' -->\n";
	if(file_exists($GR3file)) {
		if(file_exists("radar-status.php")) { // add radar status check if available
		   echo "	<tr>\n";
		   echo "	  <td colspan=\"4\">\n";
		   include_once("radar-status.php");
		   echo "</td>\n";
		   echo "	</tr>\n";
		}
	
	   do_check(langtransstr("GRLevel3 Radar FTP"),$GR3file,60*10+15,'file');
	}
  } // end GR3 checking
  
  // check for optional WXSIM forecast
  if(isset($SITE['WXSIM']) and file_exists('plaintext.txt')) {
	 $doPrint = false;
	 include_once($SITE['WXSIMscript']);
	 print "<!-- wdate='$wdate' -->\n";
	 $lastWXSIM = strtotime($wdate);
	 // note: '6*60*60 + 2*60' is 6:02:00 hms
	 do_check(langtransstr("WXSIM forecast"),$lastWXSIM,6*60*60 + 2*60,'application');
  
  }
  
  // check for optional Boltek/Nexstorm files
  $nxDir = './';
  if(isset($SITE['nexstormDir'])) {$nxDir = $SITE['nexstormDir']; }
  
  if(file_exists($nxDir.'nexstorm.jpg')) {
	 do_check(langtransstr("Nexstorm Lightning map"),$nxDir.'nexstorm.jpg',10*60+15,'file');
  
  }
  if(file_exists($nxDir.'TRACReport.txt')) {
	 do_check(langtransstr("Nexstorm TRACreport"),$nxDir.'TRACReport.txt',10*60+15,'file');
  
  }
  if(file_exists($nxDir.'nexstorm_arc.dat')) {
	 do_check(langtransstr("Nexstorm Data file"),$nxDir.'nexstorm_arc.dat',10*60+15,'file');
  
  }
 
print "<!-- end include-wxstatus.php -->\n";

// Functions used on this page

function do_check($title, $fileOrAppTime,$maxFileSecs,$type='file') {
	if(preg_match('/file/i',$type)) {
	   $cur_status = do_check_file($fileOrAppTime,$maxFileSecs);
	} else {
	   $cur_status = do_check_applic($fileOrAppTime,$maxFileSecs);
	}
	 list($stat,$age,$data) = explode("\t",$cur_status);
echo "    <tr>
      <td>$title</td>
      <td align=\"center\">$stat</td>
      <td align=\"right\">$age</td>
      <td align=\"right\">$data</td>
    </tr>\n";
	
}
// check time on a file for last update
function do_check_file($chkfile,$maxFileSecs) {
  global $SITE;
  $timeFormat = $SITE['timeFormat']; 
  $now = time();
  if (file_exists($chkfile)) {
    $age = $now - filemtime($chkfile);
	$updateTime = date($timeFormat,filemtime($chkfile));
  } else {
    $age = 'unknown';
	$updateTime = 'file not found';
  }
  $status = '';
  $hms = sec2hms($age);
  print "<!-- $chkfile age = $age secs. $hms -->\n";
  $age = $hms;
  if (file_exists($chkfile) and (filemtime($chkfile) + $maxFileSecs > $now) ) {     // stale file
    $status = "<span style=\"color: green\"><b>".langtransstr('Current')."</b></span>\t$age\t$updateTime ";

  } else {
    $status = "<span style=\"color: red\"><b>".langtransstr('NOT Current')."</b></span>\t$age\t > " . sec2hms($maxFileSecs) ."<br/><b>$updateTime</b>";
  }
  
  return($status);


}
// check time on an application returned update time
function do_check_applic($applTime,$maxFileSecs) {
  global $SITE;
  $timeFormat = $SITE['timeFormat']; 
  $now = time();
    $age = $now - $applTime;
	$updateTime = date($timeFormat,$applTime);
  $status = '';
  $hms = sec2hms($age);
  
  print "<!-- age = $age secs. $hms -->\n";
  $age=$hms;
  if ($applTime + $maxFileSecs > $now ) {     // stale file
    $status = "<span style=\"color: green\"><b>".langtransstr('Current')."</b></span>\t$age\t$updateTime ";

  } else {
    $status = "<span style=\"color: red\"><b>".langtransstr('NOT Current')."</b></span>\t$age\t > " . sec2hms($maxFileSecs) . " <br/><b>$updateTime</b>";
  }
  
  return($status);


}	
//
  function sec2hms ($sec, $padHours = false) 
  {

    // holds formatted string
    $hms = "";
    if (! is_numeric($sec)) { return($sec); }
    // there are 3600 seconds in an hour, so if we
    // divide total seconds by 3600 and throw away
    // the remainder, we've got the number of hours
    $hours = intval(intval($sec) / 3600); 

    // add to $hms, with a leading 0 if asked for
    $hms .= ($padHours) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
          : $hours. ':';
     
    // dividing the total seconds by 60 will give us
    // the number of minutes, but we're interested in 
    // minutes past the hour: to get that, we need to 
    // divide by 60 again and keep the remainder
    $minutes = intval(($sec / 60) % 60); 

    // then add to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

    // seconds are simple - just divide the total
    // seconds by 60 and keep the remainder
    $seconds = intval($sec % 60); 

    // add to $hms, again with a leading 0 if needed
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    // done!
    return $hms;
    
  }
//=========================================================================
// change the hh:mm AM/PM to h:mmam/pm format
function fixup_time ( $WDtime ) {
  global $timeOnlyFormat,$DebugMode;
  if ($WDtime == "00:00: AM") { return ''; }
  $t = explode(':',$WDtime);
  if (preg_match('/p/i',$WDtime)) { $t[0] = $t[0] + 12; }
  if ($t[0] > 23) {$t[0] = 12; }
  if (preg_match('/^12.*a/i',$WDtime)) { $t[0] = 0; }
  $t2 = join(':',$t); // put time back to gether;
  $t2 = preg_replace('/[^\d\:]/is','',$t2); // strip out the am/pm if any
  return ($t2);
}

//=========================================================================
// adjust date to standard format
//
function fixup_date ($WDdate) {
  global $SITE;
  $d = explode('/',$WDdate);
  if(!isset($d[2])) {$d=explode("-",$WDdate); }
  if(!isset($d[2])) {$d=explode(".",$WDdate); }
  if ($d[2] > 70 and $d[2] <= 99) {$d[2] += 1900;} // 2 digit dates 70-99 are 1970-1999
  if ($d[2] < 99) {$d[2] += 2000; } // 2 digit dates (left) are assumed 20xx dates.
  if ($SITE['WDdateMDY']) {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[0],$d[1]); //  M/D/YYYY -> YYYY-MM-DD
  } else {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[1],$d[0]); // D/M/YYYY -> YYYY-MM-DD
  }
  
  return($new);
}

// end of functions
?>
