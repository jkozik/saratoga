<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (Canada/World-ML template sets)
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
# Version 1.01 - 31-Mar-2012 - day-of-week fix for get-UV-forecast-inc.php V1.07
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE= $SITE['organ'] . " - ". langtransstr('UV Index Forecast');
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
$UVscript		= 'get-UV-forecast-inc.php'; // worldwide forecast script for UV Index
$ourTZ = "PST8PDT";  

$maxIcons = 7;  // maximum number of icons to display

// overrides from Settings.php if available
global $SITE;
if (isset($SITE['tz'])) 		{$ourTZ = $SITE['tz'];}
if (isset($SITE['UVscript'])) 	{$UVscript = $SITE['UVscript'];}
// end of overrides from Settings.php if available 
/*if (isset($UVscript) and ! isset($UVfcstDate[0])) { // load up the UV forecast script
  $UVfcstDate = array_fill(0,9,'');   // initialize the return arrays
  $UVfcstUVI  = array();
}
*/
  include_once($UVscript);
  
  $maxIcons = min($maxIcons,count($UVfcstUVI));  // use lesser of number of icons available

# Set timezone in PHP5/PHP4 manner
  if (!function_exists('date_default_timezone_set')) {
	  if (! ini_get('safe_mode') ) {
		 putenv("TZ=$ourTZ");  // set our timezone for 'as of' date on file
	  }  
    } else {
	date_default_timezone_set("$ourTZ");
   }

?>

<div id="main-copy">
  <!-- wxuvforecast.php (ML) V1.01 - 06-Oct-2009 -->
  <h1><?php langtrans('UV Index Forecast'); ?></h1>
  <p>&nbsp;</p>
  <div align="center">
  <?php if($UVfcstUVI[0] == "n/a") {
   ?>
  <h2><?php langtrans('The UV Index Forecast is not currently available'); ?></h2>
   <?php } else { ?>
      <table width="620" style="border: none" cellspacing="3" cellpadding="3">
         <tr class="column-dark">
          <?php for ($i=0;$i < $maxIcons;$i++) {  ?>
          <td align="center"><?php langtrans(date('D',strtotime($UVfcstYMD[$i]))); ?></td>
          <?php } // end for
		  ?>
        </tr>
        <tr class="column-light">
          <?php for ($i=0;$i < $maxIcons;$i++) {  ?>
          <td align="center"><?php echo gen_uv_icon($UVfcstUVI[$i]); ?></td>
          <?php } // end for
		  ?>
        </tr>
        <tr class="column-dark">
          <?php for ($i=0;$i < $maxIcons;$i++) {  ?>
          <td align="center"><strong><?php echo $UVfcstUVI[$i]; ?></strong></td>
          <?php } // end for
		  ?>
        </tr>
        <tr class="column-light">
          <?php for ($i=0;$i < $maxIcons;$i++) {  ?>
          <td align="center"><?php echo get_uv_word(round($UVfcstUVI[$i],0)); ?></td>
          <?php } // end for
		  ?>
        </tr>
      </table>
    <p>
    <a href="<?php echo htmlspecialchars($UV_URL); ?>"><small>
	<?php langtrans(
		'UV forecast courtesy of and Copyright &copy; KNMI/ESA (http://www.temis.nl/). Used with permission.'); ?>
     </small></a>
    </p>
    <img src="<?php print $SITE['imagesDir']; ?>uv_image.jpg" alt="UV Index Legend" style="border: none" /><br />
    <img src="<?php print $SITE['imagesDir']; ?>UVI_maplegend_H.gif" alt="UV Index Scale" style="border: none" />
    <?php } ?>
  </div> 

</div><!-- end main-copy -->
<?php 
function gen_uv_icon($inUV) {
	global $SITE;
	if($inUV == 'n/a') { return( ''); }
	$uv = preg_replace('|,|','.',$inUV);
	$ourUVrounded = round($uv,0);
	if ($ourUVrounded > 11) {$ourUVrounded = 11; }
	if ($ourUVrounded < 1 ) {$ourUVrounded = 1; }
	$ourUVicon = "uv" . sprintf("%02d",$ourUVrounded) . ".gif";
	
	return( '<img src="'.$SITE['imagesDir']. $ourUVicon . 
	  '" height="76" width="40"  alt="UV Index" title="UV Index" />');
}
//=========================================================================
//  decode UV to word+color for display

function get_uv_word ( $inUV ) {
	global $SITE;
// figure out a text value and color for UV exposure text
//  0 to 2  Low
//  3 to 5     Moderate
//  6 to 7     High
//  8 to 10 Very High
//  11+     Extreme
   $uv = preg_replace('|,|','.',$inUV);
   switch (TRUE) {
	 case ($uv == 'n/a'):
	   $uv = '';
	 break;
     case ($uv == 0):
       $uv = langtransstr('None');
     break;
     case (($uv > 0) and ($uv < 3)):
       $uv = '<span style="border: solid 1px; background-color: #A4CE6a;">&nbsp;'.langtransstr('Low').'&nbsp;</span>';
     break;
     case (($uv >= 3) and ($uv < 6)):
       $uv = '<span style="border: solid 1px;background-color: #FBEE09;">&nbsp;'.langtransstr('Medium').'&nbsp;</span>';
     break;
     case (($uv >=6 ) and ($uv < 8)):
       $uv = '<span style="border: solid 1px; background-color: #FD9125;">&nbsp;'.langtransstr('High').'&nbsp;</span>';
     break;
     case (($uv >=8 ) and ($uv < 11)):
       $uv = '<span style="border: solid 1px; color: #FFFFFF; background-color: #F63F37;">&nbsp;'.langtransstr('Very&nbsp;High').'&nbsp;</span>';
     break;
     case (($uv >= 11) ):
       $uv = '<span style="border: solid 1px; color: #FFFF00; background-color: #807780;">&nbsp;'.langtransstr('Extreme').'&nbsp;</span>';
     break;
   } // end switch
   return $uv;
} // end getUVword

//=========================================================================

?>
<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>