<?php
###############################################################
#
#   NWS Public Alerts
#   Detail Page
#
#   This file is to be included in another page
#
###############################################################

ini_set('display_errors', 1);
error_reporting(E_ALL);

include('nws-alerts-config.php');                                            // include the config/settings file

// overrides from Settings.php if available
global $SITE;
if(isset($SITE['cacheFileDir'])) {$cacheFileDir = $SITE['cacheFileDir'];}
if(isset ($SITE['tz']))          {$ourTZ = $SITE['tz'];}
if(!function_exists('date_default_timezone_set')) {
  putenv("TZ=" . $ourTZ);
} else {
  date_default_timezone_set("$ourTZ");
}
include($cacheFileDir.$cacheFileName);                                       // include the data cache file

$alertDetails = '';                                                          // set variable
$nothing      = '';                                                          // set variable
$loclatlon    = '';                                                          // set variable
$sfll         = '';                                                          // set variable
$zcll         = '';                                                          // set variable
$jsmap        = '';                                                          // set variable
$jsloc        = '';                                                          // set variable
$zCode        = '';                                                          // set variable
$zCoord       = '';                                                          // set variable
$dmn          = '';                                                          // set variable
$haveJS       = '';                                                          // set variable
$asf          = '';                                                          // set variable
$polyLegend   = '';                                                          // set variable
$m = 1;
$fileUpdated  = date("D g:i a",filemtime($cacheFileDir.$cacheFileName));     // get last modified time of the cache file

// check if NWS-alerts.php could set a javascript cookie
if(isset($_COOKIE["NWSalerts"])) {    // IF a cookie was placed by javascript
	$haveJS = true;                     //   default to a set cookie
//	echo $_COOKIE["NWSalerts"];
//	$jsct = $_COOKIE["NWSalerts"];      //   variable equals javascript cookie value (unix timestamp)
//  if($jsct < $uts) {                  //   IF the cookie value is over an hour old
//	  $haveJS = '';                     //     javascript is no longer used
//  }
}

// set the map style
switch($mapStyle){
  case 1:
    $mStyle = 'ROADMAP';
    break;
  case 2:
    $mStyle = 'SATELLITE';
    break;
  case 3:
    $mStyle = 'HYBRID';
    break;
  case 4:
    $mStyle = 'TERRAIN';
    break;
  default:
    $mStyle = 'ROADMAP';
    break;
}

if(isset($_GET['a']) && !empty($_GET['a'])) {                                // IF the location code is set
  $czCode = htmlspecialchars(strip_tags($_GET['a']));                        //   clean up location code
	(preg_match("/$czCode/", $validCodes)) ? $czCode = $czCode : $czCode = ''; //   set variable to location code
	(preg_match("/\w{2}Z\d/", $czCode)) ? $zCode = $czCode : $zCode = '';      //   check for zone code
}
if(!isset($_GET['a']) or $czCode == '') {                                    // IF the location code is set or no code found
  $thisLoc = 'an unknown location';                                          //   set location to unknown
  $czCode = '';                                                              //   clear variable
  $nothing = 'Location has not been identified';                             //   set alert remark
}

if(!empty($noAlerts) and in_array($czCode,$noAlerts)) {                      // IF there are no alerts for current location and code is in no alert array
  $thisLoc = array_search($czCode,$noAlerts);                                //   set variable to location
  $czCode = '';                                                              //   clear variable
  $nothing = 'No severe weather expected for '.$thisLoc;                     //   set alert remark
}

if($nothing <> '') {                                                         // IF there are no alerts, or missing location, create alert table
  $alertDetails = '
<div style="width: 630px; margin: 0px auto 6px auto; border: 1px solid #000; background-color:#EFEFEF">
 <table width="600" border="0" cellspacing="0" cellpadding="0" style="margin: 10px auto 14px auto">
  <tr>
   <td style="text-align:center; color:#000; padding:4px 0px 2px;">'.$nothing.'</td>
  </tr>
 </table>
</div>
<p> </p>
';
}
if (!empty($czCode)) {                                                                          // IF there are alerts for the location
  foreach($atomAlerts[$czCode] as $aak => $aav) {                                               //   FOR EACH alert
		$thisLoc = $aav[12];                                                                        //     get the location
		$aav[7] = htmlspecialchars($aav[7]);                                                        //     change html characters
		$aav[8] = htmlspecialchars($aav[8]);                                                        //     change html characters
    (!empty($aav[4])) ? $effective = date("D g:i a",$aav[4]) : $effective = '';                 //     get effective time
    (!empty($aav[2])) ? $intensity = $aav[2] : $intensity = ' - - -';                           //     get intensity
		(!empty($aav[1])) ? $urgency = $aav[1] : $urgency = ' - - -';                               //     get urgency
		(!empty($aav[5])) ? $expires = date("D g:i a",$aav[5]) : $expires = '';                     //     get expiration time
		(!empty($aav[3])) ? $certainty = $aav[3] : $certainty = ' - - -';                           //     get certainty
		(!empty($aav[6])) ? $areas = $aav[6] : $areas = '';                                         //     get areas affected
		(!empty($aav[7])) ? $instruction = "<br />\n<b>Information:</b>\n"
                                       .'<pre style="white-space:pre-wrap">'
                                       .$aav[7].'</pre>' : $instruction = '';                   //     get information/instructions
		(!empty($aav[8])) ? $details = "\n<b>Details:</b>\n".'<pre style="white-space:pre-wrap">'
                                   .$aav[8].'</pre>' : $details = '';                           //     get details
		(!empty($aav[15])) ? $sf = $aav[15] : $sf = '';                                             //     get shape area
		if(!empty($sf)){                                                                            //     IF there is a shape area
      $sfll[] = explode(' ', $sf);                                                              //       create array of coordinates for shape files
			$asf[] = preg_replace("/\s/", '&nbsp;', $aav[0]);                                         //       create array for alert shape file
		}
		
		if(!empty($intensity) and $intensity == 'Extreme') {
			$intensity = '<span style="color: red"><b> &nbsp;'.$intensity.'&nbsp; </b></span>';}      //     change intensity color
		if(!empty($intensity) and $intensity == 'Severe') {
			$intensity = '<span style="color: #F66"><b> &nbsp;'.$intensity.'&nbsp; </b></span>';}     //     change intensity color
		if(!empty($intensity) and $intensity == 'Moderate') {
			$intensity = '<span style="-color: #FF9"><b> &nbsp;'.$intensity.'&nbsp;</b> </span>';}    //     change intensity color
    if(!empty($areas)) {$areas = preg_replace('/;/',' -',$areas);}                              //     change semi-colon to a dash
			
    // put together all details
		$alertDetails .= ' <div style="width:630px; margin:0px auto 0px auto; border:1px solid '
                     .$aav[9].'; background-color:#EFEFEF">
  <a name="WA'.$aav[13].'" id="WA'.$aav[13].'"></a>
  <table width="610px" border="0" cellspacing="0" cellpadding="0" style="margin:10px auto 14px auto">
   <tr>
    <td colspan="3" style="text-align:center; color:'.$aav[9].'; padding:4px 0px 2px; font-size:110%">'
    .$aav[10].'&nbsp;&nbsp;<b>'.strtoupper($aav[0]).'</b>&nbsp;&nbsp;'.$aav[10].'</td>
   </tr>
   <tr>
    <td colspan="3" style="text-align:center; letter-spacing:2px; padding:4px 8px 4px 8px; font-size:115%"><b>'
    .strtoupper($aav[12]).'</b></td>
   </tr>
   <tr>
    <td colspan="3" style="text-align: center; padding:4px 12px 18px 12px"><hr/><b>Areas Affected:</b><br /> '.$areas.'</td>
   </tr>
   <tr>
    <td style="padding-left:28px;"><b>Effective:</b> '.$effective.'</td>
    <td style="padding-left:28px;"><b>Updated:</b> '.$fileUpdated.'</td>
    <td style="padding-left:28px;"><b>Urgency:</b> '.$urgency.'</td>
   </tr>
   <tr>
    <td style="padding-left:28px;"><b>Expires:</b> '.$expires.'</td>
    <td style="padding-left:28px;"><b>Severity:</b> '.$intensity.'</td>
    <td style="padding-left:28px;"><b>Certainty:</b> '.$certainty.'</td>
   </tr>
   <tr>
    <td colspan="3" style="text-align: center; padding:4px 12px 0px 12px"><hr/>&nbsp;</td>
   </tr>
   <tr>
    <td colspan="3" style="background-color: #FEFEF6; padding:10px; border: 1px solid '.$aav[9].'">'.$details.$instruction.'</td>
   </tr>
  </table>
 </div>
 <p> </p>
';
	}
}

if(file_exists('nws-shapefile.txt') && !empty($zCode) && !empty($haveJS)) {           // IF the shape file exists and a valid code
  // get data from shapefile for google map
  $nsf = trim(file_get_contents('nws-shapefile.txt'));                                //   get nws shape file
  $nsf = str_replace('  ||', "",  $nsf);                                              //   replace double pipes
  $nsf = str_replace('|  ', "",  $nsf);                                               //   replace pipe & double space
  $zll = preg_replace("/([A-Z]{2})\|(\d+|\d+ )\|.*(\|\d+\.\d+\|)/", '$1Z$2$3', $nsf); //   get zone, latitude, longitude
  $zll = explode("\n", $zll);                                                         //   explode each line
  foreach($zll as $lk) {                                                              //   FOR EACH zone code, lat, lon
    list($loc, $lat, $lon) = explode('|', $lk);                                       //     list variables
    $lon = trim($lon);                                                                //     trim spaces off of longitude
    $loclatlon[$loc] = $lat.','.$lon;                                                 //     create array
  }
  if (array_key_exists($zCode, $loclatlon)) {                                         //   IF the zone code is in the shape file array
    // create zoom notice
    if($showClouds && $zoomLevel >=7 && empty($sfll)) {                               // IF display clouds & zoom level is 7 or higher & no polygons
      // display zoom information
      $dmn .= ' <div style="width: 630px; background-color:#FFF; border:1px solid black">
  <span style="font-size:80%; background-color:#FFF">&nbsp; Zoom out to display the cloud cover.&nbsp;&nbsp;&nbsp;&nbsp;</span>'
	."\n </div>\n";  //   display map notice
    }
    $zcll = $loclatlon[$zCode];	                                                             //     get zone code latitude,longitude
    // set polygon overlays	
    if(!empty($sfll)) {                                                                      // IF there are shape files
      $cp = count($sfll)*4+1;                                                                  //   count shapes files & add 2
      $polyLegend = ' <div style="width: 630px; text-align:center; background-color:#FFF">'; //   start polygon legend
      foreach($sfll as $zk => $zv) {                                                         //   FOR EACH shape file
//      $cp = $cp-1;                                                                           //     subtract 1 from shape files
      $cp = $cp/2;                                                                           //     subtract 1 from shape files
        $czc = count($zv);                                                                   //     count points
        $zCoord .= 'var zoneCoords'.$zk.' = [';                                              //     assemble polygon overlay
        for ($i=0;$i<$czc;$i++) {                                                            //     FOR EACH set of points
          $zCoord .= 'new google.maps.LatLng('.$zv[$i].'),'."\n";                            //       assemble each set of points
        }
        $zCoord .= '];'.over_lay($zk,$cp);                                                   //     finish polygon overlay
        // create polygon legend
        $polyLegend .= '
  &nbsp; <span style="white-space: nowrap"><span style="color:black; font-size:75%; background-color:'.$rc
  .'; opacity:0.8; filter:alpha(opacity=80); border:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;</span>
<span style="color:black; font-size:75%;">'.$asf[$zk].' &nbsp;&nbsp;&nbsp;</span></span> &nbsp;';
      }
      $polyLegend .= "\n </div>\n";
    }
    // put together google map data
		$jsmap = ' <div id="map_canvas" style="width: 630px; height: 160px; border:1px solid black"></div>'."\n";
    $jsloc .= '<!-- Google map javascript -->
<script type="text/javascript">
 var currentInfoWindow = null;
 var bounds = new google.maps.LatLngBounds();
 var latlng = new google.maps.LatLng('.$zcll.');
 var options = {
  zoom: '.$zoomLevel.',
  center: latlng,
  mapTypeId: google.maps.MapTypeId.'.$mStyle.',
  scrollwheel: false,
  streetViewControl: false,
 };
 function showmap() {
  var map = new google.maps.Map(document.getElementById("map_canvas"), options);
  map.setTilt(0);';
    if($showClouds) {
      $jsloc .=  '
  cloudLayer = new google.maps.weather.CloudLayer();
  cloudLayer.setMap(map);';
    }
    $jsloc .= $zCoord;
    $jsloc .= '  }
 google.maps.event.addDomListener(window, "load", showmap);
</script>'.
"\n<!-- End of Google map javascript -->\n
";		
  }
}

// FUNCTION - set google map polygon attributes
function over_lay($se,$pc) {
	global $rc;
$cs = $se;
$colors = array('#DF0101','#F79F81','#F2F5A9','#0174DF','#58FAD0',
                '#FACC2E','#01DF01','#F7BE81','#FE2E64','#E0F8EC');
$cc = count($colors);
if(!array_key_exists($cs,$colors)) {$cs = shuffle(range(0,$cs));}
$rc = $colors[$cs];
$ol = '
 countyArea'.$se.' = new google.maps.Polygon({
  paths: zoneCoords'.$se.',
  strokeColor: "'.$colors[$cs].'",
  strokeOpacity: 0.8,
  strokeWeight: '.$pc.',
  fillColor: "'.$colors[$cs].'",
  fillOpacity: 0.30
});
 countyArea'.$se.'.setMap(map);'."\n";
return $ol;
} // end of function

echo $jsloc; // echo the google map javascript
?>
<div style="width:632px; margin:0px auto 0px auto;">
 <table cellspacing="0" cellpadding="0" style="width:100%; margin:0px auto 0px auto; border:1px solid black; background-color:#F5F8FE">
  <tr>
   <td style="text-align:center; background:url(<?php 
     echo $icons_folder ?>/NOAAlogo1.png) no-repeat; background-position:center; padding:5px 0px 5px 0px"><h3>Weather Alerts for <?php
     echo $thisLoc; ?></h3><p>Issued by the National Weather Service </p></td>
  </tr>
 </table>
 <p> </p>
<?php if(!empty($haveJS)){echo $jsmap; echo $dmn; echo $polyLegend;} ?>
 <p> </p>
<?php echo $alertDetails ?>
</div>
