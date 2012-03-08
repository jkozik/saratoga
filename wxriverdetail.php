<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-USA template set)
############################################################################
#
#   Project:    Local River/Lake Heights
#   Module:     wxriverdetail.php
#   Purpose:    Show the details of the selected gauge
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

	$riverid=$_GET[id];  // Get River ID from address bar 
        if(!isset($RiverGauge[$riverid])) { // Check if gauge is in list Thanks to Ken True
            echo "<p>id='$riverid' is not defined.</p>\n";
            return;
         }
	$xmlFileData = "./River/river-$riverid.txt";
	$xmlData = simplexml_load_file($xmlFileData); // Parse the XML data
	
 // Get River Name
 	$rivername = (string)$xmlData["name"];
	if($rivername==""){
		$rivername=$RiverGauge[$riverid];
	}
 // Get Stage Levels
	$action = (string)$xmlData->sigstages->action;
	$flood = (string)$xmlData->sigstages->flood;
	$moderate = (string)$xmlData->sigstages->moderate;
	$major = (string)$xmlData->sigstages->major;	
	$faction = (string)$xmlData->sigflows>action;
	$fflood = (string)$xmlData->sigflows->flood;
	$fmoderate = (string)$xmlData->sigflows->moderate;
	$fmajor = (string)$xmlData->sigflows->major;
	$faction2 = $xmlData->sigflows>action;
	$fflood2 = $xmlData->sigflows->flood;
	$fmoderate2 = $xmlData->sigflows->moderate;
	$fmajor2 = $xmlData->sigflows->major;
	$action2 = $xmlData->sigstages->action;
	$flood2 = $xmlData->sigstages->flood;
	$moderate2 = $xmlData->sigstages->moderate;
	$major2 = $xmlData->sigstages->major;
	$recordstage = (string)$xmlData->sigstages->record;
	$recordstage2 = $xmlData->sigstages->record;
	$recordstageunits = (string)$xmlData->sigstages->record["units"];
	$recordflow = (string)$xmlData->sigflows->record;
	$recordflowunits = (string)$xmlData->sigflows->record["units"];
// Get Last Reading
	$ObsTime = (string)$xmlData->observed->datum[0]->valid;
	$ObsStage = (string)$xmlData->observed->datum[0]->primary;
	$ObsStageUnits = (string)$xmlData->observed->datum[0]->primary["units"];
	$ObsFlow = (string)$xmlData->observed->datum[0]->secondary;
	$ObsFlowUnits = (string)$xmlData->observed->datum[0]->secondary["units"];
	$data12 = strtotime($ObsTime) + (24 * 60 * 60);
	$lastobs = strtotime($ObsTime);
// Get Forecast
	$ForeStage[$i] = (string)$xmlData->forecast->datum[$i]->primary;
	$ForeStageUnits = (string)$xmlData->forecast->datum[0]->primary["units"];
	$ForeFlow[$i] = (string)$xmlData->forecast->datum[$i]->secondary;
	$ForeFlowUnits = (string)$xmlData->forecast->datum[0]->secondary["units"];
 ?>
 <div id="main-copy">
  
	<h1><?php echo ucwords(strtolower($rivername)); ?></h1>
    <?php if($dropdown){ ?>
    <form action="<?php echo $detailspage ?>" method="get">
    	  <div align="right">
    	    <select name="id">
    	      <option>Choose a River</option>
    	      
    	      <?php
				foreach ($RiverGauge as $ids => $name)
				{				
				echo "<option value='$ids'>$name</option>";	
				}
		?>
  	      </select>
    	    <input type="submit" value="GO"/>
  	    </div>
    </form>
    <?php } ?>
    <p align="right"><a href="<?php echo $riverpage ?>">Back to River Summary</a></p>
   <div align="center"> 
           <?php if($hydrographtop){ ?> 
   			<p>
				<img src="http://water.weather.gov/resources/hydrographs/<?php echo strtolower($riverid); ?>_<?php if($displayscale==1){ echo "record"; }else{ echo "hg"; } ?>.png" alt="Hydrograph" />
			</p> 
		<?php
		}else{
			echo "";
		} ?> 
   <?php if($action2!= "" or $flood2!= "" or $moderate2!= "" or $major2 != ""){ ?>
     <table width="90%" cellpadding="0" cellspacing="1" border="0">
      <tr align="center">
        <td colspan="6" class="table-top">Stage Color Key</td>
      </tr>
      <tr align="center">
      <?php if($action!=""){ ?>
        <td bgcolor="#F7FE2E"><?php echo $action ." ". $ObsStageUnits; ?> - Near Flood</td>
        <?php } if($flood!=""){ ?>
        <td bgcolor="#FE9A2E"><?php echo $flood ." ". $ObsStageUnits; ?> - Minor Flood</td>
        <?php } if($moderate!=""){ ?>
        <td bgcolor="#FF0000"><?php echo $moderate ." ". $ObsStageUnits; ?> - Moderate Flood</td>
        <?php } if($major!=""){ ?>
        <td bgcolor="#DF01D7"><?php echo $major ." ". $ObsStageUnits; ?> - Major Flood</td>
        <?php } ?>
      </tr>
    </table><br/>				
<?php   }elseif($faction2!= "" or $fflood2!= "" or $fmoderate2!= "" or $fmajor2 != ""){		?>		
     <table width="90%" cellpadding="0" cellspacing="1" border="0">
      <tr align="center">
        <td colspan="6" class="table-top">Flow Color Key</td>
      </tr>
      <tr align="center">
      <?php if($faction!=""){ ?>
		  	<td bgcolor="#F7FE2E"><?php echo $faction ." ". $ObsFlowUnits; ?> - Near Flood</td>
      <?php } if($fflood!=""){ ?>
			<td bgcolor="#FE9A2E"><?php echo $fflood ." ". $ObsFlowUnits; ?> - Minor Flood</td>
      <?php } if($fmoderate!=""){ ?>
			<td bgcolor="#FF0000"><?php echo $fmoderate ." ". $ObsFlowUnits; ?> - Moderate Flood</td>
	  <?php } if($fmajor!=""){ ?>
			<td bgcolor="#DF01D7"><?php echo $fmajor ." ". $ObsFlowUnits; ?> - Major Flood</td>
      <?php } ?>
      </tr>
    </table>
    <?php } ?>
    <p>
Latest Observation:
<?php echo date('l F jS, Y h:i A T',$lastobs); ?><br/>
River Status:
<?php 
if($data12<time()){
		echo "Old Data";
	}else{
if($action2 != "" or $flood2 != "" or $moderate2 != "" or $major2 != ""){
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
}elseif($faction2!= "" or $fflood2!= "" or $fmoderate2!= "" or $fmajor2 != ""){
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
	?></p>
    <table width="45%" cellpadding="0" cellspacing="1" border="0">
      <tr class="table-top">
        <td>&nbsp;</td>
        <td align="center">Height</td>
        <td align="center">Flow</td>
      </tr>
      <tr align="center">
        <td>Currently</td>
        <td <?php if($data12<time()){ ?>
		 bgcolor="#A4A4A4">
<?php	}else{
		
	if(number_format($ObsStage,2)<number_format($action,2) and $action!="") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format($ObsStage,2)<number_format($flood,2) and $flood!="" and $action=="") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format($ObsStage,2)>=number_format($major,2) and $major!="") { ?>
		bgcolor="#DF01D7">
<?php	}elseif(number_format($ObsStage,2)>=number_format($moderate,2) and $moderate!="") { ?>
		bgcolor="#FF0000">
<?php	}elseif(number_format($ObsStage,2)>=number_format($flood,2) and $flood!="") { ?>
		bgcolor="#FE9A2E">
<?php	}elseif(number_format($ObsStage,2)>=number_format($action,2) and $action!="") { ?>
		bgcolor="#F7FE2E">
<?php   }else{ ?>
		>
<?php	}
	} echo $ObsStage ." ". $ObsStageUnits; ?></td>
    
        <td <?php 
	if($fflood2 and $fmajor2 and $fmoderate2 ==""){
	echo ">";
	}else{
		
		if($data12<time()){ ?>
		 bgcolor="#A4A4A4">
<?php	}else{
		
	if(number_format($ObsFlow,2)<number_format($faction,2) and $faction!="") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format($ObsFlow,2)<number_format($fflood,2) and $fflood!="" and $faction=="") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format($ObsFlow,2)>=number_format($fmajor,2) and $fmajor!="") { ?>
		bgcolor="#DF01D7">
<?php	}elseif(number_format($ObsFlow,2)>=number_format($fmoderate,2) and $moderate!="") { ?>
		bgcolor="#FF0000">
<?php	}elseif(number_format($ObsFlow,2)>=number_format($fflood,2) and $fflood!="") { ?>
		bgcolor="#FE9A2E">
<?php	}elseif(number_format($ObsFlow,2)>=number_format($faction,2) and $faction!="") { ?>
		bgcolor="#F7FE2E">
<?php   }else{ ?>
		>
<?php	}
}
	} if($ObsFlow>0){		
		echo $ObsFlow." ". $ObsFlowUnits; 
		}else{
			echo "N/A";
		}
		?></td>
      </tr>
      <?php if($recordstage2 == ""){
	  echo "";
	  }else{ ?>
      <tr align="center" class="column-dark">
        <td>Record</td>
        <td <?php 
	if(number_format($recordstage,2)<number_format($action,2) and $action!="") { ?>
		bgcolor="#04B404">
<?php 	}elseif(number_format($recordstage,2)<number_format($flood,2) and $flood!="") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format($recordstage,2)>=number_format($major,2) and $major!="") { ?>
		bgcolor="#DF01D7">
<?php	}elseif(number_format($recordstage,2)>=number_format($moderate,2) and $moderate!="") { ?>
		bgcolor="#FF0000">
<?php	}elseif(number_format($recordstage,2)>=number_format($flood,2) and $flood!="") { ?>
		bgcolor="#FE9A2E">
<?php	}elseif(number_format($recordstage,2)>=number_format($action,2) and $action!="") { ?>
		bgcolor="#F7FE2E">
<?php  }else{ ?>
		>
<?php	}
		echo $recordstage ." ". $recordstageunits; ?></td>
        <td<?php
		if($recordflow == "" or $recordflow<0){
		echo ">";
	}elseif($fflood2 and $fmajor2 and $fmoderate2 =="" ){
	echo ">";
	}else{	
	if(number_format($recordflow,2)<number_format($faction,2) and $faction!="") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format($recordflow,2)<number_format($fflood,2) and $fflood!="") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format($recordflow,2)>=number_format($fmajor,2) and $fmajor!="") { ?>
		bgcolor="#DF01D7">
<?php	}elseif(number_format($recordflow,2)>=number_format($fmoderate,2) and $moderate!="") { ?>
		bgcolor="#FF0000">
<?php	}elseif(number_format($recordflow,2)>=number_format($fflood,2) and $fflood!="") { ?>
		bgcolor="#FE9A2E">
<?php	}elseif(number_format($recordflow,2)>=number_format($faction,2) and $faction!="") { ?>
		bgcolor="#F7FE2E">
<?php   }else{ ?>
		>
<?php	}
	} if($recordflow>0){		
		echo $recordflow." ". $recordflowunits; 
		}else{
			echo "N/A";
		}
		?></td>
      </tr>
      <?php } ?>
    </table>
    <?php if($ForeStageUnits[0]!=""){ ?>
            <br/>						 
    <table width="90%" cellpadding="0" cellspacing="1" border="0">
      <tr class="table-top">
        <td colspan="5">Forecast</td>
      </tr>
      <tr class="table-top">
        <td width="20">&nbsp;</td>
        <td>Date (<?php echo date('T',strtotime((string)$xmlData->forecast->datum[0]->valid)); ?>)</td>
        <td>Stage (<?php echo $ForeStageUnits; ?>)</td>
        <td>Flow (<?php echo $ForeFlowUnits; ?>)</td>
      </tr>
      <?php $i=0; 
	  while($i<7){ ?>
      <tr <?php if ($i%2==0){
echo 'class="column-light"';
} else {
echo 'class="column-dark"';
}?>>
        <td <?php 
	if($fflood2 and $fmajor2 and $fmoderate2 !=""){
	if(number_format((string)$xmlData->forecast->datum[$i]->secondary,2)<number_format($faction,2) and $faction!="") { ?>
		bgcolor="#F7FE2E">
<?php 	}elseif(number_format((string)$xmlData->forecast->datum[$i]->secondary,2)<number_format($fflood,2) and $fflood!="" and $faction == "") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format((string)$xmlData->forecast->datum[$i]->secondary,2)>=number_format($fmajor,2) and $fmajor!="") { ?>
		bgcolor="#DF01D7">
<?php	}elseif(number_format((string)$xmlData->forecast->datum[$i]->secondary,2)>=number_format($fmoderate,2) and $fmoderate!="") { ?>
		bgcolor="#FF0000">
<?php	}elseif(number_format((string)$xmlData->forecast->datum[$i]->secondary,2)>=number_format($fflood,2) and $fflood!="") { ?>
		bgcolor="#FE9A2E">
<?php	}elseif(number_format((string)$xmlData->forecast->datum[$i]->secondary,2)>=number_format($faction,2) and $faction!="") { ?>
		bgcolor="#F7FE2E">
<?php  }else{ ?>
		>
<?php	}
	}else{		
		if(number_format((string)$xmlData->forecast->datum[$i]->primary,2)<number_format($action,2) and $action!="") { ?>
		bgcolor="#04B404">
<?php 	}elseif(number_format((string)$xmlData->forecast->datum[$i]->primary,2)<number_format($flood,2) and $flood!="" and $action == "") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format((string)$xmlData->forecast->datum[$i]->primary,2)>=number_format($major,2) and $major!="") { ?>
		bgcolor="#DF01D7">
<?php	}elseif(number_format((string)$xmlData->forecast->datum[$i]->primary,2)>=number_format($moderate,2) and $moderate!="") { ?>
		bgcolor="#FF0000">
<?php	}elseif(number_format((string)$xmlData->forecast->datum[$i]->primary,2)>=number_format($flood,2) and $flood!="") { ?>
		bgcolor="#FE9A2E">
<?php	}elseif(number_format((string)$xmlData->forecast->datum[$i]->primary,2)>=number_format($action,2) and $action!="") { ?>
		bgcolor="#F7FE2E">
<?php  }else{ ?>
		>
<?php	} }
	?>&nbsp;</td>
        <td align="left"><?php echo date('l m/d/Y h:i A',strtotime((string)$xmlData->forecast->datum[$i]->valid)); ?></td>
        <td align="left"><?php echo number_format((string)$xmlData->forecast->datum[$i]->primary,2); ?></td>
        <td align="left"><?php if((string)$xmlData->forecast->datum[$i]->secondary>0){
		echo number_format((string)$xmlData->forecast->datum[$i]->secondary,2);
		}else{
			echo "N/A";
		}?></td>
      </tr>
	  <?php $i++; } ?> 
    </table>
    <?php } ?>
    <br/>
    <table width="90%" cellpadding="0" cellspacing="1" border="0">
      <tr class="table-top">
        <td colspan="5">Observation</td>
      </tr>
      <tr class="table-top">
        <td width="20">&nbsp;</td>
        <td>Date (<?php echo date('T',strtotime((string)$xmlData->observed->datum[0]->valid)); ?>)</td>
        <td>Stage (<?php echo (string)$xmlData->observed->datum[0]->primary["units"]; ?>)</td>
        <td>Flow (<?php echo (string)$xmlData->observed->datum[0]->secondary["units"]; ?>)</td>
      </tr>
      <?php $i=0; 
	  while($i<$recordstoshow){ ?>
      <tr <?php if ($i%2==0){
echo 'class="column-light"';
} else {
echo 'class="column-dark"';
}?>>
        <td <?php 	
		if(strtotime((string)$xmlData->observed->datum[$i]->valid)==""){  //Check to see how old the data is ?>
		bgcolor="#A4A4A4">	
<?php }else{
		
		if(number_format((string)$xmlData->observed->datum[$i]->primary,2)<number_format($action,2) and $action!="") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format((string)$xmlData->observed->datum[$i]->primary,2)<number_format($flood,2) and $flood!="" and $action == "") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format((string)$xmlData->observed->datum[$i]->primary,2)>=number_format($major,2) and $major!="") { ?>
		bgcolor="#DF01D7">
<?php	}elseif(number_format((string)$xmlData->observed->datum[$i]->primary,2)>=number_format($moderate,2) and $moderate!="") { ?>
		bgcolor="#FF0000">
<?php	}elseif(number_format((string)$xmlData->observed->datum[$i]->primary,2)>=number_format($flood,2) and $flood!="") { ?>
		bgcolor="#FE9A2E">
<?php	}elseif(number_format((string)$xmlData->observed->datum[$i]->primary,2)>=number_format($action,2) and $action!="") { ?>
		bgcolor="#F7FE2E">
<?php  }else{ 

		if($fflood2 and $fmajor2 and $fmoderate2 !=""){
		if(number_format((string)$xmlData->observed->datum[$i]->secondary,2)<number_format($faction,2) and $faction!="") { ?>
		bgcolor="#04B404">
<?php 	}elseif(number_format((string)$xmlData->observed->datum[$i]->secondary,2)<number_format($fflood,2) and $fflood!="" and $faction == "") { ?>
		bgcolor="#04B404">
<?php	}elseif(number_format((string)$xmlData->observed->datum[$i]->secondary,2)>=number_format($fmajor,2) and $fmajor!="") { ?>
		bgcolor="#DF01D7">
<?php	}elseif(number_format((string)$xmlData->observed->datum[$i]->secondary,2)>=number_format($fmoderate,2) and $fmoderate!="") { ?>
		bgcolor="#FF0000">
<?php	}elseif(number_format((string)$xmlData->observed->datum[$i]->secondary,2)>=number_format($fflood,2) and $fflood!="") { ?>
		bgcolor="#FE9A2E">
<?php	}elseif(number_format((string)$xmlData->observed->datum[$i]->secondary,2)>=number_format($faction,2) and $faction!="") { ?>
		bgcolor="#F7FE2E">
<?php  }else{ ?>
		>
<?php	}
}
}
	}?></td>
        <td align="left"><?php echo date('l m/d/Y h:i A',strtotime((string)$xmlData->observed->datum[$i]->valid)); ?></td>
        <td align="left"><?php echo number_format((string)$xmlData->observed->datum[$i]->primary,2); ?></td>
        <td align="left"><?php if((string)$xmlData->observed->datum[$i]->secondary>0){
		echo number_format((string)$xmlData->observed->datum[$i]->secondary,2);
		}else{
			echo "N/A";
		}?></td>
      </tr>
	  <?php $i++; } ?> 
    </table>
    <?php if($hydrographtop == false){ ?> 
   			<p>
				<img src="http://water.weather.gov/resources/hydrographs/<?php echo strtolower($riverid); ?>_<?php if($displayscale==1){ echo "record"; }else{ echo "hg"; } ?>.png" alt="Hydrograph" />
			</p> 
		<?php
		} ?> 
  </div>
  <p style="font-size: 9px;" align="right">Data Courtesy of the <a href="http://water.weather.gov/ahps/">Advanced Hydrologic Prediction Service</a>
  <br/>
Script Courtesy of Dennis at <a href="http://eastmasonvilleweather.com">East Masonville Weather</a>
  </p>
</div><!-- end main-copy -->
<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>