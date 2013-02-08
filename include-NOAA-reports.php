<?php
//------------------------------------------------
// safely display NOAA style text report files
// Author:  Ken True - webmaster@saratoga-weather.org
// Version: 1.02  12-Mar-2006 - Initial release
// Version: 1.03  13-Aug-2006 - added downloader and include capability
// Version: 1.04  01-Dec-2007 - added WL auto update for prior month/year rollover
// initialize request variables
// Version: 1.05  01-Jan-2008 - added timezone to fix year rollover issue
// Version: 1.06  03-Oct-2008 - added variables for WL LastMonth/LastYear files
// Version  2.00  15-Jan-2011 - adapted for use with template sets
// Version 2.00 - 27-Jan-2011 - modified for multilingual and new wx templates
// Version 2.01 - 11-Jun-2011 - fixed Notice: errata in script
// Version 2.02 - 27-Aug-2011 - added Cumulus support (thanks to beteljuice at http://www.beteljuice.com/ )
// Version 2.03 - 03-Dec-2011 - fixed WeatherLink auto file copy for start of month/year, added RTL language support
// Version 2.04 - 03-Jul-2012 - added wview support
// Version 2.05 - 02-Jan-2013 - fixed 01-Jan display issues
// Version 2.06 - 12-Jan-2013 - fixed Cumulus issues with non-NOAA files in the $SITE['NOAAdir'] directory
$NOAAversion = "include-noaa-reports.php - Version 2.06 - 12-Jan-2012";
	
if ( isset($_REQUEST['sce']) and strtolower($_REQUEST['sce']) == 'view' ) {
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

// NOAA-reports.php
// Author:  Ken True  webmaster@saratoga-weather.org
// 12-Mar-2006
//
//
// Permission is given to freely use and/or modify this script for
// private, non-commercial usage.
// No warranty is expressed or implied .. use at your own risk.
//

// Note:  WeatherLink conventions:
//    NOAAMO.TXT, NOAAYR.TXT  for current month and current year
//    NOAAPRMO.TXT, NOAAPRYR.TXT for last month, last year
//    NOAAyyyy.TXT for year 'yyyy' summary
//    NOAAyyyy-mm.TXT for month 'mm' in year 'yyyy' summary
//
// VWS naming format
//    noaamo.txt,noaayr.txt  for current month and current year
//    yyyy.txt  for year 'yyyy' summary
//    yyyy_mm.txt for month 'mm', year 'yyyy' summary.
//
//
// Cumulus naming format
//    NOAAMOmmyy.txt for month  (e.g. NOAA0811.txt for August, 2011 monthly report)
//    NOAAYRyyyy.txt for year   (e.g. NOAA2011.txt for 2011 yearly report)
//
// overrides from Settings.php if available
global $SITE;
if (isset($SITE['tz'])) 	{$ourTZ = $SITE['tz'];}
if (isset($SITE['NOAAdir'])) 	    {$NOAAdir = $SITE['NOAAdir'];}
if (isset($SITE['NOAACurDir'])) 	{$NOAACurDir = $SITE['NOAACurDir'];}
if (isset($SITE['WXsoftware'])) 	{$Naming = $SITE['WXsoftware'];}  // from the [$SITE['WXsoftware']]-defs.php file
// end of overrides from Settings.php
$doDebug = (isset($_REQUEST['debug']))?true:false;

$validWX = false;
if ($Naming == 'WL') {
  $ThisYearFile = $NOAACurDir."NOAAYR.TXT"; // point to your current NOAA yearly file
  $ThisMonthFile= $NOAACurDir."NOAAMO.TXT"; // point to your current NOAA monthly file
  $LastYearFile = $NOAACurDir."NOAAPRYR.TXT"; // point to your prior NOAA yearly file
  $LastMonthFile= $NOAACurDir."NOAAPRMO.TXT"; // point to your prior NOAA monthly file
  $validWX = true;
}
if ($Naming == 'VWS') {
  $ThisYearFile = $NOAAdir."noaayr.txt"; // point to your current NOAA yearly file
  $ThisMonthFile= $NOAAdir."noaamo.txt"; // point to your current NOAA monthly file
  $validWX = true;
}

if ($Naming == 'CU') {
  $cu_year = "";       // initialise Cumulus year file
  $cu_month = "";      // initialise Cumulus month file
  $cu_month_year = ""; 
  $CU_year = array();  // storage to find first/last year reports on file
  $validWX = true;
// have to wait to set ThisYearFile, ThisMonthFile until after the default timezone is set.
}

if ($Naming == 'WV') {
  $validWX = true;
}

print "<!-- $NOAAversion -->\n";
$WXparm = '';
if(isset($_REQUEST['wx'])) {
	$WXparm = '&amp;wx='.$Naming;
	print "<!-- testing with '$WXparm' in use -->\n";
}
if(!$validWX) {
	print "<p>NOAA-style climate reports not available.</p>\n";
	print "<!-- Naming='$Naming' not supported wx software type for NOAA-style reports -->\n";
	return;
}
if (!isset($PHP_SELF)) {$PHP_SELF = $_SERVER['SCRIPT_NAME']; }
# Set timezone in PHP5/PHP4 manner
  if (!function_exists('date_default_timezone_set')) {
	putenv("TZ=" . $ourTZ);
#	$Status .= "<!-- using putenv(\"TZ=$ourTZ\") -->\n";
    } else {
	date_default_timezone_set("$ourTZ");
#	$Status .= "<!-- using date_default_timezone_set(\"$ourTZ\") -->\n";
}
$yr = 	(isset($_REQUEST['yr']) and preg_match('|^\d{4}$|',$_REQUEST['yr'])) ? $_REQUEST['yr'] : '';

$mo =   (isset($_REQUEST['mo']) and preg_match('|^\d{2}$|',$_REQUEST['mo'])) ? $_REQUEST['mo'] : '';


$now = getdate();
// print "<!-- now \n" . print_r($now,true) . " -->\n";
$now_month = sprintf("%02d",$now['mon']);
$now_year = $now['year'];
$now_hour = $now['hours'];
$prior_month = $now['mon'] - 1;
$prior_year = $now['year'];
$last_year = $prior_year -1;
if ($prior_month < 1) {$prior_month = 12; $prior_year--;}
$prior_month = sprintf("%02d",$prior_month);

if ($Naming == 'CU') {
// have to set ThisYearFile, ThisMonthFile now we know the date.
  $ThisYearFile = $NOAAdir."NOAAYR${now_year}.txt"; // points to your current NOAA yearly file
  $ThisMonthFile= $NOAAdir."NOAAMO${now_month}".substr($now_year,2,2).".txt"; // points to your current NOAA monthly file
  if(!file_exists($ThisMonthFile)) {
	  $ThisMonthFile = $NOAAdir."NOAAMO${prior_month}".substr($prior_year,2,2).".txt";
  }
}
if ($Naming == 'WV') {
// have to set ThisYearFile, ThisMonthFile now we know the date.
  $ThisYearFile = $NOAAdir."NOAA-${now_year}.txt"; // points to your current NOAA yearly file
  $ThisMonthFile= $NOAAdir."NOAA-${now_year}-${now_month}.txt"; // points to your current NOAA monthly file
  if(!file_exists($ThisMonthFile)) {
	  $ThisMonthFile = $NOAAdir."NOAA-${prior_year}-${prior_month}.txt";
  }
}


print "<!-- parms: yr='$yr' mo='$mo' -->\n";
print "<!-- now='$now_year'-'$now_month' prior='$prior_year'-'$prior_month' -->\n";
print "<!-- Naming='$Naming' thisYearFile='$ThisYearFile' thisMonthFile='$ThisMonthFile' -->\n";

if ($Naming == 'WL') { // check for month/year rollover and update

  if(! file_exists("$NOAAdir/NOAA$prior_year-$prior_month.TXT") and
       file_exists($LastMonthFile) and 
	   $now_hour >= 6) {
	   print "<!-- copying $LastMonthFile to $NOAAdir/NOAA$prior_year-$prior_month.TXT -->\n";
	   if (copy($LastMonthFile,"$NOAAdir/NOAA$prior_year-$prior_month.TXT")) {
	     print "<!-- copy successful -->\n";
	   } else {
	     print "<!-- unable to copy -->\n";
	   }
   }

  if(! file_exists("$NOAAdir/NOAA$last_year.TXT") and
       file_exists($LastYearFile) and 
	   $now_hour >= 6 ) {
	   print "<!-- copying $LastYearFile to $NOAAdir/NOAA$last_year.TXT -->\n";
	   if (copy($LastYearFile,"$NOAAdir/NOAA$last_year.TXT")) {
	     print "<!-- copy successful -->\n";
	   } else {
	     print "<!-- unable to copy -->\n";
	   }

   }
}

$months = array();
$longmonths = array();

$fullmonths = array( "01"=>"January", "02"=>"February", "03"=>"March",
         "04"=>"April", "05"=>"May", "06"=>"June",
		 "07"=>"July", "08"=>"August", "09"=>"September",
		 "10"=>"October", "11"=>"November", "12"=>"December"
	   );
$RTLlang = (isset($SITE['RTL-LANG']) and strpos($SITE['RTL-LANG'],$SITE['lang']) !== false)?true:false;
foreach ($fullmonths as $m => $n) {
	$longmonths[$m] = langtransstr($n);         // support translation of month names
	if(!$RTLlang) {
	  $months[$m] = substr($longmonths[$m],0,3);  // support translations of abbrevoated month names
	} else {
	  $months[$m] = $longmonths[$m];  // use full month names for RTL languages
	}
}
$longmonths['unk'] = '';
/*
$months = array( "01"=>"Jan", "02"=>"Feb", "03"=>"Mar",
         "04"=>"Apr", "05"=>"May", "06"=>"Jun",
		 "07"=>"Jul", "08"=>"Aug", "09"=>"Sep",
		 "10"=>"Oct", "11"=>"Nov", "12"=>"Dec"
	   );
*/
if ($handle = opendir("$NOAAdir")) { 
   while (false !== ($file = readdir($handle))) { 
       if ($file != "." && $file != "..") { 
              $files[] = $file; 
        } 
   } 
  closedir($handle); 
   
  sort($files);
  if($doDebug) { print "<!-- files from '$NOAAdir' \n".print_r($files,true)." -->\n"; }

  $lastyear = '';
//  echo "<pre style=\"font-size: 9pt;\">\n";
  // set up the files and find the first and last year for reports
  $first_year = '';
 
  foreach ($files as $key => $file) {
	if ($Naming == 'WL') {
       $year = substr($file,4,4);
	} elseif ($Naming == 'VWS') {
       $year = substr($file,0,4);
	} elseif ($Naming == 'WV') {
		$year = substr($file,5,4);
	} elseif ($Naming == 'CU') {
		if (substr($file, 4, 2) == "MO") { // found a month report
			$year = "20" .substr($file, 8, 2);
		} else { // found a year report
			$year = substr($file, 6, 4);
		}
	}
   if($doDebug) { print "<!-- file='$file' year='$year' -->\n"; }
   if (! preg_match("/^\d{4}$/",$year) ) { continue;} //make sure year is numeric
   if ($Naming == 'CU') { $CU_year[] = $year;}
    
    $filesfound["$file"] = 1;
    if (!$first_year && substr($file, 4, 2) != "MO") { // everything EXCEPT Cumulus
		$first_year = $year; 
	}
    $last_year = $year;
    if($doDebug) { print "<!-- first_year='$first_year' last_year='$last_year' -->\n"; }
  }  // END foreach

  if ($Naming == "CU") { // find first and last valid years
		$first_year = min($CU_year);
		$last_year = max($CU_year); // this doesn't seem to be used !
        if($doDebug) {print "<!--Cumulus results: first_year='$first_year' last_year='$last_year' -->\n"; }
		
  }

 if($doDebug) { print "<!-- filesfound\n".print_r($filesfound,true)."-->\n"; }
 print "<!-- first_year='$first_year' last_year='$last_year' now_year='$now_year' -->\n";
 
 if (! $first_year) {
   echo "<h2>".langtransstr('No NOAA-style climate reports found').".</h2>\n";
   return;
 
 }
 
// new - css classes and some html added below - beteljuice  
// start building page - new, beteljuice
  echo "<div class=\"noaa_rep_container\">
	<div class=\"noaa_rep_nav_container\">\n";
  echo langtransstr("Select a Year or Month report")."<br />\n";

// now create the index links based on which files we have

//  for ($y = $first_year; $y <= $now_year; $y++) {
// above will do ascending sort by year in index 
// but we like the Index links sorted in descending year so
//   newest reports are at the top of the list
//

 for ($y = $now_year; $y >= $first_year; $y--) {
 
   $yy = sprintf("%04d",$y);
   if ($Naming == 'WL')  {$t = "NOAA$yy.TXT"; }
   if ($Naming == 'VWS') {$t = "$yy.txt"; }
   if ($Naming == 'CU')  {$t = "NOAAYR$yy.txt"; }
   if ($Naming == 'WV')  {$t = "NOAA-$yy.txt"; }
   
//   if (isset($filesfound[$t]) || $yy == $now_year) {
   if (isset($filesfound[$t]) || (isset($ThisYearFile) and file_exists($ThisYearFile))) {
      echo "<a href=\"$PHP_SELF?yr=$yy$WXparm\" class=\"noaa_rep_nav\"><b>$yy</b></a>:";
	} else {
	  echo "<span class=\"noaa_rep_nav\"><b>$yy</b></span>:";
   }

   for ($m = 1; $m <= 12; $m++) {
     $mm = sprintf("%02d",$m);
	 if ($Naming == 'WL')  {$testfile = "NOAA$yy-$mm.TXT";}
	 if ($Naming == 'VWS') {$testfile = "$yy" . "_" . "$mm.txt"; }
	 if ($Naming == 'CU') {$testfile = "NOAAMO$mm" .substr($yy, 2,2). ".txt"; }
	 if ($Naming == 'WV') {$testfile = "NOAA-$yy-$mm.txt"; }
	 
	 if (isset($filesfound[$testfile])) {
	   echo " <a href=\"$PHP_SELF?yr=$yy&amp;mo=$mm$WXparm\" class=\"noaa_rep_nav\"><b>" . $months[$mm]. "</b></a>";
	   if ($Naming == 'CU') {
			if (!$cu_month) { // first valid month in latest valid year
				$cu_month = $mm; 
				$cu_month_year = $yy; 
			} else { // update last valid month in latest valid year
				if ($cu_month_year == $yy) { $cu_month = $mm; }
			}
	   }
     } else {
	   if ($yy == $now_year && $mm == $now_month and (file_exists($NOAAdir.$testfile) or $Naming == 'WL')) {
	      echo  " <a href=\"$PHP_SELF?now$WXparm\" class=\"noaa_rep_nav\"><b>" . $months[$mm] ."</b></a>";
	   } else {
	      echo  " <span class=\"noaa_rep_nav\"><b>" . $months[$mm] ."</b></span>";
	   }
	 }
	 if ($m < 12) {
       // echo "| ";
	 } else {
	   echo "\n";
	 }
	} // END month step through
	
	if($y != $first_year) { echo "<br />\n"; } // new, not sure how it worked without it - beteljuice
	
 } // END year step through
 echo "<br />\n";

 echo "	</div> <!-- END noaa_rep_nav_container -->\n";
 
// Now give out the reports

  if ($Naming == "CU" && !$yr && !$mo) { // show default latest Cumulus report
	  if ($cu_month_year >= $cu_year) { // month report newer than year report
		  $yr = $cu_month_year;
		  $mo = $cu_month;
	  } else { // year report is newer than month report
		  $yr = $cu_yr;
		  $mo = "";
	  }
  }

$BIDI = $RTLlang?' style="unicode-bidi: bidi-override;direction: ltr;"':'';
 if (! $yr && ! $mo && $Naming != "CU" && $Naming != "WV") {  // special for 'current month'
    echo "<br /><b>".langtransstr('Report for')." $now_year ".$longmonths[$now_month]."</b>\n";
    $rpt = implode("",file("$ThisMonthFile"));
	echo "<br />\n<pre$BIDI>\n";
	$rpt = preg_replace('|<|Uis','&lt;',$rpt);
	$rpt = preg_replace('|院Uis','&deg;',$rpt);
	echo $rpt;
  }
 if (! $yr && ! $mo && $Naming == "WV") {  // special for 'current month' and wview
    preg_match('|NOAA-(\d{4})-(\d{2})\.txt|i',$ThisMonthFile,$matches);
	print "<!-- WV-matches\n".print_r($matches,true)."-->\n";
	
	if(isset($matches[2])) {
		$now_year = $matches[1];
		$now_month = $matches[2];
	}
    echo "<br /><b>".langtransstr('Report for')." $now_year ".$longmonths[$now_month]."</b>\n";
    $rpt = implode("",file("$ThisMonthFile"));
	echo "<br />\n<pre$BIDI>\n";
	$rpt = preg_replace('|<|Uis','&lt;',$rpt);
	$rpt = preg_replace('|院Uis','&deg;',$rpt);
	echo $rpt;
  }

 if ($yr == $now_year && ! $mo && $Naming == 'WL') { // special for current year
    echo "<br /><b>".langtransstr('Report for')." $now_year</b>\n";
    $rpt = implode("",file("$ThisYearFile"));
    echo "<br />\n<pre$BIDI>\n";
	$rpt = preg_replace('|<|Uis','&lt;',$rpt);
	$rpt = preg_replace('|>|Uis','&gt;',$rpt);
	$rpt = preg_replace('|院Uis','&deg;',$rpt);
	echo $rpt;
  } else { // otherwise, process the requested year or year/month

   if ($yr) {
      if ($mo) { // month given
	    if ($Naming == 'WL')  {$testfile = "NOAA$yr-$mo.TXT";}
		if ($Naming == 'VWS') {$testfile = "$yr" . "_" . "$mo.txt";}
		if ($Naming == 'CU')  {$testfile = "NOAAMO$mo" .substr($yr, 2,2). ".txt";}
		if ($Naming == 'WV')  {$testfile = "NOAA-$yr-$mo.txt";}
	  } else { // no month given
	    if ($Naming == 'WL')  {$testfile = "NOAA$yr.TXT";}
		if ($Naming == 'VWS') {$testfile = "$yr.txt";}
		if ($Naming == 'CU')  {$testfile = "NOAAYR$yr.txt";}
		if ($Naming == 'WV')  {$testfile = "NOAA-$yr.txt";}
	  } // no month given
     if (isset($filesfound[$testfile])) {
        echo "<br /><b>".langtransstr('Report for')." $yr ";
		echo isset($longmonths[$mo])?$longmonths[$mo]:'';
		echo "</b>\n";
	    $rpt = implode("",file("$NOAAdir/$testfile"));
  	     echo "<br />\n<pre$BIDI>\n";
		$rpt = preg_replace('|<|Uis','&lt;',$rpt);
		$rpt = preg_replace('|>|Uis','&gt;',$rpt);
		$rpt = preg_replace('|院Uis','&deg;',$rpt);
	     echo $rpt;

	  } else {
		if(!$mo) { $mo='unk'; }
	    echo "<br /><b>".langtransstr('No report for')." $yr ".$longmonths[$mo].".</b><br /><br />\n";
      }
   }
 }
 
} 
echo "</pre>\n";
echo "</div> <!-- END noaa_rep_container -->\n";
// echo "------- debugging -----\n";
// print_r($filesfound);
?>
