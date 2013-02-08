<?php
// PHP script by Ken True, webmaster@saratoga-weather.org
// radar-status.php  
//  Version 1.00 - 21-Jan-2008 - Inital release
//  Version 1.01 - 30-Jan-2008 - handle improperly terminated message
//  Version 1.02 - 25-Feb-2008 - integrate with carterlake/WD/PHP template settings control
//  Version 1.03 - 07-Apr-2008 - fixed $SITE['showradarstatus'] actions for true=show, false=suppress
//  Version 1.04 - 04-Feb-2009 - fixed for NWS site format change
//  Version 1.05 - 03-Jul-2009 - added support for PHP5 timezone setting
//  Version 1.06 - 26-Jan-2011 - added support for $cacheFileDir global cache directory
//  Version 1.07 - 31-Aug-2012 - removed excess \n characters in messages and handling for chunked response
//  Version 1.08 - 22-Jan-2013 - fixed deprecated function errata
    $Version = "radar-status.php V1.08 22-Jan-2013";
//  error_reporting(E_ALL);  // uncomment to turn on full error reporting
// script available at http://saratoga-weather.org/scripts.php
//  
// you may copy/modify/use this script as you see fit,
// no warranty is expressed or implied.
//
// Customized for: NOAA radar status from
//   http://weather.noaa.gov/monitor/radar/
//
//
// output: creates XHTML 1.0-Strict HTML page (default)
// Options on URL:
//      inc=Y    -- returns only the body code for inclusion
//                         in other webpages.  Omit to return full HTML.
// example URL:
//  http://your.website/radar-status.php?inc=Y
//  would return data without HTML header/footer 
//
// Usage:
//  you can use this webpage standalone (customize the HTML portion below)
//  or you can include it in an existing page:
//  no parms:  $doIncludeRS = true; include("radar-status.php"); 
//  parms:    include("http://your.website/radar-status.php?inc=Y");
//
//
// settings:  
//  change myRadar to your local NEXRAD radar site ID.
//    other settings are optional
// 
  $myRadar = 'LOT';   // old San Francisco
//
  $noMsgIfActive = true; // set to true to suppress message when radar is active
//
  $ourTZ   = 'America/Chicago'; // timezone
  $timeFormat = 'D, d-M-Y g:ia T';
//
// boxStyle is used for <div> surrounding the output of the script .. change styling to suit.
  $boxStyle = 'style="border: dashed 1px black; background-color:#FFFFCC; margin: 5px; padding: 0 5px;"';  
//
  $cacheFileDir = './';   // default cache file directory
  $cacheName = "radar-status.txt";  // used to store the file so we don't have to
//                          fetch it each time
  $refetchSeconds = 60;     // refetch every nnnn seconds
// end of settings

// Constants
// don't change $fileName or script may break ;-)
  $fileName = "http://weather.noaa.gov/monitor/radar/";
// end of constants
// ---------------------------------------------------------
// overrides from Settings.php if available
global $SITE;
if (isset($SITE['GR3radar'])) 	{$myRadar = $SITE['GR3radar'];}
if (isset($SITE['tz'])) 		{$ourTZ = $SITE['tz'];}
if (isset($SITE['timeFormat'])) {$timeFormat = $SITE['timeFormat'];}
if (isset($SITE['showradarstatus'])) {$noMsgIfActive = ! $SITE['showradarstatus'];}
if (isset($SITE['cacheFileDir']))     {$cacheFileDir = $SITE['cacheFileDir']; }
// end of overrides from Settings.php if available

// ------ start of code -------
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

// Check parameters and force defaults/ranges
if ( ! isset($_REQUEST['inc']) ) {
        $_REQUEST['inc']="";
}
if (!isset($doIncludeRS) ) { $doIncludeRS = true; }

if (isset($doIncludeRS) and $doIncludeRS ) {
  $includeMode = "Y";
 } else {
  $includeMode = $_REQUEST['inc']; // any nonblank is ok
}

if ($includeMode) {$includeMode = "Y";}

if (isset($_REQUEST['show']) ) { // for testing
  $noMsgIfActive = (strtolower($_REQUEST['show']) != 'active');
}

if (isset($_REQUEST['nexrad']) ) { // for testing

  $myRadar = substr(strtoupper($_REQUEST['nexrad']),0,4);
}

if (isset($_REQUEST['cache'])) {$refetchSeconds = 1; }

$myRadar = strtoupper($myRadar);

// omit HTML <HEAD>...</HEAD><BODY> if only tables wanted	
// --------------- customize HTML if you like -----------------------
if (! $includeMode) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Refresh" content="300" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Radar Status for <?php print $myRadar ?> NEXRAD station</title>
</head>
<body style="background-color:#FFFFFF;">
<?php
}

// ------------- code starts here -------------------
echo "<!-- $Version -->\n";

// refresh cached copy of page if needed
// fetch/cache code by Tom at carterlake.org
$cacheName = $cacheFileDir . $cacheName;

if (file_exists($cacheName) and filemtime($cacheName) + $refetchSeconds > time()) {
      print "<!-- using Cached version of $cacheName -->\n";
      $html = implode('', file($cacheName));
    } else {
      print "<!-- loading $cacheName from $fileName -->\n";
      $html = fetchUrlWithoutHangingRS($fileName);
      $fp = fopen($cacheName, "w");
	  if ($fp) {
        $write = fputs($fp, $html);
         fclose($fp);  
         print "<!-- cache written to $cacheName. -->\n";
	  } else {
	     print "<!-- unable to save cache to $cacheName. -->\n";
	  }
}

# Set timezone in PHP5/PHP4 manner
  if (!function_exists('date_default_timezone_set')) {
	putenv("TZ=" . $ourTZ);
#	$Status .= "<!-- using putenv(\"TZ=$ourTZ\") -->\n";
    } else {
	date_default_timezone_set("$ourTZ");
#	$Status .= "<!-- using date_default_timezone_set(\"$ourTZ\") -->\n";
   }

if(preg_match('|Transfer-Encoding:\s+chunked|i',$html)) {
	print "<!-- chunked response, un-chunking -->\n";
    $data = substr($html, (strpos($html, "\r\n\r\n")+4));
	print "<!-- in=".strlen($data);
    $data = unchunkRS($data);
	print " bytes, unchunked=".strlen($data)." bytes -->\n";
	$html = $data;
//	$tfile = preg_replace('|\.txt$|','-unchunk.txt',$cacheName);
//	$fp = fopen($tfile,'w');
//	if ($fp) {
//		$write = fputs($fp,$data);
//		fclose($fp);
//		print "<!-- debug cache written to $tfile -->\n";
//	}
}

  // extract the updated date/time
//  print "<pre>\n";
  preg_match_all('|Radar Site Status as of (.*)</b>|Uis',$html,$matches);
//  print_r($matches);

  $UDate = $matches[1][0];
 // $UDate = 'Mon Jan 21 00:09:58 CUT 2008'
  $UDp = explode(" ",$UDate);
//  print_r($UDp);
 /*
 Array $UDp is now:
(
    [0] => Mon
    [1] => Jan
    [2] => 21
    [3] => 00:14:59
    [4] => CUT
    [5] => 2008
)
*/
  $UDate = $UDp[0] . ', ' . $UDp[2] . '-' . $UDp[1] . '-' . $UDp[5] . ' ' . $UDp[3] . ' UTC';
  $UTCdate = strtotime($UDate);
  $LCLdate = date($timeFormat,$UTCdate);
//  echo "<p>Updated: $LCLdate<br/>'$UDate'</p>\n";
  
/*  The data looks like this:
<td ALIGN=CENTER BGCOLOR="#FF0000" class = "  white "  id= whitelink><b>
KMUX</a></b><br>23:28:17</td> <!--:No Data:-->
<td ALIGN=CENTER BGCOLOR="#33FF33" class = " black"  id= blacklink><b>
KMVX</a></b><br>00:15:17</td>

*/
  preg_match_all('|<table.*>(.*)</table>|Uis',$html,$matches);
  $recs = explode("\n",$matches[1][0]);

  foreach ($recs as $n => $rec) {
   if (! preg_match("|^$myRadar|",$rec)) {continue;}
   
   $prec = $recs[$n-1];  // prior record
   preg_match_all('|bgcolor="([^"]+)"|is',$prec,$matches);
//   print_r($matches);
   $statColor = $matches[1][0];
//   print "statColor='$statColor'\n";
   preg_match_all('|^(.*)</a></b><br>(.*)</td>|is',$rec,$matches);
//   print_r($matches);
   $statRadar = $matches[1][0];
   $lastUTCtime = $matches[2][0];
   $lastUTCdate = $UDp[2] . '-' . $UDp[1] . '-' . $UDp[5] . ' ' . $lastUTCtime . ' UTC';
   $age = $UTCdate - strtotime($lastUTCdate);
   if ($age < 0) { $age += (60*60*24); } // account for one day extra downtime if need be
   $ageHMS = date('H:m:s',$age);
   
   preg_match_all('|<!--:(.*):-->|is',$rec,$matches);
   $curStatus = 'Active';
   if ($statColor <> '#33FF33') {
     $curStatus = 'Data not recent';
   }
   if (isset($matches[1][0])) {
     $curStatus = $matches[1][0];
   }
   
//   print "statRadar='$statRadar' lastUTCtime='$lastUTCtime' curStatus='$curStatus'\n";
   
//   print "$prec";
//   print "$rec";
   
    break;
//	print "$rec";  // this shouldn't print ever
  }


  // extract the messages
 preg_match('|<pre[^>]*>(.*)</pre>|Usi',$html,$matches);
 $messages = $matches[1];
 // now split up the messages and process
 $messages = preg_replace('|NOUS|Us','||NOUS',$messages) . '|'; // add message delimiters
 $messages = preg_replace('|ÿÿ|Uis',"\n||NOUSnn",$messages); // remove garbage characters.
 $messages = preg_replace('|ð|Uis','',$messages);  // remove garbage characters
 preg_match_all('!\|NOUS(.*)\|!Us',$messages,$matches); // split the messages
 $messages = $matches[1];  // now have array of messages in order
 
 $radarMsgs = array();  // for storing the messages in a 'cleansed' format by Radar key, then date
 foreach ($messages as $n => $msg) {
 
 /* a $msg looks like this:
 66 KMTR 200618
FTMMUX
MESSAGE DATE:  JAN 20 2008 06:17:55
KMUX SAN FRANCISCO RADAR IS EXPERIENCING INTERMITTENT DATA FLOW
INTERUPTIONS. TROUBLE-SHOOTING PROCEDURES ARE CURRENTLY UNDERWAY TO
DETERMINE THE PROBLEM AND RESTORE NORMAL OPERATIONS.
*/
   $msgline = explode("\n",$msg);  // get 'em separated into individual lines.
   $t = explode(' ',trim($msgline[0]));
   $thisRadar = $t[1];
   $thisTD = $t[2];
   if (substr($thisRadar,1,3) != substr($msgline[1],3,3)) { // sometimes one reports for another
     $thisRadar = substr($thisRadar,0,1) . substr($msgline[1],3,3);
   }
   
   preg_match('|date:\s+(.*)|i',trim($msgline[2]),$matches);
   $istart = 3;
   if (!isset($matches[1])) {
   // oops.. no message line
     $istart--;
 /*
 use the Updated UTC to 'fill in the blanks' from the header line
(
    [0] => Mon
    [1] => Jan
    [2] => 21
    [3] => 00:14:59
    [4] => CUT
    [5] => 2008
)
*/
     $tdate = substr($thisTD,0,2) . '-' . $UDp[1] . '-' . $UDp[5] . ' ' .
	          substr($thisTD,2,2) . ':' . substr($thisTD,4,2) . ':00 UTC';
	 $thisDate = strtotime($tdate);
	
	} else { 
   
      $thisDate = strtotime($matches[1] . ' UTC');
	}
	
   $thisMsg = '';
   for ($i=$istart;$i<count($msgline);$i++) { $thisMsg .= $msgline[$i] . "\n"; };
   
   $thisMsg = preg_replace("|\n|is",'',$thisMsg);
   $radarMsgs[$thisRadar][$thisDate] = $thisMsg; // save away for later lookup
 
 
 }
 
// print_r($radarMsgs);

/*  $radarMsgs now looks like this:
    [KVNX] => Array
        (
            [1200854014] => THE KVNX WSR-88D WILL BE DOWN FOR A BRIEF PERIOD BETWEEN 1840 AND 1900 UTC FOR R
EQUIRED MAINTENANCE.  AT WFO/OUN, 1833 UTC - 1/20/08


            [1200857882] => THE KVNX WSR-88D HAS BEEN RETURNED TO SERVICE.  AT WFO/OUN, 1935 UTC - 1/20/08  


        )

    [KMUX] => Array
        (
            [1200809875] => KMUX SAN FRANCISCO RADAR IS EXPERIENCING INTERMITTENT DATA FLOW
INTERUPTIONS. TROUBLE-SHOOTING PROCEDURES ARE CURRENTLY UNDERWAY TO
DETERMINE THE PROBLEM AND RESTORE NORMAL OPERATIONS.


            [1200890280] => KMUX SAN FRANCISCO RADAR IS CONTINUING TO EXPERIENCE INTERMITTENT
DATA FLOW INTERUPTIONS. TROUBLE-SHOOTING PROCEDURES CONTINUE.
PROBLEMS ARE LIKELY WITH TELCO CONNECTIONS AND VERIZON TECHNICIANS
WILL RESUME WITH THEIR PROCESSES ON MONDAY.


        )
*/

// Output the status

  if (!$noMsgIfActive or $statColor != '#33FF33' ) {
  print "<div $boxStyle>\n";
  print "<p>NEXRAD Radar $myRadar status: <span style=\"background-color: $statColor; padding: 0 5px;\">$curStatus</span> [last data $age secs ago] as of $LCLdate</p>\n";
  
  if (isset($radarMsgs[$myRadar])) {
     foreach ($radarMsgs[$myRadar] as $timestamp => $msg) {
	   $msg = htmlspecialchars($msg);
	   $msg = preg_replace('|\n|is',"<br/>\n",$msg);
	   print "<p>Message date: " . date($timeFormat,$timestamp) . "<br/>\n";
	   print $msg . "</p>\n";
     }
  } 
  
  
  $niceFileName = preg_replace('!&!is','&amp;',$fileName);
  print "<p><small><a href=\"$niceFileName\">NWS WSR-88D Transmit/Receive Status</a></small></p>\n";
  print "</div>\n";
  } // end suppress if radar active and $noMsgIfActive == true
 else {
 
   print "<!-- NEXRAD Radar $myRadar status: $curStatus [last data $age secs ago] as of $LCLdate -->\n";
  if (isset($radarMsgs[$myRadar])) {
     foreach ($radarMsgs[$myRadar] as $timestamp => $msg) {
	   $msg = htmlspecialchars($msg);
	   print "<!-- Message date: " . date($timeFormat,$timestamp) . "\n";
	   print $msg . " -->\n";
     }
  } 

 
  }


// print footer of page if needed    
// --------------- customize HTML if you like -----------------------
if (! $includeMode ) {   
?>

</body>
</html>

<?php
}

// ----------------------------functions ----------------------------------- 
 
 function fetchUrlWithoutHangingRS($url) // thanks to Tom at Carterlake.org for this script fragment
   {
   // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed
   $numberOfSeconds=4;   

   // Suppress error reporting so Web site visitors are unaware if the feed fails
   error_reporting(0);

   // Extract resource path and domain from URL ready for fsockopen

   $url = str_replace("http://","",$url);
   $urlComponents = explode("/",$url);
   $domain = $urlComponents[0];
   $resourcePath = str_replace($domain,"",$url);

   // Establish a connection
   $socketConnection = fsockopen($domain, 80, $errno, $errstr, $numberOfSeconds);

   if (!$socketConnection)
       {
       // You may wish to remove the following debugging line on a live Web site
          print "<!-- Network error: $errstr ($errno) -->\n";
       }    // end if
   else    {
       $xml = '';
       fputs($socketConnection, "GET $resourcePath HTTP/1.1\r\nConnection: close\r\nHost: $domain\r\n\r\n");
   
       // Loop until end of file
       while (!feof($socketConnection))
           {
           $xml .= fgets($socketConnection, 4096);
           }    // end while

       fclose ($socketConnection);

       }    // end else

   return($xml);

   }    // end function

// unchunk a chunked HTTP response
   
   function unchunkRS($data) {
    $fp = 0;
    $outData = "";
    while ($fp < strlen($data)) {
        $rawnum = substr($data, $fp, strpos(substr($data, $fp), "\r\n") + 2);
        $num = hexdec(trim($rawnum));
        $fp += strlen($rawnum);
        $chunk = substr($data, $fp, $num);
        $outData .= $chunk;
        $fp += strlen($chunk);
    }
    return $outData;
}
   
// --------------end of functions ---------------------------------------

?>
