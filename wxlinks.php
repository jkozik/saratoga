<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (Canada/World template set)
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
$TITLE = langtransstr($SITE['organ']) . " - " .langtransstr('Useful Links');
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
  
	<h3><?php langtrans('Useful Links'); ?></h3> 

        <ul>
		  <li>  <a href="http://www.wxforum.net"
		    title="The Independent Weather Enthusiasts Forum">
			WXForum.net</a></li>
          <li>	<a href="http://www.wxqa.com/"
			title="CWOP">
			Citizen Weather Observer Program</a></li>
          <li>	<a href="http://www.wunderground.com/weatherstation/index.asp"
			title="Weather Underground">
			Weather Underground Personal Station Signup</a></li>
          <li>	<a href="http://www.hamweather.net/weatherstations/"
			title="WeatherForYou Signup">
			WeatherForYou Signup Page</a></li>
          <li>	<a href="http://www.anythingweather.com/contactjoinnetwork.aspx"
			title="AnythingWeather Signup Page">
			AnythingWeather Signup Page</a></li>
          <li>	<a href="http://www.awekas.at/en/index.php"
			title="AWEKAS Signup">
			AWEKAS Signup Page</a></li>
        </ul>

	<h3><?php langtrans('Weather Education'); ?></h3> 

        <ul>
          <li>	<a href="http://amsglossary.allenpress.com/glossary"
			title="Meterology Terms">
			Glossary of Meteorology</a></li>
          <li>	<a href="http://www.ofcm.gov/fmh-1/fmh1.htm"
			title="Handbook for US Meterology">
			Federal Meteorological Handbook No. 1</a></li>
          <li>	<a href="http://asd-www.larc.nasa.gov/SCOOL/tutorial/clouds/cloudtypes.html"
			title="Cool audio/visual guide">
			S'COOL Cloud Types Audio/Visual Tutorial</a></li>
          <li>	<a href="http://eo.ucar.edu/webweather/"
			title="Web Weather for Kids">
			Web Weather for Kids</a></li>
          <li>	<a href="http://www.education.noaa.gov"
			title="NOAA Education Resources">
			NOAA Education Resources</a></li>
          <li>	<a href="http://www.weather.gov/om/reachout/kidspage.shtml"
			title="NWS Weather Website for Kids">
			NWS Weather Website for Kids</a></li>
          <li>	<a href="http://www.usatoday.com/weather/resources/basics/wworks0.htm"
			title="Weather Basics">
			USA Today - Weather Basics</a></li>
        </ul>

	<h3><?php langtrans('Weather Station Info'); ?></h3> 

        <ul>
          <li>	<a href="http://mywebpages.comcast.net/dshelms/CWOP_Guide.pdf"
			title="CWOP Station Setup Guide">
			CWOP Station Setup Guide</a> (PDF)</li>
          <li>	<a href="http://sandaysoft.com/products/cumulus"
			title="Cumulus weather software">
			Cumulus Weather Station software</a></li>
          <li>	<a href="http://www.weather-display.com/"
			title="Weather Station Software">
			Weather-Display Weather Station Software</a></li>
          <li>	<a href="http://www.weather-watch.com/smf/"
			title="Weather-Display Weather Forums">
			Weather Watch Forums (Weather Display)</a></li>
         <li>	<a href="http://www.ambientweather.com/station.html"
			title="Install Guide">
			Ambient Weather: Station Reviews and Install Guide, Virtual Weather Station software</a></li>
          <li>	<a href="http://www.davisnet.com/weather/index.asp"
			title="Davis Instruments">
			Davis Instruments Weather stations, WeatherLink software</a></li>
          <li>	<a href="http://www.lacrossetechnology.com/"
			title="La Crosse Technology">
			La Crosse Technology</a></li>
          <li>	<a href="http://www2.oregonscientific.com/catalog/subcategory.asp?c=2&amp;s=4"
			title="Oregon Scientific">
			Oregon Scientific</a></li>
        </ul>
		
	<h3><?php langtrans('Weather Website PHP Scripts'); ?></h3>
		<ul>
		   <li> <a href="http://www.carterlake.org/weatherphp.php">Carterlake.org Scripts</a></li>
		   <li> <a href="http://www.jcweather.us/scripts.php">Jcweather.us Scripts</a></li>
		   <li> <a href="http://www.642weather.com/weather/scripts.php">Long Beach Weather Scripts</a></li>
		   <li> <a href="http://saratoga-weather.org/scripts.php">Saratoga-Weather.org Scripts</a></li>
		   <li> <a href="http://www.tnetweather.com/scripts.php">TNETWeather.com Scripts</a></li>
		 </ul>

</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>