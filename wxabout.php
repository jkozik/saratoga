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
$TITLE= $SITE['organ'] . " - About Us";
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
?>

<div id="main-copy">
  
	<h3>About This Station</h3> 

	<p>The station is powered by a -somekindof- weather station.  The data is collected every X seconds and the site is updated every X minutes. This site and its data is collected using <a href="http://www.weather-display.com/">Weather Display</a> Software. The station is comprised of an anemometer, a rain gauge and a thermo-hydro sensor situated in optimal positions for highest accuracy possible.</p>

	<h3>About This City</h3> 

	<p>Springfield was founded in 1796 by settlers who were trying to find a passage to Maryland after mis-interpreting a passage in the Bible. In its early days, the city was the target of many Indian raids, and to this day many forts and trading posts remain (including Fort Springfield and Fort Sensible).</p>

	<p>The founder of Springfield was pioneer Jebediah Springfield, widely celebrated in the town as a brave and proud American hero. The town motto "a noble spirit embiggens the smallest man" is attributed to Jebediah.</p>

	<p>In the mid-20th century, the city reached perhaps the pinnacle of its success when it became the home of the Aquacar, a car which could be driven in water like a boat. At this point, the city's streets were literally paved with gold. But unfortunately, as related in the Are We There Yet? Guide to Springfield, this fortune imploded when it was discovered that the Aquacar was prone to spontaneous explosion after 10,000 miles and/or knots. The town has never really recovered from this tragedy (the gold was reportedly shipped to the Sultan of Brunei to encase one of his many elephant herds), but some heavy industry remains in the town, including factories for Ah! Fudge chocolate, Southern Cracker, fireworks, candy, and boxes, as well as a steel mill.</p>

	<h3>About This Website</h3> 

	<p>This site is a template design by <a href="http://www.carterlake.org">CarterLake.org</a> with PHP conversion by <a href="http://saratoga-weather.org/">Saratoga-Weather.org</a>.<br/>
	 Special thanks go to Kevin Reed at <a href="http://www.tnetweather.com">TNET Weather</a> for his work on the original Carterlake templates, and his design for the common website PHP management.<br/>
	 Special thanks to Mike Challis of <a href="http://www.642weather.com/weather/scripts.php">Long Beach WA</a> for his wind-rose generator, Theme Switcher and CSS styling help with these templates.<br/>
 Special thanks go to Ken True of <a href="http://saratoga-weather.org/">Saratoga-Weather.org</a> for the AJAX conditions display, dashboard and integration of the <a href="http://www.tnetweather.com/nb-0200/">TNET Weather common PHP site design</a> for this site. </p>

	<p>Template is originally based on <a href="http://capmex.biz/resources/designs-by-haran">Designs by Haran</a>.</p>

	<p>This template is XHTML 1.0 compliant. Validate the <a href="http://validator.w3.org/check/referer">XHTML</a> and <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a> of this page.</p>

</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>