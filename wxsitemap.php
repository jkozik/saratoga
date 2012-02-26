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
$TITLE = langtransstr($SITE['organ']) . " - " .langtransstr("Website Map");
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
  
	<h1><?php langtrans('Website Map'); ?></h1>
    
    <?php if(isset($SITE['flyoutmenu']) and $SITE['flyoutmenu']) {
	
	     print $FlyoutMenuText;
	     } else {
		 fixup_trans($html); // from Menubar
		 }
	?>
    <p>&nbsp;</p>
</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################

// generate navigation links script - Ken True - webmaster@saratoga-weather.org

function fixup_trans($html) {
 preg_match('|<ul>(.*)</ul>|is', $html, $betweenspan); // find the navigation div
 $rawlinks  = $betweenspan[1]; 

 // Chop up each link and place in array 
 preg_match_all("/<li.*>(.*)<\/li>/Uis", $rawlinks, $betweenspan);  
 $links = $betweenspan[1]; 

 $ourPage = $_SERVER['PHP_SELF'];
 $doDebug = false;
 echo "<!-- this page='$ourPage' -->\n";
 if (isset($_REQUEST['page']) ) { // support testing
   $ourPage = $_REQUEST['page'];
   echo "<!-- using page='$ourPage' as test -->\n";
   $doDebug = true;
 }
 if (isset($_REQUEST['debug']) ) { // support testing
   $doDebug = true;
 }
  $t = pathinfo($ourPage); // extract base filename from link
  if ($doDebug) {print "<!-- pathinfo\n" . print_r($t,true) . "-->\n"; }
  $ourPage = $t['basename'];
  
// Now generate the code 
 print "<!-- navigation links from menubar.php -->\n";
 print "<ul>\n";
 
 foreach ($links as $i => $link) {
   if (preg_match('|<a.*href="([^"]+)"[^>]*>(.*)</a>|i',$link,$matches) ) {
	   if($doDebug) {  print "<!-- Matches for i=$i '$link': \n" . print_r($matches,true) . "-->\n"; }
	
	 //Matches: Array
	 //(
	 //   [0] => <a href="index.php" title="Current Conditions/Home">Home</a>
	 //   [1] => index.php
	 //   [2] => Home
	 //)
	
	    $t = langtransstr($matches[2]);
		print "<li>" . str_replace($matches[2],$t,$matches[0]) . "</li>\n";
    } else { // must be just text
      print "<li>" . $link . "</li>\n";
    } // end if has an <a href=
 }
 print "</ul>\n<!-- end of navigation links from menubar.php -->\n";
 } // end of gen_navlinks function

?>