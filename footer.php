<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-USA-ML template set)
############################################################################
#
#	Project:	Sample Included Website Design
#	Module:		footer.php
#	Purpose:	Provides the bottom section of the website
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
//Version 1.01 - 05-Feb-2012 - fixup for HTML5 validation
require_once("Settings.php");
require_once("common.php");
############################################################################
?>
    <!-- ##### Footer ##### -->

    <div id="footer">
      <div class="doNotPrint">
        <a href="#header"><?php langtrans('Top'); ?></a> |

        <a href="<?php print $SITE['email']; ?>" title="E-mail us"><?php langtrans('Contact Us'); ?></a>
        <script type="text/javascript">
        <!--
        if (navigator.appName == 'Microsoft Internet Explorer' && 
        parseInt(navigator.appVersion) >= 4)
        {
        document.write('| <a href=\"#\" onclick=\"javascript:window.external.AddFavorite        (location.href,document.title)\">');
        document.write('<?php langtrans('Bookmark Page'); ?></a>');
        }else
        {var msg = '| <a href="" title="<?php langtrans('Bookmark Page'); ?>" onClick="alert(' + "'Hit CTRL-D to bookmark this page'"+ ');"><?php langtrans('Bookmark Page'); ?></a>';
        if(navigator.appName == "Netscape") msg += " (CTRL-D)";
document.write(msg);
        }
        // -->
        </script>
      </div><!-- end doNotPrint -->

      <div>

        <?php print $SITE['copyr'] ?><span class="doNotPrint"> |  
          <a href="<?php echo $SITE['WXsoftwareURL']; ?>" title="Powered by <?php echo $SITE['WXsoftwareLongName']; ?>"><?php echo $SITE['WXsoftwareLongName'];?>
		  <?php if(isset($wdversion)) {echo " (".$wdversion.")";} ?> </a> |
		  <a href="http://validator.w3.org/check?uri=referer"><?php langtrans('Valid'); ?> 
          <?php  print (isset($useHTML5) and $useHTML5)?'HTML5':'XHTML 1.0'; ?></a> |
          <a href="http://jigsaw.w3.org/css-validator/check/referer"><?php langtrans('Valid'); ?> CSS</a> 
          </span><br class="doNotPrint" />
      <br/><?php langtrans('Never base important decisions on this or any weather information obtained from the Internet'); ?>.<br class="doNotPrint" />
      </div>
    </div><!-- end id="footer" -->
  </div><!-- end id="page" wrapper -->
  </body>
</html>
<?php 
# Leave this code here .. it will help you see what language translations are missing by running any page on your
# website with a ?show=missing argument
#
  global $missingTrans,$SITE;
  if(isset($_REQUEST['show']) and strtolower($_REQUEST['show']) == 'missing') {
	echo "<!-- missing langlookup entries for lang=".$SITE['lang']." \n";
	foreach ($missingTrans as $key => $val) {
		echo "langlookup|$key|$key|\n";
	}
	echo "\n ".count($missingTrans)." entries.  End of missing langlookup entries -->\n";
  }
?>