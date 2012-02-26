<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-World-ML template set)
############################################################################
#
#	Project:	Sample Included Website Design
#	Module:		menubar.php
#	Purpose:	Provides the menubar for the system
# 	Authors:	Kevin W. Reed <kreed@tnet.com>
#				TNET Services, Inc.
#               Ken True <webmaster@saratoga-weather.org>
#               Saratoga-Weather.org
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
?>
<!-- menubar -->
<div class="doNotPrint">
      <div class="leftSideBar">
        <p class="sideBarTitle"><?php langtrans('Navigation'); ?></p>
<?php 
// NOTE: when adding new links to the site, put them in as <li></li> entries in
//   the <ul></ul> shown below.  The order of the links is the order they appear
//   in the left navigation menu.  
// Be careful to only use html as shown below, otherwise the program that
//   automatically shows which page you're on won't work quite right.
// Don't use single-quotes in the following area unless you prefix it with a 
//   backslash.
//   Correct:  "Steve\'s page"
//   Wrong:    "Steve's page"    <=== this will cause a PHP error in menubar.php
//
// Be sure to include a title="..." tag so folks can see more info via a tooltip
//   as they mouse over the link in the menu.
//Don't change the next line in any way
$html = '
        <ul>
          <li><a href="wxindex.php" title="Home">Home</a></li>
          <li><a href="wxforecast.php" title="5-day Forecast">Forecast</a></li>
          <li><a href="wxsimforecast.php" title="WXSIM Forecast">WXSIM Forecast</a></li>
          <li><a href="wxradar.php" title="Radar">Radar</a></li>
          <li><a href="wxadvisory.php" title="Watches, Warnings, Advisories">Advisories</a></li>
          <li><a href="wxtrends.php" title="Daily weather statistics">Daily Stats</a></li>
          <li><a href="wxgraphs.php" title="24, 72, &amp; Monthly Graphs">Graphs</a></li>
          <li><a href="wxastronomy.php" title="Sun and Moon Data">Astronomy</a></li>
          <li><a href="wxlinks.php" title="Useful Links">Links</a></li>
          <li><a href="wxabout.php" title="About This Site">About Us</a></li>
        </ul>
'; // end of links set for site. Don't change this line in any way
 if (isset($SITE['flyoutmenu']) and $SITE['flyoutmenu']) {
   global $FlyoutMenuText;
   print "<div class=\"flyoutmenu\">\n";
   print $FlyoutMenuText;
   print "</div>\n";
 } else {
	gen_navlinks($html); // generate the links set with highlight for the current page
 }
?>
<?php 

 if(isset($showSidebar) and $showSidebar) {
    include_once("ajax-sidebar.php");
 } 
# Note: add other links and stuff to the left sidebar above the </div><!-- end leftSidebar -->
# as shown in the sample below.
# Keep in mind that the width allowed is about 110px
#
# if you don't want anything to appear extra here, just delete the lines
# <!-- external links -->
#   down to and including
# <!-- end external links -->
?>
<!-- external links -->
<p class="sideBarTitle"><?php langtrans('External Links'); ?></p>
<ul>
   <li><a href="http://www.wunderground.com/" title="Weather Underground">Weather Underground</a></li>
   <li><a href="http://www.wxforum.net/" title="WXForum">WXforum.net</a></li>
</ul>
<!-- end external links -->
<?php if($SITE['allowThemeSwitch']) { // insert code for theme switcher ?>
  <!-- begin Color Theme Switcher Plugin http://www.642weather.com/weather/scripts.php -->
  <div class="thisPage" style="margin-left: 5px; font-weight: normal;">
  <?php print_css_style_menu(1); ?>
  </div>
  <!-- end Color Theme Switcher Plugin http://www.642weather.com/weather/scripts.php -->
<?php } // end code for theme switcher ?>
      </div><!-- end leftSidebar -->
</div><!-- end doNotPrint -->	
<!-- end of menubar -->
<?php 

if (isset($_REQUEST['show']) and strtolower($_REQUEST['show']) == 'settings') {
  print "<!-- current settings\n" . htmlentities(print_r($SITE,true)) . " -->\n";
}

// generate navigation links script - Ken True - webmaster@saratoga-weather.org

function gen_navlinks($html) {
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
 print "<!-- navigation links -->\n";
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
	
	  if ($matches[1] == $ourPage) {
		// we're on our page
		print "<li><span class=\"thisPage\">" . langtransstr($matches[2]) . "</span></li>\n";
	  } else {
	    $t = langtransstr($matches[2]);
		print "<li>" . str_replace($matches[2],$t,$matches[0]) . "</li>\n";
	  }
    } else { // must be just text
      print "<li>" . $link . "</li>\n";
    } // end if has an <a href=
 }
 print "</ul>\n<!-- end of navigation links -->\n";
 } // end of gen_navlinks function
?>
