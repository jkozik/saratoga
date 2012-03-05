<?php
############################################################################
#
#   Project:    Data Quality Statistics
#   Module:     CwopStats
#   Purpose:    Provides Data Quality Statistics for Web page display
#   Authors:    Michael (michael@relayweather.com
############################################################################
#		Usage:  Place the following on your webpage
#		include_once('cwopstats.php');
#		
############################################################################
/////////////////////////////////////////////////////////////////////////////
//SETTINGS START HERE////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

$cwop = "C7164"; // Enter your CWOP Identification number Ex. CW3783 = C3783
// Your City, State Name
$cityname= "Naperville, IL";  //Enter your City,State
$sitename= "NapervilleWeather.net";  //Enter your City,State

/////////////////////////////////////////////////////////////////////////////
//END SETTINGS///////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

// This is for error reporting
//ini_set('display_errors', 1); 
//error_reporting(E_ALL);

// version
$cwopver = "1.1";

// date for graph images url
$graphdate =  date("Ymd");

// default zone
if (! isset($_GET['span']) ) {
$fileName = "http://weather.gladstonefamily.net/site/".$cwop."?days=28;tile=10#Data";
$span = "28 Days";
$daycount = "28";
$chartbaro = "http://weather.gladstonefamily.net/qchart/".$cwop."?date=". $graphdate ."&amp;sensor=baro&amp;days=28";
$charttemp = "http://weather.gladstonefamily.net/qchart/".$cwop."?date=". $graphdate ."&amp;sensor=temp&amp;days=28";
$chartdew = "http://weather.gladstonefamily.net/qchart/".$cwop."?date=". $graphdate ."&amp;sensor=dewp&amp;days=28";
}

// default zone (use same fileName as above)
if (isset($_GET['span']) && $_GET['span'] == false) {	
$fileName = "http://weather.gladstonefamily.net/site/".$cwop."?days=28;tile=10#Data";
$span = "28 Days";
$daycount = "28";
$chartbaro = "http://weather.gladstonefamily.net/qchart/".$cwop."?date=". $graphdate ."&amp;sensor=baro&amp;days=28";
$charttemp = "http://weather.gladstonefamily.net/qchart/".$cwop."?date=". $graphdate ."&amp;sensor=temp&amp;days=28";
$chartdew = "http://weather.gladstonefamily.net/qchart/".$cwop."?date=". $graphdate ."&amp;sensor=dewp&amp;days=28";
}

  $spanLength = array('3d', '7d', '14d', '4w', '8w', '13w', '26w', '39w', '52w');
$span2days = array(3, 7, 14, 28, 56, 91, 182, 273, 364);
$key = 0;

if (isset($_GET['span']) && $_GET['span'] == true) {
  
   // get the selected key
   $key = array_search($_GET['span'], $spanLength);

   // if the selected days is less than 4 weeks, use this data
   if($key <= 3){
      $fileName = "http://weather.gladstonefamily.net/site/".$cwop."?days=".$span2days["$key"].";tile=10#Data";
      $span = $span2days["$key"]." Days";
      $daycount = $span2days["$key"];
      $chartbaro = "http://weather.gladstonefamily.net/qchart/".$cwop."?date=". $graphdate ."&amp;sensor=baro&amp;days=".$span2days["$key"]."";
      $charttemp = "http://weather.gladstonefamily.net/qchart/".$cwop."?date=". $graphdate ."&amp;sensor=temp&amp;days=".$span2days["$key"]."";
      $chartdew = "http://weather.gladstonefamily.net/qchart/".$cwop."?date=". $graphdate ."&amp;sensor=dewp&amp;days=".$span2days["$key"]."";
  }
  // if the data is greater than 4 weeks, use this data
   if($key >= 4){
      $fileName = "http://weather.gladstonefamily.net/site/".$cwop."?days=".$span2days["$key"].";tile=10#Data";
      $span = $span2days["$key"]." Days";
      $daycount = $span2days["$key"];
      $chartbaro = "http://weather.gladstonefamily.net/cgi-bin/wxsitequal.pl?site=".$cwop."&amp;days=".$span2days["$key"]."&amp;sensor=baro";
      $charttemp = "http://weather.gladstonefamily.net/cgi-bin/wxsitequal.pl?site=".$cwop."&amp;days=".$span2days["$key"]."&amp;sensor=temp";
      $chartdew = "http://weather.gladstonefamily.net/cgi-bin/wxsitequal.pl?site=".$cwop."&amp;days=".$span2days["$key"]."&amp;sensor=dewp";
  }
}


//Start Parsing the City page
$url4 = ($fileName);
$html4 = implode(' ', file($url4));



// Find Baro --  If Baro Out of Tolerance Error is found stop the display of the additional table rows
if (preg_match('|Worst daily average barometer error|Uis', $html4))  {
   preg_match('|Average barometer error: <td align=right>(.*)<tr><Td>Error standard deviation: <td align=right>(.*)<tr><td>Worst daily a|', $html4, $baroerr);
$avbaroerr = trim($baroerr[1]);
$sdbaroerr = trim($baroerr[2]);
}
else{
   preg_match('|Average barometer error: <td align=right>(.*)<tr><Td>Error standard deviation: <td align=right>(.*)</table>|', $html4, $baroerr);
$avbaroerr = trim($baroerr[1]);
$sdbaroerr = trim($baroerr[2]);
}


//Find Avg Temp and Std Dev Temp --  If Temp Out of Tolerance Error is found stop the display of the additional table rows
if (preg_match('|Worst average temperature error|Uis', $html4)) { 
preg_match('|Average temperature error<td align=right>(.*)<tr><td>Worst a|Uis', $html4, $posnegtemperrors);
$temperr = preg_split("/<td align=right>/", $posnegtemperrors[0]);

$avtemperr24 = trim($temperr[1]);
$avtemperr24 = preg_replace('/<font color=red>/', '' , $avtemperr24);
$avtemperrday = trim($temperr[2]);
$avtemperrday = preg_replace('/<font color=red>/', '' , $avtemperrday);
$avtemperrnite = trim($temperr[3]);
$avtemperrnite = preg_replace('/<font color=red>/', '' , $avtemperrnite);
$avtemperrnite = preg_replace('/<tr><td>/', '' , $avtemperrnite);
$avtemperrnite = preg_replace('/Error standard deviation/', '' , $avtemperrnite);
$sdtemperr24 = trim($temperr[4]);
$sdtemperr24 = preg_replace('/<font color=red>/', '' , $sdtemperr24);
$sdtemperrday = trim($temperr[5]);
$sdtemperrday = preg_replace('/<font color=red>/', '' , $sdtemperrday);
$sdtemperrnite = trim(strip_tags($temperr[6]));
$sdtemperrnite = preg_replace('/Worst a/', '' , $sdtemperrnite);
$sdtemperrnite = preg_replace('/<font color=red>/', '' , $sdtemperrnite);
}
else {
preg_match('|Average temperature error<td align=right>(.*)</table>|Uis', $html4, $posnegtemperrors);
$temperr = preg_split("/<td align=right>/", $posnegtemperrors[0]);

$avtemperr24 = trim($temperr[1]);
$avtemperr24 = preg_replace('/<font color=red>/', '' , $avtemperr24);
$avtemperrday = trim($temperr[2]);
$avtemperrday = preg_replace('/<font color=red>/', '' , $avtemperrday);
$avtemperrnite = trim($temperr[3]);
$avtemperrnite = preg_replace('/<font color=red>/', '' , $avtemperrnite);
$avtemperrnite = preg_replace('/<tr><td>/', '' , $avtemperrnite);
$avtemperrnite = preg_replace('/Error standard deviation/', '' , $avtemperrnite);
$sdtemperr24 = trim($temperr[4]);
$sdtemperr24 = preg_replace('/<font color=red>/', '' , $sdtemperr24);
$sdtemperrday = trim($temperr[5]);
$sdtemperrday = preg_replace('/<font color=red>/', '' , $sdtemperrday);
$sdtemperrnite = trim(strip_tags($temperr[6]));
$sdtemperrnite = preg_replace('/Worst a/', '' , $sdtemperrnite);
$sdtemperrnite = preg_replace('/<font color=red>/', '' , $sdtemperrnite);
}


//Find Avg Dew and Std Dev Dew --  If Dew Out of Tolerance Error is found stop the display of the additional table rows
if (preg_match('|Worst average dewpoint error|Uis', $html4)) {
   preg_match('|Average dewpoint error<td align=right>(.*)<tr><td>Worst a|', $html4, $posnegdewerrors);
$errtempdew = preg_split("/<td align=right>/", $posnegdewerrors[0]);

$avdewerr24 = trim($errtempdew[1]);
$avdewerr24 = preg_replace('/<font color=red>/', '' , $avdewerr24);
$avdewerrday = trim($errtempdew[2]);
$avdewerrday = preg_replace('/<font color=red>/', '' , $avdewerrday);
$avdewerrnite = trim($errtempdew[3]);
$avdewerrnite = preg_replace('/<font color=red>/', '' , $avdewerrnite);
$avdewerrnite = preg_replace('/<tr><td>/', '' , $avdewerrnite);
$avdewerrnite = preg_replace('/Error standard deviation/', '' , $avdewerrnite);
$sddewerr24 = trim($errtempdew[4]);
$sddewerr24 = preg_replace('/<font color=red>/', '' , $sddewerr24);
$sddewerrday = trim($errtempdew[5]);
$sddewerrday = preg_replace('/<font color=red>/', '' , $sddewerrday);
$sddewerrnite = trim(strip_tags($errtempdew[6]));
$sddewerrnite = preg_replace('/Worst a/', '' , $sddewerrnite);
$sddewerrnite = preg_replace('/<font color=red>/', '' , $sddewerrnite);
}
else {
preg_match('|Average dewpoint error<td align=right>(.*)</table>|Uis', $html4, $posnegdewerrors);
$errtempdew = preg_split("/<td align=right>/", $posnegdewerrors[0]);

$avdewerr24 = trim($errtempdew[1]);
$avdewerr24 = preg_replace('/<font color=red>/', '' , $avdewerr24);
$avdewerrday = trim($errtempdew[2]);
$avdewerrday = preg_replace('/<font color=red>/', '' , $avdewerrday);
$avdewerrnite = trim($errtempdew[3]);
$avdewerrnite = preg_replace('/<font color=red>/', '' , $avdewerrnite);
$avdewerrnite = preg_replace('/<tr><td>/', '' , $avdewerrnite);
$avdewerrnite = preg_replace('/Error standard deviation/', '' , $avdewerrnite);
$sddewerr24 = trim($errtempdew[4]);
$sddewerr24 = preg_replace('/<font color=red>/', '' , $sddewerr24);
$sddewerrday = trim($errtempdew[5]);
$sddewerrday = preg_replace('/<font color=red>/', '' , $sddewerrday);
$sddewerrnite = trim(strip_tags($errtempdew[6]));
$sddewerrnite = preg_replace('/Worst a/', '' , $sddewerrnite);
$sddewerrnite = preg_replace('/<font color=red>/', '' , $sddewerrnite);
}


//MADIS Rating
preg_match_all('|alt="MADIS rating \d\d0{0,3}\%|Uis', $html4, $madis);
$madis[0][0] = preg_replace('|alt="MADIS rating |', '', $madis[0][0]);
$madis[0][1] = preg_replace('|alt="MADIS rating |', '', $madis[0][1]);
$madis[0][2] = preg_replace('|alt="MADIS rating |', '', $madis[0][2]);
$madis[0][3] = preg_replace('|alt="MADIS rating |', '', $madis[0][3]);
$madis[0][0] = preg_replace('|%|', '', $madis[0][0]);
$madis[0][1] = preg_replace('|%|', '', $madis[0][1]);
$madis[0][2] = preg_replace('|%|', '', $madis[0][2]);
$madis[0][3] = preg_replace('|%|', '', $madis[0][3]);
$qcbaro = trim($madis[0][0]);
$qctemp = trim($madis[0][1]);
$qcdewp = trim($madis[0][2]);
$qcwind = trim($madis[0][3]);

?>
