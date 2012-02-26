<?php
// Author: Ken True - webmaster-weather.org
// gen-VWStags.php 
// Purpose: read the tags.txt file from VWS and generate the VWStags.htx file for use with
//          VWS to substitute weather values for VWS tags in VWStags.php
//
// Version 1.00 - 06-Jan-2011 - Initial release
// --------------------------------------------------------------------------
// allow viewing of generated source
$Version = 'gen-VWStags.php - V1.04 - 13-Feb-2011';
$WXsoftware = 'VWS'; // do NOT change this
$defsFile = $WXsoftware . '-defs.php'; // do NOT change this .. name of definitions file

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

$inFile = 'tags.txt';

if(!file_exists($inFile)) {
	print "<h3>Program: $Version</h3>\n";
	print "<h1>Error: file '$inFile' not found.</h1>\n";
	print "<p>Make sure '$inFile' is in this same directory.<br/>You can create the file using VWS ";
	print "using the Internet, HTML Tags dialog.<br/>Then press 'Complete Tag List' and save the ";
	print "file as '$inFile'.<br/>Then upload it to your website in the same directory this script ";
	print "is in, and run this script again.</p>";
	return;
}

$showComments = false;
if(isset($_REQUEST['comments'])) {
	$showComments = (strtolower($_REQUEST['comments']) == 'yes')?true:false;
}

$rawrecs = file($inFile);
header("Content-type: text/plain");
print_start();
foreach ($rawrecs as $nrec => $rec) {
/*
input:
Parameters (Description)
^vxv001^              Wind Direction
^vhi001^              Wind Direction Daily Hi

output:
v xv001|^vxv001^|// Wind Direction:|:
v hi001|^vhi001^|// Wind Direction Daily Hi:|:

*/
  preg_match('|^(\S+)\s+(.*)$|i',$rec,$matches);
  $varname = $matches[1];
  $descr = $matches[2];
  if(substr($varname,0,1) <> '^') { continue; }
  $ourname = substr($varname,1,1) . ' ' . substr($varname,2,strlen($varname)-3); // strip leading/trailing ^
  $comment = '';
  if($showComments) {
	  $comment = '|// '.$descr;
  }
  $t = "$ourname|$varname$comment:|:\n";
  if(preg_match('|vzs\d\d\d|',$varname)) { // replace missing vzlnnn values in tags saved via VWS.. strange
  
    $varname = preg_replace('|vzs|','vzl',$varname);
    if($showComments) {
	  $comment = preg_replace('| Time|i','',$comment);
	  $comment .= ' (added - missing in tags.txt)';
	} else {
	  $comment = '';
	}
	$ourname = substr($varname,1,1) . ' ' . substr($varname,2,strlen($varname)-3); // strip leading/trailing ^
    print "$ourname|$varname$comment:|:\n";
  }
  if(preg_match('|almnormlo\d|',$varname)) { // replace missing vzlnnn values in tags saved via VWS.. strange
  
    $varname = preg_replace('|almnormlo|','almnormhi',$varname);
    if($showComments) {
	  $comment = preg_replace('| Lo|i',' High',$comment);
	  $comment .= '(added  - missing in tags.txt)';
	} else {
	  $comment = '';
	}
	$ourname = substr($varname,1,1) . ' ' . substr($varname,2,strlen($varname)-3); // strip leading/trailing ^
    print "$ourname|$varname$comment:|:\n";
  }
  print $t;	
  
	
}
print_end();

// end of mainline
function print_start() {
	global $Version,$WXsoftware,$defsFile,$inFile;
print '<?php
/*
 File: VWStags.htx 

 Purpose: load VWS variables into a $WX[] array for use with the Canada/World/USA template sets

 Instructions:  
 Save this page as VWStags.htx and place in your c:\vws\templates directory
 
 Use the VWS, Internet, HTML Settings panel and place a new entry to process as:

 c:\vws\template\VWStags.htx               c:\vws\root\VWStags.php

 then use the VWS, Internet, FTP Send(upload) File panel to  have

 c:\vws\root\VWStags.php                   /

 so that the processed file VWStags.php is uploaded to your website.

 Author: Ken True - webmaster@saratoga-weather.org

';
print " (created by $Version)\n";

print "\n These tags generated on ".gmdate('Y-m-d H:m:s T',time())."\n";
print "   From $inFile updated ".gmdate('Y-m-d H:m:s T',filemtime($inFile))."\n\n";
print '*/
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
' . 
"\$WXsoftware = '$WXsoftware';  
\$defsFile = '$defsFile';  // filename with \$varnames = \$WX['$WXsoftware-varnames']; equivalents\n " .
'
// note the embedded space in the first field.. it\'s there to prevent VWS from substituting
// a value for the name of the variable.  D\'Oh.. Apparently, whenever it finds the string
// of characters matching one of it\'s variable names, it does a substitution, even without
// the surrounding caret (^) characters.
$rawdatalines = <<<END_OF_RAW_DATA_LINES
';
}

function print_end() {
global $WXsoftware, $defsFile,$showComments;	
print 'END_OF_RAW_DATA_LINES;

// end of generation script

// put data in  array
//
$WX = array();
global $WX;
$WXComment = array();
$data = explode(":|:",$rawdatalines);
$nscanned = 0;
foreach ($data as $v => $line) {
  list($vname,$vval,$vcomment) = explode("|",trim($line).\'|||\');
  $vname = substr($vname,0,1) . substr($vname,2);
  if ($vname <> "") {
    $WX[$vname] = trim($vval);
    if($vcomment <> "") { $WXComment[$vname] = trim($vcomment); }
  }
  $nscanned++;
}
if(isset($_REQUEST[\'debug\'])) {
  print "<!-- loaded $nscanned $WXsoftware \$WX[] entries -->\n";
}

if (isset($_REQUEST["sce"]) and strtolower($_REQUEST["sce"]) == "dump" ) {

  print "<pre>\n";
  print "// \$WX[] array size = $nscanned entries.\n";
  foreach ($WX as $key => $val) {
	  $t =  "\$WX[\'$key\'] = \'$val\';";
	  if(isset($WXComment[$key])) {$t .=  " $WXComment[$key]"; }
	  print "$t\n";
  }
  print "</pre>\n";

}
if(file_exists("'.$defsFile.'")) { include_once("'.$defsFile.'"); }
?>';
}
?>