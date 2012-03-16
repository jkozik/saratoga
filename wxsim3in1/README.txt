3in1 for modded textforecast, hour-by-hour-forecast and graph by Flot, V.4.1 (Jan 2012)

As the script are managed on my spare time are the script provided "as it is" and users of the script should NOT EXPECT ANY KIND OF SUPPORT FOR THE SCRIPT.
Feel free to contact me, but be warned, the answers can take time or not be provided at all.
I accept no liability for any damages that may ensue from the usage.
You will need to configure it for your own particular weather station website.
If you find one or more of my scripts useful to you, please consider making a donation to help offset the routine expenses of operation of this website.
Thanks for your kind support!

Update (07/12/2011), needs WXSIM v. 12.8.9.
All texts are translated by plaintext.lang-xx.txt-files.

- It show snowdepth once it is above 0 cm/in (hour-by-hour-tables && graph)
- It mention frost from 1 May to 30 September if in wx-type in lastret.txt (3 hour-by-hour-table)
- If you have PRO-version of WxSim or are old customer who have PRO-functions activated, Soil & Grass-forecast will be available
- Works now also for forecasts in Fahrenheit, see below

New in V.4:
- New tooltips on each data (tables, graph & topforecast)
- New "readydefined sets" for showing on webpage
- New 1 hour graph for next 48 hours and renamed "Detailed" to "Overview"
- New graph with buttons to choose what set of values are shown
- Heavy looptrought of the code
- New wind-direction icons with 360 different direction where available in data + some other icons (also dotvoid-frc.png are new) ;)
- New javascripts with more functions but also updated jQuery 
- Shows also "Next Update"
- Cuztomized jQueryUI-css in use for the new look of the tabs ;)
- Graph can be set visible or not in the tabs
- No need for Ken's plaintext-parser.php anymore (lang-files still needed)

New in V.4.1
- Graph switched to Highcharts, Flot-graph are still as backup as wxall.lastret_FL.php and jquery.flot.js
- IMPORTANT! Graph are loaded by ajax, it updates a file called graphlog.txt, YOU NEED TO UPLOAD THAT FILE AND CHMOD IT TO 777 OR 666 IF NEEDED DEPEND ON YOUR SERVER

*******************************************************************************************************
* HOW TO SETUP *
*******************************************************************************************************

1. You need to setup WXSIM to create plaintext.txt & lastret.txt and upload them.
2. Choose values seen choosen in attached wret.png in WRET to be plotted in lastret.txt
   - Horizontal Visibility are now needed
   - Remember to choose also "Put convective parameters in lastret"
3. Check the settings in wxall.settings.php (its heavenly cleaned up + new settings in V.4!!)
4. Add the new stuff from plaintext.lang.txt to your plaintext-parser-lang-xx.txt and translate them (you may want to finetune the other words/sentenses too)
5. Upload the stuff except README.txt, plaintext.lang.txt, plaintext-parser-lang.fi.txt, wret.png. 
6. To implent on page, see below

*******************************************************************************************************
* UNIT-SETTINGS *
*******************************************************************************************************

IN wxall.settings.php are $uoms with units. Theese should match the units used in lastret.txt. 
It detects automatically if US-units (F,in,mph) are used.

********************************************************************************************************
* DIFFERENT PREDEFINED SETS *
********************************************************************************************************

3in1 comes now with 3 predefined sets to show on the page; topforecast, graph and tabs. Theese can be used stand-alone or together, 
with one exception, standalone-graph and tabs with graph active can NOT be used on same page.
Also a standalone box with updatetime + next update are defined.
Beside on that are many of the old $WXSIMvariable[$i] still avalilable.

On every page where 3in1 are used are this needed in header inside <head> and </head>:

<?php
$lang = $_GET[lang]; // not nessessary in Saratoga Templates where $lang defined
include 'wxall.settings.php';
echo $wxsimhead;
?>

In <body> put where you want to show up:

<?php
# Header with name & update-times (optional)
echo $wxsimupdated;
echo '<h3>'.get_lang("WXSIM Forecast for:").' '.$WXSIMcity.'</h3>';

# Theese can be used stand alone or together
echo $wxsimtop;     # "Top-forecast"
echo $wxsimgraph;   # Graph
echo $wxsimmain;    # Tabs
?>

You can use sivu2.php as model and testpage

#################################################################################################################

IMPORTANT:
Allthough the condition icons are free to use, the creator of the icons (A small Swedish IT-firm Dotvoid), ask for a creditlink to their site on the page(s) where they are used, ex like this:
"Weathericons by <a href="http://www.dotvoid.se" rel="external">Dotvoid</a>"

CREDITS:
Credits goes especially to Ken True for his original plaintext-parser, without that should not our forecast-pages look like they do today :)

That's it.