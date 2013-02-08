<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-World-ML template set)
############################################################################
#
#	Project:	Sample Included Website Design
#	Module:		header.php
#	Purpose:	Provides the displayable top of the website
# 	Authors:	Kevin W. Reed <kreed@tnet.com>
#				TNET Services, Inc.
#               Ken True <webmaster@saratoga-weather.org>
#               Saratoga-Weather.org
#
# 	Copyright:	(c) 1992-2007 Copyright TNET Services, Inc.
###########################################################################
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
//Version 1.01 - 28-Jul-2012 - integrated support for nws-alerts scripts
require_once("Settings.php");
require_once("common.php");

// add support for noCron=true fetch of nws-alerts to get current alerts
    if(isset($SITE['NWSalertsCodes']) and count($SITE['NWSalertsCodes']) > 0) {
		include_once("nws-alerts-config.php"); // load the configuration for nws-alerts
		if(isset($noCron) and $noCron) {
			print "<!-- nws-alerts noCron=true .. running nws-alerts.php inline -->\n";
			include_once("nws-alerts.php");
		}
	}

############################################################################
if (isset($SITE['uomTemp']) ) {
  $tuom = $SITE['uomTemp'];
} else {
$tuom = split('°',$temperature);  // extract units
$tuom = '&deg;' . $tuom[1];
}
?>
<div id="page"><!-- page wrapper -->
<!-- header -->
    <div id="header">
      <h1 class="headerTitle">
        <a href="index.php" title="Browse to homepage"><?php echo langtransstr($SITE['organ']); ?></a>
      </h1>	
	  <div class="headerTemp">
	    <span class="doNotPrint">
 		  <span class="ajax" id="ajaxbigtemp"><?php print isset($tempnodp)?"$tempnodp$tuom":"&nbsp;"; ?>
		  </span>
		</span>
 	  </div>

      <div class="subHeader">
        <?php echo $SITE['location']; ?>
		<?php if($SITE['allowLanguageSelect']) { // insert code for language select ?>
			   <br />
		<!-- begin language select -->
		<?php echo print_language_selects(); ?>
		<!-- end language select -->
		<?php } // end code for language select ?>
      </div>
      <div class="subHeaderRight">
	  <?php 
		if (isset($showGizmo) and $showGizmo) {
		  include_once("ajax-gizmo.php");
		} else {
		  print "&nbsp;<br/><br/>\n"; // needed as placeholder if no gizmo
		}
	  ?>
	  </div><!-- end subHeaderRight -->
</div>
<!-- end of header -->	
