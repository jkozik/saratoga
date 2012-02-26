<?php
/*
PHP script by Mike Challis, www.642weather.com/weather/
Carterlake/AJAX/PHP Template - Color Theme Switcher Plugin PHP Script
Script available at: http://www.642weather.com/weather/scripts.php
Contact Mike: http://www.642weather.com/weather/contact_us.php
Live Demo: http://www.642weather.com/weather/template/
Download: http://www.642weather.com/weather/scripts/css-theme-switcher.zip

Version: 1.17  19-Dec-2008
- adjustment to cookie path setting
Version 1.18 - 10-Feb-2011 - fix duplicate id= in wide/narrow selection HTML

see changelog.txt for all change history

Support comes from MCHALLIS in this topic at the Weather-Watch.com forum:
http://www.weather-watch.com/smf/index.php/topic,30183.0.html

You are free to use and modify the code

This php code provided "as is", and Long Beach Weather (Michael Challis)
disclaims any and all warranties, whether express or implied, including
(without limitation) any implied warranties of merchantability or
fitness for a particular purpose.
*/

# session_start() must be processed before any other output
# or you might get a warning "headers already sent".
if( !isset( $_SESSION ) ) {
    session_start();
}

#################
# begin settings
# ##############

# note the style names in this array are intentionally missing
# the '-wide' and '-narrow' part of the file name, because it is automatically
# replaced by the program based on the user's setting for wide or narrow
# ie: a css file on the server has weather-screen-blue-wide.css
# but the name of it in this array is weather-screen-blue.css

# You can make new styles and add them, just add a new file to the server
# and add a new entry in the array below using the file name conventions noted above

# you can add or remove styles from this array
$CSSstyles = array(
'weather-screen-black.css'   => 'Black',
'weather-screen-blue.css'    => 'Blue',
'weather-screen-dark.css'    => 'Dark',
'weather-screen-fall.css'    => 'Fall',
'weather-screen-green.css'   => 'Green',
'weather-screen-icetea.css'  => 'Ice Tea',
'weather-screen-mocha.css'   => 'Mocha',
'weather-screen-orange.css'  => 'Orange',
'weather-screen-pastel.css'  => 'Pastel',
'weather-screen-purple.css'  => 'Purple',
'weather-screen-red.css'     => 'Red',
'weather-screen-salmon.css'  => 'Salmon',
'weather-screen-silver.css'  => 'Silver',
'weather-screen-spring.css'  => 'Spring',
'weather-screen-taupe.css'   => 'Taupe',
'weather-screen-teal.css'    => 'Teal',
);

############
# Use javascript 'OnChange' to post form
############
# if set to true, the style select will not need a "set" button
# if set to false, the style select show a "set" button
# There is a known issue with IE6 and the onchange feature,
# the ajax updates sometimes interfere causing the onchange not to change when changing a selection

# recommended setting: false
$use_onchange_submit = false; # set to true or false

############
# Use cookies to remember settings between site visits
############
# cookies are used to remember the users style settings on each visit to the web site
# you can disable use of cookies, but then the settings would have to be set by
# the user each visit to the web site

# recommended setting: true
$use_cookies = true; # set to true or false

# sets allowable user style select options: (can be overriden with $SITE['CSSsettings_mode'])
$settings_mode = 1; # user can select style and widescreen (show style select and screen width select)
//$settings_mode = 2; # user can select styles only (hide screen width select)
//$settings_mode = 3; # user can select screen width only (hide style select)
#################
# end settings
# ##############

  include_once("Settings.php");
  include_once("common.php");
  global $SITE, $LANGLOOKUP;

if (isset($SITE['CSSsettings_mode']) && $SITE['CSSsettings_mode'] > 0 && $SITE['CSSsettings_mode'] < 4) {
  $settings_mode = $SITE['CSSsettings_mode'];
}
function print_css_style_menu ($menubar = 0, $output = 'echo') {

  global $SITE, $LANGLOOKUP;

$string = '';

/* ###

  # this function makes the form input html so a user can select a style
  # it can be called in menubar.php or on index.php

  # when using on on menubar.php, you can have it print narrow on a few lines this:
  #<div style="margin-left: 5px">
  #<?php print_css_style_menu(1); ?>
  #</div>

  # when using on on index.php, you can have it print horizontal on one line like this:
  #<?php print_css_style_menu(0); ?>

*/ ###

  global $CSSstyles, $use_onchange_submit, $settings_mode;

$CSSstyle = '';
# was there a style selected from the form input
if (isset($_POST['CSSstyle']) && preg_match("/^[a-z0-9-]{1,50}.css$/i", $_POST['CSSstyle'])) {
     $CSSstyle = $_POST['CSSstyle'];
} else if (isset($_SESSION['CSSstyle'])) {
     $CSSstyle = $_SESSION['CSSstyle'];
}

# was use widescreen template selected from the form input?
$CSSwscreenOnChecked = '';
$CSSwscreenOffChecked = '';
if (isset($_SESSION['CSSwidescreen']) && $_SESSION['CSSwidescreen'] == 1) {
    $CSSwscreenOnChecked = ' checked="checked"';
} else if (isset($_SESSION['CSSwidescreen']) && $_SESSION['CSSwidescreen'] == 0) {
    $CSSwscreenOffChecked = ' checked="checked"';
} else {
    $CSSwscreenOffChecked = ' checked="checked"';
}
$string .= '<p class="sideBarTitle" style="margin-left: -5px;">'. langtransstr('Style Options') .'</p>';
$string .= '
<form method="post" name="style_select" action="">
<p>';

if ($settings_mode != 3) {  // style settings allowed

	$string .= '<label for="CSSstyle">'.langtransstr('Style').':</label>';
	if ($menubar) $string .= '<br />';
	
	if($use_onchange_submit == false) {
	$string .= '
	 <select id="CSSstyle" name="CSSstyle">';
	}
	else {
	$string .= '
	<select id="CSSstyle" name="CSSstyle" onchange="this.form.submit();">';
	}
	
	$CSSstyleSelected = '';
	foreach ($CSSstyles as $k => $v) {
	 if ($CSSstyle == "$k") $CSSstyleSelected = ' selected="selected"';
	$string .= '<option value="'.$k.'"'.$CSSstyleSelected.'>'.langtransstr($v).'</option>'."\n";
	 $CSSstyleSelected = '';
	}
	$string .= '</select>';
	
	if ($menubar) {
		   $string .= '<br />';
	}else{
		   $string .= ' &nbsp;&nbsp; ';
		   if($use_onchange_submit == false) {
			  $string .= '<input type="submit" name="' . langtransstr('Set') .'" value="' . langtransstr('Set') .'" />';
		   }
	}
	
	if (!$menubar) {
		   $string .= ' &nbsp;&nbsp; ';
	}
}
if ($settings_mode != 2) {  // screen width settings allowed

	$string .= langtransstr('Widescreen') . ':';
	if ($menubar) {
		   $string .= '<br />';
	}else{
		   $string .= ' ';
	}
	
	if($use_onchange_submit == true) {
	$string .= '<label for="CSSwidescreenOn">'.langtransstr('On').'</label> <input type="radio" id="CSSwidescreenOn" name="CSSwidescreen" value="1" '.$CSSwscreenOnChecked.' onchange="this.form.submit();" />
	| <label for="CSSwidescreenOff">'.langtransstr('Off').'</label> <input type="radio" id="CSSwidescreenOff" name="CSSwidescreen" value="0" '.$CSSwscreenOffChecked.' onchange="this.form.submit();" />';
	}
	else {
	$string .= '<label for="CSSwidescreenOn">'.langtransstr('On').'</label> <input type="radio" id="CSSwidescreenOn" name="CSSwidescreen" value="1" '.$CSSwscreenOnChecked.' />
	| <label for="CSSwidescreenOff">'.langtransstr('Off').'</label> <input type="radio" id="CSSwidescreenOff" name="CSSwidescreen" value="0" '.$CSSwscreenOffChecked.' />';
	}
}
if ($menubar) {
       $string .= '<br />';
}else{
       $string .= ' &nbsp;&nbsp; ';
}

if($use_onchange_submit == false) {
  $string .= '<input type="submit" name="' . langtransstr('Set') .'" value="' . langtransstr('Set') .'" />';
}

$string .= '</p>
</form>';

# can be printed right where you call it or loaded into a string for later print
# $menubottom .= print_css_style_menu(1,'string');
# print_css_style_menu(1);

if ($output == 'echo') {
         echo $string;
} else {
         return $string;
}

 # uncomment only for debugging
// echo '<p><b>COOKIE INFO:</b><br>';
// print_r($_COOKIE).'</p>';
// echo '<hr>';
// echo '<p><b>SESSION INFO:</b><br>';
// print_r($_SESSION).'</p>';
// echo '<p>Style choice ' .$SITE['CSSscreen'] .'</p>';

}


function validate_style_choice() {

   # this function sets the working style based on user's input or not
   # input can come from post, cookie, or session

   global $CSSstyles, $SITE, $use_cookies, $settings_mode;

  $wide_style = 0;
  $narrow_style = 0;
  $CSSstyle = '';

  // incase site admin sets up $SITE['CSSscreenDefault'] wrong
  $SITE['CSSscreenDefault'] = str_replace ('-narrow','',$SITE['CSSscreenDefault']);
  $SITE['CSSscreenDefault'] = str_replace ('-wide','',$SITE['CSSscreenDefault']);

  if ($settings_mode == 2) { // user can select styles only (screen width will be $SITE['CSSscreenDefault'])
    if ($SITE['CSSwideOrNarrowDefault'] == 'wide') {
            $_SESSION['CSSwidescreen'] = 1;
            $wide_style = 1;
    }else {
            $_SESSION['CSSwidescreen'] = 0;
            $narrow_style = 1;
    }
  }
  # is there a CSSstyle selection from the post, cookie, or session?
  if (isset($_POST['CSSstyle']) && preg_match("/^[a-z0-9-]{1,50}.css$/i", $_POST['CSSstyle'])) {
       $CSSstyle = $_POST['CSSstyle'];
  }
  else if ($use_cookies == true && isset($_COOKIE['CSSstyle'])) {
       $_SESSION['CSSstyle'] = $_COOKIE['CSSstyle'];
       $CSSstyle = $_COOKIE['CSSstyle'];
  }
  else if (isset($_SESSION['CSSstyle'])) {
       $CSSstyle = $_SESSION['CSSstyle'];
  }

  if ($settings_mode == 3) { // widescreen on off only (user cannot select styles)
       $_SESSION['CSSstyle'] = $SITE['CSSscreenDefault'];
       $CSSstyle = $SITE['CSSscreenDefault'];
  }
  # was use CSSwidescreen template selected from the form post?
  if (isset($_POST['CSSwidescreen']) && $_POST['CSSwidescreen'] == 1) {
       if ($use_cookies == true) set_style_cookie ('CSSwidescreen', '1');
       $_SESSION['CSSwidescreen'] = 1;
       $wide_style = 1;
  }
  # was use CSSwidescreen template unselected from the form post?
  else if (isset($_POST['CSSwidescreen']) && $_POST['CSSwidescreen'] == 0) {
       if ($use_cookies == true) set_style_cookie ('CSSwidescreen', '0');
       $_SESSION['CSSwidescreen'] = 0;
       $narrow_style = 1;
  }

  //  fixed bug (default widescreen not being set)
  if (isset($_SESSION['CSSwidescreen']) && $_SESSION['CSSwidescreen'] == 1) {
       $wide_style = 1;
  }

  # was use CSSwidescreen template selected from a stored cookie?
  if ($wide_style == 0 && $narrow_style == 0 && $use_cookies == true && isset($_COOKIE['CSSwidescreen']) && $_COOKIE['CSSwidescreen'] == 1) {
       if ($use_cookies == true) set_style_cookie ('CSSwidescreen', '1');
       $_SESSION['CSSwidescreen'] = 1;
       $wide_style = 1;
  }
  # was use CSSwidescreen template unselected from a stored cookie?
  if ($wide_style == 0 && $narrow_style == 0 && $use_cookies == true && isset($_COOKIE['CSSwidescreen']) && $_COOKIE['CSSwidescreen'] == 0) {
       if ($use_cookies == true) set_style_cookie ('CSSwidescreen', '0');
       $_SESSION['CSSwidescreen'] = 0;
  }

  # is the seletcted template or one in session one we really have?
  if ($CSSstyle != '') {
       foreach ($CSSstyles as $k => $v) {
          if ($CSSstyle == "$k")  {
              $_SESSION['CSSstyle'] = $k;
              if ($use_cookies == true) {
                  set_style_cookie ('CSSstyle', $k);
                  set_style_cookie ('CSSwidescreen' , $wide_style);
              }
              if (isset($_SESSION['CSSwidescreen']) && $_SESSION['CSSwidescreen'] == 1) {
                      $CSSstyle = str_replace ('.css','-wide.css',$k);
              } else {
                      $CSSstyle = str_replace ('.css','-narrow.css',$k);
              }
              return $CSSstyle;
          }
       }
  }else{
       # nothing was matched, show default, no set cookie
       $_SESSION['CSSstyle'] = $SITE['CSSscreenDefault'];
       if ($SITE['CSSwideOrNarrowDefault'] == 'wide') {
               $_SESSION['CSSwidescreen'] = 1;
               $CSSstyle = str_replace ('.css','-wide.css',$SITE['CSSscreenDefault']);
       } else {
               $_SESSION['CSSwidescreen'] = 0;
               $CSSstyle = str_replace ('.css','-narrow.css',$SITE['CSSscreenDefault']);
       }
       return $CSSstyle;
  }
}

function set_style_cookie ($name, $val) {
   global $SITE;
   # this function sets a cookie
   # set expiration date 6 months ahead
   $expire = strtotime("+6 month");
   if ( isset($SITE['cookie_path']) ) {
     $path = $SITE['cookie_path']; // this can be set in Settings.php
   } else {
     $scripturlparts = explode('/', $_SERVER['PHP_SELF']);
     $scriptfilename = $scripturlparts[count($scripturlparts)-1];
     $scripturl = preg_replace("/$scriptfilename$/i", '', $_SERVER['PHP_SELF']);
     $path = $scripturl;
   }
   setcookie("$name",$val,$expire,$path);
}

?>