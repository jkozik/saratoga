<?php
$Version = "check-fetch-times.php Version 1.10 - 28-Nov-2012";
/*
Utility diagnostic script to support the Saratoga-Weather.org AJAX/PHP template sets.

Author: Ken True - webmaster@saratoga-weather.org

Note: there are no user customizations expected in this utility.  Please replace the
  entire script with a newer version when available.

*/
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
error_reporting(E_ALL);
// ------------------------------------------------------------------
if(isset($_REQUEST['show']) and preg_match('|settings|i',$_REQUEST['show'])) {
	$toShow = array("Settings.php","Settings-weather.php","Settings-language.php");
	$doneHeaders = false;
	$doHighlight = preg_match('|settingsr|i',$_REQUEST['show'])?false:true;
	foreach ($toShow as $n => $showFilename) {
	  if(!$doneHeaders) { 
	    printHeaders(); 
		$doneHeaders = true; 
	    print "<h2>Contents of Settings files</h2>\n";
	  }
	  if(file_exists($showFilename)) {
		  print "<h3>$showFilename</h3>\n";
		  print "<pre style=\"border: 1px solid black;\">\n";
		  if($doHighlight) { 
		    highlight_file_num($showFilename);
		  } else {
			$flines = file($showFilename);
			$num = 1;
			foreach ($flines as $n => $line) {
				$line = preg_replace('|<\?php|i','<&#63;php',$line);
				$pnum = sprintf('%6d',$num);
				print "$pnum:\t$line";
				$num++;
			}
		  }
		  print "</pre>\n<hr/>\n";
	  } else {
		  // print "<h2>$showFilename is not found.</h2>\n<hr/>\n";
	  }
	}
   if($doneHeaders) {
	  print "  </body>\n";
	  print "</html>\n";
   }
	
   exit;
}
// ------------------------------------------------------------------

if(isset($_REQUEST['show']) and strtolower($_REQUEST['show']) == 'info') {
  if(file_exists("Settings.php")) {
	include_once("Settings.php");
  }
# set the Timezone abbreviation automatically based on $SITE['tzname'];
# Set timezone in PHP5/PHP4 manner
  if (!function_exists('date_default_timezone_set')) {
	 putenv("TZ=" . $SITE['tz']);
//	 print "<!-- using putenv(\"TZ=". $SITE['tz']. "\") -->\n";
    } else {
	 date_default_timezone_set($SITE['tz']);
//	 print "<!-- using date_default_timezone_set(\"". $SITE['tz']. "\") -->\n";
   }
  printHeaders(); 
  print "<h2>Key PHP information</h2>\n";
  print "<p>\n";
	print "Webserver OS: <b>".php_uname()."</b><br/>\n";
	print "PHP Version: <b>".phpversion()."</b><br/>\n";
	print "Document root: <b>".$_SERVER['DOCUMENT_ROOT']."</b><br/>\n";
	print "allow_url_fopen = <b>";
	print ini_get("allow_url_fopen")?"ON":"off";
	print "</b><br/>\n";
	print "allow_url_include = <b>";
	print ini_get("allow_url_include")?"ON":"off";
	print "</b></p>\n";

	$toCheck = array('simplexml_load_file','iconv','json_decode',
	                 'curl_init','curl_setopt','curl_exec','curl_error','curl_close');

	print "<p>Status of needed built-in PHP functions:<br/>\n";

	foreach ($toCheck as $n => $chkName) {
		print "function <b>$chkName</b> ";
		if(function_exists($chkName)) {
			print " is available<br/>\n";
		} else {
			print " is <b>NOT available</b><br/>\n";
		}
		
	}
  print "</p>\n";
  	
  print "<p>Current GD status:</p>\n";
  echo describeGDdyn();

  if(!file_exists("Settings.php")) {
	$settingsLoad = "Unable to find Settings.php.. directory testing skipped.\n";
	print $settingsLoad;
	print "  </body>\n";
	print "</html>\n";
	  
	exit;
  }
  
  $Base = '';
  $WXdirs = array( // specific directories from Settings-weather.php $SITE and files to check
    'CU' => array('graphImageDir'=> 'temp.png','NOAAdir' => 'NOAAYRyyyy.txt'),
	'MH' => array('graphImageDir'=> 'tdpb2day.png'),
	'VWS'=> array('graphImageDir'=> 'vws742.jpg', 'NOAAdir'=> 'noaayr.txt'),
	'WCT'=> array('graphImageDir'=> 'temperature1.jpg'),
	'WD' => array('graphImageDir'=> 'curr24hourgraph.gif','HistoryFilesDir'=>'MONTHyyyy.htm',
	              'HistoryGraphsDir'=>'yyyymmdd.gif'),
    'WL' => array('graphImageDir'=> 'OutsideTempHistory.gif','NOAACurDir'=>'NOAAMO.TXT'),
//	'WSN' => array('graphImageDir'=> '?????'),  // no graphs for WeatherSnoop
	'WV'=> array('graphImageDir'=> 'tempdaycomp.png', 'NOAAdir'=> 'NOAA-yyyy.txt'),
//	'WXS' => array('graphImageDir'=> '?????'),  // no graphs for WXSolution
	
  
  );
  
  if(isset($SITE['fcsturlNWS']) or isset($SITE['NWSforecasts'])) {
	  $Base = 'USA';
  }
  if(isset($SITE['fcsturlEC']) or isset($SITE['ecradar'])) {
	  $Base = 'Canada';
  }

  if(isset($SITE['EUwarningURL']) or isset($SITE['fcsturlWU']) or isset($SITE['WUforecasts'])) {
	  $Base = 'World';
  }
  
  if(isset($SITE['WXsoftware']) ){
	  $wxsftw=$SITE['WXsoftware'];
  } else {
	  $wxsftw = 'N/A';
  }
  print "<h2>Directories/files status for Base-$Base, $wxsftw-Plugin</h2>\n";
  print "<p>Status of needed subdirectories<br/>\n";
  print "Settings.php <b>Cache file directory</b> in \$SITE['cacheFileDir']='<b>".$SITE['cacheFileDir']. "</b>' ";
  if(is_dir($SITE['cacheFileDir'])) {
	  $perms = fileperms($SITE['cacheFileDir']);
      $permsdecoded = decode_permissions($perms);
	  $permsoctal = substr(sprintf('%o', $perms), -4);
	  print " exists, with permissions=$permsdecoded [$permsoctal]<br/>\n";
	  $tfile = $SITE['cacheFileDir'] . "test.txt";
	  $tstring = "Test of cache directory file create and write by $Version.<br/>\n";
	  $fp = fopen($tfile,'w');
	  if($fp) {
		$write = fputs($fp, $tstring); 
		fclose($fp);
		print "..Wrote ".strlen($tstring). " bytes to $tfile successfully, ";
		$deleted = unlink($tfile);
		print $deleted?"then deleted test file. <b>Cache directory is fully functional</b>.<br/>\n":" but unable to delete $tfile so <b>Cache directory is NOT fully functional</b>.<br/>\n";
	  } else {
		print "<br/><b>Error: Unable to open/write to $tfile file</b> so so <b>Cache directory is NOT fully functional</b>.<br/>\n";
	  }
  } else {
	  print "<b>does not exist</b> so some scripts will be <b>not functional</b>.<br/>\n";
  }

  print "Settings.php <b>ajax-images file directory</b> in \$SITE['imagesDir']='<b>".$SITE['imagesDir']. "</b>' ";
  if(is_dir($SITE['imagesDir'])) {
	  print " exists; ";
	  print file_exists($SITE['imagesDir'].'skc.jpg')?
	    " and appears to have contents.<br/>\n":" but <b>does not have contents</b>.  Be sure to upload contents for proper template operation.<br/>\n";
  } else {
	  print " <b>is not on website.</b>  Be sure to upload contents for proper template operation.<br/>\n";
  }
  
  if(isset($SITE['NWSalertsCodes']) and file_exists('nws-alerts-config.php')) {
	include_once('nws-alerts-config.php');
	$tFolder = $icons_folder . '/';
	print "nws-alerts-config.php <b>alert-images file directory</b> \$icons_folder='<b>$icons_folder</b>' ";
	if(is_dir($tFolder)) {
		print " exists; ";
		print file_exists($tFolder.'A-none.png')?
		  " and appears to have contents.<br/>\n":" but <b>does not have contents</b>.  Be sure to upload contents for proper template operation.<br/>\n";
	} else {
		print " <b>is not on website.</b>  Be sure to upload contents for proper template operation.<br/>\n";
	}
  }

  if(isset($SITE['fcsticonsdirEC']) and file_exists('ec-forecast.php')) {
	$tFolder = $SITE['fcsticonsdirEC'];
	print "Settings.php <b>ec-icons file directory</b> \$SITE['fcsticonsdirEC']='<b>$tFolder</b>' ";
	if(is_dir($tFolder)) {
		print " exists; ";
		print file_exists($tFolder.'01.gif')?
		  " and appears to have contents.<br/>\n":" but <b>does not have contents</b>.  Be sure to upload contents for proper template operation.<br/>\n";
	} else {
		print " <b>is not on website.</b>  Be sure to upload contents for proper template operation.<br/>\n";
	}
  }
  
  print "Settings.php <b>forecast images file directory</b> in \$SITE['fcsticonsdir']='<b>".$SITE['fcsticonsdir']. "</b>' ";
  if(is_dir($SITE['fcsticonsdir'])) {
	  print " exists; ";
	  print file_exists($SITE['fcsticonsdir'].'skc.jpg')?
	    " and appears to have contents.<br/>\n":" but <b>does not have contents</b>.  Be sure to upload contents for proper template operation.<br/>\n";
  } else {
	  print " <b>is not on website.</b>  Be sure to upload contents for proper template operation.<br/>\n";
  }
  
  if(isset($WXdirs[$wxsftw])) { // check weather-software specific directories
    $toCheck = $WXdirs[$wxsftw]; // get the list.
	foreach ($toCheck as $siteVar => $chkFile) {
	  if(isset($SITE[$siteVar])) {
		$chkDir = $SITE[$siteVar];
		print "Settings-weather.php \$SITE['$siteVar']='<b>".$chkDir. "</b>' ";
		if(is_dir($chkDir)) {
			print " exists; ";
			list($nowYear,$nowMonth,$nowMM,$nowDD) = explode(" ",date('Y F m d',time()-24*60*60));
			$chkFile = preg_replace('|yyyy|',$nowYear,$chkFile);
			$chkFile = preg_replace('|yy|',substr($nowYear,2,2),$chkFile);
			$chkFile = preg_replace('|MONTH|',$nowMonth,$chkFile);
			$chkFile = preg_replace('|mm|',$nowMM,$chkFile);
			$chkFile = preg_replace('|dd|',$nowDD,$chkFile);
			
			print file_exists($chkDir.$chkFile)?
			  " and appears to have contents.<br/>\n":" but <b>does not have contents ($chkFile tested)</b>.  Set $wxsftw software to upload contents for proper template operation.<br/>\n";
		} else {
			print " <b>is not on website.</b>  Set $wxsftw software to upload contents for proper template operation.<br/>\n";
		}
		  
	  }
		
	}
  
 
  }
  print "</p>\n";

  $updateDate = file_exists("common.php")?filemtime("common.php"):'unknown';
  if($updateDate <> 'unknown') {$updateDate = gmdate('D, d-M-Y g:ia T',$updateDate); }
  print "<p>common.php last updated: $updateDate</p>\n";	
  $updateDate = file_exists("language-en.txt")?filemtime("language-en.txt"):'unknown';
  if($updateDate <> 'unknown') {$updateDate = gmdate('D, d-M-Y g:ia T',$updateDate); }
  print "<p>language-en.txt last updated: $updateDate</p>\n";	

  print "  </body>\n";
  print "</html>\n";
	
  exit;
}

$time_init = time();


if(file_exists("Settings.php")) {
  $T_start = microtime_float();
  include("Settings.php");
  $T_stop = microtime_float();
  $settingsLoad = "Included Settings.php time=" . sprintf("%01.3f",round($T_stop - $T_start,3)) . " secs.\n\n";
} else {
  $settingsLoad = "Unable to find Settings.php.. testing skipped.\n";
  print $settingsLoad;
  return;
}
printHeaders();
// ------------------------------------------------------------------
//
// do version checking for key scripts (part of V1.06+)
//
if(isset($_REQUEST['show']) and strtolower($_REQUEST['show']) == 'versions') { // do Version checking
  // Template updates are all based in Pacific time in the distribution .zip files
  $ourTZ = 'America/Los_Angeles';
  # set the Timezone abbreviation automatically based on $SITE['tzname'];
  # Set timezone in PHP5/PHP4 manner
	if (!function_exists('date_default_timezone_set')) {
	   putenv("TZ=" . $ourTZ);
  //	 print "<!-- using putenv(\"TZ=". $SITE['tz']. "\") -->\n";
	  } else {
	   date_default_timezone_set($ourTZ);
  //	 print "<!-- using date_default_timezone_set(\"". $SITE['tz']. "\") -->\n";
	 }
  global $SITE;
  $Lang = 'en';
  $cacheFileDir = './';
  
  if(isset($SITE['lang'])) {$Lang = $SITE['lang'];}
  if(isset($SITE['cacheFileDir'])) {$cacheFileDir = $SITE['cacheFileDir'];}

  $templateVersionsFile = 'template-version-info.txt';
  $templateVersionsURL = 'http://saratoga-weather.org/wxtemplates/'.$templateVersionsFile;  

  print $settingsLoad;
  
  # fetch/cache template version info file from master (if available)
  $TESTURL = $templateVersionsURL;
  $CACHE = $cacheFileDir.$templateVersionsFile;
  print "<pre>\n";  
  if (!isset($_REQUEST['force']) and file_exists($CACHE) and filemtime($CACHE) + 600 > time()) {  // 1800
    print "..loading $CACHE for version information.\n";
  } else {
	print "..fetching recent version information.\n";
	$rawhtml = fetchUrlWithoutHanging($TESTURL,false);
	$RC = '';
	if (preg_match("|^HTTP\/\S+ (.*)\r\n|",$rawhtml,$matches)) {
		$RC = trim($matches[1]);
	}
	print "RC=$RC, bytes=" . strlen($rawhtml) . "\n";
	$i = strpos($rawhtml,"\r\n\r\n");
	$headers = substr($rawhtml,0,$i-1);
	$content = substr($rawhtml,$i+2);
	$html = explode("\n",$content);  // put HTML area as separate lines
	$age = -1;
	$udate = 'unknown';
	$budate = 0;
	if(preg_match('|\nLast-Modified: (.*)\n|Ui',$headers,$match)) {
		$udate = trim($match[1]);
		$budate = strtotime($udate);
		$age = abs(time() - $budate); // age in seconds
		print "Data age=$age sec '$udate'\n";
	}
	  
	if (!preg_match('| 200|',$headers)) {
	  print "------------\nHeaders returned:\n\n$headers\n------------\n";
	  print "\nSkipped cache write to $CACHE file.\n";
	} elseif ($CACHE <> '') {
		$fp = fopen($CACHE,'w');
		if($fp) {
		  $write = fputs($fp, $rawhtml); 
		  fclose($fp);
		  print "Wrote ".strlen($rawhtml). " bytes to $CACHE successfully.\n";
		} else {
		  print "Error: Unable to write to $CACHE file.\n";
		}
	} 

  } // end fetch new version info from saratoga-weather.org 
 
 # now load up the version info which looks like:
/*
# template-version-info updated 2012-08-06 08:38 PDT by( version-info V1.00 - 05-Aug-2012 )
#Base	File	ModDate	Size	Index	ZipSize	MD5	Version	VDate	VersionDesc
Base-Canada	wxuvforecast.php	2012-03-31 05:20 PDT	7376	299	7193	abfb72a9504fc73812e8a4eb8822831a	1.01	2012-03-31	1.01 - 31-Mar-2012 - day-of-week fix for get-UV-forecast-inc.php V1.07
Base-Canada	wxtrends.php	2011-01-19 11:04 PST	2914	298	2842	ff4f1a25ebeb60a130b291303005817e	n/a	2011-01-19	(not specified)
*/ 
    $MasterVersions = array();
	$nVersions = 0;
    $VFile = file($CACHE);
	if(count($VFile) < 10) {
		print "Error: $CACHE file is not complete..skipping testing.\n";
		return;
	}
	foreach ($VFile as $n => $rec) {
	  $recin = trim($rec);
	  if ($recin and substr($recin,0,1) <> '#') { // got a non comment record
        list($Base,$File,$ModDate,$Size,$Index,$ZipSize,$FileMD5,$Fversion,$FvDate,$FvDesc) = explode("\t",$recin . "\t\t\t\t\t\t\t\t\t\t");
		$MasterVersions["$Base\t$File"] = "$ModDate\t$Size\t$FileMD5\t$Fversion\t$FvDate\t$FvDesc";
		$nVersions++;
	  }
	}
  print "..loaded $nVersions version descriptors from $CACHE file.\n";
 
  # end of get new template version info file
# set of files to do version checking  
  $templateFiles = array( 

    'Common' => array(
//	  'ajaxgizmo.js',
	  'ajax-gizmo.php','ajax-dashboard.php','common.php','check-fetch-times.php',
	  'flyout-menu.php','include-style-switcher.php',
	  'get-metar-conditions-inc.php','get-USNO-sunmoon.php','get-UV-forecast-inc.php',
	  'include-metar-display.php','include-wxstatus.php','plaintext-parser.php','plaintext-parser-data.txt',
	  'thermometer.php','wxgraphs.php','wxmetar.php','wxquake.php'),

   'USA' => array(
	  'advforecast2.php','atom-advisory.php','atom-top-warning.php','get-nnvl-iod.php',
	  'GR3-radar-inc.php','floatTop.js',
	  'nws-alerts.php','nws-alerts-details-inc.php','nws-alerts-summary-inc.php',
	  'nws-alertmap.js','nws-shapefile.txt','swfobject.js',
	  'quake-json.php','quake-json.js','radar-status.php','WU-radar-inc.php'),

   'Canada' => array(
	   'ec-forecast.php','ec-radar.php','quake-Canada.php'),

   'World' => array(
	  'get-meteoalarm-warning-inc.php','quake-json.php','quake-json.js','quake-UK.php',
	  'Settings-language.php','WU-forecast.php'),
	'CU' => array('ajaxCUwx.js','CU-defs.php','gen-CUtags.php','tags.txt',
	   'include-NOAA-reports.php'),
	'MH' => array('ajaxMHwx.js','MH-defs.php','yesterday.php','MH-trends-inc.php'),
	'VWS' => array('ajaxVWSwx.js','VWStags.php','VWS-defs.php','gen-VWStags.php','tags.txt',
	    'include-NOAA-reports.php'),
	'WCT' => array('ajaxWCTwx.js','WCT-defs.php','gen-WCTtags.php','WeatherCat-webtags.txt'),
    'WD' => array(
	  'ajaxWDwx.js','WD-trends-inc.php','include-wxhistory.php'),
	'WL' => array('ajaxWLwx.js','WL-defs.php','gen-WLtags.php','WLtags.txt',
	  'include-NOAA-reports.php'),
	 'WSN' => array('WSNtags.php','WSN-defs.php'),
	 'WV' => array('WV-defs.php','gen-WVtags.php','tags.txt',
	  'include-NOAA-reports.php'),
	  
	 'WXS' => array('WXS-defs.php','gen-WXStags.php','tags.txt'),
	 
  );
  $selectedVersions = array();
  $selectedVersionsType = array();
  
  $toCheckFiles = $templateFiles['Common'];
  $toCheckLegend = 'Common Files';
  foreach ($templateFiles['Common'] as $key => $val) {$selectedVersionsType[$val] = 'Common'; }
  $updateBasePlugin = '';
  if(isset($SITE['fcsturlNWS']) or isset($SITE['NWSforecasts'])) {
	  $toCheckFiles = array_merge($toCheckFiles,$templateFiles['USA']);
	  $toCheckLegend .= ', Base-USA';
	  load_selected_array('Base-USA');
	  $updateBasePlugin = 'Base-USA';
	  foreach ($templateFiles['USA'] as $key => $val) {$selectedVersionsType[$val] = 'USA'; }
  }
  if(isset($SITE['fcsturlEC']) or isset($SITE['ecradar'])) {
	  $toCheckFiles = array_merge($toCheckFiles,$templateFiles['Canada']);
	  $toCheckLegend .= ', Base-Canada';
	  load_selected_array('Base-Canada');
	  $updateBasePlugin = 'Base-Canada';
	  foreach ($templateFiles['Canada'] as $key => $val) {$selectedVersionsType[$val] = 'Canada'; }
  }

  if(isset($SITE['EUwarningURL']) or isset($SITE['fcsturlWU']) or isset($SITE['WUforecasts'])) {
	  $toCheckFiles = array_merge($toCheckFiles,$templateFiles['World']);
	  $toCheckLegend .= ', Base-World';
	  load_selected_array('Base-World');
	  $updateBasePlugin = 'Base-World';
	  foreach ($templateFiles['World'] as $key => $val) {$selectedVersionsType[$val] = 'World'; }
  }
  
  if(isset($SITE['WXsoftware']) and isset($templateFiles[ $SITE['WXsoftware'] ]) ){
	  $wxsftw=$SITE['WXsoftware'];
	  $toCheckFiles = array_merge($toCheckFiles,$templateFiles[$wxsftw]);
	  $toCheckLegend .= ', '.$wxsftw.'-plugin';
	  load_selected_array($wxsftw.'-plugin');
	  foreach ($templateFiles[$wxsftw] as $key => $val) {$selectedVersionsType[$val] = $wxsftw.'-plugin'; }
	  
	  if( isset($SITE['WXsoftwareLongName']) ) {
		  $toCheckLegend .= ' (for '.$SITE['WXsoftwareLongName'].' weather software)';
	  }
	  $updateBasePlugin .= ', '.$wxsftw.'-plugin';

  }
  print "</pre>\n";

  print "<h3>Version information for selected <strong>$toCheckLegend</strong> key template files</h3>\n";
  print "<p style=\"border: 2px dotted red; background-color: #FFCC00; padding: 5px;\">";
  print "<strong>Note</strong>: only selected key template files are checked with this script. Files with customary user modifications (Settings.php, Settings-weather.php, top.php, header.php, menubar.php, footer.php, most wx...php files, etc.) and graphics and weather tags files are NOT checked as they either do not contain version information or they are expected to be different from the distribution versions due to normal website  customization.";
  
  print "</p>\n";
  
  print "<table style=\"border: 1px;\" cellpadding=\"2\" cellspacing=\"2\">\n";
  print "<tr><th>Script<br/>Origin</th><th>Script<br/>Name</th><th>Installed Script</br>Version Status</th><th>Release Script<br/>Version</th><th>Installed Script<br/>Version</th><th>Installed Script Internal<br/>Version Description</th></tr>\n";
  $earliestDate = '9999-99-99';
  
  natcasesort($toCheckFiles);
  $idx = 0;
  foreach ($toCheckFiles as $n => $checkFile) {
	  if ($idx % 5 <> 0) { $TRclass = 'row-even'; } else { $TRclass = 'row-odd'; }
	  list($mDate,$vNumber,$vDate,$vInfo,$FileMD5,$fStatus) = chk_file_version($checkFile);
	  $instVer = '';
	  if($vNumber <> '' and $vDate <> '') {$instVer = "V$vNumber - $vDate"; }
	  $distVer = '';
	  if(isset($selectedVersions[$checkFile])) { 
		 list($mstModDate,$mstSize,$mstFileMD5,$mstFversion,$mstFvDate,$mstFvDesc) = 
			explode("\t",$selectedVersions[$checkFile]);
		 $distVer = "V$mstFversion - $mstFvDate";
	  }
	  $fSource = $selectedVersionsType[$checkFile];
	  print "<tr class=\"$TRclass\"><td>$fSource</td><td><strong>$checkFile</strong></td><td>$fStatus</td><td>$distVer</td><td>$instVer</td><td>$vInfo</td></tr>\n";
	  $idx++;
  }
	  
  print "</table>\n";	  

  if($earliestDate <> '9999-99-99') {
	  //found some updates
	 $updateBasePluginDate = date('d-M-Y',strtotime($earliestDate));
	 print "<h3>To update your template set to current script version(s), use <a href=\"http://saratoga-weather.org/wxtemplates/updates.php\"><strong>the updates tool page</strong></a> with a query set for <strong>$updateBasePluginDate</strong> for ";
	 print "<strong>$updateBasePlugin</strong></h3>\n"; 
	  
  }


 print "<pre>\n";
// end of version checking  
  
} else { // do fetch file checking

// ------------------------------------------------------------------

// file fetch checking

  print '
  <p>This script will check the load times and the ability to save cache files for the included support
  scripts with your template package.</p>
  <pre>
  ';
  
  print $settingsLoad;
  
  global $SITE;
  $Lang = 'en';
  $cacheFileDir = './';
  
  if(isset($SITE['lang'])) {$Lang = $SITE['lang'];}
  if(isset($SITE['cacheFileDir'])) {$cacheFileDir = $SITE['cacheFileDir'];}
  
  
  $Tests = array(
  'fcsturlNWS' => 'NWS Forecast URL|forecast.txt|NWSforecasts|2|forecast-0.txt',
  'noaazone'   => 'NWS Warning Zone ATOM/CAP Feed',
  'UVscript'   => 'UV Forecast from temis.nl|uv-forecast.txt',
  'fcsturlEC'  => 'EC Forecast URL|ec-forecast-LL.txt',
  'ecradar'    => 'EC Radar URL',
  'fcsturlWU'  => 'WU Forecast URL|WU-forecast-LL.txt|WUforecasts|1|WU-forecast-0-LL.txt',
  'EUwarningURL' => 'METEOalarm warning URL|meteoalarm-LL.txt'
  );
  
  print "<p>Using lang=$Lang as default for testing</p>\n";
  
  global $TOTALtime;
  $TOTALtime = $T_stop - $T_start;
  
  foreach ($Tests as $sname => $sval) {
	$useAltUrl = '';
	list($sdescript,$cname,$altvar,$altindex,$altcname) = explode('|',$sval.'||||');
	if($altvar <> '' and isset($SITE[$altvar][0]) ) { // fetch first entry in multiforecast variable
	   $vars = explode('|',$SITE[$altvar][0].'||||');
	   $useAltUrl = $vars[$altindex];
       $cname = preg_replace('|LL|',$Lang,$altcname);
	} else {
	  $cname = preg_replace('|LL|',$Lang,$cname);
	}
	if(isset($SITE[$sname])) {
	  print "--checking $sdescript --\n";
	  $TESTURL = $SITE[$sname];
	  $CACHE = '';
	  if($useAltUrl) {
		 $TESTURL = $useAltUrl;
	  }
	  if($cname <> '') {$CACHE = $cacheFileDir.$cname; }
	  
	  if($sname == 'UVscript') {
		$TESTURL = "http://www.temis.nl/uvradiation/nrt/uvindex.php?lon=" .$SITE['longitude'] . "&lat=" . $SITE['latitude'];
	  }
	  if($sname == 'noaazone') {
		$TESTURL = "http://alerts.weather.gov/cap/wwaatmget.php?zone=".$SITE['noaazone'];
		$CACHE = $cacheFileDir."atom-advisory-".$SITE['noaazone'].".txt";
	  }
	  if($sname == 'ecradar') {
		$TESTURL = 'http://weatheroffice.gc.ca/radar/index_e.html?id=' . $SITE['ecradar'];
		$CACHE = "../radar/ec-radar-en.txt";
	  }
	  if($useAltUrl <> '') {
		  print "Using first entry in \$SITE['$altvar'] for test.\n";
	  } else {
		  print "Using \$SITE['$sname'] entry for test.\n";
	  }
	  print "URL: $TESTURL\n";
	  if($CACHE <> '') {
		print "Cache: $CACHE\n";
	  }
	  $rawhtml = fetchUrlWithoutHanging($TESTURL,false);
	  $RC = '';
	  if (preg_match("|^HTTP\/\S+ (.*)\r\n|",$rawhtml,$matches)) {
		  $RC = trim($matches[1]);
	  }
	  print "RC=$RC, bytes=" . strlen($rawhtml) . "\n";
	  $i = strpos($rawhtml,"\r\n\r\n");
	  $headers = substr($rawhtml,0,$i-1);
	  $content = substr($rawhtml,$i+2);
	  $html = explode("\n",$content);  // put HTML area as separate lines
	  $age = -1;
	  $udate = 'unknown';
	  $budate = 0;
	  if(preg_match('|\nLast-Modified: (.*)\n|Ui',$headers,$match)) {
		  $udate = trim($match[1]);
		  $budate = strtotime($udate);
		  $age = abs(time() - $budate); // age in seconds
		  print "Data age=$age sec '$udate'\n";
	  }
		
	  if (!preg_match('| 200 |',$headers)) {
		print "------------\nHeaders returned:\n\n$headers\n------------\n";
		print "\nSkipped cache write test to $CACHE file.\n";
	  } elseif ($CACHE <> '') {
		  $fp = fopen($CACHE,'w');
		  if($fp) {
			$write = fputs($fp, $rawhtml); 
			fclose($fp);
			print "Wrote ".strlen($rawhtml). " bytes to $CACHE successfully.\n";
		  } else {
			print "Error: Unable to write to $CACHE file.\n";
		  }
	  } 
			  
  
	
	  print "--end $sdescript check --\n\n";
	}
  
  
  }
  
  print "\nTotal time taken = " . sprintf("%01.3f",round($TOTALtime,3)) . " secs.\n";
  $time_finished = time();
  $time_elapsed = $time_finished - $time_init;
  print "Elapsed $time_elapsed seconds.\n\n";
} // end fetch-time-checking

print "PHP Version " . phpversion() . "\n";
print "Memory post_max_size " . ini_get('post_max_size') . " bytes.\n";
print "Memory usage " . memory_get_usage() . " bytes.\n";
print "Memory peak usage " . memory_get_peak_usage() . " bytes.\n";
?>
</pre>
<?php
// ------------------------------------------------------------------

function printHeaders() {
  global $Version;
  print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Saratoga-weather.org Template Test Utility</title>
<meta http-equiv="Robots" content="noindex,nofollow,noarchive" />
<meta name="author" content="Ken True" />
<meta name="copyright" content="&copy; 2012, Saratoga-Weather.org" />
<meta name="Description" content="Saratoga-weather.org AJAX/PHP website template diagnostic utility." />
<style type="text/css">
.row-odd  {background-color: #96C6F5; }
.row-even {background-color: #EFEFEF; }
.num { 
        float: left; 
        color: gray; 
        font-size: 13px;    
        font-family: monospace; 
        text-align: right; 
        margin-right: 6pt; 
        padding-right: 6pt; 
        border-right: 1px solid gray;
} 

body {margin: 0px; margin-left: 5px;} 
td {    vertical-align: top;
        font-size: 13px;    
        font-family: monospace; 
} 
code {white-space: nowrap;
        font-size: 13px;    
        font-family: monospace; 
} 
</style>
</head>
<body style="background-color:#FFFFFF; font-family:Arial, Helvetica, sans-serif;font-size: 10pt;">
<h3>'.$Version.'</h3>
';
	
}
// ------------------------------------------------------------------
// Retrieve information about the currently installed GD library
// script by phpnet at furp dot com (08-Dec-2004 06:59)
//   from the PHP usernotes about gd_info
function describeGDdyn() {
 echo "\n<ul><li>GD support: ";
 if(function_exists("gd_info")){
   echo "<span style=\"color:#00ff00\">yes</span>";
   $info = gd_info();
   $keys = array_keys($info);
   for($i=0; $i<count($keys); $i++) {
	  if(is_bool($info[$keys[$i]])) {
		echo "</li>\n<li>" . $keys[$i] .": " . yesNo($info[$keys[$i]]);
	  } else {
		echo "</li>\n<li>" . $keys[$i] .": " . $info[$keys[$i]];
	  }
   }
 } else { 
   echo "<span style=\"color:#ff0000\">NO</span>"; 
 }
 echo "</li></ul>\n";
}

// ------------------------------------------------------------------

function yesNo($bool){
 if($bool) {
	 return "<span style=\"color:#00ff00\"> yes</span>";
 } else {
	 return "<span style=\"#ff0000\"> no</span>";
 }
}  

// ------------------------------------------------------------------

function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
// ------------------------------------------------------------------

// get contents from one URL and return as string 
 function fetchUrlWithoutHanging($url,$useFopen) {
// thanks to Tom at Carterlake.org for this script fragment
  global $Debug, $needCookie,$timeStamp,$TOTALtime;
  $overall_start = time();
  if (! $useFopen) {
   // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed
   $numberOfSeconds=2;   

   // Suppress error reporting so Web site visitors are unaware if the feed fails
   error_reporting(0);

   // Extract resource path and domain from URL ready for fsockopen
   $FullUrl = $url;
   $urlParts = parse_url($url);
   
   $domain = $urlParts['host'];
   if(isset($urlParts['port'])) {
      $port   = $urlParts['port'];
   } else { 
      $port   = 80;
   }
   $resourcePath = $urlParts['path'];
   $resourcePath = preg_replace('|nocache|','?'.$timeStamp,$resourcePath);
   if(isset($urlParts['query']))    {$resourcePath .= "?" . $urlParts['query']; }
   if(isset($urlParts['fragment'])) {$resourcePath .= "#" . $urlParts['fragment']; }
   $T_start = microtime_float();
   $hostIP = gethostbyname($domain);
   $T_dns = microtime_float();
   $ms_dns  = sprintf("%01.3f",round($T_dns - $T_start,3));
   
   $Debug .= "<!-- GET $resourcePath HTTP/1.1 \n      Host: $domain  Port: $port IP=$hostIP-->\n";
   print "GET $resourcePath HTTP/1.1 \n      Host: $domain  Port: $port IP=$hostIP\n";

   // Establish a connection
   $socketConnection = fsockopen($hostIP, $port, $errno, $errstr, $numberOfSeconds);
   $T_connect = microtime_float();
   $T_puts = 0;
   $T_gets = 0;
   $T_close = 0;
   
   if (!$socketConnection)
       {
       // You may wish to remove the following debugging line on a live Web site
       $Debug .= "<!-- Network error: $errstr ($errno) -->\n";
       print "Network error: $errstr ($errno)\n";
       }    // end if
   else    {
       $xml = '';
	   $getString = "GET $resourcePath HTTP/1.1\r\nHost: $domain\r\nConnection: Close\r\n\r\n";
//	   if (isset($needCookie[$domain])) {
//	     $getString .= $needCookie[$domain] . "\r\n";
//		 print " used '" . $needCookie[$domain] . "' for GET \n";
//	   }
	   
//	   $getString .= "\r\n";
//	   print "Sending:\n$getString\n\n";
       fputs($socketConnection, $getString);
       $T_puts = microtime_float();
	   
       // Loop until end of file
	   $TGETstats = array();
	   $TGETcount = 0;
       while (!feof($socketConnection))
           {
		   $T_getstart = microtime_float();
           $xml .= fgets($socketConnection, 8192);
		   $T_getend = microtime_float();
		   $TGETcount++;
		   $TGETstats[$TGETcount] = sprintf("%01.3f",round($T_getend - $T_getstart,3));
           }    // end while
       $T_gets = microtime_float();
       fclose ($socketConnection);
       $T_close = microtime_float();
       }    // end else
   $ms_connect = sprintf("%01.3f",round($T_connect - $T_dns,3));

   if($T_close > 0) {
      $ms_puts = sprintf("%01.3f",round($T_puts - $T_connect,3));
	  $ms_gets = sprintf("%01.3f",round($T_gets - $T_puts,3));
	  $ms_close = sprintf("%01.3f",round($T_close - $T_gets,3));
	  $ms_total = sprintf("%01.3f",round($T_close - $T_start,3)); 
    } else {
       $ms_puts = 'n/a';
	  $ms_gets = 'n/a';
	  $ms_close = 'n/a';
	  $ms_total = sprintf("%01.3f",round($T_connect - $T_start,3)); 
   }

   $Debug .= "<!-- HTTP stats: conn=$ms_connect put=$ms_puts get=$ms_gets close=$ms_close total=$ms_total secs -->\n";
   print  "HTTP stats: dns=$ms_dns conn=$ms_connect put=$ms_puts get($TGETcount blocks)=$ms_gets close=$ms_close total=$ms_total secs \n";
//   foreach ($TGETstats as $block => $mstimes) {
//     print "HTTP Block $block took $mstimes\n";
//   }
   $TOTALtime+= ($T_close - $T_start);
   $overall_end = time();
   $overall_elapsed =   $overall_end - $overall_start;
   print "fetch function elapsed= $overall_elapsed secs.\n"; 
   return($xml);
 } else {
//   print "<!-- using file function -->\n";
   $T_start = microtime_float();

   $xml = implode('',file($url));
   $T_close = microtime_float();
   $ms_total = sprintf("%01.3f",round($T_close - $T_start,3)); 
   $Debug .= "<!-- file() stats: total=$ms_total secs -->\n";
   print " file() stats: total=$ms_total secs.\n";
   $TOTALtime+= ($T_close - $T_start);
   $overall_end = time();
   $overall_elapsed =   $overall_end - $overall_start;
   print "fetch function elapsed= $overall_elapsed secs.\n"; 
   return($xml);
 }

   }    // end fetchUrlWithoutHanging
// ------------------------------------------------------------------
#---------------------------------------------------------  
# load file to string for version checking
#--------------------------------------------------------- 
function chk_file_version($inFile) {
   global $selectedVersions,$earliestDate;
   if(!file_exists($inFile)) {
	  return(
	  array('n/a','','',"<strong>$inFile file not found.</strong>",'','<strong>File not installed</strong>')); 
   }
   $mDate = date('Y-m-d H:i T',filemtime($inFile));
   $tContents = file_get_contents($inFile);
   $vInfo = scan_for_version_string($tContents);
	$tContents = preg_replace('|\r|is','',$tContents);
	$FileMD5 = md5($tContents);

   if(strlen($vInfo) > 120) {$vInfo = '(not specified)'; }
   if(preg_match('!(\d+\.\d+)[^\d]*(\d+-\S{3}-\d{4})!',$vInfo,$matches)) {
	$vNumber = $matches[1];
	$vDate = date('Y-m-d',strtotime($matches[2]));
   } else {
	$vNumber = 'n/a';
	$vDate = 'n/a';
   }
   $fStatus = 'unknown';
   if(isset($selectedVersions[$inFile])) { 
     list($mstModDate,$mstSize,$mstFileMD5,$mstFversion,$mstFvDate,$mstFvDesc) = 
	    explode("\t",$selectedVersions[$inFile]);
	 $MD5matches = ($mstFileMD5 == $FileMD5)?true:false;
	 $VerMatches = ($vNumber <> 'n/a' and $mstFversion <> 'n/a' and 
	    strcmp($vNumber,$mstFversion) === 0)?true:false;

	 if ($MD5matches) { $fStatus = "Current<!-- MD5 matched -->"; }
	 if ($fStatus == 'unknown' and $VerMatches) {$fStatus = 'Current<!-- version matched -->'; }
	 if ($fStatus == 'unknown' and $vNumber <> 'n/a' and $mstFversion <> 'n/a' and 
	    strcmp($vNumber,$mstFversion) < 0) {
		  $fStatus = "<strong>Need update to<br/>V$mstFversion - $mstFvDate</strong>"; 
		  $earliestDate = ($mstFvDate < $earliestDate)?$mstFvDate:$earliestDate;
	 }
	 if ($fStatus == 'unknown' and $vNumber <> 'n/a' and $mstFversion <> 'n/a' and 
	    strcmp($vNumber,$mstFversion) > 0) {
		  $fStatus = "<strong>Installed version is more recent</strong>"; 

	 }
	 if ($fStatus == 'unknown' and $mstFversion <> 'n/a' and $mstFvDate <> 'n/a') {
		  $fStatus = "<strong>Need update to<br/>V$mstFversion - $mstFvDate</strong>";
		  $earliestDate = ($mstFvDate < $earliestDate)?$mstFvDate:$earliestDate;
	 }
  
   }
   return(array($mDate,$vNumber,$vDate,$vInfo,$FileMD5,$fStatus));
}
#---------------------------------------------------------  
# scan for a version string in a PHP/JS/TXT file
#---------------------------------------------------------  
function scan_for_version_string($input) {

	$vstring = '(not specified)';
	
	preg_match('/\$\S*Version\s+=\s+[\'|"]([^\'|"]+)[\'|"];/Uis',$input,$matches);
	if(isset($matches[1])) {
		$vstring = $matches[1];
//		print "--- 1:found $vstring ---\n";
		return(trim($vstring));
	}
    
	preg_match_all('/Version (.*)\n/Uis',$input,$matches);
	
//	print "---2:Matches\n".print_r($matches,true)."\n---\n";
	
	if (isset($matches[1]) and count($matches[1]) > 0) {
		for($i=count($matches[1])-1;$i>=0;$i--) {
           $tstring = $matches[1][$i];		    
		   if(preg_match('|\d+-\S{3}-\d{4}|',$tstring)) {
		     $vstring = $tstring;
//		     print "--- 2:found $vstring ---\n";
		     return(trim($vstring));
		   }
	   }

	}
	
	return($vstring);
	
} // end scan_for_version_string

#---------------------------------------------------------  
# load the to-scan array with the filenames to look for
#---------------------------------------------------------  
function load_selected_array($key) {
	global $MasterVersions,$selectedVersions;
	$n = 0;
	foreach ($MasterVersions as $k => $data) {
		list($base,$file) = explode("\t",$k);
		if($base == $key) {
			$selectedVersions["$file"] = $data;
			$n++;
		}
		
	}
	print "..loaded $n version descriptors for $key.\n";
	return;
} // end load_selected_array

#---------------------------------------------------------  
# display file with PHP highlighting and line numbers
#---------------------------------------------------------  

function highlight_file_num($file) 
{ 
  $lines = implode(range(1, count(file($file))), '<br />'); 
  $content = highlight_file($file, true); 

  
    echo "<table><tr><td class=\"num\">\n$lines\n</td><td>\n$content\n</td></tr></table>";
 } 

#---------------------------------------------------------  
# decode unix file permissions
#---------------------------------------------------------  

function decode_permissions($perms) {

  if (($perms & 0xC000) == 0xC000) {
	  // Socket
	  $info = 's';
  } elseif (($perms & 0xA000) == 0xA000) {
	  // Symbolic Link
	  $info = 'l';
  } elseif (($perms & 0x8000) == 0x8000) {
	  // Regular
	  $info = '-';
  } elseif (($perms & 0x6000) == 0x6000) {
	  // Block special
	  $info = 'b';
  } elseif (($perms & 0x4000) == 0x4000) {
	  // Directory
	  $info = 'd';
  } elseif (($perms & 0x2000) == 0x2000) {
	  // Character special
	  $info = 'c';
  } elseif (($perms & 0x1000) == 0x1000) {
	  // FIFO pipe
	  $info = 'p';
  } else {
	  // Unknown
	  $info = 'u';
  }
  
  // Owner
  $info .= (($perms & 0x0100) ? 'r' : '-');
  $info .= (($perms & 0x0080) ? 'w' : '-');
  $info .= (($perms & 0x0040) ?
			  (($perms & 0x0800) ? 's' : 'x' ) :
			  (($perms & 0x0800) ? 'S' : '-'));
  
  // Group
  $info .= (($perms & 0x0020) ? 'r' : '-');
  $info .= (($perms & 0x0010) ? 'w' : '-');
  $info .= (($perms & 0x0008) ?
			  (($perms & 0x0400) ? 's' : 'x' ) :
			  (($perms & 0x0400) ? 'S' : '-'));
  
  // World
  $info .= (($perms & 0x0004) ? 'r' : '-');
  $info .= (($perms & 0x0002) ? 'w' : '-');
  $info .= (($perms & 0x0001) ?
			  (($perms & 0x0200) ? 't' : 'x' ) :
			  (($perms & 0x0200) ? 'T' : '-'));
  
  return $info;
}
?>
</body>
</html>