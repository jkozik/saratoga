/* English text for ajaxWDwx.js */
//Version 1.02 - 29-Aug-2011 - added to Base-USA distribution

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

var langHeatWords = new Array ( /* used for Heat Color Word */
 'Unknown', 'Extreme Heat Danger', 'Heat Danger', 'Extreme Heat Caution', 'Extremely Hot', 'Uncomfortably Hot',
 'Hot', 'Warm', 'Comfortable', 'Cool', 'Cold', 'Uncomfortably Cold', 'Very Cold', 'Extreme Cold' );

