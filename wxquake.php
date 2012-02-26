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
$TITLE= $SITE['organ'] . " - Nearby Earthquake Activity";
$showGizmo = true;  // set to false to exclude the gizmo
include("top.php");
############################################################################
?>
<style type="text/css">
.quake {
  width: 640px;
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
 <div align="center">
 <?php
  $doIncludeQuake = true;
  include("quake-USA.php");
  ?>
  <h2>Latest Earthquakes in the USA - Last 7 days</h2>

<p>USA earthquakes with M1+ located by USGS and <a href="http://earthquake.usgs.gov/eqcenter/aboutmaps.php#datasources">Contributing Agencies</a>.</p>
  <a href="http://earthquake.usgs.gov/eqcenter/recenteqsus/"
  title="Click to visit USGS website">
  <img src="http://earthquake.usgs.gov/eqcenter/recenteqsus/index.gif"
  height="630" width="514" 
  alt="USGS USA Earthquake Map - last 7 Days"
  align="middle" style="border: none" /></a>
  <p>Map and data courtesy of the <a href="http://earthquake.usgs.gov/eqcenter/recenteqsus/">US Geological Survey</a></p>
  </div><!-- end align="center" -->
</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>