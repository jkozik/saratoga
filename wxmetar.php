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
# Version 1.00 - 17-Nov-2011 - initial release
# Version 1.01 - 27-Nov-2011 - display 'Distance to station' mods
#
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE = langtransstr($SITE['organ']) . " - " .langtransstr('Nearby METAR Reports');
$showGizmo = true;  // set to false to exclude the gizmo
include("top.php");
############################################################################
?>
<style type="text/css">
.bidi {
	unicode-bidi: embed;
}
</style>
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
  
	<h1><?php langtrans('Nearby METAR Reports'); ?></h1>
    <p>&nbsp;</p>

<?php
// Customize this list with your nearby METARs by
// using http://saratoga-weather.org/wxtemplates/find-metar.php to create the list below

$MetarList = array( // set this list to your local METARs 
// Metar(ICAO) | Name of station | dist-mi | dist-km | direction |
  'KDPA|Chicago/Dupage, Illinois, USA|11|18|NW|', // lat=41.9167,long=-88.2500, elev=231, dated=09-NOV-04
  'KLOT|Romeoville/Chi, Illinois, USA|12|19|S|', // lat=41.6167,long=-88.1000, elev=205, dated=09-NOV-04
  'KORD|Chicago Ohare, Illinois, USA|16|26|NE|', // lat=41.9833,long=-87.9333, elev=200, dated=09-NOV-04
  'KARR|Chicago/Aurora, Illinois, USA|19|30|W|', // lat=41.7667,long=-88.4833, elev=215, dated=09-NOV-04
  'KJOT|Joliet, Illinois, USA|19|31|S|', // lat=41.5167,long=-88.1667, elev=177, dated=09-NOV-04
  'KMDW|Chicago, Illinois, USA|19|31|E|', // lat=41.7833,long=-87.7500, elev=188, dated=09-NOV-04
  'KPWK|Palwaukee, Illinois, USA|25|41|NNE|', // lat=42.1167,long=-87.9000, elev=203, dated=09-NOV-04
  'KC09|Morris-Washburn, Illinois, USA|29|47|SSW|', // lat=41.4333,long=-88.4167, elev=178, dated=09-NOV-04
  'KDKB|De Kalb, Illinois, USA|31|51|WNW|', // lat=41.9333,long=-88.7000, elev=279, dated=09-NOV-04
  'KIGQ|Chicago/Lansing, Illinois, USA|35|56|ESE|', // lat=41.5333,long=-87.5333, elev=188, dated=09-NOV-04
  'KGYY|Gary Regional, Indiana, USA|38|61|ESE|', // lat=41.6167,long=-87.4167, elev=180, dated=31-OCT-11
  'KUGN|Waukegan, Illinois, USA|45|73|NNE|', // lat=42.4167,long=-87.8667, elev=222, dated=09-NOV-04
  'KRPJ|Rochelle/Koritz, Illinois, USA|50|80|W|', // lat=41.8833,long=-89.0833, elev=238, dated=09-NOV-04
// list generated Thu, 09-Feb-2012 7:36pm PST at http://saratoga-weather.org/wxtemplates/find-metar.php
);
$maxAge = 75*60; // max age for metar in seconds = 75 minutes
// end of customizations
#
# Note: you do not need to change the below settings .. your current values from Settings.php
# will be applied and replace what you change below.
#
$condIconDir = './ajax-images/';  // directory for ajax-images with trailing slash
$condIconType = '.jpg'; // default type='.jpg' -- use '.gif' for animated icons from http://www.meteotreviglio.com/
$uomTemp = '&deg;F';
$uomBaro = ' inHg';
$uomWind = ' mph';
$uomRain = ' in';
// optional settings for the Wind Rose graphic in ajaxwindiconwr as wrName . winddir . wrType
$wrName   = 'wr-';       // first part of the graphic filename (followed by winddir to complete it)
$wrType   = '.png';      // extension of the graphic filename
$wrHeight = '58';        // windrose graphic height=
$wrWidth  = '58';        // windrose graphic width=
$wrCalm   = 'wr-calm.png';  // set to full name of graphic for calm display ('wr-calm.gif')
$Lang = 'en'; // default language used (for Windrose display)
?>
<?php
  if(file_exists("include-metar-display.php")) {
	  include_once("include-metar-display.php");
  } else {
	  print "<p>Sorry.. include-metar-display.php not found</p>\n";
  }
?>
    
</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>
