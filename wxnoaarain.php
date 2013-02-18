<?php
############################################################################
#
#   Module:     wxnoaarain.php
#   Purpose:    Display a graph of rain data from dailynoaareport files.
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
# 2010-08-21 1.1 Changed Position of Legend and added bargraph option
# 2010-12-14 1.11 Added function to display error message if jpgraph files not found
############################################################################ 
include_once("Settings.php");
include_once("common.php");
#################   User Settings   ###############################
# $loc = "./";            # Set to location of NOAA report files
$loc = "./noaa/";            # Set to location of NOAA report files
$jploc = "./jpgraph/" ; # Set to location of jpgraph files
$first_year_of_data = "2010";  # First year of NOAA data that is available
$heading_name = 'Naperville Rainfall since March '.$first_year_of_data;  # Text to be displayed above graph
$rain_unit = 'inches' ;  # Set to inches, mm, etc - Only used on heading 
$graph_interval = 2;  # 1 = daily, 2 = monthly
$graph_type = 2; # 1 = line graph, 2 = bar graph -- NOTE: Bar Graph is only valid when using monthly interval
$show_values = true; # Set to true to display the value on the chart. - Only used with monthly graph interval
$show_record_rain = true;
$show_average_rain = true;
$record_rain_color = "red";
$average_rain_color = "blue";
$margin_color = "lightblue";
$legend_background_color = "white";
$record_rain_legend_text = "Record Rain";
$average_rain_legend_text = "Average Rain";
$month_names = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'); 
$width = "660";   # Width of graph in pixels
$height = "400";   # Height of graph in pixels
$do_cache = true; # Set to true to cache data for up to 24 hours.
$cache_location = "./cache/"; # Set to directory for cache files. Directory must be chmod to 666 or higher; 
$SITE['viewscr'] = 'sce';  // Password for View Source Function 
#################   End of User Settings   ###############################

error_reporting(E_ALL & ~E_NOTICE);
check_sourceview();
require_once("wxnoaacsvdata.php"); 

if ($graph_interval == 1)
    $graph_type = 1; // Bar graph not valid with daily increment

$max_rain_scale = ''; //max($yeardata[5]);
$min_rain_scale = 0;

    
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
    $record_data = $yeardata[5];
    $average_data = $yeardata[6]; 
} else {
    $tickPositions = array(0,1,2,3,4,5,6,7,8,9,10,11);   
    $average_data = $yeardata[15];  
} 
$tickLabels = $month_names; 

      // Setup the graph
$graph = new Graph($width, $height);
//$graph = new BarPlot($average_data);

//Setup margin color
$graph->SetMarginColor("$margin_color");

// Set the legend background color
$graph->legend->SetFillColor("$legend_background_color");

// Put a horizontal legend box at the bottom of the graph
$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->Pos(0.5,0.98,'center','bottom');
  
$graph->title->Set ("$heading_name ($rain_unit)");
 
// Use a "text" X-scale
$graph->SetScale('textlin',$min_rain_scale,$max_rain_scale);
 
// Specify X-labels
$graph->xaxis->SetTickLabels($tickLabels);
if ($graph_type == 1){ 
    $graph->xaxis->SetTickPositions($tickPositions);
}
$graph->xaxis->SetTextLabelInterval(1);  
$graph->xaxis->SetPos( 'min' );
$graph->xgrid->Show();
if ($show_values AND ($graph_interval == 2)){
    $graph->yaxis->SetLabelMargin(15);   
    $graph->yaxis->scale->SetGrace(10,0);    
}
if ($graph_interval == 1){
    if ($show_record_rain){
        $p1 = new LinePlot($record_data); 
        $p1->SetColor("$record_rain_color");  
        $p1->SetWeight( 2 ); 
        $graph->Add($p1);
        $p1->SetLegend("$record_rain_legend_text");
        }
}

if ($show_average_rain){
    if (($graph_type == 2) AND ($graph_interval == 2)){
        // Create a bar pot
        $p2 = new BarPlot($average_data);
        //Setup width of bars
        $p2->SetWidth(0.7);
        // Setup color for gradient fill style 
        $p2->SetFillGradient("$average_rain_color","white",GRAD_LEFT_REFLECTION);
    } else {    
        $p2 = new LinePlot($average_data);  
        $p2->SetColor("$average_rain_color");
    }
    
    $p2->SetWeight( 2 );     
    $graph->Add($p2);
    $p2->SetLegend("$average_rain_legend_text"); 
    if ($show_values AND ($graph_interval == 2)){
        $p2->value->Show();
        $p2->value->SetColor("red","darkred");
   
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

