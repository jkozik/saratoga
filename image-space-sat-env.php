<?php
/*
PHP script by Mike Challis, www.642weather.com/weather
Multifunction Image Handler PHP Script - multifunction-image-handler.php
Script available at: http://www.642weather.com/weather/scripts.php
Contact Mike: http://www.642weather.com/weather/contact_us.php
Live Demo: http://www.642weather.com/weather/scripts-image-handler.php#IHdemo
Version: 1.99.1  23-Oct-2009

See change log for complete history:
http://www.642weather.com/weather/scripts-history.php
Version: 1.99.1  23-Oct-2009  - fix: no longer requires allow_url_fopen to be enabled in PHP,
uses CURL as first method, if CURL is not available it falls back to fopen

You are free to use and modify the code for your own non-commercial use

This php code provided "as is", and Long Beach Weather (Michael Challis)
disclaims any and all warranties, whether express or implied, including
(without limitation) any implied warranties of merchantability or
fitness for a particular purpose. Warning: always make sure you have permission
to "hot link" images from other web sites. This script is designed to be a more
graceful way to link other site's images, asking for their permission,
and excluding their content from your copyright is absolutely recommended.

License:
    This work is licensed under a
    CREATIVE COMMONS ATTRIBUTION - NONCOMMERCIAL 3.0
    UNITED STATES LICENSE
    For Complete Details please go to:
    http://creativecommons.org/licenses/by-nc/3.0/us/

PHP version 4.3.9 or greater is recommended
NOTE: This script requires PHP installed using GD image support.
http://us2.php.net/manual/en/ref.image.php

This version only works for jpg, png, and gif files.
It will not work for any other file type.

Some code is borrowed from Ken True - resize nexstorm-image.php script
http://saratoga-weather.org/scripts.php

################
# What does it do?:
# ##############

Multifunction Image Handler PHP Script
This script is a very easy to use multifunction image handler script
for your weather web site. The caching feature makes it the perfect
solution to gracefully "hot link" an image from another web site.

Some of the many features include:
    * Supports .jpg .gif and .png, download an image, cache the image,
    display the image
    * Supports remote images or local images on your web server
    * Convert the image type(optional)
    * Crop the image(optional)
    * Resize the image(optional)
    * Resize the image preserving aspect ratio(optional)
    * Jpeg compression(optional)
    * Annotate text on the image, add up to 3 text overlays(optional)
    * Multi line text support(optional)
    * Text alignment options(optional)
    * Text margin options(optional)
    * Text color options(optional)
    * Auto archive/rotate images with or without thumbs,
    this feature can be utilized for web cam image archiving(optional)
    * Outputs most errors as an image with error code.

################
# Why use it:
# ##############

Reduces bandwidth consumption that can annoy webmasters:
Even when you have permission to "hot link" to another site's image,
if your site generates too many requests for the image, they may get
annoyed and tell you to stop.
This script will considerably lighten the bandwidth consumption load.
Make thumbnail size images from larger ones: I use this script to make
a thumbnail image of of my web cam image for a link on my main page,
it even puts the words "Live Cam" on it.
See sample here:
http://www.642weather.com/weather/image-webcam-thumb.php
.. and it offers many other optional desirable features mentioned
in the "What does it do" section above.
Can also be used to output an image with web cam text overlay with up to 3
different overlay areas
Live Demo: http://www.642weather.com/weather/scripts-image-handler.php#IHdemo

################
# How to use it:
# ##############

1) Note: Please use common courtesy...
get permission to use the image you will download and display.
It is also nice to give credit on your page as to the source of the image
and provide a link to the source if possible.

2) Set your settings in the section below

3) Upload the contents of this file as a .php file in the same directory
your image will display.
Example: you want a radar image ...  name it image-radar.php

4) Call the file in the html where you want the image displayed
<img src="image-radar.php" alt="Radar Image" width="200" height="200" />

5) If you use this script ... please let me know ..
http://www.642weather.com/weather/contact_us.php
I would also appreciate a link to my weather site on your links page ...
Long Beach, WA Weather
http://www.642weather.com/weather/

Tip: If you are loading more than one image with this script,
be sure to uniquely name this script for each image.
and be sure to uniquely name each image file name in the setting below

Tip: This script can also be manually run from the web browser
for diagnostic purposes. No parameters are required for the script.

Tip: If you change a setting like image size,
the change may not show up instantly because the image may be cached.
You can force a download reload of the image with ?reload=1
like this: http://www.yoursite.com/image-radar.php?reload=1

Tip: If you get a PHP error about file permissions,
try changing the folder permissions to 755, or on some servers 777
http://www.stadtaus.com/en/tutorials/chmod-ftp-file-permissions.php

*/
//error_reporting(E_ALL); // Report all errors and warnings (very strict, use for testing only)
//ini_set('display_errors', 1); // turn error reporting on
#################
# begin settings
# ##############

#####
# where is the image you are downloading?
#####
$ImageURL = 'http://www.swpc.noaa.gov/rt_plots/SatEnv.gif';
# note: both samples are correct as long as they are actual image files
# $ImageURL = 'http://www.somesite.com/radar_latest.php?site=kdmx&prod=reflectivity1';
# $ImageURL = 'http://www.642weather.com/weather/webcamimage.jpg';
# Must start with http

# Note: This program will always save 2 image files to your web server
# $localimgName is a raw (temp image) file of the image downloaded
# $outputimgName is the (output image) for the web browser
# the following 2 settings will determine the file names.

#####
# The (temp image) - file name the image will be saved as on your server
#####
$localimgName = 'image-space-sat-env.gif';
# note: use a unique name that is not a file on your server already
# or it will be OVERWRITTEN!
# note: only letters, numbers, dash, underscore, and dots allowed
# note: if the url being downloaded and the url of the file saved matches ..
# there will be an error so (use a different name for $localimgName)
# note: this script only supports .jpg .gif and .png
# note: this will convert png to jpg, png to gif, etc ..
# the extension you name it here is what it will be saved as
# note: If you are loading more than one image with this script,
# be sure to uniquely name this script for each image.
# Example: image-radar.php image-satellite.php
# ..and be sure to uniquely name each local image file
# Example:
# in image-radar.php,     name  $localimgName = 'image-radar-temp.jpg';
# in image-satellite.php, name  $localimgName = 'image-satellite-temp.jpg';

#####
# The (output image) - file name the image will be saved as on your server
#####
$outputimgName = 'image-space-sat-env-cached.gif';
# $localimgName and $outputimgName MUST HAVE SAME EXTENSION!
# note: use a unique name that is not a file on your server already
# or it will be OVERWRITTEN!
# note: If you are loading more than one image with this script,
# be sure to uniquely name this script for each image.
# Example: image-radar.php image-satellite.php
# ..and be sure to uniquely name each local image file
# Example:
# in image-radar.php,     name  $outputimgName = 'image-radar-cached.jpg';
# in image-satellite.php, name  $outputimgName = 'image-satellite-cached.jpg';

#####
# Local Directory - directory the images will be saved as on your server
#####
$localDirectory = './';
# use $localDirectory = './'; for same folder
# the folder must exist, you must make the folder yourself
# must end with a slash
# full path is recommended for cron scheduler use
# $localDirectory = '/var/www/html/weather/';

#####
# cache only option (to preserve animated GIFs when you do not need to resize, crop, or text overlay)
#####
$cache_only = 0; # just download and cache the file (ONLY!!), NO resize, NO crop, and NO text overlay
# Default set to 0 for no cache-only.
# note: setting this option $cache_only = 1;
# WILL BYPASS all other settings for resize, crop, and text overlay!!

#####
# resize options (uncomment only one of the settings below)
#####
$resize_setting = 1; # use $new_width and $new_height
// $resize_setting = 2; # use $new_width
// $resize_setting = 3; # use $new_height
// $resize_setting = 4; # use $percent only to calculate $new_width $new_height
// $resize_setting = 5; # no resize
# 1) manual: use $new_width and $new_height settings below
# 2) use $new_width,  auto adjust height based on aspect ratio
# 3) use $new_height, auto adjust width  based on aspect ratio
# 4) use $percent only, image is resized by it's value in percent (i.e. 50 to downsize by 50 percent)
# 5) no resize

#####
# new image size for graphic display
#####
$new_width  = 320; # ignored with   $resize_setting 3 or 4
$new_height = 240; # ignored with   $resize_setting 2 or 4
$percent    =  50; # only used for  $resize_setting 4

#####
# jpeg compression percentage
#####
$jpegcompress = 95; # (range of 50 - 95 recommended)
# Example: 50 will make a smaller file but with less detail quality
#          95 will make a larger file but with very good detail quality
# used with $localimgName as a .jpg only, ignored for .gif or .png

#####
# Basic crop square from center - crops the image from calculated center in a square of $cropSize pixels
#####
$cropSize = 0;
# Default set to 0 for no basic crop.
# Example: $cropSize = 300; crops the image from calculated center to be a 300 pixel square.
# crop functions are performed on the original image BEFORE resize functions.
# resultant image can still be resized based on image resize options settings above.
# do not use basic and advance crop settings at the same time.

#####
# Advanced crop - crops an image using $cropStartX and $cropStartY as the upper-left hand corner.
#####
$cropStartX = 0; # crop from X pixels from upper-left hand corner.
$cropStartY = 0; # crop from Y pixels from upper-left hand corner.
$cropWidth  = 0; # crop area width  in pixels
$cropHeight = 0; # crop area height in pixels
# Default set to 0 for no advanced crop.
# crop functions are performed on the original image BEFORE resize functions.
# resultant image can still be resized based on image resize options settings above.
# do not use basic and advance crop settings at the same time.
# tip: a photo editor program will give you pixel X,Y locations to determine your crop settings

#####
# When to download a new image? (uncomment only one of the settings below)
#####
$download_setting = 1; # by last-modified
//$download_setting = 2; # by time interval
//$download_setting = 3; # always
# 1) only when the image last-modified timestamp changes at the source,
# 2) at a preset time interval like every 5 minutes,
# 3) always: (download and resize every hit)(less recommended, but good for testing)

#####
# Download new image every nnn seconds: like 600 for 5 minutes
#####
# note: only used with $download_setting = 2;
$refetchSeconds = 600;

#####
# Annotate text on image (optional, useful for text overlay on your image)
#####
$text1 = ''; # leave empty for no text1
$text2 = ''; # leave empty for no text2
$text3 = ''; # leave empty for no text3
# Note: $text1 = ''; no text will apear on the image
# See sample here: http://www.642weather.com/weather/image-webcam-thumb.php
# Example: $text1 = 'Live Cam';
# Multiline text also supported ...
# $text1 = 'Live
# Cam';
# See sample here: http://www.642weather.com/weather/scripts-image-handler.php#IHdemo

# Text alignment
$textalign1 = 'upperleft';
$textalign2 = 'lowerleft';
$textalign3 = 'centerbottom';
# your textalign options are ...
# upperright upperleft lowerleft lowerright center centertop centerbottom
$xmargin1 = 5; # side margin in pixels
$xmargin2 = 5; # side margin in pixels
$xmargin3 = 5; # side margin in pixels
$ymargin1 = 5; # top or bottom margin in pixels
$ymargin2 = 5; # top or bottom margin in pixels
$ymargin3 = 5; # top or bottom margin in pixels

# Font style
$fontstyle1 = 5; # There are 5 built in GD fonts: 1 2 3 4 5 ONLY!
$fontstyle2 = 5; # There are 5 built in GD fonts: 1 2 3 4 5 ONLY!
$fontstyle3 = 5; # There are 5 built in GD fonts: 1 2 3 4 5 ONLY!
# $fontstyle GD fonts can be overridden with true type fonts, see below

# Font color in Hex
$fontcolor1 = 'FFFFFF'; # FFFFFF white
$fontcolor2 = 'FFFFFF'; # FFFFFF white
$fontcolor3 = 'FFFFFF'; # FFFFFF white
# Font drop shadow color in Hex
$fontshadowcolor1 = '808080'; # 808080 grey
$fontshadowcolor2 = '808080'; # 808080 grey
$fontshadowcolor3 = '808080'; # 808080 grey
# free online hex color picker http://www.colorschemer.com/online.html

# Override built in (Font style) fonts with a True Type Font
$ttfont1 = ''; # (READ NOTE BELOW!)
$ttfont2 = ''; # (READ NOTE BELOW!)
$ttfont3 = ''; # (READ NOTE BELOW!)
$fontsize1 = 13; # size for True Type Font1 only (8-18 recommended)
$fontsize2 = 13; # size for True Type Font2 only (8-18 recommended)
$fontsize3 = 13; # size for True Type Font3 only (8-18 recommended)
# Example: $ttfont1 = 'arial.ttf'
# Note: for $ttfont1 = ''; $fontstyle1 above is used
# DO NOT USE UNLESS YOU HAVE arial.ttf file in the same directory as this script!
# Any .ttf font files are supported,
# get free fonts: http://www.google.com/search?hl=en&q=free+ttf+fonts
# full path is recommended for cron scheduler use
# $ttfont1 = '/var/www/html/mosaic/htdocs/weather/arial.ttf';

$browser_output_only = 0;
# $browser_output_only = 0; # normal usage: saves the resultant image file
# $browser_output_only = 1; # demo usage: outputs resultant image to a web browser ONLY

# suppress browser image output for cron job or scheduled task usage
$no_output = 0;
# $no_output = 0; # normal usage: saves a image file and displays it to a web browser
# $no_output = 1; # save image file only, no image to browser, output url link to browser
# see here for a tutorial ..
# http://www.642weather.com/weather/scripts-weather-cam-text.php
# Simple guide to crontab usage: http://en.wikipedia.org/wiki/Cron
# Scheduled HTTP Request - Windows Task Scheduler:
# http://www.642weather.com/weather/wxblog/php-scripts/scheduled-http-request-windows-task-scheduler/

# use curl to get last_modified time from a remote URL file
# set to 0 if your PHP does not support CURL
$use_curl = 1;

# Set permissions of images 0644 (www readable)
$chmod = 0;
# disabled by default, most servers do not need it.
# enable it only if your images do not load from a web page even though they are on the server.
# $chmod = 0; # Disabled
# $chmod = 1; # Enabled

#####
# Auto archive images feature (great for web cam images)
#####
$archive = 0;
# $archive = 0; # Disabled
# $archive = 1; # Enabled
# Live example: http://www.642weather.com/weather/cam-history.php
# Thanks Jim McMurry at http://www.jcweather.us/ for the archive feature code idea/suggestion

# How many archived images will be stored
$archivecount = 12;
# example setting: $archivecount = 12;
# only this many will be saved at a time, new ones are placed at webcamimage0.jpg
# newest image will be webcamimage0.jpg ... oldest image will be webcamimage12.jpg

# Where will the archived images be saved
$archivedir = './webcam/';
# use $archivedir = './'; for same folder, or something like this: $archivedir = './webcam/';
# best to put them in their own folder though
# the folder must exist, you must make the folder yourself
# must end with a slash
# full path is recommended for cron scheduler use
# $archivedir = '/var/www/html/mosaic/htdocs/weather/webcam/';

# prefix of the archived file name
$archivefilepre = 'webcamimage'; # no extension!! the 0.jpg part will be autogenerated
# example setting: $archivefilepre = 'webcamimage';
# newest image will be webcamimage0.jpg

# autogenerate thumb images of the archived?
$archivewiththumbs = 0;
# $archivewiththumbs = 0; # Disabled
# $archivewiththumbs = 1; # Enabled
# newest image will be webcamimage0.jpg and newest thumb will be webcamimage0-thm.jpg
$thumb_new_width  = 100; # thumb image width size (height will auto size)

#####
# memory_limit is a safety incase downloaded file is 1 gig or something
#####
# DO NOT CHANGE THIS unless you really know what you are doing
# if you get "out of memory error" on a normal size image,
# then you may want to increase the value slightly
# not all php installs have this setting enabled so it is commented out by default
# ini_set ("memory_limit", "10M");

#####
# If you find any bugs,
# or can think of other possible settings/features for future releases:
# Contact Mike - http://www.642weather.com/weather/contact_us.php
#####

/*
# sometimes an error code can be printed in the image
# possible errors that could show up in the image are:
Error 000: Forbidden characters in $localimgName only letters, numbers, dash, underscore, and dots allowed
Error 001: Forbidden characters in $outputimgName only letters, numbers, dash, underscore, and dots allowed
Error 002: $localimgName file type not gif, jpg, or png. This script only supports .jpg .gif and .png
Error 003: Download error: cannot write to file, check server permission settings
Error 004: Download error: reading or opening file, check availability of $ImageURL
Error 005: Error loading image
Error 006: Missing downloaded temp file
Error 007: Convert_download: file not gif, jpg, or png. This script only supports .jpg .gif and .png
Error 008: Missing cache file
Error 009: Missing downloaded file
Error 010: Copy cache image failed
Error 011: Convert_download: touch image last modified time failed
Error 012: Chmod 0644 image failed
Error 015: Wrong settings: $localimgName and $outputimgName must have same extension IE: .jpg .gif or .png
Error 016: $outputimgName file type not gif, jpg, or png. This script only supports .jpg .gif and .png
Error 017: $localimgName and $outputimgName cannot be named the same
Error 018: fetching timestamp failed for URL (remote_image) 404 not found?
Error 019: could not connect to remote image
Error 020: $ImageURL must start with http
*/

#################
# end settings
#################

#####                                                                      #####
# Do not alter any code below this point in the script or it may not run properly.
#####                                                                      #####

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

# make sure the GD library is installed
if ( !extension_loaded('gd') || !function_exists('gd_info') ) {
   echo 'You do not have the GD Library installed.
   This script requires the GD library to function properly.
   visit http://us2.php.net/manual/en/ref.image.php for more information';
   exit;
}

// fall back if CURL is not installed.
if($use_curl && !function_exists('curl_init') ) {
    $use_curl = 0;
}

if (!preg_match('/^http/i', $ImageURL)) errorIMG('020');
# sanitize the string no html tags, spaces,
# allow only letters, numbers, dash, underscore, and dots
$localimgName = strip_tags($localimgName, '');
$localimgName = preg_replace('/ /', '', $localimgName);
if (preg_match("/[^\w\.-]+/", $localimgName)) errorIMG('000');
$outputimgName = strip_tags($outputimgName, '');
$outputimgName = preg_replace('/ /', '', $outputimgName);
if (preg_match("/[^\w\.-]+/", $outputimgName)) errorIMG('001');
if (!preg_match('/\.(jpg|gif|png)$/i', $localimgName)) errorIMG('002');
if (!preg_match('/\.(jpg|gif|png)$/i', $outputimgName)) errorIMG('016');
# $localimgName and $outputimgName cannot be named the same
if ($localimgName == $outputimgName) errorIMG('017');
# $localimgName and $outputimgName must have same extension
if (substr($localimgName,-3) != substr($outputimgName,-3)) errorIMG('015');

################# get the image, cache it and display it ############

$islocal = 0;
# is $ImageURL a local file in this same folder?
# if yes, no need to download it, still ok to cache and resize though
if (empty($_SERVER['SCRIPT_URI'])) {
   $_SERVER['SCRIPT_URI'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
$scripturlparts = explode('/', $_SERVER['SCRIPT_URI']);
$scriptfilename = $scripturlparts[count($scripturlparts)-1];
$scripturl = preg_replace("/$scriptfilename$/i", '', $_SERVER['SCRIPT_URI']);

if (preg_match('/\.(jpg|gif|png)$/i', $ImageURL)) {
  $ImageURLparts = explode('/', $ImageURL);
  $Imagefilename = $ImageURLparts[count($ImageURLparts)-1];
  $imageurl = preg_replace("/$Imagefilename$/i", '', $ImageURL);
  if(strtolower($imageurl) == strtolower($scripturl)) {
    $islocal = 1;
    $Imagefilename = $localDirectory . $Imagefilename;
  }
}

# filetype will be forced to the file type
# selected for $localimgName display image
$FileType = substr($localimgName,-3);
$Graphic = $localDirectory . $localimgName;
$Cache =   $localDirectory . $outputimgName;

$havefile = 0;
if (file_exists($Cache)){
# see if an image is cached and how old it is
        $havefile =1;
        if (!file_exists($Graphic)) errorIMG('009');
        if ($islocal) {
              // wait 2 seconds incase it is being re-uploaded
              if (!file_exists($Imagefilename) || !is_readable($Imagefilename)) sleep(3);
              $GraphicTime = filectime($Imagefilename);
        }else{
              $GraphicTime = filectime($Graphic);
        }
        $CacheTime = filectime($Cache);
        if(!$islocal){
          if($use_curl) {
                  $URLdate = curl_last_mod($ImageURL);
          } else {
                  $Headers = getHTTPheaders($ImageURL,1);
                  $URLdate = strtotime($Headers['last-modified']);
          }
        }
        if ((!$islocal and $download_setting ==1 and $URLdate > $GraphicTime)||
        ($islocal and $download_setting ==1 and $GraphicTime > $CacheTime)) {
           # download new image because
           # last-modified timestamp changes at the source
           if($islocal) copy_file($Imagefilename, $Graphic);
           if(!$islocal) {
             if ($use_curl) {
                     curl_download_file($ImageURL, $Graphic);
             } else {
                     download_file($ImageURL, $Graphic);
             }
           }
           if ($cache_only) {
                   cache_download($Graphic);
           } else {
                   convert_download($Graphic);
           }
        }
        if ($download_setting ==2 and file_exists($Cache) and filemtime($Cache) + $refetchSeconds < time()) {
           # download new image at the preset time interval
           if($islocal) copy_file($Imagefilename, $Graphic);
           if(!$islocal) {
             if ($use_curl) {
                     curl_download_file($ImageURL, $Graphic);
             } else {
                     download_file($ImageURL, $Graphic);
             }
           }
           if ($cache_only) {
                   cache_download($Graphic);
           } else {
                   convert_download($Graphic);
           }
        }
        if ($download_setting ==3 || $_GET['reload'] == 1) {
           # download new image every hit
           if($islocal)  copy_file($Imagefilename, $Graphic);
           if(!$islocal) {
             if ($use_curl) {
                     curl_download_file($ImageURL, $Graphic);
             } else {
                     download_file($ImageURL, $Graphic);
             }
           }
           if ($cache_only) {
                   cache_download($Graphic);
           } else {
                   convert_download($Graphic);
           }
        }
        $GraphicTime = filectime($Graphic);
} else {
       # download the first time
       if($islocal)  copy_file($Imagefilename, $Graphic);
       if(!$islocal) {
             if ($use_curl) {
                     curl_download_file($ImageURL, $Graphic);
             } else {
                     download_file($ImageURL, $Graphic);
             }
           }
       if ($cache_only) {
               cache_download($Graphic);
       } else {
               convert_download($Graphic);
       }
}

if($no_output == 1) {
      echo "You have setting mode: \$no_output =1;<br>
      suppress browser image output for cron job or scheduled task usage<br>
      Here is a URL to your image:<br>
      <a href=\"$scripturl$outputimgName\">$scripturl$outputimgName</a>";
}else{
      if (file_exists($Cache)) {
        # now send image to browser
        if($_GET['reload'] == 1) {
              header('Last-modified: ' . gmdate("D, d M Y H:i:s"). ' GMT');
        }elseif(!$islocal) {
              header('Last-modified: ' . $Headers['last-modified']);
        }else{
              header('Last-modified: ' . gmdate("D, d M Y H:i:s", filectime($Graphic)). ' GMT');
        }
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Length: '.filesize($Cache));
        if ($FileType == 'jpg') header("Content-type: image/jpeg");
        if ($FileType == 'gif') header("Content-type: image/gif");
        if ($FileType == 'png') header("Content-type: image/png");
         readfile($Cache);
     } else {
             errorIMG('008');
     }
}
exit;

############# Begin Functions #################

function cache_download($Graphic) {
# cache only option (this will preserve animated GIFS)
# just download and cache the file (ONLY!!)
# will bypass all other settings for resize, crop, and text overlay!!
# NO resize, NO crop, and NO text overlay

   global $chmod, $archive, $Cache, $islocal;

   # is anyhing there?
   if (is_file($Graphic)) {
      list($width, $height, $type) = getimagesize($Graphic);
      $new_ext ='';
      switch($type) {
        case "1": $new_ext = 'gif';
        break;
        case "2": $new_ext = 'jpg';
        break;
        case "3": $new_ext = 'png';
        break;
      }
      !preg_match('/(jpg|gif|png)$/i', $new_ext) and errorIMG('007');
      copy($Graphic, $Cache);
      # update file last modified if it is a local image and we are forcing reload
      if($islocal and $_GET['reload'] == 1) touch($Graphic) or errorIMG('011');
      if ($chmod) chmod($Cache, 0644) or errorIMG('012');
      # archive?
      if($archive) {
        //Begin archive Sequencing
        archive_file($Cache);
      }
   } else {
           errorIMG('006');
   }
} // end function cache_download

function makethumb($file,$thumbfile) {
    global $thumb_new_width, $chmod, $jpegcompress;
   # is anyhing there?
   if (is_file($file)) {
      list($width, $height, $type) = getimagesize($file);
      $new_ext ='';
      switch($type) {
        case "1": $new_ext = 'gif';
        break;
        case "2": $new_ext = 'jpg';
        break;
        case "3": $new_ext = 'png';
        break;
      }
      !preg_match('/(jpg|gif|png)$/i', $new_ext) and errorIMG('007');
      # resize to $thumb_new_width set width but keep proportion
      $thumb_new_height = round($height * $thumb_new_width / $width);
      $imageb = loadIMG($file);
      $imaget = imagecreatetruecolor($thumb_new_width, $thumb_new_height);
      imagecopyresampled($imaget, $imageb, 0, 0, 0, 0, $thumb_new_width, $thumb_new_height, $width, $height);
      # save image
      if ($new_ext == 'jpg') imagejpeg($imaget, $thumbfile, $jpegcompress);
      if ($new_ext == 'gif') imagegif($imaget, $thumbfile);
      if ($new_ext == 'png') imagepng($imaget, $thumbfile);
      imagedestroy($imageb);
      imagedestroy($imaget);
      if ($chmod) chmod($thumbfile, 0644);
   }# end if is file

} // end function make_thumb

function new_width_height($width,$height){
     global $resize_setting, $new_width, $new_height, $percent;
      # no resize
      if ($resize_setting ==5 ) {
         $resize_setting =4; $percent = 100;
      }
      # resize to $new_width  set width  but keep proportion
      $resize_setting ==2 and $new_height = round($height * $new_width / $width);
      # resize to $new_height  set height  but keep proportion
      $resize_setting ==3 and $new_width = round($width * $new_height / $height);
      # use $percent to calculate $new_width $new_height
      if ($resize_setting ==4 ) {
         $percent = $percent * 0.01;
         $new_width = $width * $percent;
         $new_height = $height * $percent;
      }
      return array ($new_width,$new_height);
} // end function new_width_height

function convert_download($Graphic) {
   global $new_width, $new_height, $Cache, $FileType, $percent, $browser_output_only;
   global $resize_setting, $jpegcompress, $chmod, $text1, $text2, $text3, $archive;
   global $cropSize, $cropStartX, $cropStartY, $cropWidth, $cropHeight, $islocal;

   # is anyhing there?
   if (is_file($Graphic)) {
      list($width, $height, $type) = getimagesize($Graphic);
      $new_ext ='';
      switch($type) {
        case "1": $new_ext = 'gif';
        break;
        case "2": $new_ext = 'jpg';
        break;
        case "3": $new_ext = 'png';
        break;
      }
      !preg_match('/(jpg|gif|png)$/i', $new_ext) and errorIMG('007');

      # convert and resize
         $image = loadIMG($Graphic);
         # Basic crop from center - crops the image from calculated center in a square of $cropSize pixels
         $cropped=0;
         if($cropSize > 0) {
           $cropX =0;
           $cropY =0;
           $cropSize > $width  and $cropSize = $width;
           $cropSize > $height and $cropSize = $height;
           $cropX = intval(($width - $cropSize) / 2);
           $cropY = intval(($height - $cropSize) / 2);
           $image_c = imagecreatetruecolor($cropSize, $cropSize);
           list ($new_width, $new_height) = new_width_height($cropSize, $cropSize);
           $image_p = imagecreatetruecolor($new_width, $new_height);
           imagecopyresampled($image_c, $image, 0, 0, $cropX, $cropY, $cropSize, $cropSize, $cropSize, $cropSize);
           imagecopyresampled($image_p, $image_c, 0, 0, 0, 0, $new_width, $new_height, $cropSize, $cropSize);
           $cropped=1;
         }
         # Advanced crop - crops an image using $cropStartX and $cropStartY as the upper-left hand corner.
         if(!$cropped and $cropWidth > 0 and $cropHeight > 0) {
           list ($new_width, $new_height) = new_width_height($cropWidth,$cropHeight);
           $cropWidth >  $width  and $cropWidth = $width;
           $cropHeight > $height and $cropHeight = $height;
           if(($cropStartX + $cropWidth) > $width)   $cropStartX = ($width - $cropWidth);
           if(($cropStartY + $cropHeight) > $height) $cropStartY = ($height - $cropHeight);
           $cropStartX < 0 and $cropStartX = 0;
           $cropStartY < 0 and $cropStartY = 0;
           $image_c = imagecreatetruecolor($cropWidth, $cropHeight);
           $image_p = imagecreatetruecolor($new_width, $new_height);
           imagecopyresampled($image_c, $image, 0, 0, $cropStartX, $cropStartY, $cropWidth, $cropHeight, $cropWidth, $cropHeight);
           imagecopyresampled($image_p, $image_c, 0, 0, 0, 0, $new_width, $new_height, $cropWidth, $cropHeight);
           $cropped=1;
         }
         if (!$cropped) {
              list ($new_width, $new_height) = new_width_height($width,$height);
              $image_p = imagecreatetruecolor($new_width, $new_height);
              imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
         }
         $text1 != '' and textoverlay(1,$image_p, $new_width, $new_height);
         $text2 != '' and textoverlay(2,$image_p, $new_width, $new_height);
         $text3 != '' and textoverlay(3,$image_p, $new_width, $new_height);
         if($browser_output_only) {
                 # send image to browser only (DEMO MODE)
                 header('Last-modified: ' . gmdate("D, d M Y H:i:s"). ' GMT');
                 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                 header("Cache-Control: no-store, no-cache, must-revalidate");
                 header("Cache-Control: post-check=0, pre-check=0", false);
                 header("Pragma: no-cache");
                 if ($FileType == 'jpg') header("Content-type: image/jpeg");
                 if ($FileType == 'gif') header("Content-type: image/gif");
                 if ($FileType == 'png') header("Content-type: image/png");
                 if ($FileType == 'jpg') imagejpeg($image_p, '', $jpegcompress);
                 if ($FileType == 'gif') imagegif($image_p);
                 if ($FileType == 'png') imagepng($image_p);
                 imagedestroy($image);
                 imagedestroy($image_p);
                 exit;
         } else {
                 # save image (NORMAL MODE)
                 if ($FileType == 'jpg') imagejpeg($image_p, $Cache, $jpegcompress);
                 if ($FileType == 'gif') imagegif($image_p, $Cache);
                 if ($FileType == 'png') imagepng($image_p, $Cache);

         }
         $cropped and imagedestroy($image_c);
         imagedestroy($image);
         imagedestroy($image_p);
         # update file last modified if it is a local image and we are forcing reload
         if($islocal and $_GET['reload'] == 1) touch($Graphic) or errorIMG('011');
   } else {
           errorIMG('006');
   }
   if ($chmod) chmod($Cache, 0644) or errorIMG('012');
   # archive?
   if($archive) {
     //Begin archive Sequencing
     archive_file($Cache);
   }
} // end of convert_download function

function archive_file($Cache) {

   global $FileType, $chmod, $archive, $archivecount, $archivedir, $archivefilepre, $archivewiththumbs;

  //Begin archive Sequencing
  # delete oldest file
  $targetfile = $archivedir . $archivefilepre . $archivecount . '.' .$FileType;
  $targetfilethm = $archivedir . $archivefilepre . $archivecount . '-thm.' .$FileType;
  if (file_exists($targetfile)) unlink($targetfile);
  if ($archivewiththumbs and file_exists($targetfilethm)) unlink($targetfilethm);
  # archive/rotate the rest
  for($i = $archivecount-1; $i >= 0; $i--){ //cycle through files renaming them
     $targetfile = $archivedir . $archivefilepre . $i . '.' .$FileType;
     $targetfilethm = $archivedir . $archivefilepre . $i . '-thm.' .$FileType;
     $t = $i + 1;
     $nextfile = $archivedir . $archivefilepre . $t . '.' .$FileType;
     $nextfilethm = $archivedir . $archivefilepre . $t . '-thm.' .$FileType;
     if (file_exists($targetfile)) rename($targetfile, $nextfile);
     if ($archivewiththumbs and file_exists($targetfilethm)) rename($targetfilethm, $nextfilethm);
  }
  # add newest file
  $targetfile = $archivedir . $archivefilepre . '0.' . $FileType;
  copy($Cache, $targetfile);
  if ($chmod) chmod($targetfile, 0644) or errorIMG('012');
  if($archivewiththumbs) {
    makethumb("$archivedir$archivefilepre" . '0.' . "$FileType","$archivedir$archivefilepre" . '0-thm.' . "$FileType");
  }
} // end of archive_file function

function textoverlay($textoption,$image_p, $new_width, $new_height) {
    global $text1, $ttfont1, $fontsize1, $fontcolor1, $fontshadowcolor1, $fontstyle1, $textalign1, $xmargin1, $ymargin1;
    global $text2, $ttfont2, $fontsize2, $fontcolor2, $fontshadowcolor2, $fontstyle2, $textalign2, $xmargin2, $ymargin2;
    global $text3, $ttfont3, $fontsize3, $fontcolor3, $fontshadowcolor3, $fontstyle3, $textalign3, $xmargin3, $ymargin3;
    if ($textoption == 1) {
      $text = $text1; $ttfont = $ttfont1; $fontsize = $fontsize1; $fontstyle = $fontstyle1;
      $fontcolor = $fontcolor1; $fontshadowcolor = $fontshadowcolor1;
      $textalign = $textalign1; $xmargin = $xmargin1; $ymargin = $ymargin1;
    }
    if ($textoption == 2) {
      $text = $text2; $ttfont = $ttfont2; $fontsize = $fontsize2; $fontstyle = $fontstyle2;
      $fontcolor = $fontcolor2; $fontshadowcolor = $fontshadowcolor2;
      $textalign = $textalign2; $xmargin = $xmargin2; $ymargin = $ymargin2;
    }
    if ($textoption == 3) {
      $text = $text3; $ttfont = $ttfont3; $fontsize = $fontsize3; $fontstyle = $fontstyle3;
      $fontcolor = $fontcolor3; $fontshadowcolor = $fontshadowcolor3;
      $textalign = $textalign3; $xmargin = $xmargin3; $ymargin = $ymargin3;
    }
    if (!preg_match('#[a-z0-9]{6}#i', $fontcolor)) $fontcolor = 'FFFFFF';  # default white
    if (!preg_match('#[a-z0-9]{6}#i', $fontshadowcolor)) $fontshadowcolor = '808080'; # default grey
    $fcint = hexdec("#$fontcolor");
    $fsint = hexdec("#$fontshadowcolor");
    $fcarr = array("red" => 0xFF & ($fcint >> 0x10),"green" => 0xFF & ($fcint >> 0x8),"blue" => 0xFF & $fcint);
    $fsarr = array("red" => 0xFF & ($fsint >> 0x10),"green" => 0xFF & ($fsint >> 0x8),"blue" => 0xFF & $fsint);
    $fcolor  = imagecolorallocate($image_p, $fcarr["red"], $fcarr["green"], $fcarr["blue"]);
    $fscolor = imagecolorallocate($image_p, $fsarr["red"], $fsarr["green"], $fsarr["blue"]);
    if ($ttfont != '') {
       # using ttf fonts
       $alpha   = range("a", "z");
       $alpha_u = range("A", "Z");
       $alpha = $alpha.$alpha_u.range(0, 9);
       $_b = imageTTFBbox($fontsize,0,$ttfont,$alpha);
       $fontheight = abs($_b[7]-$_b[1]);
    } else {
      $font = $fontstyle;
      # using built in fonts, find alignment
      if($font < 0 || $font > 5){ $font = 1; }
          $fontwidth = ImageFontWidth($font);
          $fontheight = ImageFontHeight($font);
      }
      $text = preg_replace("/\r/",'',$text);
      # wordwrap line if too many characters on one line
      if ($ttfont != '') {
         # array lines
         $lines = explode("\n", $text);
         $lines = ttf_wordwrap($lines,$ttfont,$fontsize,floor($new_width - ($xmargin * 2)));
      } else {
        $maxcharsperline = floor(($new_width - ($xmargin * 2)) / $fontwidth);
        $text = wordwrap($text, $maxcharsperline, "\n", 1);
        # array lines
        $lines = explode("\n", $text);
      }
      # determine alignment
      $align = 'ul'; # default upper left
      if ($textalign == 'lowerleft')    $align = 'll';
      if ($textalign == 'upperleft')    $align = 'ul';
      if ($textalign == 'lowerright')   $align = 'lr';
      if ($textalign == 'upperright')   $align = 'ur';
      if ($textalign == 'center')       $align = 'c';
      if ($textalign == 'centertop')    $align = 'ct';
      if ($textalign == 'centerbottom') $align = 'cb';
      # find start position for each text position type
      if ($align == 'ul') { $x = $xmargin; $y = $ymargin;}
      if ($align == 'll') { $x = $xmargin;
         $y = $new_height - ($fontheight + $ymargin);
         $lines = array_reverse($lines);
      }
      if ($align == 'ur') $y = $ymargin;
      if ($align == 'lr') { $x = $xmargin;
         $y = $new_height - ($fontheight + $ymargin);
         $lines = array_reverse($lines);
      }
      if ($align == 'ct') $y = $ymargin;
      if ($align == 'cb') { $x = $xmargin;
         $y = $new_height - ($fontheight + $ymargin);
         $lines = array_reverse($lines);
      }
      if ($align == 'c') $y = ($new_height/2) - ((count($lines) * $fontheight)/2);
      if ($ttfont != '') $y +=$fontsize; # fudge adjustment for truetype margin
         while (list($numl, $line) = each($lines)) {
             # adjust position for each text position type
             if ($ttfont != '') {
                $_b = imageTTFBbox($fontsize,0,$ttfont,$line);
                $stringwidth = abs($_b[2]-$_b[0]);
             }else{
                $stringwidth = strlen($line) * $fontwidth;
             }
             if ($align == 'ur'||$align == 'lr') $x = ($new_width - ($stringwidth) - $xmargin);
             if ($align == 'ct'||$align == 'cb'||$align == 'c') $x = $new_width/2 - $stringwidth/2;
             if ($ttfont != '') {
                # write truetype font text with slight SE shadow to standout
                imagettftext($image_p, $fontsize, 0, $x-1, $y, $fscolor, $ttfont, $line);
                imagettftext($image_p, $fontsize, 0, $x, $y-1, $fcolor, $ttfont, $line);
             }else{
                # write text with slight SE shadow to standout
                imagestring($image_p,$font,$x-1,$y,$line,$fscolor);
                imagestring($image_p,$font,$x,$y-1,$line,$fcolor);
             }
             # adjust position for each text position type
             if ($align == 'ul'||$align == 'ur'||$align == 'ct'||$align == 'c') $y += $fontheight;
             if ($align == 'll'||$align == 'lr'||$align == 'cb') $y -= $fontheight;
         } # end while
} // end textoverlay function

function errorIMG($error) {
   global $no_output;
   # shows error messages in an image output
   # no image is actually written to server, just to web browser
   global $new_width, $new_height, $no_output;
 if($no_output) {
   echo "Error: $error";
 }else{
   $im  = imagecreate ($new_width, $new_height);
   $bgc = imagecolorallocate ($im, 255, 255, 255);
   $tc  = imagecolorallocate ($im, 0, 0, 0);
   imagefilledrectangle ($im, 0, 0, $new_width, $new_height, $bgc);
   imagestring ($im, 5, 5, 5, "Error: $error", $tc);
   header('Content-type: image/gif');
   imagegif($im);
   imagedestroy($im);
 }
   exit;
} // end of errorIMG function


function loadIMG($imgname) {
  # have to get the right filetype based on what the image actually is,
  # not what it is going to be saved as later
  if (is_file($imgname)) {
      list($width, $height, $type) = getimagesize($imgname);
      $new_ext ='';
      switch($type) {
        case "1": $new_ext = 'gif';
        break;
        case "2": $new_ext = 'jpg';
        break;
        case "3": $new_ext = 'png';
        break;
      }
      if (!preg_match('/(jpg|gif|png)$/i', $new_ext)) {
        errorIMG('007');
      }
  }
  if ($new_ext == 'jpg')$im = @imagecreatefromjpeg($imgname);
  if ($new_ext == 'gif')$im = @imagecreatefromgif($imgname);
  if ($new_ext == 'png')$im = @imagecreatefrompng($imgname);
  if (!$im) {
       errorIMG('005');
  }
  return $im;
} // end of loadIMG function

function curl_last_mod($remote_file) {
    // return unix timestamp (last_modified) from a remote URL file
    $last_modified = $ch = $resultString = $headers = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_file);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5 sec timeout
    curl_setopt($ch, CURLOPT_HEADER, 1);  // make sure we get the header
    curl_setopt($ch, CURLOPT_NOBODY, 1);  // make it a http HEAD request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // write the response to a variable
    curl_setopt($ch, CURLOPT_FILETIME, 1 );

    $i = 1;
    while ($i++ <= 2) {
       if(curl_exec($ch) === false){
               errorIMG('019'); // could not connect
               //   echo 'Curl error: ' . curl_error($ch);
               //   exit;
       }
       $headers = curl_getinfo($ch);
       if ($headers['http_code'] != 200) {
          sleep(3);  // Let's wait 3 seconds to see if its a temporary network issue.
       } else if ($headers['http_code'] == 200) {
          // we got a good response, drop out of loop.
          break;
       }
    }
    $last_modified = $headers['filetime'];
    if ($headers['http_code'] != 200) errorIMG('018'); // remote file not found
    curl_close ($ch);

    return $last_modified;
} // end of curl_last_mod function

function getHTTPheaders($url,$format=0) {
  $url_info=parse_url($url);
  $port = isset($url_info['port']) ? $url_info['port'] : 80;
  $fp=fsockopen($url_info['host'], $port, $errno, $errstr, 15);
  if($fp) {
    $head = "HEAD ".@$url_info['path']."?".@$url_info['query'];
    $head .= " HTTP/1.0\r\nHost: ".@$url_info['host']."\r\n\r\n";
    fputs($fp, $head);
    while(!feof($fp)) {
      if($header=trim(fgets($fp, 1024))) {
        if($format == 1) {
          $h2 = explode(': ',$header);
// the first element is the http header type, such as HTTP/1.1 200 OK,
// it doesn't have a separate name, so we have to check for it.
          if($h2[0] == $header) {
            $headers['status'] = $header;
              if (! preg_match('|HTTP/1.* 200 OK|i',$header)) {
                errorIMG('018'); // 404 image?
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
          errorIMG('019'); // could not connect
          exit;
  }
} // end of getHTTPheaders function

function copy_file($file_source, $file_target) {
  global $chmod;
  // wait incase it is being re-uploaded
  if (!file_exists($Imagefilename) || !is_readable($Imagefilename)) sleep(3);
  copy($file_source, $file_target) or errorIMG('011');
  if ($chmod) chmod($file_target, 0644) or errorIMG('012');
  // No error
  return false;
} // end of copy_file function

function download_file($file_source, $file_target) {
  global $chmod;
  $rh = fopen($file_source, 'rb') or errorIMG('004');
  $wh = fopen($file_target, 'wb') or errorIMG('003');
  while (!feof($rh)) {
    if (fwrite($wh, fread($rh, 1024)) === FALSE) {
          errorIMG('003');
          return true;
    }
  }
  fclose($rh);
  fclose($wh);
  if ($chmod) chmod($file_target, 0644) or errorIMG('012');
  // No error
  return false;
} // end of download_file function

function curl_download_file($file_source, $file_target) {
  global $chmod;

  $wh = fopen($file_target, 'wb') or errorIMG('003');
  $ch = curl_init($file_source);  // the file we are downloading
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  curl_setopt($ch, CURLOPT_FILE, $wh);
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_exec($ch);
  curl_close($ch);
  fclose($wh);

  if ($chmod) chmod($file_target, 0644) or errorIMG('012');

  return false;
} // end of download_file function

function ttf_wordwrap($srcLines,$font,$textSize,$width) {
    $dstLines = Array(); // The destination lines array.
    foreach ($srcLines as $currentL) {
        $line = '';
        $words = explode(" ", $currentL); //Split line into words.
        foreach ($words as $word) {
            $dimensions = imagettfbbox($textSize, 0, $font, $line.' '.$word);
            $lineWidth = $dimensions[4] - $dimensions[0]; // get the length of this line, if the word is to be included
            if ($lineWidth > $width && !empty($line) ) { // check if it is too big if the word was added, if so, then move on.
                $dstLines[] = trim($line); //Add the line like it was without spaces.
                $line = '';
            }
            $line .= $word.' ';
        }
        $dstLines[] =  trim($line); //Add the line when the line ends.
    }
    return $dstLines;
} // end of ttf_wordwrap function
?>