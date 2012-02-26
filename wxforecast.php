<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-World template set)
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
$TITLE= $SITE['organ'] . " - Forecast";
$showGizmo = true;  // set to false to exclude the gizmo
include("top.php");
############################################################################
// testing parameters
$fcstby = isset($_REQUEST['fcstby'])?strtoupper($_REQUEST['fcstby']):'';
if ($fcstby == 'NWS') {
$SITE['fcstscript']		= 'advforecast2.php';  // USA-only NWS Forecast script
$SITE['fcstorg']		= 'NWS';    // set to 'NWS' for NOAA NWS
}

if ($fcstby == 'EC') {

$SITE['fcstscript']   = 'ec-forecast.php';    // Canada forecasts from Environment Canada
$SITE['fcstorg']		= 'EC';    // set to 'EC' for Environment Canada
}
if ($fcstby == 'WU') {

$SITE['fcstscript']	= 'WU-forecast.php';    // Non-USA, Non-Canada Wunderground Forecast Script
$SITE['fcstorg']		= 'WU';    // set to 'WU' for WeatherUnderground
}
if ($fcstby == 'WXSIM') {

$SITE['fcstscript']	= 'plaintext-parser.php';    // WXSIM forecast
$SITE['fcstorg']		= 'WXSIM';    // set to 'WXSIM' for WXSIM forecast
}
// end of special testing parms

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
         <?php 
		 $doIncludeNWS = true; // handle advforecast2.php include
		 $doIncludeWU  = true; // handle WU-forecast include also
		 $doInclude	   = true; // handle ec-forecast and WXSIM include also
		 $doPrint	   = true; //  ec-forecast.php setting
		 include_once($SITE['fcstscript']); ?>

</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>