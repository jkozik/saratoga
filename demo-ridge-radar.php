<?php
#######################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-USA template set)
#######################################################################
#
#   Project:    Sample Included Website Design
#   Module:     sample.php
#   Purpose:    Sample Page
#   Authors:    Kevin W. Reed <kreed@tnet.com>
#               TNET Services, Inc.
#
# 	Copyright:	(c) 1992-2007 Copyright TNET Services, Inc.
#######################################################################
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
#######################################################################
#	This document uses Tab 4 Settings
#######################################################################
if ( isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
//--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header('Pragma: public');
   header('Cache-Control: private');
   header('Cache-Control: no-cache, must-revalidate');
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header('Connection: close');
   
   readfile($filenameReal);
   exit;
}
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE= $SITE['organ'] . " - Animated Ridge Radar";
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

<?php include("./ridge-radar/ridge.php"); ?>

<div id="main-copy">

<br/>
  
<div style="margin-top:5px; margin-bottom:12px; font-size: 18px; color: #000000; font-family:arial, tahoma, times new roman;" align="center">
<b>Chicago Base Reflectivity Radar</b>
</div>

<br/>

<div align="center">

<!-- Object code -->
<object type="application/x-shockwave-flash" data="http://napervilleweather.net/ridge-radar/flanis/flanis.swf" width="620" height="700" id="FlAniS">
<param name="movie" value="http://napervilleweather.net/ridge-radar/flanis/flanis.swf"/>
<param name="quality" value="high"/>
<param name="menu" value="false"/>
<param name="FlashVars" value="debug=false&amp;overlay_gap = 1&amp;controls_gap = 3&amp;backcolor = 0x00365F&amp;autorefresh_labels = AutoUpdate is Off, AutoUpdate is On,&amp;controls=framelabel&amp;bottom_controls = overlay, startstop, looprock, step,firstlast,speed,autorefresh/off, refresh,toggle&amp;bottom_controls_tooltip= overlay,Start-n-stop the animation,Toggle between loop and rock modes,step,Go to first or last image,speed,Turn auto update on/off,Refresh images,Click on frame square to remove from animation; Click again to add it&amp;overlay_zoom = y,y,y,y,y,y,y,n&amp;overlay_labels = Topo/on, Radar/on, Counties/on, Rivers, Highways/on, Cities/on, Warnings/on, Legend/on&amp;overlay_labels_mouseover_color = #FFCC00, #FFCC00, #FFCC00, #FFCC00, #FFCC00, #FFCC00, #FFCC00, #FFCC00&amp;file_of_filenames=ridge-radar/<?php echo $radarstation2; ?>_<?php echo $radartype; ?>_overlayfiles.txt&amp;transparency = x000000&amp;no_enh=true&amp;pause=500&amp;keep_zoom=true&amp;frame_label_width = 190&amp;active_zoom = true&amp;keep_zoom = true&amp;forecolor = xffffff&amp;zoom_factor = 10"/>
</object>
<!-- End object code -->

<!-- Object footer text -->
<table style="border-collapse:collapse;" width="620" align="center">
<tr><td style="color:#ffffff; background-color:#00365F; font-size:11px; font-family:arial; width:620px;" align="center">Left click to <b>zoom</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; "Ctrl" + Left click to <b>zoom out</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; "Alt" + Left click to <b>zoom out completely</b>
</td></tr></table>
<!-- End object footer code -->

<table style="font-size:10px; color:#000000; width:620px;" align="center">
<tr><td align="left">
Images courtesy of <a href="http://www.weather.gov/">NOAA</a></td>
<td align="right">
Script developed by: <a href="http://www.eldoradocountyweather.com/">El Dorado Weather</a></div>
</td></tr></table>

<br/><br/>

<?php @include("./radar-status.php"); ?>

</div>

<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>

</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>
