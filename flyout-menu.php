<?php
// flyout menu generator using http://www.cssplay.co.uk/menus/flyout_4level.html
// menu stylesheet adapted by Ken True - http://saratoga-weather.org/
//                         and Mike Challis - http://www.642weather.com/weather/
//
// this release is specifically for the Weather-Display/AJAX/PHP template set
//
// Version 1.00 - 18-Mar-2008 - Initial release
// Version 1.01 - 23-Apr-2008 - added support for WD-World-ML (multilingual)
// Version 1.02 - 18-Aug-2008 - fix for 'unknown' css processing by Mike Challis
// Version 1.03 - 20-Jan-2009 - added translation for <item title="..." /> in menu set
// Version 1.04 - 23-Jul-2010 - added tags target=,img=,align= to parsing, use menu=<name> for flyout-menu-<name>.xml
// Version 1.05 - 19-Jan-2011 - added wx="..." conditionals for universal templates
// Version 1.06 - 05-Feb-2011 - fixed IE8+ display of $FlyoutMenuText
// Version 1.07 - 04-Mar-2011 - fixed errata casting Strict: messages
// Version 1.08 - 05-Feb-2013 - fixed HTML5 validation with literal quote in translation
//
$Version = 'flyout-menu.php (ML) Version 1.08 - 05-Feb-2013';
//
// ---------- settings ------------------------------
$MENUdef = './flyout-menu.xml'; // (relative) file location of XML menu definition file
$MENUdefTest = './flyout-menu-%s.xml'; // (relative) file location of test XML menu definition file
$imagesDir = './ajax-images/';  // (relative) URL location of images dir (with trailing '/')
//
$lang = 'en';  // default language
$WXsoftware = 'WD'; // default weather software
//
// ---------- end settings --------------------------
//  error_reporting(E_ALL); // for testing
//------------------------------------------------
// overrides from Settings.php if available
global $SITE;
if (isset($SITE['imagesDir'])) 	{$imagesDir = $SITE['imagesDir'];}
if (isset($SITE['menudef'])) 	{$MENUdef = $SITE['menudef'];}
if (isset($SITE['menudeftest'])) 	{$MENUdefTest = $SITE['menudeftest'];}
if (isset($SITE['lang']))	{$lang = $SITE['lang'];}
if (isset($SITE['WXsoftware']))	{$WXsoftware = $SITE['WXsoftware'];}
// end of overrides from Settings.php if available
if (isset($_REQUEST['sce']) && ( strtolower($_REQUEST['sce']) == 'view' or
    strtolower($_REQUEST['sce']) == 'show') ) {
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

$Debug = false;
if (isset($_REQUEST['debug']) && strtolower($_REQUEST['debug']) == 'y') {
  $Debug = true;
}
$doDiv = false;
if (isset($_REQUEST['gendiv']) && strtolower($_REQUEST['gendiv']) == 'y') {
  $doDiv = true;
}
if (isset($genDiv)) {
  $doDiv = $genDiv;
}

$doCSS = false;
if (isset($_REQUEST['css']) && strtolower($_REQUEST['css']) == 'y') {
  $doCSS = true;
}
if (isset($genCSS)) {
  $doCSS = $genCSS;
}
  $CSS = '';
  $FlyoutMenuColors = loadStyleSettings(); // Get the colors to use
  genCSSFlyout($FlyoutMenuColors);

$doPrintMenu = true;
if (isset($PrintFlyoutMenu)) {
  $doPrintMenu = $PrintFlyoutMenu;
}
if ($doCSS) { // only return the necessary CSS for direct include
  print $FlyoutCSS;
  return;

}
$usingAltXML = false;
if(isset($_REQUEST['menu']) and  preg_match('|%s|',$MENUdefTest)) {
   $tMenu = sprintf($MENUdefTest,strtolower($_REQUEST['menu']));
   // print "<!-- checking '$tMenu' -->\n";
   if(file_exists($tMenu)) {															
     $MENUdef = $tMenu;
     $usingAltXML = true;
     print "<!-- using '$tMenu' -->\n";
   }
 }

$depth = array();
$MENU = array();
$MENUcnt = 0;
$lastDepth = 0;
$Status = "<!-- $Version -->\n";

// ------------- main routine --------------------
$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startElementFlyout", "endElementFlyout");
$doTrans = true;
if ($lang <> 'en') { // special handling for non-english menu constructions
  $tfile = preg_replace('|\.xml|',"-$lang.xml",$MENUdef);
  if (file_exists($tfile)) {
     $MENUdef = $tfile; // use the XML and no other translation.
	 $doTrans = false;
   }
}

if (!($fp = fopen($MENUdef, "r"))) {
    die("could not open XML input from $MENUdef ");
}

while ($data = fread($fp, 8192)) {
    if (!xml_parse($xml_parser, $data, feof($fp))) {
        die(sprintf("XML error: %s at line %d",
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser)));
    }
}
xml_parser_free($xml_parser);

// ----------- generate the menu XHTML ---------------
$FlyoutMenuText = "<!-- begin generated flyout menu -->\n";

if ($doDiv) {
  $FlyoutMenuText .= "<div class=\"flyoutmenu\">\n";
}
$FlyoutMenuText .= "<!-- $Version -->\n";
$FlyoutMenuText .= "<!-- by Ken True - webmaster[at]saratoga-weather.org and -->\n";
$FlyoutMenuText .= "<!-- by Mike Challis - webmaster[at]642weather.com  -->\n";
$FlyoutMenuText .= "<!-- Adapted from Stu Nicholl's CSS/XHTML at http://www.cssplay.co.uk/menus/flyout_4level.html -->\n";
$FlyoutMenuText .= "<!-- script available at http://saratoga-weather.org/scripts-CSSmenu.php#flyout -->\n";


$FlyoutMenuText .= "<!-- using \n" . print_r($FlyoutMenuColors,true) . " -->\n";
$FlyoutMenuText .= "<!-- using $MENUdef for XML, doTrans=$doTrans -->\n";

for ($i=1;$i<count($MENU);$i++) { // loop over all menu items -1
  $depth = $MENU[$i]['depth'];
  $nextdepth = $MENU[$i+1]['depth'];
  $indent = str_repeat("  ",$depth);
  $link = $MENU[$i]['link'];
  $title = $MENU[$i]['title'];
  $target = $MENU[$i]['target'];
  $img = $MENU[$i]['img'];
  $align = $MENU[$i]['align'];
  $wxonly = $MENU[$i]['wxonly'];
  $wxonlydisplay = '';
  $wxonlyPrefix = '';
  $wxonlySuffix = '';
  if($wxonly <> '' and ! preg_match("|$WXsoftware|i",$wxonly) ) { // see if this menu is allowed
	  $wxonlydisplay = 'wxonly=\''.$wxonly.'\' ';
	  $wxonlyPrefix = "<!-- not used with $WXsoftware ";
	  $wxonlySuffix = " -->";
  }
  if ($doTrans and $title <> '') { $title = preg_replace('|"|','&quot;',langtransstr($title)); }
  $caption = $doTrans?preg_replace('|"|','&quot;',langtransstr($MENU[$i]['caption'])):$MENU[$i]['caption'];
//  $caption = htmlspecialchars($caption);

  if ($target <> '') {
    $target = ' target="' . $target . '"';
  } else {
    $target = '';
  }
  
  if ($link <> '') {
    $link = 'href="' . $link . '"';
  } else {
    $link = 'href="' . "#" . '"';
  }
  
  if ($title <> '') {
    $title = ' title="' . $title . '"';
  } else {
    $title = '';
  }
  
  $leftimg = '';
  $rightimg = '';
  
  if ($img <> '') {
    $img = '<img src="' . $img . '" style="border:none" alt=" "/>';
	if (preg_match('|left|i',$align)) {
	   $leftimg = $img;
	} else {
		$rightimg = $img;
	}
  }

  if ($i==1) { // start of entire image
    $FlyoutMenuText .= "<ul>\n";
  }
  if ($Debug) {
    $FlyoutMenuText .= "$indent<!-- $i: depth=$depth next=$nextdepth $wxonlydisplay caption='" . $MENU[$i]['caption'] . "' link='" . $MENU[$i]['link'] ."' title='" . $MENU[$i]['title'] . "' -  ";
  }
  if ($depth < $nextdepth) { // -------------------  start of new submenu 
    if ($Debug) {
      $FlyoutMenuText .= "Start new submenu -->\n";
	}
	$FlyoutMenuText .= "$indent$wxonlyPrefix<li class=\"sub\"><a $link$title$target>$leftimg" . $caption . "$rightimg<!--[if gte IE 7]><!--></a>$wxonlySuffix<!--<![endif]-->
$indent  <!--[if lte IE 6]><table><tr><td><![endif]-->
$indent  <ul>\n";
	
  }
  
  if ($depth > $nextdepth) { // --------------------  end of new submenu
    if ($Debug) {
      $FlyoutMenuText .= "End new submenu -->\n";
	}
	$FlyoutMenuText .= "$indent$wxonlyPrefix<li><a $link$title$target>$leftimg" . $caption . "$rightimg</a></li>$wxonlySuffix\n";
	
	for ($j=$depth; $j > $nextdepth ;$j--) { // close off intervening submenu(s)
	
	  $newindent = str_repeat("  ",$j-1);
	$FlyoutMenuText .= "$newindent  </ul>
$newindent  <!--[if lte IE 6]></td></tr></table></a><![endif]-->
$newindent</li>\n";
    }
   

  }
  
  if ($depth == $nextdepth) { // ---------------------- menu item at current depth
    if ($Debug) {
      $FlyoutMenuText .= "Normal menu item -->\n";
	}
	$FlyoutMenuText .= "$indent$wxonlyPrefix<li><a $link$title$target>$leftimg" . $caption . "$rightimg</a></li>$wxonlySuffix\n";
  
  }
  
  if ($i==count($MENU)-1) {
    $FlyoutMenuText .= "</ul>\n";
  }
} // end of loop over menu items
if ($doDiv) {
  $FlyoutMenuText .= "</div>\n";
}
$FlyoutMenuText .= "<!-- end generated flyout menu -->\n";

if ($doPrintMenu) {
  print $FlyoutMenuText;
}

if ($Debug) {
  print $Status;
}

// functions invoked by XML_parser

function startElementFlyout($parser, $name, $attrs) 
{
    global $depth,$Status,$lastDepth,$MENU,$MENUcnt,$WXsoftware;

    $indent = '';
	if (! empty($depth[(integer)$parser]) ) {
	  $j = $depth[(integer)$parser];
	} else {
	  $j = 0;
	}
    for ($i = 0; $i < $j; $i++) {
        $Status .= "  ";
		$indent .= "  ";
    }
    $Status .= "<!-- Depth: $j - $name " . print_r($attrs,true) . " -->\n";
    // format the CAPTION and LINK entries
	if (! empty($attrs['LINK']) ) {
	  $link = $attrs['LINK'];
	 } else {
	  $link = '';
	 }
	if (! empty($attrs['CAPTION']) ) {
	  $caption = $attrs['CAPTION'];
	} else {
	  $caption = '';
	}

	if (! empty($attrs['TITLE']) ) {
	  $title = $attrs['TITLE'];
	} else {
	  $title = '';
	}
	if (! empty($attrs['TARGET']) ) {
	  $target = $attrs['TARGET'];
	} else {
	  $target = '';
	}
	if (! empty($attrs['IMG']) ) {
	  $img = $attrs['IMG'];
	} else {
	  $img = '';
	}
	if (! empty($attrs['ALIGN']) ) {
	  $align = preg_match('|left|i',$attrs['ALIGN'])?'left':'right';
	} else {
	  $align = '';
	}
    if (! empty($attrs['WXONLY']) ) {
		$wxonly = $attrs['WXONLY'];
	} else {
	  $wxonly = '';
	}

    if ($caption <> '' or $link <> '') { // ignore entries that are wholly blank
	  $MENUcnt++;
	  $MENU[$MENUcnt]['depth'] = $j;
	  $MENU[$MENUcnt]['caption'] = $caption;
	  $MENU[$MENUcnt]['title'] = $title;
	  $MENU[$MENUcnt]['link'] = $link;
	  $MENU[$MENUcnt]['target'] = $target;
	  $MENU[$MENUcnt]['img'] = $img;
	  $MENU[$MENUcnt]['align'] = $align;
	  $MENU[$MENUcnt]['wxonly'] = $wxonly;
	  // store dummy next entry at highest level for final run-through to generate
	  // the XHTML.  This will be overwritten by a 'real' entry if any
	  $MENU[$MENUcnt+1]['depth'] = 1; 
	  $MENU[$MENUcnt+1]['caption'] = '';
	  $MENU[$MENUcnt+1]['title'] = '';
	  $MENU[$MENUcnt+1]['link'] = '';
	  $MENU[$MENUcnt+1]['target'] = '';
	  $MENU[$MENUcnt+1]['img'] = '';
	  $MENU[$MENUcnt+1]['align'] = '';
	  $MENU[$MENUcnt+1]['wxonly'] = '';
	
	}
	
	$lastDepth = $j; // remember for next time
	$j++;
    $depth[(integer)$parser] = $j;
}

// called at end of particular element
function endElementFlyout($parser, $name) 
{
    global $depth,$Status;
    $depth[(integer)$parser]--;
}
// end of XML_parser functions

// return the CSS
function genCSSFlyout ($color)
{
global  $FlyoutCSS,$imagesDir, $SITE;
global  $TopMenuWidth,$SubMenuWidth;
global  $TopMenuBkgnd,$TopMenuTextColor,$TopMenuTextHover,$TopMenuTextBkgnd;
global  $TopMenuBkgrndHover,$TopMenuBorder;
global  $SubMenuPresent,$SubMenuTextColor,$SubMenuTextHover,$SubMenuBkgrnd,$SubMenuBkgrndHover;

$SNAME = $color['NAME'];
$LC = $color['LINK_COLOR'];
$LBG = $color['LINK_BACKGROUND'];
$HC = $color['HOVER_COLOR'];
$HBG = $color['HOVER_BACKGROUND'];
$BC = $color['BORDER_COLOR'];
$SC = $color['SHADE_IMAGE']; 

$FlyoutCSS = <<<END_OF_CSS
<!-- begin flyout-menu.php CSS definition style='${SNAME}' -->
<style type="text/css">
/* ================================================================
This copyright notice must be untouched at all times.

The original version of this stylesheet and the associated (x)html
is available at http://www.cssplay.co.uk/menus/flyout_4level.html
Copyright (c) 2005-2007 Stu Nicholls. All rights reserved.
This stylesheet and the associated (x)html may be modified in any
way to fit your requirements.
Modified by Ken True and Mike Challis for Weather-Display/AJAX/PHP
template set.
=================================================================== */
.flyoutmenu {
font-size:90%;
}

/* remove all the bullets, borders and padding from the default list styling */
.flyoutmenu ul {
position:relative;
z-index:500;
padding:0;
margin:0;
padding-left: 4px; /* mchallis added to center links in firefox */
list-style-type:none;
width: 110px;
}

/* style the list items */
.flyoutmenu li {
color: ${LC};
background:${LBG} url(${imagesDir}${SC});
/* for IE7 */
float:left;
margin:0; /* mchallis added to tighten gaps between links */
}
.flyoutmenu li.sub {background:${LBG} url(${imagesDir}flyout-sub.gif) no-repeat right center;}

/* get rid of the table */
.flyoutmenu table {position:absolute; border-collapse:collapse; top:0; left:0; z-index:100; font-size:1em;}

/* style the links */
.flyoutmenu a, .flyoutmenu a:visited {
display:block;
text-decoration:none;
line-height: 1.8em; 
width:95px; /* mchallis changed for adjusting firefox link width */
color:${LC};
padding: 0 2px 0 5px; 
border:1px solid ${BC};
border-width:0 1px 1px 1px;
}
/* hack for IE5.5 */
         /* mchallis lowered the two width values to (101, 100)to fix IE6 links wider than menu width */
* html .flyoutmenu a, * html .flyoutmenu a:visited {width:95px; w\idth:94px;}
/* style the link hover */
* html .flyoutmenu a:hover {color:${HC}; background:${HBG}; position:relative;}

.flyoutmenu li:hover {position:relative;}

/* For accessibility of the top level menu when tabbing */
.flyoutmenu a:active, .flyoutmenu a:focus {color:${HC}; background:${HBG};}

/* retain the hover colors for each sublevel IE7 and Firefox etc */
.flyoutmenu li:hover > a {color:${HC}; background:${HBG};}

/* hide the sub levels and give them a positon absolute so that they take up no room */
.flyoutmenu li ul {
visibility:hidden;
position:absolute;
top:-10px;
/* set up the overlap (minus the overrun) */
left:90px;
/* set up the overrun area */
padding:10px;
/* this is for IE to make it interpret the overrrun padding */
background:transparent url(${imagesDir}flyout-transparent.gif);
}

/* for browsers that understand this is all you need for the flyouts */
.flyoutmenu li:hover > ul {visibility:visible;}


/* for IE5.5 and IE6 you need to style each level hover */

/* keep the third level+ hidden when you hover on first level link */
.flyoutmenu ul a:hover ul ul{
visibility:hidden;
}
/* keep the fourth level+ hidden when you hover on second level link */
.flyoutmenu ul a:hover ul a:hover ul ul{
visibility:hidden;
}
/* keep the fifth level hidden when you hover on third level link */
.flyoutmenu ul a:hover ul a:hover ul a:hover ul ul{
visibility:hidden;
}

/* make the second level visible when hover on first level link */
.flyoutmenu ul a:hover ul {
visibility:visible;
}
/* make the third level visible when you hover over second level link */
.flyoutmenu ul a:hover ul a:hover ul{
visibility:visible;
}
/* make the fourth level visible when you hover over third level link */
.flyoutmenu ul a:hover ul a:hover ul a:hover ul {
visibility:visible;
}
/* make the fifth level visible when you hover over fourth level link */
.flyoutmenu ul a:hover ul a:hover ul a:hover ul a:hover ul {
visibility:visible;
}

</style>
<!-- end of flyout-menu.php CSS definition -->

END_OF_CSS;

} // end of genCSS function

function loadStyleSettings ( ) {

global $SITE;

# array of css style names, when adding a new style ...
# edit this to add a new name, also add a new link color array below,
# weather-screen-[NAME]-[narrow or wide].css

$styles_array_all = array(
array('NAME' => 'black',  'SHADE_IMAGE'=> 'flyout-shade-4A4A4A.gif', 'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> '#CC9933', 'LINK_BACKGROUND'=> '#4A4A4A', 'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => 'black',),   # black
array('NAME' => 'blue',   'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> '#336699', 'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#3173B1',), # blue
array('NAME' => 'dark',   'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> 'black',   'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#58B6C7',), # dark
array('NAME' => 'fall',   'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> 'black',   'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#E87510',), # fall
array('NAME' => 'green',  'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> 'black',   'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#00745B',), # green
array('NAME' => 'icetea', 'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> 'black',   'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#786032',), # icetea
array('NAME' => 'mocha',  'SHADE_IMAGE'=> 'flyout-shade-CCBFAD.gif', 'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> 'black',   'LINK_BACKGROUND'=> '#CCBFAD', 'HOVER_COLOR' => 'black', 'HOVER_BACKGROUND' => '#D7E2E8',), # mocha
array('NAME' => 'orange', 'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> '#CC6600', 'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#CC6600',), # orange
array('NAME' => 'pastel', 'SHADE_IMAGE'=> 'flyout-shade-E1EBF2.gif', 'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> '#03447D', 'LINK_BACKGROUND'=> '#E1EBF2', 'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#7190BF',), # pastel
array('NAME' => 'purple', 'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> '#993333', 'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#993333',), # purple
array('NAME' => 'red',    'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> '#993333', 'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#993333',), # red
array('NAME' => 'salmon', 'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> '#46222E', 'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#A4CB32',), # salmon
array('NAME' => 'silver', 'SHADE_IMAGE'=> 'flyout-shade-9A9A9A.gif', 'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> '#EDDD2F', 'LINK_BACKGROUND'=> '#9A9A9A', 'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#666666',), # silver
array('NAME' => 'spring', 'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> 'black',   'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#A9D26A',), # spring
array('NAME' => 'taupe',  'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> 'black',   'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#75B7BF',), # taupe
array('NAME' => 'teal',   'SHADE_IMAGE'=> 'flyout-shade-white.gif',  'BORDER_COLOR'=> 'black', 'LINK_COLOR'=> '#006666', 'LINK_BACKGROUND'=> 'white',   'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#006666',), # teal
);

# default
$menu_colors = array('NAME' => 'unknown', 'SHADE_IMAGE'=> 'flyout-shade.gif',  'BORDER_COLOR'=> 'white', 'LINK_COLOR'=> 'black', 'LINK_BACKGROUND'=> '#D4D8BD', 'HOVER_COLOR' => 'white', 'HOVER_BACKGROUND' => '#AA7');

if (isset($SITE['CSSscreen']) && preg_match("/^[a-z0-9-]{1,50}.css$/i", $SITE['CSSscreen'])) {
  preg_match("/^weather-screen-([a-z0-9]+)-(wide|narrow).css$/i", $SITE['CSSscreen'], $matches);

  # this can be used for any conditionals requiring color style name
  $style_color = trim($matches[1]);

  # this can be used for any conditionals requiring wide or narrow
  $style_wide_or_narrow = trim($matches[2]);
}

foreach ($styles_array_all as $v) {
     if($style_color == $v['NAME']) {
       $menu_colors = $v;
       break;
     }
}

return ($menu_colors); // return the color choice associative array

} // end loadStyleSettings

// end of flyout-menu.php
?>