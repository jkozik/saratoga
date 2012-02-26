<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (Canada/World-ML template set)
############################################################################
#
#   Project:    Sample Included Website Design
#   Module:     sample.php
#   Purpose:    Sample Page
#   Authors:    Kevin W. Reed <kreed@tnet.com>
#               TNET Services, Inc.
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
// Version 1.01 - 22-Jan-2012 - fixes for mixed date format translations
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE= $SITE['organ'] . " - " . langtransstr("Astronomy");
$showGizmo = true;  // set to false to exclude the gizmo
include("top.php");
############################################################################
?>
</head>
<body>
<?php
############################################################################
include("header.php");
############################################################################
include("menubar.php");
############################################################################
# NOTE: you don't have to change these defaults.. use Settings.php to set your values.
$timeFormat = 'd-M-Y g:ia';  // 31-Mar-2006 6:35pm
//$timeFormat = 'd-M-Y H:i';   // 31-Mar-2006 18:35
$timeOnlyFormat = 'g:ia';    // h:mm[am|pm];
//$timeOnlyFormat = 'H:i';     // hh:mm
$dateOnlyFormat = 'd-M-Y';   // d-Mon-YYYY
$WDdateMDY = true;     // true=dates by WD are 'month/day/year'
//                     // false=dates by WD are 'day/month/year'
$ourTZ = "PST8PDT";  //NOTE: this *MUST* be set correctly to
// translate UTC times to your LOCAL time for the displays.
$imagesDir = './ajax-images/';  // directory for ajax-images with trailing slash
if (isset($SITE['imagesDir'])) 	{$imagesDir = $SITE['imagesDir'];}
if (isset($SITE['timeFormat'])) {$timeFormat = $SITE['timeFormat'];}
if (isset($SITE['timeOnlyFormat'])) {$timeOnlyFormat = $SITE['timeOnlyFormat'];}
if (isset($SITE['dateOnlyFormat'])) {$dateOnlyFormat = $SITE['dateOnlyFormat'];}
if (isset($SITE['WDdateMDY']))  {$WDdateMDY = $SITE['WDdateMDY'];}
if (isset($SITE['tz'])) 		{$ourTZ = $SITE['tz'];}
$DebugMode = false;
if (isset($_REQUEST['debug'])) {$DebugMode = strtolower($_REQUEST['debug']) == 'y'; }

?>
<div id="main-copy-dark">
  
<h3><?php langtrans('Astronomy'); ?></h3> 

<br />
<?php if(isset($SITE['WXtags']) and $SITE['WXtags'] <> '') { // do astronomy only if station is configured
if(isset($SITE['WXsoftware']) and $SITE['WXsoftware'] == 'WD') {
// Sample from WD: $moonage = "Moon age: 10 days,10 hours,41 minutes,80%";	// current age of the moon (days since new moon)
// Sample from WD: $moonphase = "80%";	// Moon phase %
  $moonagedays = preg_replace('|^Moon age:\s+(\d+)\s.*$|is',"\$1",$moonage);
  if(!isset($moonphase) or $moonphase == '') { // MAC version of WD is missing this value
    preg_match_all('|(\d+)|is',$moonage,$matches);
    $moonphase  = $matches[1][3];
	if(isset($matches[1][4])) {
	  $moonphase .= '.' . $matches[1][4]; // pick up decimal;
	  $moonphase = round($moonphase,0);
	}
	$moonphase .= '%';
  }
  $moonphasename = moonphase($moonage);
  
} else { // perform non-WD moon stuff
	$mooninfo = cGetMoonInfo();  /* Note:  getMoonInfo() is located in common.php */
	print "<!-- cGetMoonInfo returns\n".print_r($mooninfo,true)." -->\n";
	if(!isset($moonphase)) {$moonphase = $mooninfo->ill.'%'; }
	if(!isset($newmoon))   {$newmoon   = $mooninfo->NMWD;    }
	if(!isset($nextnewmoon)) {$nextnewmoon = $mooninfo->Q4WD; } 
	if(!isset($firstquarter)) {$firstquarter = $mooninfo->Q1WD; }
	if(!isset($lastquarter)) {$lastquarter = $mooninfo->Q3WD; }
	if(!isset($fullmoon))    {$fullmoon = $mooninfo->FMWD; }
	if(!isset($moonphasename)) {$moonphasename = $mooninfo->phase;}
	if(!isset($moonagedays))  {$moonagedays = $mooninfo->pic; }
	if(!isset($moonage))      {$moonage = 'Moon age: '.$mooninfo->age.','.$mooninfo->ill.'%'; }
	
	$seasoninfo = cGetSeasonInfo(); /* Note: getSeasonInfo() is located in common.php */
	if(!isset($marchequinox) and isset($seasoninfo->spring)) {$marchequinox = $seasoninfo->spring; } 
	if(!isset($junesolstice) and isset($seasoninfo->summer)) {$junesolstice = $seasoninfo->summer; } 
	if(!isset($sepequinox) and isset($seasoninfo->fall))     {$sepequinox   = $seasoninfo->fall; } 
	if(!isset($decsolstice) and isset($seasoninfo->winter))  {$decsolstice  = $seasoninfo->winter; } 

    if(file_exists("get-USNO-sunmoon.php")) { 
      include_once("get-USNO-sunmoon.php");
	  $USNOdata = getUSNOsunmoon();
   }
   if(isset($USNOdata['sunrise']))                 {$sunrise =     $USNOdata['sunrise']; }  
   if(isset($USNOdata['sunrisedate']))             {$sunrisedate = $USNOdata['sunrisedate']; }  
   if(isset($USNOdata['sunset']))                  {$sunset =      $USNOdata['sunset']; }  
   if(isset($USNOdata['sunsetdate']))              {$sunsetdate =  $USNOdata['sunsetdate']; }  
   if(isset($USNOdata['moonrise']))                {$moonrise =    $USNOdata['moonrise']; }  
   if(isset($USNOdata['moonrisedate']))            {$moonrisedate= $USNOdata['moonrisedate']; }  
   if(isset($USNOdata['moonset']))                 {$moonset =     $USNOdata['moonset']; }  
   if(isset($USNOdata['moonsetdate']))             {$moonsetdate = $USNOdata['moonsetdate']; }  
   if(isset($USNOdata['moonphase']))               {$moonphasename = $USNOdata['moonphase']; }  
   if(isset($USNOdata['illumination']))            {$moonphase = $USNOdata['illumination']; }  
   if(isset($USNOdata['hoursofpossibledaylight'])) {$hoursofpossibledaylight = $USNOdata['hoursofpossibledaylight'];} 
 }  

?>
<div align="center">
  <table width="99%" cellpadding="0" border="0" cellspacing="0">
  <tr>
    <td width="50%" align="center"><h3><?php langtrans('Sun&nbsp;'); ?></h3></td>
    <td width="50%" align="center"><h3><?php langtrans('Moon'); ?></h3></td>
  </tr>
  <tr>
    <td width="50%" align="center">
    <img src="<?php print $SITE['imagesDir']; ?>sunicon.jpg" width="104" height="72" alt="<?php langtrans('Sun'); ?>" style="border: 0;"/></td>
    <td width="50%" align="center">
    <?php if(file_exists('moonicon.gif')) { ?>
    <img src="moonicon.gif" width="104" height="72" 
	  alt="<?php print $moonage; ?>" title="<?php print $moonage; ?>"/></td>
     <?php } else { // ?>
    <img src="<?php echo $imagesDir . 'moon' . $moonagedays; ?>.gif" width="72" height="72"
	  alt="<?php print $moonage; ?>" title="<?php print $moonage; ?>"/></td>
     <?php } ?>
  </tr>
  <tr>
      <td width="50%" align="center"><?php langtrans('Sunrise'); ?>: <?php print adjustWDtime($sunrise); ?><br />
	  <?php langtrans('Sunset'); ?>: <?php print adjustWDtime($sunset); ?><br />
	  <?php langtrans('Daylight'); ?>: <?php print $hoursofpossibledaylight; ?></td>
      <td width="50%" align="center">
	  <?php langtrans('Moonrise'); ?>: <?php print adjustWDtime($moonrise);
	  if(isset($moonrisedate)) { print ' (' . adjustWDdate($moonrisedate) . ')';} ?><br />
	  <?php langtrans('Moonset'); ?>: <?php print adjustWDtime($moonset);
	  if(isset($moonsetdate)) { print ' (' . adjustWDdate($moonsetdate) . ')';} ?><br />
	  <?php echo langtrans($moonphasename); ?><br/>
	  <?php print $moonphase; ?> <?php langtrans('Illuminated'); ?></td>
  </tr>
</table>
</div>

<br /><br />

<div align="center">
  <table width="99%" cellpadding="0" border="0" cellspacing="0">
    <tr>
      <td width="25%" align="center"><?php langtrans('First Quarter Moon') ;?></td>
      <td width="25%" align="center"><?php langtrans('Full Moon'); ?></td>
      <td width="25%" align="center"><?php langtrans('Last Quarter Moon'); ?></td>
      <td width="25%" align="center"><?php langtrans('New Moon'); ?></td>
    </tr>
    <tr>
      <td align="center">
      <img src="<?php print $SITE['imagesDir']; ?>moon-firstquar.gif" width="100" height="100" 
	  alt="<?php langtrans('First Quarter Moon'); ?>"
	  title="<?php langtrans('First Quarter Moon'); ?>"/></td>
      <td align="center">
      <img src="<?php print $SITE['imagesDir']; ?>moon-fullmoon.gif" width="100" height="100" 
	  alt="<?php langtrans('Full Moon'); ?>"
	  title="<?php langtrans('Full Moon'); ?>"/></td>
      <td align="center">
      <img src="<?php print $SITE['imagesDir']; ?>moon-lastquar.gif" width="100" height="100" 
	  alt="<?php langtrans('Last Quarter Moon'); ?>"
	  title="<?php langtrans('Last Quarter Moon'); ?>"/></td>
      <td align="center">
      <img src="<?php print $SITE['imagesDir']; ?>moon-newmoon.gif" width="100" height="100" 
	  alt="<?php langtrans('New Moon'); ?>"
	  title="<?php langtrans('New Moon'); ?>"/></td>
    </tr>
	<?php $sourceMonths = $SITE['monthNames']; // from Settings.php file for WD month names ?>
    <tr>
      <td width="25%" align="center">
	  <?php echo get_localdate($firstquarter); ?><br/>
	  <small><?php echo get_utcdate($firstquarter); ?></small></td>
      <td width="25%" align="center">
	  <?php echo get_localdate($fullmoon); ?><br/>
	  <small><?php echo get_utcdate($fullmoon); ?></small></td>
      <td width="25%" align="center">
	  <?php echo get_localdate($lastquarter); ?><br/>
	  <small><?php echo get_utcdate($lastquarter); ?></small></td>
      <td width="25%" align="center">
	  <?php echo get_localdate($nextnewmoon); ?><br/>
	  <small><?php echo get_utcdate($nextnewmoon); ?></small></td>
    </tr>
  </table>
</div>
<br/><br/>
<div align="center">
<table width="99%" cellpadding="0" border="0" cellspacing="0">
    <tr>
      <td align="center"><?php langtrans('Vernal Equinox'); ?><br/>
	  <small><?php langtrans('Start of Spring'); ?></small></td>
      <td align="center"><?php langtrans('Summer Solstice'); ?><br/>
	  <small><?php langtrans('Start of Summer'); ?></small></td>
      <td align="center"><?php langtrans('Autumn Equinox'); ?>
	  <br/><small><?php langtrans('Start of Fall'); ?></small></td>
      <td align="center"><?php langtrans('Winter Solstice'); ?><br/>
	  <small><?php langtrans('Start of Winter'); ?></small></td>
    </tr>
    <tr>
      <td align="center">
      <img src="<?php print $SITE['imagesDir']; ?>earth-spring.jpg" width="125" height="125"
	   alt="<?php langtrans('Start of Spring'); ?>"
	   title="<?php langtrans('Start of Spring'); ?>"/></td>
      <td align="center">
<?php if($SITE['latitude'] >=0) { // Use Northern Summer ?>
      <img src="<?php print $SITE['imagesDir']; ?>earth-summer.jpg" width="125" height="125"
	<?php } else { // use Southern Summer image 
	?>
      <img src="<?php print $SITE['imagesDir']; ?>earth-winter.jpg" width="125" height="125" 
<?php } // end Northern Summer test ?>	
	   alt="First day of Summer"
	   title="First day of Summer"/></td>
      <td align="center">
      <img src="<?php print $SITE['imagesDir']; ?>earth-fall.jpg" width="125" height="125" 
	  alt="First day of Fall"
	  title="First day of Fall"/></td>
      <td align="center">
<?php if($SITE['latitude'] >=0) { // Use Northern Winter ?>
      <img src="<?php print $SITE['imagesDir']; ?>earth-winter.jpg" width="125" height="125" 
	<?php } else { // use Southern Winter image 
	 ?>
      <img src="<?php print $SITE['imagesDir']; ?>earth-summer.jpg" width="125" height="125"
<?php } // end Northern Winter test ?>	
	  alt="First day of Winter"
	  title="First day of Winter"/></td>
    </tr>
    <tr>
<?php if ($SITE['latitude'] >= 0) { // Use Northern Hemisphere dates with images?>
      <td align="center">
	  <?php echo get_localdate($marchequinox); ?><br/>
	  <small><?php echo get_utcdate($marchequinox); ?></small></td>
      <td align="center">
	  <?php echo get_localdate($junesolstice); ?><br/>
	  <small><?php echo get_utcdate($junesolstice); ?></small></td>
      <td align="center">
	  <?php echo get_localdate($sepequinox); ?><br/>
	  <small><?php echo get_utcdate($sepequinox); ?></small></td>
      <td align="center">
	  <?php echo get_localdate($decsolstice); ?><br/>
	  <small><?php echo get_utcdate($decsolstice); ?></small></td>
<?php } else { // Use Southern Hemisphere dates with images ?>
      <td align="center">
     <?php echo get_localdate($sepequinox); ?><br/>
	 <small><?php echo get_utcdate($sepequinox); ?></small></td>
      <td align="center">
     <?php echo get_localdate($decsolstice); ?><br/>
	 <small><?php echo get_utcdate($decsolstice); ?></small></td>
      <td align="center">
     <?php echo get_localdate($marchequinox); ?><br/>
	 <small><?php echo get_utcdate($marchequinox); ?></small></td>
      <td align="center">
     <?php echo get_localdate($junesolstice); ?><br/>
	 <small><?php echo get_utcdate($junesolstice); ?></small></td>
<?php } // end test for Hemisphere ?>
    </tr>
  </table>

<p>&nbsp;
<?php if(file_exists("moondetail1.gif")) { ?>
<img src="moondetail1.gif" alt="Moon Details from Weather-Display" style="border: white 1px solid" /><br/><br/>
<?php }
      if(file_exists("moondetail2.gif")) { ?>
<img src="moondetail2.gif" alt="Additional Moon facts from Weather-Display"  style="border: white 1px solid"/>
<?php } ?>
</p>
</div>
<?php } else { // show we need a weather station to be configured for this page
?>
<p>&nbsp;</p>
<p>Note: Astronomy information not available since weather station not yet specified.</p>
<?php for ($i=0;$i<10;$i++) { print "<p>&nbsp;</p>\n"; } ?>
<?php } // end of no station configured ?>


</div><!-- end main-copy -->
<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
# locally used functions
# routines adjusted to support World-ML template set

//=========================================================================
// decode WD %moonage% tag and return a text description for the moon phase name
// "Moon age: 10 days,10 hours,41 minutes,80%"

function moonphase ($WDmoonage) {

  preg_match_all('|(\d+)|is',$WDmoonage,$matches);
  print "<!-- WDmoonage='$WDmoonage' matches=\n" . print_r($matches,true) . "-->\n";
  $mdays = $matches[1][0];
  $mhours = $matches[1][1];
  $mmins = $matches[1][2];
  $mpct  = $matches[1][3];
  
  $mdaysd = $mdays + ($mhours / 24) + ($mmins / 1440);
  // Definitions from http://www.answers.com/topic/lunar-phase
  //  * Dark Moon - Not visible
  //  * New Moon - Not visible, or traditionally, the first visible crescent of the Moon
  //  * Waxing Crescent Moon - Right 1-49% visible
  //  * First Quarter Moon - Right 50% visible
  //  * Waxing gibbous Moon - Right 51-99% visible
  //  * Full Moon - Fully visible
  //  * Waning gibbous Moon - Left 51-99% visible
  //  * Third Quarter Moon - Left 50% visible
  //  * Waning Crescent Moon - Left 1-49% visible
  //  * New Moon - Not visible

  if ($mdaysd <= 29.53/2) { // increasing illumination
    $ph = "Waxing";
	$qtr = "First";
  } else { // decreasing illumination
    $ph = "Waning";
	$qtr = "Last";
  }
  
  if ($mpct < 1 ) { return("New Moon"); }
  if ($mpct <= 49) { return("$ph Crescent"); }
  if ($mpct < 51) { return("$qtr Quarter"); }
  if ($mpct < 99) { return("$ph Gibbous"); }
	return("Full Moon");
 }
# perform two-way translation on UTC String from WD  for astronomical dates
#  first, change from WD UTC date format (possibly in non-English) to English
#  second, change from English to targeted language (possibly leave in English)

function get_utcdate ( $indate ) {
  global $SITE, $sourceMonths;
  $EnglishMonths = array(
   'January','February','March','April','May','June',
   'July','August','September','October','November','December');

  $utcstr = substr($indate,10) . " " . substr($indate,0,9); // move formats

  if (isset($SITE['monthNames'])) {
    // convert TO English for strtotime()
    echo "<!-- before utcstr='$utcstr' -->\n";
	foreach ($EnglishMonths as $i => $monthEN) {
	   $utcstr = preg_replace('| '.$SITE['monthNames'][$i].' |i'," $monthEN ",$utcstr);
	}  
    echo "<!-- after utcstr='$utcstr' -->\n";
  } 
    $lclstr = $utcstr;
	
    if (isset($SITE['langMonths'])) {
    // convert From English for return (will only work if long-format month names in $timeFormat)  
    echo "<!-- before lclstr='$lclstr' -->\n";
	foreach ($EnglishMonths as $i => $monthEN) {
	   $lclstr = preg_replace('| '.$monthEN.' |i',' '.$SITE['langMonths'][$i].' ',$lclstr);
	}  
    echo "<!-- after lclstr='$lclstr' -->\n";
  }
  return ($lclstr);

}

function get_localdate ( $indate) {
  global $SITE;
  $EnglishMonths = array(
   'January','February','March','April','May','June',
   'July','August','September','October','November','December');
   
// Change '02:33 UTC 4 September 2007' to
//        specified date by  
  $timeFormat = 'D, d-M-Y h:ia T';  // Fri, 31-Mar-2006 14:03 TZone
//  $timeFormat = 'h:ia T D, d-M-Y ';  // Fri, 31-Mar-2006 14:03 TZone
  if(isset($SITE['timeFormat'])) { $timeFormat = $SITE['timeFormat']; }
  
  $utcstr = substr($indate,10) . " " . substr($indate,0,9); // move formats
  echo "<!-- input utcstr='$utcstr' -->\n";

 // input dates are assumed to be in English only
 if (isset($SITE['monthNames'])) {
    // convert TO English for strtotime()
    echo "<!-- before utcstr='$utcstr' -->\n";
	foreach ($EnglishMonths as $i => $monthEN) {
	   $utcstr = preg_replace('| '.$SITE['monthNames'][$i].' |i'," $monthEN ",$utcstr);
	}  
    echo "<!-- after utcstr='$utcstr' -->\n";
  }

  $utc = strtotime($utcstr);
  $lclstr = date($timeFormat,$utc);
  if (isset($SITE['langMonths'])) {
    // convert From English for return (will only work if long-format month names in $timeFormat)  
    echo "<!-- before lclstr='$lclstr' -->\n";
	foreach ($EnglishMonths as $i => $monthEN) {
	   $lclstr = preg_replace('| '.$monthEN.' |i',' '.$SITE['langMonths'][$i].' ',$lclstr);
	}  
    echo "<!-- after lclstr='$lclstr' -->\n";
  }
  return ($lclstr);
}

//=========================================================================
// change the hh:mm AM/PM to h:mmam/pm format or format spec by $timeOnlyFormat
function adjustWDtime ( $WDtime ) {
  global $timeOnlyFormat,$DebugMode;
  if ($WDtime == "00:00: AM") { return ''; }
  $t = explode(':',$WDtime);
  if (preg_match('/pm/i',$WDtime)) { $t[0] = $t[0] + 12; }
  if ($t[0] > 23) {$t[0] = 12; }
  if (preg_match('/^12.*am/i',$WDtime)) { $t[0] = 0; }
  $t2 = join(':',$t); // put time back to gether;
  $t2 = preg_replace('/[^\d\:]/is','',$t2); // strip out the am/pm if any
  $r = date($timeOnlyFormat , strtotime($t2));
  if ($DebugMode) {
    $r = "<!-- adjustWDtime WDtime='$WDtime' t2='$t2' -->" . $r;
    $r = '<span style="color: green;">' . $r . '</span>'; 
  }
  return ($r);
}
//=========================================================================
// adjust WD date to desired format
//
function adjustWDdate ($WDdate) {
  global $timeFormat,$timeOnlyFormat,$dateOnlyFormat,$WDdateMDY,$DebugMode;
  $d = explode('/',$WDdate);
  if(!isset($d[2])) {$d=explode("-",$WDdate); }
  if(!isset($d[2])) {$d=explode(".",$WDdate); }
  if ($d[2] > 70 and $d[2] <= 99) {$d[2] += 1900;} // 2 digit dates 70-99 are 1970-1999
  if ($d[2] < 99) {$d[2] += 2000; } // 2 digit dates (left) are assumed 20xx dates.
  if ($WDdateMDY) {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[0],$d[1]); //  M/D/YYYY -> YYYY-MM-DD
  } else {
    $new = sprintf('%04d-%02d-%02d',$d[2],$d[1],$d[0]); // D/M/YYYY -> YYYY-MM-DD
  }
  
  $r = date($dateOnlyFormat,strtotime($new));
  if ($DebugMode) {
    $r = "<!-- adjustDate WDdate='$WDdate', WDdateUSA='$WDdateMDY' new='$new' -->" . $r;
    $r = '<span style="color: green;">' . $r . '</span>'; 
  }
  return ($r);
}

?>