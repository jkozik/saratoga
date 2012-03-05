<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-USA template set)
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
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE= $SITE['organ'] . " - " . $cityname . " Air Quality Index";
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
/////////////////////////////////////////////////////////////////////////////
//SETTINGS START HERE////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////


//ini_set('display_errors', 1); 
//error_reporting(E_ALL);

//Select your URLs at the following location http://feeds.enviroflash.info/
//Forecast URL
$urlForecast = "http://feeds.enviroflash.info/rss/forecast/23.xml";  //Chicago, IL
//Action Day URL
$urlActionDay = "http://feeds.enviroflash.info/rss/actionDay/23.xml";  //Chicago, IL
//Current Air Quality URL
$urlRealtime = "http://feeds.enviroflash.info/rss/realtime/23.xml";  //Chicago, IL
// Your City, State Name
//$cityname= "Baltimore, MD";
$cityname= "Naperville, IL";
// Images Directory (may not be needed, remove the // if needed)
// $imagesDir = "./ajax-images";

// Images can be found here:  http://www.airnow.gov (find your city)
// "Current AQI" image from airnow.gov found on your city page
//$hourlyaqiimage = "http://www.epa.gov/airnow/today/cur_aqi_baltimore_md.jpg";
$hourlyaqiimage = "http://www.epa.gov/airnow/today/cur_aqi_chicago_il.jpg";
// "Forecast" image from airnow.gov found on your city page
// Example: http://www.epa.gov/airnow/today/forecast_aqi_20100401_va_wv_md_de_dc.jpg  (20100401 will change daily)
// So, grab "http://www.epa.gov/airnow/today/forecast_aqi_20" then grab the end "_va_wv_md_de_dc.jpg"
// Code below will automatically insert the appropriate year, month and day (ie. "100401")
$forecastimagebegin = "http://www.epa.gov/airnow/today/forecast_aqi_20"; 
$forecastimageend = "_va_wv_md_de_dc.jpg"; 
//http://www.epa.gov/airnow/today/forecast_aqi_20120305_chicago_il.jpg
$forecastimageend = "_chicago_il.jpg";
// "AQI Animation" image from airnow.gov found on your city page
//$hourlyaqianimation = "http://www.epa.gov/airnow/today/anim_aqi_va_wv_md_de_dc.gif";
$hourlyaqianimation = "http://www.epa.gov/airnow/today/anim_aqi_chicago_il.gif";

/////////////////////////////////////////////////////////////////////////////
//END SETTINGS///////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
?>

<div id="main-copy"><br />

<h3><?php print"$cityname" ?> Air Quality Index</h3>
<hr id="snippets-1" /> <br/>


<?php

/////////////////////////////////////////////////////////////////////////////
//DO NOT MODIFY BELOW THIS OR SCRIPT MAY BREAK///////////////////////////////
/////////////////////////////////////////////////////////////////////////////

// GET THE DATA FROM EACH URL
getAQIdata($urlForecast, "forecast");
getAQIdata($urlRealtime, "realtime");
getAQIdata($urlActionDay, "actionday");

// arrays for comparing levels and values
$aqiValues = array('0-50', '51-100', '101-150', '151-200', '201-300', '301-500');
$aqiLevels = array('Good', 'Moderate', 'Unhealthy for Sensitive Groups', 'Unhealthy', 'Very Unhealthy', 'Hazardous');
$aqiGraphic = array('aqi_good_text.gif', 'aqi_mod_text.gif', 'aqi_usg_text.gif', 'aqi_unh_text.gif', 'aqi_vunh_text.gif', 'aqi_haz_text.gif');  

// set the variables
$locF1 = 'N/A';
$currentFdate = 'N/A';
$currentFvalue = 'N/A';
$currentFindex = 'N/A';
$currentFmeasure = 'N/A';
$nextFdate = 'N/A';
$nextFvalue = 'N/A';
$nextFindex = 'N/A';
$nextFmeasure = 'N/A';
$forecastAgency = 'N/A';
$lastFupdate = 'N/A';
$locR1 = 'N/A';
$CAQtime = 'N/A';
$realMeasure1 = 'N/A';
$realIndex1 = 'N/A';
$realValue1 = 'N/A';
$realMeasure2 = 'N/A';
$realIndex2 = 'N/A';
$realValue2 = 'N/A';
$realUpdate = 'N/A';
$dashbrdAQI = 'N/A';
$showGI = 'N/A';
$realTitle = 'N/A';

// REALTIME DATA AQUISITION
// get the location
if(preg_match('|Location:</b> (.*)</div>|', $realtime[0]["desc"])) {
   preg_match('|Location:</b> (.*)</div>|', $realtime[0]["desc"], $r1);
   $locR1 = trim(strip_tags($r1[1]));

   // get the date and time
   preg_match('|Current Air Quality:</b>(.*)<br /><br />|Uis', $realtime[0]["desc"], $r2);
   $CAQtime = trim(strip_tags($r2[1]));

   // strip out the needed data
   preg_match('|<br /><br />\n(.*) - (.*) AQI - (.*)<br />\n|Uis', $realtime[0]["desc"], $r3);
   $realIndex2 = 'N/A';
   $realValue2 = 'N/A';
   $realMeasure2 = 'N/A';

   // get the title/location
   preg_match('|<title>(.*) - Current|Uis', $realtime[0]["desc"], $r4);
   $CAQloc = $r4[1];
   print_r($CAQloc);

   // search for two AQI types
   if(preg_match('|<br /><br />\n(.*) - (.*) AQI - (.*)<br />(.*) - (.*) AQI - (.*)</div>|Uis', $realtime[0]["desc"])) {
      preg_match('|<br /><br />\n(.*) - (.*) AQI - (.*)<br />(.*) - (.*) AQI - (.*)</div>|Uis', $realtime[0]["desc"], $r3);
      $realIndex2 = trim(strip_tags($r3[4]));
      $realValue2 = trim(strip_tags($r3[5]));
      $realMeasure2 = trim(strip_tags($r3[6]));
   }

   $realIndex1 = trim(strip_tags($r3[1]));
   $realValue1 = trim(strip_tags($r3[2]));
   $realMeasure1 = trim(strip_tags($r3[3]));

   // get the last update
   preg_match('|Last Update: (.*)</i>|', $realtime[0]["desc"], $r4);
   $realUpdate = trim(strip_tags($r4[1]));
   
   
   $real2 = '';

   // if there is no 2nd type
   if($realMeasure2 !== 'N/A') {
     $real2 = '1';	
   }
}

// TODAYS FORECAST DATA AQUISITION
// get the location
if(preg_match('|Location:</b> (.*)</div>|', $forecast[0]["desc"])) {
	
   preg_match('|Location:</b> (.*)</div>|', $forecast[0]["desc"], $f1);
   $locF1 = trim($f1[1]);

   // strip out the needed data
   preg_match('|Today,(.*):(.*) - (.*)<br />|', $forecast[0]["desc"], $f2);
   $currentFdate = trim($f2[1]);

   // get the correct data between listed and NA values
   if(preg_match('|(.*) - (.*)|', $f2[2])) {
      $f2[2] = preg_replace('| - .*|', '', $f2[2]);
   }

   $currentFindex = trim($f2[2]);
   $currentFvalue = trim($f2[3]);
   $currentFmeasure = 'N/A';

   // get Acency
   preg_match('|Agency:</b> (.*) </div>|', $forecast[0]["desc"], $f4);
   $forecastAgency = trim($f4[1]);

   // get last update
   preg_match('|Last Update: (.*)</i>|', $forecast[0]["desc"], $f5);
   $lastFupdate = trim($f5[1]);
	  
   // get the correct data between listed and NA values
   if(preg_match('|Today,(.*):(.*) - (.*) - (.*)<br />|', $forecast[0]["desc"])) {
      preg_match('|Today,.*:.* - (.*) - (.*)<br />|', $forecast[0]["desc"], $f2a);	
      $currentFvalue = trim($f2a[2]);
      $currentFmeasure = trim($f2a[1]);
   }

   // remove AQI
   $currentFmeasure = preg_replace('| AQI|', '', $currentFmeasure);
   

   // if the value is N/A then replace it with a range of values according to the level
   if($currentFmeasure == 'N/A') {
      $key = array_search($currentFindex, $aqiLevels);
      $currentFmeasure = $aqiValues["$key"];  
   }

// TOMORROWS FORECAST DATA AQUISITION
   // strip out the needed data
   if(preg_match('|Tomorrow,(.*):(.*) - (.*)<br />|', $forecast[0]["desc"], $f3)) {

      preg_match('|Tomorrow,(.*):(.*) - (.*)<br />|', $forecast[0]["desc"], $f3);
      $nextFdate = trim($f3[1]);
   
      // get the correct data between listed and NA values
      if(preg_match('|(.*) - (.*)|', $f3[2])) {
         $f3[2] = preg_replace('| - .*|', '', $f3[2]);
      }

      $nextFindex = trim($f3[2]);
      $nextFvalue = trim($f3[3]);
      $nextFmeasure = 'N/A';

      // get the correct data between listed and NA values
      if(preg_match('|Tomorrow,(.*):(.*) - (.*) - (.*)<br />|', $forecast[0]["desc"])) {
         preg_match('|Tomorrow,.*:.* - (.*) - (.*)<br />|', $forecast[0]["desc"], $f3);
         $nextFvalue = trim($f3[2]);
         $nextFmeasure = trim($f3[1]);
      }

      // remove AQI
      $nextFmeasure = preg_replace('| AQI|', '', $nextFmeasure);

      // if the value is N/A then replace it with a range of values according to the level
      if($nextFmeasure == 'N/A') {
         $key = array_search($nextFindex, $aqiLevels);
         $nextFmeasure = $aqiValues["$key"];  
      }
   }
     
   $fore2 = '';

   // if there is no 2nd type
   if($nextFvalue !== 'N/A') {
     $fore2 = '1';	
   }
}


// ACTION DAY DATA AQUISITION
// get action day text
$adtext = '';

// grab data if there is an Action Day
if(preg_match('|<td valign="top">\s+(.*)\s+</td>|', $actionday[0]["desc"])) {
   preg_match('|<td valign="top">\s+(.*)\s+</td>|', $actionday[0]["desc"], $ad1);
   $adtext = trim($ad1[1]);
}

// grab data if there is NO Action Day alerts
if(preg_match('|<td valign="top">\s+(.*)\s+<div>|', $actionday[0]["desc"])) {
   preg_match('|<td valign="top">\s+(.*)\s+<div>|', $actionday[0]["desc"], $ad1);
   $adtext = trim($ad1[1]);
}


//// FUNCTIONS ////
// Data aquisition
function getAQIdata($AQIurl,$type) {
	global $$type;

	$$type = array();
	$typeA = $type;
	$typeA = new DOMDocument();
	$typeA->load($AQIurl);
	foreach ($typeA->getElementsByTagName('item') as $node) {
		$itemRSS = array ( 
			'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
			'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
			);
		array_push($$type, $itemRSS);
	}
}	

// Associate AQI level with graphic icon
function getGraphic($value, $levels, $graphic, $iDir) {
   global $levelImage;
   
   $var = array_search($value, $levels);
   if($value !== 'N/A') {
	   $levelImage = "<img src=\"".$iDir.$graphic["$var"]."\" alt=\"  ".$levels["$var"]."\" title=\"  ".$levels["$var"]."\" border=\"0\" />";
	   echo $levelImage;
   }
   else {
	   $levelImage = "<img src=\"".$iDir."aqi_nodata_text.gif\" alt=\"  No Data\" title=\"  No Data\" border=\"0\" />";
	   echo $levelImage;
   }
}
	
//// END OF FUNCTIONS

?>
 
<br />
<br />
<table width="680px" border="0" style="margin: 0px auto 0px auto";>
  <tr>
    <td><table width="360" border="0" style="border:solid; border-color: #CCCCCC;">
      <tr>
        <td class="table-top" style="text-align:center;"><strong>Realtime Air Quality Data</strong></td>
      </tr>
      <tr class="column-dark">
        <td align="center"><span style="font-size: 75%; color: black;"><strong>Realtime Location:</strong> <?php print $locR1; ?><br /><strong>Issue Date:</strong> <?php print $CAQtime; ?></span><br /><br /><span style="font-size: 95%; color: black;"><strong>Current Air Quality Index</strong></span><br /><br />
          <table width="60%">
  <tr>
  <td align="center"><!-- If "Current" Air Quality is not available display "no data" image otherwise display appropriate image  -->
  <?php getGraphic($realIndex1, $aqiLevels, $aqiGraphic, $imagesDir); ?>
  </td>
  <td align="center"><span style="font-size: 180%; color: black; font-weight:bold;">
  <!-- If "Current" Air Quality is not available display text "data not available" otherwise display actual AQI number  -->
  <?php
if ($realValue1 == "N/A") {
  echo "Data Not<br />Available";
} else {
	echo $realValue1;
	}
?></span>
  </td>
  </tr>
  </table><br /><br />
    <?php
if ($adtext == "") {
  echo "";
} else {
	echo "<strong>Action Day Alert</strong>:<br /><span style=\"font-size: 95%; color: white; \">".$adtext."</span>";
	}
?>
<br /></td>
      </tr>
      <tr>
        <td class="table-top" style="text-align:center;"><strong>Realtime AQI - Pollutant Details</strong></td>
      </tr>
      <tr class="column-dark">
        <td>
          <table width="342" border="0">
            <tr>
              <td width="278"><strong>Type:</strong>  <?php print $realMeasure1; ?><br />
                <strong>Index:</strong>  <?php print $realIndex1; ?><br />
                <strong>AQI Value:</strong>  <?php print $realValue1; ?></td>
              <td align="center" valign="middle"><?php getGraphic($realIndex1, $aqiLevels, $aqiGraphic, $imagesDir); ?></td>
              </tr>
             <?php  if($real2) { ?>
            <tr>
              <td><br /><strong>Type:</strong>  <?php print $realMeasure2; ?><br />
                <strong>Index:</strong>  <?php print $realIndex2; ?><br />
                <strong>AQI Value:</strong>  <?php print $realValue2; ?></td>
              <td align="center" valign="middle"><br /><?php getGraphic($realIndex2, $aqiLevels, $aqiGraphic, $imagesDir); ?></td>
              </tr>
              <?php if ($real2) ;}?>
          </table>
          <strong><span style="font-size: 75%; color: black;"><br />Last Update:</span></strong><span style="font-size: 75%; color: black;"><?php print $realUpdate; ?></span></td>
      </tr>
    </table></td>
    <td><table width="80%" border="0" align="center">
<tr>
<td colspan="2" align="center"><img src="<?php print${imagesDir}?>aqi.gif" width="221" height="102" alt="" /><br /><br /></td>
</tr>
<tr bgcolor="#00CC00">
<td><span style="font-size: 80%; color: black;">0-50</span></td>
    <td><span style="font-size: 80%; color: black;">Good</span></td>
  </tr>
 <tr bgcolor="#FFFF00">
    <td><span style="font-size: 80%; color: black;">51-100</span></td>
    <td><span style="font-size: 80%; color: black;">Moderate</span></td>
  </tr>
  <tr bgcolor="#FF6600">
    <td><span style="font-size: 80%; color: black;">101-150</span></td>
    <td><span style="font-size: 80%; color: black;">Unhealthy for Sensitive Groups</span></td>
  </tr>
  <tr bgcolor="#FF0000">
    <td><span style="font-size: 80%; color: black;">151-200</span></td>
    <td><span style="font-size: 80%; color: black;">Unhealthy</span></td>
  </tr>
  <tr bgcolor="#99004C">
    <td><span style="font-size: 80%; color: white;">201-300</span></td>
    <td><span style="font-size: 80%; color: white;">Very Unhealthy</span></td>
  </tr>
  <tr bgcolor="#7E0023">
    <td><span style="font-size: 80%; color: white;">301-500</span></td>
    <td><span style="font-size: 80%; color: white;">Hazardous</span></td>
  </tr>
</table></td>
  </tr>
</table>
<p><br />
</p>

<table width="<?php if($fore2) { echo "95%" ;} else { echo "362" ;} ?>" cellpadding="2" cellspacing="1" border="0" style="border:solid; border-color: #CCCCCC; margin: 0px auto 0px auto">
  <tr>
    <td colspan="<?php if($fore2) { echo "2" ;} else { echo "1" ;} ?>" class="table-top" style="text-align:center;"><strong>Air Quality Forecast</strong></td>
  </tr>
  <tr class="column-dark">
    <td height="47" colspan="<?php if($fore2) { echo "2" ;} else { echo "1" ;} ?>" align="center"><span style="font-size: 75%; color: black;"><strong>Forecast Location:</strong> <?php print $locF1; ?><br />
      <strong>Forecast Agency:</strong> <?php print $forecastAgency; ?></span></td>
  </tr>
<?php  if($fore2) { ?>
  <tr>
    <td colspan="2" class="table-top" style="text-align:center;"><strong>Two Day Forecast</strong></td>
  </tr>
<?php  ;}?>
  <tr>
    <td class="table-top" style="text-align:center;"><strong><?php if($fore2) { echo "Day One Forecast" ;} else { echo "Todays Forecast" ;} ?></strong></td>
<?php  if($fore2) { ?>
    <td class="table-top" style="text-align:center;"><strong>Day Two Forecast</strong></td>
<?php  ;}?>
  </tr>
  <tr class="column-dark">
    <td width="50%" align="center" valign="middle" style="text-align:center;">
    	<strong>Date:</strong> <?php print $currentFdate; ?><br />
      <strong>Type:</strong> <?php print $currentFvalue; ?><br />
      <strong>Index:</strong> <?php print $currentFindex; ?><br />
      <strong>AQI Value:</strong> <?php print $currentFmeasure; ?><br />
      <br />
      <?php getGraphic($currentFindex, $aqiLevels, $aqiGraphic, $imagesDir); ?>
      <br />
    </td>
<?php  if($fore2) { ?>
    <td width="50%" align="center" valign="middle" style="text-align:center;">
      <strong>Date:</strong> <?php print $nextFdate; ?><br />
      <strong>Type:</strong> <?php print $nextFvalue; ?><br />
      <strong>Index:</strong> <?php print $nextFindex; ?><br />
      <strong>AQI Value:</strong> <?php print $nextFmeasure; ?><br />
      <br />
      <?php getGraphic($nextFindex, $aqiLevels, $aqiGraphic, $imagesDir); ?>
      <br />
    </td>
<?php  ;}?>
  </tr>
  <tr class="column-dark">
    <td colspan="<?php if($fore2) { echo "2" ;} else { echo "1" ;} ?>"><strong><span style="font-size: 75%; color: black;"> Last Update:</span></strong><span style="font-size: 75%; color: black;"><?php print $lastFupdate; ?></span></td>
  </tr>
</table>

<br/>
<br/>
<br/>
<center>
<span style="font-size: 105%; color: black;"><strong>Current Hourly Air Quality Index Map</strong></span><br/>
<img src="<?php print $hourlyaqiimage ?>" border="0" width="525" height="400" alt="" />
<table width="540">
<tr align="center">
<td><img src="<?php print ${imagesDir}?>aqi_good_text.gif" alt="AQI: Good" title="AQI: Good" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_mod_text.gif" alt="AQI: Moderate" title="AQI: Moderate" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_usg_text.gif" alt="AQI: Unhealthy for Sensitive Groups" title="AQI: Unhealthy for Sensitive Groups" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_unh_text.gif" alt="AQI: Unhealthy" title="AQI: Unhealthy" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_vunh_text.gif" alt="AQI: Very Unhealthy" title="AQI: Very Unhealthy" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_haz_text.gif" alt="AQI: Hazardous" title="AQI: Hazardous" border="0" /></td>
</tr>
</table>
<br/>
<br/>
<span style="font-size: 105%; color: black;"><strong>Today's Air Quality Forecast</strong></span><br/>
<img src="<?php print $forecastimagebegin ?><?php print date("y"); ?><?php print date("m"); ?><?php print date("d"); ?><?php print $forecastimageend ?>" border="0" width="525" height="400" alt="" />
<table width="540">
<tr align="center">
<td><img src="<?php print ${imagesDir}?>aqi_good_text.gif" alt="AQI: Good" title="AQI: Good" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_mod_text.gif" alt="AQI: Moderate" title="AQI: Moderate" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_usg_text.gif" alt="AQI: Unhealthy for Sensitive Groups" title="AQI: Unhealthy for Sensitive Groups" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_unh_text.gif" alt="AQI: Unhealthy" title="AQI: Unhealthy" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_vunh_text.gif" alt="AQI: Very Unhealthy" title="AQI: Very Unhealthy" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_haz_text.gif" alt="AQI: Hazardous" title="AQI: Hazardous" border="0" /></td>
</tr>
</table>
<br/>
<br/>
<span style="font-size: 105%; color: black;"><strong>Today's Air Quality Timed Animation</strong></span><br/>
<img src="<?php print $hourlyaqianimation ?>" border="0" width="525" height="400" alt="" />
<table width="540">
<tr align="center">
<td><img src="<?php print ${imagesDir}?>aqi_good_text.gif" alt="AQI: Good" title="AQI: Good" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_mod_text.gif" alt="AQI: Moderate" title="AQI: Moderate" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_usg_text.gif" alt="AQI: Unhealthy for Sensitive Groups" title="AQI: Unhealthy for Sensitive Groups" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_unh_text.gif" alt="AQI: Unhealthy" title="AQI: Unhealthy" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_vunh_text.gif" alt="AQI: Very Unhealthy" title="AQI: Very Unhealthy" border="0" /></td>
<td><img src="<?php print ${imagesDir}?>aqi_haz_text.gif" alt="AQI: Hazardous" title="AQI: Hazardous" border="0" /></td>
</tr>
</table>
<br/>
<br/>
<table width="650">
<tr><td style="font-size: 9px; color: #CCC">Script courtesy of &nbsp;Michael Holden of  
<a href='http://www.relayweather.com/'><strong>Relay Weather</strong></a>. Data courtesy of <a href='http://www.airnow.gov/'><strong> Airnow.gov </strong></a></td></tr>
</table>
</center>
<br/>
<br/>



</div><!-- end page-copy -->

<?php   

############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>
