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

	<p>The station is powered by a <a href="http://www.davisnet.com/weather/products/weather_product.asp?pnum=06163">Davis Vantage Pro2 Plus</a> weather station, located in the backyard of my residence in Naperville, Illinois USA.

The weather station is polled every few seconds with updates sent to this web site every 2 minutes.  The main software engine behind this web site is Virtual Weather Station (VWS) by <a href="http://www.ambientweather.com/">Ambient Weather</a>.

The station is composed of an anemometer, located on the roof of our house, and a sensor assembly, on my patio, that captures rain, solar, UV, temperature and  humidity.
</p>

	<h3>About Naperville</h3> 

Naperville (<a href="http://en.wikipedia.org/wiki/Naperville,_Illinois">WIKI</a>) is a city of 141000 located in the South West suburbs of Chicago.  As you can see on the Neighboring Stations report, I am one of many weather station enthusiasts living in the Naperville area.



	<h3>About This Website</h3> 
<p>The weather enthusiast community shares their weather data on the Internet leveraging many different software tools.  The VWS package has a built in website generater that creates a very nice single page weather web page.  My original web site, <a href="http://NapervilleWeather.com">NapervilleWeather.com</a> was built using VWS.  I first hosted this site back in 2005.  </p>

<p>Over the years many web design experts have created an open-source style community of weather website modules, where enthusiast like me can create sophisticated multi-page weather web sites by leveraging the work of others.  The following describes in more detail:</p>

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
