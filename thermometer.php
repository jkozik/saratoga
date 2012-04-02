<?php
// Thermometer image script
// Ken True - webmaster@Saratoga-weather.org
// error_reporting(E_ALL); // uncomment to run testing
// Version 1.00 - 17-Sep-2007 - Initial release
// Version 1.01 - 18-Nov-2007 - added GD test, autoscale function, updated for sce=view on IIS systems
// Version 1.02 - 23-Nov-2007 - fixed Notice: errata on min/max calculations
// Version 1.03 - 28-Feb-2008 - added support for Carterlake/AJAX/PHP template integration
// Version 1.04 - 02-Feb-2009 - added support for 'dark' template (white text instead of black)
// Version 1.05 - 21-Oct-2009 - added support for Cumulus realtime.txt
// Version 1.06 - 12-Jan-2011 - added support for new universal templates.
// Version 1.07 - 02-Apr-2011 - fixed WLrealtime offsets for WeatherLink
// Version 1.08 - 29-Jul-2011 - added support for Meteohub clientraw.txt
// Version 1.09 - 14-Jan-2012 - added support for WeatherSnoop via WSNtags/WSN-defs
// Version 1.10 - 24-Mar-2012 - added support for WeatherCat via WCTtags/WCT-defs or WCT_realtime.txt
//
// script available at http://saratoga-weather.org/scripts.php
//  
// you may copy/modify/use this script as you see fit,
// no warranty is expressed or implied.

// This script reads WD clientraw.txt , VWS wflash.txt/wflash2.txt, or Cumulus realtime.txt 
// gets the current, high, low temperature and draws a
// thermometer image (filled area + scale) on a transparent-background thermometer graphic 
// PNG.
//  usage on your page:
//
//  <img src="thermometer.php" height="170" width="54" alt="current temperature, daily low/high" />
//
// New with version 1.01 - $autoScale = true; enables autoranging on output scale.  The program will
// add majortick values to maximum or substract majortick values from minimum to always show the
// scale, current, minimum, maximum on the graphic.
//------------ settings ------------------
$wxSoftware         = 'WD';                           // 'WD' for Weather-Display, 'VWS' for Virtual Weather Station,
                                                      // 'CU' for Cumulus, 'WL' for WeatherLink, 'WXS' for WXSolution
//
$UOM = 'F';                                           // set to 'C' for Celsius/Centigrade, 'F'=Fahrenheit
//
$autoScale = true;                                    // set to false to disable autoscale.
//
// you only have to set one of these correctly based on the $useWD selection
// $wxSoftware = 'WD' : set the $clientrawfile
// $wxSoftware = 'VWS': set the $wflashDir
// $wxSoftware = 'CU': set the $realtimefile
//
$clientrawfile = './clientraw.txt';                // relative file address for WD clientraw.txt
$wflashDir     = '../WxFlash/Data/';                 // directory for the the VWS wflash.txt and wflash2.txt files
//                                                 // relative to directory location of this script (include
//                                                 // trailing '/' in the specification
$realtimefile  = './realtime.txt';                 // relative file location for Cumulus realtime.txt file

//$WLrealtime    = './WLrealtime.txt';               // relative file location of WeatherLink WLrealtime.txt file
//
// settings for ranges -- adjust for your climate :-)
// Fahrenheit settings
$TmaxF = 105;     // maximum °F temperature on thermometer
$TminF = 25;      // minimum °F temperature on thermometer
$TincrF = 5;      // increment number of degrees °F for major/minor ticks on thermometer
$TMajTickF = 10;  // major tick with value when °F scale number divisible by this
// Centigrade settings
$TmaxC = 40;      // maximum °C temperature on thermometer
$TminC = -10;     // minimum °C temperature on thermometer
$TincrC = 2;      // increment number of degrees °C for major/minor ticks on thermometer
$TMajTickC = 10;  // major tick with value when °C scale number divisible by this
//
$invertColor = false; // set to true if thermometer display is over black background
$BlankGraphic  = './thermometer-blank.png'; // relative file address for thermometer blank image PNG
$BlankGraphicBlack = './thermometer-blank-black.png'; // for black background use
//------------ end settings --------------
// overrides from Settings.php if available
if(file_exists("Settings.php")) { include_once("Settings.php"); }
global $SITE;
if (isset($SITE['uomTemp'])) 	{
  $UOM = preg_replace('|&deg;|is','',$SITE['uomTemp']);
  if ($UOM <> 'F' and $UOM <> 'C') { $UOM = 'F'; }
}
if (isset($SITE['clientrawfile']) ) {$clientrawfile = $SITE['clientrawfile']; }
if (isset($SITE['wflashdir']) ) {$wflashDir = $SITE['wflashdir']; }
if (isset($SITE['realtimefile']) ) {$realtimefile = $SITE['realtimefile']; }
if (isset($SITE['WLrealtime']) ) {$WLrealtime = $SITE['WLrealtime']; }
if (isset($SITE['WCTrealtime']) ) {$WCTrealtime = $SITE['WCTrealtime']; }
if (isset($SITE['WXsoftware']) ) {$wxSoftware = $SITE['WXsoftware']; }

$CSSstyle = '';
# was there a style selected from the form input
if (isset($_COOKIE['CSSstyle'])) {
       $_SESSION['CSSstyle'] = $_COOKIE['CSSstyle'];
       $CSSstyle = $_COOKIE['CSSstyle'];
  } else if (isset($_SESSION['CSSstyle']) and $_SESSION['CSSstyle'] <> '' ) {
       $CSSstyle = $_SESSION['CSSstyle'];
  }
if (preg_match('|black|i',$CSSstyle) ) {
	$invertColor = true; 
}
// end of overrides from Settings.php

// -------------------begin code ------------------------------------------
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

if( ! function_exists("gd_info")){
  die("Sorry.. this script requires the GD library in PHP to function.");
}


if (isset($_REQUEST['wx']) and strtolower($_REQUEST['wx']) == 'wd') { $wxSoftware = 'WD'; } // testing
if (isset($_REQUEST['wx']) and strtolower($_REQUEST['wx']) == 'vws') { $wxSoftware = 'VWS'; } // testing
if (isset($_REQUEST['wx']) and strtolower($_REQUEST['wx']) == 'cu') { $wxSoftware = 'CU'; } // testing
if (isset($_REQUEST['wx']) and strtolower($_REQUEST['wx']) == 'wl') { $wxSoftware = 'WL'; } // testing
if (isset($_REQUEST['wx']) and strtolower($_REQUEST['wx']) == 'mh') { $wxSoftware = 'MH'; } // testing
if (isset($_REQUEST['wx']) and strtolower($_REQUEST['wx']) == 'wsn') { $wxSoftware = 'WSN'; } // testing
if (isset($_REQUEST['wx']) and strtolower($_REQUEST['wx']) == 'wct') { $wxSoftware = 'WCT'; } // testing

if (isset($_REQUEST['uom']) and strtolower($_REQUEST['uom']) == 'c') { $UOM = 'C'; }
if (isset($_REQUEST['uom']) and strtolower($_REQUEST['uom']) == 'f') { $UOM = 'F'; }
if (isset($_REQUEST['dark'])) {$invertColor = strtolower($_REQUEST['dark'])=='y'; }

if ($wxSoftware == 'WD' or $wxSoftware == 'MH') { // Get the Weather-Display/Meteohub clientraw.txt data file

      $dataraw = file_get_contents($clientrawfile);

      // clean up any blank lines
      $dataraw = trim($dataraw);
      $dataraw = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/","\n",$dataraw);
      $data = explode(" ", $dataraw);
	  
	  $curtemp = CtoF($data[4],1);
	  $mintemp = CtoF($data[47],1);
	  $maxtemp = CtoF($data[46],1);
} // end Weather Display data
  
if ($wxSoftware == 'VWS') { // Get the VWS Weather Flash data files
  
      $filename = "${wflashDir}wflash.txt";
      $file = file($filename);
      $file = implode('',$file);
      $data = explode(",",$file);;
  
	  $curtemp = FtoC($data[9],1);
	  
      $filename = "${wflashDir}wflash2.txt";
      $file = file($filename);
      $file = implode('',$file);
      $data = explode(",",$file);;
	  
	  $mintemp = FtoC($data[92],1);
	  $maxtemp = FtoC($data[36],1);
}

if ($wxSoftware == 'CU') { // Get the Cumulus realtime.txt file
  
      $dataraw = file_get_contents($realtimefile);

      // clean up any blank lines
      $dataraw = trim($dataraw);
      $dataraw = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/","\n",$dataraw);
      $data = explode(" ", $dataraw);
	  $inUOM = $data[14];
      if ($inUOM == 'F' and $UOM == 'C') {
		$curtemp = FtoC($data[2],1);
		$mintemp = FtoC($data[28],1);
		$maxtemp = FtoC($data[26],1);
	  } elseif ($inUOM == 'C' and $UOM == 'F') {
		$curtemp = CtoF($data[2],1);
		$mintemp = CtoF($data[28],1);
		$maxtemp = CtoF($data[26],1);
	  } else {
		$curtemp = $data[2];
		$mintemp = $data[28];
		$maxtemp = $data[26];
		$UOM = $inUOM;
	  }
}

if ($wxSoftware == 'WL') { // Process WeatherLink
      if(!isset($WLrealtime)) { // get from WLtags.php
	     if (file_exists($SITE['WXtags'])) {global $WX; include_once($SITE['WXtags']); }
	     $inUOM = 'F';
		 $curtemp = 70;
		 $mintemp = 60;
		 $maxtemp = 80;
		 
		 if(preg_match("|C|i",$WX['tempUnit'])) {
		    $inUOM = 'C';
		 } else {
			$inUOM = 'F';
		 }
		if ($inUOM == 'F' and $UOM == 'C') {
		  $curtemp = FtoC($WX['outsideTemp'],1);
		  $mintemp = FtoC($WX['lowOutsideTemp'],1);
		  $maxtemp = FtoC($WX['hiOutsideTemp'],1);
		} elseif ($inUOM == 'C' and $UOM == 'F') {
		  $curtemp = CtoF($WX['outsideTemp'],1);
		  $mintemp = CtoF($WX['lowOutsideTemp'],1);
		  $maxtemp = CtoF($WX['hiOutsideTemp'],1);
		} elseif (isset($WX['outsideTemp'])) {
		  $curtemp = $WX['outsideTemp'];
		  $mintemp = $WX['lowOutsideTemp'];
		  $maxtemp = $WX['hiOutsideTemp'];
		  $UOM = $inUOM;
		}
		

	} else { // fetch it from the $WLrealtime

	 $dataraw = file_get_contents($WLrealtime);
	
	 // clean up any blank lines
	 $dataraw = trim($dataraw);
	 $dataraw = preg_replace("/[\r\n]+/",'',$dataraw);
	 $data = explode("|", $dataraw);
	 if(preg_match("/C/i",$data[14])) {
		 $inUOM = 'C';
	 } else {
		 $inUOM = 'F';
	 }
	 if ($inUOM == 'F' and $UOM == 'C') {
			$curtemp = FtoC($data[2],1);
			$mintemp = FtoC($data[28],1);
			$maxtemp = FtoC($data[26],1);
	 } elseif ($inUOM == 'C' and $UOM == 'F') {
			$curtemp = CtoF($data[2],1);
			$mintemp = CtoF($data[28],1);
			$maxtemp = CtoF($data[26],1);
	 } else {
			$curtemp = $data[2];
			$mintemp = $data[28];
			$maxtemp = $data[26];
			$UOM = $inUOM;
	 }
	   
	} // end fetch it from $WLrealtime
} // end WeatherLink handling

if ($wxSoftware == 'WCT') { // Process WeatherCat
  if(!isset($WCTrealtime)) { // get from WCTtags.php

	   if (file_exists($SITE['WXtags'])) {
		   global $WX;
		   include_once($SITE['WXtags']);
	   }
	   $inUOM = 'F';
	   $curtemp = 70;
	   $mintemp = 60;
	   $maxtemp = 80;
	   
	   if(preg_match("|C|i",$WX['TEMPUNITS'])) {
		  $inUOM = 'C';
	   } else {
		  $inUOM = 'F';
	   }
	  if ($inUOM == 'F' and $UOM == 'C') {
		$curtemp = FtoC($WX['STAT:TEMPERATURE:CURRENT'],1);
		$mintemp = FtoC($WX['STAT:TEMPERATURE:MIN:TODAY'],1);
		$maxtemp = FtoC($WX['STAT:TEMPERATURE:MAX:TODAY'],1);
	  } elseif ($inUOM == 'C' and $UOM == 'F') {
		$curtemp = CtoF($WX['STAT:TEMPERATURE:CURRENT'],1);
		$mintemp = CtoF($WX['STAT:TEMPERATURE:MIN:TODAY'],1);
		$maxtemp = CtoF($WX['STAT:TEMPERATURE:MAX:TODAY'],1);
	  } elseif (isset($WX['STAT:TEMPERATURE:CURRENT'])) {
		$curtemp = $WX['STAT:TEMPERATURE:CURRENT'];
		$mintemp = $WX['STAT:TEMPERATURE:MIN:TODAY'];
		$maxtemp = $WX['STAT:TEMPERATURE:MAX:TODAY'];
		$UOM = $inUOM;
	  }
  } else { // get from WCT_realtime.txt
	 $dataraw = file_get_contents($WCTrealtime);
	
	 // clean up any blank lines
	 $dataraw = trim($dataraw);
	 $dataraw = preg_replace("/[\r\n]+/",'',$dataraw);
	 $data = explode("|", $dataraw);
	 if(preg_match("/C/i",$data[14])) {
		 $inUOM = 'C';
	 } else {
		 $inUOM = 'F';
	 }
	 if ($inUOM == 'F' and $UOM == 'C') {
			$curtemp = FtoC($data[2],1);
			$mintemp = FtoC($data[28],1);
			$maxtemp = FtoC($data[26],1);
	 } elseif ($inUOM == 'C' and $UOM == 'F') {
			$curtemp = CtoF($data[2],1);
			$mintemp = CtoF($data[28],1);
			$maxtemp = CtoF($data[26],1);
	 } else {
			$curtemp = $data[2];
			$mintemp = $data[28];
			$maxtemp = $data[26];
			$UOM = $inUOM;
	 }
  
  
  
  } // end get from WCT_realtime.txt

} // end WeatherCat processing


if ($wxSoftware == 'WSN') { // Process WeatherSnoop (tags files only)
	     if (file_exists($SITE['WXtags'])) {
			 global $WX; 
			 include_once($SITE['WXtags']); 
		 }
	     $inUOM = 'F';
		 $curtemp = $temperature;
//		 $mintemp = '-';
//		 $maxtemp = '-';
		 
		 if(preg_match("|C|i",$WX['temperature:outdoor:unit'])) {
		    $inUOM = 'C';
		 } else {
			$inUOM = 'F';
		 }
		if ($inUOM == 'F' and $UOM == 'C') {
		  $curtemp = $WX['temperature:outdoor:C'];
//		  $mintemp = FtoC($WX['lowOutsideTemp'],1);
//		  $maxtemp = FtoC($WX['hiOutsideTemp'],1);
		} elseif ($inUOM == 'C' and $UOM == 'F') {
		  $curtemp = $WX['temperature:outdoor:F'];
//		  $mintemp = CtoF($WX['lowOutsideTemp'],1);
//		  $maxtemp = CtoF($WX['hiOutsideTemp'],1);
		} else {
		  $curtemp = $WX['temperature:outdoor:'.$WX['temperature:outdoor:unit']];
//		  $mintemp = CtoF($WX['lowOutsideTemp'],1);
//		  $maxtemp = CtoF($WX['hiOutsideTemp'],1);
		}
		
//		if($mintemp == '-') {$mintemp = $curtemp;}
//		if($maxtemp == '-') {$maxtemp = $curtemp;}

} // end WeatherSnoop processing

if (isset($_REQUEST['current'])) { $curtemp = $_REQUEST['current']; } // for testing
if (isset($_REQUEST['min'])) { $mintemp = $_REQUEST['min']; } // for testing
if (isset($_REQUEST['max'])) { $maxtemp = $_REQUEST['max']; } // for testing


if ($UOM == 'F') { // use Fahrenheit settings
    $Tmax = $TmaxF;   // maximum temperature on thermometer
    $Tmin = $TminF;    // minimum temperature on thermometer
    $Tincr = $TincrF;    // increment number of degrees for major/minor ticks on thermometer
    $TMajTick = $TMajTickF;// major tick with value when scale number divisible by this
  } else { // use Centigrade settings
    $Tmax = $TmaxC;    // maximum temperature on thermometer
    $Tmin = $TminC;   // minimum temperature on thermometer
    $Tincr = $TincrC;    // increment number of degrees for major/minor ticks on thermometer
    $TMajTick = $TMajTickC;// major tick with value when scale number divisible by this
}

      if($autoScale) { autoscale($curtemp,$mintemp,$maxtemp); }
	  
      genThermometer($curtemp, $mintemp,$maxtemp); // make graphic!
	  
return;
	  
// ----------- functions ----------------------------------------------------------
//
function genThermometer( $current,$min,$max ) {

   global $UOM,$BlankGraphic,$BlankGraphicBlack,$wxSoftware,$invertColor;
   global  $Tmax,$Tmin,$Tincr,$TMajTick;

// draw a filled thermometer with scale, min max on a blank thermometer image
 $BGfile = $invertColor?$BlankGraphicBlack:$BlankGraphic;
 $image = LoadPNG($BGfile);
 
 // settings relative to the thermometer image file defines the drawing area
 // for the thermometer filling
 // these settings are SPECIFICALLY for the thermometer-blank.png image background
 
 $minX = 20; // left
 $maxX = 24; // right
 $minY = 20; // top
 $maxY = 140;// bottom
 

 $width = imagesx($image);
 $height = imagesy($image);
 $font = 1;

 $bg    = imagecolorallocate($image,255,255,255 );
 $tx    = imagecolorallocate($image,0,0,0);
 $blue  = imagecolorallocate($image,0,0,255);
 $red   = imagecolorallocate($image,255,0,0);
 if ($invertColor) {
   $tx    = imagecolorallocate($image,255,255,255);
   $blue  = imagecolorallocate($image,0,192,255);
   $red   = imagecolorallocate($image,255,32,32);
 }
  
 $Trange = $Tmax - $Tmin; // total temperature range
 
 $Tpct = ($current-$Tmin)/($Trange); // percent for current temperature of range
  
 $Y = (1-$Tpct)*($maxY-$minY)+$minY; // upper location for fill
  
// fill the thermometer with a red bar from bottom to $Y 
  imagefilledrectangle( $image,
                 $minX,
                 $Y,
                 $maxX,
                 $maxY,
                 $red );
				 
// Draw tick marks and scale values on right
			 
 for ($T=$Tmin;$T<=$Tmax;$T+=$Tincr) {
   
     $Tpct = ($T-$Tmin)/($Trange);
     $Y = (1-$Tpct)*($maxY-$minY)+$minY;
	 
	 if ($T == 0 or ($T % $TMajTick) == 0) { // Major Tick
	 
	    imagefilledrectangle( $image,
            $maxX+7 ,
            $Y ,
            $maxX+12,
            $Y +1, $tx );

        imagestring($image, $font,
            $maxX + 14,
            $Y - (ImageFontHeight($font)/2),
            sprintf( "%2d", $T),$tx);
	 } else { // Minor tick
     	imagefilledrectangle( $image,
            $maxX+7,
            $Y ,
            $maxX+9,
            $Y +1, $tx );
	 }

 
 } // end do ticks legend
 
 if(isset($min) and $min <> '-') { // put on minimum temp bar/value
 
//     $Tpct = ($min-$Tmin)/($Trange);
     $Tpct = ( ( (float)$min-$Tmin )/ $Trange );
     $Y = (1-$Tpct)*($maxY-$minY)+$minY;
	 imagefilledrectangle( $image,
            $minX - 18,
            $Y ,
            $minX - 5,
           $Y +1, $blue );
      $tstr = sprintf('%2d',round($min,0));
	  $tsize = strlen($tstr)*imagefontwidth($font+1);
      imagestring($image, $font+1,
            $minX - $tsize - 3 ,
            $Y + 2 ,
            $tstr,$blue);

 }
 
 if(isset($max) and $max <> '-') { // put on maximum temp bar/value
 
     $Tpct = ($max-$Tmin)/($Trange);
     $Y = (1-$Tpct)*($maxY-$minY)+$minY;
	 imagefilledrectangle( $image,
            $minX - 18,
            $Y ,
            $minX - 5,
            $Y +1, $red );
 
      $tstr = sprintf('%2d',round($max,0));
	  $tsize = strlen($tstr)*imagefontwidth($font+1);

      imagestring($image, $font+1,
            $minX - $tsize - 3 ,
            $Y - imagefontheight($font+1),
            $tstr,$red);
 }

 // put legend on top with UOM
 
    $cnt = '°' . $UOM;
    imagestring( $image, $font+2, ($width/2)-((strlen($cnt)/2)*ImageFontWidth($font+2)),
       (10-(ImageFontHeight($font+2) / 2)),
       $cnt, $tx);

// write current temperature on thermometer bulb	
//    $tstr = sprintf('%2d',round($current,0));
//	$tsize = strlen($cnt)*imagefontwidth($font);

//    imagestring($image, $font,
//            ($minX+$maxX)/2 - $tsize/2 -2 ,
//            $maxY+6,
//            $tstr,$bg);

	
//imagestring( $image, $font, ($width/2)-((strlen($wxSoftware)/2)*imagefontwidth($font)),
//   $height-imagefontheight($font),
//	$wxSoftware,$tx);

 // send the image
 header("content-type: image/png");
 imagepng($image);
 imagedestroy($image);

} // end genThermometer

// load PNG image 
function LoadPNG ($imgname) { 
   $im = @imagecreatefrompng ($imgname); /* Attempt to open */ 
   if (!$im) { /* See if it failed */ 
       $im  = imagecreate (150, 30); /* Create a blank image */ 
       $bgc = imagecolorallocate ($im, 255, 255, 255); 
       $tc  = imagecolorallocate ($im, 0, 0, 0); 
       imagefilledrectangle ($im, 0, 0, 150, 30, $bgc); 
       /* Output an errmsg */ 
       imagestring ($im, 1, 5, 5, "Error loading $imgname", $tc); 
   } 
   return $im; 
} 

// CtoF: converts degrees Celcius to degress Farenheight (from Anolecomputing.com)
function CtoF($value, $precision) {
  global $UOM;
  if ($UOM <> 'F') { 
    return round($value,$precision); 
  } else { 
    return round($value = (($value * 9 / 5) + 32),$precision); 
  }
} // end function C_to_F

// FtoC: converts degress Farenheight to degrees Celcius  (from Anolecomputing.com)
function FtoC($value, $precision) {
  global $UOM;
  if ($UOM == 'F') { 
    return round($value,$precision); 
  } else { 
    return round(($value - 32) * (5/9),$precision); 
  }
} // end function F to C

// autoscale function .. adjust scale to fit current conditions if need be.

function autoscale($curtemp,$mintemp,$maxtemp) {

   global  $Tmax,$Tmin,$Tincr,$TMajTick;
   
   $highest = max($curtemp,$Tmax,$maxtemp);
   $lowest = min($curtemp,$Tmin,$mintemp);
   
   
   while ($Tmax < $highest) {
     $Tmax += $TMajTick;
   }
  
   while ($Tmin > $lowest) {
     $Tmin = $Tmin - $TMajTick;
   }
   
   return;


}
?>
