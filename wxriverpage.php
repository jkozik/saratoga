<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-USA template set)
############################################################################
#
#   Project:    Local River/Lake Heights
#   Module:     wxriverpage.php
#   Purpose:    Show the overview of all gauges
#   Authors:    Dennis Clapperton <webmaster@eastmasonvilleweather.com>
#               East Masonville Weather
#	Version:	2.15
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
$TITLE= $SITE['organ'] . " - Local River/Lake Heights";
$showGizmo = false;  // set to false to exclude the gizmo
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
include("./River/river-config.php");
############################################################################
?>

<div id="main-copy">
  
	<h1>River Heights</h1>
		<div align="center">
        <?php if($rivermaptop){ // See where you want the river map ?> 
        	<p><img src="<?php echo $currentstage ?>" alt="Current River Heights" usemap="#rivermap"/>
				</p> 
       <?php include("./River/river-map.txt"); // added the image map previously downloaded.
		}else{
			echo "";
		} ?>
         
			<br />
    <table width="85%" cellpadding="0" cellspacing="1" border="0">
      <tr align="center">
        <th colspan="6" scope="col" class="table-top">Stage Color Key</th>
      </tr>
      <tr align="center">
        <td bgcolor="#04B404">No Flooding</td>
        <td bgcolor="#F7FE2E">Near Flood</td>
        <td bgcolor="#FE9A2E">Minor Flooding</td>
        <td bgcolor="#FF0000">Mod Flooding</td>
        <td bgcolor="#DF01D7">Major Flooding</td>
        <td bgcolor="#A4A4A4">Obs &gt; 24hrs</td>
      </tr>
    </table>
    <br />
  <table width="99%" cellpadding="0" cellspacing="1" border="0">
  <tr class="table-top">
    <th colspan="2" scope="col" align="center">Location</th>
    <th scope="col" align="center">Height</th>
    <th scope="col" align="center">Status</th>
  </tr>
<?php
foreach($RiverGauge as $riverid => $rivername){
	$xmlFileData = "./River/river-$riverid.txt";

	$xmlData["$riverid"] = simplexml_load_file($xmlFileData);
 // Get Stage Levels
	$action = (string)$xmlData["$riverid"]->sigstages->action;
	$flood = (string)$xmlData["$riverid"]->sigstages->flood;
	$moderate = (string)$xmlData["$riverid"]->sigstages->moderate;
	$major = (string)$xmlData["$riverid"]->sigstages->major;
	$record = (string)$xmlData["$riverid"]->sigstages->record;
// Get Flow Levels
	$faction = (string)$xmlData["$riverid"]->sigflows->action;
	$fflood = (string)$xmlData["$riverid"]->sigflows->flood;
	$fmoderate = (string)$xmlData["$riverid"]->sigflows->moderate;
	$fmajor = (string)$xmlData["$riverid"]->sigflows->major;
	$frecord = (string)$xmlData["$riverid"]->sigflows->record;
// Get Last Reading
	$ObsTime = (string)$xmlData["$riverid"]->observed->datum[0]->valid;
	$ObsStage = (string)$xmlData["$riverid"]->observed->datum[0]->primary;
	$ObsFlow = (string)$xmlData["$riverid"]->observed->datum[0]->secondary;
	$data12 = strtotime($ObsTime) + (24 * 60 * 60);
 ?>
  <tr <?php if ($i%2==0){
echo 'class="column-dark"';
} else {
echo 'class="column-light"';
}?>>
	<?php 	if($data12<time()){
				echo "<td width='20' bgcolor='#A4A4A4'></td>";
			}else{
				if($action != "" or $flood != "" or $moderate != "" or $major != ""){
					if(number_format($ObsStage,2)<number_format($action,2) and $action!="") {
						echo "<td width='20' bgcolor='#04B404'></td>";
					}elseif(number_format($ObsStage,2)<number_format($flood,2) and $flood!="" and $action == "") {
						echo "<td width='20' bgcolor='#04B404'></td>";
					}elseif(number_format($ObsStage,2)>=number_format($record,2) and $record!="") {
						echo "<td width='20' bgcolor='#DF01D7'></td>";
					}elseif(number_format($ObsStage,2)>=number_format($major,2) and $major!="") {
						echo "<td width='20' bgcolor='#DF01D7'></td>";
					}elseif(number_format($ObsStage,2)>=number_format($moderate,2) and $moderate!="") {
						echo "<td width='20' bgcolor='#FF0000'></td>";
					}elseif(number_format($ObsStage,2)>=number_format($flood,2) and $flood!="") {
						echo "<td width='20' bgcolor='#FE9A2E'></td>";
					}elseif(number_format($ObsStage,2)>=number_format($action,2) and $action!="") {
						echo "<td width='20' bgcolor='#F7FE2E'></td>";
	 				}else{ 
						echo "<td width='20'></td>";
					}
				}elseif($faction!= "" or $fflood!= "" or $fmoderate!= "" or $fmajor != ""){
					if(number_format($ObsFlow,2)<number_format($faction,2) and $faction!="") {
						echo "<td width='20' bgcolor='#04B404'></td>";
					}elseif(number_format($ObsFlow,2)<number_format($fflood,2) and $fflood!="" and $faction == "") {
						echo "<td width='20' bgcolor='#04B404'></td>";
					}elseif(number_format($ObsFlow,2)>=number_format($frecord,2) and $frecord!="") {
						echo "<td width='20' bgcolor='#DF01D7'></td>";
					}elseif(number_format($ObsFlow,2)>=number_format($fmajor,2) and $fmajor!="") {
						echo "<td width='20' bgcolor='#DF01D7'></td>";
					}elseif(number_format($ObsFlow,2)>=number_format($fmoderate,2) and $moderate!="") {
						echo "<td width='20' bgcolor='#FF0000'></td>";
					}elseif(number_format($ObsFlow,2)>=number_format($fflood,2) and $fflood!="") {
						echo "<td width='20' bgcolor='#FE9A2E'></td>";
					}elseif(number_format($ObsFlow,2)>=number_format($faction,2) and $faction!="") {
						echo "<td width='20' bgcolor='#F7FE2E'></td>";
	  				}else{
						echo "<td width='20'></td>";
					}
				}else{
					echo "<td width='20'></td>";
				}
			 } ?>
    <td align="left"><a href="<?php echo $detailspage ?>?id=<?php echo $riverid; ?>"><?php echo $rivername; ?> (<?php echo $riverid; ?>)</a></td>
    <td align="center"><?php echo number_format($ObsStage,2); ?> ft</td>
    <td align="center"><?php 
	if($data12<time()){
		echo "Old Data";
	}else{
		if($action != "" or $flood != "" or $moderate != "" or $major != ""){
			if(number_format($ObsStage,2)<number_format($action,2) and $action!="") {
				echo "Normal";
			}elseif(number_format($ObsStage,2)<number_format($flood,2) and $flood!="" and $action =="") {
				echo "Normal";
			}elseif(number_format($ObsStage,2)>=number_format($recordstage,2) and $recordstage!="") {
				echo "Record Flooding";
			}elseif(number_format($ObsStage,2)>=number_format($major,2) and $major!="") {
				echo "Major Flooding";
			}elseif(number_format($ObsStage,2)>=number_format($moderate,2) and $moderate!="") {
				echo "Moderate Flooding";
			}elseif(number_format($ObsStage,2)>=number_format($flood,2) and $flood!="") {
				echo "Minor Flooding";
			}elseif(number_format($ObsStage,2)>=number_format($action,2) and $action!="") {
				echo "Near Flood Stage";
			}else{
				echo "Not Defined";
			}
		}elseif($faction!= "" or $fflood!= "" or $fmoderate!= "" or $fmajor != ""){
			if(number_format($ObsFlow,2)<number_format($faction,2) and $faction!="") {
				echo "Normal";
			}elseif(number_format($ObsFlow,2)<number_format($fflood,2) and $fflood!="" and $faction =="") {
				echo "Normal";
			}elseif(number_format($ObsFlow,2)>=number_format($frecordflow,2) and $frecordflow!="") {
				echo "Record Flooding";
			}elseif(number_format($ObsFlow,2)>=number_format($fmajor,2) and $fmajor!="") {
				echo "Major Flooding";
			}elseif(number_format($ObsFlow,2)>=number_format($fmoderate,2) and $fmoderate!="") {
				echo "Moderate Flooding";
			}elseif(number_format($ObsFlow,2)>=number_format($fflood,2) and $fflood!="") {
				echo "Minor Flooding";
			}elseif(number_format($ObsFlow,2)>=number_format($faction,2) and $faction!="") {
				echo "Near Flood Stage";
			}else{
				echo "Not Defined";	
			}	
		}else{
			echo "Not Defined";	
		}
	}
	?>
	</td>
  </tr>
  <?php $i++;
} ?>
</table>
        <?php if($rivermaptop == false){ ?> 
        	<p><img src="<?php echo $currentstage ?>" alt="Current River Heights" usemap="#rivermap"/>
				</p> 
       <?php include("./River/river-map.txt"); // added the image map previously downloaded.
		}else{
			echo "";
		} ?>
</div>
  <p style="font-size: 9px;" align="right">Data Courtesy of the <a href="http://water.weather.gov/ahps/">Advanced Hydrologic Prediction Service</a>
  <br/>
Script Courtesy of Dennis at <a href="http://eastmasonvilleweather.com">East Masonville Weather</a>
  <br/>
Clickable Map Courtesy of Jim at <a href="http://jcweather.us/index.php">Juneau County Weather</a>
  </p>
</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>