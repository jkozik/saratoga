<?php
############################################################################
#
#   Module:     wxnoaawind.php
#   Purpose:    Display a graph of wind data from dailynoaareport files.
#   Author:     Murry Conarroe <murry@murry.com>
#              
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
#   This document uses Tab 4 Settings
############################################################################
#   History
# 2010-08-21 1.0 Inital Release
# 2010-08-21 1.01 Changed Position of Legend
# 2010-12-14 1.11 Added function to display error message if jpgraph files not found
############################################################################ 
include_once("Settings.php");
include_once("common.php");
#################   User Settings   ###############################
$loc = "./noaa/";            # Set to location of NOAA report files
$jploc = "./jpgraph/" ; # Set to location of jpgraph files
$first_year_of_data = "2010";  # First year of NOAA data that is available
$heading_name = 'Naperville Wind Speeds since March '.$first_year_of_data;  # Text to be displayed above graph
$wind_unit = 'MPH' ;  # Set to MPH, KMH, etc 
$graph_interval = 2;  # 1 = daily, 2 = monthly
$show_values = true; # Set to true to display the value on the chart. - Only used with monthly graph interval
$show_record_highs = true;
$show_average_highs = true;
$show_average_wind = true;
$record_high_color = "red";
$average_high_color = "green";
$average_color = "blue";
$margin_color = "lightblue";
$legend_background_color = "white";
$record_high_legend_text = "Record Highs";
$average_high_legend_text = "Average Highs";
$average_legend_text = "Daily Average";
$month_names = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
$width = "660";   # Width of graph in pixels
$height = "400";   # Height of graph in pixels
$do_cache = true; # Set to true to cache data for up to 24 hours.
$cache_location = "./cache/"; # Set to directory for cache files. Directory must be chmod to 666 or higher; 
$SITE['viewscr'] = 'sce';  // Password for View Source Function 
#################   End of User Settings   ###############################

error_reporting(E_ALL & ~E_NOTICE);
check_sourceview();
require ("wxnoaacsvdata.php"); 

$max_wind_scale = '';
$min_wind_scale = 0;

    
if (!file_exists($jploc."jpgraph.php")) {
  $string = "Unable to find JPGraph files";
  create_image1($string,$jploc);
  exit;
}
   
// We now have the data, now do the graph
    require_once ($jploc."jpgraph.php");
    require_once ($jploc."jpgraph_line.php");
    require_once ($jploc."jpgraph_bar.php");
    include ($jploc."jpgraph_utils.inc.php");
   
if ($graph_interval == 1) {   
    $tickPositions = array(0,31,60,91,121,152,182,213,244,274,305,335);
    $record_data = $yeardata[7];
    $average_data = $yeardata[8]; 
    $average_high_data = $yeardata[9];    
} else {
    $tickPositions = array(0,1,2,3,4,5,6,7,8,9,10,11);   
    $record_data = $yeardata[16];
    $average_data = $yeardata[17]; 
    $average_high_data = $yeardata[18]; 
} 
$tickLabels = $month_names; 

      // Setup the graph
$graph = new Graph($width, $height);

//Setup margin color
$graph->SetMarginColor("$margin_color");

// Set the legend background color
$graph->legend->SetFillColor("$legend_background_color");
// Put a horizontal legend box at the bottom of the graph
$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->Pos(0.5,0.98,'center','bottom');
  
$graph->title->Set ("$heading_name ($wind_unit)");
 
// Use a "text" X-scale
$graph->SetScale('textlin',$min_wind_scale,$max_wind_scale);
 
// Specify X-labels
$graph->xaxis->SetTickLabels($tickLabels); 
$graph->xaxis->SetTickPositions($tickPositions);
$graph->xaxis->SetTextLabelInterval(1);  
$graph->xaxis->SetPos( 'min' );
if ($show_values AND ($graph_interval == 2)){
    $graph->yaxis->SetLabelMargin(15); 
    $graph->yaxis->scale->SetGrace(10,0);
}
$graph->xgrid->Show();

if ($show_record_highs){
    $p1 = new LinePlot($record_data); 
    $p1->SetColor("$record_high_color");  
    $p1->SetWeight( 2 ); 
    $graph->Add($p1);
    $p1->SetLegend("$record_high_legend_text");
    if ($show_values AND ($graph_interval == 2)){
        $p1->value->Show();
    }    
}

if ($show_average_highs){
    $p2 = new LinePlot($average_high_data);  
    $p2->SetColor("$average_high_color");
    $p2->SetWeight( 2 ); 
    $graph->Add($p2);
    $p2->SetLegend("$average_high_legend_text");
    if ($show_values AND ($graph_interval == 2)){
        $p2->value->Show();
    }    
}

if ($show_average_wind){
    $p3 = new LinePlot($average_data);  
    $p3->SetColor("$average_color");
    $p3->SetWeight( 2 );  
    $graph->Add($p3);
    $p3->SetLegend("$average_legend_text");
    if ($show_values AND ($graph_interval == 2)){
        $p3->value->Show();
    }    
}


$graph->img->SetMargin(40,30,30,80); // left,right,top,bot

$graph->Stroke();   

################# Functions ####################################

function create_image1(&$value,$value1)
{
    //Set the image width and height
    $width = 400;
    $height = 100;

    //Create the image resource
    $image = ImageCreate($width, $height);

    //We are making three colors, white, black and gray
    $white = ImageColorAllocate($image, 255, 255, 255);
    $black = ImageColorAllocate($image, 0, 0, 0);
    $grey = ImageColorAllocate($image, 204, 204, 204);

    //Make the background black
    ImageFill($image, 0, 0, $grey);

    //Add randomly generated string in white to the image
    ImageString($image, 5, 40, 20, $value, $black);
    ImageString($image, 5, 40, 60, $value1, $black);

    //Tell the browser what kind of file is come in
    header("Content-Type: image/jpeg");

    //Output the newly created image in jpeg format
    ImageJpeg($image);

    //Free up resources
    ImageDestroy($image);
}  

function check_sourceview () {
    global $SITE;
    
    if ( isset($_GET['view']) && $_GET['view'] == $SITE['viewscr'] ) {
        $filenameReal = __FILE__;
        $download_size = filesize($filenameReal);
        header('Pragma: public');
        header('Cache-Control: private');
        header('Cache-Control: no-cache, must-revalidate');
        header("Content-type: text/plain");
        header("Accept-Ranges: bytes");
        header("Content-Length: $download_size");
        header('Connection: close');
        readfile($filenameReal);
        exit;
    }
}

############################################################################
# End of Page
############################################################################
?>     

