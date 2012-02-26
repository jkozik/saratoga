<?php
// resize NOAA image-of-the-day from the new NNVL source
// Ken True - webmaster@saratoga-weather.org
//
// Version 1.00 - 06-Aug-2010 -- initial release
// Version 1.01 - 07-Aug-2010 -- corrected link for hi-res image
// Version 1.02 - 09-Aug-2010 -- added UTF-8 to ISO-8859-1 conversion for Title and Text
// Version 1.03 - 26-Aug-2010 -- try to handle issues when IOD posts an MP4 movie instead of a .jpg
// Version 1.04 - 01-Sep-2010 -- allow either PNG or JPG for default image type
// Version 1.05 - 14-Sep-2010 -- autoscale based on new_width, autofind image when .MP4 posted
// Version 1.06 - 21-Nov-2010 -- fix em-dash issue with iconv()
// Version 1.07 - 31-Dec-2010 -- handle missing image when only a movie is posted
// Version 1.08 - 26-Jan-2011 - added support for global $cacheFileDir for new templates
// Version 1.09 - 05-Mar-2011 - replaced split() with explode()
// Version 1.10 - 31-Jan-2012 - Fix for new NNVL page formatting
//
// Usage:
//
//  include_once("get-nnvl-iod.php");
// 
// on a PHP page where you'd like the output to appear
//
// Settings ------------------------------------------------------------
	$refreshTime = 60*60; // refresh every 60 minutes
	$IODdir      = './';  // directory for cache files (2 images, 1 html)
// new size for graphic
//	$new_width = 640;
//	$new_height = 360;
// 
	$new_width = 620;
	$new_height = 349;  // note: this will autoadjust based on image W x H px
//  --------------------------------------------------------------------
//  error_reporting(E_ALL); // uncomment for debugging

//  begin code ------
if (isset($_REQUEST['sce']) and strtolower($_REQUEST['sce']) == 'view' ) {
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
global $Debug;
// overrides from Settings.php if available
global $SITE;
if(isset($SITE['cacheFileDir']))     {$IODdir = $SITE['cacheFileDir']; }
// end of overrides from Settings.php

$Debug = "get-nnvl-iod.php V1.10 - 31-Jan-2012<br/>\n";
// Script internal settings .. don't normally change these
    $IODpageURL       = 'http://www.nnvl.noaa.gov/imageoftheday.php';
	$IODpageCache     = 'IOD-page-cache.txt';  // cache storage of $IODpage
	$IODimageCache    = 'IOD-image.jpg';      // cache storage of current IOD image resized to new width/height
	$IODimageCacheFS  = 'IOD-image-full.jpg'; // cache storage of full-size IOD image
    $IODURL           = 'http://www.nnvl.noaa.gov/';
//
$hasUrlFopenSet = ini_get('allow_url_fopen');
if(!$hasUrlFopenSet) {
	print "<h2>Warning: PHP does not have 'allow_url_fopen = on;' --<br/>image fetch by get-nnvl-iod.php is not possible.</h2>\n";
	print "<p>To fix, add the statement: <pre>allow_url_fopen = on;\n\n</pre>to your php.ini file to enable get-nnvl-iod.php operation.</p>\n";
	return;
}

$doRefresh = false;
if (isset($_REQUEST['cache']) and strtolower($_REQUEST['cache']) == 'refresh') {
	$refreshTime = 1; // force cache refresh
	$doRefresh = true;
	$Debug .= "Cache refresh forced <br/>\n";
}

$html = '';

if (file_exists($IODdir.$IODpageCache) and filemtime($IODdir.$IODpageCache) + $refreshTime > time()) {
	$Debug .= "cache $IODdir$IODpageCache current<br/>\n";
	$rawhtml = file_get_contents($IODdir.$IODpageCache);
    list($headers,$html) = explode("\r\n\r\n",$rawhtml);
	$Debug .= "html loaded from cache $IODdir$IODpageCache<br/>\n";
} else {
	$Debug .= "cache $IODdir$IODpageCache is stale.. reloading<br/>\n";
	$doRefresh = true;
}
$RC = '';

if ($doRefresh) { // do a refresh cycle

  $rawhtml = getHTTPpageIOD($IODpageURL,false);
  
  list($headers,$html) = explode("\r\n\r\n",$rawhtml);
  if (preg_match("|^HTTP\/\S+ (.*)\r\n|",$rawhtml,$matches)) {
	$RC = trim($matches[1]);
  }
  
  if(preg_match('|200 |',$RC) and strlen($html) > 50) {
	$udate = gmdate("D, d M Y H:i:s", time()) . " GMT";
 
   $fp = fopen($IODdir.$IODpageCache, "w");
   if ($fp) {
	$write = fputs($fp, $rawhtml);
	if ($write) {
	  fputs($fp,"\n<!-- $IODdir$IODpageCache lmod=$udate -->\n");
	  $Debug .= "$IODdir$IODpageCache page cache updated $udate<br/>\n";
	}
	fclose($fp);
	
   } else {
	$Debug .= "unable to write cache file $IODdir$IODpageCache<br/>\n";
   }
 
  
  } else {
    $Debug .= "Problem fetching $IODpageURL with RC=$RC , html length=".strlen($html) . "<br/>\n";
	$Debug .= "Headers returned are:\n";
	$Debug .= "<pre>\n$headers\n</pre>\n";
	$Debug .= "cache not saved.<br/>\n";
   }
}

$Debug .= "html size=".strlen($html)." RC=$RC <br/>\n";

preg_match('|charset="{0,1}([^"]+)"{0,1}|i',$headers,$matches);
  
if (isset($matches[1])) {
    $charsetInput = strtoupper($matches[1]);
  } else {
    $charsetInput = 'ISO-8859-1';
}
 $charsetOutput = 'ISO-8859-1';
 
 $Debug .= "using charsetInput='$charsetInput' charsetOutput='$charsetOutput'<br/>\n";


preg_match_all('|<td colspan=\'2\'>(.+)</td>|Uis',$html,$matches);
   // $Debug .= "<pre>".print_r($matches,true)."</pre>\n";

/*
    [1] => Array
        (
            [0] => <a href='images/high_resolution/478_MODIS-AOD-Moscow-20100804.jpg'><img style='max-width:900px;' src='images/low_resolution/478_MODIS-AOD-Moscow-20100804.jpg' alt='Data Shows Dangerous Smoke Plumes Over Russia' /></a>
            [1] => <big><big>Data Shows Dangerous Smoke Plumes Over Russia</big></big>

            [2] => A series of over 500 forest fires across Russia have killed at least 48 people since last week.  Air quality in Moscow and the surrounding areas has been severely degraded, with extremely dangerous levels of smoke and carbon monoxide.  The cause of these fire outbreaks is due to excessively hot and dry conditions, leading to drought and conditions ripe for the intensification and spread of otherwise normal fires in the Russian forests.  These drought conditions can be seen in our real-time data imagery <a href="http://nnvl.noaa.gov/DailyImage.php?product=DroughtRisk_Daily.png%7CDrought+Risk">here</a>. <p>The blanket of smoke resulting from the fires can be seen in satellite Aerosol Optical Depth data, such as from the NASA MODIS sensor, shown here over the period of July 31 - August 2, 2010.  Smoke is a combination of gases and small particles called aerosols, which affect the way that energy is transmitted though Earth's atmosphere into space.  The MODIS sensor detects these differences.  Aerosol optical depths around 0.1 are associated with clear skies, whereas values near 1.0 represent incredibly hazy conditions.
            [3] => <a href='images/high_resolution/478_MODIS-AOD-Moscow-20100804.jpg'>View Data Shows Dangerous Smoke Plumes Over Russia - High Resolution Version</a>
            [4] => &nbsp;
            [5] => <a href='MediaHome.php?MediaTypeID=1'>Return to List</a>
        )
or

   [1] => Array 
       ( 

           [0] => <a href='images/high_resolution/479_August2010-ENSO-SSTAnom.jpg'><img style='max-width:900px;' src='images/low_resolution/479_August2010-ENSO-SSTAnom.jpg' alt='ENSO Cycle: La Niña Conditions Returns in July 2010' /></a>
           [1] => <table><tr><td><a href='MediaDetail.php?MediaID=479&amp;MediaTypeID=1'><img src='images/thumbnails/479_August2010-ENSO-SSTAnom-NoLabel-T.jpg' alt='ENSO Cycle: La Niña Conditions Returns in July 2010' /></a>
           [2] => <big><big>ENSO Cycle: La Niña Conditions Returns in July 2010</big></big> -->
           [3] => Conditions favoring the reemergence of La Niña are developing in the equatorial Pacific.  During July, surface and subsurface ocean temperatures dropped well below the average, consistent with the formation of La Niña.  In this image, sea surface temperature anomalies are plotted for the first week of August, 2010.  Blue areas are indicative of cooler than average temperatures; red areas are warmer than average.  Notice the wave of cool temperatures in the equatorial Pacific.  These meandering anomalies are characteristic of La Niña.  Temperatures in the Atlantic, however, are much warmer than average.  These warm seas will fuel the intensification of hurricanes, whereas storms in the Pacific will be less likely to intensify due to the cooler temperatures.
           [4] => <a href='images/high_resolution/479_August2010-ENSO-SSTAnom.jpg'>View ENSO Cycle: La Niña Conditions Returns in July 2010 - High Resolution Version</a>
            [5] => <a href='images/high_resolution/479-82_August2010-ENSO-SSTAnom-NoLabel.jpg'>View Sea Surface Temperature Anomaly Image - No Labels - High Resolution Version</a>
           [6] => &nbsp; -->
            [7] => <a href='MediaHome.php?MediaTypeID=1'>Return to List</a> -->
        ) -->

*/
if(isset($matches[1])) {
	for ($i=0;$i<count($matches[1]);$i++) { // loop to find the title line and relative positions
	   if(preg_match('|<big><big>|is',$matches[1][$i])) {
		   break;
	   }
	}
	$rawImg = $matches[1][0];       // always in the [0] entry
	$rawTitle = $matches[1][$i];
	$IODimageTitle = strip_tags($rawTitle);
	$IODimageText = $matches[1][$i+1];
	$IODimageText = preg_replace('/[\r|\n]+/is','',$IODimageText);
	$IODimageHiRes = $matches[1][$i+2];
	if(!preg_match('|href=|is',$IODimageHiRes)) {
		$IODimageHiRes = '';
	} else {
  	  $IODimageHiRes = preg_replace('|href=\'|is',"href=\"$IODURL",$IODimageHiRes);
	  $IODimageHiRes = preg_replace('|\'>|is','">',$IODimageHiRes);
	}
	if(preg_match('|<img.*src=\'([^\']+)\'|Uis',$rawImg,$tmatch)) {
		$IODimageURL = $IODURL . $tmatch[1];
	    $Debug .= "found image at '$IODimageURL'<br/>\n";
	}
	if(!preg_match('/\.(png|jpg)$/i',$IODimageURL) ) {
		for ($j=4;$j<count($matches[1]);$j++) {
		  if(preg_match('|href=\'([^\']+)\'|i',$matches[0][$j],$tmatch) and 
			 preg_match('/\.(png|jpg)$/i',$tmatch[1]) ) {
		       $IODimageURL = $IODURL . $tmatch[1];
		       $Debug .= "image url not PNG. Substituting HiRes URL '$IODimageURL'<br/>\n";
			   break;
		  }
		}
	}
   // $Debug .= "<pre>".print_r($matches,true)."</pre>\n";
	
} else {

   $Debug .= "<pre>".print_r($matches,true)."</pre>\n";
   $Debug .= "unable to find image URL or text<br/>\n";
   print $Debug;
   return;
}

if(function_exists('iconv')) {
	error_reporting(E_ALL);
	$IODimageTitle = iconv($charsetInput,$charsetOutput.'//TRANSLIT',$IODimageTitle);
	$IODimageText = iconv($charsetInput,$charsetOutput.'//TRANSLIT',$IODimageText);
	$IODimageHiRes = iconv($charsetInput,$charsetOutput.'//TRANSLIT',$IODimageHiRes);
	$Debug .= "$charsetInput to $charsetOutput conversion done<br/>\n";
}
	
if(isset($IODimageURL)) {
// get dates
	$GraphicTime = file_exists($IODdir.$IODimageCacheFS)?filemtime($IODdir.$IODimageCacheFS):0;
	$CacheTime = file_exists($IODdir.$IODimageCache)?filemtime($IODdir.$IODimageCache):0;
	$Headers = getHTTPheaders($IODimageURL,1);
//	print_r($Headers);
	$URLdate = strtotime($Headers['last-modified']);
	
	if ($doRefresh or $URLdate > $GraphicTime) {
	    $Debug .= "$IODdir$IODimageCacheFS is stale .. downloading new copy from $IODimageURL<br/>\n";
	    download_file($IODimageURL,$IODdir.$IODimageCacheFS);
	}
	$GraphicTime = filemtime($IODdir.$IODimageCacheFS);

    if ($doRefresh or $GraphicTime > $CacheTime) {
	  $Debug .= "$IODdir$IODimageCache is stale .. regenerating<br/>\n";
	  if(preg_match('|\.jpg$|i',$IODimageURL)) {
         $image = loadJPEG($IODdir.$IODimageCacheFS);  // fetch our full sized image
	  }
	  if(preg_match('|\.png$|i',$IODimageURL)) {
         $image = loadPNG($IODdir.$IODimageCacheFS);  // fetch our full sized image
	  }
	  $MaxX = imagesx($image);
	  $MaxY = imagesy($image);
	  $ratio = $MaxX/$new_width;
	  $new_height = round($MaxY/$ratio,0);
	  $image_p = imagecreatetruecolor($new_width, $new_height);
      imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $MaxX, $MaxY);
      $tst = imagejpeg($image_p, $IODdir.$IODimageCache, 90);
	  if(!$tst) {
		  $Debug .= "$IODdir$IODimageCache NOT saved<br/>\n";
	  } else {
		  $Debug .= "$IODdir$IODimageCache saved as $new_width x $new_height px (from $MaxX x $MaxY)<br/>\n";
	  }
      imagedestroy($image); 
      imagedestroy($image_p); 
	}
	$sorryMsg = '';
} else {
	$sorryMsg = '<h3>Sorry... today\'s image is a movie. Please visit the <a href="'.$IODpageURL.'">NNVL site</a> to view it.</h3>';
}
/*    if (file_exists($Cache)) {
	  header("Content-type: image/jpeg"); // now send to browser
	  header("Last-modified: " . $Headers['last-modified']);
	  readfile($Cache);
	}
*/	
print "<div id=\"NOAAIOD\">\n";
print "<h2>$IODimageTitle</h2>\n";
if($sorryMsg == '') {
  print "<img src=\"$IODdir$IODimageCache\" alt=\"$IODimageTitle\" title=\"$IODimageTitle\" height=\"$new_height\" width=\"$new_width\" />\n";
} else {
  print $sorryMsg;
}
print "<p>$IODimageText</p>\n";
if(isset($IODimageHiRes)) {
  print "<p><small>$IODimageHiRes</small></p>\n";
}
print "<p><small>Courtesy of <a href=\"$IODpageURL\">NOAA Environmental Visualization Laboratory</a></small></p>\n";
print "</div>\n";

$Debug = "<!-- ". preg_replace('|\n|is'," -->\n<!-- ",$Debug);
$Debug .= "end of debug output -->\n";
$Debug = preg_replace('|<br/>|is','',$Debug);

print $Debug;



    return;




function loadJPEG ($imgname) { 
   global $Debug;
   $im = imagecreatefromjpeg ($imgname); /* Attempt to open */ 
   if (!$im) { /* See if it failed */ 
       $Debug .= "Error loading $imgname<br/>\n";
   } 
   return $im; 
} 
function loadPNG ($imgname) { 
   global $Debug;
   $im = imagecreatefrompng ($imgname); /* Attempt to open */ 
   if (!$im) { /* See if it failed */ 
       $Debug .= "Error loading $imgname<br/>\n";
   } 
   return $im; 
} 

function getHTTPheaders($url,$format=0) {
  global $Debug;
  $url_info=parse_url($url);
  $port = isset($url_info['port']) ? $url_info['port'] : 80;
  $fp=fsockopen($url_info['host'], $port, $errno, $errstr, 30);
  if($fp) {
    $head = "HEAD ".@$url_info['path']."?".@$url_info['query'];
    $head .= " HTTP/1.0\r\nHost: ".@$url_info['host']."\r\n\r\n";
    fputs($fp, $head);
    while(!feof($fp)) {
      if($header=trim(fgets($fp, 8192))) {
        if($format == 1) {
          $h2 = explode(': ',$header);
// the first element is the http header type, such as HTTP/1.1 200 OK,
// it doesn't have a separate name, so we have to check for it.
          if($h2[0] == $header) {
            $headers['status'] = $header;
          } else {
            $headers[strtolower($h2[0])] = trim($h2[1]);
          }
        } else {
          $headers[] = $header;
        }
     
      }
    }
	fclose($fp);
    return $headers;
  } else {
    return false;
  }
} // end of get_headers function def

function getHTTPpageIOD ($url, $useFopen=false) {
  global $Debug;
  if (! $useFopen) {
   // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed
   $numberOfSeconds=4;   

   // Suppress error reporting so Web site visitors are unaware if the feed fails
   //error_reporting(0);

   // Extract resource path and domain from URL ready for fsockopen
   $FullUrl = $url;
   $url = str_replace("http://","",$url);
   $urlComponents = explode("/",$url);
   $domain = $urlComponents[0];
   $resourcePath = str_replace($domain,"",$url);
   $Debug .= "GET $resourcePath HTTP/1.1 <br/>\n      Host: $domain <br/>\n";

   // Establish a connection
   $socketConnection = fsockopen($domain, 80, $errno, $errstr, $numberOfSeconds);

   if (!$socketConnection)
       {
       // You may wish to remove the following debugging line on a live Web site
          $Debug .= "Network error: $errstr ($errno) <br/>\n";
       }    // end if
   else    {
       $xml = '';
	   $getString = "GET $resourcePath HTTP/1.1\r\nHost: $domain\r\nConnection: close\r\nUser-agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11 global-conditions SaratogaWX)\r\n";
	   
	   $getString .= "\r\n";
       fputs($socketConnection, $getString);
   
       // Loop until end of file
       while (!feof($socketConnection))
           {
           $xml .= fgets($socketConnection, 8192);
           }    // end while

       fclose ($socketConnection);

       }    // end else

   return($xml);
 } else {
//   print "<!-- using file function -->\n";
   $xml = implode('',file($url));
   return($xml);
 }

   }    // end getHTTPpageIOD
// ------------------------------------------------------------------
	
function download_file($file_source, $file_target) {
  global $Debug;
  $rh = fopen($file_source, 'rb');
  if(!$rh) {
	  $Debug .= "unable to read $file_source<br/>\n";
  }
  $wh = fopen($file_target, 'wb');
  if(!$wh) {
	  $Debug .= "unable to write $file_target<br/>\n";
  }
  if ($rh===false || $wh===false) {
   // error reading or opening file
    return true;
  }
  while (!feof($rh)) {
    if (fwrite($wh, fread($rh, 1024)) === FALSE) {
          $Debug .= 'Cannot write to file ('.$file_target.')' ." <br/>\n";
          return true;
    }
  }
  fclose($rh);
  fclose($wh);
  // No error
  $Debug .= "loaded $file_target from $file_source<br/>\n";
  return false;
}

?>