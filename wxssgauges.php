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
//Version 1.00 - 05-Feb-2013 - initial release
//Version 1.01 - 09-Feb-2013 - added default HTML if missing gauges-ss-basic-inc.php file.
require_once("Settings.php");
require_once("common.php");
############################################################################
$useHTML5 = true;  // force this page to use HTML5 instead of XHTML 1.0-Transitional
$useUTF8 = true;   // force this page to convert language files to UTF8 for display
#
$TITLE = langtransstr($SITE['organ']) . " - " .langtransstr('Current Weather Gauges');
$showGizmo = true;  // set to false to exclude the gizmo
include("top.php");
############################################################################
# Required setting:
#
$ssgDir = './ssg/'; // set to relative directory location of the SteelSeries gauge directory
#
# Note: the Steel Series Gauges are available from http://wiki.sandaysoft.com/a/SteelSeries_Gauges
# and must be installed in the subdirectory listed above and the scripts/gauges.js
# file configured for your weather software and location of the required
# data file (weather software specific).  The gauges-ss-basic-inc.php file is distributed
# by Mark with the SteelSeries distribution and should be in the $ssgDir directory.
#
############################################################################
?>
  <link rel="stylesheet" href="<?php echo $ssgDir;?>css/gauges-ss.css">

</head>
<body>
<?php
############################################################################
include("header.php");
############################################################################
include("menubar.php");
?>

<div id="main-copy">
  
   <h2><?php langtrans('Current Weather Gauges'); ?></h2>
   <p>&nbsp;</p>
<?php 
   if(! file_exists($ssgDir."gauges-ss-basic-inc.php")) {
	  print "<!-- Steel Series Gauges template (gauges-ss-basic-inc.php) not found in $ssgDir.  ".
	  "Using default HTML for gauges. -->\n";
	  gen_default_html($ssgDir);
	   
   } else {
	include_once($ssgDir."gauges-ss-basic-inc.php"); 
   }
	$ssLanguages = array(
	// available languages for the Steel Series Gauges by Mark Crossley
	// from the LANG.ll= entries in $ssGdir scripts/language.min.js file
	
	  'en' => 'EN', // english
	  'fr' => 'FR', // french
	  'de' => 'DE', // german
	  'nl' => 'NL', // dutch
	  'se' => 'SE', // swedish
	  'dk' => 'DK', // danish
	  'fi' => 'FI', // finnish (Suomi)
	  'no' => 'NO', // norwegian
	  'it' => 'IT', // italian
	  'es' => 'ES', // spanish
	  'ct' => 'CT', // catalan
	  'el' => 'GR', // greek
	); 
	if(isset($ssLanguages[$SITE['lang']])) {
	   $toLang = $ssLanguages[$SITE['lang']];
	} else {
	   $toLang = 'EN';
	   print "<p><small><strong>Note:</strong> no lang='".$SITE['lang']."' (".ucfirst($SITE['WULanguages'][$SITE['lang']]).")  language translation is available.  English is used for gauge legends instead.</small></p>\n";
	}
	   print "<script type=\"text/javascript\">
	   changeLang(LANG.$toLang);  // change SteelSeries language\n</script>\n";

 
?>
</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################

function gen_default_html ($ssgDir) {

print '<!-- default HTML is being used -->
  <noscript>
    <h2 style="color:red; text-align:center">&gt;&gt;This pages requires JavaScript enabling in your browser.&lt;&lt;<br>&gt;&gt;Please enable scripting it to enjoy this site at its best.&lt;&lt;</h2>
  </noscript>
  <div class="row">
    <canvas id="canvas_led" width="25" height="25"></canvas>&nbsp;&nbsp;&nbsp;
    <canvas id="canvas_status" width="350" height="25"></canvas>&nbsp;&nbsp;
    <canvas id="canvas_timer" width="50" height="25"></canvas>
  </div>
  <div class="row">
    <div class="gauge">
      <div id="tip_0">
        <canvas id="canvas_temp" width="221" height="221"></canvas>
      </div>
      <input id="rad_temp1" type="radio" name="rad_temp" value="out" checked onclick="gauges.doTemp(this);"><label id="lab_temp1" for="rad_temp1">Outside</label>
      <input id="rad_temp2" type="radio" name="rad_temp" value="in" onclick="gauges.doTemp(this);"><label id="lab_temp2" for="rad_temp2">Inside</label>
    </div>
    <div class="gauge">
      <div id="tip_1">
        <canvas id="canvas_dew" width="221" height="221"></canvas>
      </div>
      <input id="rad_dew1" type="radio" name="rad_dew" value="dew" onclick="gauges.doDew(this);"><label id="lab_dew1" for="rad_dew1">Dew Point</label>
      <input id="rad_dew2" type="radio" name="rad_dew" value="app" checked onclick="gauges.doDew(this);"><label id="lab_dew2" for="rad_dew2">Apparent</label>
      <br>
      <input id="rad_dew3" type="radio" name="rad_dew" value="wnd" onclick="gauges.doDew(this);"><label id="lab_dew3" for="rad_dew3">Wind Chill</label>
      <input id="rad_dew4" type="radio" name="rad_dew" value="hea" onclick="gauges.doDew(this);"><label id="lab_dew4" for="rad_dew4">Heat Index</label>
      <br>
      <input id="rad_dew5" type="radio" name="rad_dew" value="hum" onclick="gauges.doDew(this);"><label id="lab_dew5" for="rad_dew5">Humidex</label>
    </div>
    <div class="gauge">
      <div id="tip_4">
        <canvas id="canvas_hum" width="221" height="221"></canvas>
      </div>
      <input id="rad_hum1" type="radio" name="rad_hum" value="out" checked onclick="gauges.doHum(this);"><label id="lab_hum1" for="rad_hum1">Outside</label>
      <input id="rad_hum2" type="radio" name="rad_hum" value="in" onclick="gauges.doHum(this);"><label id="lab_hum2" for="rad_hum2">Inside</label>
    </div>
  </div>
  <div class="row">
    <div id="tip_6" class="gauge">
      <canvas id="canvas_wind" width="221" height="221"></canvas>
    </div>
    <div id="tip_7" class="gauge">
      <canvas id="canvas_dir" width="221" height="221"></canvas>
    </div>
    <div id="tip_10" class="gauge">
      <canvas id="canvas_rose" width="221" height="221"></canvas>
    </div>
  </div>
  <div class="row">
    <div id="tip_5" class="gauge">
      <canvas id="canvas_baro" width="221" height="221"></canvas>
    </div>
    <div id="tip_2" class="gauge">
      <canvas id="canvas_rain" width="221" height="221"></canvas>
    </div>
    <div id="tip_3" class="gauge">
      <canvas id="canvas_rrate" width="221" height="221"></canvas>
    </div>
  </div>
  <div class="row">
    <div id="tip_8" class="gauge">
      <canvas id="canvas_uv" width="221" height="221"></canvas>
    </div>
    <div id="tip_9" class="gauge">
      <canvas id="canvas_solar" width="221" height="221"></canvas>
    </div>
  </div>

  <div class="unitsTable">
    <div style="display:table-row">
      <div id="temperature" class="cellRight">
        <span id="lang_temperature">Temperature</span>:
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsTemp1" type="radio" name="rad_unitsTemp" value="C" checked onclick="gauges.setUnits(this);"><label id="lab_unitsTemp1" for="rad_unitsTemp1">&deg;C</label>
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsTemp2" type="radio" name="rad_unitsTemp" value="F" onclick="gauges.setUnits(this);"><label id="lab_unitsTemp2" for="rad_unitsTemp2">&deg;F</label>
      </div>
    </div>
    <div style="display:table-row">
      <div id ="rainfall" class="cellRight">
        <span id="lang_rainfall">Rainfall</span>:
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsRain1" type="radio" name="rad_unitsRain" value="mm" checked onclick="gauges.setUnits(this);"><label id="lab_unitsRain1" for="rad_unitsRain1">mm</label>
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsRain2" type="radio" name="rad_unitsRain" value="in" onclick="gauges.setUnits(this);"><label id="lab_unitsRain2" for="rad_unitsRain2">Inch</label>
      </div>
    </div>
    <div style="display:table-row">
      <div id="pressure" class="cellRight">
        <span id="lang_pressure">Pressure</span>:
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsPress1" type="radio" name="rad_unitsPress" value="hPa" checked onclick="gauges.setUnits(this);"><label id="lab_unitsPress1" for="rad_unitsPress1">hPa</label>
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsPress2" type="radio" name="rad_unitsPress" value="inHg" onclick="gauges.setUnits(this);"><label id="lab_unitsPress2" for="rad_unitsPress2">inHg</label>
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsPress3" type="radio" name="rad_unitsPress" value="mb" onclick="gauges.setUnits(this);"><label id="lab_unitsPress3" for="rad_unitsPress3">mb</label>
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsPress4" type="radio" name="rad_unitsPress" value="kPa" onclick="gauges.setUnits(this);"><label id="lab_unitsPress4" for="rad_unitsPress4">kPa</label>
      </div>
    </div>
    <div style="display:table-row">
      <div id="wind" class="cellRight">
        <span id="lang_windSpeed">Wind Speed</span>:
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsWind4" type="radio" name="rad_unitsWind" value="km/h" checked onclick="gauges.setUnits(this);"><label id="lab_unitsWind4" for="rad_unitsWind4">km/h</label>
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsWind3" type="radio" name="rad_unitsWind" value="m/s" onclick="gauges.setUnits(this);"><label id="lab_unitsWind3" for="rad_unitsWind3">m/s</label>
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsWind1" type="radio" name="rad_unitsWind" value="mph" onclick="gauges.setUnits(this);"><label id="lab_unitsWind1" for="rad_unitsWind1">mph</label>
      </div>
      <div style="display:table-cell">
        <input id="rad_unitsWind2" type="radio" name="rad_unitsWind" value="kts" onclick="gauges.setUnits(this);"><label id="lab_unitsWind2" for="rad_unitsWind2">knots</label>
      </div>
    </div>
  </div>
  <!-- Credits -->
  <div class="credits" style="padding: 0px 10px 10px 10px; text-align: left">
    <hr>
    Scripts by Mark Crossley - version <span id="scriptVer"></span><br>
    Gauges drawn using Gerrit Grunwald\'s <a href="http://harmoniccode.blogspot.com" target="_blank">SteelSeries</a> <a href="https://github.com/HanSolo/SteelSeries-Canvas">JavaScript library</a>
    <span id="rgraph_attrib"><br>Wind Rose drawn using <a href="http://www.rgraph.net/">RGraph</a></span>
    <br>
    powered by <span id="programName"></span> v<span id="programVersion"></span> (b<span id="programBuild"></span>)
  </div><!-- Credits -->

  <!-- Included Scripts -->

  <!-- Google CDN hosted JQuery library -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
  <!-- or... -->
  <!-- Local JQuery library, do not use if your containing page already pulls in a copy of JQuery -->
  <!-- <script src="scripts/jquery-1.8.2.min.js"></script> -->

  <!-- Combined steelseries.js & tween.js -->
  <script src="'.$ssgDir.'scripts/steelseries_tween.min.js"></script>

  <!-- Once you have customised this scripts to your requirements you should minimise
       and concatenate them into a single file in the same order  as below -->
  <script src="'.$ssgDir.'scripts/language.min.js"></script>
  <script src="'.$ssgDir.'scripts/gauges.js"></script>

  <!--Optional Wind Rose scripts -->

  <script src="'.$ssgDir.'scripts/windrose.js"></script>
  <script src="'.$ssgDir.'scripts/RGraph.common.core.min.js"></script>
  <script src="'.$ssgDir.'scripts/RGraph.radar.min.js"></script>
';
return;
}
?>