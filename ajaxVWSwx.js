// <![CDATA[

// Special thanks to: Pinto http://www.joske-online.be/ and Tom http://www.carterlake.org/
// They pioneered the basic AJAX code using WD clientraw.txt which was
// cheerfully borrowed from Tom at CarterLake.org and adapted by
// Ken True - Saratoga-weather.org  21-May-2006
// --- added flash-green on data update functions - Ken True  24-Nov-2006
// --- Version 1.00 - 03-Dec-2006 -- Adapted for VWS data.csv usage - Ken True saratoga-weather.org
// --- Version 1.04 - 13-Dec-2006 -- Ken True -repackaged AJAX function, 
//     also included Mike Challis' counter script to display seconds since last update and error
//     handling for the fetch to fix to fix random error: NS_ERROR_NOT_AVAILABLE
//     Mike's site: http://www.carmosaic.com/weather/index.php
//     Thanks to FourOhFour on wxforum.net ( http://skigod.us/ ) for replacing all the
//     x.responseText.split(' ')[n] calls with a simple array lookup.. much better speed and
//     for his streamlined version of getUVrange.
//
// --- Version 2.00 - 30-Mar-2007 -- adapted to read VWS WeatherFlash text files by Matt at weatherbus.com
// --- Version 2.01 - 20-Jul-2007 -- packaged and adapted code for more robust loader functions and unit
//                                   conversions by Ken at saratoga-weather.org
// --- Version 2.02 - 23-Jul-2007 -- added checks on heatidx/windchill, added wind display features, new
//                                   uom display logic.
// --- Version 2.03 - 21-Sep-2007 -- added support for dynamic thermometer.php display updates
// --- Version 2.04 - 30-Dec-2007 -- added maxupdates feature, language translation features and 
//                                   added optional Wind-Rose display
// --- Version 2.05 - 10-Mar-2008 -- changed toFixed to Math.round in conversions
// --- Version 2.06 - 13-Mar-2008 -- added ajaxfeelslike support
// --- Version 2.07 - 20-Mar-2009 -- added support for IE8 native mode
// --- Version 2.08 - 12-Jan-2011 -- addes support for new universal templates
// --- Version 2.09 - 28-Jan-2011 -- some code cleanup to reduce JavaScript messages
// --- Version 2.10 - 03-Feb-2011 -- fixed NaN issue with I=<ID>& uploads to wflash/wflash2.txt
// --- Version 2.11 - 17-Feb-2011 -- added decimal comma option for international display
//
// for updates to this script, see http://saratoga-weather.org/scripts-VWS-AJAX.php
// announcements of new versions will be on ambientwxsupport.com and wxforum.net

// -- begin settings --------------------------------------------------------------------------
var flashcolor = '#00CC00'; // color to flash for changed observations RGB
var flashtime  = 2000;    // miliseconds to keep flash color on (2000 = 2 seconds);
var reloadTime = 10000;      // reload AJAX conditions every 10 seconds (= 10000 ms)
var maxupdates = 60;	         // Maxium Number of updates allowed (set to zero for unlimited)
                             // maxupdates * reloadTime / 1000 = number of seconds to update
var wflashDir = '../WxFlash/';   // URL for directory for WeatherFlash relative to this script with
//                               trailing '/'.  In Root = '/', in /wflash = '/wflash/'
var imagedir = './ajax-images'  // place for wind arrows, rising/falling arrows, etc.
var useunits = 'E';         // 'E'=USA(English) or 'M'=Metric
var decimalComma = false;    // =false for '.' as decimal point, =true for ',' (comma) as decimal point
var useKnots = false;       // set to true to use wind speed in Knots (otherwise 
							// wind in km/hr for Metric or mph for English will be used.
var useMPS   = false;       // set to true for meters/second for metric wind speeds, false= km/h
var useMPH   = false;       // set to true to force MPH for both English and Metric units
var useFeet  = false;       // set to true to force Feet for height in both English and Metric
var usehPa   = false;       // set to true to force hPa for baro in both English and Metric
var showUnits = true;       //  set to false if no units are to be displayed
var useAMPM   = true;       // set to false for 24hr time (only used for update time value)
var showNoWind = true;      // true shows wind=0 as 'Calm' and gust=0 as 'No Wind'
//                          // flase shows wind=0 as '0' and gust=0 as '0'
var thermometer = './thermometer.php'; // script for dynamic thermometer PNG image (optional)
// optional settings for the Wind Rose graphic in ajaxwindiconwr as wrName + winddir + wrType
var wrName   = 'wr-';       // first part of the graphic filename (followed by winddir to complete it)
var wrType   = '.png';      // extension of the graphic filename
var wrHeight = '58';        // windrose graphic height=
var wrWidth  = '58';        // windrose graphic width=
var wrCalm   = 'wr-calm.png';  // set to full name of graphic for calm display ('wr-calm.gif')
// -- end of settings -------------------------------------------------------------------------
//
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

var langTempRising = 'Rising %s '; /* used for trend arrow alt/title tags .. %s marks where value will be placed */
var langTempFalling = 'Falling %s ';
var langTempPerHour = '/hour.';

var langHumRising = 'Rising %s '; /* used for trend arrow alt/title tags .. %s marks where value will be placed */
var langHumFalling = 'Falling %s ';
var langHumPerHour = '/hour.';

var langTransLookup = new Object;  // storage area for key/value for current conditions translation

var langHeatWords = new Array (
 'Unknown', 'Extreme Heat Danger', 'Heat Danger', 'Extreme Heat Caution', 'Extremely Hot', 'Uncomfortably Hot',
 'Hot', 'Warm', 'Comfortable', 'Cool', 'Cold', 'Uncomfortably Cold', 'Very Cold', 'Extreme Cold' );

// -- end of language settings ----------------------------------------------------------
//
// You shouldn't have to change these settings for file locations, they are the defaults for
// WeatherFlash
var wflashFile = wflashDir+'Data/wflash.txt'; // location of wflash.txt relative to this page on website
var wflashFile2 = wflashDir+'Data/wflash2.txt'; // location of wflash2.txt relative to this page on website
var wflashUnitsFile = wflashDir+'Config/Units.txt'; // location of Config/Units.txt file for wflash
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
var lastajaxtime = 'unknown'; //used to reset the counter when a real update is done
var doTooltip = 0;   // set to 1 to have ajaxed variable names appear as tooltips (except for graphics)

// handle setup options for units-of-measure and whether to show them at all
// note.. units to use will be updated automatically from the Weather Flash Config/Units.txt file
// --------------- DON'T change thise defaults ---- they are the units used in wflash.txt, wflash2.txt
var uomTemp = '&deg;F'; 	var uomTempCnvt = 0;
var uomWind = ' mph';   	var uomWindCnvt = 0;
var uomBaro = ' inHg';  	var uomBaroCnvt = 0;
var uomRain = ' in';    	var uomRainCnvt = 0;
//var uomHumid= '%';      
//var uomSolar= ' W/m<sup>2</sup>';
var uomHumid= '';      
var uomSolar= '';
var uomHeight = ' ft';  	var uomHeightCnvt = 0;
var uomDistance = ' miles'; var uomDistanceCnvt = 0;
var uomPerHr = '/hr';
var uomWindDir = '&deg;';
var dpBaro = 2;
var dpRain = 2;
//----------------------------------------------------------------------------------------------------

function ajax_set_units( units ) {
//   Establish overall units for  script to use
//   Default is English (like in wflash.txt/wflash2.txt
//   ='M' chooses Metric  C, km/h, hPa, mm, m, km (option for m/s for wind)
//   ="W" forces pull of values from Config/Units.txt file (Weather Flash default settings file)
//
if (units == 'M') { // set to metric
	uomTemp = '&deg;C'; 	uomTempCnvt = 1;
	uomWind = ' km/h';		uomWindCnvt = 1;
	uomBaro = ' hPa'; 		uomBaroCnvt = 3;
	uomRain = ' mm';  		uomRainCnvt = 1;
	uomHeight = ' m'; 		uomHeightCnvt = 1;
	uomDistance = ' km'; 	uomDistanceCnvt = 1;
	dpBaro = 1;
	dpRain = 1;
  }
  if(useKnots) { uomWind = ' kts'; uomWindCnvt = 2;}
  if(useMPS)   { uomWind = ' m/s'; uomWindCnvt = 3;}
  if(useMPH)   { uomWind = ' mph'; uomWindCnvt = 0;}
  if(useFeet)  { uomHeight = ' ft'; uomHeightCnvt = 0;}
  if(usehPa)  { uomBaro = ' hPa'; uomBaroCnvt = 3;}
  if(units == "W") { // get the units to use first (runs once)
    ajaxGetUnits(wflashUnitsFile + '?' + new Date().getTime()); 
  }

} // end ajax_set_units()

ajax_set_units(useunits); // set up the units to ues

// utility function to display UOM based on showUnits flag
function ajaxUOM ( uom ) {
	if (showUnits) { 
		return( uom );
	} else {
		return( "" );
	}
}

// utility function to display UOM based on showUnits flag
function nilWind ( v1, v2 ) {
	if (showNoWind) { 
		return( v1 );
	} else {
		return( v2 );
	}
}

// utility functions to handle conversions from clientraw data to desired units-of-measure
function convertTemp ( rawtemp ) {
	var retval = 0;
	if (uomTempCnvt == 0) { // leave in F
		retval = rawtemp * 1.0 ;
	} else {  // convert to C
		retval = (rawtemp - 32.0) * (100.0/(212.0-32.0));
	}
	return(Math.round(retval*10.0)/10);
}

function convertTempRate ( rawtemp ) {
	var retval = 0;
	if (uomTempCnvt == 0) { // leave in F rate
		retval = rawtemp * 1.0 ;
	} else {  // convert to C rate
		retval = rawtemp * 0.55555 ;
	}
	return(Math.round(retval*10.0)/10);
}

function convertWind  ( rawwind ) {
  var retval = 0;
  switch (uomWindCnvt) { // convert from MPH to
	case 0 : // MPH
  		retval = rawwind * 1.0;
		break;
	case 1: // KPH
		retval = rawwind * 1.609344;
		break;
	case 2: // knots
		retval = rawwind * 0.868976242;
		break;
	case 3: // meters per second
		retval = rawwind * 0.44704;
		break;
	default:
	    retval = rawwind * 1.0;
  }
  
  return (Math.round(retval*10.0)/10);

}

function convertBaro ( rawbaro ) {
  var retval = 0;
  switch (uomBaroCnvt) { // convert from inHg to
	case 0 : // inHg
  		retval = rawbaro * 1.0;
		break;
	case 1: // mmHg
		retval = rawbaro * 25.4;
		break;
	case 2: // mb
		retval = rawbaro * 33.86;
		break;
	case 3: // hPa
		retval = rawbaro * 33.86;
		break;
	default:
	    retval = rawbaro * 1.0;
  
  }
  var fudgeIt = 10.0;
  if (dpBaro == 2) {fudgeIt = 100.0;}
  return (Math.round(retval*fudgeIt)/fudgeIt);
}

function convertRain ( rawrain ) { // convert from inches to
	var retval = 0;
	if (uomRainCnvt == 0) { // leave in in
		retval = rawrain * 1.0 ;
	} else {  // convert to mm
		retval = rawrain * 25.4;
	}
  var fudgeIt = 10.0;
  if (dpRain == 2) {fudgeIt = 100.0;}
  return (Math.round(retval*fudgeIt)/fudgeIt);
}

function convertHeight ( rawheight ) { // convert from feet to
	var retval = 0;
	if (uomHeightCnvt == 0) { // leave in feet
		retval = rawrain * 1.0 ;
	} else {  // convert to meters
		retval = rawrain * 0.3048;
	}
	return(Math.round(retval));
}

function convertDistance ( rawdist ) { // convert from miles to
	var retval = 0;
	if (uomDistanceCnvt == 0) { // leave in miles
		retval = rawdist * 1.0 ;
	} else {  // convert to km
		retval = rawdist * 1.609344;
	}
	return(Math.round(retval*10.0)/10);
}


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
		  element.innerHTML =  value;  // moved to suppress excess 'flashing' .. J McMurry
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
   var windlabel = new Array("N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW");
   return windlabel[Math.floor(((parseInt($winddir) + 11) / 22.5) % 16 )];
}

function windDirLang ($winddir)
// Take wind direction value, return the
// text label based upon 16 point compass -- function by beeker425
//  see http://www.weather-watch.com/smf/index.php/topic,20097.0.html
{
   return langWindDir[Math.floor(((parseInt($winddir) + 11) / 22.5) % 16 )];
}

function ajax_get_beaufort_number ( wind ) { 
// return a number for the beaufort scale based on wind mph
  if (wind < 1 ) {return("0"); }
  if (wind < 4 ) {return("1"); }
  if (wind < 8 ) {return("2"); }
  if (wind < 13 ) {return("3"); }
  if (wind < 19 ) {return("4"); }
  if (wind < 25 ) {return("5"); }
  if (wind < 32 ) {return("6"); }
  if (wind < 39 ) {return("7"); }
  if (wind < 47 ) {return("8"); }
  if (wind < 55 ) {return("9"); }
  if (wind < 64 ) {return("10"); }
  if (wind < 73 ) {return("11"); }
  if (wind >= 73 ) {return("12"); }
  return("0");
}

function ajax_getUVrange ( uv ) { // code simplified by FourOhFour on wxforum.net
   var uvword = "Unspec.";
   if (uv <= 0) {
       uvword = langUVWords[0];
   } else if (uv < 3) {
       uvword = "<span style=\"border: solid 1px; background-color: #A4CE6a;\">&nbsp;"+langUVWords[1]+"&nbsp;</span>";
   } else if (uv < 6) {
       uvword = "<span style=\"border: solid 1px; background-color: #FBEE09;\">&nbsp;"+langUVWords[2]+"&nbsp;</span>";
   } else if (uv < 8) {
       uvword =  "<span style=\"border: solid 1px; background-color: #FD9125;\">&nbsp;"+langUVWords[3]+"&nbsp;</span>";
   } else if (uv < 11) {
       uvword =  "<span style=\"border: solid 1px; color: #FFFFFF; background-color: #F63F37;\">&nbsp;"+langUVWords[4]+"&nbsp;</span>";
   } else {
       uvword =  "<span style=\"border: solid 1px; color: #FFFF00; background-color: #807780;\">&nbsp;"+langUVWords[5]+"&nbsp;</span>";
   }
   return uvword;
} // end ajax_getUVrange function

function ajax_get_barotrend(btrnd) {
// routine from Anole's wxsticker PHP (adapted to JS by Ken True)
// input: trend inHG
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
   if ((btrnd >= -0.02) && (btrnd <= 0.02)) { return(langBaroTrend[0]); }
   if ((btrnd > 0.02) && (btrnd < 0.06)) { return(langBaroTrend[1]); }
   if (btrnd >= 0.06) { return(langBaroTrend[2]); }
   if ((btrnd < -0.02) && (btrnd > -0.06)) { return(langBaroTrend[3]); }
   if (btrnd <= -0.06) { return(langBaroTrend[4]); }
  return(btrnd);
}

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

function ajax_format_time(rawtime) {
// convert 24hr time to 12hr time (for updated time only)
	if (! useAMPM ) {
		return(rawtime); // keep it as 24hr time
	}
	
	var hms = rawtime.split(":");
	
	var amOrPm = "am";
	if (hms[0] > 11) {amOrPm = "pm";}
	if (hms[0] > 12) {hms[0] = hms[0] - 12;}
	if (hms[0] == 0) {hms[0] = 12;}
	return(hms[0] + ":" + hms[1] + ":" + hms[2] + amOrPm);
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
//  function.. read Units.txt and reset the uomVVVV and dpVVVV values as needed .. run once
// ------------------------------------------------------------------------------------------
function ajaxGetUnits(url) {
// read the Units.txt file and set our uomVVVV and dpVVVV values 
// This routine is run once at startup (load of page)
  if (document.getElementById) {
    var x = new ajaxRequest();
	
  }
  if (x) { // got something back
    x.onreadystatechange = function() {
    try { if (x.readyState == 4 && x.status == 200) { // Mike Challis added fix to fix random error: NS_ERROR_NOT_AVAILABLE 
      var wunits = x.responseText.split('&');
	  var t = '';
	  var i = 0;
	  for (i=0;i<wunits.length;i++) {
		  var uparts = wunits[i].split('=');
		  t = t+"'"+uparts[0]+"' = '"+uparts[1]+"'\n";
		  
		  switch (uparts[0]) { // handle the units values
		    case "Distance" :
			  if (uparts[1] == 'miles') { uomDistance = ' miles';	uomDistanceCnvt = 0; }
			  if (uparts[1] == 'km')    { uomDistance = ' km'; 		uomDistanceCnvt = 1; }
			  t = t+"Set='"+uomDistance+"'\n";
			  break;
		    case "Altitude" :
			  if (uparts[1] == 'ft') { uomHeight = ' ft';  	uomHeightCnvt = 0;}
			  if (uparts[1] == 'm') { uomHeight = ' m'; 	uomHeightCnvt = 1;}
			  t = t+"Set='"+uomHeight+"'\n";
			  break;
		    case "Rain" :
			  if (uparts[1] == 'in') { uomRain = ' in'; dpRain = 2;	uomRainCnvt = 0;}
			  if (uparts[1] == 'mm') { uomRain = ' mm'; dpRain = 1;	uomRainCnvt = 1;}
			  t = t+"Set='"+uomRain+"' dpRain='"+dpRain+"'\n";
			  break;
		    case "Wind" :
			  if (uparts[1] == 'mph') { uomWind = ' mph';	uomWindCnvt = 0;}
			  if (uparts[1] == 'kph') { uomWind = ' km/h';	uomWindCnvt = 1;}
			  if (uparts[1] == 'knots') { uomWind = ' kts';	uomWindCnvt = 2;}
			  if (uparts[1] == 'm/s') { uomWind = ' m/s';	uomWindCnvt = 3;}
			  t = t+"Set='"+uomWind+"'\n";
			  break;
		    case "Pressure" :
			  if (uparts[1] == 'inHg') { uomBaro = ' inHg'; dpBaro = 2;		uomBaroCnvt = 0;}
			  if (uparts[1] == 'mmHg') { uomBaro = ' mmHg'; dpBaro = 1;		uomBaroCnvt = 1;}
			  if (uparts[1] == 'mb') { uomBaro = ' mb'; dpBaro = 1;			uomBaroCnvt = 2;}
			  if (uparts[1] == 'hPa') { uomBaro = ' hPa'; dpBaro = 1;		uomBaroCnvt = 3;}
			  t = t+"Set='"+uomBaro+"' dpBaro='"+dpBaro+"'\n";
			  break;
		    case "Temperature" :
			  var tmp = uparts[1];
			  if (tmp.match(/F$/i) ) { uomTemp = '&deg;F';	uomTempCnvt = 0;}
			  if (tmp.match(/C$/i) ) { uomTemp = '&deg;C';	uomTempCnvt = 1;}
			  t = t+"Set='"+uomTemp+"'\n";
			  break;
			default :
		    // no 'default'
		  } // end switch (uparts[0])
		
	  } // end for
//	  alert(t);
      x.abort();

// 	  } // END if(wunits[0] 

	 } // END if (x.readyState == 4 && x.status == 200)

    } // END try

   	catch(e){ }  // Mike Challis added fix to fix random error: NS_ERROR_NOT_AVAILABLE

    } // END x.onreadystatechange = function() {
    x.open("GET", url, true);
    x.send(null);
//	alert("did Open and send of null");
  }
  
} // end ajaxGetUnits function


// ------------------------------------------------------------------------------------------
//  main function.. read wflash.txt and format <span class="ajax" id="ajax..."></span> areas
// ------------------------------------------------------------------------------------------
function ajaxLoaderVWSf(url) {
    var x = new ajaxRequest();

   if (x) { // got something back
    x.onreadystatechange = function() {
    try { if (x.readyState == 4 && x.status == 200) { // Mike Challis added fix to fix random error: NS_ERROR_NOT_AVAILABLE 
    var wflash = x.responseText.split(',');
	// now make sure we got the entire wflash.txt  -- thanks to Johnnywx
	// valid wflash.txt has 'F=nnnnnnnnnn'
	var wdpattern=/F\=(\d+)/; 
	if( wdpattern.test(wflash[0]) &&
		( updates <= maxupdates || maxupdates > 0  )) { // got it.. process wflash.txt
		if (maxupdates > 0 ) {updates++; } // increment counter if needed
        // main routine ---
		var datestamp = wflash[0]; // extracted from the F=() in the first string
		// Note: F=nnnnn: the value is number of seconds since Jan 01, 1900 00:00:00 UTC
		datestamp = datestamp.replace(/^I=\S+\&/i,"");     // remove I= field if present
		datestamp = datestamp.replace(wdpattern,"$1");     // extract timestamp from F=nnnnnnnn field
		var datezero = new Date('Jan 01, 1900 00:00:00 UTC');
		var datetime = new Date();
		datetime.setTime(datestamp*1000 + datezero.getTime()); // adjust date to offset from zero time
		set_ajax_obs("ajaxdatetime",datetime);
		set_ajax_obs("ajaxdatetimelocale",datetime.toLocaleString());
		set_ajax_obs("ajaxdate",datetime.toLocaleDateString());
		set_ajax_obs("ajaxtime",datetime.toLocaleTimeString());
		set_ajax_obs("gizmodate",datetime.toLocaleDateString());
		set_ajax_obs("gizmotime",datetime.toLocaleTimeString());
	
		//BEGIN TEMPERATURE DATA
		//CURRENT TEMPERATURE
		var temperature = convertTemp(wflash[9]);
		set_ajax_obs("ajaxtemp",temperature.toFixed(1)+ajaxUOM(uomTemp));
		set_ajax_obs("gizmotemp",temperature.toFixed(1)+ajaxUOM(uomTemp));
		set_ajax_obs("ajaxtempNoU",temperature.toFixed(1));
		set_ajax_obs("ajaxbigtemp",temperature.toFixed(0) + uomTemp);

        set_ajax_obs("ajaxthermometer",
            "<img src=\"" + thermometer + "?t=" + temperature + "\" " +
            "width=\"54\" height=\"170\" alt=\"Current Temp is " + temperature.toFixed(1)+ajaxUOM(uomTemp) + "\" />" );	
		//CURRENT TEMPERATURE RATE
		var temprate = convertTempRate(wflash[37]);
		set_ajax_obs("ajaxtemprate",temprate.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
		set_ajax_obs("gizmotemprate",temprate.toFixed(1)+ajaxUOM(uomTemp));
	
		var temparrow = ajax_genarrow(temperature*1.0, temperature-temprate*1.0, '', 
			 langTempRising+uomTemp+langTempPerHour,
			 langTempFalling+uomTemp+langTempPerHour,1);
		set_ajax_obs("ajaxtemparrow",temparrow);
		set_ajax_obs("gizmotemparrow",temparrow);
		
		//END TEMPERATURE DATA
	
	
		//BEGIN HEAT INDEX DATA
		//CURRENT HEAT INDEX
		if (wflash[9] >= 80) { // NOAA sez need 80F+ for Heat Index
			var heatindex = convertTemp(wflash[23]);
			set_ajax_obs("ajaxheatidx",heatindex.toFixed(1)+ajaxUOM(uomTemp));
		
			//CURRENT HEAT INDEX RATE
			var heatindexrate = convertTempRate(wflash[51]);
			set_ajax_obs("ajaxheatidxrate",heatindexrate.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
	
			set_ajax_obs("ajaxheatidxarrow", 
			   ajax_genarrow(heatindex*1.0, heatindex-heatindexrate*1.0, '', 
			     langTempRising+uomTemp+langTempPerHour,
			     langTempFalling+uomTemp+langTempPerHour,1)
			);	
		} else {
			set_ajax_obs("ajaxheatidx",'---');
			set_ajax_obs("ajaxheatidxrate",'---');
			set_ajax_obs("ajaxheatidxarrow",'');
		}
		//END HEAT INDEX DATA
	
	
		//BEGIN WIND CHILL DATA
		//CURRENT WIND CHILL
		if (wflash[9] <= 40) { // NOAA sez Wind Chill starts at 40F
			var windchill = convertTemp(wflash[21]);
			set_ajax_obs("ajaxwindchill",windchill.toFixed(1)+ajaxUOM(uomTemp));
		
			//CURRENT WIND CHILL RATE
			var windchillrate = convertTempRate(wflash[49]);
			set_ajax_obs("ajaxwindchillrate",windchillrate.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
		
			set_ajax_obs("ajaxwindchillarrow", 
			   ajax_genarrow(windchill*1.0, windchill-windchillrate*1.0, '', 
			     langTempRising+uomTemp+langTempPerHour,
			     langTempFalling+uomTemp+langTempPerHour,1)
			);	
		} else {
			set_ajax_obs("ajaxwindchill",'---');
			set_ajax_obs("ajaxwindchillrate",'---');
			set_ajax_obs("ajaxwindchillarrow",'');
		}
		//END WIND CHILL DATA
	
		//BEGIN FEELS-LIKE
		if (wflash[9] <= 40) { // NOAA sez Wind Chill starts at 40F
			set_ajax_obs("ajaxfeelslike",windchill.toFixed(1)+ajaxUOM(uomTemp));
		} 
		if (wflash[9] >= 80) { // NOAA sez need 80F+ for Heat Index
			set_ajax_obs("ajaxfeelslike",heatindex.toFixed(1)+ajaxUOM(uomTemp));
		} 
        if (wflash[9] > 40 && wflash[9] < 80) {
			set_ajax_obs("ajaxfeelslike",temperature.toFixed(1)+ajaxUOM(uomTemp));
  	    }
		//END FEELS-LIKE
	
		//BEGIN PRESSURE DATA
		//CURRENT PRESSURE (Sea Level)
		var pressure = convertBaro(wflash[25]);
		set_ajax_obs("ajaxbaro",pressure.toFixed(dpBaro)+ajaxUOM(uomBaro));
		set_ajax_obs("gizmobaro",pressure.toFixed(dpBaro)+ajaxUOM(uomBaro));
	
		//CURRENT PRESSURE RATE (Sea Level)
		var barometerrate = convertBaro(wflash[53]);
		set_ajax_obs("ajaxbarorate",barometerrate.toFixed(dpBaro)+ajaxUOM(uomBaro)+ajaxUOM(uomPerHr));
		set_ajax_obs("gizmobarorate",barometerrate.toFixed(dpBaro)+ajaxUOM(uomBaro)+ajaxUOM(uomPerHr));
	
		var baroarrow =	   ajax_genarrow(pressure*1.0, pressure-barometerrate*1.0, '', 
			     langBaroRising+uomBaro+langBaroPerHour,
			     langBaroFalling+uomBaro+langBaroPerHour,1);
		set_ajax_obs("ajaxbaroarrow",baroarrow);
		set_ajax_obs("gizmobaroarrow",baroarrow);
		var barotrendtext = ajax_get_barotrend(wflash[53]*1.0);
		set_ajax_obs("ajaxbarotrend",barotrendtext );
		set_ajax_obs("gizmobarotrend",barotrendtext );
		set_ajax_obs("ajaxbarotrendtext",barotrendtext);
		set_ajax_obs("gizmobarotrendtext",barotrendtext);

		//END PRESSURE DATA
 

		//BEGIN RAW BAROMETER DATA
		//CURRENT RAW BAROMETER
		var rawbaro = convertBaro(wflash[10]);
		set_ajax_obs("ajaxrawbaro",rawbaro.toFixed(dpBaro)+ajaxUOM(uomBaro));
	
		//CURRENT RAW BAROMETER RATE
		var rawbarorate = convertBaro(wflash[38]);
		set_ajax_obs("ajaxrawbarorate",rawbarorate.toFixed(dpBaro)+ajaxUOM(uomBaro)+ajaxUOM(uomPerHr));
	
		set_ajax_obs("ajaxrawbaroarrow",
			   ajax_genarrow(rawbaro*1.0, rawbaro-rawbarorate*1.0, '', 
			     langBaroRising+uomBaro+langBaroPerHour,
			     langBaroFalling+uomBaro+langBaroPerHour,1)
		);	
		set_ajax_obs("ajaxrawbarotrend",ajax_get_barotrend(wflash[38]*1.0) );
		//END RAW BAROMETER DATA
	
		//BEGIN WIND DATA
	
		//CURRENT WIND GUST RATE
		var gustrate = convertWind(wflash[33]);
		set_ajax_obs("ajaxgustrate",gustrate+ajaxUOM(uomWind)+ajaxUOM(uomPerHr));
	
		//CURRENT WIND RATE
		var windrate = convertWind(wflash[32]);
		set_ajax_obs("ajaxwindrate",windrate+ajaxUOM(uomWind)+ajaxUOM(uomPerHr));
	
		//WIND DIRECTION DATA
		var winddir2 = wflash[3];
		winddir2 = winddir2 * 1.0;
		set_ajax_obs("ajaxwinddir2",winddir2.toFixed(0)+ajaxUOM(uomWindDir));
	
		//CURRENT WIND GUST
		var gust = convertWind(wflash[5]);
 
 		//CURRENT WIND SPEED
		var wind = convertWind(wflash[4]);
		
		set_ajax_obs("ajaxwinduom",uomWind);
		set_ajax_obs("ajaxgustuom",uomWind);


		var windcardinal = windDir(wflash[3]);
	    var windcardinalLang = windDirLang(wflash[3]);  // for language translation
	
		if (wind >= 0.1) {
			set_ajax_obs("ajaxwind",wind.toFixed(1)+uomWind);
			set_ajax_obs("gizmowind",wind.toFixed(1)+uomWind);
			set_ajax_uom("ajaxwinduom",true);
		} else {
			set_ajax_obs("ajaxwind",nilWind(langWindCalm,'0'));
			set_ajax_obs("gizmowind",nilWind(langWindCalm,'0'));
			set_ajax_uom("ajaxwinduom",false);
		}
	
		if (gust > 0.0) {
			set_ajax_obs("ajaxgust",gust.toFixed(1)+uomWind);
			set_ajax_obs("gizmogust",gust.toFixed(1)+uomWind);
			set_ajax_uom("ajaxgustuom",true);
		} else {
			set_ajax_obs("ajaxgust",nilWind(langGustNone,'0'));
			set_ajax_obs("gizmogust",nilWind(langGustNone,'0'));
			set_ajax_uom("ajaxgustuom",false);
		}
	
		if (gust > 0.0 || wind > 0.0) {
			var windicon = "<img src=\""+imagedir+"/"+windcardinal + ".gif\" width=\"12\" height=\"12\" alt=\"" + 
						 langWindFrom + windcardinalLang + "\" title=\"" + 
						 langWindFrom + windcardinalLang + "\" /> ";
 		    set_ajax_obs("ajaxwindicon",windicon);
		    set_ajax_obs("gizmowindicon",windicon);
			set_ajax_obs("ajaxwindiconwr",
		                "<img src=\"" + imagedir + "/" +wrName +  windcardinal + wrType + "\" width=\""+
		                wrWidth+"\" height=\""+wrHeight+"\" alt=\"" + 
		                langWindFrom + windcardinalLang + "\" title=\"" +
						langWindFrom + windcardinalLang + "\" /> ");
			set_ajax_obs("ajaxwinddir",windcardinalLang);
			set_ajax_obs("gizmowinddir",windcardinalLang);
		} else {
			var nilwindicon = nilWind(" ",
				"<img src=\""+imagedir+"/"+windcardinal + ".gif\" width=\"12\" height=\"12\" alt=\"" +
			    langWindFrom + windcardinalLang + "\" title=\"" + 
				langWindFrom + windcardinalLang + "\" /> ");
			set_ajax_obs("ajaxwindicon",nilwindicon);
			set_ajax_obs("ajaxwinddir",nilWind('',windcardinalLang));
			set_ajax_obs("gizmowinddir",nilWind('',windcardinalLang));
		    if (wrCalm != '') {
 		      set_ajax_obs("ajaxwindiconwr",
		      "<img src=\"" + imagedir + "/" + wrCalm + "\" width=\""+
		      wrWidth+"\" height=\""+wrHeight+"\" alt=\"" + 
		      langBeaufort[0] + "\" title=\"" +langBeaufort[0] + "\" /> ");
		    }
		}
		
		var beaufortnum = ajax_get_beaufort_number(wflash[4]); // calculate beaufort from wind speed
		set_ajax_obs("ajaxbeaufortnum",beaufortnum);
		set_ajax_obs("ajaxbeaufort",langBeaufort[beaufortnum]); // so we can translate if necess.
	
		//CURRENT WIND RATE
		var winddirrate = convertWind(wflash[31]);
		set_ajax_obs("ajaxwinddirrate",winddirrate+ajaxUOM(uomWindDir)+ajaxUOM(uomPerHr));
		//END WIND DATA
	
	
		//BEGIN RAIN DATA
		//YEARLY RAIN
		var rainyr = convertRain(wflash[11]);
		rainyr = rainyr * 1.0;
		set_ajax_obs("ajaxrainyr",rainyr.toFixed(dpRain)+ajaxUOM(uomRain));
		//END RAIN DATA
		//NOTE THAT OTHER RAIN DATA IS IN THE WFLASH2.TXT FILE SO IT IS IN THE SCRIPT BELOW THIS ONE
	
	
		//BEGIN HUMIDITY DATA
		//CURRENT HUMIDITY
		var humidity = wflash[7];
		humidity = humidity * 1.0;
		set_ajax_obs("ajaxhumidity",humidity.toFixed(0)+ajaxUOM(uomHumid));
		set_ajax_obs("gizmohumidity",humidity.toFixed(0)+ajaxUOM(uomHumid));
	
		//CURRENT HUMIDITY RATE
		var humidityrate = wflash[35];
		humidityrate = humidityrate * 1.0;
		set_ajax_obs("ajaxhumidityrate",humidityrate.toFixed(1)+ajaxUOM(uomHumid)+ajaxUOM(uomPerHr));
		set_ajax_obs("ajaxhumidityarrow", 
			   ajax_genarrow(humidity*1.0, humidity-humidityrate*1.0, '', 
			     langHumRising+'%'+langHumPerHour,
			     langHumFalling+'%'+langHumPerHour,1)
		);	
		//END HUMIDITY DATA
	
	
		//BEGIN DEW POINT DATA
		//CURRENT DEW POINT
		var dew = convertTemp(wflash[24]);
		set_ajax_obs("ajaxdew",dew.toFixed(1)+ajaxUOM(uomTemp));
        set_ajax_obs("gizmodew",dew.toFixed(1)+ajaxUOM(uomTemp));
	
		//CURRENT DEW POINT RATE
		var dewrate = convertTempRate(wflash[52]);
        set_ajax_obs("ajaxdewrate",dewrate.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
		set_ajax_obs("ajaxdewarrow", 
			   ajax_genarrow(dew*1.0, dew-dewrate*1.0, '', 
			     langTempRising+uomTemp+langTempPerHour,
			     langTempFalling+uomTemp+langTempPerHour,1)
		);	
		//END DEW POINT DATA
	
	
		//Current UV Index
	
		var uv = wflash[19];
		uv = uv * 1.0;
		set_ajax_obs("ajaxuv",uv.toFixed(1));
	
		var uvrate = wflash[47];
		uvrate = uvrate * 1.0;
		set_ajax_obs("ajaxuvrate",uvrate.toFixed(1)+ajaxUOM(uomPerHr));
		set_ajax_obs("gizmouvrate",uvrate.toFixed(1)+ajaxUOM(uomPerHr));
	
		var	uvword = ajax_getUVrange(uv);
		set_ajax_obs("ajaxuvword",uvword);
		set_ajax_obs("gizmouvword",uvword);
	
		//Current Solar Radiation
		var solar = wflash[20];
		solar = solar * 1.0;
		set_ajax_obs("ajaxsolar",solar.toFixed(0)+ajaxUOM(uomSolar));
	
		var solarrate = wflash[48];
		solarrate = solarrate * 1.0;
		set_ajax_obs("ajaxsolarrate",solarrate.toFixed(0)+ajaxUOM(uomSolar)+ajaxUOM(uomPerHr));
	
		//Current Evaportranspiration
		var et = convertRain(wflash[18]);
		set_ajax_obs("ajaxet",et+ajaxUOM(uomRain));
	
		var etrate = convertRain(wflash[46]);
		etrate = etrate * 1.0;
		set_ajax_obs("ajaxetrate",etrate.toFixed(dpRain+1)+ajaxUOM(uomRain)+ajaxUOM(uomPerHr));
	
		//UPDATED TIME AND DATE
		// note: date will be from wflash2[275]

		var ajaxtime = wflash[1];
		ajaxtime = ajaxtime.replace( "+" , "0");
		ajaxtime = ajax_format_time(ajaxtime);
		set_ajax_obs("ajaxtime",ajaxtime);
 
  		if (lastajaxtime != ajaxtime) {
			counterSecs = 0;                      // reset timer
			lastajaxtime = ajaxtime; // remember this time
		}


		// now ensure that the indicator flashes on every AJAX fetch
        var element = document.getElementById("ajaxindicator");
		if (element) {
          element.style.color = flashcolor;
		}
        element = document.getElementById("gizmoindicator");
		if (element) {
          element.style.color = flashcolor;
		}
		if (maxupdates > 0 && updates > maxupdates-1) { /* chg indicator to pause message */
			set_ajax_obs("ajaxindicator",langPauseMsg);
			set_ajax_obs("gizmoindicator",langPauseMsg);
		}
		set_ajax_obs('ajaxupdatecount',updates);       /* for test pages */
		set_ajax_obs('ajaxmaxupdatecount',maxupdates); /* for test pages */

 	  } // END if(wflash[0] 

	 } // END if (x.readyState == 4 && x.status == 200)

    } // END try

   	catch(e){}  // Mike Challis added fix to fix random error: NS_ERROR_NOT_AVAILABLE

    } // END x.onreadystatechange = function() {
    x.open("GET", url, true);
    x.send(null);

	setTimeout("reset_ajax_color('')",flashtime); // change text back to default color 
	if ( (maxupdates == 0) || (updates < maxupdates-1)) {
      setTimeout("ajaxLoaderVWSf(wflashFile + '?' + new Date().getTime())", reloadTime); // get new data after 5 secs
	}
  }
} // end ajaxLoaderVWSf function

// ------------------------------------------------------------------------------------------
//  main function.. read wflash2.txt and format <span class="ajax" id="ajax..."></span> areas
// ------------------------------------------------------------------------------------------
function ajaxLoaderVWSf2(url) {
  var x = new ajaxRequest();
  if (x) { // got something back
    x.onreadystatechange = function() {
    try { if (x.readyState == 4 && x.status == 200) { // Mike Challis added fix to fix random error: NS_ERROR_NOT_AVAILABLE 
    var wflash2 = x.responseText.split(',');
	// now make sure we got the entire wflash.txt  -- thanks to Johnnywx
	// valid wflash2.txt has 'S=HH:MM:SS'
	var wdpattern=/S\=.*:/; // looks for 'S=HH:MM:SS' timestamp
	if( wdpattern.test(wflash2[0]) ) { // got it.. process wflash2.txt
        // main routine ---

		//BEGIN TEMPERATURE DATA
		//High AND TIME TEMPERATURE
		var hightemperature = convertTemp(wflash2[36]);
		set_ajax_obs("ajaxhightemp",hightemperature.toFixed(1)+ajaxUOM(uomTemp));
		var hightemperaturetime = wflash2[64];
		set_ajax_obs("ajaxhightemptime",hightemperaturetime) ;
		
		//LOW AND TIME TEMPERATURE
		var lowtemperature = convertTemp(wflash2[92]);
		set_ajax_obs("ajaxlowtemp",lowtemperature.toFixed(1)+ajaxUOM(uomTemp));
		var lowtemperaturetime = wflash2[120];
		set_ajax_obs("ajaxlowtemptime",lowtemperaturetime) ;
		
		//AVERAGE TEMPERATURE
		var avgtemperature = convertTemp(wflash2[8]);
		set_ajax_obs("ajaxavgtemp",avgtemperature.toFixed(1)+ajaxUOM(uomTemp));
		
		//High Rate AND TIME TEMPERATURE
		var highratetemperature = convertTempRate(wflash2[148]);
		set_ajax_obs("ajaxhighratetemp",highratetemperature.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
		var highratetemperaturetime = wflash2[176];
		set_ajax_obs("ajaxhighratetemptime",highratetemperaturetime) ;
		
		//Low Rate AND TIME TEMPERATURE
		var lowratetemperature = convertTempRate(wflash2[204]);
		set_ajax_obs("ajaxlowratetemp",lowratetemperature.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
		var lowratetemperaturetime = wflash2[232];
		set_ajax_obs("ajaxlowratetemptime",lowratetemperaturetime) ;
		//END TEMEPRATURE DATA
		
		
		
		//BEGIN DEW POINT DATA
		//High AND TIME DEW POINT
		var highdewpoint = convertTemp(wflash2[51]);
		set_ajax_obs("ajaxhighdew",highdewpoint.toFixed(1)+ajaxUOM(uomTemp));
		var highdewpointtime = wflash2[79];
		set_ajax_obs("ajaxhighdewtime",highdewpointtime) ;
		
		//LOW AND TIME DEW POINT
		var lowdewpoint = convertTemp(wflash2[107]);
		set_ajax_obs("ajaxlowdew",lowdewpoint.toFixed(1)+ajaxUOM(uomTemp));
		var lowdewpointtime = wflash2[135];
		set_ajax_obs("ajaxlowdewtime",lowdewpointtime) ;
		
		//AVERAGE DEW POINT
		var avgdewpoint = convertTemp(wflash2[23]);
		set_ajax_obs("ajaxavgdew",avgdewpoint.toFixed(1)+ajaxUOM(uomTemp));
		
		//High Rate AND TIME DEW POINT
		var highratedewpoint = convertTempRate(wflash2[163]);
		set_ajax_obs("ajaxhighratedew",highratedewpoint.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
		var highratedewpointtime = wflash2[191];
		set_ajax_obs("ajaxhighratedewtime",highratedewpointtime) ;
		
		//Low Rate AND TIME DEW POINT
		var lowratedewpoint = convertTempRate(wflash2[219]);
		set_ajax_obs("ajaxlowratedew",lowratedewpoint.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
		var lowratedewpointtime = wflash2[247];
		set_ajax_obs("ajaxlowratedewtime",lowratedewpointtime) ;
		//END DEW POINT DATA
		
		
		
		//BEGIN HUMIDITY DATA
		//High AND TIME HUMIDITY
		var highhumidity = wflash2[34];
		highhumidity = highhumidity * 1.0;
		set_ajax_obs("ajaxhighhumidity",highhumidity.toFixed(0)+ajaxUOM(uomHumid));
		var highhumiditytime = wflash2[62];
		set_ajax_obs("ajaxhighhumiditytime",highhumiditytime) ;
		
		//LOW AND TIME HUMIDITY
		var lowhumidity = wflash2[90];
		lowhumidity = lowhumidity * 1.0;
		set_ajax_obs("ajaxlowhumidity",lowhumidity.toFixed(0)+ajaxUOM(uomHumid));
		var lowhumiditytime = wflash2[118];
		set_ajax_obs("ajaxlowhumiditytime",lowhumiditytime) ;
		
		//AVERAGE HUMIDITY
		var avghumidity = wflash2[6];
		avghumidity = avghumidity * 1.0;
		set_ajax_obs("ajaxavghumidity",avghumidity.toFixed(0)+ajaxUOM(uomHumid));
		
		//High Rate AND TIME HUMIDITY
		var highratehumidity = wflash2[146];
		highratehumidity = highratehumidity * 1.0;
		set_ajax_obs("ajaxhighratehumidity",highratehumidity.toFixed(0)+ajaxUOM(uomHumid)+ajaxUOM(uomPerHr));
		var highratehumiditytime = wflash2[174];
		set_ajax_obs("ajaxhighratehumiditytime",highratehumiditytime) ;
		
		//Low Rate AND TIME HUMIDITY
		var lowratehumidity = wflash2[202];
		lowratehumidity = lowratehumidity * 1.0;
		set_ajax_obs("ajaxlowratehumidity",lowratehumidity.toFixed(0)+ajaxUOM(uomHumid)+ajaxUOM(uomPerHr));
		var lowratehumiditytime = wflash2[230];
		set_ajax_obs("ajaxlowratehumiditytime",lowratehumiditytime) ;
		//END HUMIDITY DATA
		
		
		
		//BEGIN WIND SPEED DATA
		//High AND TIME WIND SPEED
		var highwindspeed = convertWind(wflash2[31]);
		set_ajax_obs("ajaxhighwind",highwindspeed+ajaxUOM(uomWind));
		var highwindspeedtime = wflash2[59];
		set_ajax_obs("ajaxhighwindtime",highwindspeedtime) ;
		
		//LOW AND TIME WIND SPEED
		var lowwindspeed = convertWind(wflash2[87]);
		set_ajax_obs("ajaxlowwind",lowwindspeed+ajaxUOM(uomWind));
		var lowwindspeedtime = wflash2[115];
		set_ajax_obs("ajaxlowwindtime",lowwindspeedtime) ;
		
		//AVERAGE WIND SPEED
		var avgwindspeed = convertWind(wflash2[3]);
		set_ajax_obs("ajaxavgwind",avgwindspeed+ajaxUOM(uomWind));
		
		//High Rate AND TIME WIND SPEED
		var highratewindspeed = convertWind(wflash2[143]);
		set_ajax_obs("ajaxhighratewind",highratewindspeed+ajaxUOM(uomWind)+ajaxUOM(uomPerHr));
		var highratewindspeedtime = wflash2[171];
		set_ajax_obs("ajaxhighratewindtime",highratewindspeedtime) ;
		
		//Low Rate AND TIME WIND SPEED
		var lowratewindspeed = convertWind(wflash2[199]);
		set_ajax_obs("ajaxlowratewind",lowratewindspeed+ajaxUOM(uomWind)+ajaxUOM(uomPerHr));
		var lowratewindspeedtime = wflash2[227];
		set_ajax_obs("ajaxlowratewindtime",lowratewindspeedtime) ;
		//END WIND SPEED DATA
		
		
		
		//BEGIN WIND GUST DATA
		//High AND TIME WIND GUST
		var highwindgust = convertWind(wflash2[32]);
		set_ajax_obs("ajaxhighgust",highwindgust.toFixed(1)+ajaxUOM(uomWind));
		var highwindgusttime = wflash2[60];
		set_ajax_obs("ajaxhighgusttime",highwindgusttime) ;
		
		//LOW AND TIME WIND GUST
		var lowwindgust = convertWind(wflash2[88]);
		set_ajax_obs("ajaxlowgust",lowwindgust.toFixed(1)+ajaxUOM(uomWind));
		var lowwindgusttime = wflash2[116];
		set_ajax_obs("ajaxlowgusttime",lowwindgusttime) ;
		
		//AVERAGE WIND GUST
		var avgwindgust = convertWind(wflash2[4]);
		set_ajax_obs("ajaxavggust",avgwindgust.toFixed(1)+ajaxUOM(uomWind));
		
		//High Rate AND TIME GUST SPEED
		var highratewindgust = convertWind(wflash2[144]);
		set_ajax_obs("ajaxhighrategust",highratewindgust.toFixed(1)+ajaxUOM(uomWind)+ajaxUOM(uomPerHr));
		var highratewindgusttime = wflash2[172];
		set_ajax_obs("ajaxhighrategusttime",highratewindgusttime) ;
		
		//Low Rate AND TIME GUST SPEED
		var lowratewindgust = convertWind(wflash2[200]);
		lowratewindgust = lowratewindgust * 1.0;
		set_ajax_obs("ajaxlowrategust",lowratewindgust.toFixed(1)+ajaxUOM(uomWind)+ajaxUOM(uomPerHr));
		var lowratewindgusttime = wflash2[228];
		set_ajax_obs("ajaxlowrategusttime",lowratewindgusttime) ;
		//END WIND GUST DATA
		
		
		
		//BEGIN WIND Direction DATA
		//High AND TIME Direction
		var highwinddirection = wflash2[30];
		highwinddirection = highwinddirection * 1.0;
		set_ajax_obs("ajaxhighwinddir",highwinddirection.toFixed(0)+ajaxUOM(uomWindDir));
		var highwinddirectiontime = wflash2[58];
		set_ajax_obs("ajaxhighwinddirtime",highwinddirectiontime) ;
		
		//LOW AND TIME WIND Direction
		var lowwinddirection = wflash2[86];
		lowwinddirection = lowwinddirection * 1.0;
		set_ajax_obs("ajaxlowwinddir",lowwinddirection.toFixed(0)+ajaxUOM(uomWindDir));
		var lowwinddirectiontime = wflash2[114];
		set_ajax_obs("ajaxlowwinddirtime",lowwinddirectiontime) ;
		
		//AVERAGE WIND Direction
		var avgwinddirection = wflash2[2];
		avgwinddirection = avgwinddirection * 1.0;
		set_ajax_obs("ajaxavgwinddir",avgwinddirection.toFixed(0)+ajaxUOM(uomWindDir));
		
		//High Rate AND TIME Direction
		var highratewinddirection = wflash2[142];
		highratewinddirection = highratewinddirection * 1.0;
		set_ajax_obs("ajaxhighratewinddir",highratewinddirection.toFixed(0)+ajaxUOM(uomWindDir)+ajaxUOM(uomPerHr));
		var highratewinddirectiontime = wflash2[170];
		set_ajax_obs("ajaxhighratewinddirtime",highratewinddirectiontime) ;
		
		//Low Rate AND TIME Direction
		var lowratewinddirection = wflash2[198];
		lowratewinddirection = lowratewinddirection * 1.0;
		set_ajax_obs("ajaxlowratewinddir",lowratewinddirection.toFixed(0)+ajaxUOM(uomWindDir)+ajaxUOM(uomPerHr));
		var lowratewinddirectiontime = wflash2[226];
		set_ajax_obs("ajaxlowratewinddirtime",lowratewinddirectiontime) ;
		//END WIND Direction DATA
		
		
		
		//BEGIN BAROMETER DATA (Sea Level Pressure)
		//High AND TIME BAROMETER
		var highbarometer = convertBaro(wflash2[52]);
		set_ajax_obs("ajaxhighbarometer",highbarometer.toFixed(dpBaro)+ajaxUOM(uomBaro));
		var highbarometertime = wflash2[80];
		set_ajax_obs("ajaxhighbarometertime",highbarometertime) ;
		
		//LOW AND TIME BAROMETER
		var lowbarometer = convertBaro(wflash2[108]);
		set_ajax_obs("ajaxlowbarometer",lowbarometer.toFixed(dpBaro)+ajaxUOM(uomBaro));
		var lowbarometertime = wflash2[136];
		set_ajax_obs("ajaxlowbarometertime",lowbarometertime) ;
		
		//AVERAGE BAROMETER
		var avgbarometer = convertBaro(wflash2[24]);
		set_ajax_obs("ajaxavgbarometer",avgbarometer.toFixed(dpBaro)+ajaxUOM(uomBaro));
		
		//High Rate AND TIME BAROMETER
		var highratebarometer = convertBaro(wflash2[164]);
		set_ajax_obs("ajaxhighratebarometer",highratebarometer.toFixed(dpBaro)+ajaxUOM(uomBaro)+ajaxUOM(uomPerHr));
		var highratebarometertime = wflash2[192];
		set_ajax_obs("ajaxhighratebarometertime",highratebarometertime) ;
		
		//Low Rate AND TIME BAROMETER
		var lowratebarometer = convertBaro(wflash2[220]);
		set_ajax_obs("ajaxlowratebarometer",lowratebarometer.toFixed(dpBaro)+ajaxUOM(uomBaro)+ajaxUOM(uomPerHr));
		var lowratebarometertime = wflash2[248];
		set_ajax_obs("ajaxlowratebarometertime",lowratebarometertime) ;
		//END BAROMETER DATA


		//BEGIN RAW BAROMETER DATA
		//High AND TIME BAROMETER
		var highrawbaro = convertBaro(wflash2[37]);
		set_ajax_obs("ajaxhighrawbaro",highrawbaro.toFixed(dpBaro)+ajaxUOM(uomBaro));
		var highrawbarotime = wflash2[65];
		set_ajax_obs("ajaxhighrawbarotime",highrawbarotime) ;
		
		//LOW AND TIME BAROMETER
		var lowrawbaro = convertBaro(wflash2[93]);
		set_ajax_obs("ajaxlowrawbaro",lowrawbaro.toFixed(dpBaro)+ajaxUOM(uomBaro));
		var lowrawbarotime = wflash2[121];
		set_ajax_obs("ajaxlowrawbarotime",lowrawbarotime) ;
		
		//AVERAGE BAROMETER
		var avgrawbaro = convertBaro(wflash2[9]);
		avgrawbaro = avgrawbaro * 1.0;
		set_ajax_obs("ajaxavgrawbaro",avgrawbaro.toFixed(dpBaro)+ajaxUOM(uomBaro));
		
		//High Rate AND TIME BAROMETER
		var highraterawbaro = convertBaro(wflash2[149]);
		set_ajax_obs("ajaxhighraterawbaro",highraterawbaro.toFixed(dpBaro)+ajaxUOM(uomBaro)+ajaxUOM(uomPerHr));
		var highraterawbarotime = wflash2[177];
		set_ajax_obs("ajaxhighraterawbarotime",highraterawbarotime) ;
		
		//Low Rate AND TIME BAROMETER
		var lowraterawbaro = convertBaro(wflash2[205]);
		set_ajax_obs("ajaxlowraterawbaro",lowraterawbaro.toFixed(dpBaro)+ajaxUOM(uomBaro)+ajaxUOM(uomPerHr));
		var lowraterawbarotime = wflash2[233];
		set_ajax_obs("ajaxlowraterawbarotime",lowraterawbarotime) ;
		//END RAW BAROMETER DATA
		
		
		//BEGIN HEAT INDEX DATA
		//High AND TIME HEAT INDEX
		var highheatidx = convertTemp(wflash2[50]);
		set_ajax_obs("ajaxhighheatidx",highheatidx.toFixed(1)+ajaxUOM(uomTemp));
		var highheatidxtime = wflash2[78];
		set_ajax_obs("ajaxhighheatidxtime",highheatidxtime) ;
		
		//LOW AND TIME HEAT INDEX
		var lowheatidx = convertTemp(wflash2[106]);
		set_ajax_obs("ajaxlowheatidx",lowheatidx.toFixed(1)+ajaxUOM(uomTemp));
		var lowheatidxtime = wflash2[134];
		set_ajax_obs("ajaxlowheatidxtime",lowheatidxtime) ;
		
		//AVERAGE HEAT INDEX
		var avgheatidx = convertTemp(wflash2[22]);
		set_ajax_obs("ajaxavgheatidx",avgheatidx.toFixed(1)+ajaxUOM(uomTemp));
		
		//High Rate AND TIME HEAT INDEX
		var highrateheatidx = convertTempRate(wflash2[162]);
		set_ajax_obs("ajaxhighrateheatidx",highrateheatidx.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
		var highrateheatidxtime = wflash2[190];
		set_ajax_obs("ajaxhighrateheatidxtime",highrateheatidxtime) ;
		
		//Low Rate AND TIME HEAT INDEX
		var lowrateheatidx = convertTempRate(wflash2[218]);
		set_ajax_obs("ajaxlowrateheatidx",lowrateheatidx.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
		var lowrateheatidxtime = wflash2[246];
		set_ajax_obs("ajaxlowrateheatidxtime",lowrateheatidxtime) ;
		//END HEAT INDEX DATA
		
		
		
		//BEGIN WIND CHILL DATA
		//High AND TIME WIND CHILL
		var highwindchill = convertTemp(wflash2[48]);
		set_ajax_obs("ajaxhighwindchill",highwindchill.toFixed(1)+ajaxUOM(uomTemp));
		var highwindchilltime = wflash2[76];
		set_ajax_obs("ajaxhighwindchilltime",highwindchilltime) ;
		
		//LOW AND TIME WIND CHILL
		var lowwindchill = convertTemp(wflash2[104]);
		set_ajax_obs("ajaxlowwindchill",lowwindchill.toFixed(1)+ajaxUOM(uomTemp));
		var lowwindchilltime = wflash2[132];
		set_ajax_obs("ajaxlowwindchilltime",lowwindchilltime) ;
		
		//AVERAGE WIND CHILL
		var avgwindchill = convertTemp(wflash2[20]);
		set_ajax_obs("ajaxavgwindchill",avgwindchill.toFixed(1)+ajaxUOM(uomTemp));
		
		//High Rate AND TIME WIND CHILL
		var highratewindchill = convertTempRate(wflash2[160]);
		set_ajax_obs("ajaxhighratewindchill",highratewindchill.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
		var highratewindchilltime = wflash2[188];
		set_ajax_obs("ajaxhighratewindchilltime",highratewindchilltime) ;
		
		//Low Rate AND TIME WIND CHILL
		var lowratewindchill = convertTempRate(wflash2[216]);
		set_ajax_obs("ajaxlowratewindchill",lowratewindchill.toFixed(1)+ajaxUOM(uomTemp)+ajaxUOM(uomPerHr));
		var lowratewindchilltime = wflash2[244];
		set_ajax_obs("ajaxlowratewindchilltime",lowratewindchilltime) ;
		//END WIND CHILL DATA
		
		//BEGIN UV DATA
		//High AND TIME UV
		var highuv = wflash2[46];
		highuv = highuv * 1.0;
		set_ajax_obs("ajaxhighuv",highuv.toFixed(1));
		var highuvtime = wflash2[74];
		set_ajax_obs("ajaxhighuvtime",highuvtime) ;
		
		//LOW AND TIME UV
		var lowuv = wflash2[102];
		lowuv = lowuv * 1.0;
		set_ajax_obs("ajaxlowuv",lowuv.toFixed(1));
		var lowuvtime = wflash2[130];
		set_ajax_obs("ajaxlowuvtime",lowuvtime) ;
		
		//AVERAGE UV
		var avguv = wflash2[18];
		avguv = avguv * 1.0;
		set_ajax_obs("ajaxavguv",avguv.toFixed(1));
		
		//High Rate AND TIME UV
		var highrateuv = wflash2[158];
		highrateuv = highrateuv * 1.0;
		set_ajax_obs("ajaxhighrateuv",highrateuv.toFixed(1)+ajaxUOM(uomPerHr));
		var highrateuvtime = wflash2[186];
		set_ajax_obs("ajaxhighrateuvtime",highrateuvtime) ;
		
		//Low Rate AND TIME UV
		var lowrateuv = wflash2[214];
		lowrateuv = lowrateuv * 1.0;
		set_ajax_obs("ajaxlowrateuv",lowrateuv.toFixed(1)+ajaxUOM(uomPerHr));
		var lowrateuvtime = wflash2[242];
		set_ajax_obs("ajaxlowrateuvtime",lowrateuvtime) ;
		//END UV DATA
		
		
		//BEGIN SOLAR DATA
		//High AND TIME SOLAR
		var highsolar = wflash2[47];
		highsolar = highsolar * 1.0;
		set_ajax_obs("ajaxhighsolar",highsolar.toFixed(0)+ajaxUOM(uomSolar));
		var highsolartime = wflash2[75];
		set_ajax_obs("ajaxhighsolartime",highsolartime) ;
		
		//LOW AND TIME SOLAR
		var lowsolar = wflash2[103];
		lowsolar = lowsolar * 1.0;
		set_ajax_obs("ajaxlowsolar",lowsolar.toFixed(0)+ajaxUOM(uomSolar));
		var lowsolartime = wflash2[131];
		set_ajax_obs("ajaxlowsolartime",lowsolartime) ;
		
		//AVERAGE SOLAR
		var avgsolar = wflash2[19];
		avgsolar = avgsolar * 1.0;
		set_ajax_obs("ajaxavgsolar",avgsolar.toFixed(0)+ajaxUOM(uomSolar));
		
		//High Rate AND TIME SOLAR
		var highratesolar = wflash2[159];
		highratesolar = highratesolar * 1.0;
		set_ajax_obs("ajaxhighratesolar",highratesolar.toFixed(0)+ajaxUOM(uomSolar)+ajaxUOM(uomPerHr));
		var highratesolartime = wflash2[187];
		set_ajax_obs("ajaxhighratesolartime",highratesolartime) ;
		
		//Low Rate AND TIME SOLAR
		var lowratesolar = wflash2[215];
		lowratesolar = lowratesolar * 1.0;
		set_ajax_obs("ajaxlowratesolar",lowratesolar.toFixed(0)+ajaxUOM(uomSolar)+ajaxUOM(uomPerHr));
		var lowratesolartime = wflash2[243];
		set_ajax_obs("ajaxlowratesolartime",lowratesolartime) ;
		//END SOLAR DATA
		
		
		//BEGIN EVAPOTRANSPIRATION DATA
		//High AND TIME EVAPOTRANSPIRATION
		var highet = convertRain(wflash2[45]);
		set_ajax_obs("ajaxhighet",highet.toFixed(dpRain)+ajaxUOM(uomRain));
		var highettime = wflash2[73];
		set_ajax_obs("ajaxhighettime",highettime) ;
		
		//LOW AND TIME EVAPOTRANSPIRATION
		var lowet = convertRain(wflash2[101]);
		set_ajax_obs("ajaxlowet",lowet.toFixed(dpRain)+ajaxUOM(uomRain));
		var lowettime = wflash2[129];
		set_ajax_obs("ajaxlowettime",lowettime) ;
		
		//AVERAGE EVAPOTRANSPIRATION
		var avget = convertRain(wflash2[17]);
		set_ajax_obs("ajaxavget",avget.toFixed(dpRain)+ajaxUOM(uomRain));
		
		//High Rate AND TIME EVAPOTRANSPIRATION
		var highrateet = convertRain(wflash2[157]);
		set_ajax_obs("ajaxhighrateet",highrateet.toFixed(dpRain)+ajaxUOM(uomRain)+ajaxUOM(uomPerHr));
		var highrateettime = wflash2[185];
		set_ajax_obs("ajaxhighrateettime",highrateettime) ;
		
		//Low Rate AND TIME EVAPOTRANSPIRATION
		var lowrateet = convertRain(wflash2[213]);
		set_ajax_obs("ajaxlowrateet",lowrateet.toFixed(dpRain)+ajaxUOM(uomRain)+ajaxUOM(uomPerHr));
		var lowrateettime = wflash2[241];
		set_ajax_obs("ajaxlowrateettime",lowrateettime) ;
		//END EVAPOTRANSPIRATION DATA
		
		
		//BEGIN MISC DATA
		//DAILY WIND RUN
		var dailywindrun = convertDistance(wflash2[258]);
		set_ajax_obs("ajaxdailywindrun",dailywindrun.toFixed(0)+ajaxUOM(uomDistance));
		
		//MONTHLY WIND RUN
		var monthlywindrun = convertDistance(wflash2[265]);
		set_ajax_obs("ajaxmonthlywindrun",monthlywindrun.toFixed(0)+ajaxUOM(uomDistance));
		
		//YEARLY WIND RUN
		var yearlywindrun = convertDistance(wflash2[268]);
		set_ajax_obs("ajaxyearlywindrun",yearlywindrun.toFixed(0)+ajaxUOM(uomDistance));
		
		//HEAT STRESS
		var heatstress = wflash2[269];
		heatstress = heatstress.replace(/\+/g," ");
		set_ajax_obs("ajaxheatstress",heatstress);
		
		//COMFORT LEVEL
		var comfortlevel = wflash2[270];
		comfortlevel = comfortlevel.replace(/\+/g," ");
		set_ajax_obs("ajaxcomfortlevel",comfortlevel);

// removed to first routine to enable language translation of trend
//		//RAW BAROMETER TREND
//		rawbarotrend = wflash2[272];
//		rawbarotrend = rawbarotrend.replace(/\+/g," ");
//		set_ajax_obs("ajaxrawbarotrend",rawbarotrend);
//		
//		//SEA LEVEL PRESSURE TREND
//		pressuretrend = wflash2[273];
//		pressuretrend = pressuretrend.replace(/\+/g," ");
//		set_ajax_obs("ajaxbarotrend",pressuretrend);
		
		//DAILY DEGREE DAY COOLING
		var degreedaycool = wflash2[260];
		degreedaycool = degreedaycool * 1.0;
		set_ajax_obs("ajaxdegreedaycool",degreedaycool.toFixed(1));
		
		//DAILY DEGREE DAY HEATING
		var degreedayheat = wflash2[259];
		degreedayheat = degreedayheat * 1.0;
		set_ajax_obs("ajaxdegreedayheat",degreedayheat.toFixed(1));
		
		//MONTHLY DEGREE DAY COOLING
		var degreemonthcool = wflash2[264];
		degreemonthcool = degreemonthcool * 1.0;
		set_ajax_obs("ajaxdegreemonthcool",degreemonthcool.toFixed(1));
		
		//MONTHLY DEGREE DAY HEATING
		var degreemonthheat = wflash2[263];
		degreemonthheat = degreemonthheat * 1.0;
		set_ajax_obs("ajaxdegreemonthheat",degreemonthheat.toFixed(1));
		
		//YEARLY DEGREE DAY COOLING
		var degreeyearcool = wflash2[267];
		degreeyearcool = degreeyearcool * 1.0;
		set_ajax_obs("ajaxdegreeyearcool",degreeyearcool.toFixed(1));
		
		//YEARLY DEGREE DAY HEATING
		var degreeyearheat = wflash2[266];
		degreeyearheat = degreeyearheat * 1.0;
		set_ajax_obs("ajaxdegreeyearheat",degreeyearheat.toFixed(1));

//  beaufort moved to computed section above to enable language translation feature
//		beaufort = wflash2[274];
//		beaufort = beaufort.replace(/\+/g," ");
//		set_ajax_obs("ajaxbeaufort",beaufort);
		
		var forecast = wflash2[271];
		forecast = forecast.replace(/\+/g," ");
		set_ajax_obs("ajaxforecast",forecast);
		
		set_ajax_obs("ajaxsunrise",wflash2[277]);
		set_ajax_obs("ajaxsunset",wflash2[278]);
		set_ajax_obs("ajaxmoonrise",wflash2[279]);
		set_ajax_obs("ajaxmoonset",wflash2[280]);
		
		//END MISC DATA
		
		//BEGIN RAIN DATA
		//DAILY RAIN
		var rain = convertRain(wflash2[254]);
		set_ajax_obs("ajaxrain",rain.toFixed(dpRain)+ajaxUOM(uomRain));
		
		//24 HOURS RAIN
		var rain24 = convertRain(wflash2[256]);
		set_ajax_obs("ajaxrain24",rain24.toFixed(dpRain)+ajaxUOM(uomRain));
		
		//HOURLY RAIN
		var rainhr = convertRain(wflash2[255]);
		set_ajax_obs("ajaxrainhr",rainhr.toFixed(dpRain)+ajaxUOM(uomRain));
		
		//MONTHLY RAIN
		var rainmo = convertRain(wflash2[262]);
		set_ajax_obs("ajaxrainmo",rainmo.toFixed(dpRain)+ajaxUOM(uomRain));
		
		//RAIN RATE
		var rainrate = convertRain(wflash2[257]);
		set_ajax_obs("ajaxrainratehr",rainrate.toFixed(dpRain+1)+ajaxUOM(uomRain));
		//END RAIN DATA
		  
		//UPDATED DATE
		var ajaxdate = wflash2[275];
		set_ajax_obs("ajaxdate",ajaxdate);
		set_ajax_obs("gizmodate",ajaxdate);
	
		// now ensure that the indicator flashes on every AJAX fetch
        var element = document.getElementById("ajaxindicator");
		if (element) {
          element.style.color = flashcolor;
		}
        element = document.getElementById("gizmoindicator");
		if (element) {
          element.style.color = flashcolor;
		}

 	  } // END if(wflash[0] = '12345' and '!!' at end)

	 } // END if (x.readyState == 4 && x.status == 200)

    } // END try

   	catch(e){}  // Mike Challis added fix to fix random error: NS_ERROR_NOT_AVAILABLE

    } // END x.onreadystatechange = function() {
    x.open("GET", url, true);
    x.send(null);
    if ( (maxupdates == 0) || (updates < maxupdates-1)) {
      setTimeout("ajaxLoaderVWSf2(wflashFile2 + '?' + new Date().getTime())", reloadTime); // get new data after 5 secs
	}
  }
} // end ajaxLoaderVWSf2 function

// ---------------------------------------------------------------------------------------------
// the following runs once when this script is loaded by the browser.
//

// Start the countup timer
window.setInterval("ajax_countup()", 1000); // run the counter for seconds since update

// Start the pair of AJAX loaders .. they'll reinvoke themselves
ajaxLoaderVWSf(wflashFile + '?' + new Date().getTime()); 
ajaxLoaderVWSf2(wflashFile2 + '?' + new Date().getTime()); 


// ---------------- end of ajaxVWSwxf.js --------------------------------
// ]]>
