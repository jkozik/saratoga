// <![CDATA[

// Special thanks to: Kevin Reed http://www.tnetweather.com/
// Kevin was the first to decode the realtime in PHP
// Special thanks to: Pinto http://www.joske-online.be/
// Pinto wrote the basic AJAX code for this page!
// Cheerfully borrowed from Tom at CarterLake.org and adapted by
// Ken True - Saratoga-weather.org  21-May-2006
// --- added flash-green on data update functions - Ken True  24-Nov-2006
//
// --- Version 2.00 - 13-Dec-2006 -- Ken True -repackaged AJAX function, added metric/english units
//     also included Mike Challis' counter script to display seconds since last update and error
//     handling for the fetch to fix to fix random error: NS_ERROR_NOT_AVAILABLE
//     Mike's site: http://www.642weather.com/weather/index.php
//     Thanks to FourOhFour on wxforum.net ( http://skigod.us/ ) for replacing all the
//     x.responseText.split(' ')[n] calls with a simple array lookup.. much better speed and
//     for his streamlined version of getUVrange.
// --- Version 2.01 - 17-Dec-2006 -- Corrected cloud height calculation
// --- Version 2.02 - 20-Dec-2006 -- added unescape to set_ajax_obs comparison for lastobs
// --- Version 2.03 - 07-Jan-2006 -- added wind m/s or km/h for metric variables
// --- Version 2.04 - 08-Jan-2006 -- use epoch time for get (thanks to johnnywx on WD forum)
//                                   so a numeric time without HTMLencoded characters is used
// --- Version 2.05a - 30-Jan-2006 -- added new 'anti-NaN' check from johnnywx to make sure full
//                                   realtime.txt is read by looking for
//                                   '12345' at start and '!!' at end of record
// --- Version 2.06 - 24-Jun-2007 -- added '/' as delimiter for currentcond
// --- Version 2.07 - 21-Sep-2007 -- added support for dynamic thermometer.php display refresh
// --- Version 2.08 - 07-Nov-2007 -- added useMPH to force wind display in Miles-per-hour and
//                                   added optional Wind-Rose, optional new current icon display graphics
// --- Version 2.09 - 23-Dec-2007 -- added maxupdates feature, new ajax variables from K. Reed www.tnetweather.com
// --- Version 2.10 - 18-Jan-2008 -- fixed icon=34 for ra1.jpg
// --- Version 2.11 - 21-Feb-2008 -- added icon=35 for windyrain.gif/.jpg
// --- Version 2.12 - 07-Mar-2008 -- added fix for 'flashing icon/thermometer' from Jim at jcweather.us
// --- Version 2.13 - 11-Mar-2008 -- changed Wind-rose defaults to .png type (Carterlake/AJAX/PHP templates)
// --- Version 2.14 - 29-Mar-2008 -- fixed UV words with  color: black; for display on dark/black template (MCHALLIS)
// --- Version 2.15 - 28-Apr-2008 -- added ajaxFixConditions() and translation capability
// --- Version 2.16 - 20-May-2008 -- added headcolorword processing V1.0 from MCHALLIS
// --- Version 2.17 - 25-Jun-2008 -- added gizmo-specific ajax variables
// --- Version 2.18 - 20-Mar-2009 -- added fix for 'green-flash' issue with Internet Explorer 8
// --- Version 2.19 - 03-Jul-2009 -- additional gizmo-specific ajax added, and useHpa variable for pressure
// --- Version 2.20 - 16-Jul-2010 -- reduced JS warning msgs by adding var and new x-browser request method finder
//
// --- Version 3.00 - 26-Jan-2011 -- converted WD AJAX script to use Cumulus realtime.txt - Ken True - saratoga-weather.org
// --- Version 3.01 - 17-Feb-2011 -- added decimal comma option for international display
// --- Version 3.02 - 21-Sep-2011 -- added choices for feelslike temp high (heat-index, humidex, apparenttemp)
// --- Version 3.03 - 22-Jan-2012 -- added fix for dd.mm.yyyy date format
//
// for updates to this script, see http://saratoga-weather.org/wxtemplates/
// announcements of new versions will be on wxforum.net and Twitter @saratogaWXPHP

// -- begin settings --------------------------------------------------------------------------
var flashcolor = '#00CC00'; // color to flash for changed observations RGB
var flashtime  = 2000;       // miliseconds to keep flash color on (2000 = 2 seconds);
var reloadTime = 5000;       // reload AJAX conditions every 5 seconds (= 5000 ms)
var maxupdates = 12;	         // Maxium Number of updates allowed (set to zero for unlimited)
                             // maxupdates * reloadTime / 1000 = number of seconds to update
var realtimeFile = '/realtime.txt'; //  URL location of realtime.txt relative to document root of website
var ajaxLoaderInBody = false; // set to true if you have <body onload="ajaxLoader(..."
var imagedir = './ajax-images';  // place for wind arrows, rising/falling arrows, etc.
var useunits = 'E';         // 'E'=USA(English) or 'M'=Metric
var decimalComma = false;    // =false for '.' as decimal point, =true for ',' (comma) as decimal point
var useFeelslike = 0;        // =0 use HeatIndex, =1 use Humidex, =2 use apparent temperature
var useKnots = false;       // set to true to use wind speed in Knots (otherwise 
							// wind in km/hr for Metric or mph for English will be used.
var useMPS   = false;       // set to true for meters/second for metric wind speeds, false= km/h
var useMPH   = false;       // set to true to force MPH for both English and Metric units
var useFeet  = false;       // set to true to force Feet for height in both English and Metric
var usehPa  = false;       // set to true to force hPa for baro in both English and Metric
var showUnits = true;       //  set to false if no units are to be displayed
var showDateMDY = true;     // set to false to show date as dd/mm/yy
var thermometer = './thermometer.php'; // script for dynamic thermometer PNG image (optional)
// optional settings for the Wind Rose graphic in ajaxwindiconwr as wrName + winddir + wrType
var wrName   = 'wr-';       // first part of the graphic filename (followed by winddir to complete it)
var wrType   = '.png';      // extension of the graphic filename
var wrHeight = '58';        // windrose graphic height=
var wrWidth  = '58';        // windrose graphic width=
var wrCalm   = 'wr-calm.png';  // set to full name of graphic for calm display ('wr-calm.gif')
// -- end of settings -------------------------------------------------------------------------

// -- language settings -- you don't need to customize this area if you are using English -----

var langPauseMsg = 'Updates paused - reload page to start'; // substitute this for ajaxindicator when
                             // maxupdates has been reached and updating paused.

var langMonths = new Array ( "January","February","March","April","May",
			"June","July","August","September","October","November","December");
var langDays = new Array ( "Sun","Mon","Tue","Wed","Thu","Fri","Sat","Sun");	

var langBaroTrend = new Array (
 "Steady", "Rising Slowly", "Rising Rapidly", "Falling Slowly", "Falling Rapidly");

var langUVWords = new Array (
 "None", "Low", "Medium", "High",
 "Very&nbsp;High", /* be sure to include &nbsp; for space */
 "Extreme" );

var langBeaufort = new Array ( /* Beaufort 0 to 12 in array */
 "Calm", "Light air", "Light breeze", "Gentle breeze", "Moderate breeze", "Fresh breeze",
 "Strong breeze", "Near gale", "Gale", "Strong gale", "Storm",
 "Violent storm", "Hurricane"
);

var langWindDir = new Array( /* used for alt and title tags on wind dir arrow and wind direction display */
	"N", "NNE", "NE", "ENE", 
	"E", "ESE", "SE", "SSE", 
	"S", "SSW", "SW", "WSW", 
	"W", "WNW", "NW", "NNW");

var langWindCalm = 'Calm';
var langGustNone = 'None';
var langWindFrom = 'Wind from '; /* used on alt/title tags on wind direction arrow*/

var langBaroRising = 'Rising %s '; /* used for trend arrow alt/title tags .. %s marks where value will be placed */
var langBaroFalling = 'Falling %s ';
var langBaroPerHour = '/hour.'; /* will be assembled as rising/falling + value + uom + perhour text */

var langThermoCurrently = 'Currently: '; /* used on alt/title tags for thermometer */
var langThermoMax     = 'Max: ';
var langThermoMin     = 'Min: ';

var langTempRising = 'Warmer %s '; /* used for trend arrow alt/title tags .. %s marks where value will be placed */
var langTempFalling = 'Colder %s ';
var langTempLastHour = ' than last hour.';

var langTransLookup = new Object;  // storage area for key/value for current conditions translation

var langHeatWords = new Array (
 'Unknown', 'Extreme Heat Danger', 'Heat Danger', 'Extreme Heat Caution', 'Extremely Hot', 'Uncomfortably Hot',
 'Hot', 'Warm', 'Comfortable', 'Cool', 'Cold', 'Uncomfortably Cold', 'Very Cold', 'Extreme Cold' );

// -- end of language settings ----------------------------------------------------------

// --- you don't need to customize the stuff below, the actions are controlled by the 
//  settings above.  

var ie4=document.all;
var browser = navigator.appName;
var ie8 = false;
if (ie4 && /MSIE (\d+\.\d+);/.test(navigator.userAgent)){ //test for MSIE x.x;
 var ieversion=new Number(RegExp.$1) // capture x.x portion and store as a number
 if (ieversion>=8) {
   ie4=false;
   ie8=true;
 }
}
var counterSecs = 0;  // for MCHALLIS counter script from weather-watch.com (adapted by K. True)
var updates = 0;		// update counter for limit by maxupdates
var lastajaxtimeformat = 'unknown'; //used to reset the counter when a real update is done
var doTooltip = 0;   // set to 1 to have ajaxed variable names appear as tooltips (except for graphics)

// handle setup options for units-of-measure and whether to show them at all
// these variables are global
var uomTemp = '&deg;F';
var uomWind = ' mph';
var uomBaro = ' inHg';
var uomRain = ' in';
var uomHeight = ' ft';
var dpBaro = 2;
var dpRain = 2;
var dpWind = 1;
// later updated by the ajax fetch
var rTempUOM = 'F';
var rWindUOM = 'mph';
var rBaroUOM = 'inHg';
var rRainUOM = 'in';
var rHeightUOM = 'ft';

// ------------ functions ------------------
function ajax_set_units( units ) {
  useunits = units;
  if (useunits != 'E') { // set to metric
	uomTemp = '&deg;C';
	uomWind = ' km/h';
	uomBaro = ' hPa';
	uomRain = ' mm';
	uomHeight = ' m';
	dpBaro = 1;
	dpRain = 1;
	dpWind = 1;
  }
  if(useKnots) { uomWind = ' kts'; }
  if(useMPS)   { uomWind = ' m/s'; }
  if(useMPH)   { uomWind = ' mph'; }
  if(useFeet)  { uomHeight = ' ft'; }
  if(usehPa)  { uomBaro = ' hPa'; }
  if (! showUnits) {
	uomTemp = '';
	uomWind = '';
	uomBaro = '';
	uomRain = '';
	uomHeight = '';
  }
}

ajax_set_units(useunits);

// utility functions to navigate the HTML tags in the page
function get_ajax_tags ( ) {
// search all the span tags and return the list with class="ajax" in it
//
  if (ie4 && browser != "Opera" && ! ie8) {
    var elem = document.body.getElementsByTagName('span');
	var lookfor = 'className';
  } else {
    var elem = document.getElementsByTagName('span');
	var lookfor = 'class';
  }
     var arr = new Array();
	 var iarr = 0;
     for(var i = 0; i < elem.length; i++) {
          var att = elem[i].getAttribute(lookfor);
          if(att == 'ajax') {
               arr[iarr] = elem[i];
               iarr++;
          }
     }

	 return arr;

}

function reset_ajax_color( usecolor ) {
// reset all the <span class="ajax"...> styles to have no color override
      var elements = get_ajax_tags();
	  var numelements = elements.length;
	  for (var index=0;index!=numelements;index++) {
         var element = elements[index];
	     element.style.color=usecolor;
 
      }
}

function set_ajax_obs( name, inValue ) {
// store away the current value in both the doc and the span as lastobs="value"
// change color if value != lastobs
        var value = inValue;
        if(decimalComma) {
			value = inValue.replace(/(\d)\.(\d)/,"$1,$2");
		}
		var element = document.getElementById(name);
		if (! element ) { return; } // V1.04 -- don't set if missing the <span id=name> tag
		var lastobs = element.getAttribute("lastobs");
		element.setAttribute("lastobs",value);
		if (value != unescape(lastobs)) {
          element.style.color=flashcolor;
		  if ( doTooltip ) { element.setAttribute("title",'AJAX tag '+name); }
		  element.innerHTML =  value; // moved inside to fix flashing issue (Jim at jcweather.us)
		}
}

function set_ajax_uom( name, onoroff ) {
// this function will set an ID= to visible or hidden by setting the style="display: "
// from 'inline' or 'none'

		var element = document.getElementById(name);
		if (! element ) { return; } 
		if (onoroff) {
          element.style.display='inline';
		} else {
          element.style.display='none';
		}
}

// --- end of flash-green functions

function windDir ($winddir)
// Take wind direction value, return the
// text label based upon 16 point compass -- function by beeker425
//  see http://www.weather-watch.com/smf/index.php/topic,20097.0.html
{
   var $windlabel = new Array("N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW");
   return $windlabel[Math.floor(((parseInt($winddir) + 11) / 22.5) % 16 )];
}

function windDirLang ($winddir)
// Take wind direction value, return the
// text label based upon 16 point compass -- function by beeker425
//  see http://www.weather-watch.com/smf/index.php/topic,20097.0.html
{
   return langWindDir[Math.floor(((parseInt($winddir) + 11) / 22.5) % 16 )];
}

// utility functions to handle conversions from realtime data to desired units-of-measure
function cTempToC ( rawtemp ) {
	// convert input temperature to C if need be			   
	var cpat=/C/i;
	if(cpat.test(rTempUOM)) { // temperature already in C
		return rawtemp * 1.0;
	} else { // convert F to C
		return (rawtemp - 32.0) * (100.0/(212.0-32.0));
	}
}

function cBaroToHPA ( rawbaro ) {
	// convert input pressure to hPa if need be			   
	var cpat=/mb|hpa/i;
	if(cpat.test(rBaroUOM)) { // baro already in mb/hPa
		return rawbaro * 1.0;
	} else { // convert inHg to mb/hPa
		return rawbaro * 33.86;
	}
				   
}

function cWindToKTS ( rawwind ) {
	// convert input wind to knots if need be
	if(rawwind == '---') {rawwind = 0.0;}
	var cpat=/kts|knots/i;
	if(cpat.test(rWindUOM)) { // wind already in knots
		return rawwind * 1.0;
	}
	cpat=/mph/i;
	if(cpat.test(rWindUOM)) { // wind in mph -> knots
		return rawwind * 0.868976242;
	}
	cpat=/kmh|km\/h/i;
	if(cpat.test(rWindUOM)) { // wind in kmh -> knots
		return rawwind * 0.539956803;
	}
	cpat=/mps|m\/s/i;
	if(cpat.test(rWindUOM)) { // wind in mps -> knots
		return rawwind * 1.9438444924406;
	}
	return -1.0;  // shouldn't be here :)

}

function cRainToMM ( rawrain ) {
	// convert input rain to mm if need be			   
	var cpat=/mm/i;
	if(cpat.test(rRainUOM)) { // rain already in mm
		return rawrain * 1.0;
	} else { // convert inches to mm
		return rawrain * 25.4;
	}
}

function cHeightToFT ( rawheight ) {
	// convert input height to feet if need be			   
	var cpat=/ft/i;
	if(cpat.test(rHeightUOM)) { // rain already in mm
		return rawheight * 1.0;
	} else { // convert meters to feet
		return rawheight * 3.2808399;
	}
}


function convertTemp ( inrawtemp ) {
	// function expects temperature in C
	var rawtemp = cTempToC(inrawtemp);
	if (useunits == 'E') { // convert C to F
		return( (1.8 * rawtemp) + 32.0 );
	} else {  // leave as C
		return (rawtemp * 1.0);
	}
}

function convertTempRate ( inrawtemprate ) {
	// function expects temperature in C
	var cpat=/C/i;
	var temprate = inrawtemprate
	if(cpat.test(rTempUOM)) { // temperature already in C
		temprate = inrawtemprate * 1.0;
	} else { // convert F rate to C rate
		temprate = inrawtemprate / 1.8;   /* 1 degree C = 1.8 degrees F for rate */
	}
	
	if (useunits == 'E') { // convert C rate to F rate
		return( (1.8 * temprate) );
	} else {  // leave as C
		return (temprate * 1.0);
	}
}

function convertTempC ( rawtemp ) {
	// function expects temperature in C
	if (useunits == 'E') { // convert C to F
		return( (1.8 * rawtemp) + 32.0 );
	} else {  // leave as C
		return (rawtemp * 1.0);
	}
}

function convertWind  ( inrawwind ) {
	// function expects wind speed in Knots
	var rawwind = cWindToKTS(inrawwind);
	if (useKnots) { return(rawwind * 1.0).toFixed(dpWind); } //force usage of knots for speed
	if (useunits == 'E' || useMPH ) { // convert knots to mph
		return(rawwind * 1.1507794);
	} else {  
	    if (useMPS) { // convert knots to m/s
		  return (rawwind * 0.514444444);
		} else { // convert knots to km/hr
		  return (rawwind * 1.852);
		}
	}
}

function convertBaro ( inrawpress ) {
	// function expects input in mb/hPa
	var rawpress = cBaroToHPA(inrawpress);
	if (! usehPa && useunits == 'E') { // convert hPa to inHg
	   return (rawpress  / 33.86388158);
	} else {
	   return (rawpress * 1.0); // leave in hPa
	}
}

function convertRain ( inrawrain ) {
	// function expects input in mm
	var rawrain = cRainToMM(inrawrain);
	if (useunits == 'E') { // convert mm to inches
	   return (rawrain * .0393700787);
	} else {
	   return (rawrain * 1.0); // leave in mm
	}
}

function convertHeight ( inrawheight ) {
	// function expects input in feet
	var rawheight = cHeightToFT(inrawheight)
	if (useunits == 'E' || useFeet ) { // convert feet to meters if metric
	   return (Math.round(rawheight * 1.0)); // leave in feet
	} else {
	   return (Math.round(rawheight / 3.2808399));
	}
}


function ajax_get_beaufort_number ( inwind ) { 
// return a number for the beaufort scale based on wind in knots (native WD format)
  var wind = cWindToKTS(inwind);
  
  if (wind < 1 ) {return("0"); }
  if (wind < 4 ) {return("1"); }
  if (wind < 7 ) {return("2"); }
  if (wind < 11 ) {return("3"); }
  if (wind < 17 ) {return("4"); }
  if (wind < 22 ) {return("5"); }
  if (wind < 28 ) {return("6"); }
  if (wind < 34 ) {return("7"); }
  if (wind < 41 ) {return("8"); }
  if (wind < 48 ) {return("9"); }
  if (wind < 56 ) {return("10"); }
  if (wind < 64 ) {return("11"); }
  if (wind >= 64 ) {return("12"); }
  return("0");
}

function ajax_get_barotrend(inbtrnd) {
// routine from Anole's wxsticker PHP (adapted to JS by Ken True)
// input: trend in hPa or millibars
//   Barometric Trend(3 hour)

// Change Rates
// Rapidly: =.06 inHg; 1.5 mm Hg; 2 hPa; 2 mb
// Slowly: =.02 inHg; 0.5 mm Hg; 0.7 hPa; 0.7 mb

// 5 conditions
// Rising Rapidly
// Rising Slowly
// Steady
// Falling Slowly
// Falling Rapidly

// Page 52 of the PDF Manual
// http://www.davisnet.com/product_documents/weather/manuals/07395.234-VP2_Manual.pdf
// figure out a text value for barometric pressure trend
   var btrnd = cBaroToHPA(inbtrnd);
   
   if ((btrnd >= -0.7) && (btrnd <= 0.7)) { return(langBaroTrend[0]); }
   if ((btrnd > 0.7) && (btrnd < 2.0)) { return(langBaroTrend[1]); }
   if (btrnd >= 2.0) { return(langBaroTrend[2]); }
   if ((btrnd < -0.7) && (btrnd > -2.0)) { return(langBaroTrend[3]); }
   if (btrnd <= -2.0) { return(langBaroTrend[4]); }
  return(btrnd);
}

function ajax_getUVrange ( uv ) { // code simplified by FourOhFour on wxforum.net
   var uvword = "Unspec.";
   if (uv <= 0) {
       uvword = langUVWords[0];
   } else if (uv < 3) {
       uvword = "<span style=\"border: solid 1px; color: black; background-color: #A4CE6a;\">&nbsp;"+langUVWords[1]+"&nbsp;</span>";
   } else if (uv < 6) {
       uvword = "<span style=\"border: solid 1px; color: black; background-color: #FBEE09;\">&nbsp;"+langUVWords[2]+"&nbsp;</span>";
   } else if (uv < 8) {
       uvword =  "<span style=\"border: solid 1px; color: black; background-color: #FD9125;\">&nbsp;"+langUVWords[3]+"&nbsp;</span>";
   } else if (uv < 11) {
       uvword =  "<span style=\"border: solid 1px; color: #FFFFFF; background-color: #F63F37;\">&nbsp;"+langUVWords[4]+"&nbsp;</span>";
   } else {
       uvword =  "<span style=\"border: solid 1px; color: #FFFF00; background-color: #807780;\">&nbsp;"+langUVWords[5]+"&nbsp;</span>";
   }
   return uvword;
} // end ajax_getUVrange function

function ajax_genarrow( nowTemp, yesterTemp, Legend, textUP, textDN, numDp) {
// generate an <img> tag with alt= and title= for rising/falling values	
	
  var diff = nowTemp.toFixed(3) - yesterTemp.toFixed(3);
  var absDiff = Math.abs(diff);
  var diffStr = '' + diff.toFixed(numDp);  // sprintf("%01.0f",$diff);
  var absDiffStr = '' + absDiff.toFixed(numDp); // sprintf("%01.0f",$absDiff);
  var image = '';
  var msg = '';
  
  if (diff == 0) {
 // no change
    image = '&nbsp;'; 
  } else if (diff > 0) {
// today is greater 
//    msg = textUP + " by " + diff.toFixed(1); // sprintf($textDN,$absDiff); 
	msg = textUP.replace(/\%s/,absDiffStr);
    image = "<img src=\"" + imagedir + "/rising.gif\" alt=\"" + msg + 
	"\" title=\""+ msg + 
	"\" width=\"7\" height=\"8\" style=\"border: 0; margin: 1px 3px;\" />";
  } else {
// today is lesser
    msg = textDN.replace(/\%s/,absDiffStr); // sprintf($textDN,$absDiff); 
//	msg = textDN.replace(/\%s/,absDiffStr);
    image = "<img src=\"" + imagedir + "/falling.gif\" alt=\"" + msg + 
	"\" title=\""+ msg + 
	"\" width=\"7\" height=\"8\" style=\"border: 0; margin: 1px 3px;\" />";
   
  }

   if (Legend) {
       return (diff + Legend + image);
	} else {
	   return image;
	}
} // end genarrow function

// function to add colored heatColorWord by Mike Challis
// final version 1.00 
function heatColor(inTemp,inWindChill,inHumidex) {
  var hcWord = langHeatWords[0];
  // make sure raw variables are converted to C before the compare
  var temp = cTempToC(inTemp);
  var WindChill = cTempToC(inWindChill);
  var Humidex = cTempToC(inHumidex);
  
 if (temp > 32 && Humidex > 29) {
  if (Humidex > 54) { return ('<span style="border: solid 1px; color: white; background-color: #BA1928;">&nbsp;'+langHeatWords[1]+'&nbsp;</span>'); }
  if (Humidex > 45) { return ('<span style="border: solid 1px; color: white; background-color: #E02538;">&nbsp;'+langHeatWords[2]+'&nbsp;</span>'); }
  if (Humidex > 39) { return ('<span style="border: solid 1px; color: black; background-color: #E178A1;">&nbsp;'+langHeatWords[4]+'&nbsp;</span>'); }
  if (Humidex > 29) { return ('<span style="border: solid 1px; color: white; background-color: #CC6633;">&nbsp;'+langHeatWords[6]+'&nbsp;</span>'); }
 } else if (WindChill < 16 ) {
  if (WindChill < -18) { return ('<span style="border: solid 1px; color: black; background-color: #91ACFF;">&nbsp;'+langHeatWords[13]+'&nbsp;</span>'); }
  if (WindChill < -9)  { return ('<span style="border: solid 1px; color: white; background-color: #806AF9;">&nbsp;'+langHeatWords[12]+'&nbsp;</span>'); }
  if (WindChill < -1)  { return ('<span style="border: solid 1px; color: white; background-color: #3366FF;">&nbsp;'+langHeatWords[11]+'&nbsp;</span>'); }
  if (WindChill < 8)   { return ('<span style="border: solid 1px; color: white; background-color: #6699FF;">&nbsp;'+langHeatWords[10]+'&nbsp;</span>'); }
  if (WindChill < 16)  { return ('<span style="border: solid 1px; color: black; background-color: #89B2EA;">&nbsp;'+langHeatWords[9]+'&nbsp;</span>'); }
 }  else if (WindChill >= 16 && temp <= 32) {
  if (temp < 26) { return ('<span style="border: solid 1px; color: black; background-color: #C6EF8C;">&nbsp;'+langHeatWords[8]+'&nbsp;</span>'); }
  if (temp <= 32) { return ('<span style="border: solid 1px; color: black; background-color: #CC9933;">&nbsp;'+langHeatWords[7]+'&nbsp;</span>'); }
  }
  return hcWord;
}

// Mike Challis' counter function (adapted by Ken True)
//
function ajax_countup() {
 var element = document.getElementById("ajaxcounter");
 if (element) {
  element.innerHTML = counterSecs;
  counterSecs++;
 }
}

function ucFirst ( str ) {
   return str.substr(0,1).toUpperCase() + str.substr(1,str.length);
}
//
// slice and dice the realtime[49] for possible translation of current weather
//
function ajaxFixupCondition( rawcond ) {

  var cond = rawcond;
  cond = cond.replace(/_/gm,' ');  // replace any _ with blank.
  cond = cond.replace(/[\r\n]/gm,'');  // remove embedded CR and/or LF
  var conds = cond.split('/');  // split up the arguments.
  var tstr = '';
  for (var i = 0;i<conds.length;i++) {
    var t = conds[i];
	t = t.toLowerCase();
	t = ucFirst(t);
    t = t.replace(/\s+$/,'');  // trim trailing blanks
	if(langTransLookup[t]) { 
	  conds[i] = langTransLookup[t];
	} else {
	  conds[i] = t;
	}
  }
  if (conds[0].length == 0) { conds.splice(0,1);  } // remove blank entry
  if (conds[0] == conds[2]) { conds.splice(2,1); } // remove duplicate last entry
  
  return(conds.join(', '));

}

function ajaxRequest () {
	/* find the handler for AJAX based on availability of the request object */
	try { var request = new XMLHttpRequest() /* non IE browser (or IE8 native) */ }
	catch(e1) {
		try { request = ActiveXObject("Msxml2.XMLHTTP") /* try IE6+ */ }
		catch(e2) {
			try { request = ActiveXObject("Microsoft.XMLHTTP") /* try IE5 */}
			catch(e3) // no Ajax support
			{ request = false; alert('Sorry.. AJAX updates are not available for your browser.') }
		}
	}
	if (! request) { maxupdates = 1; }
	return request;
}

// ------------------------------------------------------------------------------------------
//  main function.. read realtime.txt and format <span class="ajax" id="ajax..."></span> areas
// ------------------------------------------------------------------------------------------
function ajaxLoader(url) {
/*
  if (document.getElementById) {
	if (typeof(window.ActiveXObject) != "undefined") {
    var x = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
	var x = new XMLHttpRequest(url);
	}
  }
*/
  var x = new ajaxRequest();
  
  if (x) { // got something back
    x.onreadystatechange = function() {
    try { if (x.readyState == 4 && x.status == 200) { // Mike Challis added fix to fix random error: NS_ERROR_NOT_AVAILABLE 
    var realtime = x.responseText.split(' ');
	// now make sure we got the entire realtime.txt  -- thanks to Johnnywx
    var cupattern=/\d+(\/|-|\.)\d+(\/|-|\.)\d+ \d+:\d+:\d+/; // looks for 'dd/dd/dd' date string followed by 'hh:mm:ss'
	// If we have a valid realtime file AND updates is < maxupdates
	if( cupattern.test(x.responseText) && 
	    ( updates <= maxupdates || maxupdates > 0  ) ) {
		if (maxupdates > 0 ) {updates++; } // increment counter if needed
		
		rWindUOM = realtime[13];  // extract units-of-measure used in realtime file and replace defaults
		rTempUOM = realtime[14];
		rBaroUOM = realtime[15];
		rRainUOM = realtime[16];
		rHeightUOM = realtime[53];
        
        //Temperature
		var temp = convertTemp(realtime[2]);
		set_ajax_obs("ajaxtemp", temp.toFixed(1) + uomTemp);
		set_ajax_obs("ajaxtempNoU", temp.toFixed(1));
		set_ajax_obs("gizmotemp", temp.toFixed(1) + uomTemp);
		set_ajax_obs("ajaxbigtemp",temp.toFixed(0) + uomTemp);
  
		var temptrend = convertTempRate(realtime[25]);
		var temparrow = ajax_genarrow(temp, temp-temptrend, '', 
			 langTempRising+uomTemp+langTempLastHour,
			 langTempFalling+uomTemp+langTempLastHour,1)
		
		set_ajax_obs("ajaxtemparrow",temparrow); 
		set_ajax_obs("gizmotemparrow",temparrow); 
		   
	    var temprate = temptrend.toFixed(1);
		if (temprate > 0.0) { temprate = '+' + temprate;} // add '+' for positive rates
		set_ajax_obs("ajaxtemprate",temprate + uomTemp);
		set_ajax_obs("gizmotemprate",temprate + uomTemp);

		var tempmax = convertTemp(realtime[26]);
		set_ajax_obs("ajaxtempmax",tempmax.toFixed(1) + uomTemp);

		var tempmin = convertTemp(realtime[28]);
		set_ajax_obs("ajaxtempmin",tempmin.toFixed(1) + uomTemp);
		
		var thermometerstr = langThermoCurrently +  + temp.toFixed(1) + uomTemp + 
		  ", " + langThermoMax + tempmax.toFixed(1) + uomTemp +
		  ", " + langThermoMin + tempmin.toFixed(1) + uomTemp;

		set_ajax_obs("ajaxthermometer",
			"<img src=\"" + thermometer + "?t=" + temp.toFixed(1) + "\" " +
				"width=\"54\" height=\"170\" " +
				"alt=\"" + thermometerstr + "\" " +
				"title=\"" + thermometerstr + "\" />" );

		//Humidity ...
		var humidity = realtime[3];
		set_ajax_obs("ajaxhumidity",humidity);
		set_ajax_obs("gizmohumidity",humidity);
		
		//Dewpoint ...
		var dew = convertTemp(realtime[4]);
		set_ajax_obs("ajaxdew",dew.toFixed(1) + uomTemp);
		set_ajax_obs("gizmodew",dew.toFixed(1) + uomTemp);

		// Humidex
		var humidex = convertTemp(realtime[42]);
		set_ajax_obs("ajaxhumidex",humidex.toFixed(1) + uomTemp);

		//  WindChill
		var windchill = convertTemp(realtime[24]);
		set_ajax_obs("ajaxwindchill",windchill.toFixed(1) + uomTemp);

		// Heat Index
		var heatidx = convertTemp(realtime[41]);
		set_ajax_obs("ajaxheatidx",heatidx.toFixed(1) + uomTemp);
        // FeelsLike
        temp = cTempToC(realtime[2]); // note.. temp in C
		var HC = 0;
		var HCraw = 0;
		switch (useFeelslike) {
		  case 0: 
			  HCraw = realtime[41]; //use HeatIndex
		  case 1: 
			  HCraw = realtime[42]; //use Humidex
			  break;
		  case 2: 
			  HCraw = realtime[54]; //use Apparent Temperature
			  break;
		  default: 
			  HCraw = realtime[41]; //use HeatIndex (default)
			  break;
  		}
		var HC = cTempToC(HCraw);
        if (temp <= 16.0 ) {
		  feelslike = cTempToC(realtime[24]); //use WindChill
		} else if (temp >=27.0) {
		  feelslike = HC; //use HeatIndex/Humidex/ApparentTemp
		} else {
		  feelslike = temp;   // use temperature
		}
		var feelslike  = Math.round(convertTempC(feelslike));
        set_ajax_obs("ajaxfeelslike",feelslike + uomTemp);

		// # mike challis added heatColorWord feature
		var heatColorWord = heatColor(realtime[2],realtime[24],HCraw);
		set_ajax_obs("ajaxheatcolorword",heatColorWord);
		
		//Pressure...
		var pressure = convertBaro(realtime[10]);
		set_ajax_obs("ajaxbaro",pressure.toFixed(dpBaro) + uomBaro);
		set_ajax_obs("ajaxbaroNoU",pressure.toFixed(dpBaro));
		set_ajax_obs("gizmobaro",pressure.toFixed(dpBaro) + uomBaro);
		var pressuretrend = convertBaro(realtime[18]);
		pressuretrend = pressuretrend.toFixed(dpBaro+1);
		if (pressuretrend > 0.0) {pressuretrend = '+' + pressuretrend; } // add '+' to rate
		set_ajax_obs("ajaxbarotrend",pressuretrend + uomBaro);
		set_ajax_obs("gizmobarotrend",pressuretrend + uomBaro);
		set_ajax_obs("ajaxbaroarrow",
		   ajax_genarrow(pressure, pressure-pressuretrend, '', 
			 langBaroRising+uomBaro+langBaroPerHour,
			 langBaroFalling+uomBaro+langBaroPerHour,2)
			 );	
		var barotrendtext = ajax_get_barotrend(realtime[18]);
		set_ajax_obs("ajaxbarotrendtext",barotrendtext);
		set_ajax_obs("gizmobarotrendtext",barotrendtext);

		var pressuremin = convertBaro(realtime[36]);
		set_ajax_obs("ajaxbaromin",pressuremin.toFixed(dpBaro) + uomBaro);
		var pressuremax = convertBaro(realtime[34]);
		set_ajax_obs("ajaxbaromax",pressuremax.toFixed(dpBaro) + uomBaro);

        //Wind gust
		var gust    = convertWind(realtime[40]);
		var maxgust = convertWind(realtime[32]);
		if (maxgust > 0.0 ) {
		  set_ajax_obs("ajaxmaxgust",maxgust.toFixed(1) + uomWind);
		} else {
		  set_ajax_obs("ajaxmaxgust",'None');
		}

		//Windspeed ...
		var wind = convertWind(realtime[5]);
		var beaufortnum = ajax_get_beaufort_number(realtime[5]);
		set_ajax_obs("ajaxbeaufortnum",beaufortnum);
		set_ajax_obs("ajaxbeaufort",langBeaufort[beaufortnum]);

       //WIND DIRECTION ...
        var val = windDir(realtime[7]);
		var valLang = windDirLang(realtime[7]); /* to enable translations */

       if (wind > 0.0) {
		set_ajax_obs("ajaxwind",wind.toFixed(1) + uomWind);
		set_ajax_obs("ajaxwindNoU",wind.toFixed(1));
		set_ajax_obs("gizmowind",wind.toFixed(1) + uomWind);
		set_ajax_uom("ajaxwinduom",true);
	   } else {
		set_ajax_obs("ajaxwind",langWindCalm);
		set_ajax_obs("ajaxwindNoU",langWindCalm);
		set_ajax_obs("gizmowind",langWindCalm);
		set_ajax_uom("ajaxwinduom",false);
	   }
	   
	   if (gust > 0.0) {
		set_ajax_obs("ajaxgust",gust.toFixed(1) + uomWind);
		set_ajax_obs("ajaxgustNoU",gust.toFixed(1));
		set_ajax_obs("gizmogust",gust.toFixed(1) + uomWind);
		set_ajax_uom("ajaxgustuom",true);
	   } else {
		set_ajax_obs("ajaxgust",langGustNone);
		set_ajax_obs("ajaxgustNoU",langGustNone);
		set_ajax_obs("gizmogust",langGustNone);
		set_ajax_uom("ajaxgustuom",false);
	   }
	   
   	   if (gust > 0.0 || wind > 0.0) {
		var windicon = 	"<img src=\"" + imagedir + "/" +  val + ".gif\" width=\"14\" height=\"14\" alt=\"" + 
		  langWindFrom + valLang + "\" title=\"" + langWindFrom + valLang + "\" /> ";
 		set_ajax_obs("ajaxwindicon",windicon);
		set_ajax_obs("gizmowindicon",windicon);
 		set_ajax_obs("ajaxwindiconwr",
		  "<img src=\"" + imagedir + "/" +wrName +  val + wrType + "\" width=\""+
		   wrWidth+"\" height=\""+wrHeight+"\" alt=\"" + 
		  langWindFrom + valLang + "\" title=\"" +langWindFrom + valLang + "\" /> ");
		set_ajax_obs("ajaxwinddir",valLang);
		set_ajax_obs("gizmowinddir",valLang);
	   } else {
 		set_ajax_obs("ajaxwindicon","");
 		set_ajax_obs("gizmowindicon","");
		set_ajax_obs("ajaxwinddir","");
		set_ajax_obs("gizmowinddir","");
		if (wrCalm != '') {
 		 set_ajax_obs("ajaxwindiconwr",
		  "<img src=\"" + imagedir + "/" + wrCalm + "\" width=\""+
		   wrWidth+"\" height=\""+wrHeight+"\" alt=\"" + 
		  langBeaufort[0] + "\" title=\"" +langBeaufort[0] + "\" /> ");
		}
	   }

		var windmaxavg = convertWind(realtime[30]);
		set_ajax_obs("ajaxwindmaxavg",windmaxavg.toFixed(1) + uomWind);
		
		var windmaxgust = convertWind(realtime[32]);
		set_ajax_obs("ajaxwindmaxgust",windmaxgust.toFixed(1) + uomWind);

		var windmaxgusttime = realtime[33];
		set_ajax_obs("ajaxwindmaxgusttime",windmaxgusttime);
		

		//  Solar Radiation
		var solar    = realtime[45] * 1.0;
		set_ajax_obs("ajaxsolar",solar.toFixed(0));

		// UV Index		
		var uv       = realtime[43];
		set_ajax_obs("ajaxuv",uv) ;
		set_ajax_obs("gizmouv",uv) ;

		var uvword = ajax_getUVrange(uv);
		set_ajax_obs("ajaxuvword",uvword);
		set_ajax_obs("gizmouvword",uvword);

		//Rain ...
		var rain = convertRain(realtime[9]);
		set_ajax_obs("ajaxrain",rain.toFixed(dpRain) + uomRain);
		set_ajax_obs("ajaxrainNoU",rain.toFixed(dpRain));
		set_ajax_obs("gizmorain",rain.toFixed(dpRain) + uomRain);

/*		var rainydy = convertRain(realtime[19]);
		set_ajax_obs("ajaxrainydy",rainydy.toFixed(dpRain)+ uomRain);
*/
		var rainmo = convertRain(realtime[19]);
		set_ajax_obs("ajaxrainmo",rainmo.toFixed(dpRain) + uomRain);

		var rainyr = convertRain(realtime[20]);
		set_ajax_obs("ajaxrainyr",rainyr.toFixed(dpRain) + uomRain);

		var rainratehr = convertRain(realtime[8]); // make per hour rate.
		set_ajax_obs("ajaxrainratehr",rainratehr.toFixed(dpRain+1) + uomRain);

/*		var rainratemax = convertRain(realtime[11]); // make per hour rate
		set_ajax_obs("ajaxrainratemax",rainratemax.toFixed(dpRain+1) + uomRain);
*/
		// Provides Date String Objects in the form of
		// ntime = HH:MM                as in 17:24
		// ndate = Mon DD, YYYY         as in Nov 14, 2007
		// tday  = 3 letter Abr of Day  as in Wed
		//
		// All combined you could end up with   Mon Nov 14, 2007
		// 
		// Uses realtime elements:
		// Hour 29  Min 30  Day 35  Month 36  Year 141
		// Added 2007-11-14 by Kevin Reed TNETWeather.com
		//======================================================================
/*		var ntime = realtime[1].substring(0,3);
		var ndate = langMonths[ realtime[36] -1 ].substring(0,3) + " " + realtime[35] + " " + realtime[141];
		var ndate2 = realtime[35] + "-" +langMonths[ realtime[36] -1 ].substring(0,3) + "-" +  realtime[141];
		var myDate = new Date( langMonths[ realtime[36] - 1 ] + " " + realtime[35] + ", " + realtime[141] );
		var tday = langDays[myDate.getDay()];
		//
		set_ajax_obs("ajaxndate", ndate );
		set_ajax_obs("ajaxndate2",ndate2);
		set_ajax_obs("ajaxntime", ntime );
		set_ajax_obs("ajaxntimess", ntime + ":" + realtime[31]);
		set_ajax_obs("ajaxdname", tday );
*/
		// current date and time of observation in realtime.txt
		var ajaxtimeformat = realtime[1];
		var ajaxdateraw = realtime[0]; // comes in dd/mm/yy, dd-mm-yy or dd.mm.yy format
		var tdate = ajaxdateraw.split("/");
		if(typeof(tdate[2])=='undefined') {tdate = ajaxdateraw.split("-"); }
		if(typeof(tdate[2])=='undefined') {tdate = ajaxdateraw.split("."); }
		var ajaxdateformat = tdate[1]+"/"+tdate[0]+"/"+tdate[2];
        if(!showDateMDY) { ajaxdateformat = tdate[0]+"/"+tdate[1]+"/"+tdate[2];  }
		set_ajax_obs("ajaxdatetime",ajaxdateformat + " " +ajaxtimeformat);
		set_ajax_obs("ajaxdate",ajaxdateformat);
		set_ajax_obs("ajaxtime",ajaxtimeformat);
		set_ajax_obs("gizmodate",ajaxdateformat);
		set_ajax_obs("gizmotime",ajaxtimeformat);
		
		if (lastajaxtimeformat != ajaxtimeformat) {
			counterSecs = 0;                      // reset timer
			lastajaxtimeformat = ajaxtimeformat; // remember this time
		}
		
		// cloud height
		var cloudheight = realtime[52];
		set_ajax_obs("ajaxcloudheight",convertHeight(cloudheight) + uomHeight);
		
		// now ensure that the indicator flashes on every AJAX fetch
        var element = document.getElementById("ajaxindicator");
		if (element) {
          element.style.color = flashcolor;
		}
        var element = document.getElementById("gizmoindicator"); // separate gizmo ajax variable
		if (element) {
          element.style.color = flashcolor;
		}
		if (maxupdates > 0 && updates > maxupdates-1) { /* chg indicator to pause message */
			set_ajax_obs("ajaxindicator",langPauseMsg);
			set_ajax_obs("gizmoindicator",langPauseMsg);
		}
		set_ajax_obs('ajaxupdatecount',updates);       /* for test pages */
		set_ajax_obs('ajaxmaxupdatecount',maxupdates); /* for test pages */

       } // END if(realtime[0] = '12345' and '!!' at end)

	 } // END if (x.readyState == 4 && x.status == 200)

    } // END try

   	catch(e){}  // Mike Challis added fix to fix random error: NS_ERROR_NOT_AVAILABLE

    } // END x.onreadystatechange = function() {
    x.open("GET", url, true);
    x.send(null);

//  reset the flash colors, and restart the update unless maxupdate limit is reached

    setTimeout("reset_ajax_color('')",flashtime); // change text back to default color 

	if ( (maxupdates == 0) || (updates < maxupdates-1)) {
      setTimeout("ajaxLoader(realtimeFile + '?' + new Date().getTime())", reloadTime); // get new data 
    }
  }
} // end ajaxLoader function

//element = document.getElementById("ajaxcounter");
//if (element) {
  window.setInterval("ajax_countup()", 1000); // run the counter for seconds since update
//}

// invoke when first loaded on page
if (! ajaxLoaderInBody) { ajaxLoader(realtimeFile + '?' + new Date().getTime(), reloadTime); }


// ]]>