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
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE = langtransstr($SITE['organ']) . " - " .langtransstr('Sample Blank Page');
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

<!-- WXSIM 3in1 HEAD START -->
<!-- Put this part inside <head> and </head>-tags, needed on ALL pages using 3in1 -->
<?php
if(isset($_GET[lang])) {$lang = $_GET[lang]; }
else if (isset($SITE['lang'])) {$lang = $SITE['lang']; }
else {$lang = 'fi';}           // not nessessary in Saratoga Templates where $lang defined
include 'wxsim3in1/wxall.settings.php';
echo $wxallhead;
?>
<!-- WXSIM 3in1 HEAD END -->


<!-- WXSIM 3in1 BODY START -->
<!-- Put this part where you want it to show up -->
<!-- You may want wrap it inside a div like this -->
<div style="width:<?php echo $mainwidth ?>px;font: 72% Tahoma;">
<?php
# Header with name & update-times (optional)
echo $wxallupdated;
echo '<h2>'.get_lang("WXSIM Forecast for:").' '.$wxallcity.'</h2>';

# Theese can be used stand alone or together
echo $wxalltop;     # "Top-forecast"
echo $wxallgraph;   # Graph
echo $wxallmain;    # Tabs
?>
</div>
<!-- WXSIM 3in1 BODY END -->

</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>
