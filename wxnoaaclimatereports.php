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
//Version 1.01 - 27-Aug-2011 - added styling support to links/display of NOAA-style reports from beteljuice
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE = langtransstr($SITE['organ']) . " - " .langtransstr('NOAA-Style Climate Reports');
$showGizmo = true;  // set to false to exclude the gizmo
include("top.php");
############################################################################
?>
<style type="text/css">
/* default stylesheet for NOAA Style Reports ala beteljuice */
.noaa_rep_container {
    font-family: courier new,courier,monospace;
	width: 635px;
	margin: 0 auto;
}

.noaa_rep_container a, .noaa_rep_container a:link, .noaa_rep_container a:visited, .noaa_rep_container a:hover, .noaa_rep_container a:active {
    color: #000000; 
    text-decoration: none;
	background-color: white;
}

.noaa_rep_nav_container {
    font-family: lucida console, courier new, courier, monospace;
    font-size: 8pt;
    font-weight: bold;
    text-align: left;
    line-height: 2.2;
	text-align: center;
	}
	
.noaa_rep_nav {
    font-weight: bold;
    text-align: left;
    border: 1px solid #000000;
    border-radius: 5px;
    padding: 2px 4px;
	color: #666;
	background-color: #D0D0D0;
	}

a.noaa_rep_nav:hover {
	box-shadow: 0 0 7px #007EBF;
}	
	
.noaa_rep_container pre {
    color: #000000;
    font-family: monospace;
    font-size: 9pt;
    font-weight: normal;
    text-align: left;
    border: 1px solid #000000;
    border-radius: 10px 10px 10px 10px;
    padding: 20px 0px 25px 20px;
	background-color: #f9f8EB;
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
  
	<h1><?php langtrans('NOAA-Style Climate Reports'); ?></h1>
    
    <?php include_once("include-NOAA-reports.php") ?>
    
</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>