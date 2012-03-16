<?php
# Settingsfile for combined 3in1 V.4.1

# Paths
$wxallmainfolder = 'wxsim3in1/';		# Location of 3in1 scripts RELATIVE to page where shown
#$wxallmainfolder = ''; # use this setting to use http://napervilleweather.net/wxsim3in1/sivu2.php
# Full paths
#$wxallmainfolderfull = '/var/www/vhosts/nordicweather.net/httpdocs/test/wxsim/';
$wxallmainfolderfull = '/home/weather/public_html/saratoga/wxsim3in1/';
#$plaintextfolderfull = '/var/www/vhosts/nordicweather.net/httpdocs/test/wxsim/';
$plaintextfolderfull = '/home/weather/public_html/saratoga/';
#$lastretfile = '/var/www/vhosts/nordicweather.net/subdomains/data/httpdocs/wd/lastret.txt';
$lastretfile = '/home/weather/public_html/saratoga/lastret.txt';
#$plaintextfile = '/var/www/vhosts/nordicweather.net/subdomains/data/httpdocs/wd/plaintext.txt';
$plaintextfile = '/home/weather/public_html/saratoga/plaintext.txt';
# Graphicons. You can use the icons shipped from nordicweather or use own local ones
$wxallwindicondir = "http://static.nordicweather.net/nordic/images/barbs2/";
$wxallicondir = "http://static.nordicweather.net/nordic/images/dotvoid_30/";
# For easier debugging, ignore theese two ;)
#$lastretfile = '/var/www/vhosts/nordicweather.net/httpdocs/test/wxsim/lastret.txt';
#$plaintextfile = '/var/www/vhosts/nordicweather.net/httpdocs/test/wxsim/plaintext.txt';

# Timesettings
$timezone = "America/Chicago";      # Your timezone
$lat = 41.7897;                     # Your latitude
$long = -88.1242;										# Your longitude
$zenith = 90+40/60;                 # Zenit-setting for php suntimes
$timeFormat = "d.m.Y H:i";          # Timeformat
#$updatehrs = array(2,5,8,11,14,17,20,23);  # Hours when wxsim runs
$updatehrs = array(7,11,15,21);       # Hours when wxsim runs
$uploadupdate = 25; 	              # minutes past full hour for upload time

# widths
#$mainwidth = "870";									# width of whole container
$mainwidth = "625";	# to fit in saratoga template width
$barwidth = 15;
# positionings
$legendtop = "10";                  # Top padding for legend in graph

#$uoms = array('°C','mm','m/s','hPa','cm','km');  # IMPORTANT! Units in use, temperature, precip, wind, pressure, snowdepth, visibility
$uoms = array('°F','in','mph','in','in','mi');

$showgraph = false;						      # show graph in tabs?

# Limits
$windchilllimit = "0";              # Below what temperature windchill are shown on hour-table if next setting is reached
$windchilllimitb = "-4";            # How many degrees lower than temperature windchill needs to be before shown
$heatlimit = "15";                  # Above what temperature heat are shown on hour-table if next setting is reached
$heatlimitb = "4";                  # How many degrees higher than temperature heat needs to be before shown

$minuvtoshow = "1";						      # Min UV what are shown in hour-table
$washowzeroprecip = false;         # Show 0 mm rain as "No precip" in the tables?
$froststart = "5";                  # Month when start show frost
$frostend = "9";                    # Month when stop show frost
$snowlcolor = false;               # Show Snowlevel text colorized?
$showtsprob = true;                # Show thunderstormprobability % on hourtable?
$tsclcover = 35;                    # Lowest cloudcover when thunderstorms shown (when >85% are TS shown allways)
$precclcover = 35;                  # Lowest cloudcover when precip shown
$poplow = 5;                        # Lower limit to change pop color  >= 5(mm)
$pophigh = 20;                      # High limit to change pop color >= 20(mm) 
$topmany = 9;                       # How many forecasts shown in top-forecast (if used)
$highwindlimit = 6;                 # At wich (average) windspeed wind-direction icon change to orange (6-12 for m/s)

# Preciptiation limits for light/moderate/heavy -> affects icons
$modrain = 2;
$heavyrain = 8;
$modsnow = 2;
$heavysnow = 6;

# Soil & Grass-forecast
# Script use Depth 2 amd Depth 3 from WxSim
#$sgactive = true;										# NOTE! You need PRO-version of WxSim for this feature, or be old customer who have it enabled
$sgactive = false;										# NOTE! You need PRO-version of WxSim for this feature, or be old customer who have it enabled
$sgdepth1 = "-4 cm";								# Depth for Soil 1
$sgdepth2 = "-8 cm";								# Depth for Soil 2

date_default_timezone_set($timezone);
include $wxallmainfolderfull.'wxall.plaintext.php';
?>
