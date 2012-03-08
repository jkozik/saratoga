<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-USA template set)
############################################################################
#
#   Project:    Local River/Lake Heights
#   Module:     river-fetch.php
#   Purpose:    Gets data from AHPS
#   Authors:    Dennis Clapperton <webmaster@eastmasonvilleweather.com>
#               East Masonville Weather
#   Version:	2.15
############################################################################

include("river-config.php");

foreach($RiverGauge as $riverid => $rivername){
/*
$ch = curl_init("http://water.weather.gov/ahps2/hydrograph_to_xml.php?gage=$riverid&output=xml");
$fp = fopen("river-$riverid.txt", "w");

curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);

curl_exec($ch);
curl_close($ch);
fclose($fp);
}
*/
// 	if the curl does not work
	$html = implode(" ", file("http://water.weather.gov/ahps2/hydrograph_to_xml.php?gage=$riverid&output=xml")); 
	$fp = fopen("river-$riverid.txt", "w");
	if ($fp) {
	$write = fputs($fp, $html);
	fclose($fp);
	} 
}

// Thanks to Jim for this great addition
// get the image map and write to file
$BasePage = str_replace("all_layer_merge.php","index.php",$currentstage);  // use the image url to find the main page
$html = implode(" ", file($BasePage)); 
preg_match_all('|<area(.*)>|Uis', $html, $matches);                        // get each of the "area" lines 
$fp = fopen("river-map.txt", "w");
$write = fputs($fp, '<map name="rivermap" id="rivermap">' . "\r");
for ($i=1;$i<count($matches[1]);$i++) {                                     // skip [1][0] because that is the one for printing	
/* Removed 9/26/11 due to another change in the source page
	if (substr($matches[1][$i],0,1) <> '"') {                                                        // this added 12-31-10 for the quote sign that went missing
		$matches[1][$i] = '"' . substr($matches[1][$i],1,strlen($matches[1][$i]));                   // put the quote symbol back in there
	}                                                                                                // end of 12-31-10 change here
*/		
	$matches[1][$i] = '"' . substr($matches[1][$i],1,strlen($matches[1][$i]));                       // added 9/26/11 to put quotes around the url
	$matches[1][$i] = str_replace(' tabindex=','" tabindex=',$matches[1][$i]);                       // end of the 9/26/11 change	
	preg_match_all('|"(.*)"|Uis', $matches[1][$i], $parts);                // divide into each of the elements
	$site = substr($parts[1][4], 0, 5);                                    // extract the site id
	
	$parts[1][0] = str_replace("href=","",$parts[1][0]);                                             // added 12-31-10 due to url change
	if (array_key_exists($site, $RiverGauge) || array_key_exists(strtoupper($site), $RiverGauge)) {  // it's in our array
		if (array_key_exists(strtoupper($site), $RiverGauge)) {                                      // check if it's upper case in the array
			$site = strtoupper($site);
		}
		$line = '<area href="' . $detailspage . '?id=' . $site . '" tabindex="' . $parts[1][1] . '" shape="'	. $parts[1][2] . '" coords="'	. $parts[1][3] . '" alt="'	. $parts[1][4] . '" title="'	. $parts[1][5] . '" />' . "\r";
	} else {                                                               // not in array so we'll link to their site
//		$line = '<area href="' . $parts[1][0] . '" tabindex="' . $parts[1][1] . '" shape="'	. $parts[1][2] . '" coords="'	. $parts[1][3] . '" alt="'	. $parts[1][4] . '" title="'	. $parts[1][5] . '" '  .  $target . '/>' . "\r";
		$line = '<area href="http://water.weather.gov' . $parts[1][0] . '" tabindex="' . $parts[1][1] . '" shape="'	. $parts[1][2] . '" coords="'	. $parts[1][3] . '" alt="'	. $parts[1][4] . '" title="'	. $parts[1][5] . '" '  .  $target . '/>' . "\r";                                 // this changed 12-31-10 too

	}
	if ($fp) {
		$line = str_replace("&","&amp;",$line);
		$write = fputs($fp, $line);
	}
}
$write = fputs($fp, '</map>' . "\r");
fclose($fp);
?>