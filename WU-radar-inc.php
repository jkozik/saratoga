<?php
/*------------------------------------------------
// This script is for use in USA as it retrieves information from Weather Underground
//  for NOAA Radar sites and USA National and regional weather maps.
//
// According to John Celenza, Director of Weather Technology at Wunderground.
// 
// Please feel free to use Wunderground images and data on personal sites,
// as long as you link those images to Wunderground or give direct credit.
// Something like "This image courtesy of Weather Underground" is appropriate.
//
// Be sure to give credit where credit is due.
//
//  Original script by Tom at http://www.carterlake.org/
//  Modifications and enhancements by Jim at http://jcweather.us/
//  
//  Additional modifications by Ken True at http://saratoga-weather.org/ for use
//  in the Carterlake/AJAX/PHP website template and allowing easy setup using
//  settings area (using built-in javascript functions).
//
// the script does NOT generate a complete HTML page and is intended for use
// ONLY by being included in an existing webpage on your site by:
//   <?php include("WU-radar-inc.php"); ?>
// */
//  Version 1.00 - 07-Jan-2008 -  Initial beta release with settings customization features 
//                                and XHTML 1.0-Transitional valid code.
//  Version 1.01 - 08-Jan-2008 -  Regional map fixed, color option on legend, zoom radar corrections
//  Version 1.02 - 08-Jan-2008 -  Fixed problem with Safari browser, new Regional Advisory map and $CityPos9
//  Version 1.03 - 09-Jan-2008 -  Initial release - tested in IE7, FF2, Opera 9, Safari-Win
//  Version 1.04 - 09-Jan-2008 -  Fixed missing </a>, moved lower text inside overall table for better format 
//                                using <iframe> include.
//  Version 1.05 - 24-Jul-2008 -  Fixed legend for severe weather (thanks to Michael at http://www.relayweather.com/ )
//  Version 1.06 - 15-Apr-2011 -  Fixed wandering animation scale (changed 'type=N1'R to 'type=N0R' for 5 occurrence)
//
// settings ----------------------------- */
$imagesDir = './ajax-images/';  // directory for ajax-images+radar buttons with trailing slash
$RDR  = 'LOT';  // last 3 characters of NOAA Radar Site Name
$Lat  = '41.7900009';    //North=positive, South=negative decimal degrees
$Long = '-88.1200027';  //East=positive, West=negative decimal degrees
$City = 'Naperville';  // Name of city
$WUregion = 'mw';    // WeatherUnderground regional map group name
// 'sw'=SouthWest, 'nw'=NorthWest, 'mw'=Midwest
// 'sp'=South Central, 'ne'=North East, 'se'=South East
$WUname1	= 'City Level';		// tooltip label for mode=1
$WUname2	= 'Chicago Area';	// tooltip label for mode=2
$WUname3	= 'Chicago Area';	// tooltip label for mode=3
$WUname4	= 'Midwest US';		// tooltip label for mode=4
$WUname5	= 'Entire US';		// tooltip label for mode=5
//
//
// To set $MetroURL (and $CityURL) follow these steps:
//  1) go to www.wunderground.com and search for your city, state, then click on the radar image
//  2) draw a box around your 'metro area' .. the radar image will zoom in.
//  3) right click on 'View/Save this image' and copy URL to clipboard
//  4) paste the url from the clipboard into the $MetroURL = '...'; below.
//  5) draw a box on the WU radar around your 'city area'
//  6) right click on 'View/Save this image' and copy URL to clipboard
//  7) paste the URL from the clipboard into the $CityURL = '...'; below
//
// the $CityPosN variables are used to store the relative position information to
// place a red dot and $City over the map displayed.  There are 9 different sizes
// of maps displayed.  The $CityColorN variables are for the text color of the
// overlay city name display.  '#FFFFFF' (white) should work for most displays,
// You may need to adjust $CityColor4 (advisory map) and/or $CityColor8 (Flu map)
// to a different color since they use white backgrounds.

//  Now run the script with ?show=loc to enable the helper app.
//  1) Click on the $CityPos1 link shown on the page, then 
//  2) click the cross-hair cursor over the point where the red dot is to appear, and
//      the legend will move to that location, and the PHP code will appear in a text box
//      for copying new values.
//  3) Highlight the text box on the page displaying $CityPos1 = 'left: nnnpx; top: -mmmpx';
//  4) copy the contents to the clipboard, and paste it to replace the $CityPos1 line
//     shown below.
//  Repeat the procedure for $CityPos2 through $CityPos9 
//
// $CityPos1 - for City Level radar/animation
$CityPos1  =  'left: 389px; top: -254px;';
$CityColor1 = '#FFFFFF';  // color of legend display
// see instructions above to set  $CityURL value
$CityURL = 'http://radblast-aa.wunderground.com/cgi-bin/radar/WUNIDS_map?station=MUX&brand=wui&num=1&delay=15&type=N0R&frame=0&scale=0.125&noclutter=0&t=1199816619&lat=37.27153397&lon=-122.02274323&label=Saratoga%2C+CA&showstorms=0&map.x=400&map.y=240&centerx=478&centery=335&transx=78&transy=95&showlabels=1&severe=0&rainsnow=0&lightning=0';

// $CityPos2 - for Metro Level radar/animation	 
$CityPos2  =  'left: 394px; top: -260px;';
$CityColor2 = '#FFFFFF';  // color of legend display
// see instructions above to set  $MetroURL value
$MetroURL = 'http://radblast-aa.wunderground.com/cgi-bin/radar/WUNIDS_map?station=MUX&brand=wui&num=1&delay=15&type=N0R&frame=0&scale=0.272&noclutter=0&t=1199816502&lat=37.27153397&lon=-122.02274323&label=Saratoga%2C+CA&showstorms=0&map.x=400&map.y=240&centerx=436&centery=276&transx=36&transy=36&showlabels=1&severe=0&rainsnow=0&lightning=0';


// $CityPos3 - for unzoomed radar/animation
$CityPos3  =  'left: 386px; top: -262px;';
$CityColor3 = '#FFFFFF';  // color of legend display

// $CityPos4 - for US Advisory map
$CityPos4  =  'left: 35px; top: -324px;';
$CityColor4 = '#FFFFFF';  // color of legend display

// $CityPos5 = USA Radar/animation	 
$CityPos5  =  'left: 50px; top: -345px;';
$CityColor5 = '#FFFFFF';  // color of legend display

// $CityPos6 - for USA Radar Map
$CityPos6  =  'left: 29px; top: -319px;';
$CityColor6 = '#FFFFFF';  // color of legend display

// $CityPos7 - Regional maps (Fronts, Satellite, Wind, Jet Stream, Snow Depth, etc)
$CityPos7  =  'left: 51px; top: -345px;';
$CityColor7 = '#FFFFFF';  // color of legend display

// $CityPos8 - for USA Flu map
$CityPos8  =  'left: 34px; top: -318px;';
$CityColor8 = '#0000FF';  // color of legend display

// $CityPos9 - for Regional Advisories map
$CityPos9  =  'left: 49px; top: -326px;';
$CityColor9 = '#FFFFFF';  // color of legend display

// end of settings
//------------------------------------------------
if (isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
   //--self downloader --
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
?><!-- WU-radar-inc.php - Version 1.06 - 15-Apr-2011 - http://saratoga-weather.org/scripts.php --><?php

$PHP_SELF = $_SERVER['PHP_SELF'];
if ( empty($_REQUEST['mode']) ) 
        $_REQUEST['mode']="3";
if ( empty($_REQUEST['animated']) ) 
        $_REQUEST['animated']="0";
if ( empty($_REQUEST['advisories']) ) 
        $_REQUEST['advisories']="0";
if ( empty($_REQUEST['track']) ) 
        $_REQUEST['track']="0";
if ( empty($_REQUEST['lightning']) ) 
        $_REQUEST['lightning']="0";
//------------------------------------------------
// overrides from Settings.php if available
global $SITE;
if (isset($SITE['imagesDir'])) 	{$imagesDir = $SITE['imagesDir'];}
if (isset($SITE['noaaradar'])) 	{$RDR = $SITE['noaaradar'];}
if (isset($SITE['latitude'])) 	{$Lat = $SITE['latitude'];}
if (isset($SITE['longitude'])) 	{$Long = $SITE['longitude'];}
if (isset($SITE['cityname'])) 	{$City = $SITE['cityname'];}
if (isset($SITE['WUregion']))	{$WUregion = $SITE['WUregion']; }
if (isset($SITE['timeFormat'])) {$timeFormat = $SITE['timeFormat'];}
if (isset($SITE['tz'])) 		{$ourTZ = $SITE['tz'];}
if (isset($SITE['WUname1'])) 	{$WUname1 = $SITE['WUname1'];}	// tooltip label for mode=1
if (isset($SITE['WUname2'])) 	{$WUname2 = $SITE['WUname2'];}	// tooltip label for mode=2
if (isset($SITE['WUname3'])) 	{$WUname3 = $SITE['WUname3'];}	// tooltip label for mode=3
if (isset($SITE['WUname4'])) 	{$WUname4 = $SITE['WUname4'];}	// tooltip label for mode=4
if (isset($SITE['WUname5'])) 	{$WUname5 = $SITE['WUname5'];}	// tooltip label for mode=5
// end of overrides from Settings.php if available

$UTCtime = time(); // not random, but needed for URL fetches.
//------------------------------------------------
//Get values from web - set as default if nothing
//------------------------------------------------
$PHP_SELF = $_SERVER['PHP_SELF'];
if ( empty($_REQUEST['mode']) ) 
        $_REQUEST['mode']="3";
if ( empty($_REQUEST['animated']) ) 
        $_REQUEST['animated']="0";
if ( empty($_REQUEST['advisories']) ) 
        $_REQUEST['advisories']="0";
if ( empty($_REQUEST['track']) ) 
        $_REQUEST['track']="0";
if ( empty($_REQUEST['lightning']) ) 
        $_REQUEST['lightning']="0";
//------------------------------------------------

//------------------------------------------------
//Pass into PHP variables
//------------------------------------------------
$Mode = $_REQUEST['mode'];
$Animated = $_REQUEST['animated'];
$Advisories = $_REQUEST['advisories'];
$Track = $_REQUEST['track'];
$Lightning = $_REQUEST['lightning'];

$doShow = '';
if(isset($_REQUEST['show'])) {
  $doShow ='show=loc&amp;';
}

//------------------------------------------------
//Set more variables used below based on current value
//------------------------------------------------
if ($Advisories == 0) {
	$AdOption = "1";
	$AdNotice = "OFF";
	$AdText ="&amp;severe=0";
	$AdKey = '';
	} else {
	$AdOption = "0";
	$AdNotice = "ON";
	$AdText ="&amp;severe=1";
	$AdKey ='<br /><img src="'.$imagesDir.'severeKey.gif" width="640" border="1" height="64" alt="Severe Weather Key" /><br />&nbsp;'; }

if ($Animated == 0) {
	$AnOption = "1";
	$AnNotice = "OFF";
	$AnText = "&amp;num=1&amp;delay=15";
	} else {
	$AnOption = "0";
	$AnNotice = "ON";
	$AnText = "&amp;num=6&amp;delay=60"; }

if ($Track == 0) {
	$TrOption = "1";
	$TrNotice = "OFF";
	$TrText ="&amp;showstorms=0";
	} else {
	$TrOption = "0";
	$TrNotice = "ON";
	$TrText ="&amp;showstorms=31";
	$AdKey ='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family: Verdana,Arial,Sans Serif; font-size: 12px; color: #FFFFFF"><img src="' .
	$imagesDir . 'clearTriangle.gif" width="15" height="15" alt="" /> Tornado vortex&nbsp;&nbsp;<img src="' .
	$imagesDir . 'clearSquare.gif" width="15" height="15" alt="" /> Cloud rotation&nbsp;&nbsp;<img src="' .
	$imagesDir . 'clearDiamond.gif" width="15" height="15" alt="" /> Probable hail&nbsp;&nbsp;<img src="' .
	$imagesDir . 'blackSquare.gif" width="15" height="15" alt="" /> Storm cell
<br />&nbsp;</span>'; }

if ($Lightning == 0) {
	$LightOption = "1";
	$LightNotice = "OFF";
	$LightText ="&amp;lightning=0";
	} else {
	$LightOption = "0";
	$LightNotice = "ON";
	$LightText ="&amp;lightning=1"; }

//------------------------------------------------
//Set Mode plus and minus info - don't let user select invalid amount
//------------------------------------------------
$ModeMinus = $Mode - 1;
if ($ModeMinus < 1) { $ModeMinus = 1; }

$ModePlus = $Mode + 1;
if ($ModePlus > 5) { $ModePlus = 5; }

//------------------------------------------------
//Set variables for highlighting selected items
//------------------------------------------------

if ($Advisories==0) {$advcolor="#FFFFFF";} else {$advcolor="#FFFF00";}
if ($Animated==0) {$animcolor="#FFFFFF";} else {$animcolor="#FFFF00";}
if ($Track==0) {$trackcolor="#FFFFFF";} else {$trackcolor="#FFFF00";}
if ($Lightning==0) {$lightcolor="#FFFFFF";} else {$lightcolor="#FFFF00";}
if ($Mode==1) {$mode1HL="'${imagesDir}radar2a.gif'";} else {$mode1HL="'${imagesDir}radar2.gif'";}
if ($Mode==2) {$mode2HL="'${imagesDir}radar3a.gif'";} else {$mode2HL="'${imagesDir}radar3.gif'";}
if ($Mode==3) {$mode3HL="'${imagesDir}radar4a.gif'";} else {$mode3HL="'${imagesDir}radar4.gif'";}
if ($Mode==4) {$mode4HL="'${imagesDir}radar5a.gif'";} else {$mode4HL="'${imagesDir}radar5.gif'";}
if ($Mode==5) {$mode5HL="'${imagesDir}radar6a.gif'";} else {$mode5HL="'${imagesDir}radar6.gif'";}
if ($Mode==6) {$mode6HL="#FFFF00";} else {$mode6HL="#FFFFFF";}
if ($Mode==7) {$mode7HL="#FFFF00";} else {$mode7HL="#FFFFFF";}
if ($Mode==8) {$mode8HL="#FFFF00";} else {$mode8HL="#FFFFFF";}
if ($Mode==9) {$mode9HL="#FFFF00";} else {$mode9HL="#FFFFFF";}
if ($Mode==10) {$mode10HL="#FFFF00";} else {$mode10HL="#FFFFFF";}
if ($Mode==11) {$mode11HL="#FFFF00";} else {$mode11HL="#FFFFFF";}
if ($Mode==12) {$mode12HL="#FFFF00";} else {$mode12HL="#FFFFFF";}
if ($Mode==13) {$mode13HL="#FFFF00";} else {$mode13HL="#FFFFFF";}
if ($Mode==14) {$mode14HL="#FFFF00";} else {$mode14HL="#FFFFFF";}
if ($Mode==15) {$mode15HL="#FFFF00";} else {$mode15HL="#FFFFFF";}
if ($Mode==16) {$mode16HL="#FFFF00";} else {$mode16HL="#FFFFFF";}
if ($Mode==17) {$mode17HL="#FFFF00";} else {$mode17HL="#FFFFFF";}
if ($Mode==18) {$mode18HL="#FFFF00";} else {$mode18HL="#FFFFFF";}
if ($Mode==19) {$mode19HL="#FFFF00";} else {$mode19HL="#FFFFFF";}
if ($Mode==20) {$mode20HL="#FFFF00";} else {$mode20HL="#FFFFFF";}

//------------------------------------------------
//You need to download some graphics for this html. Pull them from...
//Do not link directly to them!
// NOTE:  all the graphics needed are in the ./ajax-images/ directory
//  in the distribution .zip file
//------------------------------------------------
//http://www.carterlake.org/radar1.gif
//http://www.carterlake.org/radar1a.gif
//http://www.carterlake.org/radar2.gif
//http://www.carterlake.org/radar2a.gif
//http://www.carterlake.org/radar3.gif
//http://www.carterlake.org/radar3a.gif
//http://www.carterlake.org/radar4.gif
//http://www.carterlake.org/radar4a.gif
//http://www.carterlake.org/radar5.gif
//http://www.carterlake.org/radar5a.gif
//http://www.carterlake.org/radar6.gif
//http://www.carterlake.org/radar6a.gif
//http://www.carterlake.org/radar7.gif
//http://www.carterlake.org/radar7a.gif
//http://www.carterlake.org/radar7a.gif
//http://www.carterlake.org/severeKey.gif
//http://www.carterlake.org/clearTriangle.gif
//http://www.carterlake.org/clearSquare.gif
//http://www.carterlake.org/clearDiamond.gif
//http://www.carterlake.org/blackSquare.gif
?>

<script type="text/javascript">
<!-- This script is the rollover script for graphics -->
function rollThis(whichImage,whichPic){
document.images[whichImage].src = whichPic;
}
</script> 
<a name="WUtop" id="WUtop"></a>
<table width="640" cellpadding="0" cellspacing="0" style="background-color:#000000; border: none;">

<tr>
<td style="background-color:#000000"><span style="font-family: Arial, Helvetica, sans-serif; font-size:12px; color:#FFFFFF">Zoom:&nbsp;</span>
<a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=<?php echo $ModeMinus; ?>&amp;animated=<?php echo $Animated; ?>&amp;track=<?php echo $Track; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;lightning=<?php echo $Lightning; ?>#WUtop" onmouseover="rollThis('pic1','<?php echo $imagesDir; ?>radar1a.gif')" onmouseout="rollThis('pic1','<?php echo $imagesDir; ?>radar1.gif')"><img src="<?php echo $imagesDir; ?>radar1.gif" alt="Zoom In" title="Zoom In" width="24" height="28" name="pic1" border="0"/></a>&nbsp;

<a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=1&amp;animated=<?php echo $Animated; ?>&amp;track=<?php echo $Track; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;lightning=<?php echo $Lightning; ?>#WUtop" onmouseover="rollThis('pic2','<?php echo $imagesDir; ?>radar2a.gif')" onmouseout="rollThis('pic2',<?php echo $mode1HL;?>)"><img alt="<?php echo $WUname1; ?>" title="<?php echo $WUname1; ?>" src=<?php echo $mode1HL;?> width="24" height="28" name="pic2" border="0"/></a>&nbsp;

<a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=2&amp;animated=<?php echo $Animated; ?>&amp;track=<?php echo $Track; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;lightning=<?php echo $Lightning; ?>#WUtop" onmouseover="rollThis('pic3','<?php echo $imagesDir; ?>radar3a.gif')" onmouseout="rollThis('pic3',<?php echo $mode2HL;?>)"><img src=<?php echo $mode2HL;?> alt="<?php echo $WUname2; ?>" title="<?php echo $WUname2; ?>" width="24" height="28" name="pic3" border="0"/></a>



<a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=3&amp;animated=<?php echo $Animated; ?>&amp;track=<?php echo $Track; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;lightning=<?php echo $Lightning; ?>#WUtop" onmouseover="rollThis('pic4','<?php echo $imagesDir; ?>radar4a.gif')" onmouseout="rollThis('pic4',<?php echo $mode3HL;?>)"><img alt="<?php echo $WUname3; ?>" title="<?php echo $WUname3; ?>" src=<?php echo $mode3HL;?> width="24" height="28" name="pic4" border="0"/></a>



<a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=4&amp;animated=<?php echo $Animated; ?>&amp;track=<?php echo $Track; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;lightning=<?php echo $Lightning; ?>#WUtop" onmouseover="rollThis('pic5','<?php echo $imagesDir; ?>radar5a.gif')" onmouseout="rollThis('pic5',<?php echo $mode4HL;?>)"><img alt="<?php echo $WUname4; ?>" title="<?php echo $WUname4; ?>" src=<?php echo $mode4HL;?> width="24" height="28" name="pic5" border="0"/></a>

<a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=5&amp;animated=<?php echo $Animated; ?>&amp;track=<?php echo $Track; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;lightning=<?php echo $Lightning; ?>#WUtop" onmouseover="rollThis('pic6','<?php echo $imagesDir; ?>radar6a.gif')" onmouseout="rollThis('pic6',<?php echo $mode5HL;?>)"><img alt="<?php echo $WUname5; ?>" title="<?php echo $WUname5; ?>" src=<?php echo $mode5HL;?> width="24" height="28" name="pic6" border="0"/></a>

<a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=<?php echo $ModePlus; ?>&amp;animated=<?php echo $Animated; ?>&amp;track=<?php echo $Track; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;lightning=<?php echo $Lightning; ?>#WUtop" onmouseover="rollThis('pic7','<?php echo $imagesDir; ?>radar7a.gif')" onmouseout="rollThis('pic7','<?php echo $imagesDir; ?>radar7.gif')"><img alt="Zoom Out" title="Zoom Out" src="<?php echo $imagesDir; ?>radar7.gif" width="29" height="28" name="pic7" border="0"/></a>


<a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=<?php echo $Mode; ?>&amp;animated=<?php echo $AnOption; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>&amp;lightning=<?php echo $Lightning; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $animcolor;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $animcolor;?>'">Animated: <?php echo $AnNotice; ?></span></a>&nbsp;&nbsp;

<a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=<?php echo $Mode; ?>&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $AdOption; ?>&amp;track=<?php echo $Track; ?>&amp;lightning=<?php echo $Lightning; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $advcolor;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $advcolor;?>'">Advisories: <?php echo $AdNotice; ?></span></a>&nbsp;&nbsp;

<a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=<?php echo $Mode; ?>&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $TrOption; ?>&amp;lightning=<?php echo $Lightning; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $trackcolor;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $trackcolor;?>'">Track: <?php echo $TrNotice; ?></span></a>&nbsp;&nbsp;

<a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=<?php echo $Mode; ?>&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>&amp;lightning=<?php echo $LightOption; ?>" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $lightcolor;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $lightcolor;?>'">Lightning: <?php echo $LightNotice; ?></span></a><br />
<?php
  if (isset($_REQUEST['show']) and strtolower($_REQUEST['show']) == 'loc') {
 ?>
<!-- code for setup of city location -->

<form name="Show" action="getNada();">

<?php
}

if ($Mode == "1") {
//-------------------------------------------------------------------------------
// Code for radar map 1 (Close Zoom)
//-------------------------------------------------------------------------------
$MapScale = 1;
$CityParms = getParmsFromURL($CityURL);
echo "<!-- CityParms='$CityParms' -->\n";
?>

<span style="position: relative; left: 0px; top: 0px; border: none;"><img id="WUimage" src="http://radblast-aa.wunderground.com/cgi-bin/radar/WUNIDS_map?station=<?php echo $RDR; ?>&amp;brand=wui<?php echo $AnText; ?>&amp;type=N0R&amp;frame=0&amp;noclutter=0&amp;t=<?php echo $UTCtime; ?>&amp;lat=<?php echo $Lat; ?>&amp;lon=<?php echo $Long; ?>&amp;label=<?php echo urlencode($City); ?><?php echo $TrText; ?>&amp;map.x=400&amp;map.y=240<?php echo $CityParms; ?>&amp;showlabels=1<?php echo $AdText; ?>&amp;rainsnow=1<?php echo $LightText; ?>" width="640" height="480" alt="" /></span>
<span id="cityloc" style="position: relative; <?php echo $CityPos1; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor1; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span><?php echo $AdKey; ?>

<?php
}

if ($Mode == "2") {
//-------------------------------------------------------------------------------
// Code for radar map 2 (Medium Zoom)  Done!!
//-------------------------------------------------------------------------------
$MapScale = 2;
$MetroParms = getParmsFromURL($MetroURL);
echo "<!-- MetroParms='$MetroParms' -->\n";
?>

<span style="position: relative; left: 0px; top: 0px; border: none;"><img id="WUimage" src="http://radblast-aa.wunderground.com/cgi-bin/radar/WUNIDS_map?station=<?php echo $RDR; ?>&amp;brand=wui<?php echo $AnText; ?>&amp;type=N0R&amp;frame=0&amp;noclutter=0&amp;t=<?php echo $UTCtime; ?><?php echo $TrText; ?>&amp;map.x=400&amp;map.y=240<?php echo $MetroParms; ?>&amp;showlabels=1<?php echo $AdText; ?>&amp;rainsnow=1<?php echo $LightText; ?>&amp;lat=<?php echo $Lat; ?>&amp;lon=<?php echo $Long; ?>&amp;label=<?php echo urlencode($City); ?>" width="640" height="480" alt="" /></span>
<span id="cityloc" style="position: relative; <?php echo $CityPos2; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor2; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span><?php echo $AdKey; ?>

<?php
}

if ($Mode == "3") {
//-------------------------------------------------------------------------------
// Code for radar map 3  (No Zoom) done!!
//-------------------------------------------------------------------------------
$MapScale = 3;
?>

<span style="position: relative; left: 0px; top: 0px; border: none;"><img id="WUimage" src="http://radblast-aa.wunderground.com/cgi-bin/radar/WUNIDS_map?station=<?php echo $RDR; ?>&amp;brand=wui<?php echo $AnText; ?>&amp;type=N0R&amp;frame=0&amp;scale=0.999&amp;noclutter=0&amp;t=<?php echo $UTCtime; ?><?php echo $TrText; ?>&amp;map.x=400&amp;map.y=240&amp;centerx=400&amp;centery=240&amp;transx=0&amp;transy=0&amp;showlabels=1<?php echo $AdText; ?>&amp;rainsnow=1<?php echo $LightText; ?>&amp;lat=<?php echo $Lat; ?>&amp;lon=<?php echo $Long; ?>&amp;label=<?php echo urlencode($City); ?>" width="640" height="480" alt="" /></span>
<span id="cityloc" style="position: relative; <?php echo $CityPos3; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor3; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span><?php echo $AdKey; ?>
<?php
}

if ($Mode == "4" && $Advisories == "1") {
//-------------------------------------------------------------------------------
// Code for radar map 4 with advisories selected (Advisories) done!!
//-------------------------------------------------------------------------------
$MapScale = 9;
?>

<span style="position: relative; left: 0px; top: 0px; border: none;"><img id="WUimage" src="http://icons.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_severe.gif?id=<?php echo $UTCtime; ?>" width="640" height="480" alt=" " /></span>
<span id="cityloc" style="position: relative; <?php echo $CityPos9; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor9; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span>
<img src="<?php echo $imagesDir; ?>severeKey.gif" alt="Severe weather key" height="64" width="640" /> 

<?php
}

if ($Mode == "4" && $Animated == "0" && $Advisories == "0") {
//-------------------------------------------------------------------------------
// Code for radar map 4 with no animation or advisories (regional map) done!!!
//-------------------------------------------------------------------------------
$MapScale = 5;
?>

<span style="position: relative; left: 0px; top: 0px; border: none;"><img id="WUimage" src="http://icons.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_rd.gif?id=<?php echo $UTCtime; ?>" width="640" height="480"  alt="" /></span>
<span id="cityloc" style="position: relative; <?php echo $CityPos5; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor5; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "4" && $Animated == "1" && $Advisories == "0") {
//-------------------------------------------------------------------------------
// Code for radar map 4 with animation but no advisories (Animated Regional) DONE!! 223 -270
//-------------------------------------------------------------------------------
$MapScale = 5;
?>

<span style="position: relative; left: 0px; top: 0px; border: none;"><img id="WUimage" src="http://icons.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_rd_anim.gif?id=<?php echo $UTCtime; ?>" width="640" height="480"  alt=""/></span>
<span id="cityloc" style="position: relative; <?php echo $CityPos5; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor5; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "5" && $Advisories == "1") {
//-------------------------------------------------------------------------------
// Code for radar map 5 with advisories selected 
//-------------------------------------------------------------------------------
$MapScale = 4;
?>

<span style="position: relative; left: 0px; top: 0px; border: none;"><img id="WUimage" src="http://maps.wunderground.com/data/severe/current_severe_nostatefarm.gif?id=<?php echo $UTCtime; ?>" width="640" height="480" alt="" /></span>
<span id="cityloc" style="position: relative; <?php echo $CityPos4; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor4; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "5" && $Animated == "0" && $Advisories == "0") {
//-------------------------------------------------------------------------------
// Code for radar map 5 with no animation or advisories (National) done!!
//-------------------------------------------------------------------------------
$MapScale = 6;
?>

<span style="position: relative; left: 0px; top: 0px; border: none;"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2xus_rd.gif?id=<?php echo $UTCtime; ?>" width="640" height="480" alt="" /></span>
<span id="cityloc" style="position: relative; <?php echo $CityPos6; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor6; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "5" && $Animated == "1" && $Advisories == "0") {
//-------------------------------------------------------------------------------
// Code for radar map 4 with animation but no advisories (National Animated) done!!
//-------------------------------------------------------------------------------
$MapScale = 6;
?>

<span style="position: relative; left: 0px; top: 0px; border: none;"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2xus_rd_anim.gif?id=<?php echo $UTCtime; ?>" width="640" height="480" alt="" /></span>
<span id="cityloc" style="position: relative; <?php echo $CityPos6; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor6; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span>

<?php
}

if ($Mode == "6" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Fronts
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px; border: none;"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_sf.gif?id=<?php echo $UTCtime; ?>" width="640" height="480" alt="" /></span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "6" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Fronts Animated
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px; border: none;"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_sf_anim.gif?id=<?php echo $UTCtime; ?>" width="640" height="480" alt="" /></span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "7" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Jet Stream
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_jt.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 
<?php
}

if ($Mode == "7" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Jet Stream
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_jt_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "8" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Vis Satellite
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_vi.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "8" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Vis Satellite
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_vi_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "9" && $Animated == "0") {
//-------------------------------------------------------------------------------
// IR Satellite done!!
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_ir.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "9" && $Animated == "1") {
//-------------------------------------------------------------------------------
// IR Satellite animated done!!
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_ir_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 


<?php
}

if ($Mode == "10" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Wind done!!
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_ws.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "10" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Wind animated done!!
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_ws_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "11" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Temperatures
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_st.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "11" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Temperatures animated
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_st_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "12" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Humidity
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_rh.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "12" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Humidity Animated
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_rh_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "13" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Dew Point
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_dp.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "13" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Dew Point Animated
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_dp_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "14" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Heat Index
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_hi.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "14" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Heat Index
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_hi_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "15" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Wind Chill
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_wc.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "15" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Wind Chill
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_wc_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}
if ($Mode == "16" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Snow done!!
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_snow.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "16" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Snow animated done!!
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2x<?php echo $WUregion; ?>_snow_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "17" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Visibility
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://icons.wunderground.com/data/640x480/<?php echo $WUregion; ?>_vs.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "17" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Visibility Animated
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://icons.wunderground.com/data/640x480/<?php echo $WUregion; ?>_vs_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "18" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Air Quality
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://icons.wunderground.com/data/640x480/<?php echo $WUregion; ?>_ozone.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "18" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Air Quality Animated
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://icons.wunderground.com/data/640x480/<?php echo $WUregion; ?>_ozone_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}
if ($Mode == "19" && $Animated == "0") {
//-------------------------------------------------------------------------------
// UV
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://icons.wunderground.com/data/640x480/<?php echo $WUregion; ?>_uv.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "19" && $Animated == "1") {
//-------------------------------------------------------------------------------
// UV Animated
//-------------------------------------------------------------------------------
$MapScale = 7;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://icons.wunderground.com/data/640x480/<?php echo $WUregion; ?>_uv_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos7; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor7; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "20" && $Animated == "0") {
//-------------------------------------------------------------------------------
// Flue done!!
//-------------------------------------------------------------------------------
$MapScale = 8;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2xus_flu.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos8; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor8; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php
}

if ($Mode == "20" && $Animated == "1") {
//-------------------------------------------------------------------------------
// Flue animated done!!
//-------------------------------------------------------------------------------
$MapScale = 8;
?>

<span style="position: relative; left: 0px; top: 0px"><img id="WUimage" src="http://maps.wunderground.com/data/640x480/2xus_flu_anim.gif?id=<?php echo($UTCtime); ?>" width="640" height="480"  border="0" alt="WU Map"/> </span>
<span id="cityloc" style="position: relative; <?php echo $CityPos8; ?> font-size: 10pt; color:#FF0000">&bull;&nbsp;<span style="color:<?php echo $CityColor8; ?>; font-size: 9pt;"><b><?php echo $City; ?></b></span></span> 

<?php

}

//-------------------------------------------------------------------------------
// End of all pages
//-------------------------------------------------------------------------------
?>
<?php
  if (isset($_REQUEST['show']) and strtolower($_REQUEST['show']) == 'loc') {
 ?>
<!-- setting location code follows -->
<div style="border: 1px dashed #0000FF; padding: 5px; background-color:#FFFF99; color:#000000; font-family:Arial, Helvetica, sans-serif; font-size: 10pt;">
Check position of city legend red dot {<span style="color:red; font-size:11pt;"><b>&bull;</b></span>)with these links:<br/><span style="font-size: 9pt;">
<a href="?show=loc&amp;mode=1&amp;advisories=0#WUtop"><b>$CityPos1</b></a> | 
<a href="?show=loc&amp;mode=2&amp;advisories=0#WUtop"><b>$CityPos2</b></a> | 
<a href="?show=loc&amp;mode=3&amp;advisories=0#WUtop"><b>$CityPos3</b></a> | 
<a href="?show=loc&amp;mode=5&amp;advisories=1#WUtop"><b>$CityPos4</b></a> | 
<a href="?show=loc&amp;mode=4&amp;advisories=0#WUtop"><b>$CityPos5</b></a> | 
<a href="?show=loc&amp;mode=5&amp;advisories=0#WUtop"><b>$CityPos6</b></a> | 
<a href="?show=loc&amp;mode=6&amp;advisories=0#WUtop"><b>$CityPos7</b></a> | 
<a href="?show=loc&amp;mode=20&amp;advisories=0#WUtop"><b>$CityPos8</b></a> |  
<a href="?show=loc&amp;mode=4&amp;advisories=1#WUtop"><b>$CityPos9</b></a></span>  
<br/>
Click on radar image where city legend red dot {<span style="color:red; font-size:11pt;"><b>&bull;</b></span>) should appear to see offset and copy code to use in the script settings.  When clicked, the legend will move to the designated spot over the map image.
<br/>
Copy PHP code for $CityPos<?php echo $MapScale;?> is <input type="text" name="CopyCode" value="" size="45"/><br/>
Select the text, copy to clipboard and paste into top of script to replace the existing $CityPos<?php echo $MapScale; ?> line.
<br/>&nbsp;<br/>
Mouse cursor at:  Left <input type="text" name="MouseX" value="0" size="4"/> 
Top <input type="text" name="MouseY" value="0" size="4"/>
, Computed offset to use:
Left <input type="text" name="OffsetLeft" value="0" size="4"/>
Top <input type="text" name="OffsetTop" value="0" size="4"/>
</div>
</form>

<script type="text/javascript">
// <![CDATA[
<!-- Original:  CodeLifter.com (support@codelifter.com) -->
<!-- Web Site:  http://www.codelifter.com -->

<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->

var IE = document.all?true:false;
//if (!IE) {document.captureEvents(Event.MOUSEMOVE); }
var SAF=navigator.userAgent.indexOf('Safari')!=-1;
var Opera=navigator.userAgent.indexOf('Opera')!=-1;
if (SAF) {IE=false;}
//if (SAF) {alert('Safari Detected -- sorry, this script works only in IE, Firefox or Opera browsers.'); }
document.onmousemove = getMouseXY;
var doDebug = false;
// debugging variables
var cX = 0;
var cY = 0;
var wX = 0;
var wY = 0;
var eX = 0;
var eY = 0;
//-------------------
var tempX = 0;
var tempY = 0;
var WUimageH = 0;
var WUimageW = 0;
var WUimageT = 0;
var WUimageL = 0;
var OffsetT = 0;
var OffsetL = 0;
var CityLocL = 0;
var CityLocT = 0;
var CityLocOffY = -8; // for display of city name (FF)
var CityLocOffX = -2; // fudge factors to position bullet point correctly
if (IE) {
 CityLocOffY = -61; // for display of city name (IE)
 CityLocOffX = -4; // fudge factors to position bullet point correctly
}
if (SAF) {
 CityLocOffY = 459; // for display of city name (Safari Win)
 CityLocOffX = -1; // fudge factors to position bullet point correctly
}
if (Opera) {
 CityLocOffY = -8; // for display of city name (Opera)
 CityLocOffX = -1; // fudge factors to position bullet point correctly
}
var temparr = new Array();

var element = document.getElementById("WUimage");
if (element) { // get size of WUimage and left, top coordinates
  WUimageH = element.height;
  WUimageW = element.width;
  if(IE && ! WUimageH && ! WUimageW) {
    WUimageH = 480;
	WUimageW = 640;
  }
  temparr = findPos(element);
  WUimageL = temparr[0];
  WUimageT = temparr[1];
  element.onclick = captureOffset;
  element.style.cursor = "crosshair";
}

element = document.getElementById("cityloc");
if (element) { // find left, top position of current cityloc span (lable for city)
  temparr = findPos(element);
  CityLocL  = temparr[0];
  CityLocT  = temparr[1];
  element.onclick = captureOffset;
  element.style.cursor = "crosshair";
}

function captureOffset() { // runs to reset the left, top relative location of cityloc span
  var lval = (tempX - WUimageL + CityLocOffX) + 'px';
  var tval =  - (WUimageT + WUimageH - tempY - CityLocOffY) + 'px';
  document.Show.OffsetLeft.value = lval;
  document.Show.OffsetTop.value = tval;
  document.Show.CopyCode.value = '$CityPos<?php echo $MapScale; ?>  =  \'left: ' + lval + '; top: ' + tval + ';\';';
  element = document.getElementById("cityloc");
  if (element) {
   element.style.left = lval;
   element.style.top  = tval;
  }
  if(doDebug) {  
    var msg='tempX='+tempX+' tempY=' + tempY +
	 "\nWUimageL="+WUimageL+" WUimageT="+WUimageT+" WUimageH="+WUimageH+" WUimageW="+WUimageW+
	 "\nCityLocL="+CityLocL+" CityLocT="+CityLocT+
	 "\nCityLocOffX="+CityLocOffX+" CityLocOffY="+CityLocOffY+
	  "\n IE="+IE+" SAF="+SAF+ " Opera="+Opera +
	  "\n cX="+cX+" cY="+cY+" wX="+wX+" wY="+wY+" eX="+eX+" eY="+eY;
    	alert(msg); // for debugging
  }
 
  return;
}

function findPos(obj) {
	var curleft = 0;
	var curtop = 0;
	while (obj) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
			obj = obj.offsetParent;
	}
	return [curleft,curtop];
}

function getMouseXY(e) {
if (window.event) { // grab the x-y pos.s if browser is IE
  cX = window.event.clientX;
  cY = window.event.clientY;
  wX = document.body.scrollLeft;
  wY = document.body.scrollTop;
  eX = document.documentElement.scrollLeft;
  eY = document.documentElement.scrollTop;
  tempX = window.event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
  if (document.body.scrollLeft == document.documentElement.scrollLeft) { // Opera does this
    tempX -= document.body.scrollLeft;
  }
  tempY = window.event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
  if (document.body.scrollTop == document.documentElement.scrollTop) { // Opera does this
    tempY -= document.body.scrollTop;
  }
}
else {  // grab the x-y pos.s if browser is NS
  tempX = e.pageX;
  tempY = e.pageY;
}  
if (tempX < 0){tempX = 0;}
if (tempY < 0){tempY = 0;} 
 
document.Show.MouseX.value = tempX;
document.Show.MouseY.value = tempY;
return true;
}

function getNada() {
// stub function for form action= statement
 return true;
}
//  End script
// ]]>
</script>
<!-- end of setting location code -->
<?php } ?>

 </td>
</tr>
<tr>
  <td style="background-color: #000000">
<div align="center"><span style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size:14px; color:#FFFFFF">&nbsp;&nbsp;<b>Additional Weather Maps</b>&nbsp;&nbsp;&nbsp;
          <br/></span>
    
          <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=6&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none">
		  <span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode6HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode6HL;?>'">Fronts</span></a>&nbsp;&nbsp;&nbsp; 
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=7&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none">
		  <span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode7HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode7HL;?>'">Jet Stream</span></a>&nbsp;&nbsp;&nbsp; 
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=8&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none">
		  <span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode8HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode8HL;?>'">Vis Sat</span></a>&nbsp;&nbsp;&nbsp; 
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=9&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode9HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode9HL;?>'">IR Sat</span></a>&nbsp;&nbsp;&nbsp; 
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=10&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode10HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode10HL;?>'">Wind</span></a>&nbsp;&nbsp;&nbsp; 
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=11&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode11HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode11HL;?>'">Temperatures</span></a>&nbsp;&nbsp;&nbsp; 
    
          <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=12&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode12HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode12HL;?>'">Humidity</span></a>&nbsp;&nbsp;&nbsp; 
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=13&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode13HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode13HL;?>'">Dew Point</span></a>&nbsp;&nbsp;&nbsp; <br/>
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=14&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode14HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode14HL;?>'">Heat Index</span></a>&nbsp;&nbsp;&nbsp; 
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=15&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode15HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode15HL;?>'">Wind Chill</span></a>&nbsp;&nbsp;&nbsp; 
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=16&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode16HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode16HL;?>'">Snow Depth</span></a>&nbsp;&nbsp;&nbsp; 

		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=17&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode17HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode17HL;?>'">Visibility</span></a>&nbsp;&nbsp;&nbsp; 
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=18&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode18HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode18HL;?>'">Air Quality</span></a>&nbsp;&nbsp;&nbsp; 
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=19&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode19HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode19HL;?>'">UV</span></a>&nbsp;&nbsp;&nbsp; 
		  
		  <a href="<?php echo $PHP_SELF; ?>?<?php echo $doShow; ?>mode=20&amp;animated=<?php echo $Animated; ?>&amp;advisories=<?php echo $Advisories; ?>&amp;track=<?php echo $Track; ?>#WUtop" style="text-decoration:none"><span style="font-size: 12px; font-family: Arial, Helvetica, sans-serif; color:<?php echo $mode20HL;?>" onmouseover="this.style.color = '#FFFF00'" onmouseout="this.style.color = '<?php echo $mode20HL;?>'">Flu</span></a>&nbsp;&nbsp;&nbsp; <br/>
      &nbsp;
</div>
  </td>
 </tr>
 <tr>
 <td align="center" style="font-family:Arial, Helvetica, sans-serif; font-size: 12px;color:#FFFFFF;background-color:#000000">
Radar and map images courtesy of <a href="http://www.weatherunderground.com/" style="color:#FFFF00">Weather Underground</a>.<br/>
<br/>
<span style="font-size:x-small;">Thanks to Tom at <a href="http://carterlake.org/" style="color:#FFFF00"><b>Carter Lake</b></a>,
Jim at <a href="http://jcweather.us/" style="color:#FFFF00">Juneau County Weather</a> and Ken at 
<a href="http://saratoga-weather.org/" style="color:#FFFF00"><b>Saratoga-Weather</b></a> for the display script for this page.</span>
 </td>
 </tr>
</table>
<!-- end of WU-radar-inc.php -->
<?php
 function getParmsFromURL ( $URL) {
 
   $URLparts = parse_url($URL);
   parse_str($URLparts['query'],$query);
//   print "<!-- query\n" . print_r($query,true) . " -->\n";
   $coords = '&amp;scale=' . $query['scale'] .
             '&amp;centerx=' . $query['centerx'] .
            '&amp;centery=' . $query['centery'] .
			'&amp;transx=' . $query['transx'] .
			'&amp;transy=' . $query['transy'] ;
//   print "<!-- coords='$coords' -->\n";
   return ($coords);
 }
 ?>
