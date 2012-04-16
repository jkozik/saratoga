<?php
global $doTestOnly;
$doTestOnly = false;

/*
PHP script by Mike Challis, www.carmosaic.com/weather
mesomap-update-utility.php
North American Weather Network mesomap, php, and image control file updater

Version 1.00 05-17-2008  test release
Version 1.01 05-18-2008  added feature (fix) write_mode_flag for download as text or binary
Version 1.02 05-19-2008  added helpful information "Why use it, What does it do, How to use it"
Version 1.03 05-19-2008  changed a bit to work with SWN also - K. True
Version 1.04 05-19-2008  First Release: added user selectable log file feature
Version 1.05 05-20-2008  updated the "How to use it" section in this file and the readme.
Version 1.06 05-20-2008  added error trap for failed fsockopen when checking new file times.
Version 1.07 05-21-2008  fixed remote location of mesomap graphic for SWN members - K. True
Version 1.08 05-21-2008  added many error traps with descritive errors for files not found, not readable, etc.
                         The program should exit on all the new error traps instead of trying to plow forward with warnings.
Version 1.09 05-21-2008  fixed the error check for HTTP/1.* 200 OK - K. True
Version 1.10 11-Aug-2009 added PHP5 support


*/
$Version = 'mesomap-update-utility.php Version 1.10  11-Aug-2009';
/*

Note: distribution of this program is limited to members of the
North American Weather Network (http://www.northamericanweather.net)
and all it regional network's members.
Distribution outside of the membership is prohibited.
Copyright 2006-2008, Mike Challis and Ken True - Saratoga-weather.org

################
# Why use it:
# ##############

Most network members run a copy of the network map PHP script on their weather web site.
It used to be that whenever a new member was added to the map at the parent network server,
all the member sites had to download a zip file and manually replace the files
or their map would not display the newest members.

By using this utility, the map image, map control file, and map php script
can self update from the parent network server.
When using this utility, the network member sites only have to install the map files once.


################
# What does it do?:
# ##############

It updates the XXXX_meso.jpg, XXXX-stations-cc.txt, and the XXXX-mesomap.php
It reads the *-mesomap-config.txt file on your web server and uses that info to
check out the 'freshness' of the map graphic, control file and PHP code,
then automatically updates if needed.

Tip: This script can be manually run from the web browser or from a cron schedule.
No parameters are required for the script.


################
# How to use it:
# ##############

1) Set your settings in the "Settings" section below,
the most important one is the first one, your network initials

2) Upload this file as mesomap-update-utility.php file in the same directory as your network mesomap files
XXXX_meso.jpg, XXXX-stations-cc.txt, XXXX-mesomap.php and XXXX-mesomap-config.txt
in this example XXXX is the prefix of the network files, ie: SWN, NWWN, NEWN, etc.
mesomap-update-utility.php will pick up other needed settings from the XXXX-mesomap-config.txt

3) Test the script by visiting your site URL to the php file
like this: http://www.yoursite.com/mesomap-update-utility.php

Sample output when no updates were needed:
NWWN_meso.jpg does not need to be updated
NWWN-stations-cc.txt does not need to be updated
NWWN-mesomap.php does not need to be updated
nothing was updated
done

Sample output when all 3 updates were needed:
downloading a newer NWWN_meso.jpg
downloading a newer NWWN-stations-cc.txt
downloading a newer NWWN-mesomap.php
3 files were updated
done

4) Visit the mesomap-update-utility.php URL regularly to make sure your map stays up to date
with new WX stations that are added.
Once a day is probably enough.

5)OPTIONAL, Schedule automatic timed updates: There are many ways to do this, method 1 is probably easiest.

Method 1: Use a program like System scheduler to ping the URL
http://www.yourwebsite.com/mesomap-update-utility.php once a day, or whenever you want
System scheduler:
http://www.splinterware.com/products/wincron.htm

Method 2: If you already have weather-display software click on
Control Panel | FTP & Connections | HTTP Download tab
Click on #1,#2 or #3 setup button | switch on the button for "downloads on
| enter your mesomap-update-utility.php url in the box called
"URL of file to be downloaded" | click "add to list"
| check the tick that says "Tick if text file"
| enter mesomap-update-utility.txt in the box that says
"Local filename to be downloaded"
| click "add to list" | click "download hourly" (or for once a day,
click "clear all" then click the 00:00 in the right column so it then shows in the left column)
| click "save now" | click OK

Method 3: Schedule a server cron job to run this php file five minutes after midnight, every day.
Example using crontab -e: (first remove the # from the line below and change /path/to/ to your server path)
# 5 0 * * * php /path/to/mesomap-update-utility.php 2>&1 > /dev/null
If your site uses a control panel you may have a different method of setting up cron jobs.
see also http://en.wikipedia.org/wiki/Cron

NOTE!! if you get PHP warning errors during an update:
warning: fopen(./XXXX_meso.jpg) "failed to open stream: Permission denied"
try changing the folder permissions CHMOD to 755, or on some servers 777
NOTE, you may not find out there is a permissions problem until an update occurs.
How to Change File Permissions (chmod) with FTP...
http://www.stadtaus.com/en/tutorials/chmod-ftp-file-permissions.php

Tip: If you change a setting you might not know if the setting works until
new map files are available for update.

Tip: changes are logged to a file if you set $use_log_file = true; (default) mesomap-update-log.txt
Anytime after an update, view the log at
http://www.yourwebsite.com/mesomap-update-log.txt

If you find any bugs, or can think of other possible settings/features for future releases:
Post in this forum: http://www.northamericanweather.net/forum/index.php?topic=29.0
Contact Mike - http://www.carmosaic.com/weather/contact_us.php


*/
##############
# begin settings -------------------------------------------------
##############

$network = 'MWWN'; // prefix initials of your network files, usually in ALL CAPS, ie: SWN, NWWN, NEWN, etc.

$update_php_file = true;   // set to true to allow updating XXXX-mesomap.php
# mesomap image and control file are always allowed to update

# NOTE!! a copy of XXXX-mesomap.php must be named XXXX-mesomap.txt on the remote network
# or it will NEVER be downloaded. Severs do not allow .php files to be "downloaded".
# Do not even name it XXXX-mesomap.php.txt because it will not work either.

$download_temp_first = true; // download as a .tmp file and rename (recommended)
# if you get errors during an update: warning: fopen(./XXXX_meso.jpg.tmp) "failed to open stream: Permission denied"
# try changing the folder permissions CHMOD to 0777, or setting this to false

$use_log_file = true;  // set to true to allow a mesomap-update-log.txt to log updates.

$chmod = false; // # Set permissions of downloaded files, disabled by default, most servers do not need it.
# if your map files update with permissions less than 0644 and then refuse to display,
# then you need to CHMOD them manually back to 0644, then have this setting set to true before the next update

$chmod_php_perms = 0644; // what perms do you want XXXX-mesomap.php file chmod, ie: 0644, 0775, 0777
# only used when $chmod = true;
# if your site requires PHP files to have a permissions greater than 0644,
# then you need to change this setting to whatever you need, ie: 0644, 0775, or 0777

##############
# end of settings, unless you changed default file names
##############

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

echo "$Version - for network=$network<br/>\n";

// include the settings file if it exists, or croak
if (file_exists("$network-mesomap-config.txt")) {
  require_once("$network-mesomap-config.txt");
} else {
  echo "settings file $network-mesomap-config.txt not found, exited\n";
  exit;
}

# $masterCacheURL = 'http://northwesternweather.net/NWWN-conditions.txt';
# find http://northwesternweather.net/ in http://northwesternweather.net/NWWN-conditions.txt
  $URL = parse_url($masterCacheURL);          // Ken True .. parse_url instead of explode
  $network_url = 'http://' . $URL['host'] . '/';

# Local Directory -
# directory the mesomap image, mesomap php file, and control file will be saved as on your server
 $localDirectory = '';

# $Graphic = './NWWN_meso.jpg';
$localImageName = str_replace('./','',$Graphic);

# The file name of the stations control file on your server XXXX-stations-cc.txt
# $LinksFile = 'NWWN-stations-cc.txt';
 $localccFile    = $LinksFile;

# The file name of the mesomap file on your server XXXX-mesomap.php
 $localphpFile   = "$network-mesomap.php";

# Full URL of the mesomap image you are downloading?
 $remoteImageURL = "$network_url$Graphic";
 if ($network == 'SWN') {
   $remoteImageURL = "$network_url" . 'swn_maps/swn_meso.jpg';  // force subdirectory
 }

# Full URL of the stations control file you are downloading?
 $remoteccFileURL = "$network_url$network-stations-cc.txt";

# Full URL of the stations control file you are downloading?
 $remotephpFileURL = "$network_url$network-mesomap.txt";

##############
# end of settings -------------------------------------------------
##############

// mesomap and image control file self updater
// Author: Mike Challis www.carmosaic.com

$updated_something = 0;
$log_file_string = '';

if ($use_log_file) {
# Set timezone in PHP5/PHP4 manner
  if (!function_exists('date_default_timezone_set')) {
	  if (! ini_get('safe_mode') ) {
		 putenv("TZ=$ourTZ");  // set our timezone for 'as of' date on file
	  }  
#	$Status .= "<!-- using putenv(\"TZ=$ourTZ\") -->\n";
    } else {
	date_default_timezone_set("$ourTZ");
#	$Status .= "<!-- using date_default_timezone_set(\"$ourTZ\") -->\n";
   }
   $logTimeFormat = 'Y-m-d H:i:s T, l'; // 2006-03-31 14:03:22 TZone, Saturday
   $log_file_timestamp  = date($logTimeFormat,time());
}

$myImage    = $localDirectory . $localImageName;
$myccFile   = $localDirectory . $localccFile;
$myphpFile  = $localDirectory . $localphpFile;

is_file_check($myImage);
is_file_check($myccFile);
is_file_check($myphpFile);

# look for new XXXX_meso.jpg
do_update($myImage, $remoteImageURL, 0644, 'binary');

# look for new XXXX-stations-cc.txt
do_update($myccFile, $remoteccFileURL, 0644, 'text');

# look for new XXXX-mesomap.php if setting is enabled
if ($update_php_file) {
  do_update($myphpFile, $remotephpFileURL, $chmod_php_perms, 'text');
}

if ($updated_something > 0) {
        echo "$updated_something files were updated<br/>\n";
        if ($use_log_file) {
          log_file();
        }
} else {
        echo "nothing was updated<br/>\n";
}
echo 'done';
exit;

function is_file_check($file) {
   if (!is_file("$file")) {
     echo "Fatal ERROR at is_file_check: exiting<br />\n";
     echo "Could not find the member's '$file' file, perhaps a setting or path is wrong<br/>\n";
     exit;
   }
}

function do_update($myFile, $remoteURL, $chmod_perms) {
  global $doTestOnly, $updated_something, $use_log_file, $log_file_string, $log_file_timestamp;

  if ($doTestOnly) {
    echo "<br><br>myFile: $myFile, remoteURL: $remoteURL<br>";
  }
  # compare local and remote files to see if a newer one is available
  $myFileTime = filectime("$myFile");
  $Headers = getHTTPheaders($remoteURL,1);
  // print_r($Headers);
  $remoteURLtime = strtotime($Headers['last-modified']);

  if ($remoteURLtime > $myFileTime) {
         # download new file because
         # last-modified timestamp changes at the source
          echo "downloading a newer $myFile<br>\n";
          download_file($myFile, $remoteURL, $chmod_perms);
          $use_log_file and $log_file_string .= "$log_file_timestamp || updated $myFile\n";
          $updated_something++;
  } else {
          echo "$myFile does not need to be updated<br/>\n";
  }

}

function getHTTPheaders($url,$format=0) {
  $url_info=parse_url($url);
  $port = isset($url_info['port']) ? $url_info['port'] : 80;
  $fp=fsockopen($url_info['host'], $port, $errno, $errstr, 30);
  if($fp) {
    $head = "HEAD ".@$url_info['path']."?".@$url_info['query'];
    $head .= " HTTP/1.1\r\nHost: ".@$url_info['host']."\r\nConnection: close\r\n\r\n";
    fputs($fp, $head);
    while(!feof($fp)) {
      if($header=trim(fgets($fp, 1024))) {
        if($format == 1) {
          $h2 = split(': ',$header);
// the first element is the http header type, such as HTTP/1.1 200 OK,
// it doesn't have a separate name, so we have to check for it.
          if($h2[0] == $header) {
            $headers['status'] = $header;
              if (! preg_match('|HTTP/1.* 200 OK|i',$header)) {
               echo "Fatal ERROR at getHTTPheaders: fetching timestamp failed for URL: $url with status: $header, exiting<br />\n";
               exit;
              }
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
          echo "Fatal ERROR at fsockopen: Failed opening http socket connection to check $url for new file time, exiting<br />\n";
          exit;
  }
} # end of getHTTPheaders function

function download_file($file_target, $file_source, $chmod_perms = 0644, $write_mode_flag = 'wb') {
  global $download_temp_first, $chmod, $doTestOnly;
  if($doTestOnly) {
    echo "Test mode: update '$file_target' from '$file_source'<br/>\n";
    return false;
  }
  if ($download_temp_first) {
     $file = $file_target;
     $file_temp = $file_target . '.tmp';
     $file_target = $file_temp;
  }
  $write_mode = 'wb';
  if ($write_mode_flag == 'binary') $write_mode = 'wb';
  if ($write_mode_flag == 'text')   $write_mode = 'w';
  $rh = fopen($file_source, 'rb');
  if (!$rh){
       echo "Fatal ERROR at download_file: exiting<br />\n";
       echo "Could not find or read the '$file_source' file<br/>\n";
       exit;
  }
  $wh = fopen($file_target, $write_mode);
  if (!$wh){
       echo "Fatal ERROR at download_file: exiting<br />\n";
       echo "Could not open or write the '$file_target' file<br/>\n";
       exit;
  }
  while (!feof($rh)) {
    if (fwrite($wh, fread($rh, 8192)) === FALSE) {
          echo "Fatal ERROR at download_file: exiting<br />\n";
          echo "While trying to download update: '$file_target' from '$file_source'<br/>\n";
          exit;
    }
  }
  fclose($rh);
  fclose($wh);

  if ($download_temp_first) {
     # rename the .tmp file to the file name we really want
     unlink($file);
     rename($file_target, $file);
     if ($chmod) chmod($file, $chmod_perms);
  }
  // No error
  return false;
}

function log_file() {
   global $chmod, $log_file_string;

 if ($log_file_string != '') {
    # append to the log file file
    echo "writing to the mesomap-update-log.txt file<br/>\n";
    $fh = fopen('mesomap-update-log.txt', 'a');
    if (!$fh){
       echo "Fatal ERROR at log_file: exiting<br />\n";
       echo "Could not open and write the mesomap-update-log.txt file<br/>\n";
       exit;
    }
    fwrite($fh, $log_file_string);
    fclose($fh);
    if ($chmod) chmod('mesomap-update-log.txt', 0644);
  }
}

?>
