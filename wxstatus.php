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
//Version - 1.01 - 17-Feb-2011 - fixed date issue with ##-##-#### dates and deprecated use of split()
//Version - 1.02 - 11-Jun-2011 - fixed NOAA report check for WL and VWS
//Version - 1.03 - 07-Aug-2011 - added support for Meteohub 
//Version - 1.04 - 17-Nov-2011 - added translation capability to status
//Version - 1.05 - 20-Nov-2011 - corrected check for WD clientraw.txt realtime file
//Version - 1.06 - 21-Nov-2011 - standard checks moved to include-wxstatus.php, custom checks should be added to this page
//
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE= $SITE['organ'] . " - " . langtransstr("Station Status");
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
 
 <h1><?php langtrans('Weather Station Status'); ?></h1>
  <?php
    if(isset($SITE['WXsoftware']) and $SITE['WXsoftware'] <> '' ) {
	include_once($SITE['WXtags']);
	global $SITE;
  ?>
   <p><?php 
   langtransstr('This page shows the current status of the weather software used in the operation of this website.'); ?>
   <br/><br/>
   <?php if(isset($windowsuptime)) { ?>
  <?php langtrans('Station system up for'); ?> <b><?php print $windowsuptime; ?></b><br/>
  <?php } // end $windowsuptime ?>
   <?php if(isset($freememory)) { ?>
   <?php langtrans('Station system free memory'); ?> <b><?php print $freememory; ?></b><br/>
  <?php } // end $freememory ?>
   <?php langtrans('This website uses'); ?> <b><?php echo $SITE['WXsoftwareLongName'];?> 
  <?php if(isset($wdversion)) {echo " (".$wdversion.")";} ?></b> <?php langtrans('for weather conditions reporting') ?>.<br/>  
  <?php if(isset($Startimedate)) { langtrans("It was last started"); echo " <b>$Startimedate</b>."; } ?><br/></p>

  <table width="620" border="0" cellspacing="6" cellpadding="3" style="border-collapse:collapse">
    <tr>
      <th style="width: 40%" scope="col"><?php langtrans('Component'); ?></th>
      <th style="width: 15%" scope="col" align="center"><?php langtrans('Status'); ?></th>
      <th style="width: 10%" scope="col" align="right"><?php langtrans('Age'); ?><br />h:m:s</th>
      <th style="width: 35%" scope="col" align="right"><?php langtrans('Latest update time as of'); ?><br/> <?php print date($SITE['timeFormat'],time()); ?></th>
    </tr>
  
<?php 
// insert standard checks for software
  if(file_exists('include-wxstatus.php')) {
	  include_once('include-wxstatus.php');
  } else {
	  print "<tr><td colspan=\"4\" align=\"center\">";
	  print "Note: include-wxstatus.php not found .. no checks done.";
	  print "</td></tr>\n";
  }
############################################################################
#
# Add your custom checks below using the commented examples
#
# the do_check function has four arguments:
#   do_check($text,$target,$maxtime,$checktype);
#
# where:
#   $text is the text to display on the page.. always a good idea to run it
#        through langtransstr($text) for multilanguage support
#   $target is the item to be checked .. type of item is determined by $checktype
#
#     $target = relative file location/name if $checktype = 'file'
#     $target = unix timestamp (like from time() ) if $checktyp = 'application'
#
#   $maxtime is number of SECONDS expected between updates .. always allow a cushion
#        (extra time) to allow for delays in updates via FTP
#
#   $checktype defaults to 'file' type, use 'application' for timestamp checking from
#         applications.
#
#
############################################################################

/*
// example check for 'file' update time
        $tDir = './';
		if(file_exists($tDir.'somefile.ext')) {
           do_check(langtransstr("Some File"),$tDir.'somefile.ext',10*60+15,'file');
		
        }
	
// example check for 'application' update time (this WXSIM check is already in wxstatus.php)

		if(isset($SITE['WXSIM']) and file_exists('plaintext.txt')) {
	       $doPrint = false;
		   include_once($SITE['WXSIMscript']);
		   print "<!-- wdate='$wdate' -->\n";
		   $lastWXSIM = strtotime($wdate);
		   // note: '6*60*60 + 2*60' is 6:02:00 hms
           do_check(langtransstr("WXSIM forecast"),$lastWXSIM,6*60*60 + 2*60,'application');
		
        }

*/	
############################################################################
# start of custom checks (put your custom checks below)		



# end of custom checks
############################################################################
?>

  </table>
  
  <?php } else { // software not defined yet ?>
  <p>&nbsp;</p>
  <p>Weather Software not yet specified .. no status to report.</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <?php } // end software not defined ?>


</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>