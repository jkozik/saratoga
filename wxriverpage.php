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
#   Version:	3.00F
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
<style type="text/css">
.greencircle{width:7px;height:7px;border-radius:10px;background:#04B404}
.yellowcircle{width:7px;height:7px;border-radius:10px;background:#F7FE2E}
.orangecircle{width:7px;height:7px;border-radius:10px;background:#FE9A2E}
.redcircle{width:7px;height:7px;border-radius:10px;background:#FF0000}
.purplecircle{width:7px;height:7px;border-radius:10px;background:#DF01D7}
</style>
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
<?php
echo "<table width='99%' cellpadding='0' cellspacing='1' border='0'>
  		<tr class='table-top'>
    		<th colspan='2' scope='col' align='center'>Location</th>
    		<th align='center' scope='col'>Height</th>";
if($trend==1){
    echo 	"<th align='center' scope='col'>Trend</th>";
} 

if($forecasttrend==1||$forecastcolor==1){ 
	$colspan =2;
	if($forecasttrend!=1||$forecastcolor!=1){ 
		$colspan =1;
	}
	echo 	"<th scope='col' align='center' colspan= $colspan >Forecast</th>";
}
    echo 	"<th align='center' scope='col'>Status</th>
  		</tr>";	

foreach($RiverGauge as $riverid => $rivername){
	$xmlFileData = "./River/river-$riverid.txt";

	$xmlData["$riverid"] = simplexml_load_file($xmlFileData);
	
	if($xmlData["$riverid"] ===  FALSE){
   		continue;
	}
	
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
	
//Lets determine if flow or stage is primary and which is secondary

	$Name = (string)$xmlData["$riverid"]->observed->datum[0]->primary->attributes()->name;
	if($Name=="Stage"){
		$stage = "primary";
		$flow = "secondary";
	}else{
		$stage = "secondary";
		$flow = "primary";	
	}

// Get Last Reading
	$ObsTime = (string)$xmlData["$riverid"]->observed->datum[0]->valid;
	$ObsStage = (string)$xmlData["$riverid"]->observed->datum[0]->$stage;
	$ObsFlow = (string)$xmlData["$riverid"]->observed->datum[0]->$flow;
	$data12 = strtotime($ObsTime) + (24 * 60 * 60);
	
// Get Previous Reading	
if($trend==1){
	$ObsStageold = (string)$xmlData["$riverid"]->observed->datum[1]->$stage;
	$ObsFlowold = (string)$xmlData["$riverid"]->observed->datum[1]->$flow;
}

if($forecasttrend==1||$forecastcolor==1){
	// Get Next forecasted point
	if($forecasttrend==1){
		$ForeTime = (string)$xmlData["$riverid"]->forecast->datum[0]->valid;
		if($ForeTime!=""){
			$j=0;
			while(strtotime($ForeTime)<time()){
				$j++;
				$ForeTime = (string)$xmlData["$riverid"]->forecast->datum[$j]->valid;
			}
			$ForeStage = (string)$xmlData["$riverid"]->forecast->datum[$j]->$stage;
			$ForeFlow = (string)$xmlData["$riverid"]->forecast->datum[$j]->$flow;
		}
	}
	// Get Highest Forecasted Point
	if($forecastcolor==1){
		$y=0;
		$coloricon=array();
		$coloriconflow=array();
		while($y<7){
			$coloricon[]=(string)$xmlData["$riverid"]->forecast->datum[$y]->$stage;
			$coloriconflow[]=(string)$xmlData["$riverid"]->forecast->datum[$y]->$flow;
			$y++;
		}
		$maxforecast = max($coloricon);
		$maxforecastflow = max($coloriconflow);
	}
}

		
// Give our rows some color
if ($i%2==0){
	$color= 'class="column-dark"';
} else {
	$color= 'class="column-light"';
}

	echo	"<tr $color >";
				color($ObsStage, $data12, $action, $flood, $moderate, $major, $ObsFlow, $faction, $fflood, $fmoderate, $fmajor);
    echo		"<td align='left'><a href='$detailspage?id=$riverid'>$rivername ($riverid)</a></td>
    			<td align='center'>". number_format($ObsStage,2) ."ft </td>";
			
if($trend==1){
    echo 		"<td align='center'>";
	if($data12<time()){
		echo 		"&nbsp;";
	}else{
		gen_difference( $ObsStageold, $ObsStage ); 
	}
	echo 		"</td>";
} 

if($forecasttrend==1||$forecastcolor==1){ 
	if($forecasttrend==1){
		echo 	"<td width='5%' align='center'>";
		if($data12<time()){
			echo "&nbsp;";
		}else{
			if($ForeTime!=""){
				gen_difference( $ObsStage, $ForeStage ); 
			}else{
				echo "&nbsp;"; 
			}
		}
		echo	 "</td>"; 
	}
	if($forecastcolor==1){
		echo 	"<td width='5%' align='center'>";
		 maxforecast($maxforecast, $maxforecastflow, $data12, $action, $flood, $moderate, $major, $faction, $fflood, $fmoderate, $fmajor);
		 echo 	"</td>";
	} 

}

    echo 		"<td align='center'>";
			status($ObsStage, $data12, $action, $flood, $moderate, $major, $recordstage, $faction, $fflood, $fmoderate, $fmajor, $frecordflow); 
		echo	"</td>
  			</tr>";
$i++;
}
?>
</table>			  
</div>
  <p style="font-size: 9px;" align="right">Data Courtesy of the <a href="http://water.weather.gov/ahps/">Advanced Hydrologic Prediction Service</a>
  <br/>
Script Courtesy of Dennis at <a href="http://eastmasonvilleweather.com">East Masonville Weather</a>
  </p>
</div><!-- end main-copy -->

<?php
############################################################################
#Functions
############################################################################

function gen_difference( $current, $compared ) {
	$diff=$current-$compared;
	if($diff>0){
		$image = "<img src=\"/ajax-images/falling.gif\" alt=\"\" title=\"\" width=\"7\" height=\"8\" style=\"border: 0; margin: 1px 3px;\" />";
	}elseif($diff<0){
		$image = "<img src=\"/ajax-images/rising.gif\" alt=\"\" title=\"\" width=\"7\" height=\"8\" style=\"border: 0; margin: 1px 3px;\" />";
	}elseif($diff==0){
		$image = "<img src=\"/ajax-images/steady.gif\" alt=\"\" title=\"\" width=\"7\" height=\"8\" style=\"border: 0; margin: 1px 3px;\" />";
	}
	echo $image;
}

function maxforecast($maxforecast, $maxforecastflow, $data12, $action, $flood, $moderate, $major, $faction, $fflood, $fmoderate, $fmajor){
	if($maxforecast!=''){
		if($data12<time()){
			echo "&nbsp;";
		}else{
			if($action != "" or $flood != "" or $moderate != "" or $major != ""){
				if(number_format((double)$maxforecast,2)<number_format((double)$action,2) and $action!="") {
					echo "<div title='Highest forecasted stage: Normal' class='greencircle'></div>";
				}elseif(number_format((double)$maxforecast,2)<number_format((double)$flood,2) and $flood!="" and $action =="") {
					echo "<div title='Highest forecasted stage: Normal' class='greencircle'></div>";
				}elseif(number_format((double)$maxforecast,2)>=number_format((double)$recordstage,2) and $recordstage!="") {
					echo "<div title='Highest forecasted stage: Record Flooding' class='purplecircle'></div>";
				}elseif(number_format((double)$maxforecast,2)>=number_format((double)$major,2) and $major!="") {
					echo "<div title='Highest forecasted stage: Major Flooding' class='purplecircle'></div>";
				}elseif(number_format((double)$maxforecast,2)>=number_format((double)$moderate,2) and $moderate!="") {
					echo "<div title='Highest forecasted stage: Moderate Flooding' class='redcircle'></div>";
				}elseif(number_format((double)$maxforecast,2)>=number_format((double)$flood,2) and $flood!="") {
					echo "<div title='Highest forecasted stage: Minor Flooding' class='orangecircle'></div>";
				}elseif(number_format((double)$maxforecast,2)>=number_format((double)$action,2) and $action!="") {
					echo "<div title='Highest forecasted stage: Near Flood Stage' class='yellowcircle'></div>";
				}else{
					echo "&nbsp;";
				}
			}elseif($faction!= "" or $fflood!= "" or $fmoderate!= "" or $fmajor != ""){
				if(number_format((double)$ObsFlow,2)<number_format((double)$faction,2) and $faction!="") {
					echo "<div title='Highest forecasted flow: Normal' class='greencircle'></div>";
				}elseif(number_format((double)$maxforecastflow,2)<number_format((double)$fflood,2) and $fflood!="" and $faction =="") {
					echo "<div title='Highest forecasted flow: Normal' class='greencircle'></div>";
				}elseif(number_format((double)$maxforecastflow,2)>=number_format((double)$frecordflow,2) and $frecordflow!="") {
					echo "<div title='Highest forecasted flow: Record Flooding' class='purplecircle'></div>";
				}elseif(number_format((double)$maxforecastflow,2)>=number_format((double)$fmajor,2) and $fmajor!="") {
					echo "<div title='Highest forecasted flow: Major Flooding' class='purplecircle'></div>";
				}elseif(number_format((double)$maxforecastflow,2)>=number_format((double)$fmoderate,2) and $fmoderate!="") {
					echo "<div title='Highest forecasted flow: Moderate Flooding' class='redcircle'></div>";
				}elseif(number_format((double)$maxforecastflow,2)>=number_format((double)$fflood,2) and $fflood!="") {
					echo "<div title='Highest forecasted flow: Minor Flooding' class='orangecircle'></div>";
				}elseif(number_format((double)$maxforecastflow,2)>=number_format((double)$faction,2) and $faction!="") {
					echo "<div title='Highest forecasted flow: Near Flood Stage' class='yellowcircle'></div>";
				}else{
					echo "&nbsp;";	
				}	
			}else{
				echo "&nbsp;";	
			}
		}
	}else{
		echo "&nbsp;";
	}
}

function status($ObsStage, $data12, $action, $flood, $moderate, $major, $recordstage, $faction, $fflood, $fmoderate, $fmajor, $frecordflow){
	if($data12<time()){
		echo "Old Data";
	}else{
		if($action != "" or $flood != "" or $moderate != "" or $major != ""){
			if(number_format((double)$ObsStage,2)<number_format((double)$action,2) and $action!="") {
				echo "Normal";
			}elseif(number_format((double)$ObsStage,2)<number_format((double)$flood,2) and $flood!="" and $action =="") {
				echo "Normal";
			}elseif(number_format((double)$ObsStage,2)>=number_format((double)$recordstage,2) and $recordstage!="") {
				echo "Record Flooding";
			}elseif(number_format((double)$ObsStage,2)>=number_format((double)$major,2) and $major!="") {
				echo "Major Flooding";
			}elseif(number_format((double)$ObsStage,2)>=number_format((double)$moderate,2) and $moderate!="") {
				echo "Moderate Flooding";
			}elseif(number_format((double)$ObsStage,2)>=number_format((double)$flood,2) and $flood!="") {
				echo "Minor Flooding";
			}elseif(number_format((double)$ObsStage,2)>=number_format((double)$action,2) and $action!="") {
				echo "Near Flood Stage";
			}else{
				echo "Not Defined";
			}
		}elseif($faction!= "" or $fflood!= "" or $fmoderate!= "" or $fmajor != ""){
			if(number_format((double)$ObsFlow,2)<number_format((double)$faction,2) and $faction!="") {
				echo "Normal";
			}elseif(number_format((double)$ObsFlow,2)<number_format((double)$fflood,2) and $fflood!="" and $faction =="") {
				echo "Normal";
			}elseif(number_format((double)$ObsFlow,2)>=number_format((double)$frecordflow,2) and $frecordflow!="") {
				echo "Record Flooding";
			}elseif(number_format((double)$ObsFlow,2)>=number_format((double)$fmajor,2) and $fmajor!="") {
				echo "Major Flooding";
			}elseif(number_format((double)$ObsFlow,2)>=number_format((double)$fmoderate,2) and $fmoderate!="") {
				echo "Moderate Flooding";
			}elseif(number_format((double)$ObsFlow,2)>=number_format((double)$fflood,2) and $fflood!="") {
				echo "Minor Flooding";
			}elseif(number_format((double)$ObsFlow,2)>=number_format((double)$faction,2) and $faction!="") {
				echo "Near Flood Stage";
			}else{
				echo "Not Defined";	
			}	
		}else{
			echo "Not Defined";	
		}
	}
}

function color($ObsStage, $data12, $action, $flood, $moderate, $major, $ObsFlow, $faction, $fflood, $fmoderate, $fmajor){
	if($data12<time()){
		echo "<td width='20' bgcolor='#A4A4A4'></td>";
	}else{
		if($action != "" or $flood != "" or $moderate != "" or $major != ""){
			if(number_format((double)$ObsStage,2)<number_format((double)$action,2) and $action!="") {
				echo "<td width='20' bgcolor='#04B404'></td>";
			}elseif(number_format((double)$ObsStage,2)<number_format((double)$flood,2) and $flood!="" and $action == "") {
				echo "<td width='20' bgcolor='#04B404'></td>";
			}elseif(number_format((double)$ObsStage,2)>=number_format((double)$major,2) and $major!="") {
				echo "<td width='20' bgcolor='#DF01D7'></td>";
			}elseif(number_format((double)$ObsStage,2)>=number_format((double)$moderate,2) and $moderate!="") {
				echo "<td width='20' bgcolor='#FF0000'></td>";
			}elseif(number_format((double)$ObsStage,2)>=number_format((double)$flood,2) and $flood!="") {
				echo "<td width='20' bgcolor='#FE9A2E'></td>";
			}elseif(number_format((double)$ObsStage,2)>=number_format((double)$action,2) and $action!="") {
				echo "<td width='20' bgcolor='#F7FE2E'></td>";
			}else{ 
				echo "<td width='20'></td>";
			}
		}elseif($faction!= "" or $fflood!= "" or $fmoderate!= "" or $fmajor != ""){
			if(number_format((double)$ObsFlow,2)<number_format((double)$faction,2) and $faction!="") {
				echo "<td width='20' bgcolor='#04B404'></td>";
			}elseif(number_format((double)$ObsFlow,2)<number_format((double)$fflood,2) and $fflood!="" and $faction == "") {
				echo "<td width='20' bgcolor='#04B404'></td>";
			}elseif(number_format((double)$ObsFlow,2)>=number_format((double)$fmajor,2) and $fmajor!="") {
				echo "<td width='20' bgcolor='#DF01D7'></td>";
			}elseif(number_format((double)$ObsFlow,2)>=number_format((double)$fmoderate,2) and $moderate!="") {
				echo "<td width='20' bgcolor='#FF0000'></td>";
			}elseif(number_format((double)$ObsFlow,2)>=number_format((double)$fflood,2) and $fflood!="") {
				echo "<td width='20' bgcolor='#FE9A2E'></td>";
			}elseif(number_format((double)$ObsFlow,2)>=number_format((double)$faction,2) and $faction!="") {
				echo "<td width='20' bgcolor='#F7FE2E'></td>";
			}else{
				echo "<td width='20'></td>";
			}
		}else{
			echo "<td width='20'></td>";
		}
	}
} 

	
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>