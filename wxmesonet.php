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
$TITLE = langtransstr($SITE['organ']) . " - " .langtransstr('Mesonet Stations');
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
$uomPerHour = '/hr';
if (isset($SITE['uomPerHour'])) {$uomPerHour = $SITE['uomPerHour'];}
$uomPerHour = langtransstr($uomPerHour);
?>

<div id="main-copy">
  
	<h1><?php langtrans('Mesonet Stations'); ?></h1>
    <p>&nbsp;</p>

<div class="ajaxDashboard" style="width: 630px;font-size: 10px">
        <table border="1" cellspacing="0">
          <tr>
                  <td align="center" valign="top" class="datahead">
                    <?php echo langtransstr('Station ID'); ?>
                  </td>
                  <td align="center" valign="top" class="datahead">
                    <?php echo langtransstr('City'); ?><br/>
                    (<?php echo langtransstr('Click for Map'); ?>)
                  </td>
                  <td align="center" valign="top" class="datahead">
                     <?php echo langtransstr('Neighborhood'); ?><br/>
                    (<?php echo langtransstr('Click for History'); ?>)
                  </td>
                  <td align="center" valign="top" class="datahead">
                    <?php echo langtransstr('Updated'); ?>
                  </td>
                  <td align="center" valign="top" class="datahead">
                    <?php echo langtransstr('Temp'); ?><br/>
                    (<?php print $WX['uni007']; ?>)
                  </td>
                  <td align="center" valign="top" class="datahead">
				    <?php echo langtransstr('Dew Point'); ?><br/>
                    (<?php print $WX['uni007']; ?>)
                   </td>
                  <td align="center" valign="top" class="datahead">
				    <?php echo langtransstr('Humidity'); ?><br/>
                    (%)
                  </td>
                  <td align="center" valign="top" class="datahead">
				    <?php echo langtransstr('Wind'); ?><br/>
                    (<?php print $WX['uni002']; ?>)
                  </td>
                  <td align="center" valign="top" class="datahead">
				    <?php echo langtransstr('Wind from'); ?>
                  </td>
                  <td align="center" valign="top" class="datahead">
				    <?php echo langtransstr('Gust'); ?><br/>
                    (<?php print $WX['uni002']; ?>)&nbsp;
                  </td>
                  <td align="center" valign="top" class="datahead">
				    <?php echo langtransstr('Barometer'); ?>
                    (<?php print $WX['uni023']; ?>)
                  </td>
                  <td align="center" valign="top" class="datahead">
				    <?php echo langtransstr('Rain Rate'); ?><br/>
                   (<?php print $WX['uni122'].$uomPerHour; ?>)
                  </td>
          </tr>
          <?php
		    $tgt = ' target="_blank"';
			
		    for ($i=1;$i<=40;$i++) {
			  if(isset($WX['meso_stat'.$i]) and $WX['meso_stat'.$i] <> '') {
		   // loop over the defined meso stations 
		   ?>
          <tr>
                  <td class="data1" style="text-align: center;">
                    <?php print $WX['meso_stat'.$i]; ?>
                  </td>
                  <td class="data1" style="text-align: center;">
                    <a href="http://www.wunderground.com/wundermap/?lat=<?php print $WX['meso_lat'.$i]; ?>&amp;lon=<?php print $WX['meso_lon'.$i]; ?>&amp;zoom=12"<?php print $tgt; ?>><?php print $WX['meso_loc'.$i]; ?></a>
                  </td>
                  <td class="data1" style="text-align: center;">
                    <a href="http://<?php print $WX['meso_historyURL'.$i]; ?>"<?php print $tgt; ?>><?php print $WX['meso_neigh'.$i]; ?></a>
                  </td>
                  <td align="center" valign="top" class="data1">
                    <?php print $WX['meso_time'.$i]; ?>
                  </td>
                  <td class="data1" style="text-align: center;">
                    <?php print $WX['meso_temp'.$i]; ?>
                  </td>
                  <td class="data1" style="text-align: center;">
                    <?php print $WX['meso_dew'.$i]; ?>
                  </td>
                  <td class="data1" style="text-align: center;">
				    <?php print $WX['meso_rh'.$i]; ?>
                  </td>
                  <td class="data1" style="text-align: center;">
                    <?php print $WX['meso_wspeed'.$i]; ?>
                  </td>
                  <td class="data1" style="text-align: center;">
                    <?php print langtransstr(VWS_fixup_wind($WX['meso_dir'.$i])); ?>
                  </td>
                  <td class="data1" style="text-align: center;">
                    <?php print $WX['meso_gust'.$i]; ?>
                  </td>
                  <td class="data1" style="text-align: center;">
                    <?php print $WX['meso_barom'.$i]; ?>
                  </td>
                  <td class="data1" style="text-align: center;">
                    <?php print $WX['meso_rrate'.$i]; ?>
                  </td>
          </tr>
          <?php   } // end isset
			} // end loop
		  ?>
</table>    
</div>    
</div><!-- end main-copy -->

<?php
function VWS_fixup_wind( $dir ) {
	$WDir = array(
	  'North' => 'N',
	  'East'  => 'E',
	  'South' => 'S',
	  'West'  => 'W'
	);
	
	if (isset($WDir[$dir])) {
	  return($WDir[$dir]);
	} else {
	  return ($dir);
	}
	 
}

############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>