<?php
$Version = "check-fetch-times.php Version 1.04 - 14-Jan-2012";
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
if(isset($_REQUEST['show']) and strtolower($_REQUEST['show']) == 'settings') {
	$toShow = array("Settings.php","Settings-weather.php","Settings-language.php");
	$doneHeaders = false;
	
	foreach ($toShow as $n => $showFilename) {
	  if(!$doneHeaders) { 
	    printHeaders(); 
		$doneHeaders = true; 
	    print "<h2>Contents of Settings files</h2>\n";
	  }
	  if(file_exists($showFilename)) {
		  print "<h3>$showFilename</h3>\n";
		  print "<pre style=\"border: 1px solid black;\">\n";
		  highlight_file($showFilename);
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

if(isset($_REQUEST['show']) and strtolower($_REQUEST['show']) == 'info') {
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

	$toCheck = array('simplexml_load_file','iconv','json_decode');

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
  return;
}
printHeaders();

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
'fcsturlNWS' => 'NWS Forecast URL|forecast.txt',
'noaazone'   => 'NWS Warning Zone ATOM/CAP Feed',
'UVscript'   => 'UV Forecast from temis.nl|uv-forecast.txt',
'fcsturlEC'  => 'EC Forecast URL|ec-forecast-LL.txt',
'ecradar'    => 'EC Radar URL',
'fcsturlWU'  => 'WU Forecast URL|WU-forecast-LL.txt',
'EUwarningURL' => 'METEOalarm warning URL|meteoalarm-LL.txt'
);

print "<p>Using lang=$Lang as default for testing</p>\n";

global $TOTALtime;
$TOTALtime = $T_stop - $T_start;

foreach ($Tests as $sname => $sval) {
  list($sdescript,$cname) = explode('|',$sval.'||');
  $cname = preg_replace('|LL|',$Lang,$cname);
  if(isset($SITE[$sname])) {
    print "--checking $sdescript --\n";
	$TESTURL = $SITE[$sname];
	$CACHE = '';
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
print "PHP Version " . phpversion() . "\n";
print "Memory post_max_size " . ini_get('post_max_size') . " bytes.\n";
print "Memory usage " . memory_get_usage() . " bytes.\n";
print "Memory peak usage " . memory_get_peak_usage() . " bytes.\n";
?>
</pre>
<?php

function printHeaders() {
  global $Version;
  print '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>HTTP fetch load tester</title>
</head>
<body style="background-color:#FFFFFF; font-family:Arial, Helvetica, sans-serif">
<h1>HTTP Fetch Load Time Tester</h1>
<p>'.$Version.'</p>
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

?>
</body>
</html>
