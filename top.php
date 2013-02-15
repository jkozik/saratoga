<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (Base-* template sets)
############################################################################
#
#	Project:	Sample Included Website Design
#	Module:		top.php
#	Purpose:	Provides the initial top section of the website
# 	Authors:	Kevin W. Reed <kreed@tnet.com>
#				TNET Services, Inc.
#               Ken True <webmaster@saratoga-weather.org>
#               Saratoga-Weather.org
#
# 	Copyright:	(c) 1992-2007 Copyright TNET Services, Inc.
############################################################################
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA
############################################################################
#	This document uses Tab 4 Settings
############################################################################
require_once("Settings.php");
require_once("common.php");
global	$TITLE;
//Version  V3.01 - 03-Mar-2011 - added Content-Type header for charset switching
//Version  V3.02 - 23-Jul-2011 - added WXtags upload copy capability
//Version  V3.03 - 17-Nov-2011 - added support for right-to-left presentation format
//Version  V3.04 - 05-Feb-2013 - added support for HTML5+UTF8
############################################################################
if(isset($useUTF8) and function_exists('set_langtrans_UTF8') ) {
	set_langtrans_UTF8(); // convert langrans items to UTF-8
}
header("Content-Type: text/html; charset=".strtoupper($SITE['charset']));
if(isset($useHTML5) and $useHTML5) {
  print "<!DOCTYPE html>\n";
  print "<html";
  if (isset($SITE['ISOLang'][$SITE['lang']])) { // 
	$olang = $SITE['ISOLang'][$SITE['lang']];
	echo " lang=\"$olang\""; 
  }
  print ">\n";
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php 
} // end HTML5 v.s. XHTML 1.0 Transitional header
?>
<?php
 if(isset($SITE['WXtags']) and $SITE['WXtags'] <> '') {
	// see if upload copy should be done
	$siteUploadFile = preg_replace('|\.php$|','-new.php',$SITE['WXtags']);
	if(file_exists($siteUploadFile) and 
	   is_writable($SITE['WXtags']) and
	   filesize($siteUploadFile) > filesize($SITE['WXtags']) - 1023 and
	   filemtime($siteUploadFile) > filemtime($SITE['WXtags']) ) {
		 $didCopy = copy($siteUploadFile,$SITE['WXtags']);
		 if($didCopy) {
			 print "<!-- WXtags file updated successfully from $siteUploadFile -->\n";
		 } else {
			 print "<!-- WXtags file update failed from $siteUploadFile -->\n";
		 }
	}
	if (isset($_REQUEST['debug']) and strtolower($_REQUEST['debug']) == 'y') {
	  $canWriteTags = is_writable($SITE['WXtags'])?"is":"IS NOT";
	  print "<!-- WXtags '".$SITE['WXtags']. "' $canWriteTags writeable. -->";
	}
	include_once($SITE['WXtags']);
 }
 $doIconvUTF8 = ($SITE['charset'] == 'UTF-8' and $SITE['origCharset'] <> 'UTF-8')?true:false;
?>
  <head>
<?php if(isset($SITE['ajaxScript'])) { ?>
    <!-- ##### start AJAX mods ##### -->
    <script type="text/javascript" src="<?php echo $SITE['ajaxScript']; ?>"></script>
    <!-- AJAX updates by Ken True - http://saratoga-weather.org/wxtemplates/ -->
<?php } // end if ajaxScript ?>
<?php if (isset($showGizmo) and $showGizmo) { ?>
    <script type="text/javascript" src="ajaxgizmo.js"></script>
<?php if (isset($SITE['UV']) and !$SITE['UV']) {  // turn gizmo uv display off ?>
    <script type="text/javascript"> showUV = false; </script>
<?php   }  // end of turn gizmo uv display off ?>
<?php } // end of showGizmo ?>
<?php if (file_exists("language-". $SITE['lang'] . ".js") and (!$doIconvUTF8) ) { ?>
    <script type="text/javascript" src="language-<?php echo $SITE['lang']; ?>.js"></script>
	<!-- language for AJAX script included -->
<?php }
  if(isset($useUTF8) and $useUTF8 and file_exists("language-". $SITE['lang'] . ".js") and $doIconvUTF8) {
	$tjsfile = file("language-". $SITE['lang'] . ".js");
	$sceCharset = $SITE['origCharset']; // set in common.php when useUTF8 invoked
	print "    <script type=\"text/javascript\">\n// <![CDATA[ /* converted to UTF-8 from $sceCharset */\n";
	foreach ($tjsfile as $n => $line) {
		print iconv($sceCharset,'UTF-8//TRANSLIT',$line);
	}
	print "// converted ".count($tjsfile)." lines of language-".$SITE['lang'].".js from $sceCharset to UTF-8 inline -->\n// ]]\n    </script>\n"; 
  }
?>
    <meta name="description" content="Personal weather station." />
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo strtoupper($SITE['charset']); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $SITE['CSSscreen']; ?>" media="screen" title="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo $SITE['CSSprint']; ?>" media="print" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<?php 
if(isset($useHTML5) and $useHTML5) { // no pragma/cache-control for us

} else { ?>
	<meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Cache-Control" content="no-cache" />
<?php } // end HTML5 check 

if(isset($SITE['showTitleTemp']) and $SITE['showTitleTemp'] and isset($temperature)) {
  $dispTUOM = $temperature . '&deg;'.strtoupper(preg_replace('|[^CF]+|i','',$uomtemp)). ' - ';
} else {
  $dispTUOM = '';
}
if( isset ($TITLE) ) {
	echo "    <title>$dispTUOM" . $TITLE . "</title>\n";
} else {
	echo "    <title>$dispTUOM" . langtransstr($SITE['organ']) . "</title>\n";
}

if (isset($SITE['flyoutmenu']) and $SITE['flyoutmenu'] or
	isset($_REQUEST['menu']) and strtolower($_REQUEST['menu']) == 'test' ) {
  $SITE['flyoutmenu'] = true;
  $PrintFlyoutMenu = false;
  $genDiv =false;
  global $FlyoutCSS, $FlyoutMenuText;
  include_once('flyout-menu.php');
  print $FlyoutCSS;
}
// Right-to-left language CSS mod
if(isset($SITE['CSS-RTL']) and file_exists($SITE['CSS-RTL']) and isset($SITE['RTL-LANG']) and 
   (strpos($SITE['RTL-LANG'],$SITE['lang']) !== false) ) { // use the override CSS
   print "<!-- lang='".$SITE['lang']."' using RTL CSS Override -->\n"; 
  // print "<style type=\"text/css\">\n";
  // readfile($SITE['CSS-RTL']);
  // print "</style>\n";
  print "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$SITE['CSS-RTL']."\"/>\n";
} // end Right-to-left CSS override 
?>
<!-- World-ML template from http://saratoga-weather.org/wxtemplates/ -->
<!-- end of top -->
