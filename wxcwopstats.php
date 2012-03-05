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
$TITLE= $SITE['organ'] . " - CWOP Data Check";
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
include("cwopstats.php");
echo "\n<!-- CWOP Stats version: $cwopver -->\n";
############################################################################
?>

<div id="main-copy">
<br />  
	<h3>Data Quality for <?php echo "$cityname"; ?><br />
		CWOP ID - <?php echo "$cwop"; ?></h3>	 
<br/> 
<hr id="snippets-1" /> <br/>
<table width="640" cellspacing="1" cellpadding="1">
  <tr>
    <td width="35"><img src="images/cwoplogo4848.png" alt="cwop" /></td>
    <td width="596"><form action='' method="get">
      <p>
        <select name="span" onchange="this.form.submit()">
          <option value=''> - Choose Time Span - </option>
          <option value='3d'>3 Days</option>
          <option value='7d'>7 Days</option>
          <option value='14d'>14 Days</option>
          <option value='4w'>4 Weeks</option>
          <option value='8w'>8 Weeks</option>
          <option value='13w'>13 Weeks</option>
          <option value='26w'>26 Weeks</option>
          <option value='39w'>39 Weeks</option>
          <option value='52w'>52 Weeks</option>
        </select>        
      </p>
      <div>
        <noscript>
        <pre><input name="submit" type="submit" value="Get Time Span" />    
          </pre>
          </noscript>
      </div>
    </form>
      <span style="font-size: 80%; color: black; ">Current Time Span Selected: <?php echo "$span"; ?></span></td>
  </tr>
</table>
  <script type="text/javascript">
        <!--
        function menu_goto( menuform ){
         selecteditem = menuform.logfile.selectedIndex ;
         logfile = menuform.logfile.options[ selecteditem ].value ;
         if (logfile.length != 0) {
          location.href = logfile ;
         }
        }
        //-->
  </script>
<br />
<br />
<table width="640" border="1" cellspacing="1" cellpadding="1">
  <tr>
    <td class="table-top" colspan="2"><span style="font-size: 125%; font-weight:bold;">Barometer Graph</span></td>
  </tr>
  <tr class="column-dark">
    <td><span style="font-size: 90%; color: black; font-weight:bold;">Madis Value: </span></td>
    <td align="left"><span style="font-size: 90%; color: black; "><?php echo "$qcbaro"; ?>%&nbsp;</span>
      <?php if ($qcbaro >= "90") {
  echo "<img src=\"images/check.png\" border=\"0\" width=\"17\" height=\"17\" alt=\"\" />";
} else {
	echo "<img src=\"images/redx.png\" border=\"0\" width=\"17\" height=\"17\" alt=\"\" />";
	} ?></td>
  </tr>
  <tr class="column-dark">
    <td><span style="font-size: 90%; color: black; font-weight:bold;">Data Span: </span></td>
    <td align="left"><span style="font-size: 90%; color: black; "><?php echo "$span"; ?></span></td>
  </tr>
  <tr class="column-dark">
    <td width="177"><span style="font-size: 90%; color: black; font-weight:bold;">Average Barometer Error:</span></td>
    <td width="450"><span style="font-size: 90%; color: black; "><?php echo "$avbaroerr"; ?></span></td>
  </tr>
  <tr class="column-dark">
    <td><span style="font-size: 90%; color: black; font-weight:bold;">Error Standard Deviation:</span></td>
    <td><span style="font-size: 90%; color: black; "><?php echo "$sdbaroerr"; ?></span></td>
  </tr>
  <tr class="column-light">
    <td colspan="2"><p><?php echo "<img src=\"".$chartbaro."\" alt=\"Baro Chart\" title=\"Baro Chart\" border=\"0\" />";?><br /><span style="font-size: 9px;">Calculations/Data courtesy of <a href='http://weather.gladstonefamily.net/'><strong> Phillip Gladstone.</strong></a> Script courtesy of &nbsp;Michael Holden of <a href='http://www.relayweather.com/'><strong>Relay Weather</strong></a>.</span></p></td>
  </tr>
</table>
  <br />
  <br />
  <br />
<br />
<br />
<table width="640" border="1" cellspacing="1" cellpadding="1">
  <tr>
    <td class="table-top" colspan="4"><span style="font-size: 125%;">Temperature Graph</span></td>
  </tr>
  <tr class="column-dark">
    <td align="left"><span style="font-size: 90%; color: black; font-weight:bold;">Madis Value: </span></td>
    <td colspan="3" align="left"><span style="font-size: 90%; color: black; "><?php echo "$qctemp"; ?>%&nbsp;</span>
    <?php if ($qctemp >= "90") {
  echo "<img src=\"images/check.png\" border=\"0\" width=\"17\" height=\"17\" alt=\"\" />";
} else {
	echo "<img src=\"images/redx.png\" border=\"0\" width=\"17\" height=\"17\" alt=\"\" />";
	} ?></td>
  </tr>
  <tr class="column-dark">
    <td align="left"><span style="font-size: 90%; color: black; font-weight:bold;">Data Span:  <?php echo "$span"; ?></span></td>
    <td align="center"><span style="font-size: 90%; color: black; font-weight:bold;">24 Hours</span></td>
    <td align="center"><span style="font-size: 90%; color: black; font-weight:bold;">Daytime</span></td>
    <td align="center"><span style="font-size: 90%; color: black; font-weight:bold;">Nighttime</span></td>
  </tr>
  <tr class="column-dark">
    <td width="175"><span style="font-size: 90%; color: black; font-weight:bold;">Average Temp. Error:</span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$avtemperr24"; ?></span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$avtemperrday"; ?></span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$avtemperrnite"; ?></span></td>
  </tr>
  <tr class="column-dark">
    <td><span style="font-size: 90%; color: black; font-weight:bold;">Error Standard Deviation:</span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$sdtemperr24"; ?></span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$sdtemperrday"; ?></span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$sdtemperrnite"; ?></span></td>
  </tr>
  <tr class="column-light">
    <td colspan="4"><p><?php echo "<img src=\"".$charttemp."\" alt=\"Temp Chart\" title=\"Temp Chart\" border=\"0\" />";?><br /><span style="font-size: 9px;">Calculations/Data courtesy of <a href='http://weather.gladstonefamily.net/'><strong> Phillip Gladstone.</strong></a> Script courtesy of &nbsp;Michael Holden of <a href='http://www.relayweather.com/'><strong>Relay Weather</strong></a>.</span></p></td>
  </tr>
</table>
<br />
<br />
<br />
<table width="640" border="1" cellspacing="1" cellpadding="1">
  <tr>
    <td class="table-top" colspan="4"><span style="font-size: 125%;">Dewpoint Graph</span></td>
  </tr>
  <tr class="column-dark">
    <td align="left"><span style="font-size: 90%; color: black; font-weight:bold;">Madis Value: </span></td>
    <td colspan="3" align="left"><span style="font-size: 90%; color: black; "><?php echo "$qcdewp"; ?>%&nbsp;</span>
    <?php if ($qcdewp >= "90") {
  echo "<img src=\"images/check.png\" border=\"0\" width=\"17\" height=\"17\" alt=\"\" />";
} else {
	echo "<img src=\"images/redx.png\" border=\"0\" width=\"17\" height=\"17\" alt=\"\" />";
	} ?></td>
  </tr>
  <tr class="column-dark">
    <td align="left"><span style="font-size: 90%; color: black; font-weight:bold;">Data Span:  <?php echo "$span"; ?></span></td>
    <td align="center"><span style="font-size: 90%; color: black; font-weight:bold;">24 Hours</span></td>
    <td align="center"><span style="font-size: 90%; color: black; font-weight:bold;">Daytime</span></td>
    <td align="center"><span style="font-size: 90%; color: black; font-weight:bold;">Nighttime</span></td>
  </tr>
  <tr class="column-dark">
    <td width="175"><span style="font-size: 90%; color: black; font-weight:bold;">Average Dewpoint Error:</span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$avdewerr24"; ?></span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$avdewerrday"; ?></span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$avdewerrnite"; ?></span></td>
  </tr>
  <tr class="column-dark">
    <td><span style="font-size: 90%; color: black; font-weight:bold;">Error Standard Deviation:</span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$sddewerr24"; ?></span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$sddewerrday"; ?></span></td>
    <td align="center"><span style="font-size: 90%; color: black; "><?php echo "$sddewerrnite"; ?></span></td>
  </tr>
  <tr class="column-light">
    <td colspan="4"><p><?php echo "<img src=\"".$chartdew."\" alt=\"Dew Chart\" title=\"Dew Chart\" border=\"0\" />";?><br /><span style="font-size: 9px;">Calculations/Data courtesy of <a href='http://weather.gladstonefamily.net/'><strong> Phillip Gladstone.</strong></a> Script courtesy of &nbsp;Michael Holden of <a href='http://www.relayweather.com/'><strong>Relay Weather</strong></a>.</span></p></td>
  </tr>
</table>
<br />
<br />
<br />
<table width="640" border="1" cellspacing="1" cellpadding="1">
  <tr>
    <td class="table-top" colspan="4"><span style="font-size: 125%;">Wind</span></td>
  </tr>
  <tr class="column-dark">
    <td align="left"><span style="font-size: 90%; color: black; font-weight:bold;">Madis Value: </span></td>
    <td colspan="3" align="left"><span style="font-size: 90%; color: black; "><?php echo "$qcwind"; ?>%&nbsp;</span>
    <?php if ($qcwind >= "90") {
  echo "<img src=\"images/check.png\" border=\"0\" width=\"17\" height=\"17\" alt=\"\" />";
} else {
	echo "<img src=\"images/redx.png\" border=\"0\" width=\"17\" height=\"17\" alt=\"\" />";
	} ?></td>
  </tr>
  <tr class="column-light">
    <td colspan="4"><p><img src="http://weather.gladstonefamily.net/cgi-bin/wxqchartwind.pl?site=<?php echo "$cwop"; ?>&amp;start=-7&amp;days=7" alt="Wind Vector" title="Wind Vector" border="0" /><br /><span style="font-size: 9px;">Calculations/Data courtesy of <a href='http://weather.gladstonefamily.net/'><strong> Phillip Gladstone.</strong></a> Script courtesy of &nbsp;Michael Holden of <a href='http://www.relayweather.com/'><strong>Relay Weather</strong></a>.</span></p></td>
  </tr>
</table>
<br />
<br />
<p><?php echo "$sitename"; ?> is a proud member of the <a href="http://www.wxqa.com">Citizen Weather Observer 
Program</a> (CWOP). The above charts represent data reported to CWOP for <?php echo "$cityname"; ?> (<?php echo "$cwop"; ?> actuals in <font color="#0000FF"><b>blue</b></font>) with the 
predicted data based on surrounding stations (<?php echo "$cwop"; ?> Analysis in <font color="#FF0000"><b>red</b></font>).</p>
<p><span style="font-size: 100%; color: black; font-weight:bold;">Data Quality:</span><br />The <a href="http://madis.noaa.gov/">MADIS</a> value represents the percentage of observations that have successfully passed the MADIS QC checks.  If the Madis rating is within the acceptable limits, a green check <img src="images/check.png" border="0" width="13" height="13" alt="" /> will appear.  Otherwise, a red x-mark <img src="images/redx.png" border="0" width="13" height="13" alt="" /> will appear indicating that the data has not passed quality control.
<br /><br /><span style="font-size: 100%; color: black; font-weight:bold;">Errors</span><br />  If the above errors are POSITIVE, this means that the analysis variable is HIGHER than the reported variable. This means that the sensor is reading a variable lower than expected.  If the above errors are NEGATIVE, this means that the analysis variable is LOWER than the reported variable. This means that the sensor is reading a variable higher than expected.</p>
<p><span style="font-size: 100%; color: black; font-weight:bold;">CWOP:</span><br />The Citizen Weather Observer Program (CWOP) is a private-public partnership with the National Oceanic &amp; Atmospheric Administration. Its 
three main goals: 1) to collect weather data contributed by citizens; 2) to make these data available for weather services; and 3) to provide 
feedback to the data contributors so that they have the tools to check and improve their data quality.</p><p> Many thanks go to Phillip Gladstone at CWOP
for his dedication to providing the needed accuracy and quality checks for the amateur weather observer.</p>
<br />
<br />
<br />
		
</div><!-- end main-copy -->




<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>