<?php
// wflash-filetest.php script by Ken True - webmaster@saratoga-weather.org
//
// Version 1.00 - 03-Nov-2007 - Initial release
// Version 1.01 - 04-Nov-2007 - added permission display function for config/data files
//
$Version = "wflash-filetest.php Version 1.01 - 04-Nov-2007";
//
 error_reporting(E_ALL);  // uncomment to turn on full error reporting
//
// script available at http://saratoga-weather.org/scripts.php
//  
// you may copy/modify/use this script as you see fit,
// no warranty is expressed or implied.
// ------------settings -- no need to change these -----------
// this file should be installed in the same directory as WeatherFlash
//
//
$testName = 'wflash-test.txt';    
$wflashDir = './';  // directory for the the WeatherFlash files
//                         will assume Data/ and Config/ directories below this dir.
//-------------end of settings-------------------------------

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
error_reporting(E_ALL);  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>PHP file writing test for WeatherFlash</title>
<style type="text/css">
body {
  background-color:#FFFFFF;
  font-family:Verdana, Arial, Helvetica, sans-serif;
  font-size: 12px;
}
</style>
</head>
<h1>Test for WeatherFlash file writing by PHP</h1>
<?php
   echo "<h2>PHP Version " . phpversion() ."</h2>";
   $errorcnt = 0;

   echo "<h2>Parsing site.txt configuration</h2>\n";
   
   $siteFile = implode('',file('site.txt')) . '&';
   if (strlen($siteFile) < 3) {
     echo "<h2>Warning: site.txt file missing or empty.</h2>\n";
	 echo "<p>Unable to continue test.</p>\n";
	 return;
   }
   $siteFileRecs = explode('&',$siteFile);

   $SiteConfig = array();
   
   echo "<pre>\n";
   foreach ($siteFileRecs as $siteRec) {
	 if (! trim($siteRec)) { continue; } // skip blank lines if any  
//     echo "'" . trim($siteRec) . "'\n";
	 list($key,$val) = split("=",trim($siteRec));
	 if ($val) {
	   $SiteConfig[$key] = $val;
	 }
   }
   print_r($SiteConfig);
   echo "</pre>\n";
 
 $ConfigDir = $SiteConfig['ConfigFolder'];
 $ConfigExt = $SiteConfig['ConfigfileSuffix'];
 $DataDir = $SiteConfig['DataFolder'];
 $ScriptsDir = $SiteConfig['ScriptsFolder']; 
 $KeyExt = $SiteConfig['KeyfileSuffix']; 
 $ScriptsExt = $SiteConfig['ScriptsSuffix'];
 if ($ScriptsExt <> 'php') {
   echo "<p><b>Warning: ScriptsSuffix set for '$ScriptsExt' not for 'php'.</b>/n";
   $errorcnt++;
 }
  
$wflashFiles = array(
"site.txt",
"index.htm",
"$ConfigDir/",
"$ConfigDir/Units.$ConfigExt",
"$DataDir/",
"$DataDir/wflash.txt",
"$DataDir/wflash2.txt",
"$ScriptsDir/",
"$ScriptsDir/wxf-submit.php"
);

echo "<h2>WeatherFlash files and permissions</h2>\n";
echo "<pre>\n";
foreach ($wflashFiles as $file) {
  $t = show_permissions($wflashDir . $file);
  echo  "$t\n";
  $t = explode(":",$t);
  if (count($t) < 3) { $errorcnt++; }
}	
echo "</pre>";
   
   echo "<h2>Now testing for write access to Data directory</h2>\n";
   $errorcnt += try_file($wflashDir . "$DataDir/" . $testName);

   echo "<h2>Now testing for write access to Config directory</h2>\n";
   $errorcnt += try_file($wflashDir . "$ConfigDir/" . $testName);
   	
   if ($errorcnt > 0) {
     $s = '';
	 $s = ($errorcnt>1)?'s':'';
	 
     echo "<h2>Test concluded .. $errorcnt error$s found.  See 'Warning:' message$s for details.</h2>\n";
   } else {
     echo "<h2>Test concluded .. no errors found. WeatherFlash data uploads should work correctly based on permissions.</h2>\n";
   }

echo "<p><small>Test script by <a href=\"http://saratoga-weather.org\">Saratoga-Weather.org</a>.<br/>\n";
echo "$Version available <a href=\"http://saratoga-weather.org/wflash/wflash-filetest.php?sce=view\">here</a>.</small></p>\n";   

function try_file( $filename) {
	$NOWgmt = time();
    $NOWdate = gmdate("D, d M Y H:i:s", $NOWgmt);
	echo "<p>Using $filename as test file.</p>\n";
	echo "<p>Now date='$NOWdate'</p>\n";
	$errors = 0;
	$fp = fopen($filename,"w");
	if ($fp) {
	  $rc = fwrite($fp,$NOWdate);
	  if ($rc <> strlen($NOWdate)) {
	    echo "<p>unable to write $filename: rc=$rc</p>\n";
		$errors++;
	  }
	  fclose($fp);
	} else {
	  echo "<p>Unable to open $filename for write.</p>\n";
	  $errors++;
	}
	if(file_exists($filename)) {
  		$contents = implode('',file($filename));
	
		echo "<p>File says='$contents'</p>\n";
		if ($contents == $NOWdate) {
	  		echo "<p>Write and read-back successful.. contents identical.</p>\n";
		} else {
	  		echo "<p>Read-back unsuccessful. contents different.</p>\n";
			$errors++;
		}
		if(unlink($filename)) {
		    echo "<p>Test file $filename deleted.</p>\n";
		}
	} else {
	  echo "<p>File $filename not found.</p>\n";
	  $errors++;
	}

  return($errors);
}

function show_permissions ($filename) {

if (! file_exists($filename) ) {
  return("----   ----------   <b>$filename  -- Warning: required WeatherFlash file not found</b>.");
}

$perms = fileperms($filename);

$infohex = substr(sprintf('%o', $perms), -4);

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

return ("$infohex : $info : $filename");


}

?>

<body>
</body>
</html>